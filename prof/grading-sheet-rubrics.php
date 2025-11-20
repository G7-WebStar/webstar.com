<?php $activePage = 'sheet-rubrics'; ?>
<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include("../shared/assets/database/connect.php");
date_default_timezone_set('Asia/Manila');
include("../shared/assets/processes/prof-session-process.php");

if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
    require '../shared/assets/phpmailer/src/Exception.php';
    require '../shared/assets/phpmailer/src/PHPMailer.php';
    require '../shared/assets/phpmailer/src/SMTP.php';
}

// ✅ Get submissionID
$submissionID = isset($_GET['submissionID']) ? intval($_GET['submissionID']) : 0;

// Automatically fetch userID from the submissions table
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
    $userID = intval($row['userID']);
} else {
    header("Location: 404.html");
    exit();

}

// ✅ Fetch student, program, course, and assessment details
$detailsQuery = $conn->prepare("
    SELECT 
        s.userID,
        a.assessmentID,
        CONCAT(u.firstName, ' ', u.middleName, '. ', u.lastName) AS studentName,
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
}

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


// Filter only files that have a non-empty link
$linkFiles = array_filter($fileLinks, function ($f) {
    return !empty($f['link']);
});

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
    WHERE s.assessmentID = ?
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


// Get submissionID and userID dynamically (assuming from URL or session)
$submissionID = isset($_GET['submissionID']) ? intval($_GET['submissionID']) : 0;
$userID = isset($_SESSION['userID']) ? intval($_SESSION['userID']) : 0;

