<?php $activePage = 'post-announcement'; ?>
<?php
date_default_timezone_set('Asia/Manila');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include('../shared/assets/database/connect.php');

include("../shared/assets/processes/prof-session-process.php");

$errorMessages = [
    "emailNoCredential" => "No email credentials found in the database!"
];

$toastMessage = '';
$toastType = '';

if (isset($_SESSION['toast'])) {
    $toastMessage = $_SESSION['toast']['message'];
    $toastType = $_SESSION['toast']['type'];
    unset($_SESSION['toast']);
}

// --- Google Link Processor ---
function processGoogleLink($link)
{
    $link = trim($link);

    // Case 1: Google Drive folder
    if (preg_match('/drive\.google\.com\/drive\/folders\/([a-zA-Z0-9_-]+)/', $link, $matches)) {
        $folderId = $matches[1];
        return "https://drive.google.com/embeddedfolderview?id={$folderId}#grid";
    }

    // Case 2: Google Drive file
    if (preg_match('/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/', $link, $matches)) {
        $fileId = $matches[1];
        return "https://drive.google.com/file/d/{$fileId}/preview";
    }

    // Case 3: Google Docs, Sheets, Slides, etc.
    if (preg_match('/(https:\/\/docs\.google\.com\/[a-z]+\/d\/[a-zA-Z0-9_-]+)/', $link, $matches)) {
        $baseUrl = $matches[1];
        if (str_contains($link, '/preview')) {
            return preg_replace('/\?.*/', '', $link);
        }
        return "{$baseUrl}/preview";
    }

    // Case 4: Already preview link
    if (str_contains($link, '/preview')) {
        return preg_replace('/\?.*/', '', $link);
    }

    // Fallback
    return preg_replace('/\?.*/', '', $link);
}

function fetchLinkTitle($url)
{
    $url = trim($url);

    // Ensure URL protocol
    if (!preg_match('/^https?:\/\//', $url)) {
        $url = 'https://' . $url;
    }

    // Check if it's a YouTube link
    if (preg_match('/(youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $matches)) {
        // Use oEmbed API to fetch the title
        $apiUrl = "https://www.youtube.com/oembed?url=" . urlencode($url) . "&format=json";

        $json = @file_get_contents($apiUrl);
        if ($json !== false) {
            $data = json_decode($json, true);
            if (isset($data['title'])) {
                return $data['title'];
            }
        }
    }

    // Normal HTML fetch if not YouTube
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $html = curl_exec($ch);
    curl_close($ch);

    if ($html) {
        // OG Title
        if (preg_match('/<meta property="og:title" content="([^"]+)"/i', $html, $matches)) {
            return trim($matches[1]);
        }

        // Page <title>
        if (preg_match("/<title>(.*?)<\/title>/is", $html, $matches)) {
            return trim($matches[1]);
        }
    }

    // Fallback: Domain name
    $parsedUrl = parse_url($url);
    return isset($parsedUrl['host']) ? ucfirst(str_replace('www.', '', $parsedUrl['host'])) : 'Link';
}


if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
    require '../shared/assets/phpmailer/src/Exception.php';
    require '../shared/assets/phpmailer/src/PHPMailer.php';
    require '../shared/assets/phpmailer/src/SMTP.php';
}

$mode = '';
$announcementID = 0;

if (isset($_GET['edit'])) {
    $mode = 'edit';
    $announcementID = intval($_GET['edit']);
} elseif (isset($_GET['reuse'])) {
    $mode = 'reuse';
    $announcementID = intval($_GET['reuse']);
}

// Get all courses owned by this user
$sql = "SELECT courseID, courseCode FROM courses WHERE userID = '$userID'";
$courses = executeQuery($sql);

