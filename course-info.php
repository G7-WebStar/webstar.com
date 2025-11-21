<?php
$activePage = 'course';
$activeTab = $_POST['activeTab'] ?? 'announcements';


include("shared/assets/database/connect.php");
date_default_timezone_set('Asia/Manila');
include("shared/assets/processes/session-process.php");

if (isset($_GET['courseID'])) {
    $courseID = $_GET['courseID'];
    $selectCourseQuery = "SELECT 
    courses.*, 
   	profInfo.firstName AS profFirstName,
    profInfo.middleName AS profMiddleName,
    profInfo.lastName AS profLastName,
    profInfo.profilePicture AS profPFP
    FROM courses
    INNER JOIN userinfo AS profInfo
    	ON courses.userID = profInfo.userID
    INNER JOIN enrollments
    	ON courses.courseID = enrollments.courseID
    WHERE enrollments.userID = '$userID' AND enrollments.courseID = '$courseID'
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

        case 'Missing':
            $todoOrderBy = "assessments.deadline DESC";
            break;

        default:
            $todoOrderBy = "assessments.deadline DESC";
            break;
    }

    // Status Todo
    switch ($statusFilter) {
        case 'Pending':
            $todoWhereStatus = "AND todo.status = 'Pending'";
            break;

        case 'Missing':
            $todoWhereStatus = "AND todo.status = 'Missing'";
            break;

        case 'Done':
            $todoWhereStatus = "AND todo.status IN ('Submitted', 'Returned', 'Graded')";
            break;

        default:
            $todoWhereStatus = "";
            break;
    }

    if ($sortTodo === 'Missing') {
        $todoWhereStatus = "AND todo.status = 'Missing'";
    }

    $selectAssessmentQuery = "SELECT
    tests.testID,
    assignments.assignmentID,
    assessments.*,
    assessments.assessmentTitle AS assessmentTitle,
    todo.*,
    courses.courseCode,
    DATE_FORMAT(assessments.deadline, '%b %e') AS assessmentDeadline
    FROM assessments
    INNER JOIN courses
        ON assessments.courseID = courses.courseID
    INNER JOIN todo 
        ON assessments.assessmentID = todo.assessmentID
    LEFT JOIN assignments
    	ON assignments.assessmentID = todo.assessmentID
    LEFT JOIN tests
        ON tests.assessmentID = todo.assessmentID
    WHERE todo.userID = '$userID' 
    AND courses.courseID = '$courseID' 
    AND (assessments.deadline >= NOW() OR todo.status = 'Missing')
    $todoWhereStatus
    ORDER BY $todoOrderBy
";
    $selectAssessmentResult = executeQuery($selectAssessmentQuery);

    $selectAssessmentQueryForCourseCard = "SELECT
    tests.testID,
    assignments.assignmentID,
    assessments.*,
    assessments.assessmentTitle AS assessmentTitle,
    todo.*,
    courses.courseCode,
    DATE_FORMAT(assessments.deadline, '%b %e') AS assessmentDeadline
    FROM assessments
    INNER JOIN courses
        ON assessments.courseID = courses.courseID
    INNER JOIN todo 
        ON assessments.assessmentID = todo.assessmentID
    LEFT JOIN assignments
    	ON assignments.assessmentID = todo.assessmentID
    LEFT JOIN tests
        ON tests.assessmentID = todo.assessmentID
    WHERE todo.userID = '$userID' 
    AND courses.courseID = '$courseID'AND todo.status = 'Pending'
    ORDER BY $todoOrderBy";

    $selectLimitAssessmentQuery = $selectAssessmentQueryForCourseCard;
    $selectLimitAssessmentResult = executeQuery($selectLimitAssessmentQuery);

    $selectLeaderboardQuery = "SELECT 
	courses.courseTitle,
    leaderboard.xpPoints AS totalPoints
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
    leaderboard.xpPoints AS totalPoints,
    leaderboard.previousRank,
    leaderboard.currentRank
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
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="shared/assets/css/course-Info.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="shared/assets/img/webstar-icon.png">
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


