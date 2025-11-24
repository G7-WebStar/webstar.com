<?php
$activePage = 'course';

include('../shared/assets/database/connect.php');
date_default_timezone_set('Asia/Manila');
include("../shared/assets/processes/prof-session-process.php");

$activeTab = 'announcements'; // default

if (isset($_POST['activeTab'])) {
    $activeTab = $_POST['activeTab'];
} elseif (isset($_SESSION['activeTab'])) {
    $activeTab = $_SESSION['activeTab'];
    unset($_SESSION['activeTab']); // clear after using
}

$toastMessage = '';
$toastType = '';

if (isset($_SESSION['toast'])) {
    $toastMessage = $_SESSION['toast']['message'];
    $toastType = $_SESSION['toast']['type'];
    unset($_SESSION['toast']);
}


if (isset($_POST['deleteCourse'])) {
    $courseID = $_POST['courseID'];

    // --- Delete Assessments and all their children ---
    $selectAssessments = executeQuery("SELECT assessmentID FROM assessments WHERE courseID = '$courseID'");
    while ($assessment = mysqli_fetch_assoc($selectAssessments)) {
        $assessmentID = $assessment['assessmentID'];

        // Delete assignments and their files
        $selectAssignments = executeQuery("SELECT assignmentID FROM assignments WHERE assessmentID = '$assessmentID'");
        while ($assignment = mysqli_fetch_assoc($selectAssignments)) {
            $assignmentID = $assignment['assignmentID'];
            executeQuery("DELETE FROM files WHERE assignmentID = '$assignmentID'");
        }
        executeQuery("DELETE FROM assignments WHERE assessmentID = '$assessmentID'");

        // Delete tests and their children
        $selectTests = executeQuery("SELECT testID FROM tests WHERE assessmentID = '$assessmentID'");
        while ($test = mysqli_fetch_assoc($selectTests)) {
            $testID = $test['testID'];

            // Delete test questions
            $selectQuestions = executeQuery("SELECT testQuestionID FROM testquestions WHERE testID = '$testID'");
            while ($question = mysqli_fetch_assoc($selectQuestions)) {
                $questionID = $question['testQuestionID'];

                executeQuery("DELETE FROM testquestionchoices WHERE testQuestionID = '$questionID'");
                executeQuery("DELETE FROM testresponses WHERE testQuestionID = '$questionID'");
            }
            executeQuery("DELETE FROM testquestions WHERE testID = '$testID'");

            // Delete scores linked to test
            executeQuery("DELETE FROM scores WHERE testID = '$testID'");
        }
        executeQuery("DELETE FROM tests WHERE assessmentID = '$assessmentID'");

        // Delete submissions and their files and scores
        $selectSubmissions = executeQuery("SELECT submissionID FROM submissions WHERE assessmentID = '$assessmentID'");
        while ($submission = mysqli_fetch_assoc($selectSubmissions)) {
            $submissionID = $submission['submissionID'];
            // Delete selected rubric levels
            executeQuery("DELETE FROM selectedlevels WHERE submissionID = '$submissionID'");
            // Delete files linked to submission
            executeQuery("DELETE FROM files WHERE submissionID = '$submissionID'");
            // Delete scores for submission
            executeQuery("DELETE FROM scores WHERE submissionID = '$submissionID'");
        }
        // Finally delete submissions
        executeQuery("DELETE FROM submissions WHERE assessmentID = '$assessmentID'");

        // Delete todos
        executeQuery("DELETE FROM todo WHERE assessmentID = '$assessmentID'");

        // Delete files linked directly to assessment
        executeQuery("DELETE FROM files WHERE assessmentID = '$assessmentID'");
    }

    // Delete assessments
    executeQuery("DELETE FROM assessments WHERE courseID = '$courseID'");

    // --- Delete announcements and their children ---
    $selectAnnouncements = executeQuery("SELECT announcementID FROM announcements WHERE courseID = '$courseID'");
    while ($announcement = mysqli_fetch_assoc($selectAnnouncements)) {
        $announcementID = $announcement['announcementID'];
        executeQuery("DELETE FROM announcementnotes WHERE noteID IN (SELECT noteID FROM announcementnotes WHERE announcementID = '$announcementID')");
        executeQuery("DELETE FROM files WHERE announcementID = '$announcementID'");
    }
    executeQuery("DELETE FROM announcements WHERE courseID = '$courseID'");

    // --- Delete lessons and their files ---
    $selectLessons = executeQuery("SELECT lessonID FROM lessons WHERE courseID = '$courseID'");
    while ($lesson = mysqli_fetch_assoc($selectLessons)) {
        $lessonID = $lesson['lessonID'];
        executeQuery("DELETE FROM files WHERE lessonID = '$lessonID'");
    }
    executeQuery("DELETE FROM lessons WHERE courseID = '$courseID'");

    // --- Delete enrollments and children (leaderboard, inbox, report, files) ---
    $selectEnrollments = executeQuery("SELECT enrollmentID FROM enrollments WHERE courseID = '$courseID'");
    while ($enrollment = mysqli_fetch_assoc($selectEnrollments)) {
        $enrollmentID = $enrollment['enrollmentID'];
        executeQuery("DELETE FROM leaderboard WHERE enrollmentID = '$enrollmentID'");
        executeQuery("DELETE FROM inbox WHERE enrollmentID = '$enrollmentID'");
        executeQuery("DELETE FROM report WHERE enrollmentID = '$enrollmentID'");
    }
    executeQuery("DELETE FROM enrollments WHERE courseID = '$courseID'");

    // --- Delete files directly linked to course ---
    executeQuery("DELETE FROM files WHERE courseID = '$courseID'");

    // --- Delete course schedules ---
    executeQuery("DELETE FROM courseschedule WHERE courseID = '$courseID'");

    // --- Finally delete the course itself ---
    $deleteFromCourseTableQuery = executeQuery("DELETE FROM courses WHERE courseID = '$courseID'");

    // Redirect after deletion
    if ($deleteFromCourseTableQuery) {
        $_SESSION['toast'] = [
            'type' => 'alert-success',
            'message' => 'Course deleted successfully!'
        ];
        header("Location: course.php");
        exit();
    }
}

