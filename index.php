<?php $activePage = 'home'; ?>
<?php
include('shared/assets/database/connect.php');

include("shared/assets/processes/session-process.php");
$selectEnrolledQuery = "SELECT 
	courses.userID  AS profID,
    profInfo.firstName AS profName,
    profInfo.profilePicture AS profProfile,
    student.userID AS studentID,
    studentInfo.firstName AS studentName,
    studentInfo.profilePicture as studentProfile,
    studentInfo.isNewUser AS isNewUser, 
    courses.courseID,
    courses.courseCode,
    courses.courseTitle,
    courses.courseImage,
    (SELECT COUNT(*) 
     FROM enrollments AS e
     WHERE e.userID = enrollments.userID) AS totalEnrollments
    FROM enrollments
    INNER JOIN courses 
        ON enrollments.courseID = courses.courseID
    INNER JOIN users AS prof 
        ON courses.userID = prof.userID
    INNER JOIN users AS student 
        ON enrollments.userID = student.userID
    INNER JOIN userinfo AS profInfo
        ON prof.userID = profInfo.userID
    INNER JOIN userinfo AS studentInfo
        ON student.userID = studentInfo.userID
    WHERE enrollments.userID = '$userID';
";
$selectEnrolledResult = executeQuery($selectEnrolledQuery);


// Welcoming Message for new/older users
if (mysqli_num_rows($selectEnrolledResult) > 0) {
    $studentEnrolled = mysqli_fetch_assoc($selectEnrolledResult);

    if ($studentEnrolled['isNewUser']) {
        $welcomeText = "Welcome, " . $studentEnrolled['studentName'] . "!";
        $updateUserQuery = "UPDATE userinfo SET isNewUser = 0 WHERE userID = '{$studentEnrolled['studentID']}'";
        executeQuery($updateUserQuery);
    } else {
        $welcomeText = "Welcome back, " . $studentEnrolled['studentName'] . "!";
    }

    mysqli_data_seek($selectEnrolledResult, 0); // reset pointer 
}


$selectAnnouncementsQuery = "SELECT 
    announcements.*, 
    profInfo.firstName AS profName,
    profInfo.profilePicture,
    courses.courseCode
    FROM enrollments
    INNER JOIN courses
        ON enrollments.courseID = courses.courseID
    INNER JOIN users
        ON courses.userID = users.userID
    INNER JOIN announcements
        ON courses.courseID = announcements.courseID
    INNER JOIN userinfo AS profInfo
        ON users.userID = profInfo.userID
    WHERE enrollments.userID = '$userID'
    AND announcements.announcementID NOT IN (
        SELECT announcementID FROM announcementNotes WHERE userID = '$userID'
    );
    ";
$selectAnnouncementsResult = executeQuery($selectAnnouncementsQuery);

$selectAssessmentQuery = "SELECT
    tests.testID,
    assignments.assignmentID,
    assessments.*,
    assessments.assessmentTitle AS assessmentTitle,
    todo.*,
    courses.courseCode,
    DATE_FORMAT(assessments.deadline, '%b %e') AS assessmentDeadline,
    (SELECT COUNT(*) 
     FROM todo WHERE todo.userID = '$userID' AND todo.status = 'Pending') AS totalAssessments
    FROM assessments
    INNER JOIN courses
        ON assessments.courseID = courses.courseID
    INNER JOIN enrollments
        ON courses.courseID = enrollments.courseID
    INNER JOIN todo 
    	ON assessments.assessmentID = todo.assessmentID
    LEFT JOIN assignments
        ON assessments.assessmentID = assignments.assessmentID
    LEFT JOIN tests
        ON assessments.assessmentID = tests.assessmentID
    WHERE todo.userID = '$userID' AND todo.status = 'Pending'
    AND (assessments.deadline IS NULL OR assessments.deadline >= CURDATE())
    GROUP BY assessments.assessmentID DESC
    LIMIT 3;
";
$selectAssessmentResult = executeQuery($selectAssessmentQuery);

$selectLeaderboardQuery = "SELECT 
    courses.courseID,
    courses.courseCode,
    courses.courseTitle,
    SUM(leaderboard.xpPoints) AS totalPoints
    FROM leaderboard
    INNER JOIN enrollments
        ON leaderboard.enrollmentID = enrollments.enrollmentID
    INNER JOIN courses
	    ON enrollments.courseID = courses.courseID
    WHERE enrollments.userID = '$userID'
    GROUP BY courses.courseID, courses.courseCode, courses.courseTitle;
