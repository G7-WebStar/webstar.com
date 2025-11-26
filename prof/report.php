<?php $activePage = 'report'; ?>
<?php
include('../shared/assets/database/connect.php');
include("../shared/assets/processes/prof-session-process.php");

// --- Get enrollmentID from GET (professor selects a student) ---
$enrollmentID = intval($_GET['enrollmentID'] ?? 0);

// If no enrollmentID or invalid → 404
if ($enrollmentID <= 0) {
    header("Location: 404.php");
    exit;
}

if ($enrollmentID > 0) {
    // Fetch student info based on enrollmentID
    $studentQuery = "
        SELECT 
            users.userName,
            userinfo.firstName,
            userinfo.middleName,
            userinfo.lastName,
            userinfo.studentID,
            userinfo.yearLevel,
            userinfo.yearSection,
            userinfo.profilePicture,
            program.programInitial,
            enrollments.enrollmentID,
            enrollments.userID,
            enrollments.courseID
        FROM enrollments
        INNER JOIN users ON users.userID = enrollments.userID
        INNER JOIN userinfo ON users.userID = userinfo.userID
        INNER JOIN program ON userinfo.programID = program.programID
        WHERE enrollments.enrollmentID = '$enrollmentID'
    ";

    $studentQueryResult = mysqli_query($conn, $studentQuery);

    // If enrollmentID not found in DB → 404
    if (!$studentQueryResult || mysqli_num_rows($studentQueryResult) === 0) {
        header("Location: 404.php");
        exit;
    }

    // Valid student found
    $student = mysqli_fetch_assoc($studentQueryResult);
    $userID = $student['userID'];
    $courseID = $student['courseID'];
}
// --- LEADERBOARD + REPORT LOGIC ---
if (!empty($student)) {
    $leaderboardQuery = "
            SELECT leaderboard.enrollmentID, SUM(leaderboard.xpPoints) AS totalXP
            FROM leaderboard
            INNER JOIN enrollments
                ON leaderboard.enrollmentID = enrollments.enrollmentID
            WHERE enrollments.courseID = '$courseID'
            GROUP BY leaderboard.enrollmentID
            ORDER BY totalXP DESC
        ";
    $leaderboardResult = executeQuery($leaderboardQuery);

    $rank = 1;
    $studentTotalXP = 0;
    $studentRank = 0;

    while ($row = mysqli_fetch_assoc($leaderboardResult)) {
        $id = $row['enrollmentID'];
        $totalXP = $row['totalXP'];

        // Update or insert report
        $checkQuery = "SELECT * FROM report WHERE enrollmentID = '$id'";
        $checkResult = executeQuery($checkQuery);

        if (mysqli_num_rows($checkResult) > 0) {
            $updateReport = "
                    UPDATE report 
                    SET totalXP = '$totalXP',
                        allTimeRank = '$rank',
                        generatedAt = NOW()
                    WHERE enrollmentID = '$id'
                ";
            executeQuery($updateReport);
        } else {
            $insertReport = "
                    INSERT INTO report (enrollmentID, totalXP, allTimeRank, generatedAt)
                    VALUES ('$id', '$totalXP', '$rank', NOW())
                ";
            executeQuery($insertReport);
        }

        // Capture the selected student's XP and rank
        if ($id == $enrollmentID) {
            $studentTotalXP = $totalXP;
            $studentRank = $rank;
        }

        $rank++;
    }

    // --- On-time and overall performance ---
    $onTimePercentage = '-';
    $overallPerformance = '-';

    $submissionQuery = "
            SELECT 
                assessments.assessmentID,
                assessments.assessmentTitle,
                assessments.type,
                assessments.deadline,
                scores.score,
                assignments.assignmentPoints AS totalPoints,
                submissions.submittedAt
            FROM assessments
            LEFT JOIN assignments ON assignments.assessmentID = assessments.assessmentID
            LEFT JOIN submissions ON submissions.assessmentID = assessments.assessmentID
            LEFT JOIN scores ON scores.scoreID = submissions.scoreID
            WHERE assessments.courseID = '$courseID' AND scores.userID = '$userID'

            UNION ALL

            SELECT
                assessments.assessmentID,
                assessments.assessmentTitle,
                assessments.type,
                assessments.deadline,
                scores.score,
                (
                    SELECT SUM(testquestions.testQuestionPoints) 
                    FROM testquestions
                    INNER JOIN tests ON testquestions.testID = tests.testID
                    WHERE tests.assessmentID = assessments.assessmentID
                ) AS totalPoints,
                scores.gradedAt AS submittedAt
            FROM assessments
            INNER JOIN tests ON tests.assessmentID = assessments.assessmentID
            INNER JOIN scores ON scores.testID = tests.testID
            WHERE assessments.courseID = '$courseID' AND scores.userID = '$userID'
            ";
    $submissionResult = executeQuery($submissionQuery);

    if ($submissionResult && mysqli_num_rows($submissionResult) > 0) {
        $totalCount = 0;
        $onTimeCount = 0;
        $totalScore = 0;
        $totalPoints = 0;

        while ($row = mysqli_fetch_assoc($submissionResult)) {
            $totalCount++;

            $submittedAt = $row['submittedAt'] ?? null;
            if ($submittedAt && $row['deadline'] && strtotime($submittedAt) <= strtotime($row['deadline'])) {
                $onTimeCount++;
            }

            if (isset($row['score']) && isset($row['totalPoints']) && $row['totalPoints'] > 0) {
                $totalScore += $row['score'];
                $totalPoints += $row['totalPoints'];
            }
        }

        $onTimePercentage = $totalCount > 0 ? round(($onTimeCount / $totalCount) * 100) . '%' : '-';
        $overallPerformance = $totalPoints > 0 ? round(($totalScore / $totalPoints) * 100) . '%' : '-';
    }

    // --- Submission records ---
    $recordQuery = "
            SELECT 
                submissions.submittedAt,
                assessments.assessmentID,
                assessments.assessmentTitle,
                assessments.type,
                scores.score,
                assignments.assignmentID,
                COALESCE(rubric.totalPoints, assignments.assignmentPoints) AS totalPoints
            FROM submissions
            INNER JOIN scores ON submissions.scoreID = scores.scoreID
            INNER JOIN assessments ON submissions.assessmentID = assessments.assessmentID
            INNER JOIN assignments ON assignments.assessmentID = assessments.assessmentID
            LEFT JOIN rubric ON assignments.rubricID = rubric.rubricID
            WHERE scores.userID = '$userID'
            AND assessments.courseID = '$courseID'
            ORDER BY submissions.submittedAt DESC
            ";
    $recordResult = executeQuery($recordQuery);

    // --- Test records ---
    $testRecordQuery = "
            SELECT 
                scores.gradedAt AS testDate,
                assessments.assessmentID,
                assessments.assessmentTitle,
                assessments.type,
                tests.testID,
                scores.score,
                (
                    SELECT SUM(testQuestionPoints)
                    FROM testquestions
                    WHERE testquestions.testID = tests.testID
                ) AS totalPoints
            FROM scores
            INNER JOIN tests ON scores.testID = tests.testID
            INNER JOIN assessments ON tests.assessmentID = assessments.assessmentID
            WHERE scores.userID = '$userID'
            AND assessments.courseID = '$courseID'
            ORDER BY scores.gradedAt DESC
            ";
    $testRecordResult = executeQuery($testRecordQuery);
}

