<?php $activePage = 'home'; ?>
<?php

include('../shared/assets/database/connect.php');
include("../shared/assets/processes/prof-session-process.php");

$profInfoQuery = "SELECT firstName FROM userinfo
WHERE userID = $userID";
$profInfoResult = executeQuery($profInfoQuery);

$courses = [];
$result = executeQuery("SELECT * FROM courses WHERE userID = '$userID' AND isActive = '1' ORDER BY courseID DESC");
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $courseID = $row['courseID'];

        $countStudentsQuery = "SELECT COUNT(*) AS studentCount FROM enrollments  
        INNER JOIN courses ON enrollments.courseID = courses.courseID 
        WHERE enrollments.courseID = '$courseID'";
        $countStudentResult = executeQuery($countStudentsQuery);

        $studentCount = 0;

        if (mysqli_num_rows($countStudentResult) > 0) {
            $countRow = mysqli_fetch_assoc($countStudentResult);
            $studentCount = $countRow['studentCount'];
        }

        $courseScheduleQuery = "SELECT GROUP_CONCAT(
            CONCAT(
                courseschedule.day, ' ', 
                DATE_FORMAT(courseschedule.startTime, '%h:%i %p'), '-', 
                DATE_FORMAT(courseschedule.endTime, '%h:%i %p')
            ) 
            ORDER BY FIELD(courseschedule.day, 'Mon','Tue','Wed','Thu','Fri','Sat','Sun'), courseschedule.startTime
            SEPARATOR '\n'
        ) AS schedule FROM courseschedule WHERE courseID = '$courseID'";
        $courseScheduleResult = executeQuery($courseScheduleQuery);

        $courseSchedule;

        if (mysqli_num_rows($courseScheduleResult) > 0) {
            $scheduleRow = mysqli_fetch_assoc($courseScheduleResult);
            $courseSchedule = $scheduleRow['schedule'];
        }

        $row['studentCount'] = $studentCount;
        $row['schedule'] = $courseSchedule;
        $courses[] = $row;
    }
}
$totalCourses = count($courses);

//Active Assessments Tab
$assessments = [];
$activeAssessmentsTabQuery = "SELECT
assessments.type, 
tests.testID,
assignments.assignmentID,
courses.courseID, 
assessments.assessmentID, 
assessments.assessmentTitle,
courses.courseCode, 
DATE_FORMAT(assessments.deadline, '%b %e') AS assessmentDeadline 
FROM assessments
LEFT JOIN assignments ON assignments.assessmentID = assessments.assessmentID
    LEFT JOIN tests ON tests.assessmentID = assessments.assessmentID
INNER JOIN courses
	ON assessments.courseID = courses.courseID
WHERE assessments.deadline >= CURRENT_DATE AND isArchived = '0' AND courses.userID = '$userID'
";
$activeAssessmentsTabResult = executeQuery($activeAssessmentsTabQuery);
if ($activeAssessmentsTabResult && mysqli_num_rows($activeAssessmentsTabResult) > 0) {
    while ($rowAssessment = mysqli_fetch_assoc($activeAssessmentsTabResult)) {
        $assessmentCourseID = $rowAssessment['courseID'];
        $assessmentID = $rowAssessment['assessmentID'];

        $countStudentAssessmentQuery = "SELECT COUNT(*) AS courseStudents
                                        FROM enrollments
                                        INNER JOIN courses
	                                        ON courses.courseID = enrollments.courseID
                                        INNER JOIN assessments
                                            ON assessments.courseID = courses.courseID
                                        WHERE courses.userID = '$userID' AND enrollments.courseID = '$assessmentCourseID' AND assessments.assessmentID = '$assessmentID';";
        $countStudentAssessmentResult = executeQuery($countStudentAssessmentQuery);

        $studentAssessmentCount = 0;

        if (mysqli_num_rows($countStudentAssessmentResult) > 0) {
            $countRowAssessment = mysqli_fetch_assoc($countStudentAssessmentResult);
            $studentAssessmentCount = $countRowAssessment['courseStudents'];
        }

        $countSubmittedQuery = "SELECT COUNT(*) AS submittedTodo FROM todo 
                                WHERE assessmentID = '$assessmentID' AND (status = 'Returned' OR status = 'Submitted')";
        $countSubmittedResult = executeQuery($countSubmittedQuery);

        $submittedTodoCount = 0;

        if (mysqli_num_rows($countSubmittedResult) > 0) {
            $countRowSubmitted = mysqli_fetch_assoc($countSubmittedResult);
            $submittedTodoCount = $countRowSubmitted['submittedTodo'];
        }

        $rowAssessment['courseStudents'] = $studentAssessmentCount;
        $rowAssessment['submittedTodo'] = $submittedTodoCount;
        $assessments[] = $rowAssessment;
    }
}
$totalAssessments = count($assessments);
//END

