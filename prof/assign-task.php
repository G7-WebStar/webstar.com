<?php $activePage = 'assign-task';
date_default_timezone_set('Asia/Manila');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include('../shared/assets/database/connect.php');

include("../shared/assets/processes/prof-session-process.php");

$toastMessage = '';
$toastType = '';

if (isset($_SESSION['toast'])) {
    $toastMessage = $_SESSION['toast']['message'];
    $toastType = $_SESSION['toast']['type'];
    unset($_SESSION['toast']);
}

$errorMessages = [
    "emailNoCredential" => "No email credentials found in the database!"
];

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
    $processedLink = processGoogleLink($link);

    // Always use original link for fetching title
    $titleFetchUrl = $link;

    // Ensure the link has a scheme
    if (!preg_match('/^https?:\/\//', $titleFetchUrl)) {
        $titleFetchUrl = 'http://' . $titleFetchUrl;
    }

    // --- YouTube Title Detection ---
    if (preg_match('/(youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $titleFetchUrl, $ytMatch)) {
        $oembedUrl = "https://www.youtube.com/oembed?url=" . urlencode($titleFetchUrl) . "&format=json";
        $json = @file_get_contents($oembedUrl);
        if ($json !== false) {
            $data = json_decode($json, true);
            if (isset($data['title'])) {
                return $data['title']; // 🟢 Return YouTube Title Immediately
            }
        }
    }

    // Set context for user-agent
    $options = [
        "http" => [
            "header" => "User-Agent: Mozilla/5.0\r\n",
            "timeout" => 5
        ]
    ];
    $context = stream_context_create($options);

    // --- General HTML Title Fetch ---
    try {
        $html = @file_get_contents($titleFetchUrl, false, $context);

        if ($html) {

            // Try OG title first
            if (preg_match('/<meta property="og:title" content="([^"]+)"/i', $html, $matches)) {
                $title = trim($matches[1]);
            }

            // Try <title> tag
            elseif (preg_match("/<title>(.*?)<\/title>/is", $html, $matches)) {
                $title = trim($matches[1]);
            }
        }
    } catch (Exception $e) {
        $title = '';
    }

    // Fallback to domain if title is empty
    if (empty($title)) {
        $parsedUrl = parse_url($titleFetchUrl);
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
$taskID = 0;

if (isset($_GET['edit'])) {
    $mode = 'edit';
    $taskID = intval($_GET['edit']);
} elseif (isset($_GET['reuse'])) {
    $mode = 'reuse';
    $taskID = intval($_GET['reuse']);
}

$course = "SELECT courseID, courseCode 
           FROM courses 
           WHERE userID = '$userID'";
$courses = executeQuery($course);

// Save Assignment Query
if (isset($_POST['saveAssignment'])) {
    $mode = $_POST['mode'] ?? 'new';
    $taskID = intval($_POST['taskID'] ?? 0);

    $titleRaw = $_POST['assignmentTitle'] ?? '';
    $title = mysqli_real_escape_string($conn, $titleRaw);
    $descRaw = $_POST['assignmentContent'] ?? '';
    $desc = mysqli_real_escape_string($conn, $descRaw);
    $deadline = !empty($_POST['deadline']) ? $_POST['deadline'] : null;
    $points = !empty($_POST['points']) ? $_POST['points'] : 0;
    $rubricID = !empty($_POST['selectedRubricID']) ? intval($_POST['selectedRubricID']) : null;
    $links = $_POST['links'] ?? [];
    $uploadedFiles = !empty($_FILES['materials']['name'][0]);
    $existingFiles = $_POST['existingFiles'] ?? []; // files user kept

    foreach ($_POST['courses'] ?? [] as $selectedCourseID) {
        $deadlineEnabled = isset($_POST['stopSubmissions']) ? 1 : 0;

        if ($mode === 'new') {
            // --- INSERT new assessment ---
            $insertAssessment = "INSERT INTO assessments 
                (courseID, assessmentTitle, type, deadline, deadlineEnabled, createdAt)
                VALUES 
                ('$selectedCourseID', '$title', 'Task', " . ($deadline ? "'$deadline'" : "NULL") . ", '$deadlineEnabled', NOW())";
            executeQuery($insertAssessment);
            $assessmentID = mysqli_insert_id($conn);

            // --- INSERT new assignment ---
            $insertAssignment = "INSERT INTO assignments 
                (assessmentID, assignmentDescription, assignmentPoints, rubricID)
                VALUES 
                ('$assessmentID', '$desc', '$points', '$rubricID')";
            executeQuery($insertAssignment);
            $assignmentID = mysqli_insert_id($conn);

        } elseif ($mode === 'edit') {

            // --- Get deadlineEnabled (checkbox) ---
            $deadlineEnabled = isset($_POST['stopSubmissions']) ? 1 : 0;

            // --- UPDATE existing assessment ---
            $updateAssessment = "UPDATE assessments 
                SET assessmentTitle='$title',
                    deadline=" . ($deadline ? "'$deadline'" : "NULL") . ",
                    deadlineEnabled='$deadlineEnabled'
                WHERE assessmentID='$taskID'";
            executeQuery($updateAssessment);

            // --- FETCH correct assignmentID ---
            $assignmentQuery = "SELECT assignmentID FROM assignments WHERE assessmentID='$taskID' LIMIT 1";
            $assignmentResult = executeQuery($assignmentQuery);
            $assignmentRow = mysqli_fetch_assoc($assignmentResult);
            $assignmentID = $assignmentRow['assignmentID'];

            // --- UPDATE assignment ---
            $updateAssignment = "UPDATE assignments 
                SET assignmentDescription='$desc', assignmentPoints='$points', rubricID='$rubricID'
                WHERE assignmentID='$assignmentID'";
            executeQuery($updateAssignment);
        } elseif ($mode === 'reuse') {
            // --- CREATE NEW ASSESSMENT ---
            $insertAssessment = "INSERT INTO assessments
        (courseID, assessmentTitle, type, deadline, deadlineEnabled, createdAt)
        VALUES
        ('$selectedCourseID', '$title', 'Task', " . ($deadline ? "'$deadline'" : "NULL") . ", '$deadlineEnabled', NOW())";
            executeQuery($insertAssessment);
            $assessmentID = mysqli_insert_id($conn);

            // --- CREATE NEW ASSIGNMENT LINKED TO NEW ASSESSMENT ---
            $insertAssignment = "INSERT INTO assignments
        (assessmentID, assignmentDescription, assignmentPoints, rubricID)
        VALUES
        ('$assessmentID', '$desc', '$points', '$rubricID')";
            executeQuery($insertAssignment);
            $assignmentID = mysqli_insert_id($conn);

            // --- COPY FILES / LINKS FROM OLD ASSIGNMENT ---
            $oldAssignmentQuery = "SELECT assignmentID FROM assignments WHERE assessmentID='$taskID' LIMIT 1";
            $oldAssignmentResult = executeQuery($oldAssignmentQuery);
            $oldAssignmentRow = mysqli_fetch_assoc($oldAssignmentResult);
            $oldAssignmentID = $oldAssignmentRow['assignmentID'];

            $filesQuery = "SELECT * FROM files WHERE assignmentID='$oldAssignmentID'";
            $filesResult = executeQuery($filesQuery);
            while ($file = mysqli_fetch_assoc($filesResult)) {
                $fileAttachment = $file['fileAttachment'] ?? null;
                $fileLink = $file['fileLink'] ?? null;
                $fileTitle = mysqli_real_escape_string($conn, $file['fileTitle'] ?? '');

                // INSERT with correct courseID and userID
                $insertFile = "INSERT INTO files
            (assignmentID, fileAttachment, fileLink, fileTitle, courseID, userID)
            VALUES
            ('$assignmentID', '$fileAttachment', '$fileLink', '$fileTitle', '$selectedCourseID', '$userID')";
                executeQuery($insertFile);
            }

            // --- INSERT TODOS FOR STUDENTS ENROLLED IN THE NEW COURSE ---
            $studentsQuery = "SELECT userID FROM enrollments WHERE courseID = '$selectedCourseID'";
            $studentsResult = executeQuery($studentsQuery);
            while ($student = mysqli_fetch_assoc($studentsResult)) {
                $studentUserID = $student['userID'];
                $todoQuery = "INSERT INTO todo (userID, assessmentID, status, isRead)
            VALUES ('$studentUserID', '$assessmentID', 'Pending', 0)";
                executeQuery($todoQuery);
            }
        }

        // --- REMOVE FILES if requested ---
        if (!empty($_POST['removeFiles'])) {
            $removeFiles = $_POST['removeFiles'];
            $removeFilesStr = implode("','", array_map(function ($file) use ($conn) {
                return mysqli_real_escape_string($conn, $file);
            }, $removeFiles));

            $deleteQuery = "DELETE FROM files 
                WHERE assignmentID='$assignmentID' 
                AND fileAttachment IN ('$removeFilesStr')";
            executeQuery($deleteQuery);

            foreach ($removeFiles as $file) {
                $filePath = __DIR__ . "/../shared/assets/files/" . $file;
                if (file_exists($filePath))
                    unlink($filePath);
            }
        }

        // --- REMOVE LINKS if requested ---
        if (!empty($_POST['removeLinks'])) {
            $removeLinks = $_POST['removeLinks'];
            $removeLinksStr = implode("','", array_map(function ($link) use ($conn) {
                return mysqli_real_escape_string($conn, $link);
            }, $removeLinks));

            $deleteQuery = "DELETE FROM files 
                WHERE assignmentID='$assignmentID' 
                AND fileLink IN ('$removeLinksStr')";
            executeQuery($deleteQuery);
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

                    // Get original filename and extension
                    $originalName = pathinfo($fileName, PATHINFO_FILENAME);
                    $extension = pathinfo($fileName, PATHINFO_EXTENSION);

                    // Replace symbols and spaces with underscores (keep letters, numbers, dash, underscore)
                    $safeName = preg_replace('/[^a-zA-Z0-9-_]/', '_', $originalName);

                    // Reattach the extension
                    $fileTitle = $safeName . '.' . $extension;

                    // Stored filename on server (unique)
                    $fileAttachment = date('Ymd_His') . '_' . $fileTitle;

                    $targetPath = $uploadDir . $fileAttachment;

                    if (move_uploaded_file($tmpName, $targetPath)) {

                        executeQuery("
                        INSERT INTO files 
                        (courseID, userID, assignmentID, fileAttachment, fileTitle, fileLink) 
                        VALUES 
                        ('$selectedCourseID', '$userID', '$assignmentID', '$fileAttachment', '$fileTitle', '')
                    ");
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

                // Skip Google Drive conversion for YouTube links
                if (preg_match('/(youtube\.com|youtu\.be)/i', $link)) {
                    $processedLink = $link;
                } else {
                    $processedLink = processGoogleLink($link);
                }

                $insertLink = "INSERT INTO files 
                (courseID, userID, assignmentID, fileAttachment, fileTitle, fileLink) 
                VALUES 
                ('$selectedCourseID', '$userID', '$assignmentID', '', '" .
                mysqli_real_escape_string($conn, $fileTitle) . "', '$processedLink')";
                executeQuery($insertLink);
            }
        }

        if ($mode === 'new' || $mode === 'reuse') {
            // Insert todos for each student enrolled in the course
            $studentsQuery = "SELECT userID, enrollmentID FROM enrollments WHERE courseID = '$selectedCourseID'";
            $studentsResult = executeQuery($studentsQuery);
            if ($studentsResult) {
                while ($student = mysqli_fetch_assoc($studentsResult)) {
                    $studentUserID = $student['userID'];
                    $todoQuery = "INSERT INTO todo (userID, assessmentID, status, isRead)
                        VALUES ('$studentUserID', '$assessmentID', 'Pending', 0)";
                    executeQuery($todoQuery);
                }
            }

            // --- Notifications & Email ---
            $notificationMessage = "A new task has been assigned: " . $titleRaw;
            $notifType = 'Course Update';
            $courseCode = "";

            // Fetch course code for email
            $selectCourseDetailsQuery = "SELECT courseCode FROM courses WHERE courseID = '$selectedCourseID'";
            $courseDetailsResult = executeQuery($selectCourseDetailsQuery);
            if ($courseDetailsResult && mysqli_num_rows($courseDetailsResult) > 0) {
                $courseData = mysqli_fetch_assoc($courseDetailsResult);
                $courseCode = $courseData['courseCode'];
            }

            $escapedNotificationMessage = mysqli_real_escape_string($conn, $notificationMessage);
            $escapedNotifType = mysqli_real_escape_string($conn, $notifType);

            // For 'new' mode: prevent duplicates within 5 minutes
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
                       COALESCE(s.questDeadlineEnabled, 0) as questDeadlineEnabled
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
                    $mail->Subject = "[NEW TASK] " . $titleRaw . " for Course " . $courseCode;

                    $recipientsFound = false;
                    while ($student = mysqli_fetch_assoc($emailsResult)) {
                        if ($student['questDeadlineEnabled'] == 1 && !empty($student['email'])) {
                            $mail->addAddress($student['email']);
                            $recipientsFound = true;
                        }
                    }

                    if ($recipientsFound) {
                        $deadlineDisplay = 'Not set';
                        if (!empty($deadline)) {
                            try {
                                $deadlineDate = new DateTime($deadline);
                                $deadlineDisplay = $deadlineDate->format('F j, Y \\a\\t g:i A');
                            } catch (Exception $e) {
                                $deadlineDisplay = $deadline;
                            }
                        }

                        $pointsDisplay = ($points !== '' && $points !== null) ? $points : 'Ungraded';
                        $descHtml = nl2br(htmlspecialchars($descRaw, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
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
                                                        A new task has been assigned in your course <strong>' . $courseCodeEsc . '</strong>.
                                                    </p>
                                                    <h2 style="text-align:center; font-size:24px; color:#2C2C2C; margin:20px 0;">' . $emailTitleEsc . '</h2>
                                                    <p style="font-size:15px; color:#333; margin-top: 25px;">
                                                        <strong>Task Instructions:</strong>
                                                    </p>
                                                    <div style="font-size:15px; color:#333; margin-bottom: 20px; line-height: 22px;">
                                                        ' . $descHtml . '
                                                    </div>
                                                    <div style="background-color:#f9f9f9; padding:15px; border-radius:5px; margin:20px 0;">
                                                        <p style="font-size:14px; color:#666; margin:5px 0;"><strong>Deadline:</strong> ' . htmlspecialchars($deadlineDisplay, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '</p>
                                                        <p style="font-size:14px; color:#666; margin:5px 0;"><strong>Points:</strong> ' . htmlspecialchars($pointsDisplay, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '</p>
                                                    </div>
                                                    <p style="font-size:15px; color:#333;">
                                                        Please log in to your Webstar account to review the task details and submit on time.
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
    // --- Determine toast message based on mode ---
    if ($mode === 'new' || $mode === 'reuse') {
        $_SESSION['toast'] = [
            'type' => 'alert-success',
            'message' => 'Assignment assigned successfully!'
        ];
    } elseif ($mode === 'edit') {
        $_SESSION['toast'] = [
            'type' => 'alert-success',
            'message' => 'Assignment edited successfully!'
        ];
    }

    // --- Set active tab and redirect ---
    $_SESSION['activeTab'] = 'todo';
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

// Selecting Tasks made by the Instructor logged in
$taskQuery = "
    SELECT 
        a.assessmentTitle, 
        asg.assignmentDescription, 
        a.createdAt, 
        a.assessmentID, 
        c.courseCode
    FROM assessments a
    JOIN assignments asg ON a.assessmentID = asg.assessmentID
    JOIN courses c ON a.courseID = c.courseID
    JOIN users u ON c.userID = u.userID
    WHERE a.type = 'Task' AND u.userID = '$userID'
    GROUP BY a.assessmentTitle, asg.assignmentDescription
    ORDER BY a.createdAt DESC
";

$tasks = executeQuery($taskQuery);


// Storing reused data
$reusedData = null;

// Storing selected rubric
$activeRubric = null; // always exists

// If the instructor chose to reuse, it'll check the button if it was set
if (isset($_GET['reuse']) || isset($_GET['edit'])) {
    $reuseID = isset($_GET['reuse']) ? intval($_GET['reuse']) : intval($_GET['edit']);

    // Getting the data of the selected task to reuse or edit
    $reuseQuery = "
    SELECT 
        a.assessmentTitle, 
        asg.assignmentDescription, 
        a.deadline, 
        a.deadlineEnabled,
        asg.assignmentPoints,
        r.rubricID,
        r.rubricTitle,
        r.totalPoints,
        COUNT(c.criterionID) AS criteriaCount,
        f.fileID,
        f.fileAttachment,
        f.fileTitle,
        f.fileLink
    FROM assessments a
    JOIN assignments asg ON a.assessmentID = asg.assessmentID
    LEFT JOIN files f ON asg.assignmentID = f.assignmentID
    LEFT JOIN rubric r ON asg.rubricID = r.rubricID
    LEFT JOIN criteria c ON c.rubricID = r.rubricID
    JOIN courses crs ON a.courseID = crs.courseID
    JOIN users u ON crs.userID = u.userID
    WHERE a.assessmentID = '$reuseID'
      AND a.type = 'Task'
      AND u.userID = '$userID'
    GROUP BY r.rubricID, r.rubricTitle, r.totalPoints, f.fileID
    ";

    $reuseResult = executeQuery($reuseQuery);
    if ($reuseResult && $reuseResult->num_rows > 0) {
        $reusedData = [];
        while ($row = $reuseResult->fetch_assoc()) {
            $reusedData[] = $row; // store all file rows
        }
    } else {
        // Invalid or unauthorized reuse/edit attempt
        header("Location: assign-task.php");
        exit();
    }
}

if (!empty($reusedData)) {
    // If the instructor chose to reuse, it'll populate these fields with the selected task's info
    $mainData = [
        'assessmentTitle' => $reusedData[0]['assessmentTitle'],
        'assignmentDescription' => $reusedData[0]['assignmentDescription'],
        'deadline' => $reusedData[0]['deadline'],
        'assignmentPoints' => $reusedData[0]['assignmentPoints'],
        'deadlineEnabled' => $reusedData[0]['deadlineEnabled']
    ];

    // Display the attachments of the reused tasks 
    $files = [];
    foreach ($reusedData as $row) {
        if (!empty($row['fileID'])) {
            $files[] = [
                'fileID' => $row['fileID'],
                'fileTitle' => $row['fileTitle'],
                'fileAttachment' => $row['fileAttachment'],
                'fileLink' => $row['fileLink']
            ];
        }
    }

    // Fetch rubric for the reused assignment
    $reusedRubricInfo = null;

    if (!empty($reusedData)) {
        // Find the first row that has a rubricID
        foreach ($reusedData as $row) {
            if (isset($row['rubricID']) && $row['rubricID'] != null && $row['rubricID'] != 0) {
                $reusedRubricInfo = [
                    'rubricID' => $row['rubricID'],
                    'rubricTitle' => $row['rubricTitle'] ?? 'Untitled Rubric',
                    'criteriaCount' => $row['criteriaCount'] ?? 0,
                    'totalPoints' => $row['totalPoints'] ?? 0
                ];
                break; // stop at the first one
            }
        }
    }

    // Default to null (no rubric selected)
    $activeRubric = null;

    // If this task is reused, get the reused rubric info
    if (!empty($reusedRubricInfo)) {
        $activeRubric = $reusedRubricInfo;
    }

    // If user manually selects a rubric via GET/POST, override
    $selectedRubricID = isset($_GET['selectedRubricID']) ? intval($_GET['selectedRubricID']) : 0;
    if ($selectedRubricID > 0) {
        // Look it up from $rubricsList
        foreach ($rubricsList as $r) {
            if ($r['rubricID'] == $selectedRubricID) {
                $activeRubric = $r;
                break;
            }
        }
    }
}

// Fetch latest rubric for current user to display in the rubric card
$hasTotalPoints = false;
$colCheck = executeQuery("SHOW COLUMNS FROM rubric LIKE 'totalPoints'");
if ($colCheck && $colCheck->num_rows > 0) {
    $hasTotalPoints = true;
}

// Fetch all rubrics for modal list
$rubricsList = [];
$tpSelectList = $hasTotalPoints ? "IFNULL(r.totalPoints,0) AS totalPoints" : "0 AS totalPoints";
$tpGroupList = $hasTotalPoints ? ", r.totalPoints" : "";
$rubricsQuery = "SELECT r.rubricID, r.rubricTitle, r.rubricType, $tpSelectList, COUNT(c.criterionID) AS criteriaCount
                 FROM rubric r
                 LEFT JOIN criteria c ON c.rubricID = r.rubricID
                 WHERE (r.rubricType = 'Preset' OR (r.userID = '$userID' AND r.rubricType = 'Created'))
                 GROUP BY r.rubricID, r.rubricTitle, r.rubricType$tpGroupList
                 ORDER BY r.rubricType DESC, r.rubricID DESC";

$rubricsRes = executeQuery($rubricsQuery);
if ($rubricsRes && $rubricsRes->num_rows > 0) {
    while ($row = $rubricsRes->fetch_assoc()) {
        $rubricsList[] = $row;
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
        echo isset($_GET['reuse']) ? 'Reassign Task' : (isset($_GET['edit']) ? 'Edit Task' : 'Assign Task');
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
                                            echo 'Edit Task';
                                        } elseif (isset($_GET['reuse'])) {
                                            echo 'Reassign Task';
                                        } else {
                                            echo 'Assign Task';
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
                                                style="font-size:16px">add_task</span>
                                            <span>Assign an existing task</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Form starts -->
                                <form action="" id="assignTaskForm" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="selectedRubricID" id="selectedRubricID"
                                        value="<?= $activeRubric['rubricID'] ?? '' ?>">
                                    <input type="hidden" name="mode"
                                        value="<?= isset($_GET['edit']) ? 'edit' : (isset($_GET['reuse']) ? 'reuse' : 'new') ?>">
                                    <input type="hidden" name="taskID"
                                        value="<?= $_GET['edit'] ?? $_GET['reuse'] ?? '' ?>">
                                    <input type="hidden" name="activeTab" value="task">
                                    <div class="row">
                                        <div class="col-12 pt-3">
                                            <label for="taskInfo" class="form-label text-med text-16">Task
                                                Information</label>
                                            <input type="text"
                                                class="form-control textbox mb-2 px-3 py-2 text-reg text-16"
                                                id="taskInfo" name="assignmentTitle" placeholder="Task Title *"
                                                value="<?php echo isset($mainData) ? htmlspecialchars($mainData['assessmentTitle']) : ''; ?>"
                                                required>
                                        </div>
                                    </div>

                                    <!-- Rich Text Editor -->
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <div class="editor-wrapper">
                                                <div class="text-reg " id="editor"></div>
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
                                            <input type="hidden" name="assignmentContent" id="task">
                                        </div>
                                    </div>

                                    <div class="row g-3 m-0 p-0">
                                        <div class="row g-3 mt-4 m-0 p-0">
                                            <!-- Deadline -->
                                            <?php
                                            $currentDateTime = date('Y-m-d\TH:i'); // Now including time
                                            ?>

                                            <div class="col-md-6 m-0 p-0 mb-3 mb-md-0">
                                                <label class="form-label text-med text-16">
                                                    Deadline *
                                                </label>
                                                <div class="input-group">
                                                    <input type="datetime-local"
                                                        class="form-control textbox text-reg text-16 me-0 me-md-2"
                                                        name="deadline"
                                                        value="<?php echo isset($mainData['deadline']) ? date('Y-m-d\TH:i', strtotime($mainData['deadline'])) : ''; ?>"
                                                        min="<?php echo date('Y-m-d\T00:00', strtotime('+1 day')); ?>"
                                                        required>
                                                </div>
                                            </div>

                                            <!-- Points -->
                                            <div class="col-md-6 m-0 p-0">
                                                <label class="form-label text-med text-16">
                                                    Points
                                                </label>
                                                <input type="number" id="rubricPointsInput"
                                                    class="form-control textbox text-reg text-16" name="points"
                                                    placeholder="Enter points to be earned"
                                                    value="<?php echo isset($mainData['assignmentPoints']) ? htmlspecialchars($mainData['assignmentPoints']) : ''; ?>"
                                                    min="1" required />
                                            </div>
                                        </div>

                                        <div class="form-check mt-2 col d-flex align-items-center">
                                            <input class="form-check-input" type="checkbox" id="stopSubmissions"
                                                name="stopSubmissions" value="1" style="border: 1px solid var(--black);"
                                                <?php if (!empty($mainData['deadlineEnabled']) && $mainData['deadlineEnabled'] == 1)
                                                    echo 'checked'; ?> />
                                            <label class="form-check-label text-reg text-14 ms-2"
                                                style="margin-top: 4.5px;" for="stopSubmissions">
                                                Stop accepting submissions after the deadline.
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Learning Materials -->
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="learning-materials">
                                                <div class="d-flex align-items-center mt-5 mb-0 mb-md-2">
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

                                            <!-- Rubrics -->
                                            <div class="row mb-0">
                                                <div class="col">

                                                    <div class="d-flex align-items-center mt-4 mb-0 mb-md-2">
                                                        <label class="text-med text-16">Rubric</label>
                                                        <!-- For desktop -->
                                                        <span
                                                            class="material-symbols-outlined ms-3 me-1 d-none d-md-block"
                                                            style="font-size:16px">

                                                            info
                                                        </span>
                                                        <span class="text-reg text-14 d-none d-md-block">Any
                                                            points entered above will be replaced by the rubric’s total
                                                            points.</span>
                                                    </div>
                                                    <!-- For mobile -->
                                                    <div class="d-block d-md-none d-flex align-items-center mb-2">
                                                        <span class="material-symbols-outlined me-1"
                                                            style="font-size:16px">
                                                            info
                                                        </span>
                                                        <span class="text-reg text-14">Any
                                                            points entered above will be replaced by the rubric’s total
                                                            points.</span>
                                                    </div>


                                                    <div class="row mb-0 mt-3"
                                                        data-has-rubric="<?= !empty($activeRubric) ? '1' : '0' ?>"
                                                        <?= $activeRubric ? '' : 'style="display:none;"' ?>>
                                                        <div class="col-12">
                                                            <div
                                                                class="materials-card rubric-card d-flex align-items-stretch px-2 py-2 w-100 rounded-3">
                                                                <div
                                                                    class="d-flex w-100 align-items-center justify-content-between">
                                                                    <div class="d-flex align-items-center flex-grow-1">
                                                                        <div class="mx-3 d-flex align-items-center">
                                                                            <span
                                                                                class="material-symbols-rounded">rate_review</span>
                                                                        </div>
                                                                        <div>
                                                                            <div class="text-sbold text-16"
                                                                                style="line-height: 1.5;">
                                                                                <?= $activeRubric['rubricTitle'] ?? '' ?>
                                                                            </div>
                                                                            <div class="text-reg text-12 text-break"
                                                                                style="line-height: 1.5;">
                                                                                <?= ($activeRubric['totalPoints'] ?? 0) ?>
                                                                                Points ·
                                                                                <?= intval($activeRubric['criteriaCount'] ?? 0) ?>
                                                                                Criteria
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mx-3 d-flex align-items-center">
                                                                        <span class="material-symbols-outlined"
                                                                            style="cursor:pointer;">close</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <script>
                                                        (function () {
                                                            try {
                                                                var cardRow = document.querySelector('.row.mb-0.mt-3');
                                                                if (!cardRow) return;

                                                                // Only show the card if PHP has an active rubric
                                                                var hasActiveRubric = cardRow.dataset.hasRubric === '1';
                                                                if (hasActiveRubric) {
                                                                    cardRow.style.display = '';
                                                                } else {
                                                                    cardRow.style.display = 'none';
                                                                }
                                                            } catch (e) { }
                                                        })();
                                                    </script>


                                                    <?php if ($activeRubric) { ?>
                                                        <script>
                                                            (function () {
                                                                try {
                                                                    var ptsInput = document.querySelector('input[name="points"]');
                                                                    if (ptsInput) { ptsInput.value = '<?= (float) ($activeRubric['totalPoints'] ?? 0) ?>'; }
                                                                } catch (e) { }
                                                            })();
                                                        </script>
                                                    <?php } ?>

                                                    <!-- Buttons -->
                                                    <div class="mt-2 mb-5 text-start">
                                                        <!-- Select Rubric Button -->
                                                        <button type="button"
                                                            class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14 mt-2"
                                                            style="background-color: var(--primaryColor); border: 1px solid var(--black);"
                                                            data-bs-toggle="modal" data-bs-target="#selectRubricModal">
                                                            <div style="display: flex; align-items: center; gap: 5px;">
                                                                <span class="material-symbols-outlined"
                                                                    style="font-size:16px">add_circle</span>
                                                                <span>Rubric</span>
                                                            </div>
                                                        </button>
                                                    </div>

                                                    <!-- Course selection + Post button -->
                                                    <div class="row align-items-center justify-content-between text-center text-md-start"
                                                        style="margin-bottom: <?php echo ($courses && $courses->num_rows > 0) ? ($courses->num_rows * 50) : 0; ?>px;">
                                                        <!-- Dynamic Border Bottom based on the number of courses hehe -->
                                                        <!-- Add to Course -->

                                                        <div
                                                            class="col-12 col-md-auto mt-3 mt-md-0 d-flex align-items-center flex-nowrap justify-content-center justify-content-md-start <?php echo isset($_GET['edit']) ? 'invisible' : ''; ?>">
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
                                                                            $assignedQuery = "SELECT courseID FROM assessments WHERE assessmentID = '$editID'";
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
                                                                                    <input
                                                                                        class="form-check-input course-checkbox"
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


                                                        <!-- Assign Button -->
                                                        <div class="col-12 col-md-auto mt-3 mt-md-0 text-center">
                                                            <button type="submit" name="saveAssignment"
                                                                class="px-4 py-2 rounded-pill text-sbold text-md-14 mt-4 mt-md-0"
                                                                style="background-color: var(--primaryColor); border: 1px solid var(--black);">
                                                                <?php
                                                                if (isset($_GET['edit'])) {
                                                                    echo 'Save Changes';
                                                                } elseif (isset($_GET['reuse'])) {
                                                                    echo 'Reassign';
                                                                } else {
                                                                    echo 'Assign';
                                                                }
                                                                ?>
                                                            </button>
                                                        </div>

                                                    </div>

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

    <!-- Select Rubric Modal -->
    <div class="modal fade" id="selectRubricModal" tabindex="-1" aria-labelledby="selectRubricModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered py-4 " style="max-width: 700px;  height: 80vh">
            <div class="modal-content d-flex flex-column" style="height: 100%;">

                <!-- HEADER -->
                <div class="modal-header flex-shrink-0">
                    <div class="modal-title text-sbold text-20 ms-3" id="selectRubricModalLabel">Select Rubric</div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        style="transform: scale(0.8); filter: grayscale(100%);"></button>
                </div>

                <!-- DESC -->
                <div class="modal-body flex-grow-1 overflow-auto">
                    <p class="mb-3 text-med text-14 ms-3" style="color: var(--black);">
                        Choose from preset rubrics, create your own, or edit an existing one.
                    </p>
                    <div class="col p-0 m-3">
                        <div class="card rounded-3 mb-2 border-0">
                            <div class="card-body p-0">
                                <!-- OPTIONS -->
                                <?php if (!empty($rubricsList)) {
                                    foreach ($rubricsList as $rub) {
                                        $isSelected = ($activeRubric && $activeRubric['rubricID'] == $rub['rubricID']);
                                        ?>
                                        <div class="rubric-option rounded-3 d-flex align-items-center justify-content-between mb-2"
                                            style="cursor: pointer; background-color: <?= $isSelected ? 'var(--primaryColor)' : 'var(--pureWhite)' ?>; border: 1px solid var(--black);"
                                            data-rubric-id="<?= $rub['rubricID'] ?>"
                                            data-rubric-title="<?= htmlspecialchars($rub['rubricTitle']) ?>"
                                            data-rubric-points="<?= $rub['totalPoints'] ?>"
                                            data-rubric-criteria="<?= (int) $rub['criteriaCount'] ?>">
                                            <div style="line-height: 1.5; padding:10px 15px;">
                                                <div class="text-sbold text-14"><?= $rub['rubricTitle'] ?></div>
                                                <div class="text-med text-12"><?= $rub['totalPoints'] ?> Points ·
                                                    <?= (int) $rub['criteriaCount'] ?> Criteria
                                                </div>
                                            </div>
                                            <?php if (($rub['rubricType'] ?? '') === 'Created') { ?>
                                                <a href="edit-rubric.php?rubricID=<?= $rub['rubricID'] ?>"
                                                    class="text-decoration-none" onclick="event.stopPropagation();">
                                                    <span class="material-symbols-rounded"
                                                        style="font-variation-settings: 'FILL' 1; padding-right:15px; color: var(--black);">
                                                        edit
                                                    </span>
                                                </a>
                                            <?php } ?>
                                        </div>
                                    <?php }
                                } else { ?>
                                    <div class="text-muted text-reg text-14 py-2">No rubrics found. Create one.</div>
                                <?php } ?>

                                <!-- Select Rubric Button -->
                                <a href="create-rubric.php"
                                    class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14"
                                    style="background-color: var(--primaryColor); border: 1px solid var(--black);">
                                    <div style="display: flex; align-items: center; gap: 5px;">
                                        <span class="material-symbols-outlined" style="font-size:16px">add_circle</span>
                                        <span>Create</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer border-top flex-shrink-0">

                    <!-- BUTTON -->
                    <button type="button" id="confirmRubricBtn" class="btn rounded-5 px-4 text-sbold text-14 me-3"
                        style="background-color: var(--primaryColor); border: 1px solid var(--black);">
                        Select
                    </button>

                </div>
            </div>


        </div>
    </div>
    </div>

    <script>
        document.getElementById('confirmRubricBtn').addEventListener('click', function () {
            const hiddenInput = document.getElementById('selectedRubricID');
            const cardRow = document.querySelector('.row.mb-0.mt-3'); // your rubric card row
            const titleDiv = cardRow.querySelector('.text-sbold.text-16');
            const infoDiv = cardRow.querySelector('.text-reg.text-12');

            const selectedOption = document.querySelector(`.rubric-option[data-rubric-id="${selectedRubric.id}"]`);
            if (selectedOption) {
                cardRow.style.display = '';
                titleDiv.textContent = selectedOption.dataset.rubricTitle;
                infoDiv.textContent = `${selectedOption.dataset.rubricPoints} Points · ${selectedOption.dataset.rubricCriteria} Criteria`;
                hiddenInput.value = selectedRubric.id;
            }

            const modalEl = document.getElementById('selectRubricModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        });
    </script>

    <!-- Reuse Task Modal -->
    <div class="modal fade" id="reuseTaskModal" tabindex="-1" aria-labelledby="reuseTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered py-4 " style="max-width: 700px;  height: 80vh">
            <div class="modal-content d-flex flex-column" style="height: 100%;">

                <!-- HEADER -->
                <div class="modal-header flex-shrink-0">
                    <div class="modal-title text-sbold text-20 ms-3" id="selectRubricModalLabel">Assign an existing task
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        style="transform: scale(0.8); filter: grayscale(100%);"></button>
                </div>

                <!-- DESC -->
                <div class="modal-body flex-grow-1 overflow-auto">
                    <p class="mb-3 text-med text-14 mx-3" style="color: var(--black);">
                        Select a task you’ve previously created to reuse its details. You can review and edit the title,
                        instructions, or settings before assigning or saving the task.
                    </p>
                    <div class="col p-0 m-3">
                        <div class="card rounded-3 mb-2 border-0">
                            <div class="card-body p-0">
                                <!-- OPTIONS -->
                                <?php if ($tasks && $tasks->num_rows > 0) {
                                    while ($task = $tasks->fetch_assoc()) {
                                        ?>
                                        <div class="rubric-option rounded-3 d-flex align-items-center justify-content-between mb-2 w-100"
                                            style="cursor: pointer; background-color: var(--pureWhite); border: 1px solid var(--black);"
                                            onclick='window.location.href="assign-task.php?reuse=<?php echo $task["assessmentID"]; ?>"'>
                                            <div style="line-height: 1.5; padding:10px 15px;">
                                                <div class="text-sbold text-14">
                                                    <?php echo htmlspecialchars($task['assessmentTitle']); ?>
                                                </div>
                                                <div class="text-med text-12"
                                                    style="display:-webkit-box; -webkit-line-clamp:1; -webkit-box-orient:vertical; overflow:hidden;">
                                                    <?php echo strip_tags($task['assignmentDescription']); ?>
                                                </div>
                                                <div class="text-med text-muted text-12"
                                                    style="display:-webkit-box; -webkit-line-clamp:1; -webkit-box-orient:vertical; overflow:hidden;">
                                                    <?php
                                                    echo date('F j, Y g:i A', strtotime($task['createdAt']))
                                                        . ' · ' . $task['courseCode'];
                                                    ?>

                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <div class="text-muted text-reg text-14 py-2">No existing assignments found.</div>
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

        var quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Task Instructions *',
            modules: {
                toolbar: '#toolbar'
            }
        });

        <?php if (isset($reusedData)) { ?>
            quill.clipboard.dangerouslyPasteHTML(
                <?= json_encode($mainData['assignmentDescription']); ?>
            );
        <?php } ?>

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
        const form = document.querySelector('#assignTaskForm'); // use your form ID
        form.addEventListener('submit', function (e) {
            const taskInput = document.querySelector('#task');
            let text = quill.getText().trim();

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

            // Convert Quill HTML to plain text with bullets/line breaks
            let html = quill.root.innerHTML;
            html = html.replace(/<p>/g, '').replace(/<\/p>/g, '<br>');
            html = html.replace(/<li>/g, '• ').replace(/<\/li>/g, '<br>');
            html = html.replace(/<\/?(ul|ol)>/g, '');
            html = html.replace(/(<br>)+$/g, '');
            taskInput.value = html.trim();

            // Validation
            if (text.length === 0) {
                e.preventDefault();
                quill.root.focus();
                taskInput.setCustomValidity('Please fill out this field.');
                taskInput.reportValidity();
                return false;
            } else {
                taskInput.setCustomValidity('');
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

        // Checkbox function for Stop Submissions
        const stopCheckbox = document.getElementById('stopSubmissions');
        stopCheckbox.addEventListener('change', function () {
            if (this.checked) {
                console.log('Stop submissions enabled');
            } else {
                console.log('Stop submissions disabled');
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

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
                    $jsFiles[] = [
                        'title' => $file['fileTitle'] ?: basename($file['fileAttachment']),
                        'attachment' => $file['fileAttachment'],
                        'link' => $file['fileLink'],
                        'ext' => strtoupper(pathinfo($file['fileAttachment'], PATHINFO_EXTENSION)),
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

    <!-- Rubric JS -->
    <script>
        // Auto-open rubric modal when returning from create-rubric
        (function () {
            function openRubricModal() {
                var modalEl = document.getElementById('selectRubricModal');
                if (!modalEl) return;
                var modal = new bootstrap.Modal(modalEl);
                // slight delay to ensure layout ready
                setTimeout(function () { modal.show(); }, 50);
            }
            function needsOpen() {
                try {
                    var params = new URLSearchParams(window.location.search);
                    if (params.get('openRubric') === '1') return true;
                } catch (e) { }
                // also support hash trigger
                if (window.location.hash === '#openRubric') return true;
                return false;
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function () { if (needsOpen()) openRubricModal(); });
            } else {
                if (needsOpen()) openRubricModal();
            }
        })();

        var selectedRubric = null;
        (function () {
            // Only run if the card exists
            var cardRow = document.querySelector('.row.mb-0.mt-3');
            if (!cardRow) return;

            var hiddenInput = document.getElementById('selectedRubricID');
            var titleDiv = cardRow.querySelector('.text-sbold.text-16');
            var infoDiv = cardRow.querySelector('.text-reg.text-12');

            // Check if PHP has an active rubric
            var hasActiveRubric = cardRow.dataset.hasRubric === '1';
            if (hasActiveRubric && hiddenInput) {
                // Populate hidden input
                hiddenInput.value = "<?= $activeRubric['rubricID'] ?? '' ?>";

                // Populate card content
                titleDiv.textContent = "<?= addslashes($activeRubric['rubricTitle'] ?? '') ?>";
                infoDiv.textContent = "<?= ($activeRubric['totalPoints'] ?? 0) ?> Points · <?= intval($activeRubric['criteriaCount'] ?? 0) ?> Criteria";

                // Also set selectedRubric JS variable
                window.selectedRubric = {
                    id: "<?= $activeRubric['rubricID'] ?? '' ?>",
                    title: "<?= addslashes($activeRubric['rubricTitle'] ?? '') ?>",
                    points: "<?= ($activeRubric['totalPoints'] ?? 0) ?>",
                    criteria: "<?= intval($activeRubric['criteriaCount'] ?? 0) ?>"
                };

                // Ensure card is visible
                cardRow.style.display = '';
            }
        })();


        // Click on a rubric option: highlight and store selection
        document.addEventListener('click', function (e) {
            var opt = e.target.closest && e.target.closest('.rubric-option');
            if (!opt) return;

            // Clear previous highlight
            document.querySelectorAll('.rubric-option').forEach(function (el) {
                el.style.backgroundColor = 'var(--pureWhite)';
            });
            // Highlight selected (yellow)
            opt.style.backgroundColor = 'var(--primaryColor)';

            selectedRubric = {
                id: opt.getAttribute('data-rubric-id'),
                title: opt.getAttribute('data-rubric-title'),
                points: opt.getAttribute('data-rubric-points'),
                criteria: opt.getAttribute('data-rubric-criteria')
            };
        });

        // Confirm selection: apply to card and close modal
        document.addEventListener('click', function (e) {
            var btn = e.target.closest && e.target.closest('#confirmRubricBtn');
            if (!btn) return;
            if (!selectedRubric) return;

            var cardRow = document.querySelector('.row.mb-0.mt-3');
            if (cardRow) cardRow.style.display = '';
            var titleEl = document.querySelector('.rubric-card .text-sbold.text-16');
            var metaEl = document.querySelector('.rubric-card .text-reg.text-12');
            if (titleEl) titleEl.textContent = selectedRubric.title;
            if (metaEl) metaEl.textContent = selectedRubric.points + ' Points · ' + selectedRubric.criteria + ' Criteria';

            // Also reflect total points into the Points input field
            try {
                var ptsInput = document.querySelector('#rubricPointsInput'); // add an ID to the points input
                if (ptsInput) ptsInput.value = selectedRubric.points;

            } catch (e) { }

            var modalEl = document.getElementById('selectRubricModal');
            if (modalEl) {
                var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.hide();
            }
        });

        // Close rubric card
        document.addEventListener('click', function (e) {
            const closeIcon = e.target.closest('.rubric-card .material-symbols-outlined');
            if (!closeIcon) return;

            const cardRow = closeIcon.closest('.row.mb-0.mt-3');
            if (cardRow) cardRow.style.display = 'none';

            // Clear selection
            selectedRubric = null;
            const hiddenInput = document.getElementById('selectedRubricID');
            if (hiddenInput) hiddenInput.value = '';

            // Clear card text
            const titleDiv = cardRow.querySelector('.text-sbold.text-16');
            const infoDiv = cardRow.querySelector('.text-reg.text-12');
            if (titleDiv) titleDiv.textContent = '';
            if (infoDiv) infoDiv.textContent = '';

            // Reset modal option highlights
            document.querySelectorAll('.rubric-option').forEach(el => {
                el.style.backgroundColor = 'var(--pureWhite)';
            });
        });

    </script>

    <!-- Assign Task JS -->
    <script>
        // Persist Assign Task form fields using localStorage so input survives navigating away and back
        (function () {
            var STORAGE_KEY = 'assignTaskDraft_v1';
            var NAVIGATION_FLAG = 'assignTaskNavigatedAway_v1';

            function getElementById(id) { return document.getElementById(id); }

            function getDeadlineInput() { return document.querySelector('input[name="deadline"]'); }
            function getPointsInput() { return document.querySelector('input[name="points"]'); }

            function getSelectedCourseIds() {
                var selectedIds = [];
                var courseCheckboxes = document.querySelectorAll('.course-checkbox');
                for (var i = 0; i < courseCheckboxes.length; i++) {
                    if (courseCheckboxes[i].checked) selectedIds.push(courseCheckboxes[i].value);
                }
                return selectedIds;
            }

            function getCurrentLinks() {
                var links = [];
                var hiddenLinkInputs = document.querySelectorAll('#filePreviewContainer input.link-hidden');
                for (var i = 0; i < hiddenLinkInputs.length; i++) {
                    var value = hiddenLinkInputs[i].value;
                    if (value && value.trim() !== '') links.push(value.trim());
                }
                return links;
            }

            function saveDraftToStorage() {
                try {
                    var titleInput = getElementById('taskInfo');
                    var deadlineInput = getDeadlineInput();
                    var pointsInput = getPointsInput();
                    var stopCheckbox = getElementById('stopSubmissions');
                    var draft = {
                        assignmentTitle: titleInput ? titleInput.value : '',
                        assignmentContentHtml: (typeof quill !== 'undefined' ? quill.root.innerHTML : ''),
                        deadline: deadlineInput ? deadlineInput.value : '',
                        points: pointsInput ? pointsInput.value : '',
                        stopSubmissions: stopCheckbox ? !!stopCheckbox.checked : false,
                        courses: getSelectedCourseIds(),
                        links: getCurrentLinks()
                    };
                    localStorage.setItem(STORAGE_KEY, JSON.stringify(draft));
                } catch (e) { }
            }

            function clearDraftInStorage() {
                try { localStorage.removeItem(STORAGE_KEY); } catch (e) { }
            }

            function appendLinkPreview(linkValue) {
                var container = document.getElementById('filePreviewContainer');
                if (!container || !linkValue) return;
                var uniqueId = Date.now() + Math.floor(Math.random() * 1000);
                var domain = '';
                try { var urlObj = new URL(linkValue); domain = urlObj.hostname; } catch (e) { domain = linkValue; }
                var faviconUrl = 'https://www.google.com/s2/favicons?sz=64&domain=' + domain;
                var html = '' +
                    '<div class="col-12 my-1" data-id="' + uniqueId + '">' +
                    '  <div class="materials-card d-flex align-items-stretch p-2 w-100 rounded-3">' +
                    '    <div class="d-flex w-100 align-items-center justify-content-between">' +
                    '      <div class="d-flex align-items-center flex-grow-1">' +
                    '        <div class="mx-3 d-flex align-items-center">' +
                    '          <img src="' + faviconUrl + '" alt="' + domain + ' Icon" onerror="this.onerror=null;this.src=\'../shared/assets/img/web.png\';" style="width: 30px; height: 30px;">' +
                    '        </div>' +
                    '        <div>' +
                    '          <div id="title-' + uniqueId + '" class="text-sbold text-16" style="line-height: 1.5;">' + linkValue + '</div>' +
                    '          <div class="text-reg text-12 text-break" style="line-height: 1.5;"><a href="' + linkValue + '" target="_blank">' + linkValue + '</a></div>' +
                    '        </div>' +
                    '      </div>' +
                    '      <div class="mx-3 d-flex align-items-center delete-file" style="cursor:pointer;">' +
                    '        <span class="material-symbols-outlined">close</span>' +
                    '      </div>' +
                    '    </div>' +
                    '  </div>' +
                    '  <input type="hidden" name="links[]" value="' + linkValue + '" class="link-hidden">' +
                    '</div>';
                container.insertAdjacentHTML('beforeend', html);
            }

            function hasUserEnteredData(draft) {
                if (!draft) return false;
                var title = draft.assignmentTitle ? draft.assignmentTitle.trim() : '';
                if (title !== '') return true;
                var content = draft.assignmentContentHtml ? draft.assignmentContentHtml.trim() : '';
                if (content !== '' && content !== '<p><br></p>' && content !== '<p></p>') return true;
                var deadline = draft.deadline ? draft.deadline.trim() : '';
                if (deadline !== '') return true;
                var points = draft.points ? draft.points.trim() : '';
                if (points !== '') return true;
                if (draft.stopSubmissions === true) return true;
                if (draft.courses && Array.isArray(draft.courses) && draft.courses.length > 0) return true;
                if (draft.links && Array.isArray(draft.links) && draft.links.length > 0) return true;
                return false;
            }

            function clearAllFormFields() {
                try {
                    var titleInput = getElementById('taskInfo');
                    if (titleInput) titleInput.value = '';
                    var deadlineInput = getDeadlineInput();
                    if (deadlineInput) deadlineInput.value = '';
                    var pointsInput = getPointsInput();
                    if (pointsInput) pointsInput.value = '';
                    var stopCheckbox = getElementById('stopSubmissions');
                    if (stopCheckbox) stopCheckbox.checked = false;
                    var courseCheckboxes = document.querySelectorAll('.course-checkbox');
                    for (var i = 0; i < courseCheckboxes.length; i++) courseCheckboxes[i].checked = false;
                    var container = document.getElementById('filePreviewContainer');
                    if (container) container.innerHTML = '';
                    if (typeof quill !== 'undefined' && quill.root) {
                        quill.root.innerHTML = '';
                    }
                } catch (e) { }
            }

            function restoreDraftFromStorage() {
                var raw = null;
                try { raw = localStorage.getItem(STORAGE_KEY); } catch (e) { }
                if (!raw) {
                    clearAllFormFields();
                    return;
                }
                var draft = null;
                try { draft = JSON.parse(raw); } catch (e) {
                    draft = null;
                    clearDraftInStorage();
                    clearAllFormFields();
                    return;
                }
                if (!draft) {
                    clearAllFormFields();
                    return;
                }

                // Only restore if there's actual user-entered data (not just empty defaults)
                if (!hasUserEnteredData(draft)) {
                    clearDraftInStorage();
                    clearAllFormFields();
                    return;
                }

                try {
                    var titleInput = getElementById('taskInfo');
                    if (titleInput) titleInput.value = draft.assignmentTitle || '';

                    if (typeof quill !== 'undefined' && draft.assignmentContentHtml) {
                        var contentHtml = draft.assignmentContentHtml;
                        if (contentHtml && contentHtml.trim() !== '' && contentHtml !== '<p><br></p>') {
                            quill.root.innerHTML = contentHtml;
                        }
                    }

                    var deadlineInput = getDeadlineInput();
                    if (deadlineInput) deadlineInput.value = draft.deadline || '';

                    var pointsInput = getPointsInput();
                    if (pointsInput) pointsInput.value = draft.points || '';

                    var stopCheckbox = getElementById('stopSubmissions');
                    if (stopCheckbox) stopCheckbox.checked = !!draft.stopSubmissions;

                    var courseCheckboxes = document.querySelectorAll('.course-checkbox');
                    var selectedMap = {};
                    var i;
                    if (draft.courses && draft.courses.length) {
                        for (i = 0; i < draft.courses.length; i++) selectedMap[String(draft.courses[i])] = true;
                    }
                    for (i = 0; i < courseCheckboxes.length; i++) {
                        var value = String(courseCheckboxes[i].value);
                        courseCheckboxes[i].checked = !!selectedMap[value];
                    }

                    if (draft.links && draft.links.length) {
                        for (i = 0; i < draft.links.length; i++) appendLinkPreview(draft.links[i]);
                    }
                } catch (e) { }
            }

            function bindDraftPersistenceListeners() {
                var form = document.querySelector('form');
                if (form) {
                    form.addEventListener('input', function () { saveDraftToStorage(); });
                    form.addEventListener('change', function () { saveDraftToStorage(); });
                    form.addEventListener('submit', function () {
                        clearDraftInStorage();
                        clearNavigationFlag();
                    });
                }
                try {
                    if (typeof quill !== 'undefined') {
                        quill.on('text-change', function () { saveDraftToStorage(); });
                    }
                } catch (e) { }
                document.addEventListener('click', function (event) {
                    var isAddLink = event.target && event.target.id === 'addLinkBtn';
                    var isDelete = event.target && (event.target.closest ? event.target.closest('.delete-file') : null);
                    if (isAddLink || isDelete) { setTimeout(function () { saveDraftToStorage(); }, 0); }
                });
            }

            function setNavigationFlag() {
                try { localStorage.setItem(NAVIGATION_FLAG, '1'); } catch (e) { }
            }

            function clearNavigationFlag() {
                try { localStorage.removeItem(NAVIGATION_FLAG); } catch (e) { }
            }

            function hasNavigatedAway() {
                try {
                    var flag = localStorage.getItem(NAVIGATION_FLAG);
                    return flag === '1';
                } catch (e) { return false; }
            }

            function checkAndClearOrRestore() {
                try {
                    var params = new URLSearchParams(window.location.search);
                    var hasReuse = params.get('reuse');
                    var hasEdit = params.get('edit');
                    if (hasReuse || hasEdit) {
                        clearDraftInStorage();
                        clearNavigationFlag();
                        bindDraftPersistenceListeners();
                        return;
                    }


                    var hasSelectedRubric = params.get('selectedRubricID');
                    var shouldRestore = hasNavigatedAway() || hasSelectedRubric;

                    if (!shouldRestore) {
                        clearNavigationFlag();
                        clearAllFormFields();
                        bindDraftPersistenceListeners();
                        return;
                    }

                    clearNavigationFlag();

                    var raw = localStorage.getItem(STORAGE_KEY);
                    if (!raw) {
                        bindDraftPersistenceListeners();
                        return;
                    }

                    var draft = null;
                    try { draft = JSON.parse(raw); } catch (e) {
                        clearDraftInStorage();
                        bindDraftPersistenceListeners();
                        return;
                    }

                    if (!draft) {
                        bindDraftPersistenceListeners();
                        return;
                    }

                    if (!hasUserEnteredData(draft)) {
                        clearDraftInStorage();
                        bindDraftPersistenceListeners();
                        return;
                    }

                    restoreDraftFromStorage();
                    bindDraftPersistenceListeners();
                } catch (e) {
                    bindDraftPersistenceListeners();
                }
            }

            function bindNavigationAwayListeners() {
                document.addEventListener('click', function (event) {
                    var link = event.target.closest ? event.target.closest('a') : null;
                    if (!link || !link.href) return;
                    var href = link.href;
                    if (href.indexOf('create-rubric.php') !== -1 || href.indexOf('edit-rubric.php') !== -1) {
                        setNavigationFlag();
                    }
                }, true);

                function bindEditLinks() {
                    var editLinks = document.querySelectorAll('a[href*="edit-rubric.php"]');
                    for (var i = 0; i < editLinks.length; i++) {
                        var link = editLinks[i];
                        if (link.dataset.navFlagSet) continue;
                        link.dataset.navFlagSet = '1';
                        link.addEventListener('click', function () {
                            setNavigationFlag();
                        });
                    }
                }

                bindEditLinks();

                var modalEl = document.getElementById('selectRubricModal');
                if (modalEl) {
                    modalEl.addEventListener('shown.bs.modal', function () {
                        setTimeout(bindEditLinks, 100);
                    });
                }
            }

            function initializeDraftPersistenceWithCleanStart() {
                bindNavigationAwayListeners();

                function attemptCheck() {
                    if (typeof quill !== 'undefined' && quill.root) {
                        checkAndClearOrRestore();
                    } else {
                        setTimeout(attemptCheck, 100);
                    }
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', function () { attemptCheck(); });
                } else {
                    attemptCheck();
                }
            }

            initializeDraftPersistenceWithCleanStart();
        })();
    </script>

    <!-- Change the selected rubric  -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const rubricOptions = document.querySelectorAll('.rubric-option');
            const hiddenInput = document.getElementById('selectedRubricID');
            const cardRow = document.querySelector('.rubric-card-row');
            const titleDiv = cardRow.querySelector('.rubric-title');
            const infoDiv = cardRow.querySelector('.rubric-info');

            // Track currently selected option
            let selectedRubricId = hiddenInput.value;

            rubricOptions.forEach(option => {
                option.addEventListener('click', function () {
                    // Highlight selected
                    rubricOptions.forEach(o => o.style.backgroundColor = 'var(--pureWhite)');
                    this.style.backgroundColor = 'var(--primaryColor)';

                    // Update hidden input
                    selectedRubricId = this.dataset.rubricId;
                    hiddenInput.value = selectedRubricId;
                });
            });

            document.getElementById('confirmRubricBtn').addEventListener('click', function () {
                // Find selected option
                const selectedOption = document.querySelector(`.rubric-option[data-rubric-id="${selectedRubricId}"]`);
                if (selectedOption) {
                    // Show card if hidden
                    cardRow.style.display = '';

                    // Update card content
                    titleDiv.textContent = selectedOption.dataset.rubricTitle;
                    infoDiv.textContent = `${selectedOption.dataset.rubricPoints} Points · ${selectedOption.dataset.rubricCriteria} Criteria`;
                }

                // Close modal
                const modalEl = document.getElementById('selectRubricModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();

            });
        });


    </script>
</body>

</html>