if (!empty($student)) {
    $userID = $student['userID'];
    $courseID = $student['courseID'];

    // --- Fetch badges earned by student in this course ---
    $studentBadges = [];
    $badgeQuery = "
        SELECT badges.badgeName, badges.badgeIcon, badges.badgeDescription, COUNT(*) AS badgeCount
        FROM studentbadges
        INNER JOIN badges ON studentbadges.badgeID = badges.badgeID
        WHERE studentbadges.userID = '$userID'
        AND studentbadges.courseID = '$courseID'
        GROUP BY badges.badgeName, badges.badgeIcon, badges.badgeDescription
        ORDER BY MAX(studentbadges.studentBadgeID) DESC
    ";
    $badgeResult = executeQuery($badgeQuery);
    if ($badgeResult === false) {
        echo "Badge query failed: " . mysqli_error($conn);
    } else {
        while ($row = mysqli_fetch_assoc($badgeResult)) {
            $studentBadges[] = $row;
        }
    }
}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/course.css">
    <link rel="stylesheet" href="../shared/assets/css/inbox.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../shared/assets/img/webstar-icon.png">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0"
        rel="stylesheet" />

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

                    <div class="container-fluid overflow-y-auto py-3 px-4 px-md-5 row-padding-top">
                        <div class="row mt-3">
                            <div class="mb-3">
                                <div class="col-12 card p-2 d-flex flex-column flex-sm-row align-items-center justify-content-between"
                                    style="
        border: 1px solid var(--black); border-radius: 10px; background: linear-gradient(180deg, #FDDF94 0%, #FFFFFF 100%);">
                                    <!-- Left Side: Profile + Info -->
                                    <div
                                        class="d-flex flex-column flex-sm-row align-items-center text-center text-sm-start ">
                                        <div class="card d-flex justify-content-center align-items-center"
                                            style="width:83px; height:85px; border-radius:10px; overflow:hidden; border:1px solid var(--black); background-color:var(--black); margin:12px;">
                                            <img src="<?php
                                            if (!empty($student['profilePicture'])) {
                                                echo '../shared/assets/pfp-uploads/' . $student['profilePicture'];
                                            } else {
                                                echo '../shared/assets/img/default-profile.png';
                                            }
                                            ?>" alt="Profile" style="object-fit:cover; border-radius:0px; width:100%; height:100%;">
                                        </div>

                                        <div>
                                            <?php if (!empty($student)): ?>
                                                <div class="text-bold text-18">
                                                    <?php
                                                    $middleInitial = !empty($student['middleName'])
                                                        ? strtoupper(substr($student['middleName'], 0, 1)) . '.'
                                                        : '';
                                                    echo $student['firstName'] . ' ' . $middleInitial . ' ' . $student['lastName'];
                                                    ?>
                                                </div>
                                                <div class="text-med text-muted text-12">
                                                    @<?php echo $student['userName']; ?></div>
                                                <div class="text-med text-12"><?php echo $student['studentID']; ?></div>
                                                <div class="text-med text-12">
                                                    <?php echo $student['programInitial'] . ' ' . $student['yearLevel'] . '-' . $student['yearSection']; ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="text-bold text-18">No student data found.</div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Right Side: Rank + XP -->
                                    <div class="text-center align-items-center justify-content-center text-sm-end mt-3 mt-sm-0 mb-3 mb-sm-0"
                                        style="line-height: 1.2; margin-right: 15px;">
                                        <?php if (!empty($studentRank) && !empty($studentTotalXP)): ?>
                                            <div class="text-bold text-25">
                                                <?php
                                                // ordinal suffix
                                                $suffix = 'th';
                                                if (!in_array(($studentRank % 100), [11, 12, 13])) {
                                                    switch ($studentRank % 10) {
                                                        case 1:
                                                            $suffix = 'st';
                                                            break;
                                                        case 2:
                                                            $suffix = 'nd';
                                                            break;
                                                        case 3:
                                                            $suffix = 'rd';
                                                            break;
                                                    }
                                                }
                                                echo $studentRank . $suffix;
                                                ?>
                                            </div>
                                            <div class="text-bold text-22"><?php echo $studentTotalXP; ?> XPs</div>
                                            <div class="text-med text-18">All-time Rank</div>
                                        <?php else: ?>
                                            <div class="text-bold text-30">-</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Stats -->
                            <div class="row m-0 justify-content-center align-items-center mt-3 text-center">
                                <div class="col-12 col-sm-6 mb-3 mb-sm-0 d-flex flex-column align-items-center">
                                    <div
                                        class="percent d-flex justify-content-center align-items-center text-sbold text-20">
                                        <span class="material-symbols-rounded"
                                            style="margin-right:6px; font-size:23px;">
                                            assignment_turned_in
                                        </span>
                                        <span id="onTimePercentage"><?php echo $onTimePercentage; ?></span>
                                    </div>
                                    <div class="title text-sbold text-16">On-Time Submissions</div>
                                    <div class="sub text-reg text-12">Percentage of tasks and <br> exams submitted on
                                        time.</div>
                                </div>

                                <div class="col-12 col-sm-6 d-flex flex-column align-items-center">
                                    <div
                                        class="percent d-flex justify-content-center align-items-center text-sbold text-20">
                                        <span class="material-symbols-rounded"
                                            style="margin-right:6px; font-size:23px;">
                                            assignment
                                        </span>
                                        <span id="overallPerformance"><?php echo $overallPerformance; ?></span>
                                    </div>
                                    <div class="title text-sbold text-16">Overall Performance</div>
                                    <div class="sub text-reg text-12">Based on your submission rate and <br> scores in
                                        tasks and exams.</div>
                                </div>
                            </div>

                            <!-- Record Table -->
                            <div class="row mt-4 mt-md-2 w-100 m-0">
                                <div class="col-12 p-0 d-flex align-items-center align-items-md-start w-100">
                                    <div class="w-100 text-med text-14 text-center text-md-start my-3">Record</div>
                                </div>
                            </div>

                            <div class="row m-0">
                                <div class="col-12 p-0">
                                    <div class="card d-none d-md-block"
                                        style="border: 1px solid var(--black); border-radius: 10px; overflow: hidden; width: 100%;">
                                        <div class="table-responsive d-none d-md-block">
                                            <table class="table mb-0  px-3" style="width: 100%">
                                                <thead>
                                                    <tr class="text-med text-12">
                                                        <th
                                                            style="background-color: var(--primaryColor); border-right:1px solid var(--black); border-bottom:1px solid var(--black); width:25%;">
                                                            <span class="material-symbols-rounded"
                                                                style="font-size:13px; vertical-align:middle; margin-right:4px; color: var(--black);">calendar_today</span>
                                                            <span class="text-reg">Date</span>
                                                        </th>
                                                        <th
                                                            style="background-color: var(--primaryColor); border-right:1px solid var(--black); border-bottom:1px solid var(--black); width:25%;">
                                                            <span class="material-symbols-rounded"
                                                                style="font-size:13px; vertical-align:middle; margin-right:4px; color: var(--black);">notes</span>
                                                            <span class="text-reg">Title</span>
                                                        </th>
                                                        <th
                                                            style="background-color: var(--primaryColor); border-right:1px solid var(--black); border-bottom:1px solid var(--black); width:25%;">
                                                            <span class="material-symbols-rounded"
                                                                style="font-size:13px; vertical-align:middle; margin-right:4px; color: var(--black);">list</span>
                                                            <span class="text-reg">Type</span>
                                                        </th>
                                                        <th
                                                            style="background-color: var(--primaryColor); border-bottom:1px solid var(--black); width:25%;">
                                                            <span class="material-symbols-rounded"
                                                                style="font-size:13px; vertical-align:middle; margin-right:4px; color: var(--black);">task_alt</span>
                                                            <span class="text-reg">Grade</span>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $hasRecords = false;

                                                    // --- Combine all records into one array for easier handling ---
                                                    $allRecords = [];
                                                    if ($recordResult && mysqli_num_rows($recordResult) > 0) {
                                                        while ($record = mysqli_fetch_assoc($recordResult)) {
                                                            $allRecords[] = [
                                                                'date' => $record['submittedAt'],
                                                                'title' => $record['assessmentTitle'],
                                                                'link' => "assignment.php?assignmentID={$record['assignmentID']}",
                                                                'type' => $record['type'],
                                                                'score' => $record['score'],
                                                                'total' => $record['totalPoints']   // <-- updated to totalPoints
                                                            ];
                                                        }
                                                    }

                                                    if ($testRecordResult && mysqli_num_rows($testRecordResult) > 0) {
                                                        while ($test = mysqli_fetch_assoc($testRecordResult)) {
                                                            $allRecords[] = [
                                                                'date' => $test['testDate'],
                                                                'title' => $test['assessmentTitle'],
                                                                'link' => "exam-info.php?testID={$test['testID']}",
                                                                'type' => $test['type'],
                                                                'score' => $test['score'],
                                                                'total' => $test['totalPoints']
                                                            ];
                                                        }
                                                    }

                                                    $totalRows = count($allRecords);
                                                    if ($totalRows > 0):
                                                        $hasRecords = true;
                                                        foreach ($allRecords as $index => $row):
                                                            $isLast = ($index === $totalRows - 1); // check last row
                                                            ?>
                                                            <tr class="text-med text-12">
                                                                <td
                                                                    style="border-right:1px solid var(--black);color: var(--black)!important; <?php echo $isLast ? '' : 'border-bottom:1px solid var(--black);'; ?>">
                                                                    <?php echo date('F d, Y', strtotime($row['date'])); ?>
                                                                </td>
                                                                <td
                                                                    style="border-right:1px solid var(--black);color: var(--black)!important; <?php echo $isLast ? '' : 'border-bottom:1px solid var(--black);'; ?>">
                                                                    <a href="<?php echo $row['link']; ?>"
                                                                        style="text-decoration:none; color:var(--black); font-weight:500;">
                                                                        <?php echo $row['title']; ?>
                                                                    </a>
                                                                </td>
                                                                <td
                                                                    style="border-right:1px solid var(--black); color: var(--black)!important;text-align:center; <?php echo $isLast ? '' : 'border-bottom:1px solid var(--black);'; ?>">
                                                                    <span
                                                                        class="course-badge rounded-pill px-3 text-reg text-12">
                                                                        <?php echo ucfirst($row['type']); ?>
                                                                    </span>
                                                                </td>
                                                                <td
                                                                    style="color: var(--black)!important;<?php echo $isLast ? '' : 'border-bottom:1px solid var(--black);'; ?>">
                                                                    <?php echo $row['score']; ?>/<?php echo $row['total']; ?>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                        endforeach;
                                                    endif;

                                                    if (!$hasRecords):
                                                        ?>
                                                        <tr>
                                                            <td colspan="4" class="text-center text-muted text-med text-14">
                                                                No records found.</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Mobile Cards -->
                            <div class="row m-0">
                                <div class="col d-block d-md-none p-0">

                                    <?php
                                    $hasRecords = false;

                                    if (!empty($allRecords)):
                                        $hasRecords = true;

                                        foreach ($allRecords as $record):
                                            ?>
                                            <a href="<?php echo $record['link']; ?>"
                                                style="text-decoration:none; color:var(--black);">
                                                <div class="card p-3 mb-2 w-100"
                                                    style="border: 1px solid var(--black); border-radius: 10px; width: 290px;">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="text-sbold"><?php echo $record['title']; ?></span>
                                                        <span class="text-sbold text-16">
                                                            <?php echo $record['score']; ?>/<?php echo $record['total']; ?>
                                                        </span>
                                                    </div>
                                                    <div class="justify-content-start align-items-start">
                                                        <div class="text-reg text-14 mb-2">
                                                            <?php echo date('F d, Y', strtotime($record['date'])); ?>
                                                        </div>
                                                        <div class="course-badge rounded-pill px-3 text-reg text-12"
                                                            style="width: 60px;">
                                                            <?php echo ucfirst($record['type']); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                            <?php
                                        endforeach;
                                    endif;

                                    if (!$hasRecords):
                                        ?>
                                        <div class="text-center text-muted text-med text-14">No submission or test records
                                            found.</div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Badges -->
                            <div class="row mt-4 mt-md-2 w-100 m-0">
                                <div class="col-12 p-0 d-flex align-items-center align-items-md-start w-100">
                                    <div class="w-100 text-med text-14 text-center text-md-start my-3">Badges</div>
                                </div>
                            </div>

                            <div class="row m-0">
                                <?php if (!empty($studentCourseBadges)): ?>
                                    <?php foreach ($studentCourseBadges as $b): ?>
                                        <div class="col-12 mb-2 p-0">
                                            <div class="card py-2 py-sm-0"
                                                style="border: 1px solid var(--black); border-radius: 10px; width: 100%;">
                                                <div
                                                    class="col-12 d-flex flex-row align-items-center justify-content-between flex-nowrap px-3 py-1">

                                                    <!-- Left Side: Badge icon + details -->
                                                    <div class="d-flex flex-row align-items-center flex-nowrap py-1"
                                                        style="min-width: 0;">
                                                        <div
                                                            class="d-flex justify-content-center align-items-center flex-shrink-0">
                                                            <img src="../shared/assets/img/badge/<?php echo $b['badgeIcon']; ?>"
                                                                style="width:60px; height:62px; object-fit:contain;"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                title="<?php echo $b['badgeName']; ?>">
                                                        </div>

                                                        <div class="ms-3 me-4 flex-grow-1"
                                                            style="min-width:0; word-break: break-word; white-space: normal; text-align: justify;">
                                                            <div class="text-sbold text-14"><?php echo $b['badgeName']; ?></div>

                                                            <?php if (!empty($b['badgeDescription'])): ?>
                                                                <div class="text-med text-12"><?php echo $b['badgeDescription']; ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>

                                                    <!-- Right Side: Count -->
                                                    <?php if (!empty($b['badgeCount']) && $b['badgeCount'] > 1): ?>
                                                        <div class="text-end flex-shrink-0"
                                                            style="line-height: 1.2; margin-left: 5px;">
                                                            <div class="text-sbold text-22 justify-content-end">
                                                                x<?php echo $b['badgeCount']; ?></div>
                                                        </div>
                                                    <?php endif; ?>

                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-12 text-center text-muted text-med text-14">No badges earned yet.</div>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>