if (isset($_GET['courseID'])) {
    $courseID = $_GET['courseID'];

    $checkCourseOwnershipQuery = "SELECT * FROM courses WHERE userID = '$userID' AND courseID = '$courseID'";
    $checkCourseOwnershipResult = executeQuery($checkCourseOwnershipQuery);

    if (mysqli_num_rows($checkCourseOwnershipResult) <= 0) {
        header("Location: 404.html");
    }

    $selectCourseQuery = "SELECT 
    courses.*, 
   	profInfo.firstName AS profFirstName,
    profInfo.middleName AS profMiddleName,
    profInfo.lastName AS profLastName,
    profInfo.profilePicture AS profPFP,
    GROUP_CONCAT(
        CONCAT(
            courseschedule.day, ' ', 
            DATE_FORMAT(courseschedule.startTime, '%h:%i %p'), '-', 
            DATE_FORMAT(courseschedule.endTime, '%h:%i %p')
        ) 
        ORDER BY FIELD(courseschedule.day, 'Mon','Tue','Wed','Thu','Fri','Sat','Sun'), courseschedule.startTime
        SEPARATOR '\n'
    ) AS courseschedule
    FROM courses
    INNER JOIN userinfo AS profInfo
    	ON courses.userID = profInfo.userID
    LEFT JOIN courseschedule
        ON courses.courseID = courseschedule.courseID
    WHERE courses.courseID = '$courseID' AND courses.userID = '$userID';
";
    $selectCourseResult = executeQuery($selectCourseQuery);

    // Sort Todo
    $sortTodo = $_POST['sortTodo'] ?? 'Newest';
    $statusFilter = $_POST['statusFilter'] ?? 'Pending';

    // Sort by
    switch ($sortTodo) {
        case 'Oldest':
            $todoOrderBy = "assessments.deadline ASC";
            break;

        default:
            $todoOrderBy = "assessments.deadline DESC";
            break;
    }

    // Status filter
    $todoWhereStatus = "";
    $deadlineCondition = "";

    if ($statusFilter === 'Active') {
        $deadlineCondition = "AND NOW() < assessments.deadline";
    } elseif ($statusFilter === 'Done') {
        $deadlineCondition = "AND NOW() > assessments.deadline";
    } else {
        // Default: show pending items with future deadlines
        $todoWhereStatus = "AND (todo.status IS NULL OR todo.status = 'Pending')";
        $deadlineCondition = "AND NOW() < assessments.deadline";
    }

    $selectAssessmentQuery = "  SELECT
        tests.testID,
        assignments.assignmentID,
        assessments.*,
        assessments.assessmentID AS realAssessmentID,
        assessments.assessmentTitle AS assessmentTitle,
        courses.courseCode,
        DATE_FORMAT(assessments.deadline, '%b %e') AS assessmentDeadline,
        todo.*
    FROM assessments
    INNER JOIN courses
        ON assessments.courseID = courses.courseID
    LEFT JOIN assignments
        ON assignments.assessmentID = assessments.assessmentID
    LEFT JOIN tests
        ON tests.assessmentID = assessments.assessmentID
    LEFT JOIN todo
        ON todo.assessmentID = assessments.assessmentID
        AND todo.userID = '$userID'
    WHERE courses.courseID = '$courseID'
      $deadlineCondition
      $todoWhereStatus
    ORDER BY $todoOrderBy;
";
    $selectAssessmentResult = executeQuery($selectAssessmentQuery);

    $selectAssessmentQueryForCourseCard = "
    SELECT
        tests.testID,
        assignments.assignmentID,
        assessments.*,
        assessments.assessmentTitle AS assessmentTitle,
        courses.courseCode,
        DATE_FORMAT(assessments.deadline, '%b %e') AS assessmentDeadline
    FROM assessments
    INNER JOIN courses
        ON assessments.courseID = courses.courseID
    LEFT JOIN assignments
        ON assignments.assessmentID = assessments.assessmentID
    LEFT JOIN tests
        ON tests.assessmentID = assessments.assessmentID
    WHERE courses.courseID = '$courseID'
      AND NOW() < assessments.deadline
    ORDER BY assessments.deadline ASC;
";

    $selectLimitAssessmentQuery = $selectAssessmentQueryForCourseCard;
    $selectLimitAssessmentResult = executeQuery($selectLimitAssessmentQuery);

    $selectLeaderboardQuery = "SELECT 
	courses.courseTitle,
    SUM(leaderboard.xpPoints) AS totalPoints
    FROM leaderboard
    INNER JOIN enrollments
        ON leaderboard.enrollmentID = enrollments.enrollmentID
   	INNER JOIN courses
    	ON enrollments.courseID = courses.courseID
    WHERE enrollments.userID = '$userID' AND enrollments.courseID = '$courseID'
    GROUP BY courses.courseTitle;
";
    $selectLeaderboardResult = executeQuery($selectLeaderboardQuery);

    $whereTotalPlacementQuery = "WHERE enrollments.courseID = '$courseID'";
    $dateFilter = $_POST['dateFilter'] ?? null;

    if (!empty($dateFilter)) {
        if ($dateFilter === 'Monthly') {
            $whereTotalPlacementQuery .= " AND leaderboard.timeRange = '$dateFilter'";
        } else if ($dateFilter === 'Weekly') {
            $whereTotalPlacementQuery .= " AND leaderboard.timeRange = '$dateFilter'";
        } else if ($dateFilter === 'Daily') {
            $whereTotalPlacementQuery .= " AND leaderboard.timeRange = '$dateFilter'";
        }
        $navState = "active";
    } else {
        $navState = "";
        $whereTotalPlacementQuery .= " AND leaderboard.timeRange = 'Weekly'";
    }

    $countLimitQuery = "SELECT COUNT(*) AS limitCount FROM leaderboard";
    $countLimitResult = executeQuery($countLimitQuery);
    $limitRow = (mysqli_num_rows($countLimitResult) > 0) ? mysqli_fetch_assoc($countLimitResult) : 'null';
    $limit = ($limitRow == null) ? null : $limitRow['limitCount'];

    $TotalPlacementQuery = "SELECT 
    leaderboard.leaderboardID,
	userinfo.profilePicture,
    userinfo.firstName,
    userinfo.middleName,
    userinfo.lastName,
    enrollments.userID,
    leaderboard.xpPoints AS totalPoints
    FROM leaderboard
    INNER JOIN enrollments
        ON leaderboard.enrollmentID = enrollments.enrollmentID
    INNER JOIN userinfo
	    ON enrollments.userID = userinfo.userID
    $whereTotalPlacementQuery
    GROUP BY enrollments.userID
    ORDER BY totalPoints DESC
    LIMIT";

    $selectTopOneQuery = $TotalPlacementQuery . " 1;";
    $selectTopOneResult = executeQuery($selectTopOneQuery);

    $selectTopTwoToThreeQuery = $TotalPlacementQuery . " 2 OFFSET 1;";
    $selectTopTwoToThreeResult = executeQuery($selectTopTwoToThreeQuery);

    $selectTopFourToTenQuery = $TotalPlacementQuery . " $limit OFFSET 3;";
    $selectTopFourToTenResult = executeQuery($selectTopFourToTenQuery);

    $selectPlacementQuery = "SELECT
    userinfo.profilePicture,
    userinfo.firstName,
    userinfo.middleName,
    userinfo.lastName, 
    ranked.userID,
    ranked.totalPoints,
    ranked.rank
    FROM (SELECT 
        enrollments.userID,
        SUM(leaderboard.xpPoints) AS totalPoints,
        RANK() OVER (ORDER BY SUM(leaderboard.xpPoints) DESC) AS rank
        FROM leaderboard
        INNER JOIN enrollments
            ON leaderboard.enrollmentID = enrollments.enrollmentID
        $whereTotalPlacementQuery
        GROUP BY enrollments.userID
        ) AS ranked
    INNER JOIN userinfo
    	ON ranked.userID = userinfo.userID
    WHERE ranked.userID = '$userID' AND ranked.rank > 10
    ";
    $selectPlacementResult = executeQuery($selectPlacementQuery);
} else {
    header("Location: 404.php");
    exit();
}

