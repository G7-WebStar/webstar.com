<?php $activePage = 'add-lesson'; ?>
<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include('../shared/assets/database/connect.php');
date_default_timezone_set('Asia/Manila');
include("../shared/assets/processes/prof-session-process.php");

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

function fetchLinkTitle($link)
{
    $link = trim($link);
    $title = '';

    // Process Google links
    $link = processGoogleLink($link);

    // Ensure the link has a scheme
    if (!preg_match('/^https?:\/\//', $link)) {
        $link = 'http://' . $link;
    }

    // Set context for user-agent
    $options = [
        "http" => [
            "header" => "User-Agent: Mozilla/5.0\r\n",
            "timeout" => 5
        ]
    ];
    $context = stream_context_create($options);

    try {
        $html = @file_get_contents($link, false, $context);
        if ($html && preg_match("/<title>(.*?)<\/title>/is", $html, $matches)) {
            $title = trim($matches[1]);
        }
    } catch (Exception $e) {
        $title = '';
    }

    // Fallback to domain if title is empty
    if (empty($title)) {
        $parsedUrl = parse_url($link);
        $title = isset($parsedUrl['host']) ? ucfirst(str_replace('www.', '', $parsedUrl['host'])) : 'Link';
    }

    return $title;
}

if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
    require '../shared/assets/phpmailer/src/Exception.php';
    require '../shared/assets/phpmailer/src/PHPMailer.php';
    require '../shared/assets/phpmailer/src/SMTP.php';
}

$mode = '';
$lessonID = 0;

if (isset($_GET['edit'])) {
    $mode = 'edit';
    $lessonID = intval($_GET['edit']);
} elseif (isset($_GET['reuse'])) {
    $mode = 'reuse';
    $lessonID = intval($_GET['reuse']);
}

// Get all courses owned by this user
$course = "SELECT courseID, courseCode 
           FROM courses 
           WHERE userID = '$userID'";
$courses = executeQuery($course);