// Get assessmentID linked to this submission
$assessmentQuery = $conn->prepare("
    SELECT assessmentID 
    FROM submissions 
    WHERE submissionID = ?
    LIMIT 1
");
$assessmentQuery->bind_param("i", $submissionID);
$assessmentQuery->execute();
$assessmentResult = $assessmentQuery->get_result();
$assessmentRow = $assessmentResult->fetch_assoc();
$assessmentID = intval($assessmentRow['assessmentID'] ?? 0);

// Get rubricID linked to this assessment
$rubricQuery = $conn->prepare("
    SELECT rubricID 
    FROM assignments
    WHERE assessmentID = ?
    LIMIT 1
");
$rubricQuery->bind_param("i", $assessmentID);
$rubricQuery->execute();
$rubricResult = $rubricQuery->get_result();
$rubricRow = $rubricResult->fetch_assoc();
$rubricID = intval($rubricRow['rubricID'] ?? 0);

// Pull only criteria for this rubric dynamically
$criterionQuery = $conn->prepare("
    SELECT c.criterionID, c.criteriaTitle, l.levelID, l.levelTitle, l.levelDescription, l.points
    FROM criteria c
    INNER JOIN level l ON c.criterionID = l.criterionID
    WHERE c.rubricID = ?
    ORDER BY c.criterionID, l.points DESC
");
$criterionQuery->bind_param("i", $rubricID);
$criterionQuery->execute();
$criterionResult = $criterionQuery->get_result();


// --- 4. Get assessmentID from submissions ---
$submissionQuery = executeQuery("
    SELECT assessmentID
    FROM submissions
    WHERE submissionID = $submissionID
");
if (!$submissionQuery || $submissionQuery->num_rows === 0) {
    echo "<p class='text-danger'>Submission not found.</p>";
    exit;
}
$submissionData = $submissionQuery->fetch_assoc();
$assessmentID = intval($submissionData['assessmentID']);

// --- 5. Get rubricID from assignments using assessmentID ---
$assignmentQuery = executeQuery("
    SELECT rubricID
    FROM assignments
    WHERE assessmentID = $assessmentID
");
if (!$assignmentQuery || $assignmentQuery->num_rows === 0) {
    echo "<p class='text-danger'>Assignment not found for this submission.</p>";
    exit;
}
$assignmentData = $assignmentQuery->fetch_assoc();
$rubricID = intval($assignmentData['rubricID']);

// --- 6. Fetch rubric info ---
$rubricQuery = executeQuery("SELECT * FROM rubric WHERE rubricID = $rubricID");
if (!$rubricQuery || $rubricQuery->num_rows === 0) {
    echo "<p class='text-danger'>Rubric not found.</p>";
    exit;
}
$rubric = $rubricQuery->fetch_assoc();
$totalPoints = $rubric['totalPoints'] ?? 0;

// --- 7. Fetch all criteria for this rubric ---
$criteriaQuery = executeQuery("
    SELECT * 
    FROM criteria 
    WHERE rubricID = $rubricID
    ORDER BY criterionID ASC
");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitGrade'])) {
    // --- Existing grade & feedback code here ---
    $score = isset($_POST['score']) ? floatval($_POST['score']) : 0;
    $feedback = trim($_POST['feedback'] ?? '');

    if ($userID > 0 && $submissionID > 0) {
        // Check if a record already exists for this user and submission
        $check = $conn->prepare("SELECT scoreID FROM scores WHERE userID = ? AND submissionID = ?");
        $check->bind_param("ii", $userID, $submissionID);
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
            $insert->bind_param("iids", $userID, $submissionID, $score, $feedback);
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
        $enrollmentQuery->bind_param("ii", $userID, $details['courseID']);
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
            
            // Get student email and check if they have courseUpdateEnabled
            $selectEmailQuery = "
                SELECT u.email, COALESCE(s.courseUpdateEnabled, 0) as courseUpdateEnabled
                FROM users u
                LEFT JOIN settings s ON u.userID = s.userID
                WHERE u.userID = ?
            ";
            $emailStmt = $conn->prepare($selectEmailQuery);
            $emailStmt->bind_param("i", $userID);
            $emailStmt->execute();
            $emailResult = $emailStmt->get_result();
            
            if ($emailResult && $emailResult->num_rows > 0) {
                $studentData = $emailResult->fetch_assoc();
                
                if ($studentData['courseUpdateEnabled'] == 1 && !empty($studentData['email'])) {
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
                        $mail->Subject = "[GRADED] " . $details['assessmentTitle'] . " - " . $details['courseCode'];
                        $mail->addAddress($studentData['email']);
                        
                        $assessmentTitleEsc = htmlspecialchars($details['assessmentTitle'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                        $courseCodeEsc = htmlspecialchars($details['courseCode'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                        $courseTitleEsc = htmlspecialchars($details['courseTitle'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                        $scoreDisplay = number_format($score, 2);
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
                                                        <p style="font-size:18px; color:#2C2C2C; margin:10px 0;"><strong>Score: ' . $scoreDisplay . '/100</strong></p>
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
            $emailStmt->close();
        }
        $enrollmentQuery->close();
    }

    if (!empty($_POST['selectedBadgeIDs'])) {
        $badgeIDs = explode(',', $_POST['selectedBadgeIDs']); // Split multiple IDs
        $userID = intval($details['userID']);
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
            $check->bind_param("iii", $userID, $badgeID, $assignmentID);
            $check->execute();
            $checkResult = $check->get_result();

            if ($checkResult && $checkResult->num_rows == 0) {
                $insert = $conn->prepare("
                INSERT INTO studentbadges (userID, badgeID, courseID, assignmentID, earnedAt) 
                VALUES (?, ?, ?, ?, ?)
            ");
                $insert->bind_param("iiiis", $userID, $badgeID, $courseID, $assignmentID, $earnedAt);
                $insert->execute();
                $insert->close();
            }

            $check->close();
        }
    }

    if (isset($_POST['selectedLevels']) && !empty($_POST['selectedLevels'])) {
        $submissionID = intval($_POST['submissionID']);
        $selectedLevels = json_decode($_POST['selectedLevels'], true);

        // Delete old selections
        $conn->query("DELETE FROM selectedlevels WHERE submissionID = $submissionID");

        // Insert new selections
        foreach ($selectedLevels as $levelID) {
            $levelID = intval($levelID);
            $conn->query("INSERT INTO selectedlevels (submissionID, levelID) VALUES ($submissionID, $levelID)");
        }

        echo "<script>alert('Grades saved successfully!'); window.location='" . $_SERVER['REQUEST_URI'] . "';</script>";
        exit;
    }

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
                                        <a href="todo.php?userID=<?php echo htmlspecialchars($userID); ?>"
                                            class="text-decoration-none">
                                            <i class="fa-solid fa-arrow-left text-20" style="color: var(--black);"></i>
                                        </a>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle"
                                                style="width: 40px; height: 40px; background-color: var(--highlight75);">
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
                                    <div class="col-auto text-end" style="line-height: 1.3;">
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
                                            <a href="todo.php?userID=<?php echo htmlspecialchars($userID); ?>"
                                                class="text-decoration-none">
                                                <i class="fa-solid fa-arrow-left text-reg text-16"
                                                    style="color: var(--black);"></i>
                                            </a>
                                        </div>
                                        <div class="title text-sbold text-18">
                                            <?php echo htmlspecialchars($details['assessmentTitle']); ?>
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

                                    <div class="text-sbold text-14 mt-4">Attachments</div>

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
                                            <div class="container mt-3">
                                                <div class="row g-4">
                                                    <?php foreach ($images as $index => $f):
                                                        $link = htmlspecialchars($f['path']);
                                                        $name = htmlspecialchars($f['name']);
                                                        ?>
                                                        <div class="col-12 col-sm-6 col-md-4">
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
                                            <p class="text-sbold text-14 mt-4">Link Attachments</p>

                                            <?php foreach ($linkFiles as $f): ?>
                                                <div class="mt-4b">
                                                    <iframe src="<?php echo htmlspecialchars($f['link']); ?>" width="100%"
                                                        height="600" frameborder="0" allowfullscreen
                                                        allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                                                        loading="lazy" style="border-radius: 10px; border: 1px solid var(--black);">
                                                    </iframe>

                                                    <div class="text-start mt-3">
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

                            <!-- RIGHT CONTENT -->
                            <div class="col-12 col-lg-4">
                                <div class="cardSticky position-sticky" style="top: 20px;">
                                    <div class="ms-2 me-2">

                                        <!-- TOTAL GRADE -->
                                        <div class="d-flex align-items-center justify-content-center mt-5">
                                            <div class="text-sbold" id="totalGrade" id="submitGrade">
                                                0/<?= $totalPoints ?></div>
                                        </div>

                                        <form method="post" id="rubricForm">
                                            <input type="hidden" name="submissionID" value="<?= $submissionID ?>">
                                            <input type="hidden" name="selectedLevels" id="selectedLevelsInput">
                                            <input type="hidden" name="score" id="scoreInput">
                                            <input type="hidden" id="selectedBadgeIDs" name="selectedBadgeIDs" value="">

                                            <div class="d-flex align-items-center justify-content-center mb-5">
                                                <div class="text-reg"><i>Grade</i></div>
                                            </div>

                                            <?php while ($criterion = $criteriaQuery->fetch_assoc()): ?>
                                                <?php
                                                $criterionID = $criterion['criterionID'];
                                                $criterionTitle = $criterion['criteriaTitle']; // removed htmlspecialchars as requested
                                            
                                                $levelsQuery = executeQuery("
                                                SELECT * FROM level
                                                WHERE criterionID = $criterionID
                                                ORDER BY points DESC
                                            ");
                                                ?>

                                                <div class="text-center mt-5">
                                                    <div class="text-sbold text-15 mb-3" style="color: var(--black);">
                                                        <?= $criterionTitle ?>
                                                    </div>

                                                    <div id="ratingAccordion<?= $criterionID ?>">
                                                        <?php while ($level = $levelsQuery->fetch_assoc()): ?>
                                                            <?php
                                                            $levelID = $level['levelID'];
                                                            $levelTitle = $level['levelTitle'];
                                                            $levelDescription = $level['levelDescription'];
                                                            $points = intval($level['points']);
                                                            $collapseID = "level{$levelID}";
                                                            ?>
                                                            <div class="mb-2">
                                                                <button
                                                                    class="btn w-100 d-flex align-items-center justify-content-center flex-column text-med text-14 level-btn"
                                                                    type="button" data-bs-toggle="collapse"
                                                                    data-bs-target="#<?= $collapseID ?>" aria-expanded="false"
                                                                    aria-controls="<?= $collapseID ?>"
                                                                    data-criterion="<?= $criterionID ?>"
                                                                    data-points="<?= $points ?>" data-level-id="<?= $levelID ?>"
                                                                    style="background-color: var(--pureWhite); border-radius: 10px; border: 1px solid var(--black);">

                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center w-100 px-3">
                                                                        <span class="flex-grow-1 text-center ps-3 level-select">
                                                                            <?= $levelTitle ?> · <?= $points ?> pts
                                                                        </span>
                                                                        <span
                                                                            class="material-symbols-rounded transition">expand_more</span>
                                                                    </div>

                                                                    <div class="collapse w-100 mt-2" id="<?= $collapseID ?>"
                                                                        data-bs-parent="#ratingAccordion<?= $criterionID ?>">
                                                                        <p class="mb-0 px-3 pb-2 text-reg text-14">
                                                                            <?= $levelDescription ?>
                                                                        </p>
                                                                    </div>
                                                                </button>
                                                            </div>
                                                        <?php endwhile; ?>
                                                    </div>
                                                </div>
                                            <?php endwhile; ?>


                                            <!-- optional action -->
                                            <div class="text-center mt-5">
                                                <div class="text-sbold text-15 mb-3" style="color: var(--black);">
                                                    Optional Actions
                                                </div>
                                                <div class="d-flex flex-column align-items-center gap-2 mb-5">
                                                    <!-- ADD AWARD BADGE BUTTON -->
                                                    <button type="button"
                                                        class="btn custom-btn d-flex align-items-center justify-content-center"
                                                        data-bs-toggle="modal" data-bs-target="#awardBadgeModal">
                                                        <span class="material-symbols-rounded me-2">emoji_events</span>
                                                        Award badge
                                                    </button>
                                                    <!-- FEEDBACK INPUT (replacing modal button) -->
                                                    <textarea name="feedback" id="feedbackInput" rows="3"
                                                        class="form-control text-reg text-15 rounded-3 p-3 mt-3"
                                                        style="resize: none; background-color: var(--pureWhite); border: 1px solid var(--black) !important;"
                                                        placeholder="Write feedback that helps your student level up their learning journey!"><?= htmlspecialchars($existingFeedback ?? '') ?></textarea>
                                                </div>
                                            </div>
                                            <!-- Buttons always below the card -->
                                            <div class="text-center mt-4">
                                                <div
                                                    class="d-flex justify-content-center align-items-center gap-3 mb-2">
                                                    <button type="button" id="prevBtn"
                                                        class="btn px-4 py-2 rounded-pill text-15 fw-semibold"
                                                        data-current-index="<?php echo $currentIndex; ?>"
                                                        style="background-color: var(--pureWhite); border: 1px solid var(--black); color: var(--black);">
                                                        Previous
                                                    </button>

                                                    <button type="submit" name="submitGrade" id="submitRubricBtn"
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

                                                <p class="text-15 fw-medium mt-2" style="color: var(--black);">
                                                    <?php echo $leftToReview; ?> submissions left to review
                                                </p>

                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- row -->
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
                                                    <div class="text-med text-12 text-muted ms-1">
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

        document.addEventListener('DOMContentLoaded', function () {
            const buttons = document.querySelectorAll('.level-btn');
            const gradeDisplay = document.getElementById('gradeDisplay');
            const maxPoints = 100; // fixed max points
            let totalPoints = 0;

            gradeDisplay.textContent = `0/${maxPoints}`;

            buttons.forEach(button => {
                const points = parseInt(button.getAttribute('data-points'), 10);

                button.addEventListener('click', function (e) {
                    if (e.target.closest('.collapse')) return;

                    if (button.classList.contains('selected')) {
                        button.classList.remove('selected');
                        totalPoints -= points;
                        button.style.backgroundColor = 'var(--pureWhite)';
                    } else {
                        button.classList.add('selected');
                        totalPoints += points;
                        button.style.backgroundColor = 'var(--primaryColor)';
                    }

                    gradeDisplay.textContent = `${totalPoints}/${maxPoints}`;
                });
            });

            // Collapse icon rotation
            buttons.forEach(button => {
                const target = button.getAttribute('data-bs-target');
                const icon = button.querySelector('.material-symbols-rounded');
                const collapse = document.querySelector(target);

                if (collapse && icon) {
                    collapse.addEventListener('show.bs.collapse', () => {
                        icon.style.transform = 'rotate(180deg)';
                        icon.style.transition = 'transform 0.3s';
                    });

                    collapse.addEventListener('hide.bs.collapse', () => {
                        icon.style.transform = 'rotate(0deg)';
                    });
                }
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

        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form'); // your grading form
            const gradeDisplay = document.getElementById('gradeDisplay');
            const scoreInput = document.getElementById('scoreInput');

            form.addEventListener('submit', function () {
                // Extract only the numeric part from "xx/100"
                const scoreText = gradeDisplay.textContent.trim();
                const scoreValue = parseFloat(scoreText.split('/')[0]) || 0;
                scoreInput.value = scoreValue;
            });
        });


        // Rubric level selection and total score
        const levelButtons = document.querySelectorAll(".level-btn");
        const totalGradeEl = document.getElementById("totalGrade"); // top display
        const totalScoreEl = document.getElementById("totalScore");   // bottom display
        const totalPossible = <?= $totalPoints ?>;
        const criterionSelections = {}; // store selected points per criterion

        levelButtons.forEach(btn => {
            btn.addEventListener("click", function (e) {
                e.preventDefault();
                e.stopPropagation();

                const criterionID = this.dataset.criterion;
                const points = parseInt(this.dataset.points);
                const levelID = parseInt(this.dataset.levelId);

                // Check if already selected
                const isAlreadySelected = this.classList.contains("btn-active");

                // Deselect all buttons in this criterion
                levelButtons.forEach(b => {
                    if (b.dataset.criterion === criterionID) {
                        b.classList.remove("btn-active");
                        b.style.backgroundColor = "";
                        b.style.border = "1px solid var(--black)";
                    }
                });

                if (!isAlreadySelected) {
                    // Select current button
                    this.classList.add("btn-active");
                    this.style.backgroundColor = "var(--primaryColor)";
                    this.style.border = "1px solid var(--black)";

                    // Save selection
                    criterionSelections[criterionID] = { points: points, levelID: levelID };
                } else {
                    // If unselected, remove from criterionSelections
                    delete criterionSelections[criterionID];
                }

                // Recalculate total
                const total = Object.values(criterionSelections).reduce((a, b) => a + b.points, 0);

                // Update displays
                totalGradeEl.textContent = `${total}/${totalPossible}`;
                totalScoreEl.textContent = total;
            });
        });

        const submitBtn = document.getElementById("submitRubricBtn");
        const rubricForm = document.getElementById("rubricForm");
        const selectedLevelsInput = document.getElementById("selectedLevelsInput");

        submitBtn.addEventListener("click", function () {
            // collect selected levelIDs
            const selectedLevelIDs = Object.values(criterionSelections).map(v => v.levelID);

            if (selectedLevelIDs.length === 0) {
                alert("Please select at least one level before submitting.");
                return;
            }

            // set hidden input
            selectedLevelsInput.value = JSON.stringify(selectedLevelIDs);

            // submit the form
            rubricForm.submit();
        });

        submitBtn.addEventListener("click", function () {
            const selectedLevelIDs = Object.values(criterionSelections).map(v => v.levelID);
            if (selectedLevelIDs.length === 0) {
                alert("Please select at least one level before submitting.");
                return;
            }

            // set hidden input for levels
            selectedLevelsInput.value = JSON.stringify(selectedLevelIDs);

            // set hidden input for total score
            document.getElementById('scoreInput').value = Object.values(criterionSelections).reduce((a, b) => a + b.points, 0);

            rubricForm.submit();
        });

        submitBtn.addEventListener("click", function () {
            // Collect selected badge IDs
            const badgeOptions = document.querySelectorAll(".badge-option");
            const selectedBadges = [];

            badgeOptions.forEach(badge => {
                if (badge.classList.contains("selected-badge")) {
                    selectedBadges.push(badge.dataset.badgeId);
                }
            });

            // Set hidden input value before submitting
            const badgeIDsInput = document.getElementById("selectedBadgeIDs");
            badgeIDsInput.value = selectedBadges.join(",");

            // The rest of your submit logic (rubric levels, total score) stays the same
        });


    </script>
</body>

</html>