$courseCodeResult = executeQuery("SELECT courseCode FROM courses WHERE courseID = '$courseID' LIMIT 1");
$courseCode = mysqli_fetch_assoc($courseCodeResult);

$userID = $_SESSION['userID'];

$query = "
    SELECT userinfo.profilePicture
    FROM userinfo
    JOIN users ON userinfo.userID = users.userID
    WHERE users.userID = '$userID';
";

$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$user = mysqli_fetch_assoc($result);
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | <?php echo $courseCode['courseCode']; ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/course-Info.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../shared/assets/img/webstar-icon.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0"
        rel="stylesheet" />
    <style>
        .my-student-item:hover {
            cursor: pointer;
        }

        .modal-body .confirm-text {
            white-space: normal;
            overflow-wrap: anywhere;
            word-break: break-word;
            display: block;
            margin: 0;
            line-height: 1.35;
        }

        .modal-body .confirm-text .text-sbold {
            display: inline;
            max-width: 100%;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        @media screen and (max-width: 767px) {
            .text-sm-14 {
                font-size: 14px !important;
            }
        }
    </style>

</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-0 p-md-3"
        style="background-color: var(--black);">

        <div class="row w-100">

            <?php include '../shared/components/prof-sidebar-for-mobile.php'; ?>
            <?php include '../shared/components/prof-sidebar-for-desktop.php'; ?>

            <div class="col main-container m-0 p-0 mx-0 mx-md-2 p-0 p-md-4 overflow-y-auto">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">

                    <div id="toastContainer"
                        class="position-absolute top-0 start-50 translate-middle-x pt-5 pt-md-1 d-flex flex-column align-items-center"
                        style="z-index: 1100;">
                    </div>

                    <?php include '../shared/components/prof-navbar-for-mobile.php'; ?>

                    <div class="container-fluid py-3 overflow-y-auto row-padding-top" style="white-space: nowrap; ">
                        <div class="row d-flex justify-content-center">
                            <div class="row mt-0">

                                <!-- LEFT: Course Card -->
                                <div class="col-md-4">
                                    <?php
                                    if (mysqli_num_rows($selectCourseResult) > 0) {
                                        while ($courses = mysqli_fetch_assoc($selectCourseResult)) {
                                            ?>

                                            <!-- Mobile Dropdown Course Card -->
                                            <div class="course-card-mobile d-block d-md-none w-100">
                                                <div class="course-card p-0 mb-2 rounded-3"
                                                    style="width: 100%; outline: 1px solid var(--black); overflow: hidden;">

                                                    <!-- Collapsible Header -->
                                                    <a class="d-flex justify-content-center align-items-center px-3 py-3 position-relative text-decoration-none"
                                                        data-bs-toggle="collapse"
                                                        href="#mobileCourseCard<?php echo $courses['courseID']; ?>"
                                                        role="button" aria-expanded="false"
                                                        aria-controls="mobileCourseCard<?php echo $courses['courseID']; ?>"
                                                        style="background-color: var(--primaryColor); width: 100% !important; border-bottom-left-radius: 0; border-bottom-right-radius: 0;">
                                                        <div class="fa fa-arrow-left text-dark position-absolute start-0 top-50 translate-middle-y ms-4"
                                                            onclick="window.location.href='course.php';"
                                                            style="cursor: pointer;"></div>

                                                        <div class="text-center flex-grow-1">
                                                            <h5 class="text-bold mb-1" style="line-height:1;">
                                                                <?php echo $courses['courseCode']; ?>
                                                            </h5>
                                                            <p class="text-reg mb-0" style="line-height:1;">
                                                                <?php echo $courses['courseTitle']; ?>
                                                            </p>
                                                        </div>

                                                        <i
                                                            class="fa fa-chevron-down text-dark position-absolute end-0 top-50 translate-middle-y me-4"></i>
                                                    </a>

                                                    <!-- Collapsible Body -->
                                                    <div class="collapse bg-white"
                                                        id="mobileCourseCard<?php echo $courses['courseID']; ?>">

                                                        <div class="p-3">
                                                            <!-- Course Image with Three-dot menu -->
                                                            <div class="course-image w-100 mb-3 mt-1 position-relative"
                                                                style="overflow: hidden; border-radius: 10px; min-height: 150px; background-color:var(--primaryColor)">
                                                                <img src="../shared/assets/img/course-images/<?php echo $courses['courseImage']; ?>"
                                                                    alt="Course Image"
                                                                    style="width: 100%; height: 100%; object-fit: cover;">

                                                                <!-- Three-dot menu -->
                                                                <div class="dropdown position-absolute top-0 end-0 m-2">
                                                                    <button
                                                                        class="btn p-1 rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center"
                                                                        type="button" data-bs-toggle="dropdown"
                                                                        aria-expanded="false"
                                                                        style="width: 32px; height: 32px;">
                                                                        <i class="fa-solid fa-ellipsis-vertical text-dark"></i>
                                                                    </button>

                                                                    <ul class="dropdown-menu shadow-sm text-reg border">
                                                                        <li>
                                                                            <a class="dropdown-item"
                                                                                href="create-course.php?edit=<?php echo $courses['courseID']; ?>">Edit</a>
                                                                        </li>
                                                                        <li>
                                                                            <a class="dropdown-item text-danger "
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#deleteModal">Delete</a>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>

                                                            <!-- Prof -->
                                                            <div class="d-flex align-items-center text-decoration-none">
                                                                <div class="rounded-circle me-2 flex-shrink-0" style="width: 25px; height: 25px; background-color: #5ba9ff;
                                                                background: url('../shared/assets/pfp-uploads/<?php echo $courses['profPFP']; ?>') no-repeat center center;
                                                                background-size: cover;">
                                                                </div>
                                                                <div class="d-flex flex-column justify-content-center">
                                                                    <span class="text-sbold text-12" style="line-height: 1.2">
                                                                        <?php echo $courses['profFirstName'] . " " . $courses['profMiddleName'] . " " . $courses['profLastName']; ?>
                                                                    </span>
                                                                    <small class="text-reg text-12" style="line-height: 1.2">
                                                                        Instructor
                                                                    </small>
                                                                </div>
                                                            </div>

                                                            <!-- Sched -->
                                                            <div class="d-flex align-items-center text-decoration-none mt-3">
                                                                <span
                                                                    class="material-symbols-outlined text-center d-flex align-items-center"
                                                                    style="width: 10px; height: 10px; margin-right: 23px;">
                                                                    calendar_today
                                                                </span>
                                                                <div class="d-flex flex-column justify-content-center">
                                                                    <span class="text-reg text-12">
                                                                        <span><?php echo isset($courses['courseschedule']) ? nl2br($courses['courseschedule']) : 'No schedule yet'; ?></span>
                                                                    </span>
                                                                </div>
                                                            </div>

                                                            <!-- Class Standing -->
                                                            <div class="row mb-3 mt-3">
                                                                <div class="col">
                                                                    <label class="text-med text-12">Class Standing</label>
                                                                    <div class="class-standing text-12 d-flex justify-content-between align-items-center rounded-3 mt-2"
                                                                        style="background-color:white">
                                                                        <span class="material-symbols-outlined"
                                                                            style="font-size:20px">
                                                                            leaderboard
                                                                        </span>
                                                                        <span class="text-sbold">
                                                                            RANK 1
                                                                        </span>
                                                                        <?php
                                                                        if (mysqli_num_rows($selectLeaderboardResult) > 0) {
                                                                            mysqli_data_seek($selectLeaderboardResult, 0);
                                                                            while ($points = mysqli_fetch_assoc($selectLeaderboardResult)) {
                                                                                ?>
                                                                                <div class="d-flex align-items-center">
                                                                                    <img class="me-1" src="shared/assets/img/xp.png"
                                                                                        alt="Description of Image" width="15">
                                                                                    <span
                                                                                        class="text-med"><?php echo $points['totalPoints']; ?>
                                                                                        XPs</span>
                                                                                </div>

                                                                                <?php
                                                                            }
                                                                        }
                                                                        ?>

                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Level -->
                                                            <div class="row mb-3 mt-3">
                                                                <div class="col">
                                                                    <label class="text-med text-12">Level</label>
                                                                    <div class="class-standing text-12 align-items-center rounded-3 p-3 mt-2"
                                                                        style="background-color:white">
                                                                        <div class="d-flex justify-content-between py-1">
                                                                            <span class="text-sbold">
                                                                                LV. 1 · Mentor
                                                                            </span>
                                                                            <span class="text-sbold">
                                                                                3160 / 4000 XP
                                                                            </span>
                                                                        </div>

                                                                        <div class="progress mt-2 mb-2" role="progressbar"
                                                                            aria-label="Basic example" aria-valuenow="0"
                                                                            aria-valuemin="0" aria-valuemax="100"
                                                                            style="height: 10px; border: 1px solid var(--black);">
                                                                            <div class="progress-bar"
                                                                                style="width: 50%; background-color:var(--primaryColor); border-right: 1px solid var(--black);">
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Quests -->
                                                            <div class="row mb-3 mt-3">
                                                                <div class="col">
                                                                    <?php if (mysqli_num_rows($selectLimitAssessmentResult) > 0) {?>
                                                                        <label class="text-med text-12">Active Assessments</label>
                                                                        <?php
                                                                        mysqli_data_seek($selectLimitAssessmentResult, 0);
                                                                        while ($activities = mysqli_fetch_assoc($selectLimitAssessmentResult)) {
                                                                            ?>
                                                                            
                                                                            <div
                                                                                class="todo-card-course-info d-flex align-items-stretch rounded-2 mt-2 w-100">
                                                                                <div class="date-section text-sbold text-12 px-3"
                                                                                    style="text-transform:uppercase; ">
                                                                                    <?php echo $activities['assessmentDeadline']; ?>
                                                                                </div>
                                                                                <div
                                                                                    class="d-flex align-items-center flex-wrap flex-grow-1 p-2 gap-3">
                                                                                    <div class="flex-grow-1 px-2">
                                                                                        <div class="text-med text-14 truncate">
                                                                                            <?php echo $activities['assessmentTitle']; ?>
                                                                                        </div>
                                                                                    </div>

                                                                                    <?php
                                                                                    $type = strtolower(trim($activities['type']));
                                                                                    $link = "#";

                                                                                    if ($type === 'task') {
                                                                                        $link = "task-info.php?assignmentID=" . $activities['assignmentID'];
                                                                                    } elseif ($type === 'test') {
                                                                                        $link = "test-info.php?testID=" . $activities['testID'];
                                                                                    }
                                                                                    ?>

                                                                                    <div>
                                                                                        <a href="<?php echo $link; ?>"
                                                                                            class="text-decoration-none  d-flex align-items-center">
                                                                                            <i class="fa-solid fa-arrow-right text-reg text-12 pe-2"
                                                                                                style="color: var(--black);"></i>
                                                                                        </a>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <?php
                                                                        }
                                                                    } ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Desktop Course Card -->
                                            <div class="course-card-desktop d-none d-md-block w-100"
                                                style="position: sticky; overflow: hidden; white-space: normal; border: 1px solid var(--black); overflow-wrap: break-word; word-break: break-word;">
                                                <div class="course-card-desktop w-100 p-4"
                                                    style="outline: 1px solid var(--black); border-radius: 10px; ">

                                                    <!-- Back Button + Three-dot menu -->
                                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                                        <!-- Back Button (JavaScript back) -->
                                                        <a onclick="history.back()" style="cursor:pointer;" class="text-reg">
                                                            <i class="fas fa-arrow-left"></i>
                                                        </a>

                                                        <!-- Three-dot menu -->
                                                        <div class="dropdown">
                                                            <button class="btn p-0 btn-sm border-0 bg-transparent" type="button"
                                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="fa-solid fa-ellipsis-vertical"></i>
                                                            </button>
                                                            <ul class="dropdown-menu border shadow-none text-reg ">
                                                                <li>
                                                                    <a class="dropdown-item text-14"
                                                                        href="create-course.php?edit=<?php echo $courses['courseID']; ?>">Edit</a>
                                                                </li>
                                                                <li><a class="dropdown-item text-danger" data-bs-toggle="modal"
                                                                        data-bs-target="#deleteModal">Delete</a></li>
                                                            </ul>
                                                        </div>

                                                    </div>

                                                    <!-- Course Image -->
                                                    <div class="course-image w-100 mb-3"
                                                        style="position: relative; overflow: hidden; border-radius: 10px; min-height: 150px; background-color:var(--primaryColor)">
                                                        <img src="../shared/assets/img/course-images/<?php echo $courses['courseImage']; ?>"
                                                            alt="Course Image"
                                                            style="width: 100%; height: 100%; object-fit: cover;">
                                                    </div>

                                                    <!-- Course Info -->
                                                    <h5 class="text-bold text-center mb-1"><?php echo $courses['courseCode']; ?>
                                                    </h5>
                                                    <p class="text-center text-reg mb-4"><?php echo $courses['courseTitle']; ?>
                                                    </p>

                                                    <!-- Prof -->
                                                    <div class="d-flex align-items-center text-decoration-none">
                                                        <div class="rounded-circle me-2 flex-shrink-0" style="width: 30px; height: 30px; background-color: #5ba9ff;
                                                    background: url('../shared/assets/pfp-uploads/<?php echo $courses['profPFP']; ?>') no-repeat center center;
                                                    background-size: cover;">
                                                        </div>
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <span class="text-sbold text-14" style="line-height: 1.2">
                                                                <?php echo $courses['profFirstName'] . " " . $courses['profMiddleName'] . " " . $courses['profLastName']; ?>
                                                            </span>
                                                            <small class="text-reg text-12" style="line-height: 1.2">
                                                                Instructor
                                                            </small>
                                                        </div>
                                                    </div>

                                                    <!-- Sched -->
                                                    <div class="d-flex align-items-center text-decoration-none mt-3">
                                                        <span
                                                            class="material-symbols-outlined me-1 text-center d-flex align-items-center"
                                                            style="width: 30px; height: 30px;">
                                                            calendar_today
                                                        </span>
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <span class="text-reg text-14">
                                                                <span><?php echo isset($courses['courseschedule']) ? nl2br($courses['courseschedule']) : 'No schedule yet'; ?></span>
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <!-- Quests -->
                                                    <div class="row mb-3 mt-3">
                                                        <div class="col">
                                                            <?php if (mysqli_num_rows($selectLimitAssessmentResult) > 0) {?>
                                                                <label class="text-med text-14 mb-1">Active Assessments</label>
                                                                <?php
                                                                mysqli_data_seek($selectLimitAssessmentResult, 0);
                                                                while ($activities = mysqli_fetch_assoc($selectLimitAssessmentResult)) {
                                                                    ?>
                                                                    
                                                                    <div
                                                                        class="todo-card-course-info d-flex align-items-stretch rounded-2 mt-2 w-100">
                                                                        <div class="date-section text-sbold text-14 px-1"
                                                                            style="text-transform:uppercase; ">
                                                                            <?php echo $activities['assessmentDeadline']; ?>
                                                                        </div>
                                                                        <div
                                                                            class="d-flex align-items-center flex-wrap flex-grow-1 p-2 gap-3">
                                                                            <div class="flex-grow-1 px-2">
                                                                                <div class="text-med text-14 truncate">
                                                                                    <?php echo $activities['assessmentTitle']; ?>
                                                                                </div>
                                                                            </div>

                                                                            <?php
                                                                            $type = strtolower(trim($activities['type']));
                                                                            $link = "#";

                                                                            if ($type === 'task') {
                                                                                $link = "task-info.php?assignmentID=" . $activities['assignmentID'];
                                                                            } elseif ($type === 'test') {
                                                                                $link = "test-info.php?testID=" . $activities['testID'];
                                                                            }
                                                                            ?>

                                                                            <div class="d-none d-lg-block">
                                                                                <a href="<?php echo $link; ?>"
                                                                                    class="text-decoration-none  d-flex align-items-center">
                                                                                    <i class="fa-solid fa-arrow-right text-reg text-12 pe-2"
                                                                                        style="color: var(--black);"></i>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <?php
                                                                }
                                                            } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    } else { ?>
                                        <script>
                                            window.location.href = "404.php"
                                        </script>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <!-- RIGHT: Tabs and Content -->
                                <div class="col-md-8">
                                    <div class="tab-section">
                                        <div class="tab-carousel-wrapper">
                                            <div class="d-flex align-items-center position-relative"
                                                style="gap: 10px; width: 100%;">
                                                <!-- Left Arrow -->
                                                <button id="desktopScrollLeftBtn" class="scroll-arrow-btn d-none"
                                                    aria-label="Scroll Left"
                                                    style="background: none; border: none; color: var(--black); flex-shrink: 0; margin-top:-2px;">
                                                    <i class="fa-solid fa-chevron-left"></i>
                                                </button>

                                                <!-- Scrollable Tabs -->
                                                <div class="tab-scroll flex-grow-1 overflow-auto nav-tabs"
                                                    style="scroll-behavior: smooth; white-space: nowrap; flex: 1; ">
                                                    <ul class="nav custom-nav-tabs flex-nowrap w-100" id="myTab"
                                                        role="tablist"
                                                        style="display: inline-flex; white-space: nowrap; justify-content: space-between;">
                                                        <li class="nav-item">
                                                            <a class="nav-link text-14 <?php echo ($activeTab == 'announcements') ? 'active' : ''; ?>"
                                                                data-bs-toggle="tab" data-bs-target="#announcements"
                                                                href="#announcements">Announcements</a>
                                                        </li>

                                                        <li class="nav-item">
                                                            <a class="nav-link text-14 <?php echo ($activeTab == 'lessons') ? 'active' : ''; ?>"
                                                                data-bs-toggle="tab" data-bs-target="#lessons"
                                                                href="#lessons">Lessons</a>
                                                        </li>

                                                        <li class="nav-item">
                                                            <a class="nav-link text-14 <?php echo ($activeTab == 'todo') ? 'active' : ''; ?>"
                                                                data-bs-toggle="tab" data-bs-target="#todo"
                                                                href="#todo">Assessments</a>
                                                        </li>

                                                        <li class="nav-item">
                                                            <a class="nav-link text-14 <?php echo ($activeTab == 'attachments') ? 'active' : ''; ?>"
                                                                data-bs-toggle="tab" data-bs-target="#attachments"
                                                                href="#attachments">Files</a>
                                                        </li>

                                                        <li class="nav-item">
                                                            <a class="nav-link text-14 <?php echo ($activeTab == 'link') ? 'active' : ''; ?>"
                                                                data-bs-toggle="tab" data-bs-target="#link"
                                                                href="#link">Links</a>
                                                        </li>

                                                        <li class="nav-item">
                                                            <a class="nav-link text-14 <?php echo ($activeTab == 'records') ? 'active' : ''; ?>"
                                                                data-bs-toggle="tab" data-bs-target="#records"
                                                                href="#records">Records</a>
                                                        </li>

                                                        <li class="nav-item nav-leaderboard">
                                                            <a class="nav-link text-14 <?php echo ($activeTab == 'leaderboard') ? 'active' : ''; ?>"
                                                                data-bs-toggle="tab" data-bs-target="#leaderboard"
                                                                href="#leaderboard">Leaderboard</a>
                                                        </li>

                                                        <li class="nav-item nav-student">
                                                            <a class="nav-link text-14 <?php echo ($activeTab == 'student') ? 'active' : ''; ?>"
                                                                data-bs-toggle="tab" data-bs-target="#student"
                                                                href="#student">Students</a>
                                                        </li>
                                                    </ul>
                                                </div>

                                                <!-- Right Arrow -->
                                                <button id="desktopScrollRightBtn" class="scroll-arrow-btn"
                                                    aria-label="Scroll Right"
                                                    style="background: none; border: none; color: var(--black); flex-shrink: 0; margin-top:-2px;">
                                                    <i class="fa-solid fa-chevron-right"></i>
                                                </button>
                                            </div>

                                        </div>

                                        <!-- Tab Content -->
                                        <div class="tab-content mt-3" id="myTabContent">

                                            <!-- Announcements -->
                                            <div class="tab-pane fade <?php echo ($activeTab == 'announcements') ? 'show active' : ''; ?>"
                                                id="announcements" role="tabpanel">
                                                <?php include 'course-info/announcements.php'; ?>
                                            </div>

                                            <!-- Lessons -->
                                            <div class="tab-pane fade <?php echo ($activeTab == 'lessons') ? 'show active' : ''; ?>"
                                                id="lessons" role="tabpanel">
                                                <?php include 'course-info/lessons.php'; ?>
                                            </div>

                                            <!-- To-do -->
                                            <div class="tab-pane fade <?php echo ($activeTab == 'todo') ? 'show active' : ''; ?>"
                                                id="todo" role="tabpanel">
                                                <?php include 'course-info/to-do.php'; ?>
                                            </div>

                                            <!-- Attachments -->
                                            <div class="tab-pane fade <?php echo ($activeTab == 'attachments') ? 'show active' : ''; ?>"
                                                id="attachments" role="tabpanel">
                                                <?php include 'course-info/attachments.php'; ?>
                                            </div>

                                            <!-- Links -->
                                            <div class="tab-pane fade <?php echo ($activeTab == 'link') ? 'show active' : ''; ?>"
                                                id="link" role="tabpanel">
                                                <?php include 'course-info/link.php'; ?>
                                            </div>

                                            <!-- Records -->
                                            <div class="tab-pane fade <?php echo ($activeTab == 'records') ? 'show active' : ''; ?>"
                                                id="records" role="tabpanel">
                                                <?php include 'course-info/records.php'; ?>
                                            </div>

                                            <!-- Leaderboard -->
                                            <div class="tab-pane fade <?php echo ($activeTab == 'leaderboard') ? 'show active' : ''; ?>"
                                                id="leaderboard" role="tabpanel">
                                                <?php include 'course-info/leaderboard.php'; ?>
                                            </div>

                                            <!-- Students -->
                                            <div class="tab-pane fade <?php echo ($activeTab == 'student') ? 'show active' : ''; ?>"
                                                id="student" role="tabpanel">
                                                <?php include 'course-info/student.php'; ?>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Delete Modal -->
    <div class="modal" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            style="transform: scale(0.8); border:none !important; box-shadow:none !important;"></button>
                    </div>
                    <div class="modal-body d-flex flex-column justify-content-center align-items-center text-center">
                        <span class="mt-4 text-bold text-22">This action cannot be undone.</span>
                        <span class="mb-4 text-reg text-14">Are you sure you want to delete this course?</span>
                        <input type="hidden" name="courseID" value="<?php echo $courseID; ?>">
                    </div>
                    <div class="modal-footer text-sbold text-18">
                        <button type="button" class="btn rounded-pill px-4"
                            style="background-color: var(--primaryColor); border: 1px solid var(--black);"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="deleteCourse" class="btn rounded-pill px-4"
                            style="background-color: rgba(248, 142, 142, 1); border: 1px solid var(--black); color: var(--black);">
                            Delete
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const tabContainer = document.getElementById('mobileTabScroll');
            const scrollLeftBtn = document.getElementById('scrollLeftBtn');
            const scrollRightBtn = document.getElementById('scrollRightBtn');

            function updateArrowVisibility() {
                if (!tabContainer) return;

                scrollLeftBtn.classList.toggle('d-none', tabContainer.scrollLeft === 0);
                scrollRightBtn.classList.toggle('d-none', tabContainer.scrollLeft + tabContainer.clientWidth >= tabContainer.scrollWidth);
            }

            scrollLeftBtn.addEventListener('click', () => {
                tabContainer.scrollBy({
                    left: -100,
                    behavior: 'smooth'
                });
            });

            scrollRightBtn.addEventListener('click', () => {
                tabContainer.scrollBy({
                    left: 100,
                    behavior: 'smooth'
                });
            });

            tabContainer.addEventListener('scroll', updateArrowVisibility);

            updateArrowVisibility(); // Initial check
        });

        document.addEventListener("DOMContentLoaded", function () {
            // Select all headers with collapse triggers
            const headers = document.querySelectorAll('.course-card-mobile [data-bs-toggle="collapse"]');

            headers.forEach(header => {
                const icon = header.querySelector("i");
                const targetId = header.getAttribute("href") || header.getAttribute("data-bs-target");
                const collapseEl = document.querySelector(targetId);

                if (!collapseEl) return;

                collapseEl.addEventListener("show.bs.collapse", function () {
                    icon.classList.remove("fa-chevron-down");
                    icon.classList.add("fa-chevron-up");
                });

                collapseEl.addEventListener("hide.bs.collapse", function () {
                    icon.classList.remove("fa-chevron-up");
                    icon.classList.add("fa-chevron-down");
                });
            });
        });
    </script>

    <!-- JS for Desktop Scroll Buttons -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const desktopTabScroll = document.querySelector(".tab-scroll");
            const desktopScrollLeftBtn = document.getElementById("desktopScrollLeftBtn");
            const desktopScrollRightBtn = document.getElementById("desktopScrollRightBtn");

            function updateDesktopArrowVisibility() {
                if (!desktopTabScroll) return;
                desktopScrollLeftBtn.classList.toggle("d-none", desktopTabScroll.scrollLeft === 0);
                desktopScrollRightBtn.classList.toggle(
                    "d-none",
                    desktopTabScroll.scrollLeft + desktopTabScroll.clientWidth >= desktopTabScroll.scrollWidth
                );
            }

            desktopScrollLeftBtn.addEventListener("click", () => {
                desktopTabScroll.scrollBy({
                    left: -150,
                    behavior: "smooth"
                });
            });

            desktopScrollRightBtn.addEventListener("click", () => {
                desktopTabScroll.scrollBy({
                    left: 150,
                    behavior: "smooth"
                });
            });

            desktopTabScroll.addEventListener("scroll", updateDesktopArrowVisibility);

            updateDesktopArrowVisibility(); // Initial check
        });

        // JS For Course Card Sticky

        document.addEventListener('DOMContentLoaded', function () {
            const courseCardDesktop = document.querySelector('.course-card-desktop');

            if (courseCardDesktop) {
                const windowHeight = window.innerHeight;
                const courseCardHeight = courseCardDesktop.offsetHeight;

                // Add the padding (16px top + 16px bottom)
                const totalHeightWithPadding = courseCardHeight + 32 + 24 + 24 + 24 + 24; //  padding

                // Calculate the difference in height if the course card is larger than the window
                const heightDifference = totalHeightWithPadding - windowHeight;

                // If the course card (with padding) is larger than the window, set 'top' to the negative difference
                if (heightDifference > 0) {
                    courseCardDesktop.style.top = `-${heightDifference}px`;
                } else {
                    courseCardDesktop.style.top = '0';
                }
            }
        });
    </script>

    <!-- Toast Handling -->
    <?php if (!empty($toastMessage)): ?>
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                const container = document.getElementById("toastContainer");
                if (!container) return;

                const alert = document.createElement("div");
                alert.className = `alert mb-2 shadow-lg text-med text-12 d-flex align-items-center justify-content-center gap-2 px-3 py-2 <?= $toastType ?>`;
                alert.role = "alert";
                alert.innerHTML = `
            <i class="bi <?= ($toastType === 'alert-success') ? 'bi-check-circle-fill' : 'bi-x-circle-fill'; ?> fs-6"></i>
            <span><?= addslashes($toastMessage) ?></span>
        `;
                container.appendChild(alert);

                setTimeout(() => alert.remove(), 3000);
            });
        </script>
    <?php endif; ?>

    <script>
        (() => {
            const searchInput = document.getElementById('leaderboardSearch');
            const items = document.querySelectorAll('.leaderboard-item');
            const noResults = document.getElementById('leaderboard-no-results');

            searchInput.addEventListener('keyup', () => {
                const term = searchInput.value.toLowerCase();
                let anyVisible = false;

                items.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    const isMatch = text.includes(term);

                    if (isMatch) {
                        anyVisible = true;
                        item.classList.add('w-100');
                        item.style.setProperty("display", "", "important");
                    } else {
                        item.style.setProperty("display", "none", "important");
                    }

                    if (term == '') {
                        item.classList.remove('w-100');
                    }
                });

                noResults.style.display = anyVisible ? 'none' : 'block';
            });
        })();
    </script>

</body>

</html>