</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-0 p-md-3"
        style="background-color: var(--black);">

        <div class="row w-100">

            <?php include 'shared/components/sidebar-for-mobile.php'; ?>
            <?php include 'shared/components/sidebar-for-desktop.php'; ?>

            <div class="col main-container m-0 p-0 mx-0 mx-md-2 p-0 p-md-4 overflow-y-auto">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">

                    <?php include 'shared/components/navbar-for-mobile.php'; ?>

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
                                                            <!-- Course Image -->
                                                            <div class="course-image w-100 mb-3 mt-1"
                                                                style="position: relative; overflow: hidden; border-radius: 10px; min-height: 150px; background-color:var(--primaryColor)">
                                                                <img src="shared/assets/img/course-images/<?php echo $courses['courseImage']; ?>"
                                                                    alt="Course Image"
                                                                    style="width: 100%; height: 100%; object-fit: cover;">
                                                            </div>

                                                            <!-- Prof -->
                                                            <div class="d-flex align-items-center text-decoration-none">
                                                                <div class="rounded-circle me-2 flex-shrink-0" style="width: 25px; height: 25px; background-color: #5ba9ff;
                                                    background: url('shared/assets/pfp-uploads/<?php echo $courses['profPFP']; ?>') no-repeat center center;
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
                                                                        <span class="me-1 text-med">Thursdays</span> 8AM - 10AM
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
                                                                    <label class="text-med text-12">Quests</label>
                                                                    <?php if (mysqli_num_rows($selectLimitAssessmentResult) > 0) {
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
                                                                                        $link = "assignment.php?assignmentID=" . $activities['assessmentID'];
                                                                                    } elseif ($type === 'test') {
                                                                                        $link = "test.php?testID=" . $activities['assessmentID'];
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

                                                    <!-- Back Button -->
                                                    <div class="mb-3">
                                                        <a href="course.php" class="text-reg">
                                                            <i class="fas fa-arrow-left"></i>
                                                        </a>
                                                    </div>

                                                    <!-- Course Image -->
                                                    <div class="course-image w-100 mb-3"
                                                        style="position: relative; overflow: hidden; border-radius: 10px; min-height: 150px; background-color:var(--primaryColor)">
                                                        <img src="shared/assets/img/course-images/<?php echo $courses['courseImage']; ?>"
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
                                                    background: url('shared/assets/pfp-uploads/<?php echo $courses['profPFP']; ?>') no-repeat center center;
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
                                                                <span class="me-1 text-med">Thursdays</span> 8AM - 10AM
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <!-- Class Standing -->
                                                    <div class="row mb-3 mt-3">
                                                        <div class="col">
                                                            <label class="text-med text-14 mb-1">Class Standing</label>
                                                            <div class="class-standing text-14 d-flex justify-content-between align-items-center rounded-3 mt-2"
                                                                style="background-color:white">
                                                                <span class="material-symbols-outlined" style="font-size:20px">
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
                                                                            <span class="text-med"><?php echo $points['totalPoints']; ?>
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
                                                            <label class="text-med text-14 mb-1">Level</label>
                                                            <div class="class-standing text-14 align-items-center rounded-3 p-3 mt-2"
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
                                                            <label class="text-med text-14 mb-1">Quests</label>
                                                            <?php if (mysqli_num_rows($selectLimitAssessmentResult) > 0) {
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
                                                                data-bs-toggle="tab"
                                                                href="#announcements">Announcements</a>
                                                        </li>

                                                        <li class="nav-item">
                                                            <a class="nav-link text-14 <?php echo ($activeTab == 'lessons') ? 'active' : ''; ?>"
                                                                data-bs-toggle="tab" href="#lessons">Lessons</a>
                                                        </li>

                                                        <li class="nav-item">
                                                            <a class="nav-link text-14 <?php echo ($activeTab == 'todo') ? 'active' : ''; ?>"
                                                                data-bs-toggle="tab" href="#todo">Quests</a>
                                                        </li>

                                                        <li class="nav-item">
                                                            <a class="nav-link text-14 <?php echo ($activeTab == 'attachments') ? 'active' : ''; ?>"
                                                                data-bs-toggle="tab" href="#attachments">Files</a>
                                                        </li>

                                                        <li class="nav-item">
                                                            <a class="nav-link text-14 <?php echo ($activeTab == 'link') ? 'active' : ''; ?>"
                                                                data-bs-toggle="tab" href="#link">Links</a>
                                                        </li>

                                                        <li class="nav-item nav-leaderboard">
                                                            <a class="nav-link text-14 <?php echo ($activeTab == 'leaderboard') ? 'active' : ''; ?>"
                                                                data-bs-toggle="tab" href="#leaderboard">Leaderboard</a>
                                                        </li>

                                                        <li class="nav-item nav-report">
                                                            <a class="nav-link text-14 <?php echo ($activeTab == 'report') ? 'active' : ''; ?>"
                                                                data-bs-toggle="tab" href="#report">Report</a>
                                                        </li>

                                                        <li class="nav-item nav-student">
                                                            <a class="nav-link text-14 <?php echo ($activeTab == 'student') ? 'active' : ''; ?>"
                                                                data-bs-toggle="tab" href="#student">Students</a>
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
                                                <?php include 'course-info-contents/announcements.php'; ?>
                                            </div>

                                            <!-- Lessons -->
                                            <div class="tab-pane fade <?php echo ($activeTab == 'lessons') ? 'show active' : ''; ?>"
                                                id="lessons" role="tabpanel">
                                                <?php include 'course-info-contents/lessons.php'; ?>
                                            </div>

                                            <!-- Quests -->
                                            <div class="tab-pane fade <?php echo ($activeTab == 'todo') ? 'show active' : ''; ?>"
                                                id="todo" role="tabpanel">
                                                <?php include 'course-info-contents/to-do.php'; ?>
                                            </div>

                                            <!-- Attachments -->
                                            <div class="tab-pane fade <?php echo ($activeTab == 'attachments') ? 'show active' : ''; ?>"
                                                id="attachments" role="tabpanel">
                                                <?php include 'course-info-contents/attachments.php'; ?>
                                            </div>

                                            <!-- Links -->
                                            <div class="tab-pane fade <?php echo ($activeTab == 'link') ? 'show active' : ''; ?>"
                                                id="link" role="tabpanel">
                                                <?php include 'course-info-contents/link.php'; ?>
                                            </div>

                                            <!-- Leaderboard -->
                                            <div class="tab-pane fade <?php echo ($activeTab == 'leaderboard') ? 'show active' : ''; ?>"
                                                id="leaderboard" role="tabpanel">
                                                <?php include 'course-info-contents/leaderboard.php'; ?>
                                            </div>

                                            <!-- Report -->
                                            <div class="tab-pane fade <?php echo ($activeTab == 'report') ? 'show active' : ''; ?>"
                                                id="report" role="tabpanel">
                                                <?php include 'course-info-contents/report.php'; ?>
                                            </div>

                                            <!-- Students -->
                                            <div class="tab-pane fade <?php echo ($activeTab == 'student') ? 'show active' : ''; ?>"
                                                id="student" role="tabpanel">
                                                <?php include 'course-info-contents/student.php'; ?>
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
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
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

        document.addEventListener("DOMContentLoaded", function() {
            // Select all headers with collapse triggers
            const headers = document.querySelectorAll('.course-card-mobile [data-bs-toggle="collapse"]');

            headers.forEach(header => {
                const icon = header.querySelector("i");
                const targetId = header.getAttribute("href") || header.getAttribute("data-bs-target");
                const collapseEl = document.querySelector(targetId);

                if (!collapseEl) return;

                collapseEl.addEventListener("show.bs.collapse", function() {
                    icon.classList.remove("fa-chevron-down");
                    icon.classList.add("fa-chevron-up");
                });

                collapseEl.addEventListener("hide.bs.collapse", function() {
                    icon.classList.remove("fa-chevron-up");
                    icon.classList.add("fa-chevron-down");
                });
            });
        });
    </script>

    <!-- JS for Desktop Scroll Buttons -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
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

        document.addEventListener('DOMContentLoaded', function() {
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