// Save announcement query
if (isset($_POST['save_announcement'])) {
    $mode = $_POST['mode'] ?? 'new'; // new, reuse, or edit
    $announcementID = intval($_POST['announcementID'] ?? 0); // used in edit mode
    $contentRaw = $_POST['announcement'] ?? '';
    $content = mysqli_real_escape_string($conn, $contentRaw);
    $links = $_POST['links'] ?? [];
    $uploadedFiles = !empty($_FILES['materials']['name'][0]);
    $date = date("Y-m-d");
    $time = date("H:i:s");
    $isRequired = 0;
    $existingFiles = $_POST['existingFiles'] ?? []; // files user kept

    foreach ($_POST['courses'] ?? [] as $selectedCourseID) {

        if ($mode === 'new') {
            // --- INSERT new announcement ---
            $insertAnnouncement = "INSERT INTO announcements 
            (courseID, userID, announcementContent, announcementDate, announcementTime, isRequired) 
            VALUES 
            ('$selectedCourseID', '$userID', '$content', '$date', '$time', '$isRequired')";
            executeQuery($insertAnnouncement);
            $announcementID = mysqli_insert_id($conn);

        } elseif ($mode === 'edit') {
            // --- UPDATE existing announcement ---
            $updateAnnouncement = "UPDATE announcements 
            SET announcementContent='$content', 
                announcementDate='$date', 
                announcementTime='$time', 
                isRequired='$isRequired'
            WHERE announcementID='$announcementID'";
            executeQuery($updateAnnouncement);

        } elseif ($mode === 'reuse') {
            // --- OLD announcement ID from form ---
            $oldAnnouncementID = intval($_POST['announcementID']);

            // --- CREATE NEW ANNOUNCEMENT WITH EDITED VALUES ---
            $insertAnnouncement = "INSERT INTO announcements 
            (courseID, userID, announcementContent, announcementDate, announcementTime, isRequired)
            VALUES 
            ('$selectedCourseID', '$userID', '$content', '$date', '$time', '$isRequired')";
            executeQuery($insertAnnouncement);

            // --- NEW announcement ID ---
            $announcementID = mysqli_insert_id($conn);

            // --- COPY ALL FILES / LINKS FROM OLD ANNOUNCEMENT ---
            $filesQuery = "SELECT * FROM files WHERE announcementID='$oldAnnouncementID'";
            $filesResult = executeQuery($filesQuery);

            while ($file = mysqli_fetch_assoc($filesResult)) {
                $fileAttachment = $file['fileAttachment'] ?? null;
                $fileLink = $file['fileLink'] ?? null;
                $fileTitle = mysqli_real_escape_string($conn, $file['fileTitle'] ?? '');

                $insertFile = "INSERT INTO files 
            (announcementID, fileAttachment, fileLink, fileTitle, courseID, userID)
            VALUES 
            ('$announcementID', '$fileAttachment', '$fileLink', '$fileTitle', '$selectedCourseID', '$userID')";
                executeQuery($insertFile);
            }
        }

        // --- REMOVE FILES IF REQUESTED ---
        if (!empty($_POST['removeFiles'])) {
            $removeFiles = $_POST['removeFiles'];
            $removeFilesStr = implode("','", array_map(function ($f) use ($conn) {
                return mysqli_real_escape_string($conn, $f);
            }, $removeFiles));
            $deleteQuery = "DELETE FROM files 
            WHERE announcementID='$announcementID'
            AND fileAttachment IN ('$removeFilesStr')";
            executeQuery($deleteQuery);

            foreach ($removeFiles as $file) {
                $path = __DIR__ . "/../shared/assets/files/" . $file;
                if (file_exists($path))
                    unlink($path);
            }
        }

        // --- REMOVE LINKS IF REQUESTED ---
        if (!empty($_POST['removeLinks'])) {
            $removeLinks = $_POST['removeLinks'];
            $removeLinksStr = implode("','", array_map(function ($l) use ($conn) {
                return mysqli_real_escape_string($conn, $l);
            }, $removeLinks));

            $deleteQuery = "DELETE FROM files 
            WHERE announcementID='$announcementID'
            AND fileLink IN ('$removeLinksStr')";
            executeQuery($deleteQuery);
        }

        // --- Handle uploaded files ---
        if ($uploadedFiles) {
            $uploadDir = __DIR__ . "/../shared/assets/files/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            foreach ($_FILES['materials']['name'] as $key => $fileName) {
                $tmpName = $_FILES['materials']['tmp_name'][$key];
                $fileError = $_FILES['materials']['error'][$key];

                if ($fileError === UPLOAD_ERR_OK) {

                    // Get original filename and extension
                    $originalName = pathinfo($fileName, PATHINFO_FILENAME);
                    $extension = pathinfo($fileName, PATHINFO_EXTENSION);

                    // Replace symbols with underscores (keep letters, numbers, dash, and underscore)
                    $safeOriginalName = preg_replace('/[^a-zA-Z0-9-_]/', '_', $originalName);

                    // Reattach the extension
                    $safeOriginalName .= '.' . $extension;

                    // Generate a unique file name
                    $fileAttachment = date('Ymd_His') . '_' . $safeOriginalName;

                    $targetPath = $uploadDir . $fileAttachment;

                    if (move_uploaded_file($tmpName, $targetPath)) {

                        // Save file to database
                        $insertFile = "INSERT INTO files 
                        (courseID, userID, announcementID, fileAttachment, fileTitle, fileLink) 
                        VALUES 
                        ('$selectedCourseID', '$userID', '$announcementID', '$fileAttachment', '$safeOriginalName', '')";

                        executeQuery($insertFile);
                    }
                }
            }
        }

        // --- Handle link files ---
        if (!empty($links)) {
            foreach ($links as $link) {
                $link = trim($link);
                if ($link === '')
                    continue;

                $fileTitle = fetchLinkTitle($link);

                // Check if YouTube
                if (preg_match('/(youtube\.com|youtu\.be)/i', $link)) {
                    $processedLink = $link; // Do NOT modify YouTube links
                } else {
                    $processedLink = processGoogleLink($link); // Only for Google Drive/Docs
                }

                $insertLink = "INSERT INTO files 
                (courseID, userID, announcementID, fileAttachment, fileTitle, fileLink) 
                VALUES 
                ('$selectedCourseID', '$userID', '$announcementID', '', '" .
                    mysqli_real_escape_string($conn, $fileTitle) . "', '$processedLink')";
                executeQuery($insertLink);
            }
        }

        // --- Notifications & Email (only for new/reuse) ---
        if ($mode === 'new' || $mode === 'reuse') {
            // Prepare notification message using first five words (plain text)
            $contentText = strip_tags($contentRaw);
            $contentText = html_entity_decode($contentText, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $contentText = preg_replace('/\s+/', ' ', $contentText);
            $contentText = trim($contentText);

            $firstWords = '';
            if (!empty($contentText)) {
                $words = preg_split('/\s+/', $contentText, -1, PREG_SPLIT_NO_EMPTY);
                if (!empty($words)) {
                    $firstWords = implode(' ', array_slice($words, 0, min(5, count($words))));
                }
            }

            $notificationMessage = "A new announcement has been posted : \"" . $firstWords . "...\"";
            $notifType = 'Course Update';
            $courseCode = "";

            // Fetch course code for email
            $selectCourseDetailsQuery = "SELECT courseCode FROM courses WHERE courseID = '$selectedCourseID'";
            $courseDetailsResult = executeQuery($selectCourseDetailsQuery);
            if ($courseData = mysqli_fetch_assoc($courseDetailsResult)) {
                $courseCode = $courseData['courseCode'];
            }

            $escapedNotificationMessage = mysqli_real_escape_string($conn, $notificationMessage);
            $escapedNotifType = mysqli_real_escape_string($conn, $notifType);

            // Insert notifications for each student
            if ($mode === 'reuse') {
                // Always insert for reuse - it's a new posting event
                $insertNotificationQuery = "
                    INSERT INTO inbox 
                    (enrollmentID, messageText, notifType, createdAt)
                    SELECT
                        e.enrollmentID,
                        '$escapedNotificationMessage',
                        '$escapedNotifType',
                        NOW()
                    FROM
                        enrollments e
                    WHERE
                        e.courseID = '$selectedCourseID'
                ";
            } else {
                // For 'new' mode, prevent duplicates within 5 minutes
                $insertNotificationQuery = "
                    INSERT INTO inbox 
                    (enrollmentID, messageText, notifType, createdAt)
                    SELECT
                        e.enrollmentID,
                        '$escapedNotificationMessage',
                        '$escapedNotifType',
                        NOW()
                    FROM
                        enrollments e
                    WHERE
                        e.courseID = '$selectedCourseID'
                        AND NOT EXISTS (
                            SELECT 1 
                            FROM inbox i2 
                            WHERE i2.enrollmentID = e.enrollmentID 
                                AND i2.messageText = '$escapedNotificationMessage'
                                AND i2.notifType = '$escapedNotifType'
                                AND i2.createdAt > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
                        )
                ";
            }
            executeQuery($insertNotificationQuery);

            // Email enrolled students who opted-in
            $selectEmailsQuery = "
                SELECT u.email, u.userID,
                       COALESCE(s.announcementEnabled, 0) as announcementEnabled
                FROM users u
                INNER JOIN enrollments e ON u.userID = e.userID
                LEFT JOIN settings s ON u.userID = s.userID
               WHERE e.courseID = '$selectedCourseID'
            ";
            $emailsResult = executeQuery($selectEmailsQuery);

            $credentialQuery = "SELECT email, password FROM emailcredentials WHERE credentialID = 1";
            $credentialResult = executeQuery($credentialQuery);
            $credentialRow = $credentialResult ? mysqli_fetch_assoc($credentialResult) : null;

            if ($credentialRow) {
                $smtpEmail = $credentialRow['email'];
                $smtpPassword = $credentialRow['password'];

                try {
                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = $smtpEmail;
                    $mail->Password = $smtpPassword;
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;
                    $mail->setFrom($smtpEmail, 'Webstar');
                    $headerPath = __DIR__ . '/../shared/assets/img/email/email-header.png';
                    if (file_exists($headerPath)) {
                        $mail->AddEmbeddedImage($headerPath, 'emailHeader');
                    }
                    $footerPath = __DIR__ . '/../shared/assets/img/email/email-footer.png';
                    if (file_exists($footerPath)) {
                        $mail->AddEmbeddedImage($footerPath, 'emailFooter');
                    }

                    $mail->isHTML(true);
                    $mail->CharSet = 'UTF-8';
                    $mail->Encoding = 'base64';
                    $mail->Subject = "[NEW ANNOUNCEMENT] Course $courseCode";

                    $recipientsFound = false;
                    while ($student = mysqli_fetch_assoc($emailsResult)) {
                        if ($student['announcementEnabled'] == 1 && !empty($student['email'])) {
                            $mail->addAddress($student['email']);
                            $recipientsFound = true;
                        }
                    }

                    if ($recipientsFound) {
                        $contentHtml = nl2br(htmlspecialchars($contentRaw, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                        $courseCodeEsc = htmlspecialchars($courseCode, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                        $mail->Body = '<div style="font-family: Arial, sans-serif; background-color:#f4f6f7; padding: 0; margin: 0;">
                        <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f7; padding: 40px 0;">
                            <tr>
                                <td align="center">
                                    <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.1);">
                                        <tr>
                                            <td align="center" style="padding: 0;">
                                                <img src="cid:emailHeader" alt="Webstar Header" style="width:600px; height:auto; display:block;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 30px;">
                                                <p style="font-size:15px; color:#333;">Hello Learner,</p>
                                                <p style="font-size:15px; color:#333;">
                                                    A new announcement has been posted in your course <strong>' . $courseCodeEsc . '</strong>.
                                                </p>
                                                <div style="background-color:#f9f9f9; padding: 20px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #FDDF94;">
                                                    <div style="font-size:15px; color:#333; line-height: 1.6;">
                                                        ' . $contentHtml . '
                                                    </div>
                                                </div>
                                                <p style="font-size:15px; color:#333;">
                                                    Please log in to your Webstar account to view the full announcement and any attached materials.
                                                </p>
                                                <p style="margin-top:30px; color:#333;">
                                                    Warm regards,<br>
                                                    <strong>The Webstar Team</strong><br>
                                                </p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="center" style="padding: 0;">
                                                <img src="cid:emailFooter" alt="Webstar Footer" style="width:600px; height:auto; display:block; border:0; outline:none; text-decoration:none;" />
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            </table>
                        </div>';

                        $mail->send();
                    }
                } catch (Exception $e) {
                    $errorMsg = isset($mail) && is_object($mail) ? $mail->ErrorInfo : $e->getMessage();
                    error_log("PHPMailer failed for Course ID $selectedCourseID: " . $errorMsg);
                }
            }
        }
    }
    if ($mode === 'new' || $mode === 'reuse') {
        $_SESSION['toast'] = [
            'type' => 'alert-success',
            'message' => 'Annoucement added successfully!'
        ];
    }
    if ($mode === 'edit') {
        $_SESSION['toast'] = [
            'type' => 'alert-success',
            'message' => 'Annoucement edited successfully!'
        ];
    }
    header("Location: course-info.php?courseID=" . intval($_POST['courses'][0]));
    exit();

}

//Fetch the Title of link
if (isset($_GET['fetchTitle'])) {
    $url = $_GET['fetchTitle'];
    $title = $url;

    // Get HTML content
    $context = stream_context_create([
        "http" => ["header" => "User-Agent: Mozilla/5.0"]
    ]);
    $html = @file_get_contents($url, false, $context);

    if ($html !== false) {
        if (preg_match('/<meta property="og:title" content="([^"]+)"/i', $html, $matches)) {
            $title = $matches[1];
        } elseif (preg_match("/<title>(.*?)<\/title>/i", $html, $matches)) {
            $title = $matches[1];
        }
    }

    echo json_encode(["title" => $title]);
    exit;
}

// Selecting Announcements made by the Instructor logged in
$announcementQuery = "
    SELECT 
        ann.announcementID,
        ann.announcementContent,
        ann.announcementDate,
        ann.announcementTime,
        ann.isRequired,
        c.courseCode
    FROM announcements ann
    JOIN courses c ON ann.courseID = c.courseID
    WHERE c.userID = '$userID'
    ORDER BY ann.announcementDate DESC, ann.announcementTime DESC
";

$announcements = executeQuery($announcementQuery);

// Storing reused data
$reusedData = null;

// If the instructor chose to reuse or edit an announcement
if (isset($_GET['reuse']) || isset($_GET['edit'])) {
    $reuseID = isset($_GET['reuse']) ? intval($_GET['reuse']) : intval($_GET['edit']);

    // Get announcement data
    $reuseQuery = "
        SELECT 
            an.announcementContent,
            an.announcementDate,
            an.announcementTime,
            an.isRequired,
            crs.courseID,
            crs.courseCode,
            f.fileID,
            f.fileAttachment,
            f.fileTitle,
            f.fileLink
        FROM announcements an
        JOIN courses crs ON an.courseID = crs.courseID
        JOIN users u ON crs.userID = u.userID
        LEFT JOIN files f ON an.announcementID = f.announcementID
        WHERE an.announcementID = '$reuseID'
          AND u.userID = '$userID'
    ";

    $reuseResult = executeQuery($reuseQuery);
    if ($reuseResult && $reuseResult->num_rows > 0) {
        $reusedData = [];
        while ($row = $reuseResult->fetch_assoc()) {
            $reusedData[] = $row; // store all file rows
        }
    } else {
        // Invalid or unauthorized reuse/edit attempt
        header("Location: post-announcement.php");
        exit();
    }
}

if (!empty($reusedData)) {
    // Populate main fields for reuse/edit
    $mainData = [
        'announcementContent' => $reusedData[0]['announcementContent'],
        'announcementDate' => $reusedData[0]['announcementDate'],
        'announcementTime' => $reusedData[0]['announcementTime'],
        'isRequired' => $reusedData[0]['isRequired']
    ];

    // Collect attached files/links
    $files = [];
    $selectedCourses = []; // for pre-checking the courses
    foreach ($reusedData as $row) {
        if (!empty($row['fileID'])) {
            $files[] = [
                'fileID' => $row['fileID'],
                'fileTitle' => $row['fileTitle'],
                'fileAttachment' => $row['fileAttachment'],
                'fileLink' => $row['fileLink']
            ];
        }
        if (!in_array($row['courseID'], $selectedCourses)) {
            $selectedCourses[] = $row['courseID'];
        }
    }
}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?php
        echo isset($_GET['reuse']) ? 'Repost Announcement' : (isset($_GET['edit']) ? 'Edit Announcement' : 'Post Announcement');
        ?> ✦ Webstar
    </title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/post-announcement.css">
    <link rel="stylesheet" href="../shared/assets/css/add-lesson.css">
    <link rel="stylesheet" href="../shared/assets/css/sidebar-and-container-styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../shared/assets/img/webstar-icon.png">
    <!-- Quill CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0"
        rel="stylesheet" />

    <style>
        /* Change font for text typed inside the Quill editor */
        .ql-editor {
            font-family: 'Regular', sans-serif;
            letter-spacing: -0.03em;
            color: var(--black);
            font-size: 16px;
            /* optional */
        }

        .ql-editor {
            padding: 12px 16px;
            /* customize as you like */
        }

        .ql-editor::before {
            padding: 12px 16px;
        }

        .ql-editor::before {
            font-family: inherit;
            /* use same font as the text */
            font-size: inherit;
            /* same size as the text */
            line-height: inherit;
            /* align vertically */
            padding: 1px 1px;
            /* match .ql-editor padding */
            opacity: 1;
            /* ensure visibility */
        }
    </style>


</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-0 p-md-3"
        style="background-color: var(--black);">

        <div class="row w-100">

            <!-- Sidebar (mobile) -->
            <?php include '../shared/components/prof-sidebar-for-mobile.php'; ?>

            <!-- Sidebar (desktop) -->
            <?php include '../shared/components/prof-sidebar-for-desktop.php'; ?>

            <!-- Main Container -->
            <div class="col main-container m-0 p-0 mx-0 mx-md-2 p-md-4 overflow-y-auto">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">

                    <div id="toastContainer"
                        class="position-absolute top-0 start-50 translate-middle-x pt-5 pt-md-1 d-flex flex-column align-items-center"
                        style="z-index: 1100;">
                    </div>

                    <!-- Navbar (mobile) -->
                    <?php include '../shared/components/prof-navbar-for-mobile.php'; ?>

                    <div class="container-fluid py-3 overflow-y-auto row-padding-top">
                        <div class="create-prof-row">
                            <div class="col-12">
                                <!-- Header -->
                                <div class="row mb-3 align-items-center">

                                    <!-- Back Arrow -->
                                    <div class="col-auto d-none d-md-block">
                                        <a href="javascript:history.back()" class="text-decoration-none">
                                            <i class="fa-solid fa-arrow-left text-reg text-16"
                                                style="color: var(--black);"></i>
                                        </a>
                                    </div>

                                    <!-- Page Title -->
                                    <div class="col text-center text-md-start">
                                        <span class="text-sbold text-20"><?php
                                        if (isset($_GET['edit'])) {
                                            echo 'Edit Announcement';
                                        } elseif (isset($_GET['reuse'])) {
                                            echo 'Repost Announcement';
                                        } else {
                                            echo 'Post Announcement';
                                        }
                                        ?></span>
                                    </div>

                                    <!-- Assign Existing Task Button -->
                                    <div
                                        class="col-12 col-md-auto text-center d-flex d-md-block justify-content-center justify-content-md-end mt-3 mt-md-0">
                                        <button type="button"
                                            class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14 my-1 d-flex align-items-center gap-2"
                                            style="background-color: var(--primaryColor); border: 1px solid var(--black); color: var(--black);"
                                            data-bs-toggle="modal" data-bs-target="#reuseTaskModal">
                                            <span class="material-symbols-rounded"
                                                style="font-size:16px">campaign</span>
                                            <span>Post an existing announcement</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Form starts -->
                                <form id="announcementForm" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="mode"
                                        value="<?= isset($_GET['edit']) ? 'edit' : (isset($_GET['reuse']) ? 'reuse' : 'new') ?>">
                                    <input type="hidden" name="announcementID"
                                        value="<?= $_GET['edit'] ?? $_GET['reuse'] ?? '' ?>">
                                    <!-- Rich Text Editor -->
                                    <div class="row pt-4">
                                        <div class="col-12 mb-3">
                                            <div class="editor-wrapper">
                                                <div id="editor"></div>
                                                <div id="toolbar" class="row align-items-center p-3 p-md-3 g-2 g-md-5">
                                                    <div
                                                        class="col d-flex align-items-center px-2 px-md-4 gap-1 gap-md-3">
                                                        <button class="ql-bold"></button>
                                                        <button class="ql-italic"></button>
                                                        <button class="ql-underline"></button>
                                                        <button class="ql-list" value="bullet"></button>
                                                        <span id="word-counter"
                                                            class="ms-auto text-muted text-med text-16 me-2">0/120</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Hidden input for Quill content -->
                                            <input type="hidden" name="announcement" id="announcement">
                                        </div>
                                    </div>

                                    <!-- Learning Materials -->
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="learning-materials">
                                                <div class="d-flex align-items-center mt-4 mb-0 mb-md-2">
                                                    <label class="text-med text-16">Attachments</label>
                                                    <!-- For desktop -->
                                                    <span class="material-symbols-outlined ms-3 me-1 d-none d-md-block"
                                                        style="font-size:16px">
                                                        info
                                                    </span>
                                                    <span class="text-reg text-14 d-none d-md-block">You can add up to
                                                        10
                                                        files or links.</span>
                                                </div>
                                                <!-- For mobile -->
                                                <div class="d-block d-md-none d-flex align-items-center mb-2">
                                                    <span class="material-symbols-outlined me-1" style="font-size:16px">
                                                        info
                                                    </span>
                                                    <span class="text-reg text-14">You can add up to 10
                                                        files or links.</span>
                                                </div>

                                                <!-- Container for dynamically added file & link cards -->
                                                <div id="filePreviewContainer" class="row mb-0"></div>

                                                <!-- Upload buttons -->
                                                <div class="mt-0 mb-4 text-start ">
                                                    <input type="file" name="materials[]" class="d-none" id="fileUpload"
                                                        multiple>
                                                    <button type="button"
                                                        class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14 mt-1"
                                                        style="background-color: var(--primaryColor); border: 1px solid var(--black);"
                                                        onclick="document.getElementById('fileUpload').click();">
                                                        <div style="display: flex; align-items: center; gap: 5px;">
                                                            <span class="material-symbols-rounded"
                                                                style="font-size:16px">upload</span>
                                                            <span>Upload</span>
                                                        </div>
                                                    </button>

                                                    <button type="button"
                                                        class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14 ms-2 mt-1"
                                                        style="background-color: var(--primaryColor); border: 1px solid var(--black);"
                                                        data-bs-container="body" data-bs-toggle="popover"
                                                        data-bs-placement="right" data-bs-html="true"
                                                        data-bs-content='<div class="form-floating mb-3 text-reg"><input type="url" class="form-control text-reg" id="linkInput" placeholder="Paste link here"><label for="linkInput">Link</label></div><div class="link-popover-actions"><button type="button" class="btn btn-sm px-3 py-1 rounded-pill text-reg" id="addLinkBtn" style="background-color: var(--primaryColor); border: 1px solid var(--black); color: var(--black);">Add link</button></div>'>
                                                        <div style="display: flex; align-items: center; gap: 5px;">
                                                            <span class="material-symbols-rounded"
                                                                style="font-size:20px">link</span>
                                                            <span>Link</span>
                                                        </div>
                                                    </button>

                                                    <!-- Hidden input to store added links -->
                                                    <input type="hidden" name="links[]" id="taskLinks">
                                                </div>
                                            </div>

                                            <!-- Course selection + Post button -->
                                            <div class="row align-items-center justify-content-between text-center text-md-start mt-5"
                                                style="margin-bottom: <?php echo ($courses && $courses->num_rows > 0) ? ($courses->num_rows * 50) : 0; ?>px;">
                                                <!-- Dynamic Border Bottom based on the number of courses hehe -->
                                                <!-- Add to Course -->
                                                <div
                                                    class="col-12 col-md-auto mt-3 mt-md-0 d-flex align-items-center flex-nowrap justify-content-center justify-content-md-start <?php echo isset($_GET['edit']) ? 'invisible' : ''; ?>">
                                                    <span class="me-2 text-med text-16 pe-3">Add to Course</span>
                                                    <div class="dropdown">
                                                        <button
                                                            class="btn dropdown-toggle dropdown-shape text-med text-16 me-md-5"
                                                            type="button" data-bs-toggle="dropdown"
                                                            aria-expanded="false">
                                                            <span class="me-2">Select Course</span>
                                                        </button>
                                                        <ul class="dropdown-menu p-2 mt-2"
                                                            style="min-width: 200px; border: 1px solid var(--black);">
                                                            <?php
                                                            if ($courses && $courses->num_rows > 0) {
                                                                $assignedCourseIDs = [];
                                                                if (isset($_GET['edit'])) {
                                                                    $editID = intval($_GET['edit']);
                                                                    $assignedQuery = "SELECT courseID FROM announcements WHERE announcementID = '$editID'";
                                                                    $assignedResult = executeQuery($assignedQuery);
                                                                    if ($assignedResult && $assignedResult->num_rows > 0) {
                                                                        while ($row = $assignedResult->fetch_assoc()) {
                                                                            $assignedCourseIDs[] = $row['courseID'];
                                                                        }
                                                                    }
                                                                }

                                                                while ($course = $courses->fetch_assoc()) {
                                                                    $checked = in_array($course['courseID'], $assignedCourseIDs) ? 'checked' : '';
                                                                    ?>
                                                                    <li>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input course-checkbox"
                                                                                type="checkbox" name="courses[]"
                                                                                style="border: 1px solid var(--black);"
                                                                                value="<?= $course['courseID'] ?>"
                                                                                id="course<?= $course['courseID'] ?>"
                                                                                <?= $checked ?>>
                                                                            <label class="form-check-label text-reg"
                                                                                for="course<?= $course['courseID'] ?>">
                                                                                <?= $course['courseCode'] ?>
                                                                            </label>
                                                                        </div>
                                                                    </li>
                                                                    <?php
                                                                }
                                                            } else { ?>
                                                                <li><span class="dropdown-item-text text-muted">No courses
                                                                        found</span></li>
                                                            <?php } ?>
                                                        </ul>
                                                    </div>
                                                </div>

                                                <!-- Assign Button -->
                                                <div class="col-12 col-md-auto mt-3 mt-md-0 text-center">
                                                    <button type="submit" name="save_announcement"
                                                        class="px-4 py-2 rounded-pill text-sbold text-md-14 mt-4 mt-md-0"
                                                        style="background-color: var(--primaryColor); border: 1px solid var(--black);">
                                                        <?php
                                                        if (isset($_GET['edit'])) {
                                                            echo 'Save Changes';
                                                        } elseif (isset($_GET['reuse'])) {
                                                            echo 'Repost';
                                                        } else {
                                                            echo 'Post';
                                                        }
                                                        ?>
                                                    </button>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Reuse Announcement Modal -->
    <div class="modal fade" id="reuseTaskModal" tabindex="-1" aria-labelledby="reuseTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered py-4 " style="max-width: 700px;  height: 80vh">
            <div class="modal-content d-flex flex-column" style="height: 100%;">

                <!-- HEADER -->
                <div class="modal-header flex-shrink-0">
                    <div class="modal-title text-sbold text-20 ms-3" id="selectRubricModalLabel">Post an existing
                        announcement
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        style="transform: scale(0.8); filter: grayscale(100%);"></button>
                </div>

                <!-- DESC -->
                <div class="modal-body flex-grow-1 overflow-auto">
                    <p class="mb-3 text-med text-14 mx-3" style="color: var(--black);">
                        Select an announcement you’ve previously created to reuse its content. You can review and edit
                        the
                        text or settings before posting it again.
                    </p>
                    <div class="col p-0 m-3">
                        <div class="card rounded-3 mb-2 border-0">
                            <div class="card-body p-0">
                                <!-- OPTIONS -->
                                <?php if ($announcements && $announcements->num_rows > 0) {
                                    while ($announcement = $announcements->fetch_assoc()) {
                                        ?>
                                        <div class="rubric-option rounded-3 d-flex align-items-center justify-content-between mb-2 w-100"
                                            style="cursor: pointer; background-color: var(--pureWhite); border: 1px solid var(--black);"
                                            onclick='window.location.href="post-announcement.php?reuse=<?php echo $announcement["announcementID"]; ?>"'>
                                            <div style="line-height: 1.5; padding:10px 15px;">
                                                <div class="text-sbold text-14"
                                                    style="display:-webkit-box; -webkit-line-clamp:1; -webkit-box-orient:vertical; overflow:hidden;">
                                                    <?php echo substr(strip_tags($announcement['announcementContent']), 0, 100); ?>
                                                </div>
                                                <div class="text-med text-muted text-12"
                                                    style="display:-webkit-box; -webkit-line-clamp:1; -webkit-box-orient:vertical; overflow:hidden;">
                                                    <?php
                                                    echo date('F j, Y g:i A', strtotime($announcement['announcementDate'] . ' ' . $announcement['announcementTime']))
                                                        . ' · ' . $announcement['courseCode'];
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <div class="text-muted text-reg text-14 py-2">No existing announcements found.</div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer border-top flex-shrink-0 py-4">
                </div>
            </div>
        </div>
    </div>

    <!-- Quill JS -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        var icons = Quill.import("ui/icons");

        // Custom upload icon
        icons['upload'] = '<svg viewBox="0 0 18 18">' +
            '<line class="ql-stroke" x1="9" x2="9" y1="15" y2="3"></line>' +
            '<polyline class="ql-stroke" points="5 7 9 3 13 7"></polyline>' +
            '<rect class="ql-fill" height="2" width="12" x="3" y="15"></rect>' +
            '</svg>';

        // Initialize Quill editor
        var quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Announce something to your class ',
            modules: { toolbar: '#toolbar' }
        });

        <?php if (isset($reusedData)) { ?>
            quill.clipboard.dangerouslyPasteHTML(
                <?= json_encode($mainData['announcementContent']); ?>
            );
        <?php } ?>

        // Word counter
        const maxWords = 120;
        const counter = document.getElementById("word-counter");

        quill.on('text-change', function () {
            let text = quill.getText().trim();
            let words = text.length > 0 ? text.split(/\s+/).length : 0;


            if (words > maxWords) {
                let limited = text.split(/\s+/).slice(0, maxWords).join(" ");
                quill.setText(limited + " ");
                quill.setSelection(quill.getLength());
            }

            counter.textContent = `${Math.min(words, maxWords)}/${maxWords}`;
        });

        const form = document.querySelector('#announcementForm');
        form.addEventListener('submit', function (e) {
            const hiddenInput = document.querySelector('#announcement');
            const text = quill.getText().trim();

            // Validate Quill editor
            if (text.length === 0) {
                e.preventDefault();
                quill.root.focus();
                hiddenInput.setCustomValidity('Please fill out this field.');
                hiddenInput.reportValidity();
                return false;
            } else {
                hiddenInput.setCustomValidity('');
            }

            // Validation
            let valid = true;
            let errorMessages = [];

            // Validate at least one course selected (only in NEW mode)
            let checkboxes = form.querySelectorAll(".course-checkbox");
            let checked = Array.from(checkboxes).some(cb => cb.checked);
            if (!checked && !window.location.search.includes("edit")) {
                valid = false;
                errorMessages.push("Please select at least one course before submitting.");
            }

            if (!valid) {
                e.preventDefault();

                const container = document.getElementById("toastContainer");
                container.innerHTML = "";

                errorMessages.forEach(msg => {
                    const alert = document.createElement("div");
                    alert.className = "alert mb-2 shadow-lg text-med text-12 d-flex align-items-center justify-content-center gap-2 px-3 py-2 alert-danger";
                    alert.role = "alert";
                    alert.innerHTML = `
                        <i class="bi bi-x-circle-fill fs-6"></i>
                        <span>${msg}</span>
                        `;
                    container.appendChild(alert);
                    setTimeout(() => alert.remove(), 3000);
                });

            }

            // Sync Quill content to hidden input
            let html = quill.root.innerHTML;
            html = html.replace(/<p>/g, '').replace(/<\/p>/g, '<br>');
            html = html.replace(/<li>/g, '• ').replace(/<\/li>/g, '<br>');
            html = html.replace(/<\/?(ul|ol)>/g, '');
            html = html.replace(/(<br>)+$/g, '');
            hiddenInput.value = html.trim();
        });

        // File & Link preview logic with total limit of 10
        document.addEventListener('DOMContentLoaded', function () {
            const fileInput = document.getElementById('fileUpload');
            const container = document.getElementById('filePreviewContainer');
            let allFiles = [];

            // Link popovers
            const popovers = document.querySelectorAll('[data-bs-toggle="popover"]');
            popovers.forEach(el => {
                new bootstrap.Popover(el, { html: true, sanitize: false });

                el.addEventListener('shown.bs.popover', function () {
                    const tip = document.querySelector('.popover.show');
                    if (tip) tip.classList.add('link-popover');

                    const addLinkBtn = tip.querySelector('#addLinkBtn');
                    const linkInput = tip.querySelector('#linkInput');

                    addLinkBtn.addEventListener('click', function () {
                        const linkValue = linkInput.value.trim();
                        if (!linkValue) return;

                        // Validation
                        let valid = true;
                        let errorMessages = [];

                        // Check total attachments limit
                        const totalAttachments = container.querySelectorAll('.col-12').length;
                        if (totalAttachments >= 10) {
                            valid = false;
                            errorMessages.push("You can only add up to 10 files or links total.");
                        }

                        if (!valid) {
                            const toastContainer = document.getElementById("toastContainer");
                            toastContainer.innerHTML = "";

                            errorMessages.forEach(msg => {
                                const alert = document.createElement("div");
                                alert.className = "alert mb-2 shadow-lg text-med text-12 d-flex align-items-center justify-content-center gap-2 px-3 py-2 alert-danger";
                                alert.role = "alert";
                                alert.innerHTML = `
                                    <i class="bi bi-x-circle-fill fs-6"></i>
                                    <span>${msg}</span>
                                `;
                                toastContainer.appendChild(alert);
                                setTimeout(() => alert.remove(), 3000);
                            });

                            return;
                        }

                        // Get domain and favicon
                        function truncate(text, maxLength = 30) {
                            if (!text) return '';
                            return text.length > maxLength ? text.slice(0, maxLength - 1) + '…' : text;
                        }

                        const urlObj = new URL(linkValue);
                        const domain = urlObj.hostname;
                        const faviconURL = `https://www.google.com/s2/favicons?sz=64&domain=${domain}`;

                        const uniqueID = Date.now();
                        let displayTitle = "Loading...";

                        const previewHTML = `
                        <div class="col-12 my-1" data-id="${uniqueID}">
                            <div class="materials-card d-flex align-items-stretch p-2 w-100 rounded-3">
                                <div class="d-flex w-100 align-items-center justify-content-between">
                                    <div class="d-flex align-items-center flex-grow-1">
                                        <div class="mx-3 d-flex align-items-center">
                                            <img src="${faviconURL}" alt="${domain} Icon" 
                                                onerror="this.onerror=null;this.src='../shared/assets/img/web.png';" 
                                                style="width: 30px; height: 30px;">
                                        </div>
                                        <div>
                                            <div id="title-${uniqueID}" class="text-sbold text-16" style="line-height: 1.5;">${displayTitle}</div>
                                            <div class="text-reg text-12 text-break" style="line-height: 1.5;">
                                                <a href="${linkValue}" target="_blank">${truncate(linkValue, 50)}</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mx-3 d-flex align-items-center delete-file" style="cursor:pointer;">
                                        <span class="material-symbols-outlined">close</span>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="links[]" value="${linkValue}" class="link-hidden">
                        </div>`;
                        container.insertAdjacentHTML('beforeend', previewHTML);

                        // Fetch page title
                        fetch("?fetchTitle=" + encodeURIComponent(linkValue))
                            .then(res => res.json())
                            .then(data => {
                                const titleEl = document.getElementById(`title-${uniqueID}`);
                                if (titleEl) titleEl.textContent = truncate(data.title || linkValue, 50);
                            })
                            .catch(() => {
                                const titleEl = document.getElementById(`title-${uniqueID}`);
                                if (titleEl) titleEl.textContent = truncate(linkValue.split('/').pop() || "Link", 50);
                            });


                        // Delete handler
                        container.querySelectorAll('.delete-file').forEach((btn) => {
                            btn.addEventListener('click', function () {
                                const col = this.closest('.col-12');
                                col.remove();
                            });
                        });

                        linkInput.value = '';
                        const popover = bootstrap.Popover.getInstance(el);
                        popover.hide();
                    });
                });
            });

            // File input change
            fileInput.addEventListener('change', function (event) {
                const maxFileSizeMB = 10; // max size per file
                const maxAttachments = 10; // max total files/links
                const toastContainer = document.getElementById("toastContainer");

                // Create a new DataTransfer but preserve old files
                let dt = new DataTransfer();

                // --- Keep previously added files (so they don't disappear) ---
                if (typeof allFiles !== "undefined" && allFiles.length > 0) {
                    allFiles.forEach(f => dt.items.add(f));
                }

                let errorMessages = [];
                let validFiles = [];

                // Validate new files
                Array.from(event.target.files).forEach(f => {
                    const fileSizeMB = f.size / (1024 * 1024);
                    if (fileSizeMB > maxFileSizeMB) {
                        errorMessages.push(`File "${f.name}" exceeds 10 MB and was not added.`);
                    } else {
                        validFiles.push(f);
                    }
                });

                // Check total attachments limit (existing + valid new files + existing links)
                const totalExisting = container.querySelectorAll('.col-12').length;
                if (totalExisting + validFiles.length > maxAttachments) {
                    errorMessages.push("You can only add up to 10 files or links total.");
                    // Trim validFiles to fit the limit
                    validFiles = validFiles.slice(0, maxAttachments - totalExisting);
                }

                if (errorMessages.length > 0) {
                    toastContainer.innerHTML = "";
                    errorMessages.forEach(msg => {
                        const alert = document.createElement("div");
                        alert.className = "alert mb-2 shadow-lg text-med text-12 d-flex align-items-center justify-content-center gap-2 px-3 py-2 alert-danger";
                        alert.role = "alert";
                        alert.innerHTML = `<i class="bi bi-x-circle-fill fs-6"></i><span>${msg}</span>`;
                        toastContainer.appendChild(alert);
                        setTimeout(() => alert.remove(), 5000);
                    });
                }

                // Add only valid files
                validFiles.forEach(f => dt.items.add(f));
                fileInput.files = dt.files;
                allFiles = Array.from(fileInput.files);

                // Only remove previews for previously uploaded new files, not DB files
                container.querySelectorAll('.new-file-preview').forEach(el => el.remove());

                function truncate(text, maxLength = 50) {
                    return text.length > maxLength ? text.slice(0, maxLength - 1) + '…' : text;
                }

                allFiles.forEach((file, index) => {
                    const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                    const ext = file.name.split('.').pop().toUpperCase();
                    const truncatedName = truncate(file.name, 50);

                    const fileHTML = `
                    <div class="col-12 my-1 new-file-preview file-preview">
                        <div class="materials-card d-flex align-items-stretch p-2 w-100 rounded-3">
                            <div class="d-flex w-100 align-items-center justify-content-between">
                                <div class="d-flex align-items-center flex-grow-1">
                                    <div class="mx-3 d-flex align-items-center">
                                        <span class="material-symbols-rounded">description</span>
                                    </div>
                                    <div>
                                        <div class="text-sbold text-16">${truncatedName}</div>
                                        <div class="text-reg text-12">${ext} · ${fileSizeMB} MB</div>
                                    </div>
                                </div>
                                <div class="mx-3 d-flex align-items-center delete-file" style="cursor:pointer;" data-index="${index}">
                                    <span class="material-symbols-outlined">close</span>
                                </div>
                            </div>
                        </div>
                    </div>`;
                    container.insertAdjacentHTML('beforeend', fileHTML);
                });

                // Enable deletion
                container.querySelectorAll('.delete-file').forEach((btn) => {
                    btn.addEventListener('click', function () {
                        const index = parseInt(this.dataset.index);
                        if (!isNaN(index)) {
                            allFiles.splice(index, 1);
                            let dt2 = new DataTransfer();
                            allFiles.forEach(f => dt2.items.add(f));
                            fileInput.files = dt2.files;
                        }
                        this.closest('.col-12').remove();
                    });
                });
            });
        });
    </script>

    <?php if (!empty($files)): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const container = document.getElementById('filePreviewContainer');
                if (!container) return;

                const existingLinksSet = new Set();

                function truncate(text, maxLength = 50) {
                    return text.length > maxLength ? text.slice(0, maxLength - 1) + '…' : text;
                }
                function renderFile(file, isNew = true) {
                    const uniqueID = Date.now() + Math.floor(Math.random() * 1000);
                    let html = '';

                    // Link
                    if (file.link && !file.attachment) {
                        if (existingLinksSet.has(file.link)) return; // prevent duplicates
                        existingLinksSet.add(file.link);

                        try {
                            const urlObj = new URL(file.link);
                            const faviconURL = `https://www.google.com/s2/favicons?sz=64&domain=${urlObj.hostname}`;
                            html = `
                    <div class="col-12 my-1 file-preview" data-id="${uniqueID}">
                        <div class="materials-card d-flex align-items-stretch p-2 w-100 rounded-3">
                            <div class="d-flex w-100 align-items-center justify-content-between">
                                <div class="d-flex align-items-center flex-grow-1">
                                    <div class="mx-3 d-flex align-items-center">
                                        <img src="${faviconURL}" alt="${urlObj.hostname} Icon"
                                            onerror="this.onerror=null;this.src='../shared/assets/img/web.png';"
                                            style="width: 30px; height: 30px;">
                                    </div>
                                    <div>
                                        <div class="text-sbold text-16" style="line-height:1.5;" id="title-${uniqueID}">${file.title}</div>
                                        <div class="text-reg text-12 text-break" style="line-height:1.5;">
                                            <a href="${file.link}" target="_blank">${truncate(file.link)}</a>
                                        </div>
                                        <div class="text-reg text-12" style="line-height:1.5; opacity:0.7;">
    ${file.ext}
</div>
                                    </div>
                                </div>
                                <div class="mx-3 d-flex align-items-center delete-file" style="cursor:pointer;">
                                    <span class="material-symbols-outlined">close</span>
                                </div>
                            </div>
                        </div>
                        ${isNew
                                ? `<input type="hidden" name="links[]" value="${file.link}" class="link-hidden">`
                                : `<input type="hidden" name="existingLinks[]" value="${file.link}">`}
                    </div>`;
                            container.insertAdjacentHTML('beforeend', html);

                            if (isNew) {
                                const titleEl = document.getElementById(`title-${uniqueID}`);
                                fetch("?fetchTitle=" + encodeURIComponent(file.link))
                                    .then(res => res.json())
                                    .then(data => { if (titleEl) titleEl.textContent = truncate(data.title || file.link, 50); })
                                    .catch(() => { if (titleEl) titleEl.textContent = truncate(file.link.split('/').pop() || "Link", 50); });
                            }
                        } catch (e) { }
                    }
                    // File
                    else if (file.attachment) {
                        html = `
                        <div class="col-12 my-1 file-preview" data-id="${uniqueID}">
                            <div class="materials-card d-flex align-items-stretch p-2 w-100 rounded-3">
                                <div class="d-flex w-100 align-items-center justify-content-between">
                                    <div class="d-flex align-items-center flex-grow-1 overflow-hidden">
                                        <div class="mx-3 d-flex align-items-center">
                                            <span class="material-symbols-rounded">description</span>
                                        </div>
                                        <div class="overflow-hidden">
                                            <div class="text-sbold text-16 file-title" style="line-height:1.5; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                                ${file.title}
                                            </div>
                                            <div class="text-reg text-12" style="line-height:1.5;">
                                                ${file.ext} · ${file.size}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mx-3 d-flex align-items-center delete-file" style="cursor:pointer;">
                                        <span class="material-symbols-outlined">close</span>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="existingFiles[]" value="${file.attachment}">
                        </div>`;
                        container.insertAdjacentHTML('beforeend', html);
                    }

                }

                // Render all DB files and links
                const files = <?php
                $jsFiles = [];
                foreach ($files as $file) {
                    $filePath = "../shared/assets/files/" . $file['fileAttachment'];

                    // --- NEW SMART EXTENSION HANDLING ---
                    $filename = $file['fileAttachment'] ?: basename(parse_url($file['fileLink'], PHP_URL_PATH));
                    $ext = strtoupper(pathinfo($filename, PATHINFO_EXTENSION));

                    if (!$ext) {
                        $ext = $file['fileAttachment'] ? 'FILE' : 'LINK';
                    }

                    $jsFiles[] = [
                        'title' => $file['fileTitle'] ?: $filename,
                        'attachment' => $file['fileAttachment'],
                        'link' => $file['fileLink'],
                        'ext' => $ext,
                        'size' => file_exists($filePath) ? round(filesize($filePath) / 1048576, 2) . ' MB' : 'Unknown size'
                    ];
                }
                echo json_encode($jsFiles);
                ?>;

                files.forEach(file => renderFile(file, false));

                // Event delegation for delete
                container.addEventListener('click', function (event) {
                    const deleteBtn = event.target.closest('.delete-file');
                    if (!deleteBtn) return;

                    const preview = deleteBtn.closest('.col-12');
                    if (!preview) return;

                    const input = preview.querySelector('input');
                    if (!input) return;

                    let removeContainer = document.getElementById('removeFilesContainer');
                    if (!removeContainer) {
                        removeContainer = document.createElement('div');
                        removeContainer.id = 'removeFilesContainer';
                        removeContainer.style.display = 'none';
                        container.closest('form').appendChild(removeContainer);
                    }

                    if (input.name === 'existingFiles[]') {
                        const removedInput = document.createElement('input');
                        removedInput.type = 'hidden';
                        removedInput.name = 'removeFiles[]';
                        removedInput.value = input.value;
                        removeContainer.appendChild(removedInput);
                    } else if (input.name === 'existingLinks[]' || input.name === 'links[]') {
                        const removedInput = document.createElement('input');
                        removedInput.type = 'hidden';
                        removedInput.name = 'removeLinks[]';
                        removedInput.value = input.value;
                        removeContainer.appendChild(removedInput);

                        // Remove from Set to allow re-adding if deleted
                        existingLinksSet.delete(input.value);
                    }

                    // Remove preview and input
                    input.remove();
                    preview.remove();
                });

                // Add new files/links dynamically
                window.addNewFiles = function (newFiles) {
                    newFiles.forEach(file => renderFile(file, true));
                };
            });

        </script>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>