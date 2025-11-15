<?php
$activePage = 'assignment';

include('shared/assets/database/connect.php');
include("shared/assets/processes/session-process.php");

$assignmentID = intval($_GET['assignmentID'] ?? 0);
// SUBMISSIONS QUERY 

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

    // Ensure the link has a scheme
    if (!preg_match('/^https?:\/\//', $link)) {
        $link = 'http://' . $link;
    }

    // Set context for user-agent (some sites block default PHP requests)
    $options = [
        "http" => [
            "header" => "User-Agent: Mozilla/5.0\r\n",
            "timeout" => 5 // optional: prevent hanging
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

// Handle AJAX request to update modalShown
if (isset($_POST['updateModalShown']) && isset($_POST['submissionID'])) {
    $sid = intval($_POST['submissionID']);
    executeQuery("UPDATE submissions SET modalShown = 1 WHERE submissionID = '$sid'");
    exit;
}
// --- Fetch user's current Webstars ---
$userWebstarsQuery = "SELECT webstars FROM profile WHERE userID = '$userID' LIMIT 1";
$result = executeQuery($userWebstarsQuery);
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $userWebstars = (int) $row['webstars'];
} else {
    $userWebstars = 0;
}

// --- Fetch assessment and course info ---
$assessmentQuery = "
    SELECT assessments.assessmentID, courses.courseID, assessments.createdAt, assessments.deadline,
           assessments.deadlineEnabled
    FROM assignments
    INNER JOIN assessments ON assignments.assessmentID = assessments.assessmentID
    INNER JOIN courses ON assessments.courseID = courses.courseID
    WHERE assignments.assignmentID = '$assignmentID'
    LIMIT 1
";

$assessmentResult = executeQuery($assessmentQuery);
$assessmentRow = mysqli_fetch_assoc($assessmentResult);
$assessmentID = $assessmentRow['assessmentID'];
$courseID = $assessmentRow['courseID'];
$assignmentCreated = $assessmentRow['createdAt'];
$deadline = $assessmentRow['deadline'];
$deadlineEnabled = $assessmentRow['deadlineEnabled'];

$now = date("Y-m-d H:i:s");
$lockSubmission = ($deadlineEnabled == 1 && strtotime($now) > strtotime($deadline));

// --- Check if user already submitted ---
$submissionQuery = "
    SELECT submissionID, isSubmitted, submittedAt, scoreID 
    FROM submissions 
    WHERE assessmentID = '$assessmentID' AND userID = '$userID'
    ORDER BY submittedAt DESC
    LIMIT 1
";
$submissionResult = executeQuery($submissionQuery);

if ($submissionResult && mysqli_num_rows($submissionResult) > 0) {
    $submissionRow = mysqli_fetch_assoc($submissionResult);
    $isSubmitted = $submissionRow['isSubmitted'];
    $submissionID = $submissionRow['submissionID'];
    $submissionDate = $submissionRow['submittedAt'];
    $scoreID = $submissionRow['scoreID'];
} else {
    $isSubmitted = 0;
    $submissionID = null;
    $submissionDate = null;
    $scoreID = null;
}

// --- Handle multiple file uploads (Turn In) ---
if (!empty($_FILES['fileAttachment']['name'][0])) {
    $targetDir = "shared/assets/files/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $submissionCheck = executeQuery("
        SELECT submissionID FROM submissions 
        WHERE assessmentID = '$assessmentID' AND userID = '$userID'
        LIMIT 1
    ");

    if ($submissionCheck && mysqli_num_rows($submissionCheck) > 0) {
        $submissionRow = mysqli_fetch_assoc($submissionCheck);
        $submissionID = $submissionRow['submissionID'];
    } else {
        executeQuery("
            INSERT INTO submissions (assessmentID, userID, scoreID, submittedAt, isSubmitted)
            VALUES ('$assessmentID', '$userID', NULL, NOW(), 1)
        ");
        $submissionID = mysqli_insert_id($conn);
    }

    // Update submission
    executeQuery("
        UPDATE submissions 
        SET submittedAt = NOW(), isSubmitted = 1 
        WHERE submissionID = '$submissionID'
    ");

    // Fetch updated submission date immediately
    $updatedSubmission = executeQuery("
        SELECT submittedAt 
        FROM submissions 
        WHERE submissionID = '$submissionID'
        LIMIT 1
    ");
    if ($updatedSubmission && mysqli_num_rows($updatedSubmission) > 0) {
        $row = mysqli_fetch_assoc($updatedSubmission);
        $submissionDate = $row['submittedAt'];
    }

    $isSubmitted = 1;

    foreach ($_FILES['fileAttachment']['name'] as $key => $fileName) {
        $fileTmp = $_FILES['fileAttachment']['tmp_name'][$key];
        $fileName = basename($fileName);
        $targetFilePath = $targetDir . $fileName;

        if (move_uploaded_file($fileTmp, $targetFilePath)) {
            $insertFile = "
                INSERT INTO files (courseID, userID, submissionID, fileAttachment)
                VALUES ('$courseID', '$userID', '$submissionID', '$fileName')
            ";
            executeQuery($insertFile);
        }
    }
}

// --- Handle link submissions ---
if (isset($_POST['links']) && !empty($_POST['links'])) {
    $linksArray = json_decode($_POST['links'], true);
    if (is_array($linksArray)) {
        foreach ($linksArray as $linkData) {
            $rawLink = mysqli_real_escape_string($conn, $linkData['link']);
            $processedLink = processGoogleLink($rawLink);

            // Fetch actual title of the page
            $fileTitle = mysqli_real_escape_string($conn, fetchLinkTitle($rawLink));

            if (!$submissionID) {
                executeQuery("
                    INSERT INTO submissions (assessmentID, userID, scoreID, submittedAt, isSubmitted)
                    VALUES ('$assessmentID', '$userID', NULL, NOW(), 1)
                ");
                $submissionID = mysqli_insert_id($conn);
            }

            executeQuery("
                INSERT INTO files (courseID, userID, submissionID, fileTitle, fileLink)
                VALUES ('$courseID', '$userID', '$submissionID', '$fileTitle', '$processedLink')
            ");

        }

        // Update submission
        executeQuery("
            UPDATE submissions 
            SET submittedAt = NOW(), isSubmitted = 1 
            WHERE submissionID = '$submissionID'
        ");

        // Fetch updated submission date immediately
        $updatedSubmission = executeQuery("
            SELECT submittedAt 
            FROM submissions 
            WHERE submissionID = '$submissionID'
            LIMIT 1
        ");
        if ($updatedSubmission && mysqli_num_rows($updatedSubmission) > 0) {
            $row = mysqli_fetch_assoc($updatedSubmission);
            $submissionDate = $row['submittedAt'];
        }

        $isSubmitted = 1;
    }
}

// --- Handle Turn In without new file uploads ---
if (isset($_POST['assessmentID']) && empty($_FILES['fileAttachment']['name'][0])) {
    if ($submissionID) {
        executeQuery("
            UPDATE submissions 
            SET submittedAt = NOW(), isSubmitted = 1 
            WHERE submissionID = '$submissionID' AND userID = '$userID'
        ");
        $isSubmitted = 1;
    } else {
        executeQuery("
            INSERT INTO submissions (assessmentID, userID, scoreID, submittedAt, isSubmitted)
            VALUES ('$assessmentID', '$userID', NULL, NOW(), 1)
        ");
        $submissionID = mysqli_insert_id($conn);
        $isSubmitted = 1;
    }
}
$showSubmittedModal = false;

if ($submissionID) {
    $modalQuery = "
        SELECT modalShown 
        FROM submissions 
        WHERE submissionID = '$submissionID' 
        LIMIT 1
    ";
    $modalResult = executeQuery($modalQuery);
    if ($modalResult && mysqli_num_rows($modalResult) > 0) {
        $modalRow = mysqli_fetch_assoc($modalResult);
        // Only show modal if initial submission AND modalShown = 0
        if ($modalRow['modalShown'] == 0 && $isSubmitted) {
            $showSubmittedModal = true;
        }
    }
}

// --- Handle Unsubmit ---
if (isset($_POST['unsubmit'])) {
    if ($submissionID) {
        executeQuery("
            UPDATE submissions 
            SET isSubmitted = 0 
            WHERE submissionID = '$submissionID' AND userID = '$userID'
        ");
        $isSubmitted = 0;
        $submissionDate = null;
    }
}

// --- Handle deleted files on re-submit ---
if (isset($_POST['deletedFiles']) && !empty($_POST['deletedFiles'])) {
    $deletedFiles = json_decode($_POST['deletedFiles'], true);
    if (is_array($deletedFiles)) {
        foreach ($deletedFiles as $fileToDelete) {
            $fileToDelete = mysqli_real_escape_string($conn, $fileToDelete);

            executeQuery("
                DELETE FROM files 
                WHERE submissionID = '$submissionID' 
                  AND userID = '$userID' 
                  AND fileAttachment = '$fileToDelete'
            ");

            $filePath = "shared/assets/files/" . $fileToDelete; // instead of img/files
            if (file_exists($filePath))
                unlink($filePath);
        }
    }
}

// --- Fetch existing uploaded files (to show in sticky card) ---
$files = [];
if ($submissionID) {
    $filesQuery = "
        SELECT fileAttachment 
        FROM files 
        WHERE submissionID = '$submissionID'
    ";
    $filesResult = executeQuery($filesQuery);
    while ($row = mysqli_fetch_assoc($filesResult)) {
        $files[] = $row['fileAttachment'];
    }
}

// Fetch existing links for this submission
$links = [];
if ($submissionID) {
    $linksQuery = "
        SELECT fileLink, fileTitle
        FROM files
        WHERE submissionID = '$submissionID' AND fileLink IS NOT NULL
    ";
    $linksResult = executeQuery($linksQuery);
    while ($row = mysqli_fetch_assoc($linksResult)) {
        $links[] = [
            'link' => $row['fileLink'],
            'title' => $row['fileTitle']
        ];
    }
}


// --- Update todo table status based on submission actions ---
$todoCheckQuery = "
    SELECT todoID, status FROM todo 
    WHERE userID = '$userID' AND assessmentID = '$assessmentID'
    LIMIT 1
";
$todoCheckResult = executeQuery($todoCheckQuery);
$hasTodo = mysqli_num_rows($todoCheckResult) > 0;
$todoRow = $hasTodo ? mysqli_fetch_assoc($todoCheckResult) : null;
$todoStatus = $todoRow['status'] ?? 'Pending';

// --- Update todo table status based on submission actions ---
if ($isSubmitted) {
    // Only update to "Submitted" if not yet graded
    if (empty($scoreID)) {
        $newStatus = 'Submitted';
        if ($hasTodo) {
            executeQuery("
                UPDATE todo 
                SET status = '$newStatus', updatedAt = NOW()
                WHERE userID = '$userID' AND assessmentID = '$assessmentID'
            ");
        } else {
            executeQuery("
                INSERT INTO todo (userID, assessmentID, status, updatedAt)
                VALUES ('$userID', '$assessmentID', '$newStatus', NOW())
            ");
        }
    } else {
        // If already graded, ensure todo shows "Graded"
        $newStatus = 'Graded';
        if ($hasTodo) {
            executeQuery("
                UPDATE todo 
                SET status = '$newStatus', updatedAt = NOW()
                WHERE userID = '$userID' AND assessmentID = '$assessmentID'
            ");
        } else {
            executeQuery("
                INSERT INTO todo (userID, assessmentID, status, updatedAt)
                VALUES ('$userID', '$assessmentID', '$newStatus', NOW())
            ");
        }
    }
}

if (isset($_POST['unsubmit'])) {
    if ($hasTodo) {
        executeQuery("
            UPDATE todo 
            SET status = 'Pending', updatedAt = NOW()
            WHERE userID = '$userID' AND assessmentID = '$assessmentID'
        ");
    } else {
        executeQuery("
            INSERT INTO todo (userID, assessmentID, status, updatedAt)
            VALUES ('$userID', '$assessmentID', 'Pending', NOW())
        ");
    }
}

$now = date("Y-m-d H:i:s");

if ($isSubmitted) {
    // already submitted, do nothing or ensure status = Submitted
    $todoStatus = 'Submitted';
} else {
    if (strtotime($now) > strtotime($deadline)) {
        // Past deadline → mark Missing
        if ($hasTodo) {
            executeQuery("
                UPDATE todo 
                SET status = 'Missing', updatedAt = NOW()
                WHERE userID = '$userID' AND assessmentID = '$assessmentID'
            ");
        } else {
            executeQuery("
                INSERT INTO todo (userID, assessmentID, status, updatedAt)
                VALUES ('$userID', '$assessmentID', 'Missing', NOW())
            ");
        }
        $todoStatus = 'Missing';
    } else {
        // Before deadline → mark Pending
        if ($hasTodo) {
            executeQuery("
                UPDATE todo 
                SET status = 'Pending', updatedAt = NOW()
                WHERE userID = '$userID' AND assessmentID = '$assessmentID'
            ");
        } else {
            executeQuery("
                INSERT INTO todo (userID, assessmentID, status, updatedAt)
                VALUES ('$userID', '$assessmentID', 'Pending', NOW())
            ");
        }
        $todoStatus = 'Pending';
    }
}


// --- Fetch grading information (if exists) ---
$gradedAt = null;
$statusUpdated = null;
if (!empty($scoreID)) {
    $scoreQuery = "
        SELECT gradedAt 
        FROM scores 
        WHERE scoreID = '$scoreID' 
        LIMIT 1
    ";
    $scoreResult = executeQuery($scoreQuery);
    if ($scoreResult && mysqli_num_rows($scoreResult) > 0) {
        $scoreRow = mysqli_fetch_assoc($scoreResult);
        $gradedAt = $scoreRow['gradedAt'];
        $statusUpdated = $gradedAt;
    }
}

// --- Compute flags for timeline display ---
$isGraded = ($todoStatus === 'Graded');
if (!empty($gradedAt)) {
    $isGraded = true;
}
$isMissing = ($todoStatus === 'Missing');
$isBeforeDeadline = ($isSubmitted && strtotime($submissionDate) <= strtotime($deadline));
$isLate = ($isSubmitted && strtotime($submissionDate) > strtotime($deadline));

// --- Format date helper ---
function fmtDate($d)
{
    if (empty($d))
        return '—';
    $ts = strtotime($d);
    if ($ts === false || $ts <= 0)
        return '—';
    return date("M j, Y, g:iA", $ts);
}
// --- Initialize reward variables (prevent undefined warnings) ---
$baseXP = 100;
$baseWebstars = 10;
$finalXP = 0;
$bonusXP = 0;
$finalWebstars = 0;
$bonusWebstars = 0;

$showSubmittedModal = false;

// --- Compute Webstars and XP rewards or deductions ---
if ($submissionID) {

    $submittedAtTimestamp = strtotime($submissionDate ?? $now);
    $deadlineTimestamp = strtotime($deadline);
    $daysEarly = ($deadlineTimestamp - $submittedAtTimestamp) / 60 / 1440;

    if ($daysEarly >= 0) {
        $timeFactor = min(10 + $daysEarly, 15);
    } else {
        $timeFactor = max(10 + $daysEarly, 5);
    }

    // --- Compute final XP, Webstars, and bonus separately ---
    $finalXP = round($baseXP * $timeFactor);
    $bonusXP = $finalXP - $baseXP;

    $finalWebstars = round($baseWebstars * $timeFactor);
    $bonusWebstars = $finalWebstars - $baseWebstars;

    $modalCheck = executeQuery("
        SELECT modalShown 
        FROM submissions 
        WHERE submissionID = '$submissionID' 
        LIMIT 1
    ");
    if ($modalCheck && mysqli_num_rows($modalCheck) > 0) {
        $modalRow = mysqli_fetch_assoc($modalCheck);
        $modalAlreadyShown = (int) $modalRow['modalShown'];
    } else {
        $modalAlreadyShown = 0;
    }

    // Show modal only if first submission & not yet shown
    if ($isSubmitted && $modalAlreadyShown === 0) {
        $showSubmittedModal = true;
    }

    // --- Handle Submission rewards ---
    if ($isSubmitted && empty($scoreID)) {
        $checkWebstarsQuery = "
            SELECT webstarsID FROM webstars 
            WHERE userID = '$userID' 
              AND sourceType = 'Submission'
              AND assessmentID = '$assessmentID'
            LIMIT 1
        ";
        $checkWebstarsResult = mysqli_query($conn, $checkWebstarsQuery);
        if (!$checkWebstarsResult)
            die("Check query failed: " . mysqli_error($conn));

        // Only insert if first-time submission
        if (mysqli_num_rows($checkWebstarsResult) == 0) {

            // --- Insert reward into webstars table ---
            $insertWebstarQuery = "
                INSERT INTO webstars (userID, sourceType, assessmentID, pointsChanged, dateEarned)
                VALUES ('$userID', 'Submission', '$assessmentID', '$finalWebstars', NOW())
            ";
            mysqli_query($conn, $insertWebstarQuery) or die("Insert failed: " . mysqli_error($conn));

            // --- Update profile total ---
            mysqli_query($conn, "
                UPDATE profile 
                SET webstars = webstars + $finalWebstars 
                WHERE userID = '$userID'
            ");

            // --- Fetch enrollmentID from enrollments table ---
            $enrollmentQuery = "
                SELECT enrollmentID 
                FROM enrollments 
                WHERE userID = '$userID' AND courseID = '$courseID'
                LIMIT 1
            ";
            $enrollmentResult = executeQuery($enrollmentQuery);
            if ($enrollmentResult && mysqli_num_rows($enrollmentResult) > 0) {
                $enrollmentRow = mysqli_fetch_assoc($enrollmentResult);
                $enrollmentID = $enrollmentRow['enrollmentID'];

                if ($enrollmentID) {
                    // --- Insert XP into leaderboard ---
                    $insertLeaderboardQuery = "
                        INSERT INTO leaderboard (enrollmentID, xpPoints, updatedAt)
                        VALUES ('$enrollmentID', '$finalXP', NOW())
                    ";
                    $result = mysqli_query($conn, $insertLeaderboardQuery);
                    if (!$result) {
                        die("Leaderboard insert failed: " . mysqli_error($conn));
                    }
                }
            }
        }
    } elseif (isset($_POST['unsubmit']) && !$isSubmitted) {
        // --- Handle Unsubmit deductions ---
        $deductWebstars = 50;

        // Insert negative record into webstars table
        mysqli_query($conn, "
            INSERT INTO webstars (userID, sourceType, assessmentID, pointsChanged, dateEarned)
            VALUES ('$userID', 'Unsubmit', '$assessmentID', -$deductWebstars, NOW())
        ") or die("Insert failed: " . mysqli_error($conn));

        // Subtract from profile total
        mysqli_query($conn, "
            UPDATE profile 
            SET webstars = webstars - $deductWebstars 
            WHERE userID = '$userID'
        ");
    }
}

// ASSIGNMENT INFO QUERY
$userQuery = "SELECT * FROM users 
              LEFT JOIN userinfo ON users.userID = userinfo.userID 
              WHERE users.userID = $userID";
$userResult = executeQuery($userQuery);

$assignmentQuery = "
SELECT 
    assignments.assignmentID,
    assignments.assignmentDescription,
    assignments.assignmentPoints,
    assignments.rubricID,
    assessments.assessmentTitle,
    assessments.deadline,
    rubric.rubricTitle,
    rubric.totalPoints AS rubricPoints,
    userinfo.firstName,
    userinfo.lastName,
    userinfo.profilePicture
FROM assignments
LEFT JOIN assessments ON assignments.assessmentID = assessments.assessmentID
LEFT JOIN courses ON assessments.courseID = courses.courseID
LEFT JOIN userinfo ON courses.userID = userinfo.userID
LEFT JOIN rubric ON assignments.rubricID = rubric.rubricID
WHERE assignments.assignmentID = '$assignmentID'
";

$assignmentResult = executeQuery($assignmentQuery);
$assignmentRow = mysqli_fetch_assoc($assignmentResult);

if ($assignmentRow) {
    $assignmentTitle = $assignmentRow['assessmentTitle'];
    $assignmentDescription = $assignmentRow['assignmentDescription'];
    $profName = $assignmentRow['firstName'] . ' ' . $assignmentRow['lastName'];
    $profProfile = $assignmentRow['profilePicture'];
    $deadline = $assignmentRow['deadline'];

    // Determine total points (rubric overrides assignment points if exists)
    $totalPoints = !empty($assignmentRow['rubricPoints'])
        ? $assignmentRow['rubricPoints']
        : $assignmentRow['assignmentPoints'];
}

$selectedLevels = [];

if (!empty($submissionID)) {
    $sid = intval($submissionID); // Ensure it's an integer
    $selectedQuery = "SELECT levelID FROM selectedlevels WHERE submissionID = $sid";
    $selectedResult = mysqli_query($conn, $selectedQuery);

    if ($selectedResult && mysqli_num_rows($selectedResult) > 0) {
        while ($row = mysqli_fetch_assoc($selectedResult)) {
            $selectedLevels[] = $row['levelID'];
        }
        mysqli_free_result($selectedResult);
    }
}


// Rubric Info
$rubricID = isset($assignmentRow['rubricID']) ? $assignmentRow['rubricID'] : null;
$rubricTitle = '';
$rubricPoints = 0;
$criteriaList = [];
$levelsByCriterion = [];

if (!empty($rubricID)) {
    // Get rubric title and total points
    $rubricQuery = "SELECT rubricTitle, totalPoints FROM rubric WHERE rubricID = $rubricID LIMIT 1";
    $rubricResult = executeQuery($rubricQuery);
    $rubricRow = mysqli_fetch_assoc($rubricResult);

    if ($rubricRow) {
        $rubricTitle = $rubricRow['rubricTitle'];
        $rubricPoints = $rubricRow['totalPoints'];
    }

    // Get criteria for this rubric
    $criteriaQuery = "SELECT criterionID, criteriaTitle FROM criteria WHERE rubricID = $rubricID ORDER BY criterionID ASC";
    $criteriaResult = executeQuery($criteriaQuery);
    while ($criterion = mysqli_fetch_assoc($criteriaResult)) {
        $criteriaList[] = $criterion;

        // Get levels for this criterion
        $criterionID = $criterion['criterionID'];
        $levelsQuery = "SELECT levelID, levelTitle, levelDescription, points 
                        FROM level 
                        WHERE criterionID = $criterionID 
                        ORDER BY points DESC";
        $levelsResult = executeQuery($levelsQuery);
        $levelsByCriterion[$criterionID] = [];
        while ($level = mysqli_fetch_assoc($levelsResult)) {
            $levelsByCriterion[$criterionID][] = $level;
        }
    }
}

$score = null;
$feedback = null;

if (!empty($scoreID)) {
    $scoreQuery = "
        SELECT score, feedback
        FROM scores
        WHERE scoreID = $scoreID
        LIMIT 1
    ";
    $scoreResult = executeQuery($scoreQuery);
    if ($scoreResult && mysqli_num_rows($scoreResult) > 0) {
        $scoreRow = mysqli_fetch_assoc($scoreResult);
        $score = $scoreRow['score'];
        $feedback = $scoreRow['feedback'];
    }
}

// Get todo status for this assignment
$statusQuery = "
    SELECT status 
    FROM todo 
    WHERE userID = $userID 
      AND assessmentID = $assignmentID
    LIMIT 1
";
$statusResult = executeQuery($statusQuery);
$statusRow = mysqli_fetch_assoc($statusResult);
if ($statusRow && isset($statusRow['status'])) {
    $status = $statusRow['status'];
} else {
    $status = 'Pending';
}

// File query (attachments + links)
$filesQuery = "SELECT * FROM files WHERE assignmentID = $assignmentID";
$filesResult = executeQuery($filesQuery);

// Task Materials (Instructor-provided files/links)
$taskFilesArray = [];
$taskLinksArray = [];

$taskFilesQuery = "
    SELECT fileAttachment, fileLink, fileTitle 
    FROM files 
    WHERE assignmentID = $assignmentID
";
$taskFilesResult = executeQuery($taskFilesQuery);

while ($file = mysqli_fetch_assoc($taskFilesResult)) {
    if (!empty($file['fileAttachment'])) {
        $taskFilesArray[] = $file['fileAttachment'];
    }
    if (!empty($file['fileLink'])) {
        $taskLinksArray[] = [
            'link' => $file['fileLink'],
            'title' => $file['fileTitle'] ?? $file['fileLink']
        ];
    }
}

// --- Fetch existing uploads for this submission ---
$attachmentsArray = [];
$linksArray = [];

if ($submissionID) {
    $filesQuery = "
        SELECT fileAttachment, fileLink, fileTitle 
        FROM files 
        WHERE submissionID = $submissionID
    ";
    $filesResult = executeQuery($filesQuery);

    while ($file = mysqli_fetch_assoc($filesResult)) {
        if (!empty($file['fileAttachment'])) {
            $attachmentsArray[] = $file['fileAttachment'];
        }
        if (!empty($file['fileLink'])) {
            $linksArray[] = [
                'link' => $file['fileLink'],
                'title' => $file['fileTitle'] ?? $file['fileLink']
            ];
        }
    }
}
// --- Fetch Badges for this assignment and student ---
$badges = [];

$badgeQuery = "
    SELECT DISTINCT badges.badgeID, badges.badgeName, badges.badgeIcon
    FROM studentbadges
    INNER JOIN badges 
        ON studentbadges.badgeID = badges.badgeID
    WHERE studentbadges.userID = $userID
      AND studentbadges.assignmentID = $assignmentID
    ORDER BY studentbadges.studentBadgeID DESC
    LIMIT 2
";

$badgeResult = executeQuery($badgeQuery);

while ($row = mysqli_fetch_assoc($badgeResult)) {
    $badges[] = $row;
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Assignment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/assignment.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="shared/assets/img/webstar-icon.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-0 p-md-3"
        style="background-color: var(--black);">

        <div class="row w-100">

            <!-- Sidebar (only shows on mobile) -->
            <?php include 'shared/components/sidebar-for-mobile.php'; ?>

            <!-- Sidebar Column (fixed on desktop) -->
            <?php include 'shared/components/sidebar-for-desktop.php'; ?>

            <!-- Main Container Column-->
            <div class="col main-container m-0 p-0 mx-0 mx-md-2 p-0 p-md-4 overflow-y-auto">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">

                    <!-- Navbar for mobile -->
                    <?php include 'shared/components/navbar-for-mobile.php'; ?>

                    <div class="container-fluid py-3 overflow-y-auto row-padding-top">
                        <div class="row mb-3">
                            <div class="col-12 cardHeader p-3 mb-4">

                                <!-- DESKTOP VIEW -->
                                <div class="row desktop-header d-none d-sm-flex">
                                    <div class="col-auto me-2">
                                        <a href="todo.php" class="text-decoration-none">
                                            <i class="fa-solid fa-arrow-left text-reg text-16"
                                                style="color: var(--black);"></i>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <span class="text-sbold text-25"><?php echo $assignmentTitle ?></span>
                                        <div class="text-reg text-18">Due
                                            <?php echo date("M d, Y", strtotime($deadline)); ?>
                                        </div>
                                    </div>
                                    <div class="col-auto text-end">

                                        <div style="display: flex; align-items: center; gap: 12px;">

                                            <!-- LEFT: BADGES -->
                                            <div class="me-2">
                                                <?php if (!empty($badges)): ?>
                                                    <?php foreach ($badges as $b): ?>
                                                        <img src="shared/assets/img/badge/<?php echo $b['badgeIcon']; ?>"
                                                            alt="<?php echo $b['badgeName']; ?>" width="50" height="50"
                                                            class="me-1" data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="<?php echo $b['badgeName']; ?>" style="cursor:pointer;">
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>

                                            <!-- RIGHT: STATUS + SCORE -->
                                            <div class="text-end">
                                                <?php echo $status; ?>
                                                <div class="text-sbold text-25">
                                                    <?php echo $score !== null ? $score : '-'; ?>
                                                    <span class="text-muted">/<?php echo $totalPoints; ?></span>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                </div>

                                <!-- MOBILE VIEW -->
                                <div class="d-block d-sm-none mobile-assignment">

                                    <!-- Top row: back arrow + title -->
                                    <div class="mobile-top">
                                        <div class="arrow">
                                            <a href="todo.php" class="text-decoration-none">
                                                <i class="fa-solid fa-arrow-left text-reg text-16"
                                                    style="color: var(--black);"></i>
                                            </a>
                                        </div>
                                        <div class="title text-sbold text-25"><?php echo $assignmentTitle ?></div>
                                    </div>

                                    <!-- Due date -->
                                    <div class="due text-reg text-18">Due
                                        <?php echo date("M d, Y", strtotime($deadline)); ?>
                                    </div>

                                    <!-- Badges on top -->
                                    <div class="d-flex justify-content-center align-items-center gap-2 mt-3 mb-2">
                                        <?php if (!empty($badges)): ?>
                                            <?php foreach ($badges as $b): ?>
                                                <img src="shared/assets/img/badge/<?php echo $b['badgeIcon']; ?>"
                                                    alt="<?php echo $b['badgeName']; ?>" width="40" height="40"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="<?php echo $b['badgeName']; ?>" style="cursor:pointer;">
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Status + Score below badges -->
                                    <div>
                                        <div class="graded text-reg text-18"><?php echo $status; ?></div>
                                        <div class="score text-sbold text-25">
                                            <?php echo $score !== null ? $score : '-'; ?>
                                            <span class="text-muted">/<?php echo $totalPoints; ?></span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Left Content -->
                            <div class="col-12 col-lg-8">
                                <div class="p-0 px-lg-5">
                                    <div class="text-sbold text-14 mt-3">Instructions</div>
                                    <p class="mb-3 mt-3 text-med text-14"><?php echo nl2br($assignmentDescription) ?>
                                    </p>
                                    
                                    <?php if (!empty($taskFilesArray) || !empty($taskLinksArray)): ?>
                                        <hr>
                                        <div class="text-sbold text-14 mt-4">Task Materials</div>

                                        <?php foreach ($taskFilesArray as $file):
                                            $filePath = "shared/assets/files/" . $file;
                                            $fileExt = strtoupper(pathinfo($file, PATHINFO_EXTENSION));
                                            $fileSize = (file_exists($filePath)) ? filesize($filePath) : 0;
                                            $fileSizeMB = $fileSize > 0 ? round($fileSize / 1048576, 2) . " MB" : "Unknown size";
                                            $fileNameOnly = pathinfo($file, PATHINFO_FILENAME);
                                            ?>
                                            <div class="rubricCard my-3 py-1 d-flex align-items-start justify-content-between"
                                                style="max-width:100%; min-width:310px; cursor:pointer;">
                                                <div class="d-flex align-items-start">
                                                    <span class="material-symbols-outlined p-2 pe-2 mt-1 ms-1"
                                                        style="font-variation-settings:'FILL' 1;">draft</span>
                                                    <div class="ms-2">
                                                        <div class="text-sbold text-16 mt-1"><?php echo $fileNameOnly ?></div>
                                                        <div class="due text-reg text-14 mb-1"><?php echo $fileExt ?> ·
                                                            <?php echo $fileSizeMB ?></div>
                                                    </div>
                                                </div>
                                                <a href="<?php echo $filePath ?>" download="<?php echo $file ?>"
                                                    class="text-dark d-flex align-items-center" style="text-decoration: none;">
                                                    <span class="material-symbols-outlined p-2 pe-2 mt-1 me-3"
                                                        style="font-variation-settings:'FILL' 1;">download_2</span>
                                                </a>
                                            </div>
                                        <?php endforeach; ?>

                                        <?php foreach ($taskLinksArray as $item): ?>
                                            <div class="rubricCard my-3 py-1 w-lg-25 d-flex align-items-start"
                                                style="max-width:100%; min-width:310px;">
                                                <span class="material-symbols-outlined p-2 pe-2 mt-1"
                                                    style="font-variation-settings:'FILL' 1;">link</span>
                                                <div class="ms-2" style="flex: 1 1 0; min-width: 0;">
                                                    <div class="text-sbold text-16 mt-1"><?php echo $item['title'] ?></div>
                                                    <div class="text-reg link text-12 mt-0"
                                                        style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                        <a href="<?php echo $item['link'] ?>" target="_blank"
                                                            rel="noopener noreferrer"
                                                            style="text-decoration: none; color: var(--black); display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                            <?php echo $item['link'] ?>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                    <?php if (!empty($rubricID) && !empty($rubricTitle)): ?>
                                        <hr>
                                        <div class="text-sbold text-14 mt-4">Rubric</div>
                                        <div class="rubricCard my-3 w-lg-25 d-flex align-items-start"
                                            style="max-width:100%; min-width:310px; cursor:pointer;" data-bs-toggle="modal"
                                            data-bs-target="#rubricModal">

                                            <span class="material-symbols-outlined ps-3 pe-2 py-3"
                                                style="font-variation-settings:'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 48;">
                                                rate_review
                                            </span>

                                            <div class="ms-2">
                                                <div class="text-sbold text-16 mt-1"><?php echo $rubricTitle; ?></div>
                                                <div class="due text-reg text-14 mb-1"><?php echo $rubricPoints; ?> points
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($feedback)): ?>
                                        <hr>
                                        <div class="text-sbold text-14 pb-3">Feedback from Instructor</div>
                                        <div class="d-flex align-items-center pb-2">
                                            <div class="feedback text-14 text-med">
                                                <?php echo $feedback; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <hr>

                                    <div class="text-sbold text-14 pb-3">Prepared by</div>
                                    <div class="d-flex align-items-center pb-3">
                                        <div class="rounded-circle me-2"
                                            style="width: 50px; height: 50px; background-color: var(--highlight75);">
                                            <img src="shared/assets/pfp-uploads/<?php echo $profProfile ?>"
                                                alt="professor" class="rounded-circle" style="width:50px;height:50px;">
                                        </div>
                                        <div>
                                            <div class="text-sbold text-14"><?php echo $profName ?></div>
                                            <div class="text-med text-12">January 12, 2024 8:00AM</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-lg-4">
                                <!-- Sticky Container for Card + Button -->
                                <div class="position-sticky" style="top: 20px;">

                                    <!-- Sticky Card -->
                                    <div class="cardSticky" id="stickyCard">
                                        <div class="px-2 pt-2">
                                            <!-- My Work Section -->
                                            <div class="myWorkContainer mb-3"
                                                style="<?= (!empty($attachmentsArray) || !empty($linksArray)) ? 'display:block;' : 'display:none;' ?>">
                                                <div class="text-sbold text-16 mb-2" id="myWorkLabel"
                                                    style="<?= (empty($attachmentsArray) && empty($linksArray)) ? 'display:none;' : 'display:block;' ?>">
                                                    My Work
                                                </div>

                                                <div class="existingUploads">

                                                    <!-- Attachments -->
                                                    <?php foreach ($attachmentsArray as $file): ?>
                                                        <div class="cardFile text-sbold text-16 my-2 d-flex align-items-center justify-content-between"
                                                            data-type="file">
                                                            <div class="d-flex align-items-center"
                                                                style="flex: 1 1 0; min-width: 0;">
                                                                <span class="material-symbols-outlined p-2 pe-2"
                                                                    style="font-variation-settings:'FILL' 1;">draft</span>
                                                                <a href="shared/assets/img/files/<?= rawurlencode($file) ?>"
                                                                    target="_blank"
                                                                    class="ms-2 text-decoration-none text-dark"
                                                                    style="flex-shrink: 1; min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                                    <?= $file ?>
                                                                </a>
                                                            </div>
                                                            <?php if ($isSubmitted == 0): ?>
                                                                <button type="button"
                                                                    class="border-0 bg-transparent mt-2 remove-existing-file"
                                                                    data-filename="<?= addslashes($file) ?>"
                                                                    aria-label="Remove">
                                                                    <span class="material-symbols-outlined">close</span>
                                                                </button>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endforeach; ?>

                                                    <!-- Links -->
                                                    <?php foreach ($linksArray as $link): ?>
                                                        <div class="cardFile text-sbold text-16 my-2 d-flex align-items-center justify-content-between"
                                                            data-type="link" data-link="<?= $link['link'] ?>">
                                                            <div class="d-flex align-items-center"
                                                                style="flex: 1 1 0; min-width: 0;">
                                                                <span class="material-symbols-outlined p-2 pe-2"
                                                                    style="font-variation-settings:'FILL' 1;">link</span>
                                                                <a href="<?= $link['link'] ?>" target="_blank"
                                                                    class="ms-2 text-decoration-none text-dark"
                                                                    style="flex-shrink: 1; min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                                    <?= $link['title'] ?>
                                                                </a>
                                                            </div>
                                                            <?php if ($isSubmitted == 0): ?>
                                                                <button type="button"
                                                                    class="border-0 bg-transparent mt-2 remove-existing-file"
                                                                    data-link="<?= $link['link'] ?>" aria-label="Remove">
                                                                    <span class="material-symbols-outlined">close</span>
                                                                </button>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>

                                                <!-- Container for newly added uploads via JS -->
                                                <div class="uploadedFiles"></div>
                                            </div>

                                            <!-- TIMELINE SECTION -->
                                            <div id="timelineContainer">
                                                <div class="text-sbold text-16 mb-4">Status</div>
                                                <ul class="timeline list-unstyled small">
                                                    <li class="timeline-item">
                                                        <div class="timeline-circle bg-dark"></div>
                                                        <div class="timeline-content">
                                                            <div class="text-reg text-16">Assignment is ready to work
                                                                on.</div>
                                                            <div class="text-reg text-12">
                                                                <?= date("M j, Y, g:iA", strtotime($assignmentCreated)); ?>
                                                            </div>
                                                        </div>
                                                    </li>

                                                    <?php if ($isSubmitted && !$isGraded && $isBeforeDeadline): ?>
                                                        <li class="timeline-item">
                                                            <div class="timeline-circle big"
                                                                style="background-color: var(--primaryColor);"></div>
                                                            <div class="timeline-content">
                                                                <div class="text-reg text-16">Your assignment has been
                                                                    submitted.</div>
                                                                <div class="text-reg text-12">
                                                                    <?= date("M j, Y, g:iA", strtotime($submissionDate)); ?>
                                                                </div>
                                                            </div>
                                                        </li>

                                                    <?php elseif ($isLate && !$isGraded): ?>
                                                        <li class="timeline-item">
                                                            <div class="timeline-circle big" style="background-color: red;">
                                                            </div>
                                                            <div class="timeline-content">
                                                                <div class="text-reg text-16">Assignment has been submitted
                                                                    late.</div>
                                                                <div class="text-reg text-12">
                                                                    <?= date("M j, Y, g:iA", strtotime($submissionDate)); ?>
                                                                </div>
                                                            </div>
                                                        </li>

                                                    <?php elseif ($isMissing): ?>
                                                        <li class="timeline-item">
                                                            <div class="timeline-circle big" style="background-color: red;">
                                                            </div>
                                                            <div class="timeline-content">
                                                                <div class="text-reg text-16">This task is missing.</div>
                                                                <div class="text-reg text-12">
                                                                    <?= date("M j, Y, g:iA", strtotime($deadline)); ?>
                                                                </div>
                                                            </div>
                                                        </li>

                                                    <?php elseif ($isGraded): ?>
                                                        <li class="timeline-item">
                                                            <div class="timeline-circle bg-dark"></div>
                                                            <div class="timeline-content">
                                                                <div class="text-reg text-16">
                                                                    <?= $isLate ? "Assignment has been submitted late." : "Your assignment has been submitted."; ?>
                                                                </div>
                                                                <div class="text-reg text-12">
                                                                    <?= date("M j, Y, g:iA", strtotime($submissionDate)); ?>
                                                                </div>
                                                            </div>
                                                        </li>

                                                        <li class="timeline-item">
                                                            <div class="timeline-circle big"
                                                                style="background-color: var(--primaryColor);"></div>
                                                            <div class="timeline-content">
                                                                <div class="text-reg text-16">Your assignment has been
                                                                    graded.</div>
                                                                <div class="text-reg text-12">
                                                                    <?= date("M j, Y, g:iA", strtotime($statusUpdated)); ?>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>

                                            <!-- Upload / Link Buttons -->
                                            <div class="mt-0 d-flex flex-column align-items-center">
                                                <input type="file" name="fileAttachment[]" class="d-none"
                                                    id="fileUpload" accept=".pdf, .jpg, .jpeg, .png" multiple>

                                                <div class="d-flex gap-2 mb-3">
                                                    <?php if ($isSubmitted == 0 && !$lockSubmission): ?>
                                                        <button type="button" id="uploadBtn"
                                                            class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14"
                                                            style="border: 1px solid var(--black);"
                                                            onclick="document.getElementById('fileUpload').click();">
                                                            <div class="d-flex align-items-center gap-1">
                                                                <span class="material-symbols-outlined"
                                                                    style="font-size:20px">upload</span>
                                                                <span>File</span>
                                                            </div>
                                                        </button>

                                                        <button type="button" id="linkBtn"
                                                            class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14"
                                                            style="border: 1px solid var(--black);" data-bs-toggle="modal"
                                                            data-bs-target="#linkModal">
                                                            <div class="d-flex align-items-center gap-1">
                                                                <span class="material-symbols-rounded"
                                                                    style="font-size:20px">link</span>
                                                                <span>Link</span>
                                                            </div>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>

                                                <?php if ($isSubmitted == 0 && !$lockSubmission): ?>
                                                    <button type="button" id="turnInBtn"
                                                        class="btn px-4 mb-4 text-reg text-md-14 rounded-4 w-75"
                                                        style="background-color: var(--primaryColor);"
                                                        data-bs-toggle="modal" data-bs-target="#turnInModal">
                                                        Turn In
                                                    </button>

                                                <?php elseif ($isSubmitted == 1 && !$isGraded): ?>
                                                    <button type="button" id="unsubmitBtn"
                                                        class="btn btn-sm px-4 py-2 mb-4 rounded-pill text-reg text-md-14"
                                                        style="background-color: var(--primaryColor); margin-top: -25px;"
                                                        <?php if ($userWebstars >= 50): ?> data-bs-toggle="modal"
                                                            data-bs-target="#unsubmitModal" <?php endif; ?>>
                                                        Unsubmit
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- View Attachment Guidelines Button -->
                                    <div class="mt-3 d-flex justify-content-center w-100">
                                        <button type="button"
                                            class="btn text-reg text-12 rounded-4 d-flex justify-content-center align-items-center"
                                            data-bs-toggle="modal" data-bs-target="#guidelinesModal">
                                            <span class="material-symbols-outlined me-1"
                                                style="font-variation-settings:'FILL' 1;">info</span>
                                            View attachment guidelines
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Turn In Modal -->
    <div class="modal fade" id="turnInModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered py-4 style=" max-width: 500px;">
            <div class="modal-content rounded-4">

                <div class="modal-header border-bottom">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <p class="text-bold text-20 align-items-center text-center">Turn in this task?</p>
                    <p class="text-reg text-14 align-items-center text-center justify-content-center">You can still
                        edit
                        it before the deadline — but be careful! Unsubmitting will cost you webstars.</p>
                </div>

                <form id="turnInForm" action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="assessmentID" value="<?= $assessmentID ?>">
                    <input type="file" name="fileAttachment[]" id="fileUploadHidden" class="d-none" multiple>
                    <input type="hidden" name="links" id="linksInput" value="">
                    <input type="hidden" name="deletedFiles" id="deletedFiles">

                    <div class="modal-footer border-top">
                        <button type="submit" name="turnIn" class="btn rounded-5 px-4 text-sbold text-14 me-1"
                            style="background-color: var(--primaryColor); border: 1px solid var(--black);">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Submitted Modal -->
    <div class="modal fade" id="submittedModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered py-4" style="max-width: 500px;">
            <div class="modal-content rounded-4">
                <div class="modal-header border-bottom">
                    <div class="modal-title text-sbold text-20 ms-3" id="submittedModalLabel">Well Done!</div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-5 text-center">
                    <img src="shared/assets/img/wellDone.png" alt="Illustration" class="img-fluid mb-3"
                        style="max-height: 200px; margin-top: -30px;">

                    <p class="text-med text-12 mb-2 px-5" style="margin-top: -30px;">
                        Your task has been successfully submitted. Keep up the great work!
                    </p>

                    <!-- XP Section -->
                    <div class="d-flex align-items-center justify-content-center mb-1">
                        <img src="shared/assets/img/xp.png" alt="XP Icon" style="width: 20px; height: 20px;">
                        <p class="text-sbold text-14 mb-0 ms-2">
                            +<?= $baseXP ?> XPs
                            <span class="text-12 text-muted">
                                +<?= $bonusXP ?> Bonus XPs
                            </span>
                        </p>
                    </div>

                    <!-- Webstar Section -->
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <img src="shared/assets/img/webstar.png" alt="Webstar Icon" style="width: 20px; height: 20px;">
                        <p class="text-sbold text-14 mb-0 ms-2">
                            +<?= $baseWebstars ?> Webstars
                            <span class="text-12 text-muted">
                                +<?= $bonusWebstars ?> Bonus Webstars
                            </span>
                        </p>
                    </div>
                </div>
                <div class="modal-footer border-top py-4">
                </div>
            </div>
        </div>
    </div>

    <!-- Unsubmit Modal -->
    <div class="modal fade" id="unsubmitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered py-4" style="max-width: 500px;">
            <div class="modal-content rounded-4">

                <div class="modal-header border-bottom">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <p class="text-bold text-20 text-center">Unsubmit this task?</p>
                    <p class="text-reg text-14 text-center">
                        If you unsubmit, your work will return to pending status.<br>
                        Unsubmitting will also cost you <b>50 Webstars</b>.
                    </p>
                    <p class="text-reg text-14 text-center">Are you sure you want to unsubmit this task?</p>
                </div>

                <form id="unsubmitForm" action="" method="POST">
                    <div class="modal-footer border-top d-flex justify-content-center gap-2">
                        <button type="submit" id="confirmUnsubmitBtn" name="unsubmit" class="btn rounded-5 px-4 text-sbold text-14 me-1"
                            style="background-color: var(--primaryColor); border: 1px solid var(--black);">
                            Unsubmit
                        </button>

                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Link Modal -->
    <div class="modal fade" id="linkModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered py-4" style="max-width: 400px;">
            <div class="modal-content rounded-4">

                <!-- HEADER -->
                <div class="modal-header border-bottom">
                    <div class="modal-title text-sbold text-20 ms-1">
                        Add Link
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="LinkForm" action="" method="POST">
                    <div class="modal-body pb-2">
                        <input type="url" name="link" id="linkInput" class="form-control" placeholder="Link" required
                            style="border: 1px solid var(--black); border-radius: 8px; padding: 8px;">
                    </div>

                    <!-- FOOTER -->
                    <div class="modal-footer border-top">
                        <button type="button" id="addLinkBtn" class="btn rounded-5 px-4 text-sbold text-14 me-1"
                            style="background-color: var(--primaryColor); border: 1px solid var(--black);">
                            Add Link
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Guidelines Modal -->
    <div class="modal fade" id="guidelinesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered py-4" style="max-width: 500px;">
            <div class="modal-content rounded-4">

                <div class="modal-header border-bottom">
                    <div class="modal-title text-sbold text-20 ms-3" id="guidelinesModalLabel">Attachment Guidelines
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-3 mt-3">
                    <ul class="text-reg text-12">
                        <li>We only accept PDF files and image formats (JPG, PNG) below 25 MB.</li>
                        <li>You may also submit Google Docs or Google Slides links.</li>
                        <li>For files larger than 25 MB or outside the accepted formats, please upload them to a
                            third-party drive (e.g., Google Drive, OneDrive, Dropbox) and share the link instead.
                        </li>
                    </ul>
                </div>

                <div class="modal-footer border-top py-4">
                </div>
            </div>
        </div>
    </div>

    <!-- Rubric Modal -->
    <div class="modal fade" id="rubricModal" tabindex="-1" aria-labelledby="rubricModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered py-4">
            <div class="modal-content rounded-4" style="max-height:450px; overflow:hidden;">

                <!-- HEADER -->
                <div class="modal-header border-bottom">
                    <div class="modal-title text-sbold text-20 ms-3" id="rubricModalLabel">
                        <?= $rubricTitle; ?>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- BODY -->
                <div class="modal-body" style="overflow-y:auto; scrollbar-width:thin;">
                    <div class="container text-center px-5">
                        <?php foreach ($criteriaList as $criterionIndex => $criterion): ?>
                            <!-- Criterion Title -->
                            <div class="row mb-3">
                                <div class="col">
                                    <div class="text-sbold text-15" style="color: var(--black);">
                                        <?= $criterion['criteriaTitle']; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Accordion for Levels -->
                            <div id="ratingAccordion<?= $criterionIndex; ?>" class="row justify-content-center">
                                <div class="col-12 col-md-10">
                                    <?php foreach ($levelsByCriterion[$criterion['criterionID']] as $levelIndex => $level):
                                        $collapseID = strtolower(preg_replace('/\s+/', '', $level['levelTitle'])) . $criterionIndex;

                                        // Check if this level is selected
                                        $isSelected = in_array($level['levelID'], $selectedLevels);
                                        $bgColor = $isSelected ? 'var(--primaryColor)' : 'var(--pureWhite)';
                                        $borderColor = $isSelected ? 'var(--black)' : 'var(--black)';
                                        ?>
                                        <div class="mb-2">
                                            <div class="w-100 d-flex flex-column text-med text-14"
                                                style="background-color: <?= $bgColor ?>; color: <?= $textColor ?>; border-radius: 10px; border: 1px solid <?= $borderColor ?>; transition: 0.3s; overflow: hidden;">

                                                <!-- Header -->
                                                <div class="d-flex justify-content-between align-items-center w-100 px-3 py-2">
                                                    <span class="flex-grow-1 text-center ps-3">
                                                        <?= $level['levelTitle']; ?> · <?= $level['points']; ?> pts
                                                    </span>
                                                    <span class="material-symbols-rounded collapse-toggle"
                                                        data-bs-toggle="collapse" data-bs-target="#<?= $collapseID; ?>"
                                                        aria-expanded="false" aria-controls="<?= $collapseID; ?>"
                                                        style="cursor:pointer;">
                                                        expand_more
                                                    </span>
                                                </div>

                                                <!-- Collapse Content -->
                                                <div class="collapse w-100" id="<?= $collapseID; ?>"
                                                    data-bs-parent="#ratingAccordion<?= $criterionIndex; ?>">
                                                    <p class="mb-0 px-3 pb-2 text-reg text-14"
                                                        style="text-align: justify; background-color: <?= $bgColor ?>; color: <?= $textColor ?>;">
                                                        <?= $level['levelDescription']; ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer">
                    <div class="container">
                        <div class="row justify-content-end py-2">
                            <!-- Optional footer buttons -->
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toastContainer" class="position-absolute d-flex flex-column align-items-center"
        style="top: 3rem; left: 25%; transform: translateX(80%); z-index:1100; pointer-events:none;">
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- Toast for unsubmit with less than 50 webstars ---
            const userWebstars = <?= $userWebstars ?? 0 ?>;
            const requiredWebstars = 50;
            const unsubmitBtn = document.getElementById('unsubmitBtn');

            if (unsubmitBtn && userWebstars < requiredWebstars) {
                unsubmitBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    showToast(`You need at least ${requiredWebstars} Webstars to unsubmit.`);
                });
            }

            // --- Updated showToast with inline alert-style ---
            function showToast(message) {
                const container = document.getElementById('toastContainer');
                if (!container) return;

                const alert = document.createElement('div');
                alert.setAttribute('role', 'alert');

                // Use Bootstrap classes + your font classes
                alert.className = "alert alert-danger fade show mb-2 shadow-lg text-med text-12 d-flex align-items-center justify-content-center gap-2 px-3 py-2";
                alert.innerHTML = `
                <i class="bi bi-x-circle-fill me-2 fs-6"></i>
                <span>${message}</span>`;

                container.appendChild(alert);

                // Auto remove after 3 seconds
                setTimeout(() => {
                    alert.classList.remove("show");
                    alert.classList.add("fade");
                    setTimeout(() => alert.remove(), 500);
                }, 3000);
            }

            // --- Success toast for downloads ---
            document.querySelectorAll('a[download]').forEach(downloadLink => {
                downloadLink.addEventListener('click', function (e) {
                    const fileName = this.getAttribute('download');
                    showDownloadToast(`"${fileName}" downloaded successfully!`);
                });
            });

            function showDownloadToast(message) {
                const container = document.getElementById('toastContainer');
                if (!container) return;

                const alert = document.createElement('div');
                alert.setAttribute('role', 'alert');
                alert.className = "alert alert-success fade show mb-2 shadow-lg text-med text-12 d-flex align-items-center justify-content-center gap-2 px-3 py-2";
                alert.innerHTML = `
                    <i class="bi bi-check-circle-fill me-2 fs-6"></i>
                    <span>${message}</span>`;

                container.appendChild(alert);

                setTimeout(() => {
                    alert.classList.remove("show");
                    alert.classList.add("fade");
                    setTimeout(() => alert.remove(), 500);
                }, 3000);
            }

            // --- Show Submitted Modal on initial submission ---
            const showSubmittedModal = <?= $showSubmittedModal ? 'true' : 'false' ?>;
            const submissionID = <?= $submissionID ?? 0 ?>;

            if (showSubmittedModal && submissionID) {
                const submittedModalEl = document.getElementById('submittedModal');
                const submittedModal = new bootstrap.Modal(submittedModalEl);
                submittedModal.show();

                // Trigger confetti when modal is shown
                submittedModalEl.addEventListener('shown.bs.modal', () => {
                    // Create a canvas element on top of modal
                    const canvas = document.createElement('canvas');
                    canvas.style.position = 'fixed';
                    canvas.style.top = '0';
                    canvas.style.left = '0';
                    canvas.style.width = '100%';
                    canvas.style.height = '100%';
                    canvas.style.zIndex = '2000'; // higher than modal
                    canvas.style.pointerEvents = 'none';
                    document.body.appendChild(canvas);

                    const myConfetti = confetti.create(canvas, {
                        resize: true,
                        useWorker: true
                    });

                    // Initial burst
                    myConfetti({
                        particleCount: 200,
                        spread: 100,
                        origin: { y: 0.6 }
                    });

                    // Continuous confetti for 5 seconds
                    const duration = 5000; // 5 seconds
                    const animationEnd = Date.now() + duration;
                    const colors = [
                        '#ff0a54', '#ff477e', '#ff7096', '#ff85a1', '#fbb1b9',
                        '#00f5d4', '#00bfa6', '#f9c74f', '#f9844a', '#90be6d',
                        '#577590', '#f72585', '#7209b7', '#3a0ca3'
                    ];

                    (function frame() {
                        myConfetti({
                            particleCount: 5,
                            angle: 60,
                            spread: 70,
                            origin: { x: 0, y: 0.6 },
                            colors: colors
                        });
                        myConfetti({
                            particleCount: 5,
                            angle: 120,
                            spread: 70,
                            origin: { x: 1, y: 0.6 },
                            colors: colors
                        });

                        if (Date.now() < animationEnd) {
                            requestAnimationFrame(frame);
                        }
                    })();

                    // Remove canvas after animation
                    setTimeout(() => {
                        canvas.remove();
                    }, duration + 500);
                });

                submittedModalEl.addEventListener('hidden.bs.modal', function () {
                    fetch('', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'updateModalShown=1&submissionID=' + submissionID
                    }).then(res => res.text()).then(console.log);
                });
            }

            // --- My Work + File Handling ---
            const allSelectedFiles = new DataTransfer();
            const buttons = document.querySelectorAll('[data-bs-toggle="collapse"]');
            const fileUpload = document.getElementById('fileUpload');
            const myWorkContainer = document.querySelector('.myWorkContainer');
            const workName = document.getElementById('workName');

            const icons = document.querySelectorAll('.collapse-toggle');

            icons.forEach(icon => {
                try {
                    const target = icon.getAttribute('data-bs-target');
                    if (!target) return; // Skip if no target

                    const collapse = document.querySelector(target);
                    const container = icon.closest('div.d-flex.justify-content-between');

                    if (!collapse || !container) return; // Skip if target or container missing

                    // Handle expand
                    collapse.addEventListener('show.bs.collapse', () => {
                        document.querySelectorAll('.material-symbols-rounded')
                            .forEach(ic => ic.style.transform = 'rotate(0deg)');
                        icon.style.transform = 'rotate(180deg)';
                        icon.style.transition = 'transform 0.3s';
                        container.parentElement.style.backgroundColor = 'var(--primaryColor)';
                    });

                    // Handle collapse
                    collapse.addEventListener('hide.bs.collapse', () => {
                        icon.style.transform = 'rotate(0deg)';
                        container.parentElement.style.backgroundColor = 'var(--pureWhite)';
                    });

                } catch (e) {
                    console.error('Collapse toggle error for icon:', icon, e);
                }
            });

            // --- My Work visibility + multiple file handling ---
            if (fileUpload && myWorkContainer) {
                const MAX_TOTAL_SIZE = 25 * 1024 * 1024; // 25 MB

                function getTotalFileSize() {
                    return Array.from(document.querySelectorAll('.cardFile')).reduce((total, card) => {
                        return total + parseInt(card.dataset.size || 0);
                    }, 0);
                }

                const turnInModal = document.getElementById('turnInModal');
                if (turnInModal) {
                    turnInModal.addEventListener('show.bs.modal', function (e) {
                        if (getTotalFileSize() > MAX_TOTAL_SIZE) {
                            e.preventDefault();
                            showToast('Total file size exceeds 25 MB. Please remove some files.', 'bg-danger');
                        }
                    });
                }

                function hasUploadedWork() {
                    const hasFile = document.querySelectorAll('.cardFile').length > 0;
                    const hasLink = workName && workName.textContent.trim() !== '' && workName.textContent.trim() !== 'Submission';
                    return hasFile || hasLink;
                }

                if (turnInModal) {
                    turnInModal.addEventListener('show.bs.modal', function (e) {
                        if (!hasUploadedWork()) {
                            e.preventDefault();
                            showToast('Please upload a file or add a link before turning in.', 'bg-danger');
                        }
                    });
                }

                const workList = document.querySelector('.uploadedFiles');

                // --- Handle file selection ---
                fileUpload.addEventListener('change', function () {
                    const newFiles = Array.from(this.files);
                    newFiles.forEach(file => allSelectedFiles.items.add(file));
                    this.value = '';

                    myWorkContainer.style.display = 'block';
                    document.getElementById('myWorkLabel').style.display = 'block';

                    newFiles.forEach(file => {
                        const cardHTML = `
                    <div class="cardFile text-sbold text-16 my-2 d-flex align-items-center justify-content-between"
                        data-size="${file.size}" data-type="file">
                        <div class="d-flex align-items-center">
                            <span class="material-symbols-outlined p-2 pe-2" style="font-variation-settings:'FILL' 1">draft</span>
                            <span class="ms-2">${file.name}</span>
                        </div>
                        <button type="button" class="border-0 bg-transparent mt-2 remove-btn">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                `;
                        workList.insertAdjacentHTML('beforeend', cardHTML);
                    });

                    // Bind remove buttons
                    workList.querySelectorAll('.remove-btn').forEach(btn => {
                        if (!btn.dataset.bound) {
                            btn.dataset.bound = 'true';
                            btn.addEventListener('click', () => {
                                const card = btn.closest('.cardFile');
                                const fileName = card.querySelector('span.ms-2').textContent;

                                for (let i = 0; i < allSelectedFiles.items.length; i++) {
                                    if (allSelectedFiles.items[i].getAsFile().name === fileName) {
                                        allSelectedFiles.items.remove(i);
                                        break;
                                    }
                                }
                                card.remove();

                                const hasExisting = document.querySelector('.existingUploads .cardFile');
                                const hasNew = document.querySelector('.uploadedFiles .cardFile');
                                if (!hasExisting && !hasNew) myWorkContainer.style.display = 'none';
                            });
                        }
                    });

                    if (<?= $isSubmitted ?> === 1) {
                        workList.querySelectorAll('button').forEach(btn => btn.style.display = 'none');
                    }
                });

                // --- Handle Add Link ---
                const addLinkBtn = document.getElementById('addLinkBtn');
                const linkInput = document.getElementById('linkInput');
                const linksInput = document.getElementById('linksInput');

                addLinkBtn.addEventListener('click', function () {
                    const linkValue = linkInput.value.trim();
                    if (!linkValue) return;

                    let linkTitle;
                    try {
                        const url = new URL(linkValue);
                        linkTitle = url.hostname.replace('www.', '');
                    } catch {
                        linkTitle = linkValue;
                    }

                    myWorkContainer.style.display = 'block';
                    document.getElementById('myWorkLabel').style.display = 'block';

                    const cardHTML = `
                        <div class="cardFile text-sbold text-16 my-2 px-1 d-flex align-items-center justify-content-between"
                            data-link="${linkValue}" data-type="link">
                            <div class="d-flex align-items-center">
                                <span class="material-symbols-outlined">link</span>
                                <a href="${linkValue}" target="_blank" class="ms-2 text-decoration-none text-dark">${linkTitle}</a>
                            </div>
                            <button type="button" class="border-0 bg-transparent mt-2 remove-btn">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                    `;
                    workList.insertAdjacentHTML('beforeend', cardHTML);

                    function updateLinksInput() {
                        const allLinks = Array.from(workList.querySelectorAll('.cardFile'))
                            .filter(c => c.dataset.type === 'link')
                            .map(c => ({
                                link: c.dataset.link,
                                title: c.querySelector('a').textContent
                            }));
                        linksInput.value = JSON.stringify(allLinks);
                    }

                    // Re-bind remove buttons
                    workList.querySelectorAll('.remove-btn').forEach(btn => {
                        if (!btn.dataset.bound) {
                            btn.dataset.bound = 'true';
                            btn.addEventListener('click', () => {
                                const card = btn.closest('.cardFile');
                                card.remove();
                                updateLinksInput();

                                const hasExisting = document.querySelector('.existingUploads .cardFile');
                                const hasNew = document.querySelector('.uploadedFiles .cardFile');
                                if (!hasExisting && !hasNew) myWorkContainer.style.display = 'none';
                            });
                        }
                    });

                    updateLinksInput();
                    linkInput.value = '';
                    const linkModal = bootstrap.Modal.getInstance(document.getElementById('linkModal'));
                    linkModal.hide();

                    if (<?= $isSubmitted ?> === 1) {
                        workList.querySelectorAll('button').forEach(btn => btn.style.display = 'none');
                    }
                });
            }

            // --- Adjust sticky margin ---
            function updateStickyMargin() {
                const card = document.getElementById('stickyCard');
                if (card) {
                    card.style.marginLeft = (window.innerWidth >= 992) ? '-30px' : '0';
                }
            }
            updateStickyMargin();
            window.addEventListener('resize', updateStickyMargin);

            // --- Sync file input with hidden modal input ---
            const fileUploadHidden = document.getElementById('fileUploadHidden');
            const turnInModal = document.getElementById('turnInModal');
            if (turnInModal && fileUpload && fileUploadHidden) {
                turnInModal.addEventListener('show.bs.modal', () => {
                    fileUploadHidden.files = allSelectedFiles.files;
                });
            }

            // --- Handle remove existing files ---
            const filesToDelete = [];
            const deletedFilesInput = document.getElementById('deletedFiles');
            document.querySelectorAll('.remove-existing-file').forEach(btn => {
                btn.addEventListener('click', function () {
                    const fileName = this.dataset.filename;
                    filesToDelete.push(fileName);
                    deletedFilesInput.value = JSON.stringify(filesToDelete);
                    this.closest('.cardFile').remove();

                    const hasExisting = document.querySelector('.existingUploads .cardFile');
                    const hasNew = document.querySelector('.uploadedFiles .cardFile');
                    if (!hasExisting && !hasNew) myWorkContainer.style.display = 'none';
                });
            });
            // --- Enable Bootstrap Tooltips (FOR BADGES) ---
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });

        // --- Remove My Work ---
        function removeMyWork(button) {
            const container = button.closest('.myWorkContainer');
            const fileUpload = document.getElementById('fileUpload');
            const workName = document.getElementById('workName');
            if (container) container.style.display = 'none';
            if (fileUpload) fileUpload.value = '';
            if (workName) workName.textContent = 'Submission';
        }

        // --- Add link handler ---
        function addLinkWork(linkText) {
            const myWorkContainer = document.querySelector('.myWorkContainer');
            const workName = document.getElementById('workName');
            if (myWorkContainer && workName) {
                workName.textContent = linkText;
                myWorkContainer.style.display = 'block';
            }
        }
    </script>
</body>

</html>