$studentsTaughtQuery = "SELECT 
COUNT(enrollments.enrollmentID) AS studentsTaught
FROM enrollments
INNER JOIN courses
	ON courses.courseID = enrollments.courseID
WHERE courses.userID = '$userID';
";
$studentsTaughtResult = executeQuery($studentsTaughtQuery);

$coursesQuery = "SELECT COUNT(*) AS activeCourses FROM courses WHERE userID = '$userID'";
$coursesResult = executeQuery($coursesQuery);
$activeCoursesQuery = $coursesQuery .= " AND isActive = '1'";
$activeCoursesResult = executeQuery($activeCoursesQuery);

$countAssessmentsQuery = "SELECT 
COUNT(*) AS activeAssessments FROM assessments
INNER JOIN courses
	ON assessments.courseID = courses.courseID
WHERE courses.userID = $userID AND assessments.deadline > CURRENT_DATE AND courses.isActive = '1' AND isArchived = '0';
";
$countAssessmentsResult = executeQuery($countAssessmentsQuery);

$baseJoinGrading = " FROM todo 
INNER JOIN assessments
	ON todo.assessmentID = assessments.assessmentID
INNER JOIN courses
	ON assessments.courseID = courses.courseID
WHERE status = 'Submitted' AND  courses.userID = $userID";

$toGradeQuery = "SELECT COUNT(*) AS toGrade $baseJoinGrading;
";
$toGradeResult = executeQuery($toGradeQuery);

$toGradeTodayQuery = "SELECT COUNT(*) AS toGradeToday $baseJoinGrading AND (todo.updatedAt >= (CURRENT_TIMESTAMP - INTERVAL 1 DAY) AND todo.updatedAt < CURRENT_TIMESTAMP)";
$toGradeTodayResult = executeQuery($toGradeTodayQuery);

$activeAssessmentsQuery = "SELECT COUNT(*) FROM assessments
INNER JOIN courses
	ON assessments.courseID = courses.courseID
WHERE courses.userID = $userID AND assessments.deadline > CURRENT_DATE";
$activeAssessmentsResult = executeQuery($activeAssessmentsQuery);

$pendingTodoQuery = "SELECT COUNT(*) AS pending FROM todo 
INNER JOIN assessments
	ON todo.assessmentID = assessments.assessmentID
INNER JOIN courses
	ON assessments.courseID = courses.courseID
WHERE (status = 'Pending' OR status = 'Missing') AND courses.userID = $userID;";
$pendingTodoResult = executeQuery($pendingTodoQuery);
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/profIndex.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../shared/assets/img/webstar-icon.png">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0"
        rel="stylesheet" />

    <style>
        @media screen and (max-width: 767px) {
            .mobile-view {
                margin-bottom: 80px !important;
            }
        }

        @media (max-width: 1270px) {

            .left-side,
            .right-side {
                width: 100% !important;
                flex: 0 0 100% !important;
                max-width: 100% !important;
            }

            .right-side {
                margin-top: 10px
            }
        }

        @media (max-width: 576px) {
            .welcome-text-one {
                font-size: 18px !important;
                padding-top: 20px !important;
                padding-bottom: 10px !important;
                padding: 0px 30px;
            }

            .welcome-text-two {
                font-size: 14px !important;
                padding: 0px 60px;
            }
        }
    </style>


