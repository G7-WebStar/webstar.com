<?php
$activePage = 'grading-sheet-pdf-with-image';
date_default_timezone_set('Asia/Manila');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include("../shared/assets/database/connect.php");
include("../shared/assets/processes/prof-session-process.php");

$errorMessages = [
    "emailNoCredential" => "No email credentials found in the database!"
];

if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
    require '../shared/assets/phpmailer/src/Exception.php';
    require '../shared/assets/phpmailer/src/PHPMailer.php';
    require '../shared/assets/phpmailer/src/SMTP.php';
}

// ✅ Get submissionID
$submissionID = isset($_GET['submissionID']) ? intval($_GET['submissionID']) : 0;

// Automatically fetch student userID from the submissions table
$getInfo = $conn->prepare("
    SELECT s.userID
    FROM submissions s
    WHERE s.submissionID = ? AND s.isSubmitted = 1
    LIMIT 1
");
$getInfo->bind_param("i", $submissionID);
$getInfo->execute();
$result = $getInfo->get_result();

if ($row = $result->fetch_assoc()) {
    $studentUserID = intval($row['userID']);
} else {
    header("Location: 404.html");
    exit();

}

// Fetch student, program, course, and assessment details
$detailsQuery = $conn->prepare("
    SELECT 
        s.userID,
        a.assessmentID,
        CONCAT(u.firstName, ' ', u.middleName, ' ', u.lastName) AS studentName,
        p.programInitial,
        u.yearLevel,
        u.yearSection,
        c.courseID,
        c.courseCode,
        c.courseTitle,
        a.assessmentTitle,
        s.submittedAt
    FROM submissions s
    INNER JOIN assessments a ON s.assessmentID = a.assessmentID
    INNER JOIN courses c ON a.courseID = c.courseID
    INNER JOIN userinfo u ON s.userID = u.userID
    INNER JOIN program p ON u.programID = p.programID
    WHERE s.submissionID = ?
    LIMIT 1
");
$detailsQuery->bind_param("i", $submissionID);
$detailsQuery->execute();
$detailsResult = $detailsQuery->get_result();

if ($detailsResult && $detailsResult->num_rows > 0) {
    $details = $detailsResult->fetch_assoc(); // Only fetch once
    // Create formatted student info
    $studentDisplay = $details['studentName'] . ' · ' . $details['programInitial'] . ' ' . $details['yearLevel'] . '-' . $details['yearSection'];
    if (empty($studentUserID) && isset($details['userID'])) {
        $studentUserID = intval($details['userID']);
    }
    // --- Check if logged-in user owns this assessment/course ---
    $ownerCheck = $conn->prepare("
    SELECT c.userID
    FROM courses c
    INNER JOIN assessments a ON c.courseID = a.courseID
    WHERE a.assessmentID = ?
    LIMIT 1
");
    $ownerCheck->bind_param("i", $details['assessmentID']);
    $ownerCheck->execute();
    $ownerResult = $ownerCheck->get_result();

    if ($ownerResult && $ownerResult->num_rows > 0) {
        $ownerRow = $ownerResult->fetch_assoc();
        $courseOwnerID = intval($ownerRow['userID']);
        if ($courseOwnerID !== intval($_SESSION['userID'])) {
            // ❌ Not the owner → redirect to 404
            header("Location: 404.php");
            exit();
        }
    } else {
        // ❌ Assessment/course not found → redirect to 404
        header("Location: 404.php");
        exit();
    }
    $ownerCheck->close();

} else {
    $details = [
        'userID' => 0,
        'assessmentID' => 0,
        'studentName' => 'Unknown Student',
        'programInitial' => 'N/A',
        'yearLevel' => 'N/A',
        'yearSection' => 'N/A',
        'courseID' => 0,
        'courseCode' => 'N/A',
        'courseTitle' => 'N/A',
        'assessmentTitle' => 'N/A',
        'submittedAt' => 'N/A'
    ];
    $studentDisplay = $details['studentName'] . ' · ' . $details['programInitial'] . ' ' . $details['yearLevel'] . '-' . $details['yearSection'];
    $studentUserID = 0;
}

// Get assignment points for this assessment
$assignmentPoints = 100; // default fallback
$assignmentQuery = $conn->prepare("
    SELECT assignmentPoints 
    FROM assignments 
    WHERE assessmentID = ? 
    LIMIT 1
");
$assignmentQuery->bind_param("i", $details['assessmentID']);
$assignmentQuery->execute();
$assignmentResult = $assignmentQuery->get_result();
if ($row = $assignmentResult->fetch_assoc()) {
    $assignmentPoints = intval($row['assignmentPoints']);
}
$assignmentQuery->close();


// Fetch all files under this submissionID
$fileQuery = $conn->prepare("SELECT fileAttachment, fileLink, fileTitle FROM files WHERE submissionID = ?");
$fileQuery->bind_param("i", $submissionID);
$fileQuery->execute();
$fileResult = $fileQuery->get_result();

$fileLinks = [];
if ($fileResult && $fileResult->num_rows > 0) {
    while ($row = $fileResult->fetch_assoc()) {
        $fileName = basename($row['fileAttachment']);
        $fileLinks[] = [
            'name' => $fileName,
            'path' => "../shared/assets/files/" . $fileName,
            'link' => $row['fileLink'],
            'title' => $row['fileTitle']
        ];
    }
}

// Helper functions
function is_image_ext($ext)
{
    $ext = strtolower($ext);
    return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg']);
}
function is_pdf_ext($ext)
{
    return strtolower($ext) === 'pdf';
}

// Determine view type
$viewType = 'none';
if (!empty($fileLinks)) {
    $firstExt = pathinfo($fileLinks[0]['name'], PATHINFO_EXTENSION);
    if (is_image_ext($firstExt))
        $viewType = 'image';
    elseif (is_pdf_ext($firstExt))
        $viewType = 'pdf';
    else
        $viewType = 'other';
}

// Fetch badges (11–21)
$badges = [];
$badgeQuery = $conn->query("SELECT badgeID, badgeName, badgeDescription, badgeIcon FROM badges WHERE badgeID BETWEEN 11 AND 21");
if ($badgeQuery && $badgeQuery->num_rows > 0) {
    while ($b = $badgeQuery->fetch_assoc()) {
        $badges[] = $b;
    }
}


// Handle grade + feedback submission together
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitGrade'])) {
    $score = isset($_POST['score']) ? floatval($_POST['score']) : 0;
    $feedback = trim($_POST['feedback'] ?? '');

    if ($studentUserID > 0 && $submissionID > 0) {
        // Check if a record already exists for this user and submission
        $check = $conn->prepare("SELECT scoreID FROM scores WHERE userID = ? AND submissionID = ?");
        $check->bind_param("ii", $studentUserID, $submissionID);
        $check->execute();
        $checkResult = $check->get_result();

        if ($checkResult && $checkResult->num_rows > 0) {
            // ✅ Update existing score and feedback
            $row = $checkResult->fetch_assoc();
            $scoreID = intval($row['scoreID']);

            $update = $conn->prepare("UPDATE scores 
                SET score = ?, feedback = ?, gradedAt = NOW() 
                WHERE scoreID = ?");
            $update->bind_param("dsi", $score, $feedback, $scoreID);
            $update->execute();
        } else {
            // ✅ Insert new record if none exists
            $insert = $conn->prepare("INSERT INTO scores (userID, submissionID, score, feedback, gradedAt) 
                VALUES (?, ?, ?, ?, NOW())");
            $insert->bind_param("iids", $studentUserID, $submissionID, $score, $feedback);
            $insert->execute();

            // Get the newly inserted scoreID
            $scoreID = $insert->insert_id;
        }

        // ✅ Update the submissions table to reflect the scoreID
        $updateSubmission = $conn->prepare("UPDATE submissions SET scoreID = ? WHERE submissionID = ?");
        $updateSubmission->bind_param("ii", $scoreID, $submissionID);
        $updateSubmission->execute();
        $updateSubmission->close();

        // --- Notifications & Email ---
        // Get enrollmentID for the student
        $enrollmentQuery = $conn->prepare("
            SELECT e.enrollmentID 
            FROM enrollments e
            WHERE e.userID = ? AND e.courseID = ?
            LIMIT 1
        ");
        $enrollmentQuery->bind_param("ii", $studentUserID, $details['courseID']);
        $enrollmentQuery->execute();
        $enrollmentResult = $enrollmentQuery->get_result();
        
        if ($enrollmentResult && $enrollmentResult->num_rows > 0) {
            $enrollmentData = $enrollmentResult->fetch_assoc();
            $enrollmentID = intval($enrollmentData['enrollmentID']);
            
            // Prepare notification message
            $assessmentTitleEscaped = mysqli_real_escape_string($conn, $details['assessmentTitle']);
            $notificationMessage = "Your submission for \"" . $assessmentTitleEscaped . "\" has been graded.";
            $notifType = 'Submissions Update';
            
            $escapedNotificationMessage = mysqli_real_escape_string($conn, $notificationMessage);
            $escapedNotifType = mysqli_real_escape_string($conn, $notifType);
            
            // Insert notification into inbox
            $insertNotificationQuery = "
                INSERT INTO inbox (enrollmentID, messageText, notifType, createdAt)
                VALUES ('$enrollmentID', '$escapedNotificationMessage', '$escapedNotifType', NOW())
            ";
            executeQuery($insertNotificationQuery);
            
            // Get student email and check if they have questDeadlineEnabled
            $selectEmailQuery = "
                SELECT u.email, COALESCE(s.questDeadlineEnabled, 0) as questDeadlineEnabled
                FROM users u
                LEFT JOIN settings s ON u.userID = s.userID
                WHERE u.userID = ?
            ";
            $emailStmt = $conn->prepare($selectEmailQuery);
            $emailStmt->bind_param("i", $studentUserID);
            $emailStmt->execute();
            $emailResult = $emailStmt->get_result();
            
            if ($emailResult && $emailResult->num_rows > 0) {
                $studentData = $emailResult->fetch_assoc();
                
                if ($studentData['questDeadlineEnabled'] == 1 && !empty($studentData['email'])) {
                    $credentialQuery = "SELECT email, password FROM emailcredentials WHERE credentialID = 1";
                    $credentialResult = executeQuery($credentialQuery);
                    $credentialRow = $credentialResult ? mysqli_fetch_assoc($credentialResult) : null;

                    if ($credentialRow) {
                        $smtpEmail = $credentialRow['email'];
                        $smtpPassword = $credentialRow['password'];

                        try {
                            $mail = new PHPMailer(true);
                            $mail->isSMTP();
                            $mail->Host       = 'smtp.gmail.com';
                            $mail->SMTPAuth   = true;
                            $mail->Username   = $smtpEmail;
                            $mail->Password   = $smtpPassword;
                            $mail->SMTPSecure = 'tls';
                            $mail->Port       = 587;
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
                            $mail->Subject = "[GRADED] " . $details['assessmentTitle'] . " - " . $details['courseCode'];
                            $mail->addAddress($studentData['email']);
                            
                            $assessmentTitleEsc = htmlspecialchars($details['assessmentTitle'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                            $courseCodeEsc = htmlspecialchars($details['courseCode'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                            $courseTitleEsc = htmlspecialchars($details['courseTitle'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                            $scoreDisplay = number_format($score, 0);
                            $maxScoreDisplay = number_format((float) $assignmentPoints, 0);
                            $feedbackHtml = !empty($feedback) ? nl2br(htmlspecialchars($feedback, ENT_QUOTES | ENT_HTML5, 'UTF-8')) : '<em>No feedback provided.</em>';
                            
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
                                                        Your submission for <strong>' . $assessmentTitleEsc . '</strong> in <strong>' . $courseCodeEsc . '</strong> has been graded.
                                                    </p>
                                                    <div style="background-color:#f9f9f9; padding:15px; border-radius:5px; margin:20px 0;">
                                                        <p style="font-size:14px; color:#666; margin:5px 0;"><strong>Course:</strong> ' . $courseCodeEsc . ' - ' . $courseTitleEsc . '</p>
                                                        <p style="font-size:14px; color:#666; margin:5px 0;"><strong>Assessment:</strong> ' . $assessmentTitleEsc . '</p>
                                                        <p style="font-size:18px; color:#2C2C2C; margin:10px 0;"><strong>Score: ' . $scoreDisplay . '/' . $maxScoreDisplay . '</strong></p>
                                                    </div>
                                                    <p style="font-size:15px; color:#333; margin-top: 25px;">
                                                        <strong>Feedback:</strong>
                                                    </p>
                                                    <div style="font-size:15px; color:#333; margin-bottom: 20px; line-height: 22px; background-color:#f9f9f9; padding:15px; border-radius:5px;">
                                                        ' . $feedbackHtml . '
                                                    </div>
                                                    <p style="font-size:15px; color:#333;">
                                                        Please log in to your Webstar account to view your detailed results.
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
                        } catch (Exception $e) {
                            $errorMsg = isset($mail) && is_object($mail) ? $mail->ErrorInfo : $e->getMessage();
                            error_log("PHPMailer failed for Submission ID $submissionID: " . $errorMsg);
                        }
                    }
                }
            }
            $emailStmt->close();
        }
        $enrollmentQuery->close();
    }
}


// Filter only files that have a non-empty link
$linkFiles = array_filter($fileLinks, function ($f) {
    return !empty($f['link']);
});

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitGrade'])) {
    // --- Existing grade & feedback code here ---

    if (!empty($_POST['selectedBadgeIDs'])) {
        $badgeIDs = explode(',', $_POST['selectedBadgeIDs']); // Split multiple IDs
        $studentUserID = intval($details['userID']);
        $courseID = intval($details['courseID']);
        $assignmentID = intval($details['assessmentID']);
        $earnedAt = date('Y-m-d H:i:s');

        foreach ($badgeIDs as $badgeID) {
            $badgeID = intval(trim($badgeID));
            if ($badgeID <= 0)
                continue;

            // Prevent duplicate entries
            $check = $conn->prepare("
            SELECT userID 
            FROM studentbadges 
            WHERE userID = ? AND badgeID = ? AND assignmentID = ?
        ");
            $check->bind_param("iii", $studentUserID, $badgeID, $assignmentID);
            $check->execute();
            $checkResult = $check->get_result();

            if ($checkResult && $checkResult->num_rows == 0) {
                $insert = $conn->prepare("
                INSERT INTO studentbadges (userID, badgeID, courseID, assignmentID, earnedAt) 
                VALUES (?, ?, ?, ?, ?)
            ");
                $insert->bind_param("iiiis", $studentUserID, $badgeID, $courseID, $assignmentID, $earnedAt);
                $insert->execute();
                $insert->close();
            }

            $check->close();
        }
    }
    // ✅ Set session for toast notification
    $_SESSION['success'] = "Grade submitted successfully!";

    // Fetch all ungraded submissions for this assessment
    $subQuery = $conn->prepare("
    SELECT s.submissionID
    FROM submissions s
    LEFT JOIN scores sc ON s.submissionID = sc.submissionID
    WHERE s.assessmentID = ? AND sc.scoreID IS NULL AND s.isSubmitted = 1
    ORDER BY s.submittedAt ASC
");
    $subQuery->bind_param("i", $details['assessmentID']);
    $subQuery->execute();
    $subResult = $subQuery->get_result();

    $ungradedIDs = [];
    while ($row = $subResult->fetch_assoc()) {
        $ungradedIDs[] = intval($row['submissionID']);
    }
    $subQuery->close();

    // If no submissionID given or invalid, redirect to first ungraded submission
    if ($submissionID === 0 || !in_array($submissionID, $ungradedIDs)) {
        if (!empty($ungradedIDs)) {
            header("Location: ?submissionID={$ungradedIDs[0]}&assessmentID={$details['assessmentID']}");
            exit();
        } else {
            header("Location: assess.php");
            exit();
        }
    }

}

// Fetch all ungraded submissions for this assessment
$submissionIDs = [];
$subQuery = $conn->prepare("
    SELECT s.submissionID
    FROM submissions s
    LEFT JOIN scores sc ON s.submissionID = sc.submissionID
    WHERE s.assessmentID = ? AND sc.scoreID IS NULL AND s.isSubmitted = 1
    ORDER BY s.submittedAt ASC
");
$subQuery->bind_param("i", $details['assessmentID']);
$subQuery->execute();
$subResult = $subQuery->get_result();

while ($row = $subResult->fetch_assoc()) {
    $submissionIDs[] = intval($row['submissionID']);
}
$subQuery->close();

// If no ungraded submissions, show a message
if (empty($submissionIDs)) {
    header("Location: assess.php"); // redirect to assess.php
    exit(); // stop further execution
}


// Find current submission index in ungraded list
$currentIndex = array_search($submissionID, $submissionIDs);

// Total ungraded submissions
$totalUngraded = count($submissionIDs);

// Left to review = total ungraded minus 1 (exclude current student)
$leftToReview = $totalUngraded > 0 ? $totalUngraded - 1 : 0;

// Determine next and previous ungraded submissions (looping)
$nextIndex = ($currentIndex + 1) % $totalUngraded;
$prevIndex = ($currentIndex - 1 + $totalUngraded) % $totalUngraded;

$nextSubmissionID = $submissionIDs[$nextIndex];
$prevSubmissionID = $submissionIDs[$prevIndex];





// Get total ungraded submissions for this assessment/course
$ungradedQuery = $conn->prepare("
    SELECT COUNT(*) AS totalUngraded
    FROM submissions s
    LEFT JOIN scores sc ON s.submissionID = sc.submissionID
    WHERE s.assessmentID = ? AND sc.scoreID IS NULL
");
$ungradedQuery->bind_param("i", $details['assessmentID']);
$ungradedQuery->execute();
$ungradedResult = $ungradedQuery->get_result();
$totalUngraded = 0;
if ($row = $ungradedResult->fetch_assoc()) {
    $totalUngraded = intval($row['totalUngraded']);
}



// count of student
// Fetch all submissions for this assessment (graded + ungraded)
$submissionIDs = [];
$subQuery = $conn->prepare("
    SELECT s.submissionID
    FROM submissions s
    WHERE s.assessmentID = ? AND s.isSubmitted = 1
    ORDER BY s.submittedAt ASC
");
$subQuery->bind_param("i", $details['assessmentID']);
$subQuery->execute();
$subResult = $subQuery->get_result();

while ($row = $subResult->fetch_assoc()) {
    $submissionIDs[] = intval($row['submissionID']);
}
$subQuery->close();

// Fetch all ungraded submissions for this assessment
$submissionIDs = [];
$subQuery = $conn->prepare("
    SELECT s.submissionID
    FROM submissions s
    LEFT JOIN scores sc ON s.submissionID = sc.submissionID
    WHERE s.assessmentID = ? AND sc.scoreID IS NULL
    ORDER BY s.submittedAt ASC
");
$subQuery->bind_param("i", $details['assessmentID']);
$subQuery->execute();
$subResult = $subQuery->get_result();

while ($row = $subResult->fetch_assoc()) {
    $submissionIDs[] = intval($row['submissionID']);
}
$subQuery->close();

// If no ungraded submissions left
if (empty($submissionIDs)) {
    die("<p class='text-danger'>All submissions have been graded.</p>");
}

// Find current submission index in ungraded list
$currentIndex = array_search($submissionID, $submissionIDs);

// Total ungraded submissions
$totalUngraded = count($submissionIDs);

// Left to review = total ungraded minus 1 (exclude current student)
$leftToReview = $totalUngraded > 0 ? $totalUngraded - 1 : 0;

// Determine next and previous ungraded submissions (looping)
$nextIndex = ($currentIndex + 1) % $totalUngraded;
$prevIndex = ($currentIndex - 1 + $totalUngraded) % $totalUngraded;

$nextSubmissionID = $submissionIDs[$nextIndex];
$prevSubmissionID = $submissionIDs[$prevIndex];

// getting of student profile
$profilePicturePath = "../shared/assets/pfp-uploads/defaultProfile.png"; // default fallback

if ($studentUserID > 0) {
    $stmt = $conn->prepare("SELECT profilePicture FROM userinfo WHERE userID = ? LIMIT 1");
    $stmt->bind_param("i", $studentUserID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (!empty($row['profilePicture'])) {
            $profilePicturePath = "../shared/assets/pfp-uploads/" . $row['profilePicture'];
        }
    }

    $stmt->close();
}
?>



<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Assignment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/grading-sheet-pdf-with-image.css">
    <link rel="stylesheet" href="../shared/assets/css/sidebar-and-container-styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../shared/assets/img/webstar-icon.png">
    <!-- Quill CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:FILL@1" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />

</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center align-items-md-start p-0 p-md-3"
        style="background-color: var(--black); overflow-y:auto;">
        <!-- Toast Container -->
        <div id="toastContainer"
             class="position-absolute top-0 start-50 translate-middle-x pt-5 pt-md-1 d-flex flex-column align-items-center"
            style="z-index:1100; pointer-events:none;">
           <?php if (isset($_SESSION['success'])): ?>
               <div class="alert alert-success mb-2 shadow-lg text-med text-12
                           d-flex align-items-center justify-content-center gap-2 px-3 py-2" role="alert"
                    style="border-radius:8px; display:flex; align-items:center; gap:8px; padding:0.5rem 0.75rem; text-align:center; background-color:#d1e7dd; color:#0f5132;">
                   <i class="bi bi-check-circle-fill fs-6" style="color: var(--black);"></i>
                   <span style="color: var(--black);"><?= $_SESSION['success']; ?></span>
               </div>
               <?php unset($_SESSION['success']); ?>
           <?php endif; ?>
        </div>


        <div class="row w-100">
            <!-- Sidebar -->
            <?php include '../shared/components/prof-sidebar-for-mobile.php'; ?>
            <?php include '../shared/components/prof-sidebar-for-desktop.php'; ?>

            <!-- Main Container -->
            <div class="col main-container m-0 p-0 mx-0 mx-md-2 p-0 p-md-4 overflow-y-auto">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">
                    <?php include '../shared/components/prof-navbar-for-mobile.php'; ?>

                    <div class="container-fluid py-3 overflow-y-auto row-padding-top">
                        <div class="row mb-3">
                            <div class="col-12 cardHeader p-3 mb-4">
                                <div
                                    class="row desktop-header d-none d-sm-flex align-items-center justify-content-between">
                                    <div class="col-auto d-flex align-items-center gap-3">
                                        <button onclick="history.back()" class="p-0" style="background:none; border:none;">
                                            <span class="material-symbols-outlined"
                                                  style="color: var(--black); font-size: 22px;">
                                                arrow_back
                                            </span>
                                        </button>

                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle overflow-hidden"
                                                 style="width: 40px; height: 40px; background-color: var(--highlight75);">
                                                <img src="<?php echo htmlspecialchars($profilePicturePath); ?>" 
                                                     alt="Profile Picture" style="width: 100%; height: 100%; object-fit: cover;">
                                            </div>

                                            <div>
                                                <div class="text-sbold text-18" style="color: var(--black);">
                                                    <?php echo htmlspecialchars($studentDisplay); ?>
                                                </div>
                                                <div class="text-reg text-14 text-muted">
                                                    Submitted
                                                    <?php echo date("F j, Y g:i A", strtotime($details['submittedAt'])); ?>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-auto text-end d-none d-lg-block" style="line-height: 1.3;">
                                        <div class="text-sbold text-16" style="color: var(--black);">
                                            <?php echo htmlspecialchars($details['assessmentTitle']); ?>
                                        </div>
                                        <div class="text-reg text-muted text-14">
                                            <?php echo htmlspecialchars($details['courseCode']); ?><br>
                                            <?php echo htmlspecialchars($details['courseTitle']); ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- MOBILE VIEW HEADER -->
                                <div class="d-block d-sm-none mobile-assignment mt-3">
                                    <div class="mobile-top d-flex align-items-center gap-3">
                                        <div class="arrow">
                                            <a href="javascript:history.back()" class="text-decoration-none">
                                                <i class="fa-solid fa-arrow-left text-reg text-16" style="color: var(--black);"></i>
                                            </a>
                                        </div>
                                         <div class="title text-sbold text-18"
                                            style="display: block; width: 80%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            <?php echo htmlspecialchars($studentDisplay); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <!-- Left Content -->
                            <div class="col-12 col-lg-8 mb-5">
                                <div class="p-0 px-lg-5">
                                    <?php
                                    $images = [];
                                    $pdfs = [];
                                    $others = [];

                                    foreach ($fileLinks as $f) {
                                        $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
                                        if (is_image_ext($ext)) {
                                            $images[] = $f;
                                        } elseif (is_pdf_ext($ext)) {
                                            $pdfs[] = $f;
                                        } else {
                                            $others[] = $f;
                                        }
                                    }
                                    ?>

                                    <div class="text-sbold text-14 mt-4 mb-3">Attachments</div>

                                    <?php if (!empty($images) || !empty($pdfs) || !empty($others)): ?>

                                        <!-- PDF SECTION -->
                                        <?php if (!empty($pdfs)): ?>
                                            <?php foreach ($pdfs as $f): ?>
                                                <div class="mt-4 mb-4">
                                                    <iframe src="<?php echo htmlspecialchars($f['path']); ?>" width="100%"
                                                        height="600px" style="border:none; border-radius:10px;"></iframe>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>

                                        <!-- IMAGE SECTION -->
                                        <?php if (!empty($images)): ?>
                                            <div class="container mt-3 w-100 m-0">
                                                <div class="row g-4">
                                                    <?php foreach ($images as $index => $f):
                                                        $link = htmlspecialchars($f['path']);
                                                        $name = htmlspecialchars($f['name']);
                                                        ?>
                                                        <div class="col-12 col-sm-6 col-md-4 p-0 m-0 my-3">
                                                            <div class="pdf-preview-box text-center" data-bs-toggle="modal"
                                                                data-bs-target="#imageModal<?php echo $index; ?>"
                                                                style="cursor: zoom-in; overflow: hidden; border-radius: 10px; height: 180px; position: relative; border: 1px solid var(--black);">
                                                                <img src="<?php echo $link; ?>" alt="Preview - <?php echo $name; ?>"
                                                                    style="width: 100%; height: 100%; object-fit: cover; object-position: center; transition: transform 0.3s ease;">
                                                            </div>
                                                        </div>


                                                        <!-- Modal for each image -->
                                                        <div class="modal fade" id="imageModal<?php echo $index; ?>" tabindex="-1"
                                                            aria-hidden="true">
                                                            <div class="modal-dialog modal-fullscreen">
                                                                <div class="modal-content bg-black border-0 d-flex justify-content-center align-items-center position-relative"
                                                                    style="width: 99vw; height: 95vh; border-radius: 0;">

                                                                    <button type="button"
                                                                        class="btn-close btn-close-white position-absolute top-0 end-0 m-4"
                                                                        data-bs-dismiss="modal" aria-label="Close"></button>

                                                                    <div
                                                                        class="modal-img-wrapper p-0 m-0 w-100 h-100 d-flex justify-content-center align-items-center">
                                                                        <img class="modal-zoomable" src="<?php echo $link; ?>"
                                                                            alt="Full - <?php echo $name; ?>"
                                                                            style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <!-- OTHER FILES & LINKS SECTION -->
                                        <?php if (!empty($linkFiles)): ?>

                                            <?php foreach ($linkFiles as $f): ?>
                                                <div class="mt-4b">
                                                    <iframe src="<?php echo htmlspecialchars($f['link']); ?>" width="100%"
                                                        height="600" frameborder="0" allowfullscreen
                                                        allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                                                        loading="lazy" style="border-radius: 10px; border: 1px solid var(--black);">
                                                    </iframe>

                                                    <div class="text-start mt-3 text-reg">
                                                        <a href="<?php echo htmlspecialchars($f['link']); ?>" target="_blank"
                                                            rel="noopener noreferrer" class="btn custom-btn px-4 py-2">
                                                            <i class="fa-solid fa-up-right-from-square me-2"></i> Open link in new
                                                            tab
                                                        </a>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <p class="text-danger mt-3">No attachments found
                                            <?php echo $submissionID; ?>.
                                        </p>
                                    <?php endif; ?>

                                </div>
                            </div>

                            <!-- Right Content -->
                            <div class="col-12 col-lg-4">
                                <!-- Sticky Parent Container -->
                                <div class="cardSticky position-sticky"
                                    style="top:20px; max-height:60vh; overflow-y:auto; ">
                                    <!-- Grade Submission Form -->
                                    <form method="POST" action="">
                                        <!-- Dynamically include the submissionID -->
                                        <input type="hidden" name="submissionID"
                                            value="<?php echo htmlspecialchars($submissionID); ?>">
                                        <input type="hidden" name="selectedBadgeIDs" id="selectedBadgeIDs">

                                        <!-- Card Section -->
                                        <div class="cardSticky border-0 p-0">
                                            <div class="ms-2 me-2">
                                                <div class="d-flex align-items-center justify-content-center mb-5 mt-5">
                                                    <input type="number" name="score"
                                                        class="form-control me-2 ms-1 text-16 text-reg" placeholder="Grade"
                                                        style="width: 130px; border-radius: 10px; border: 1px solid var(--black);"
                                                        min="0" max="<?php echo htmlspecialchars($assignmentPoints); ?>" required>
                                                    <span class="text-sbold">/<?php echo htmlspecialchars($assignmentPoints); ?></span>
                                                </div>

                                                <div class="text-center mt-5">
                                                    <div class="text-sbold text-15 mb-3" style="color: var(--black);">
                                                        Optional Actions</div>
                                                    <div class="d-flex flex-column align-items-center gap-2 mb-5">
                                                        <!-- ADD AWARD BADGE BUTTON -->
                                                        <button type="button"
                                                            class="btn custom-btn d-flex align-items-center justify-content-center text-reg"
                                                            data-bs-toggle="modal" data-bs-target="#awardBadgeModal">
                                                            <span
                                                                class="material-symbols-rounded me-2">emoji_events</span>
                                                            Award badge
                                                        </button>
                                                        <!-- FEEDBACK INPUT (replacing modal button) -->
                                                        <textarea name="feedback" id="feedbackInput" rows="3"
                                                            class="form-control text-reg text-14 rounded-3 p-3 mt-3"
                                                            style="resize: none; background-color: var(--pureWhite); border: 1px solid var(--black) !important;"
                                                            placeholder="Write feedback that helps your student level up their learning journey!"></textarea>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Buttons always below the card -->
                                        <div class="text-center mt-4  text-reg px-2">
                                            <div class="d-flex justify-content-center align-items-center gap-3 mb-2 stack-below-large">
                                                <button type="button" id="prevBtn"
                                                    class="btn px-4 py-2 rounded-pill text-15 fw-semibold"
                                                    data-current-index="<?php echo $currentIndex; ?>"
                                                    style="background-color: var(--pureWhite); border: 1px solid var(--black); color: var(--black);">
                                                    Previous
                                                </button>

                                                <button type="submit" name="submitGrade"
                                                    class="btn px-4 py-2 rounded-pill text-15 fw-semibold"
                                                    style="background-color: var(--primaryColor); border: 1px solid var(--black); color: var(--black);">
                                                    Submit
                                                </button>

                                                <button type="button" id="nextBtn"
                                                    class="btn px-4 py-2 rounded-pill text-15 fw-semibold"
                                                    data-current-index="<?php echo $currentIndex; ?>"
                                                    style="background-color: var(--pureWhite); border: 1px solid var(--black); color: var(--black);">
                                                    Next
                                                </button>
                                            </div>



                                            <p class="text-14 fw-medium mt-3" style="color: var(--black);">
                                                <?php echo $leftToReview; ?> submissions left to review
                                            </p>

                                            <p class="text-14 fw-medium mt-3"
                                                        style="color: var(--black); font-style: italic;">
                                                        Note: Grades cannot be edited after submission.
                                                    </p>

                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div> <!-- end row -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- AWARD BADGE MODAL -->
    <div class="modal fade" id="awardBadgeModal" tabindex="-1" aria-labelledby="awardBadgeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:700px;">
            <div class="modal-content" style="max-height:80vh; overflow:hidden;">

                <!-- HEADER -->
                <div class="modal-header">
                    <div class="modal-title text-sbold text-20 ms-3" id="awardBadgeModalLabel">Award badge</div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        style="transform: scale(0.8); filter: grayscale(100%);"></button>
                </div>

                <div class="modal-body" style="overflow-y:auto; scrollbar-width:thin;">
                    <p class="mb-3 text-med text-14 ms-3" style="color: var(--black);">
                        Choose a badge to reward and recognize your student’s hard work.
                    </p>


                    <div class="col p-0 m-3">
                        <?php if (!empty($badges)): ?>
                            <?php foreach ($badges as $badge): ?>
                                <div class="card rounded-3 mb-2"
                                    style="background-color: var(--pureWhite); border: 1px solid var(--black);">
                                    <div class="card-body p-0">
                                        <div class="badge-option rounded-4 d-flex align-items-center" style="cursor: pointer;">
                                            <img src="../shared/assets/img/badge/<?php echo htmlspecialchars($badge['badgeIcon']); ?>"
                                                alt="<?php echo htmlspecialchars($badge['badgeName']); ?> Icon"
                                                style="width: 33px; height: 38px;" class="mx-1 ms-2">
                                            <div>
                                                <div style="line-height: 1.1;">
                                                    <div class="text-bold text-14 ms-1">
                                                        <?php echo htmlspecialchars($badge['badgeName']); ?>
                                                    </div>
                                                    <div class="text-med text-12 text-mutedms-1">
                                                        <?php echo htmlspecialchars($badge['badgeDescription']); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer border-top">
                </div>
            </div>
        </div>
    </div>

    <!-- bootstrap bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Quill JS -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        const quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Feedback',
            modules: {
                toolbar: '#toolbar'
            }
        });

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

        document.getElementById("feedbackForm").addEventListener("submit", function () {
            document.getElementById("feedbackContent").value = quill.root.innerHTML;
        });
    </script>

    <script>
        // THUMBNAIL hover zoom
        document.querySelectorAll('.pdf-preview-box img').forEach(img => {
            img.addEventListener('mouseenter', () => img.style.transform = 'scale(1.05)');
            img.addEventListener('mouseleave', () => img.style.transform = 'scale(1)');
        });

        // Zoom & scroll zoom for modal images
        document.querySelectorAll('.modal img').forEach(img => {
            let zoomLevel = 1;
            let isDragging = false;
            let startX, startY, scrollLeft, scrollTop;

            // Toggle zoom on click
            img.addEventListener('click', () => {
                if (zoomLevel === 1) {
                    zoomLevel = 2;
                    img.style.cursor = 'zoom-out';
                } else {
                    zoomLevel = 1;
                    img.style.cursor = 'zoom-in';
                }
                img.style.transform = `scale(${zoomLevel})`;
            });

            // Scroll to zoom in/out
            img.addEventListener('wheel', (e) => {
                e.preventDefault();
                zoomLevel += e.deltaY * -0.001; // scroll up = zoom in
                zoomLevel = Math.min(Math.max(1, zoomLevel), 5); // limit zoom (1x–5x)
                img.style.transform = `scale(${zoomLevel})`;
            });

            // Enable dragging when zoomed in
            img.addEventListener('mousedown', (e) => {
                if (zoomLevel > 1) {
                    isDragging = true;
                    startX = e.pageX - img.offsetLeft;
                    startY = e.pageY - img.offsetTop;
                    img.style.cursor = 'grabbing';
                    e.preventDefault();
                }
            });

            img.addEventListener('mouseup', () => {
                isDragging = false;
                img.style.cursor = zoomLevel > 1 ? 'grab' : 'zoom-in';
            });

            img.addEventListener('mouseleave', () => {
                isDragging = false;
            });

            img.addEventListener('mousemove', (e) => {
                if (!isDragging || zoomLevel === 1) return;
                e.preventDefault();
                const x = e.pageX - startX;
                const y = e.pageY - startY;
                img.style.transformOrigin = `${(e.offsetX / img.width) * 100}% ${(e.offsetY / img.height) * 100}%`;
                img.style.transform = `scale(${zoomLevel}) translate(${x / 100}px, ${y / 100}px)`;
            });
        });

        // Modal image zoom/scroll/drag logic
        (function () {
            // initialize for each modal image present
            const modalImages = document.querySelectorAll('.modal-zoomable');

            modalImages.forEach(img => {
                let zoom = 1;
                let isDragging = false;
                let lastClientX = 0;
                let lastClientY = 0;
                let translateX = 0;
                let translateY = 0;
                let originX = 50;
                let originY = 50;

                // helper: apply transform
                function applyTransform() {
                    img.style.transformOrigin = `${originX}% ${originY}%`;
                    img.style.transform = `translate(${translateX}px, ${translateY}px) scale(${zoom})`;
                }

                // reset function used when modal closes
                function reset() {
                    zoom = 1;
                    isDragging = false;
                    translateX = 0;
                    translateY = 0;
                    originX = 50;
                    originY = 50;
                    img.style.cursor = 'zoom-in';
                    img.style.transition = 'transform 0.12s ease';
                    applyTransform();
                }

                // click toggles between 1x and 2x
                img.addEventListener('click', (e) => {
                    // don't toggle if user is dragging
                    if (isDragging) return;
                    if (zoom === 1) {
                        // set origin based on click
                        const rect = img.getBoundingClientRect();
                        const offsetX = e.clientX - rect.left;
                        const offsetY = e.clientY - rect.top;
                        originX = (offsetX / rect.width) * 100;
                        originY = (offsetY / rect.height) * 100;
                        zoom = 2;
                        img.style.cursor = 'zoom-out';
                    } else {
                        zoom = 1;
                        translateX = 0;
                        translateY = 0;
                        img.style.cursor = 'zoom-in';
                    }
                    applyTransform();
                });

                // wheel for zoom in/out
                img.addEventListener('wheel', (e) => {
                    e.preventDefault();
                    // compute pointer position to keep focal point
                    const rect = img.getBoundingClientRect();
                    const pointerX = e.clientX - rect.left;
                    const pointerY = e.clientY - rect.top;
                    const prevZoom = zoom;
                    zoom += -e.deltaY * 0.0015; // adjust sensitivity
                    zoom = Math.min(Math.max(1, zoom), 5);

                    // recompute translate to keep pointer stable
                    if (zoom !== prevZoom) {
                        const relX = (pointerX / rect.width) * 2 - 1; // relative [-1,1]
                        const relY = (pointerY / rect.height) * 2 - 1;
                        // adjust translate proportionally
                        translateX += -relX * (zoom - prevZoom) * 50;
                        translateY += -relY * (zoom - prevZoom) * 50;
                        img.style.cursor = zoom > 1 ? 'grab' : 'zoom-in';
                        applyTransform();
                    }
                }, {
                    passive: false
                });

                // dragging
                img.addEventListener('mousedown', (e) => {
                    if (zoom <= 1) return;
                    isDragging = true;
                    lastClientX = e.clientX;
                    lastClientY = e.clientY;
                    img.style.cursor = 'grabbing';
                    img.style.transition = 'none';
                    e.preventDefault();
                });
                window.addEventListener('mousemove', (e) => {
                    if (!isDragging) return;
                    const dx = e.clientX - lastClientX;
                    const dy = e.clientY - lastClientY;
                    translateX += dx;
                    translateY += dy;
                    lastClientX = e.clientX;
                    lastClientY = e.clientY;
                    applyTransform();
                });
                window.addEventListener('mouseup', () => {
                    if (!isDragging) return;
                    isDragging = false;
                    img.style.cursor = zoom > 1 ? 'grab' : 'zoom-in';
                    img.style.transition = 'transform 0.12s ease';
                });

                // Reset zoom/position when modal closes
                // find the closest modal ancestor and hook into bootstrap's hidden event
                let modalEl = img.closest('.modal');
                if (modalEl) {
                    modalEl.addEventListener('hidden.bs.modal', () => {
                        reset();
                    });
                    modalEl.addEventListener('shown.bs.modal', () => {
                        // ensure starting values
                        reset();
                    });
                }
            });
        })();

        document.addEventListener("DOMContentLoaded", () => {
            const badgeOptions = document.querySelectorAll(".badge-option");

            badgeOptions.forEach(option => {
                option.addEventListener("click", () => {
                    // Reset all cards
                    badgeOptions.forEach(o => {
                        o.parentElement.parentElement.style.backgroundColor = "var(--pureWhite)";
                    });

                    // Highlight selected card
                    option.parentElement.parentElement.style.backgroundColor = "var(--primaryColor)";
                });
            });
        });

        // Remove modal backdrop for image viewer
        document.querySelectorAll('[id^="imageModal"]').forEach(modal => {
            modal.addEventListener('show.bs.modal', () => {
                // Remove any existing backdrop
                document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());

                // Disable new backdrop from being added
                setTimeout(() => {
                    document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
                    document.body.classList.remove('modal-open'); // avoid scroll lock
                }, 100);
            });

            modal.addEventListener('shown.bs.modal', () => {
                document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
                document.body.classList.remove('modal-open');
            });

            modal.addEventListener('hidden.bs.modal', () => {
                // Restore scroll
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
            });
        });

        // award badge
        document.addEventListener("DOMContentLoaded", function () {
            const badgeOptions = document.querySelectorAll(".badge-option");
            const selectedInput = document.getElementById("selectedBadgeIDs");

            // Store multiple selected badge IDs
            let selectedBadges = [];

            // Add data-badge-id dynamically (from PHP)
            const badgeIDs = <?php echo json_encode(array_column($badges, 'badgeID')); ?>;

            badgeOptions.forEach((badge, index) => {
                const badgeId = String(badgeIDs[index]);
                badge.dataset.badgeId = badgeId;

                // Toggle selection on click
                badge.addEventListener("click", function () {
                    const id = this.dataset.badgeId;
                    const index = selectedBadges.indexOf(id);

                    if (index === -1) {
                        // Select this badge
                        selectedBadges.push(id);
                        highlightBadge(this);
                    } else {
                        // Unselect this badge
                        selectedBadges.splice(index, 1);
                        resetBadge(this);
                    }

                    // Update hidden input
                    selectedInput.value = selectedBadges.join(",");
                });
            });

            // Highlight selected badge
            function highlightBadge(badge) {
                badge.style.backgroundColor = "var(--primaryColor)";
                badge.classList.add("selected-badge");
            }

            // Reset badge
            function resetBadge(badge) {
                badge.style.backgroundColor = "var(--pureWhite)";
                badge.classList.remove("selected-badge");
            }

            // On form submit: only submit selected badges, then deselect all visually
            const form = document.querySelector("form");
            if (form) {
                form.addEventListener("submit", () => {
                    // Set hidden input value for only selected badges
                    selectedInput.value = selectedBadges.join(",");

                    // Immediately reset all badges visually
                    badgeOptions.forEach(resetBadge);

                    // Clear the selection array
                    selectedBadges = [];
                });
            }
        });

        // next and previous button
        document.addEventListener("DOMContentLoaded", () => {
            const submissionIDs = <?php echo json_encode($submissionIDs); ?>;
            const currentSubmissionID = <?php echo $submissionID; ?>; // current submission from PHP

            const prevBtn = document.getElementById("prevBtn");
            const nextBtn = document.getElementById("nextBtn");

            function goToSubmission(index) {
                if (submissionIDs.length === 0) return;
                // Wrap around
                if (index < 0) index = submissionIDs.length - 1;
                if (index >= submissionIDs.length) index = 0;

                const submissionID = submissionIDs[index];
                window.location.href = `?submissionID=${submissionID}`;
            }

            prevBtn.addEventListener("click", () => {
                let currentIndex = submissionIDs.indexOf(currentSubmissionID);
                goToSubmission(currentIndex - 1);
            });

            nextBtn.addEventListener("click", () => {
                let currentIndex = submissionIDs.indexOf(currentSubmissionID);
                goToSubmission(currentIndex + 1);
            });
        });

    </script>

    <script>
     document.addEventListener('DOMContentLoaded', () => {
         const alertEl = document.querySelector('.alert.alert-success');
         if (alertEl) {
             setTimeout(() => {
                 alertEl.style.transition = "opacity 0.5s ease-out";
                 alertEl.style.opacity = 0;
                 setTimeout(() => alertEl.remove(), 500);
             }, 3000); // auto hide after 3s
         }
     });
    </script>





</body>

</html>