";
$selectLeaderboardResult = executeQuery($selectLeaderboardQuery);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home ✦ Webstar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="shared/assets/css/index.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="shared/assets/img/webstar-icon.png">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0"
        rel="stylesheet" />

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

                    <div class="container-fluid py-1 overflow-y-auto  row-padding-top">
                        <div class="row">
                            <!-- PUT CONTENT HERE -->
                            <?php
                            if (mysqli_num_rows($selectEnrolledResult) > 0) {
                                while ($studentEnrolled = mysqli_fetch_assoc($selectEnrolledResult)) {
                            ?>
                                    <!-- left side -->
                                    <div class="col-12 col-sm-12 col-md-7">
                                        <div class="row align-items-center ps-4">
                                            <!-- Image column -->
                                            <div class="col-auto d-none d-sm-block">
                                                <img src="./shared/assets/img/profIndex/folder.png" alt="Folder"
                                                    class="img-fluid rounded-circle folder-img"
                                                    style="width:68px; height:68px;">
                                            </div>

                                            <!-- Text column -->
                                            <div class="col text-center text-sm-start">
                                                <div class="text-sbold text-22"><?php echo $welcomeText; ?></div>
                                                <div class="text-reg text-16">Pick up where you left off and keep building your
                                                    skills.</div>
                                            </div>
                                        </div>

                                        <!-- Another row for foldering -->
                                        <div class="row pt-1 text-sbold text-18">
                                            <div class="col pt-3">
                                                <!-- Main Card -->
                                                <div class="card left-card">

                                                    <!-- Top Header -->
                                                    <div
                                                        class="p-4 pb-0 d-flex justify-content-between align-items-center mb-3">
                                                        <div class="d-flex align-items-center">
                                                            <span class="material-symbols-rounded" style="color: var(--black); margin-right: 5px;">
                                                                folder
                                                            </span>
                                                            <span>Your Courses</span>
                                                        </div>
                                                        <span class="count-badge ms-2 text-sbold text-16">
                                                            <?php echo $studentEnrolled['totalEnrollments']; ?>
                                                        </span>
                                                    </div>

                                                    <!-- Scroll Controls -->
                                                    <div class="position-relative">
                                                        <button
                                                            class="scroll-btn left-scroll ms-2 position-absolute top-50 start-0 translate-middle-y d-none d-md-block"
                                                            style="border:none;background:white;cursor:pointer;z-index:5;
                                                                    width:35px;height:35px;border-radius:50%;display:flex;align-items:center;
                                                                    justify-content:center;box-shadow:0 2px 4px rgba(0,0,0,0.15); margin-top:-15px">
                                                            <i class="fas fa-chevron-left"
                                                                style="font-size:18px;color:var(--black);"></i>
                                                        </button>

                                                        <!-- Scrollable course -->
                                                        <div class="px-4 pb-4 overflow-x-auto scroll-attachments"
                                                            style="padding-bottom:20px;scrollbar-width:none;-ms-overflow-style:none;">
                                                            <div style="display:inline-flex;gap:12px;">
                                                                <?php
                                                                if (mysqli_num_rows($selectEnrolledResult) > 0) {
                                                                    mysqli_data_seek($selectEnrolledResult, 0);
                                                                    while ($enrolledSubjects = mysqli_fetch_assoc($selectEnrolledResult)) {
                                                                ?>
                                                                        <!-- Card 1 -->
                                                                        <div class="card custom-course-card">
                                                                            <a href="course-info.php?courseID=<?php echo $enrolledSubjects['courseID']; ?>"
                                                                                class="text-decoration-none text-black">
                                                                                <img src="shared/assets/img/course-images/<?php echo $enrolledSubjects['courseImage']; ?>"
                                                                                    class="card-img-top" alt="...">
                                                                                <div class="card-body border-top border-black px-3 py-2">
                                                                                    <div class="text-sbold text-16">
                                                                                        <?php echo $enrolledSubjects['courseCode']; ?>
                                                                                    </div>
                                                                                    <p class="text-reg text-14 mb-0">
                                                                                        <?php echo $enrolledSubjects['courseTitle']; ?>
                                                                                    </p>
                                                                                </div>
                                                                            </a>
                                                                        </div>
                                                                <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>

                                                        <button
                                                            class="scroll-btn right-scroll position-absolute me-2 top-50 end-0 translate-middle-y d-none d-md-block"
                                                            style="border:none;background:white;cursor:pointer;z-index:5;
                                                                width:35px;height:35px;border-radius:50%;display:flex;align-items:center ;margin-top:-15px;
                                                                justify-content:center;box-shadow:0 2px 4px rgba(0,0,0,0.15);">
                                                            <i class="fas fa-chevron-right"
                                                                style="font-size:18px;color:var(--black);"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <!-- Another row for Announcement -->
                                        <div class="row pt-1 text-sbold text-18">
                                            <div class="col pt-3">
                                                <!-- Main Card -->
                                                <div class="card p-4 mb-4 left-card">

                                                    <!-- Top Header  -->
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <div class="d-flex align-items-center">
                                                            <span class="material-symbols-rounded me-2" style="color: var(--black); margin-right: 5px;">
                                                                campaign
                                                            </span>
                                                            <span>Recent Announcements</span>
                                                        </div>
                                                        <?php if (mysqli_num_rows($selectAnnouncementsResult) > 0): ?>
                                                            <span class="count-badge ms-2 text-sbold text-16">
                                                                <?php echo mysqli_num_rows($selectAnnouncementsResult); ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <!-- Scrollable Card List -->
                                                    <div
                                                        style="max-height: 200px; overflow-y: auto; padding-right: 5px; scroll-behavior: smooth; scrollbar-width: none; -ms-overflow-style: none;">
                                                        <?php
                                                        if (mysqli_num_rows($selectAnnouncementsResult) > 0) {
                                                            while ($announcements = mysqli_fetch_assoc($selectAnnouncementsResult)) {
                                                        ?>
                                                                <!-- Card 1 -->
                                                                <div class="card mb-3"
                                                                    style="border-radius: 12px; border: 1px solid rgba(44, 44, 44, 1); padding: 15px;">
                                                                    <div class="announcement-card d-flex align-items-start mb-3">
                                                                        <!-- Instructor Image -->
                                                                        <div class="flex-shrink-0 me-3">
                                                                            <img src="shared/assets/pfp-uploads/<?php echo $announcements['profilePicture']; ?>"
                                                                                alt="Instructor Image"
                                                                                style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">
                                                                        </div>

                                                                        <!-- Text Content -->
                                                                        <div class="prof-header text-start">
                                                                            <div class="prof-info text-reg text-12"
                                                                                style="color: var(--black); line-height: 140%; position: relative;">
                                                                                <div
                                                                                    class="main-row d-flex align-items-center justify-content-between flex-wrap">
                                                                                    <div class="d-flex align-items-center name-badge">
                                                                                        <strong>Prof.
                                                                                            <?php echo $announcements['profName']; ?></strong>
                                                                                        <span
                                                                                            class="text-reg text-12 badge rounded-pill ms-2 courses-badge"><?php echo $announcements['courseCode']; ?></span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="date-row d-flex justify-content-between align-items-center mt-1"
                                                                                    style="position: relative;">
                                                                                    <span
                                                                                        style="font-weight: normal;"><?php echo $announcements['announcementDate'] . " | " . $announcements['announcementTime']; ?></span>
                                                                                </div>
                                                                            </div>

                                                                            <p class="announcement-text mb-0 mt-3 text-reg text-12"
                                                                                style="color: var(--black); line-height: 140%;">
                                                                                <?php echo $announcements['announcementContent']; ?>
                                                                            </p>
                                                                        </div>
                                                                        <a href="course-info.php?courseID=<?php echo $announcements['courseID']; ?>"
                                                                            class="ms-auto pe-2 d-flex align-items-center text-decoration-none">
                                                                            <i class="announcement-arrow fa-solid fa-arrow-right text-reg text-12"
                                                                                style="color: var(--black);"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            <?php
                                                            }
                                                        } else {
                                                            ?>
                                                            <div class="col-12 text-center text-14">
                                                                <img src="shared/assets/img/empty/announcements.png" alt="No Announcements" class="empty-state-img" style="width: 100px;">
                                                                <p class="text-med mt-1 mb-0">Nothing new here.</p>
                                                                <p class="text-reg mt-1">Announcements are all caught up.</p>
                                                            </div>
                                                        <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Right side -->
                                    <div class="col-12 col-sm-12 col-md-5">
                                        <div class="row text-sbold text-18">
                                            <div class="col">
                                                <div class="card p-4 mb-3 left-card">

                                                    <!-- Top Header -->
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <div class="d-flex align-items-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                                fill="currentColor" class="bi bi-arrow-down-right-square-fill"
                                                                viewBox="0 0 16 16"
                                                                style="color: var(--black); width: 26px; margin-right: 5px;">
                                                                <path
                                                                    d="M14 16a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12zM5.904 5.197 10 9.293V6.525a.5.5 0 0 1 1 0V10.5a.5.5 0 0 1-.5.5H6.525a.5.5 0 0 1 0-1h2.768L5.197 5.904a.5.5 0 0 1 .707-.707z" />
                                                            </svg>

                                                            <span>Upcoming</span>
                                                        </div>
                                                        <div>
                                                            <?php
                                                            $totalAssessmentsCount = mysqli_num_rows($selectAssessmentResult);
                                                            if ($totalAssessmentsCount > 0) {
                                                                $emptyAssessment = false;
                                                                $totalAssessments = mysqli_fetch_assoc($selectAssessmentResult);
                                                            ?>
                                                                <span class="count-badge ms-2 text-sbold text-16"><?php echo $totalAssessments['totalAssessments']; ?></span>
                                                            <?php
                                                            } else {
                                                                $emptyAssessment = true;
                                                            }
                                                            ?>
                                                        </div>

                                                    </div>
                                                    <!-- Scrollable course -->
                                                    <div
                                                        style="height: 100%; overflow-y: auto; padding-right: 5px; scroll-behavior: smooth; scrollbar-width: none; -ms-overflow-style: none;">
                                                        <?php
                                                        if (mysqli_num_rows($selectAssessmentResult) > 0) {
                                                            mysqli_data_seek($selectAssessmentResult, 0);
                                                            while ($activities = mysqli_fetch_assoc($selectAssessmentResult)) {
                                                                $type = strtolower(trim($activities['type']));
                                                                $link = "#";
                                                                if ($type === 'task') {
                                                                    $link = "assignment.php?assignmentID=" . $activities['assignmentID'];
                                                                } elseif ($type === 'test') {
                                                                    $link = "test.php?testID=" . $activities['testID'];
                                                                }
                                                        ?>
                                                                <div class="todo-card d-flex align-items-stretch mb-2">
                                                                    <!-- Date -->
                                                                    <div
                                                                        class="date d-flex align-items-center justify-content-center text-sbold text-20">
                                                                        <?php echo $activities['assessmentDeadline']; ?>
                                                                    </div>
                                                                    <!-- Main content -->
                                                                    <div
                                                                        class="d-flex flex-grow-1 flex-wrap justify-content-between p-2 w-100">
                                                                        <!-- For small screen of main content -->
                                                                        <div class="px-3 py-0">
                                                                            <div class="text-sbold text-16">
                                                                                <?php echo $activities['assessmentTitle']; ?>
                                                                            </div>
                                                                            <div class="text-reg text-12">
                                                                                <?php echo $activities['courseCode']; ?>
                                                                            </div>
                                                                            <span
                                                                                class="course-badge rounded-pill px-3 text-reg text-12 mt-2 d-inline d-md-none">
                                                                                <?php echo ucfirst($activities['type']); ?>
                                                                            </span>
                                                                        </div>
                                                                        <!-- Pill and Arrow on Large screen-->
                                                                        <div class="d-flex align-items-center gap-2 ms-auto">
                                                                            <span
                                                                                class="course-badge rounded-pill px-3 text-reg text-12 d-none d-md-inline">
                                                                                <?php echo ucfirst($activities['type']); ?>
                                                                            </span>
                                                                            <a href="<?php echo $link; ?>" class="text-decoration-none">
                                                                                <i class="fa-solid fa-arrow-right text-reg text-12 pe-2"
                                                                                    style="color: var(--black);"></i>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php
                                                            }
                                                        } else {
                                                            ?>
                                                            <div class="col-12 text-center text-14">
                                                                <img src="shared/assets/img/empty/todo.png" alt="No Leaderboard" class="empty-state-img" style="width: 100px;">
                                                                <p class="text-med mt-1 mb-0">You're on track.</p>
                                                                <p class="text-reg mt-1">No new assessments ahead.</p>
                                                            </div>
                                                        <?php
                                                        }
                                                        ?>

                                                        <!-- View More (always to todo.php) -->
                                                        <?php if (!$emptyAssessment) { ?>
                                                            <div
                                                                style="display:flex; justify-content: flex-end; align-items: center; gap:6px; margin-right: 10px;">
                                                                <a href="todo.php"
                                                                    class="text-decoration-none text-black d-flex align-items-center gap-2">
                                                                    <span class="text-reg text-12" style="color: var(--black);">View
                                                                        More</span>
                                                                    <i class="fa-solid fa-arrow-right text-reg text-12"
                                                                        style="color: var(--black);"></i>
                                                                </a>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Another row for leaderboard -->
                                        <div class="row text-sbold text-18">
                                            <div class="col">
                                                <div class="card p-4 mb-3 left-card">

                                                    <!-- Top Header -->
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <div class="d-flex align-items-center">
                                                            <span class="material-symbols-rounded" style="color: var(--black); margin-right: 10px;">
                                                                leaderboard
                                                            </span>
                                                            <span>Leaderboard Rank</span>
                                                        </div>
                                                    </div>
                                                    <!-- Scrollable leaderboard -->
                                                    <div
                                                        style="max-height: 240px; overflow-y: auto; padding-right: 5px; display: flex; flex-wrap: wrap; gap: 8px; scroll-behavior: smooth; scrollbar-width: none; -ms-overflow-style: none;">
                                                        <!-- Card 1 -->
                                                        <?php
                                                        if (mysqli_num_rows($selectLeaderboardResult) > 0) {
                                                            while ($leaderboards = mysqli_fetch_assoc($selectLeaderboardResult)) {
                                                        ?>
                                                                <div class="card custom-leaderboard-card">
                                                                    <div class="card-body p-4">
                                                                        <div style="display: inline-flex; align-items: center;">
                                                                            <span class="rank-number text-bold text-18">11</span>
                                                                            <span
                                                                                class="text-reg text-12 badge rounded-pill ms-2 learderboard-badge"
                                                                                style="display: inline-flex; align-items: center; gap: 4px;">
                                                                                <i class="fa-solid fa-caret-up"></i>
                                                                                2
                                                                            </span>
                                                                        </div>

                                                                        <!-- NEW WRAPPER -->
                                                                        <div class="info-block">
                                                                            <div class="comp-code text-sbold text-16">
                                                                                <?php echo $leaderboards['courseCode']; ?>
                                                                            </div>
                                                                            <div class="subj-code text-reg text-12 mb-0 text-truncate">
                                                                                <?php echo $leaderboards['courseTitle']; ?>
                                                                            </div>

                                                                            <div class="xp-container">
                                                                                <div class="xp-block text-reg text-12 mb-0">
                                                                                    <?php echo $leaderboards['totalPoints']; ?> XPs
                                                                                </div>
                                                                                <div class="xp-arrow">
                                                                                    <i class="fa-solid fa-arrow-right text-reg text-12"
                                                                                        style="color: var(--black);"></i>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php
                                                            }
                                                        } else {
                                                            ?>
                                                            <div class="col-12 text-center text-14">
                                                                <img src="shared/assets/img/empty/leaderboard.png" alt="No Leaderboard" class="empty-state-img" style="width: 100px;">
                                                                <p class="text-med mt-1 mb-0">Leaderboard is empty.</p>
                                                                <p class="text-reg mt-1">Start submitting tasks to earn scores.</p>
                                                            </div>
                                                        <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            <?php
                                }
                            } else {
                                echo "<script>window.location.href = 'course-join.php';</script>";
                            }
                            ?>
                            <!--<div class="col-12 mx-auto text-center">
                                    <img src="shared/assets/img/courseJoin/folder-dynamic-color.png" class="mx-auto folder-image" style="width: 400px; height: 400px; object-fit: cover; border-radius: 50%;">
                                    <p class="text-30 text-lg-22 text-sbold lh-1">Enroll in your first course to begin</p>
                                    <p class="text-20 text-lg-16 text-reg lh-1">Enter the access code provided by your professor.</p>
                                    <div class="form-floating col-6 col-xl-3 mx-auto mt-lg-4 mt-xl-5">
                                        <textarea class="form-control border border-black rounded-4" placeholder="Leave a comment here" id="floatingTextarea"></textarea>
                                        <label for="floatingTextarea">Access Code</label>
                                    </div>
                                </div>-->
                        </div>
                    </div>
                </div> <!-- End here -->
            </div>
        </div>
    </div>
    </div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

</body>


</html>