</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-0 p-md-3"
        style="background-color: var(--black);">

        <div class="row w-100">

            <!-- Sidebar (only shows on mobile) -->
            <?php include '../shared/components/prof-sidebar-for-mobile.php'; ?>

            <!-- Sidebar Column (fixed on desktop) -->
            <?php include '../shared/components/prof-sidebar-for-desktop.php'; ?>

            <!-- Main Container Column-->
            <div class="col main-container m-0 p-0 mx-0 mx-md-2 p-0 p-md-4 overflow-y-auto">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">

                    <!-- Navbar for mobile -->
                    <?php include '../shared/components/prof-navbar-for-mobile.php'; ?>

                    <div class="container-fluid py-1 overflow-y-auto row-padding-top mobile-view">
                        <div class="row">
                            <!-- Left side -->
                            <div class="container-fluid py-1 overflow-y-auto">
                                <div class="row m-0 p-0">
                                    <!-- Left side -->
                                    <div class="col-12 mb-3 mb-md-0 p-0">
                                        <div class="row ms-0 ms-md-4">
                                            <div
                                                class="d-flex flex-column flex-sm-row align-items-center justify-content-between w-100 text-center text-sm-start">

                                                <!-- Left side: Image + Text -->
                                                <div class="d-flex align-items-center mb-1 mb-sm-0">
                                                    <!-- Image hidden on mobile -->
                                                    <img src="../shared/assets/img/profIndex/folder.png" alt="Folder"
                                                        class="img-fluid rounded-circle me-3 folder-img d-none d-sm-block"
                                                        style="width:68px; height:68px;">
                                                    <?php
                                                    if (mysqli_num_rows($profInfoResult) > 0) {
                                                        while ($profInfo = mysqli_fetch_assoc($profInfoResult)) {
                                                            ?>
                                                            <div class="">
                                                                <div class="text-sbold text-22 welcome-text-one">Welcome back,
                                                                    Prof.
                                                                    <?php echo $profInfo['firstName']; ?>!
                                                                </div>
                                                                <div class="text-reg text-16 welcome-text-two">Resume your work
                                                                    and keep
                                                                    developing your
                                                                    course.</div>
                                                            </div>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Stats Section -->
                                    <div class="row stats m-0 p-0 mt-2 mt-md-5 mb-3 mb-md-0 align-items-center">
                                        <?php
                                        if (mysqli_num_rows($studentsTaughtResult) > 0) {
                                            while ($studentsTaught = mysqli_fetch_assoc($studentsTaughtResult)) {
                                                ?>
                                                <div class="col-12 col-md-3 mb-3 left-side">
                                                    <div class="d-flex align-items-center">
                                                        <span class="material-symbols-rounded"
                                                            style="color: var(--black); margin-right: 5px;">
                                                            people
                                                        </span>
                                                        <div class="stats-count text-22 text-bold">
                                                            <?php echo $studentsTaught['studentsTaught']; ?>
                                                        </div>
                                                    </div>
                                                    <div class="stats-label text-18 text-sbold">currently enrolled</div>
                                                    <div class="text-reg text-16">
                                                        <?php echo $studentsTaught['studentsTaught']; ?> students taught
                                                        all-time
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                        }
                                        ?>
                                        <?php
                                        if (mysqli_num_rows($activeCoursesResult) > 0) {
                                            while ($activeCourses = mysqli_fetch_assoc($activeCoursesResult)) {
                                                ?>
                                                <div class="col-12 col-md-3 mb-3 left-side">
                                                    <div class="d-flex align-items-center">
                                                        <span class="material-symbols-rounded"
                                                            style="color: var(--black); margin-right: 5px;">
                                                            folder
                                                        </span>
                                                        <div class="stats-count text-22 text-bold">
                                                            <?php echo $activeCourses['activeCourses']; ?>
                                                        </div>
                                                    </div>
                                                    <div class="stats-label text-18 text-sbold">active courses</div>
                                                    <?php
                                            }
                                        }
                                        ?>
                                            <?php
                                            if (mysqli_num_rows($coursesResult) > 0) {
                                                while ($totalCourse = mysqli_fetch_assoc($coursesResult)) {
                                                    ?>
                                                    <div class="text-reg text-16"><?php echo $totalCourse['activeCourses']; ?>
                                                        courses created all-time</div>
                                                </div>
                                                <?php
                                                }
                                            }
                                            ?>
                                        <?php
                                        if (mysqli_num_rows($toGradeResult) > 0) {
                                            $toGradeToday = mysqli_fetch_assoc($toGradeTodayResult);
                                            while ($toGrade = mysqli_fetch_assoc($toGradeResult)) {
                                                ?>
                                                <div class="col-12 col-md-3 mb-3 left-side">
                                                    <div class="d-flex align-items-center">
                                                        <span class="material-symbols-rounded"
                                                            style="color: var(--black); margin-right: 5px;">
                                                            rate_review
                                                        </span>
                                                        <div class="stats-count text-22 text-bold">
                                                            <?php echo $toGrade['toGrade']; ?>
                                                        </div>
                                                    </div>
                                                    <div class="stats-label text-18 text-sbold">tasks to grade</div>
                                                    <div class="text-reg text-16">+<?php echo $toGradeToday['toGradeToday']; ?>
                                                        in the past 24 hours</div>
                                                </div>
                                                <?php
                                            }
                                        }
                                        ?>
                                        <?php
                                        $pendingTodo = mysqli_fetch_assoc($pendingTodoResult);
                                        if (mysqli_num_rows($countAssessmentsResult) > 0) {
                                            while ($countAssessments = mysqli_fetch_assoc($countAssessmentsResult)) {
                                                ?>
                                                <div class="col-12 col-md-3 mb-3 left-side">
                                                    <div class="d-flex align-items-center">
                                                        <span class="material-symbols-rounded"
                                                            style="color: var(--black); margin-right: 5px;">
                                                            assignment
                                                        </span>
                                                        <div class="stats-count text-22 text-bold">
                                                            <?php echo $countAssessments['activeAssessments']; ?>
                                                        </div>
                                                    </div>
                                                    <div class="stats-label text-18 text-sbold">active assessments</div>
                                                    <div class="text-reg text-16"><?php echo $pendingTodo['pending']; ?>
                                                        students yet to complete</div>
                                                </div>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </div>

                                    <!-- Cards Section -->
                                    <div class="row m-0 p-0 mt-md-5">
                                        <div class="row m-0 p-0 pt-1 text-sbold text-18 ms-0 ms-md-2">

                                            <!-- Left Side -->
                                            <div class="col-12 col-md-7 left-side">
                                                <!-- Main Card -->
                                                <div class="card left-card">

                                                    <!-- Top Header -->
                                                    <div
                                                        class="p-4 pb-0 d-flex justify-content-between align-items-center mb-3">
                                                        <div class="d-flex align-items-center">
                                                            <span class="material-symbols-rounded"
                                                                style="color: var(--black); margin-right: 5px;">
                                                                folder
                                                            </span>
                                                            <span>Your Courses</span>
                                                        </div>

                                                        <span class="count-badge ms-2 text-sbold text-16">
                                                            <?php echo (int) $totalCourses; ?>
                                                        </span>
                                                    </div>


                                                    <!-- Scrollable Wrapper (matching new styling) -->
                                                    <div class="position-relative">

                                                        <!-- Left Scroll Btn -->
                                                        <button
                                                            class="scroll-btn left-scroll ms-2 position-absolute top-50 start-0 translate-middle-y d-none d-md-block"
                                                            style="border:none;background:white;cursor:pointer;z-index:5;
                                                                width:35px;height:35px;border-radius:50%;display:flex;align-items:center;
                                                                justify-content:center;box-shadow:0 2px 4px rgba(0,0,0,0.15); margin-top:-15px">
                                                            <i class="fas fa-chevron-left"
                                                                style="font-size:18px;color:var(--black);"></i>
                                                        </button>

                                                        <!-- Scrollable Content -->
                                                        <div class="px-4 pb-4 overflow-x-auto scroll-attachments"
                                                            style="padding-bottom:20px;scrollbar-width:none;-ms-overflow-style:none; white-space:nowrap;">

                                                            <div class="<?php echo ($totalCourses === 0) ? 'w-100' : 'w-auto'; ?>"
                                                                style="display:inline-flex; gap:12px;">

                                                                <?php if ($totalCourses === 0) { ?>
                                                                    <div
                                                                        class="col-12 text-center text-14 d-flex flex-column align-items-center justify-content-center gap-0 h-100 w-100">
                                                                        <img src="../shared/assets/img/empty/folder2.png"
                                                                            alt="No Announcements" class="empty-state-img"
                                                                            style="width: 100px;">
                                                                        <p class="text-med mb-0">No courses yet.</p>
                                                                        <p class="text-reg">Create a course to get started!</p>
                                                                    </div>

                                                                <?php } else {
                                                                    foreach ($courses as $course) {

                                                                        $courseCode = ($course['courseCode'] ?? '');
                                                                        $courseTitle = ($course['courseTitle'] ?? '');
                                                                        $yearSection = ($course['yearSection'] ?? '');
                                                                        $imageFile = trim((string) ($course['courseImage'] ?? ''));
                                                                        $imagePath = "../shared/assets/img/course-images/" . $imageFile;
                                                                        $fallbackImage = "../shared/assets/img/home/webdev.jpg";
                                                                        ?>

                                                                        <!-- Each Course Card -->

                                                                        <div class="card custom-course-card pb-3"
                                                                            style="width:200px!important; height:auto!important">
                                                                            <a href="course-info.php?courseID= <?php echo $course['courseID']; ?>"
                                                                                class="text-decoration-none text-black">
                                                                                <img src="<?php echo $imageFile ? $imagePath : $fallbackImage; ?>"
                                                                                    class="card-img-top"
                                                                                    alt="<?php echo $courseTitle; ?>"
                                                                                    onerror="this.onerror=null;this.src='<?php echo $fallbackImage; ?>';">

                                                                                <div
                                                                                    class="card-body border-top border-black px-3 py-2">

                                                                                    <div class="text-sbold text-truncate mt-1"
                                                                                        style="max-width: 200px;"
                                                                                        title="<?php echo $courseCode; ?>">
                                                                                        <?php echo $courseCode; ?>
                                                                                    </div>

                                                                                    <p class="text-reg text-14 mb-0 text-truncate"
                                                                                        style="max-width: 200px;"
                                                                                        title="<?php echo $courseTitle; ?>">
                                                                                        <?php echo $courseTitle; ?>
                                                                                    </p>

                                                                                    <div
                                                                                        class="d-flex align-items-center mb-2 mt-3">
                                                                                        <span class="material-symbols-rounded"
                                                                                            style="color: var(--black); font-size:20px">
                                                                                            people
                                                                                        </span>
                                                                                        <span class="text-reg text-12 ms-2">
                                                                                            <?php echo $course['studentCount']; ?>
                                                                                            <?php echo ($course['studentCount'] > 1) ? 'Students' : 'Student'; ?>
                                                                                        </span>

                                                                                    </div>
                                                                                    <div class="d-flex align-items-center mb-2 mt-1"
                                                                                        style="width:100%">
                                                                                        <span class="material-symbols-rounded"
                                                                                            style="color: var(--black);font-size:20px">
                                                                                            calendar_today
                                                                                        </span>
                                                                                        <div class="calendar-schedule ms-2">
                                                                                            <div class="text-reg text-12"
                                                                                                style="width:100%; overflow:hidden; white-space:normal; word-break:break-word;">
                                                                                                <span
                                                                                                    style="display:inline-block; width:100%;">
                                                                                                    <?php echo nl2br(($course['schedule'] ?: 'Schedule TBA')); ?>
                                                                                                </span>
                                                                                            </div>
                                                                                        </div>

                                                                                    </div>

                                                                                    <!-- KEEP YEAR/SECTION (as requested) -->
                                                                                    <div class="d-flex align-items-center mt-1">
                                                                                        <span class="material-symbols-rounded"
                                                                                            style="color: var(--black);font-size:20px">
                                                                                            label
                                                                                        </span>
                                                                                        <span class="text-reg text-12 ms-2">
                                                                                            <?php echo $course['section']; ?>
                                                                                        </span>
                                                                                    </div>
                                                                            </a>
                                                                        </div>
                                                                    </div>

                                                                <?php }
                                                                } ?>

                                                        </div>
                                                    </div>

                                                    <!-- Right Scroll Btn -->
                                                    <button
                                                        class="scroll-btn right-scroll me-2 position-absolute top-50 end-0 translate-middle-y d-none d-md-block"
                                                        style="border:none;background:white;cursor:pointer;z-index:5;
                                                            width:35px;height:35px;border-radius:50%;display:flex;align-items:center;
                                                            justify-content:center;box-shadow:0 2px 4px rgba(0,0,0,0.15); margin-top:-15px">
                                                        <i class="fas fa-chevron-right"
                                                            style="font-size:18px;color:var(--black);"></i>
                                                    </button>

                                                </div>

                                            </div>

                                        </div>

                                        <!-- Right Side -->
                                        <div class="col-12 col-md-5 right-side mb-3 mb-md-0">
                                            <!-- Main Card -->
                                            <div class="card left-card">
                                                <!-- Top Header -->
                                                <div
                                                    class="p-4 pb-0 d-flex justify-content-between align-items-center mb-3">
                                                    <div class="d-flex align-items-center">
                                                        <span class="material-symbols-rounded me-2"
                                                            style="color: var(--black);">
                                                            assignment
                                                        </span>
                                                        <span>Active Assessments</span>
                                                    </div>
                                                    <div class="count-badge"><?php echo $totalAssessments ?></div>
                                                </div>

                                                <!-- Scrollable tasks -->
                                                <div class="scrollable-tasks ps-4 pe-4"
                                                    style="max-height: 338px; overflow-y: auto; scrollbar-width: none; -ms-overflow-style: none; scroll-behavior: smooth;">

                                                    <!-- Assessment Card -->
                                                    <?php if ($totalAssessments === 0) { ?>
                                                        <div
                                                            class="col-12 text-center text-14 d-flex flex-column align-items-center justify-content-center gap-0 h-100">
                                                            <img src="../shared/assets/img/empty/todo.png"
                                                                alt="No Announcements" class="empty-state-img"
                                                                style="width: 100px;">
                                                            <p class="text-med mb-0">Nothing new here.</p>
                                                            <p class="text-reg">No active assessments found</p>
                                                        </div>
                                                    <?php } else {
                                                        $chartsIDs = [];
                                                        $i = 1;
                                                        foreach ($assessments as $assessment) {
                                                            $assessmentType = ($assessment['type'] ?? '');
                                                            $assessmentTitle = ($assessment['assessmentTitle'] ?? '');
                                                            $about = ($assessment['about'] ?? '');
                                                            $assessmentCourseCode = ($assessment['courseCode'] ?? '');
                                                            $assessmentDeadline = ($assessment['assessmentDeadline'] ?? '');
                                                            $courseStudents = ($assessment['courseStudents'] ?? '');
                                                            $submittedTodo = ($assessment['submittedTodo'] ?? '');
                                                            $chartsIDs[] = "chart$i";
                                                            $submittedChart[] = $submittedTodo;
                                                            $studentChart[] = $courseStudents;
                                                            $type = strtolower($assessmentType);

                                                            if ($type === 'task') {
                                                                $link = "task-info.php?assignmentID=" . $assessment['assignmentID'];
                                                            } elseif ($type === 'test') {
                                                                $link = "test-info.php?testID=" . $assessment['testID'];
                                                            } else {
                                                                $link = "#"; // fallback
                                                            }

                                                            ?>
                                                            <div class="card me-2 me-md-0"
                                                                style="border-radius: 10px; border: 1px solid var(--black); padding: 15px; margin-bottom:12px">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-between pb-1">
                                                                    <!-- Left Info -->
                                                                    <div class="flex-grow-1 w-50">
                                                                        <div class="mb-2 text-reg">
                                                                            <span class="badge rounded-pill"
                                                                                style="background: var(--highlight50); color: var(--black); font-size:12px;"><?php echo $assessmentType ?></span>
                                                                        </div>
                                                                        <div class="text-sbold text-truncate"
                                                                            style="display:block; width:100%; white-space:normal; overflow:hidden; text-overflow:ellipsis;">
                                                                            <?php echo $assessmentTitle ?>
                                                                        </div>
                                                                        <div class="text-sbold text-14">
                                                                            <?php echo $assessmentCourseCode ?><br>
                                                                            <div class="text-reg text-14">
                                                                                <?php echo $about ?>
                                                                            </div>
                                                                        </div>
                                                                        <div class="text-reg text-12 mt-2"
                                                                            style="color: var(--black);">
                                                                            <span
                                                                                class="text-sbold"><?php echo $submittedTodo ?></span>
                                                                            of <?php echo $courseStudents ?>
                                                                            students submitted<br>
                                                                            <span class="text-reg">Due
                                                                                <?php echo $assessmentDeadline ?></span>
                                                                        </div>

                                                                    </div>

                                                                    <!-- Right Side: Graph + Arrow -->
                                                                    <div class="d-flex flex-column align-items-end ms-3"
                                                                        style="min-width:70px;">

                                                                        <!-- Graph -->
                                                                        <div class="me-5 mt-3">
                                                                            <canvas id="chart<?php echo $i; ?>" width="100"
                                                                                height="100"></canvas>
                                                                        </div>

                                                                        <!-- Arrow (same column, stays aligned under graph) -->
                                                                        <div class="mt-2 me-2">
                                                                            <a href="<?php echo $link; ?>">
                                                                                <i class="fa-solid fa-arrow-right"
                                                                                    style="color: var(--black);"></i>
                                                                            </a>
                                                                        </div>

                                                                    </div>

                                                                </div>

                                                            </div>
                                                            <?php
                                                            $i++;
                                                        }
                                                    } ?>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>

                        </div>
                    </div> <!-- End here -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function createDoughnutChart(canvasId, submitted, total) {
            const ctx = document.getElementById(canvasId).getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [submitted, total - submitted],
                        backgroundColor: ['#3DA8FF', '#C7C7C7'],
                        borderWidth: 0,
                    }]
                },
                options: {
                    cutout: '75%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: false
                        }
                    }
                }
            });
        }

        <?php if ($totalAssessments > 0) { ?>
            document.addEventListener("DOMContentLoaded", function () {
                const chartsIDs = <?php echo json_encode($chartsIDs); ?>;
                const submitted = <?php echo json_encode($submittedChart); ?>;
                const student = <?php echo json_encode($studentChart); ?>;
                chartsIDs.forEach((id, index) => {
                    createDoughnutChart(id, submitted[index], student[index]);
                });
            });
        <?php } ?>
    </script>
    <script>
        const scrollContainer = document.querySelector('.scroll-attachments');
        const leftBtn = document.querySelector('.left-scroll');
        const rightBtn = document.querySelector('.right-scroll');

        scrollContainer.addEventListener('wheel', (e) => e.preventDefault()); // disable native scroll if needed
        scrollContainer.style.scrollBehavior = 'smooth';

        leftBtn.addEventListener('click', () => {
            scrollContainer.scrollBy({
                left: -200,
                behavior: 'smooth'
            });
        });

        rightBtn.addEventListener('click', () => {
            scrollContainer.scrollBy({
                left: 200,
                behavior: 'smooth'
            });
        });

        // Hide scrollbar visually
        scrollContainer.style.overflowY = 'hidden';
        scrollContainer.style.scrollbarWidth = 'none';
        scrollContainer.style.msOverflowStyle = 'none';
        scrollContainer.addEventListener('scroll', () => {
            scrollContainer.style.scrollbarWidth = 'none';
        });
        scrollContainer.querySelectorAll('::-webkit-scrollbar').forEach(el => el.style.display = 'none');
    </script>
</body>


</html>