<?php
$activePage = 'courseInfo';

include("shared/assets/database/connect.php");
session_start();
include("shared/assets/processes/session-process.php");

$userID = 2;

if (isset($_GET['courseID'])) {
    $courseID = $_GET['courseID'];
    $selectCourseQuery = "SELECT 
    courses.*, 
   	profInfo.firstName AS profFirstName,
    profInfo.middleName AS profMiddleName,
    profInfo.lastName AS profLastName,
    profInfo.profilePicture AS profPFP,
    SUBSTRING_INDEX(courses.schedule, ' ', 1)  AS courseDays,
    SUBSTRING_INDEX(courses.schedule, ' ', -1) AS courseTime
    FROM courses
    INNER JOIN userinfo AS profInfo
    	ON courses.userID = profInfo.userID
    INNER JOIN enrollments
    	ON courses.courseID = enrollments.courseID
    WHERE enrollments.userID = '$userID' AND enrollments.courseID = '$courseID';
;
";
    $selectCourseResult = executeQuery($selectCourseQuery);

    $selectAssessmentQuery = "SELECT
    assignments.assignmentID,
    assessments.*,
    assessments.assessmentTitle AS assessmentTitle,
    todo.*,
    todo.title AS todoTitle,
    courses.courseCode,
    DATE_FORMAT(assessments.deadline, '%b %e') AS assessmentDeadline
    FROM assessments
    INNER JOIN courses
        ON assessments.courseID = courses.courseID
    INNER JOIN todo 
        ON assessments.assessmentID = todo.assessmentID
    INNER JOIN assignments
    	ON assignments.assessmentID = todo.assessmentID
    WHERE todo.userID = '$userID' AND todo.status = 'Pending' AND courses.courseID = '$courseID'
    ORDER BY assessments.assessmentID DESC
";
    $selectAssessmentResult = executeQuery($selectAssessmentQuery);

    $selectLimitAssessmentQuery = $selectAssessmentQuery . " LIMIT 1";
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

    $TotalPlacementQuery = "SELECT 
	userinfo.profilePicture,
    userinfo.firstName,
    userinfo.middleName,
    userinfo.lastName,
    enrollments.userID,
    SUM(leaderboard.xpPoints) AS totalPoints
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

    $selectTopFourToTenQuery = $TotalPlacementQuery . " 7 OFFSET 3;";
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
?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | My Course Info</title>

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

                    <div class="container-fluid py-3 ms-2 overflow-y-auto" style="white-space: nowrap; scrollbar-width: none; -ms-overflow-style: none; scroll-behavior: smooth;">
                        <div class="row">
                            <div class="row mt-0">

                                <!-- LEFT: Course Card -->
                                <div class="col-md-4">
                                    <?php
                                    if (mysqli_num_rows($selectCourseResult) > 0) {
                                        while ($courses = mysqli_fetch_assoc($selectCourseResult)) {
                                    ?>
                                            <!-- Mobile Dropdown Course Card -->
                                            <div class="course-card-mobile d-block d-md-none">
                                                <div class="course-card p-0"
                                                    style="width: 100%; margin: 0 auto; outline: 1px solid var(--black); border-radius: 10px; border-bottom-left-radius: 0; border-bottom-right-radius: 0; overflow: hidden;">
                                                    <!-- Yellow header section -->
                                                    <div id="dropdownHeader"
                                                        class="d-flex justify-content-between align-items-center px-3 py-2"
                                                        style="background-color: var(--primaryColor);">

                                                        <div class="flex-grow-1 text-center">
                                                            <h5 class="text-bold mb-1"><?php echo $courses['courseCode']; ?></h5>
                                                            <p class="text-reg mb-0"><?php echo $courses['courseTitle']; ?></p>
                                                        </div>
                                                        <button class="btn p-0 d-md-none" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#mobileCourseCard"
                                                            aria-expanded="false" aria-controls="mobileCourseCard">
                                                            <i class="fa fa-chevron-down text-dark"></i>
                                                        </button>
                                                    </div>

                                                    <!-- White dropdown section -->
                                                    <div class="collapse d-md-block px-3 pt-2 pb-3 bg-white"
                                                        id="mobileCourseCard">
                                                        <div class="course-image w-100 mb-3"
                                                            style="height: 200px; overflow: hidden; border-radius: 10px;">
                                                            <img src="shared/assets/img/home/<?php echo $courses['courseImage']; ?>" alt="Course Image"
                                                                class="img-fluid w-100 h-100" style="object-fit: cover;">
                                                        </div>

                                                        <div class="d-flex align-items-center mb-2">
                                                            <div class="avatar-image">
                                                                <img src="shared/assets/pfp-uploads/<?php echo $courses['profPFP']; ?>"
                                                                    alt="Instructor Image" class="img-fluid">
                                                            </div>
                                                            <div class="ms-2">
                                                                <strong class="text-sbold" style="font-size: 12px;"><?php echo $courses['profFirstName'] . " " . $courses['profMiddleName'] . " " . $courses['profLastName']; ?></strong>
                                                                <br>
                                                                <small class="text-reg">Professor</small>
                                                            </div>
                                                        </div>

                                                        <div class="mb-2">
                                                            <div class="d-flex align-items-center">
                                                                <img src="shared/assets/img/courseInfo/calendar.png"
                                                                    alt="Calendar" width="20" class="me-2">
                                                                <div>
                                                                    <small class="text-reg"><?php echo $courses['courseDays']; ?></span> <?php echo $courses['courseTime']; ?></small>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mb-2">
                                                            <label class="text-reg small mb-1">Class Standing</label>
                                                            <div
                                                                class="class-standing d-flex justify-content-between align-items-center">
                                                                <span><img src="shared/assets/img/courseInfo/star.png"
                                                                        alt="Star" width="14" class="me-2">1ST</span>
                                                                <?php
                                                                if (mysqli_num_rows($selectLeaderboardResult) > 0) {
                                                                    while ($points = mysqli_fetch_assoc($selectLeaderboardResult)) {
                                                                ?>
                                                                        <span class="fw-medium"><?php echo $points['totalPoints']; ?> WBSTRS</span>
                                                                <?php
                                                                    }
                                                                }
                                                                ?>
                                                                <span><i class="fas fa-arrow-right"></i></span>
                                                            </div>
                                                        </div>
                                                        <label class="text-reg small mb-1 ">To-do</label>
                                                        <?php
                                                        if (mysqli_num_rows($selectLimitAssessmentResult) > 0) {
                                                            while ($activities = mysqli_fetch_assoc($selectLimitAssessmentResult)) {
                                                        ?>
                                                                <div class="todo-card d-flex align-items-stretch rounded-4 mt-2">
                                                                    <div class="date-section text-sbold text-12"><?php echo $activities['assessmentDeadline']; ?></div>
                                                                    <div
                                                                        class="d-flex align-items-center flex-wrap flex-grow-1 p-2 gap-3">
                                                                        <div class="flex-grow-1 px-2">
                                                                            <div class="text-sbold text-12"><?php echo $activities['assessmentTitle']; ?></div>
                                                                        </div>
                                                                        <div class="course-badge rounded-pill px-3 text-reg text-12">
                                                                            <?php echo $activities['type']; ?>

                                                                            <?php
                                                                            $type = strtolower(trim($activities['type']));
                                                                            $link = "#";

                                                                            if ($type === 'task') {
                                                                                $link = "assignment.php?assignmentID=" . $activities['assessmentID'];
                                                                            } elseif ($type === 'test') {
                                                                                $link = "test.php?testID=" . $activities['assessmentID'];
                                                                            }
                                                                            ?>
                                                                        </div>

                                                                        <!-- Arrow icon that always shows and aligns to the right -->
                                                                        <div class="ms-auto">
                                                                            <a href="<?php echo $link; ?>" class="text-decoration-none">
                                                                                <i class="fa-solid fa-arrow-right text-reg text-12 pe-2" style="color: var(--black);"></i>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                        <?php
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                </div>

                                            </div>

                                            <!-- Desktop Course Card -->
                                            <div class="course-card-desktop d-none d-md-block">
                                                <div class="course-card mx-auto"
                                                    style="outline: 1px solid var(--black); border-radius: 10px;">
                                                    <!-- Back Button -->
                                                    <div class="mb-3">
                                                        <a href="course.php" class="text-dark fs-5">
                                                            <i class="fas fa-arrow-left"></i>
                                                        </a>
                                                    </div>

                                                    <!-- Course Image -->
                                                    <div class="course-image w-100 mb-3"
                                                        style="height: 200px; overflow: hidden; border-radius: 10px;">
                                                        <img src="shared/assets/img/home/<?php echo $courses['courseImage']; ?>" alt="Course Image"
                                                            class="img-fluid w-100 h-100" style="object-fit: cover;">
                                                    </div>

                                                    <!-- Course Info -->
                                                    <h5 class="text-bold text-center mb-1"><?php echo $courses['courseCode']; ?></h5>
                                                    <p class="text-center text-reg mb-3"><?php echo $courses['courseTitle']; ?></p>

                                                    <div class="row mb-2">
                                                        <div class="col">
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar-image">
                                                                    <img src="shared/assets/pfp-uploads/<?php echo $courses['profPFP']; ?>"
                                                                        alt="Instructor Image" class="img-fluid">
                                                                </div>
                                                                <div class="ms-2">
                                                                    <strong class="text-sbold text-12"><?php echo $courses['profFirstName'] . " " . $courses['profMiddleName'] . " " . $courses['profLastName']; ?></strong><br>
                                                                    <small class="text-reg">Professor</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <div class="col">
                                                            <div class="d-flex align-items-center">
                                                                <img src="shared/assets/img/courseInfo/calendar.png"
                                                                    alt="Calendar" width="20" class="me-2">
                                                                <div>
                                                                    <small class="text-reg"><?php echo $courses['courseDays']; ?></span> <?php echo $courses['courseTime']; ?></small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <div class="col">
                                                            <label class="text-reg small mb-1">Class Standing</label>
                                                            <div
                                                                class="text-reg class-standing d-flex justify-content-between align-items-center">
                                                                <span><img src="shared/assets/img/courseInfo/star.png"
                                                                        alt="Star" width="14" class="me-2">1ST</span>
                                                                <?php
                                                                if (mysqli_num_rows($selectLeaderboardResult) > 0) {
                                                                    mysqli_data_seek($selectLeaderboardResult, 0);
                                                                    while ($points = mysqli_fetch_assoc($selectLeaderboardResult)) {
                                                                ?>
                                                                        <span class="fw-medium"><?php echo $points['totalPoints']; ?> WBSTRS</span>
                                                                <?php
                                                                    }
                                                                }
                                                                ?>
                                                                <span><i class="fas fa-arrow-right"></i></span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-12">
                                                            <label class="text-reg small mb-1">To-do</label>
                                                            <?php if (mysqli_num_rows($selectLimitAssessmentResult) > 0) {
                                                                mysqli_data_seek($selectLimitAssessmentResult, 0);
                                                                while ($activities = mysqli_fetch_assoc($selectLimitAssessmentResult)) {
                                                            ?>
                                                                    <div class="todo-card d-flex align-items-stretch rounded-4">
                                                                        <div class="date-section text-sbold text-12"><?php echo $activities['assessmentDeadline']; ?></div>
                                                                        <div
                                                                            class="d-flex align-items-center flex-wrap flex-grow-1 p-2 gap-3">
                                                                            <div class="flex-grow-1 px-2">
                                                                                <div class="text-sbold text-12"><?php echo $activities['assessmentTitle']; ?></div>
                                                                            </div>
                                                                            <div
                                                                                class="course-badge rounded-pill px-3 text-reg text-12">
                                                                                <?php echo $activities['type']; ?>

                                                                                <?php
                                                                                $type = strtolower(trim($activities['type']));
                                                                                $link = "#";

                                                                                if ($type === 'task') {
                                                                                    $link = "assignment.php?assignmentID=" . $activities['assessmentID'];
                                                                                } elseif ($type === 'test') {
                                                                                    $link = "test.php?testID=" . $activities['assessmentID'];
                                                                                }
                                                                                ?>

                                                                            </div>
                                                                            <div class="d-none d-lg-block"
                                                                                style="margin-left: auto; margin-right: 10px;">
                                                                                <a href="<?php echo $link; ?>" class="text-decoration-none">
                                                                                    <i class="fa-solid fa-arrow-right text-reg text-12 pe-2" style="color: var(--black);"></i>
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

                                        <div class="tab-carousel-wrapper position-relative d-md-none">
                                            <!-- Left Arrow -->
                                            <button id="scrollLeftBtn" class="scroll-arrow-btn start-0 d-none"
                                                aria-label="Scroll Left">
                                                <i class="fa-solid fa-chevron-left"></i>
                                            </button>

                                            <!-- Right Arrow -->
                                            <button id="scrollRightBtn" class="scroll-arrow-btn end-0"
                                                aria-label="Scroll Right">
                                                <i class="fa-solid fa-chevron-right"></i>
                                            </button>

                                            <!-- Tab Navigation -->
                                            <ul class="nav nav-tabs custom-nav-tabs mb-3" id="mobileTabScroll"
                                                role="tablist">
                                                <li class="nav-item me-3" role="presentation">
                                                    <a class="nav-link <?php echo $navState == "active" ? "" : "active"; ?>" id="announcements-tab"
                                                        data-bs-toggle="tab" href="#announcements" role="tab"
                                                        aria-controls="announcements" aria-selected="true">
                                                        Announcements
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link" id="lessons-tab" data-bs-toggle="tab"
                                                        href="#lessons" role="tab" aria-controls="lessons"
                                                        aria-selected="false">
                                                        Lessons
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link" id="todo-tab" data-bs-toggle="tab" href="#todo"
                                                        role="tab" aria-controls="todo" aria-selected="false">
                                                        To-do
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link" id="files-tab" data-bs-toggle="tab"
                                                        href="#files" role="tab" aria-controls="files"
                                                        aria-selected="false">
                                                        Files
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link" id="link-tab" data-bs-toggle="tab"
                                                        href="#link" role="tab" aria-controls="link"
                                                        aria-selected="false">
                                                        Links
                                                    </a>
                                                </li>
                                                <li class="nav-item nav-leaderboard" role="presentation">
                                                    <a class="nav-link <?php echo $navState == "active" ? "active" : ""; ?>" id="leaderboard-tab" data-bs-toggle="tab"
                                                        href="#leaderboard" role="tab" aria-controls="leaderboard"
                                                        aria-selected="false">
                                                        Leaderboard
                                                    </a>
                                                </li>
                                                <li class="nav-item nav-report" role="presentation">
                                                    <a class="nav-link" id="report-tab" data-bs-toggle="tab"
                                                        href="#report" role="tab" aria-controls="report"
                                                        aria-selected="false">
                                                        Report
                                                    </a>
                                                </li>
                                                <li class="nav-item nav-student" role="presentation">
                                                    <a class="nav-link" id="student-tab" data-bs-toggle="tab"
                                                        href="#student" role="tab" aria-controls="student"
                                                        aria-selected="false">
                                                        Students
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>

                                        <!-- Desktop Tabs -->
                                        <div class="tab-carousel-wrapper d-none d-md-block">
                                            <div class="tab-scroll">
                                                <ul class="nav nav-tabs custom-nav-tabs mb-3 flex-nowrap" id="myTab" role="tablist">
                                                    <li class="nav-item">
                                                        <a class="nav-link <?php echo $navState == "active" ? "" : "active"; ?>" id="announcements-tab" data-bs-toggle="tab" href="#announcements" role="tab">Announcements</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" id="lessons-tab" data-bs-toggle="tab" href="#lessons" role="tab">Lessons</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" id="todo-tab" data-bs-toggle="tab" href="#todo" role="tab">To-do</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" id="files-tab" data-bs-toggle="tab" href="#files" role="tab">Files</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" id="link-tab" data-bs-toggle="tab" href="#link" role="tab">Links</a>
                                                    </li>
                                                    <li class="nav-item nav-leaderboard">
                                                        <a class="nav-link <?php echo $navState == "active" ? "active" : ""; ?>" id="leaderboard-tab" data-bs-toggle="tab" href="#leaderboard" role="tab">Leaderboard</a>
                                                    </li>
                                                    <li class="nav-item nav-report">
                                                        <a class="nav-link" id="report-tab" data-bs-toggle="tab" href="#report" role="tab">Report</a>
                                                    </li>
                                                    <li class="nav-item nav-report">
                                                        <a class="nav-link" id="student-tab" data-bs-toggle="tab" href="#student" role="tab">Students</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <!-- Tab Content -->
                                        <div class="tab-content" id="myTabContent">

                                            <!-- Announcements -->
                                            <div class="tab-pane fade <?php echo $navState == "active" ? "" : "show active"; ?>" id="announcements" role="tabpanel">
                                                <?php include 'course-info-contents/announcements.php'; ?>
                                            </div>

                                            <!-- Lessons -->
                                            <div class="tab-pane fade" id="lessons" role="tabpanel">
                                                <?php include 'course-info-contents/lessons.php'; ?>
                                            </div>

                                            <!-- To-do -->
                                            <div class="tab-pane fade" id="todo" role="tabpanel">
                                                <?php include 'course-info-contents/to-do.php'; ?>
                                            </div>

                                            <!-- Files -->
                                            <div class="tab-pane fade" id="files" role="tabpanel">
                                                <?php include 'course-info-contents/files.php'; ?>
                                            </div>

                                            <!-- Link -->
                                            <div class="tab-pane fade" id="link" role="tabpanel">
                                                <?php include 'course-info-contents/link.php'; ?>
                                            </div>

                                            <!-- Leaderboard -->
                                            <div class="tab-pane fade <?php echo $navState == "active" ? "show active" : ""; ?>" id="leaderboard" role="tabpanel">
                                                <?php include 'course-info-contents/leaderboard.php'; ?>
                                            </div>

                                            <!-- Report -->
                                            <div class="tab-pane fade" id="report" role="tabpanel">
                                                <?php include 'course-info-contents/report.php'; ?>
                                            </div>

                                            <!-- Student -->
                                            <div class="tab-pane fade" id="student" role="tabpanel">
                                                <?php include 'course-info-contents/student.php'; ?>
                                            </div>
                                        </div>

                                        <!-- Bootstrap JS -->
                                        <script
                                            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
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

                                            const dropdownHeader = document.getElementById('dropdownHeader');
                                            const dropdownContent = document.getElementById('mobileCourseCard');
                                            const courseCard = dropdownHeader.closest('.course-card'); // find the parent card

                                            // Show dropdown
                                            dropdownContent.addEventListener('show.bs.collapse', function() {
                                                dropdownHeader.style.borderBottom = '1px solid var(--black)';
                                                courseCard.style.borderBottomLeftRadius = '10px';
                                                courseCard.style.borderBottomRightRadius = '10px';
                                            });

                                            // Hide dropdown
                                            dropdownContent.addEventListener('hide.bs.collapse', function() {
                                                dropdownHeader.style.borderBottom = 'none';
                                                courseCard.style.borderBottomLeftRadius = '0';
                                                courseCard.style.borderBottomRightRadius = '0';
                                            });

                                            document.addEventListener("DOMContentLoaded", function() {
                                                const collapseElement = document.getElementById("mobileCourseCard");
                                                const icon = document.querySelector('[data-bs-target="#mobileCourseCard"] i');

                                                collapseElement.addEventListener("show.bs.collapse", function() {
                                                    icon.classList.remove("fa-chevron-down");
                                                    icon.classList.add("fa-chevron-up");
                                                });

                                                collapseElement.addEventListener("hide.bs.collapse", function() {
                                                    icon.classList.remove("fa-chevron-up");
                                                    icon.classList.add("fa-chevron-down");
                                                });
                                            });
                                        </script>
                                        <!-- Hide sort on reports -->
                                        <script>
                                            document.addEventListener("DOMContentLoaded", function() {
                                                const sortBy = document.getElementById("header");
                                                const reportTab = document.getElementById("report-tab");
                                                const otherTabs = document.querySelectorAll('#myTab a[data-bs-toggle="tab"], #mobileTabScroll a[data-bs-toggle="tab"]');

                                                function toggleSortBy(tabId) {
                                                    if (tabId === "report") {
                                                        sortBy.classList.add("d-none"); // hide sort by
                                                    } else {
                                                        sortBy.classList.remove("d-none"); // show sort by
                                                    }
                                                }

                                                // Desktop & Mobile tab switching
                                                otherTabs.forEach(tab => {
                                                    tab.addEventListener("shown.bs.tab", function(e) {
                                                        const targetId = e.target.getAttribute("href").replace("#", "");
                                                        toggleSortBy(targetId);
                                                    });
                                                });

                                                // Initial check (in case report is active on load)
                                                const activeTab = document.querySelector('.nav-link.active');
                                                if (activeTab) {
                                                    toggleSortBy(activeTab.getAttribute("href").replace("#", ""));
                                                }
                                            });
                                        </script>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>