if (isset($_POST['save_lesson'])) {
    $mode = $_POST['mode'] ?? 'new'; // new, reuse, or edit
    $lessonID = intval($_POST['lessonID'] ?? 0); // used in edit mode
    $titleRaw = $_POST['lessonTitle'];
    $title = mysqli_real_escape_string($conn, $titleRaw);
    $contentRaw = $_POST['lessonContent'];
    $content = mysqli_real_escape_string($conn, $contentRaw);
    $links = $_POST['links'] ?? [];
    $uploadedFiles = !empty($_FILES['materials']['name'][0]);
    $createdAt = date("Y-m-d H:i:s");

    if (!empty($_POST['courses'])) {
        foreach ($_POST['courses'] as $selectedCourseID) {

            if ($mode === 'new' || $mode === 'reuse') {
                // INSERT new lesson
                $lessons = "INSERT INTO lessons 
                    (courseID, lessonTitle, lessonDescription, createdAt) 
                    VALUES 
                    ('$selectedCourseID', '$title', '$content', '$createdAt')";
                executeQuery($lessons);
                $lessonID = mysqli_insert_id($conn);
            } elseif ($mode === 'edit') {
                // UPDATE existing lesson
                $updateLesson = "UPDATE lessons 
                    SET courseID='$selectedCourseID', lessonTitle='$title', lessonDescription='$content', createdAt='$createdAt'
                    WHERE lessonID='$lessonID'";
                executeQuery($updateLesson);

                // Delete old files only if new files or links are provided
                if (!empty($_FILES['materials']['name'][0]) || !empty($_POST['links'])) {
                    executeQuery("DELETE FROM files WHERE lessonID='$lessonID'");
                }
            }

            // --- Handle uploaded files ---
            if (!empty($_FILES['materials']['name'][0])) {
                $uploadDir = __DIR__ . "/../shared/assets/files/";

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                foreach ($_FILES['materials']['name'] as $key => $fileName) {
                    $tmpName = $_FILES['materials']['tmp_name'][$key];
                    $fileError = $_FILES['materials']['error'][$key];

                    if ($fileError === UPLOAD_ERR_OK) {
                        $safeName = str_replace(" ", "_", basename($fileName));
                        $targetPath = $uploadDir . $safeName;

                        if (move_uploaded_file($tmpName, $targetPath)) {
                            $insertFile = "INSERT INTO files 
                                (courseID, userID, lessonID, fileAttachment, fileLink) 
                                VALUES 
                                ('$selectedCourseID', '$userID', '$lessonID', '$safeName', '')";
                            executeQuery($insertFile);
                        }
                    }
                }
            }

            // --- Handle file links ---
            if (!empty($_POST['links'])) {
                $links = $_POST['links'];
                if (is_array($links)) {
                    foreach ($links as $link) {
                        $link = trim($link);
                        if ($link !== '') {
                            // Process Google links and fetch title
                            $processedLink = processGoogleLink($link);
                            $fileTitle = fetchLinkTitle($link);

                            $insertLink = "INSERT INTO files 
                    (courseID, userID, lessonID, fileAttachment, fileTitle, fileLink) 
                    VALUES 
                    ('$selectedCourseID', '$userID', '$lessonID', '', '" . mysqli_real_escape_string($conn, $fileTitle) . "', '$processedLink')";
                            executeQuery($insertLink);
                        }
                    }
                }
            }

            // --- Notifications & Email (only for new/reuse) ---
            if ($mode === 'new' || $mode === 'reuse') {
                // Use lesson title for consistent notification message across all courses
                $notificationMessage = "A new lesson has been added: " . $titleRaw;
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

                // Insert notifications only if they don't already exist for each student
                // This prevents duplicates when a lesson is assigned to multiple courses
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
                executeQuery($insertNotificationQuery);

                // Email enrolled students who opted-in
                $selectEmailsQuery = "
                    SELECT u.email, u.userID,
                           COALESCE(s.courseUpdateEnabled, 0) as courseUpdateEnabled
                    FROM users u
                    INNER JOIN enrollments e ON u.userID = e.userID
                    LEFT JOIN settings s ON u.userID = s.userID
                    WHERE e.courseID = '$selectedCourseID'
                ";
                $emailsResult = executeQuery($selectEmailsQuery);

                try {
                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'learn.webstar@gmail.com';
                    $mail->Password   = 'mtls vctd rhai cdem';
                    $mail->SMTPSecure = 'tls';
                    $mail->Port       = 587;
                    $mail->setFrom('learn.webstar@gmail.com', 'Webstar');
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
                    $mail->Subject = "[NEW LESSON] " . $titleRaw . " for Course " . $courseCode;

                    $recipientsFound = false;
                    while ($student = mysqli_fetch_assoc($emailsResult)) {
                        if ($student['courseUpdateEnabled'] == 1 && !empty($student['email'])) {
                            $mail->addAddress($student['email']);
                            $recipientsFound = true;
                        }
                    }

                    if ($recipientsFound) {
                        $emailTitleEsc = htmlspecialchars($titleRaw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
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
                                                        A new lesson has been posted in your course <strong>' . $courseCodeEsc . '</strong>.
                                                    </p>
                                                    <h2 style="text-align:center; font-size:24px; color:#2C2C2C; margin:20px 0;">' . $emailTitleEsc . '</h2>
                                                    <p style="font-size:15px; color:#333; margin-top: 25px;">
                                                        <strong>Lesson Description:</strong>
                                                    </p>
                                                    <div style="font-size:15px; color:#333; margin-bottom: 20px; line-height: 22px;">
                                                        ' . $contentRaw . '
                                                    </div>
                                                    <p style="font-size:15px; color:#333;">
                                                        Please log in to your Webstar account to access and view the lesson materials.
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

// Selecting Lessons made by the Instructor logged in
$lessonQuery = "
    SELECT 
        l.lessonID,
        l.lessonTitle,
        l.lessonDescription,
        l.createdAt,
        c.courseID,
        c.courseCode
    FROM lessons l
    JOIN courses c ON l.courseID = c.courseID
    WHERE c.userID = '$userID'
    ORDER BY l.createdAt DESC
";

$lessons = executeQuery($lessonQuery);

// Storing reused data
$reusedData = null;

// If the instructor chose to reuse or edit a lesson
if (isset($_GET['reuse']) || isset($_GET['edit'])) {
    $reuseID = isset($_GET['reuse']) ? intval($_GET['reuse']) : intval($_GET['edit']);

    // Get lesson data
    $reuseQuery = "
        SELECT 
            l.lessonTitle,
            l.lessonDescription,
            l.courseID,
            l.createdAt,
            crs.courseID,
            crs.courseCode,
            f.fileID,
            f.fileAttachment,
            f.fileTitle,
            f.fileLink
        FROM lessons l
        JOIN courses crs ON l.courseID = crs.courseID
        JOIN users u ON crs.userID = u.userID
        LEFT JOIN files f ON l.lessonID = f.lessonID
        WHERE l.lessonID = '$reuseID'
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
        header("Location: add-lesson.php");
        exit();
    }
}

if (!empty($reusedData)) {
    // Populate main fields for reuse/edit
    $mainData = [
        'lessonTitle' => $reusedData[0]['lessonTitle'],
        'lessonDescription' => $reusedData[0]['lessonDescription']
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
        echo isset($_GET['reuse']) ? 'Reuse Lesson' : (isset($_GET['edit']) ? 'Edit Lesson' : 'Add Lesson');
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
                                            echo 'Edit Lesson';
                                        } elseif (isset($_GET['reuse'])) {
                                            echo 'Reuse Lesson';
                                        } else {
                                            echo 'Add Lesson';
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
                                            <span class="material-symbols-rounded" style="font-size:16px">notes</span>
                                            <span>Add an existing lesson</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Form starts -->
                                <form action="" id="addLessonForm" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="mode"
                                        value="<?= isset($_GET['edit']) ? 'edit' : (isset($_GET['reuse']) ? 'reuse' : 'new') ?>">
                                    <input type="hidden" name="lessonID"
                                        value="<?= $_GET['edit'] ?? $_GET['reuse'] ?? '' ?>">
                                    <div class="row">
                                        <div class="col-12 pt-3">
                                            <label for="lessonInfo" class="form-label text-med text-16">Lesson
                                                Information</label>
                                            <input type="text"
                                                class="form-control form-control textbox mb-2 px-3 py-2 text-reg text-16"
                                                id="lessonInfo" name="lessonTitle" placeholder="Lesson Title *"
                                                value="<?php echo isset($mainData) ? htmlspecialchars($mainData['lessonTitle']) : ''; ?>"
                                                required>
                                        </div>
                                    </div>

                                    <!-- Rich Text Editor -->
                                    <div class="row">
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
                                            <!-- Hidden input to capture editor content -->
                                            <input type="hidden" name="lessonContent" id="lesson">
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
                                                    <input type="hidden" name="links[]" id="lessonLinks">
                                                </div>
                                            </div>

                                            <!-- Course selection + Post button -->
                                            <div class="row align-items-center justify-content-between text-center text-md-start mt-5"
                                                style="margin-bottom: <?php echo ($courses && $courses->num_rows > 0) ? ($courses->num_rows * 50) : 0; ?>px;">
                                                <!-- Dynamic Border Bottom based on the number of courses hehe -->
                                                <!-- Add to Course -->
                                                <?php if (!isset($_GET['edit'])): ?>
                                                    <div
                                                        class="col-12 col-md-auto mt-3 mt-md-0 d-flex align-items-center flex-nowrap justify-content-center justify-content-md-start">
                                                        <span class="me-2 text-med text-16 pe-3">Add to
                                                            Course</span>
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
                                                                    // If editing, get the assigned courses for this task
                                                                    $assignedCourseIDs = [];
                                                                    if (isset($_GET['edit'])) {
                                                                        $editID = intval($_GET['edit']);
                                                                        $assignedQuery = "SELECT courseID FROM lessons WHERE lessonID = '$editID'";
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
                                                                    <?php }
                                                                } else { ?>
                                                                    <li><span class="dropdown-item-text text-muted">No
                                                                            courses found</span></li>
                                                                <?php } ?>
                                                            </ul>

                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <!-- Assign Button -->
                                                <div class="col-12 col-md-auto mt-3 mt-md-0 text-center">
                                                    <button type="submit" name="save_lesson"
                                                        class="px-4 py-2 rounded-pill text-sbold text-md-14 mt-4 mt-md-0"
                                                        style="background-color: var(--primaryColor); border: 1px solid var(--black);">
                                                        <?php
                                                        if (isset($_GET['edit'])) {
                                                            echo 'Save Changes';
                                                        } elseif (isset($_GET['reuse'])) {
                                                            echo 'Reuse';
                                                        } else {
                                                            echo 'Add';
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

    <!-- Reuse Lesson Modal -->
    <div class="modal fade" id="reuseTaskModal" tabindex="-1" aria-labelledby="reuseTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered py-4" style="max-width: 700px; height: 80vh">
            <div class="modal-content d-flex flex-column" style="height: 100%;">

                <!-- HEADER -->
                <div class="modal-header flex-shrink-0">
                    <div class="modal-title text-sbold text-20 ms-3" id="selectRubricModalLabel">
                        Add an existing lesson
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        style="transform: scale(0.8); filter: grayscale(100%);"></button>
                </div>

                <!-- DESC -->
                <div class="modal-body flex-grow-1 overflow-auto">
                    <p class="mb-3 text-med text-14 mx-3" style="color: var(--black);">
                        Select a lesson you’ve previously created to reuse its details. You can review and edit the
                        title,
                        description, or attached materials before assigning or saving it.
                    </p>
                    <div class="col p-0 m-3">
                        <div class="card rounded-3 mb-2 border-0">
                            <div class="card-body p-0">
                                <!-- OPTIONS -->
                                <?php if ($lessons && $lessons->num_rows > 0) {
                                    while ($lesson = $lessons->fetch_assoc()) {
                                        ?>
                                        <div class="rubric-option rounded-3 d-flex align-items-center justify-content-between mb-2 w-100"
                                            style="cursor: pointer; background-color: var(--pureWhite); border: 1px solid var(--black);"
                                            onclick='window.location.href="add-lesson.php?reuse=<?php echo $lesson["lessonID"]; ?>"'>
                                            <div style="line-height: 1.5; padding:10px 15px;">
                                                <div class="text-sbold text-14"
                                                    style="display:-webkit-box; -webkit-line-clamp:1; -webkit-box-orient:vertical; overflow:hidden;">
                                                    <?php echo htmlspecialchars(substr($lesson['lessonTitle'], 0, 100)); ?>
                                                </div>
                                                <div class="text-med text-12"
                                                    style="display:-webkit-box; -webkit-line-clamp:1; -webkit-box-orient:vertical; overflow:hidden;">
                                                    <?php echo substr(strip_tags($lesson['lessonDescription']), 0, 100); ?>
                                                </div>
                                                <div class="text-med text-muted text-12"
                                                    style="display:-webkit-box; -webkit-line-clamp:1; -webkit-box-orient:vertical; overflow:hidden;">
                                                    <?php
                                                    echo date('F j, Y g:i A', strtotime($lesson['createdAt']))
                                                        . ' · ' . $lesson['courseCode'];
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <div class="text-muted text-reg text-14 py-2">No existing lessons found.</div>
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
            placeholder: 'Lesson Description / Objectives',
            modules: {
                toolbar: '#toolbar'
            }
        });

        <?php if (isset($reusedData)) { ?>
            quill.root.innerHTML = <?php echo json_encode($mainData['lessonDescription']); ?>;
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

        // Sync Quill content to hidden input before form submit
        const form = document.querySelector('#addLessonForm'); // form ID
        form.addEventListener('submit', function (e) {
            const lessonInput = document.querySelector('#lesson'); // hidden input

            // Convert Quill HTML to plain text with bullets/line breaks
            let html = quill.root.innerHTML;
            html = html.replace(/<p>/g, '').replace(/<\/p>/g, '<br>');
            html = html.replace(/<li>/g, '• ').replace(/<\/li>/g, '<br>');
            html = html.replace(/<\/?(ul|ol)>/g, '');
            html = html.replace(/(<br>)+$/g, '');
            lessonInput.value = html.trim();
        });

        // Ensure at least one course is selected
        document.querySelector("form").addEventListener("submit", function (e) {
            let checkboxes = document.querySelectorAll(".course-checkbox");
            let checked = Array.from(checkboxes).some(cb => cb.checked);
            if (!checked) {
                e.preventDefault();
                alert("Please select at least one course before submitting.");
            }
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

                        // Check total attachments limit
                        const totalAttachments = container.querySelectorAll('.col-12').length;
                        if (totalAttachments >= 10) {
                            alert("You can only add up to 10 files or links total.");
                            return;
                        }

                        // Get domain and favicon
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
                                            <a href="${linkValue}" target="_blank">${linkValue}</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="mx-3 d-flex align-items-center delete-file" style="cursor:pointer;">
                                    <span
                                    class="material-symbols-outlined">close</span>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="links[]" value="${linkValue}" class="link-hidden">
                    </div>
                `;
                        container.insertAdjacentHTML('beforeend', previewHTML);

                        // Fetch page title
                        fetch("?fetchTitle=" + encodeURIComponent(linkValue))
                            .then(res => res.json())
                            .then(data => {
                                const titleEl = document.getElementById(`title-${uniqueID}`);
                                if (titleEl) titleEl.textContent = data.title || linkValue;
                            }).catch(() => {
                                const titleEl = document.getElementById(`title-${uniqueID}`);
                                if (titleEl) titleEl.textContent = linkValue.split('/').pop() || "Link";
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
                // Merge new selections with existing files
                let dt = new DataTransfer();
                Array.from(allFiles).forEach(f => dt.items.add(f));
                Array.from(event.target.files).forEach(f => dt.items.add(f));
                fileInput.files = dt.files;
                allFiles = Array.from(fileInput.files); // update allFiles list

                // 🔹 Remove only file previews, keep links
                container.querySelectorAll('.file-preview').forEach(el => el.remove());

                // Rebuild file previews
                allFiles.forEach((file, index) => {
                    const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                    const ext = file.name.split('.').pop().toUpperCase();
                    const fileHTML = `
               <div class="col-12 my-1 file-preview">
                    <div class="materials-card d-flex align-items-stretch p-2 w-100 rounded-3">
                        <div class="d-flex w-100 align-items-center justify-content-between">
                            <div class="d-flex align-items-center flex-grow-1">
                                <div class="mx-3 d-flex align-items-center">
                                    <span class="material-symbols-rounded">
                                        description
                                    </span>
                                </div>
                                <div>
                                    <div class="text-sbold text-16" style="line-height: 1.5;">${file.name}</div>
                                    <div class="text-reg text-12 " style="line-height: 1.5;">${ext} · ${fileSizeMB} MB</div>
                                </div>
                            </div>
                            <div class="mx-3 d-flex align-items-center delete-file" style="cursor:pointer;" data-index="${index}">
                                <span
                                    class="material-symbols-outlined">close</span>
                            </div>
                        </div>
                    </div>
                </div>`;
                    container.insertAdjacentHTML('beforeend', fileHTML);
                });

                // Allow deletion of specific files
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

                <?php foreach ($files as $file): ?>
                    <?php if (!empty($file['fileLink'])): ?>
                            // Render Link Preview
                            (function () {
                                const linkValue = <?php echo json_encode($file['fileLink']); ?>;
                                const uniqueID = Date.now() + Math.floor(Math.random() * 1000);
                                try {
                                    const urlObj = new URL(linkValue);
                                    const domain = urlObj.hostname;
                                    const faviconURL = `https://www.google.com/s2/favicons?sz=64&domain=${domain}`;
                                    const html = `
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
                                            <div id="title-${uniqueID}" class="text-sbold text-16" style="line-height: 1.5;"><?= htmlspecialchars($file['fileTitle']) ?></div>
                                            <div class="text-reg text-12 text-break" style="line-height: 1.5;">
                                                <a href="${linkValue}" target="_blank">${linkValue}</a>
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
                                    container.insertAdjacentHTML('beforeend', html);
                                } catch (e) { }
                            })();
                    <?php elseif (!empty($file['fileAttachment'])): ?>
                            // Render File Preview
                            (function () {
                                const fileName = <?php echo json_encode($file['fileTitle'] ?: basename($file['fileAttachment'])); ?>;
                                const ext = fileName.split('.').pop().toUpperCase();
                                const uniqueID = Date.now() + Math.floor(Math.random() * 1000);
                                <?php
                                $filePath = "../shared/assets/files/" . $file['fileAttachment'];
                                $fileExt = strtoupper(pathinfo($file['fileAttachment'], PATHINFO_EXTENSION));
                                $fileSize = (file_exists($filePath)) ? filesize($filePath) : 0;
                                $fileSizeMB = $fileSize > 0 ? round($fileSize / 1048576, 2) . " MB" : "Unknown size";
                                ?>

                                const html = `
                    <div class="col-12 my-1 file-preview">
                        <div class="materials-card d-flex align-items-stretch p-2 w-100 rounded-3">
                            <div class="d-flex w-100 align-items-center justify-content-between">
                                <div class="d-flex align-items-center flex-grow-1">
                                    <div class="mx-3 d-flex align-items-center">
                                        <span class="material-symbols-rounded">description</span>
                                    </div>
                                    <div>
                                        <div class="text-sbold text-16" style="line-height: 1.5;"><?= htmlspecialchars($file['fileAttachment']) ?></div>
                                        <div class="text-reg text-12" style="line-height: 1.5;">
    <?= $fileExt ?> · <?= $fileSizeMB ?>
</div>

                                    </div>
                                </div>
                                <div class="mx-3 d-flex align-items-center delete-file" style="cursor:pointer;">
                                    <span class="material-symbols-outlined">close</span>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="existingFiles[]" value="<?php echo htmlspecialchars($file['fileAttachment']); ?>">
                    </div>`;
                                container.insertAdjacentHTML('beforeend', html);
                            })();
                    <?php endif; ?>
                <?php endforeach; ?>
            });
            //  Enable delete buttons for preloaded (reused) items
            document.addEventListener('click', function (event) {
                if (event.target.closest('.delete-file')) {
                    const col = event.target.closest('.col-12');
                    if (col) col.remove();
                }
            });

        </script>
    <?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>