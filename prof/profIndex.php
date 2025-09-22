<?php $activePage = 'profIndex'; ?>
<?php
include('../shared/assets/database/connect.php');

$courses = [];
$result = executeQuery("SELECT courseID, userID, courseCode, courseTitle, courseImage, yearSection, schedule FROM courses ORDER BY courseID DESC");
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $courses[] = $row;
    }
}
$totalCourses = count($courses);
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

                    <div class="container-fluid py-1 overflow-y-auto">
                        <div class="row">
                            <!-- Left side -->
                            <div class="container-fluid py-1 overflow-y-auto">
                                <div class="row">
                                    <!-- Left side -->
                                    <div class="col-12 col-md-7 mb-3 mb-md-0">
                                        <div class="row ps-4">
                                            <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between w-100 text-center text-sm-start"
                                                style="position: relative;">

                                                <!-- Left side: Image + Text -->
                                                <div class="d-flex align-items-center mb-3 mb-sm-0">
                                                    <!-- Image hidden on mobile -->
                                                    <img src="../shared/assets/img/profIndex/folder.png" alt="Folder"
                                                        class="img-fluid rounded-circle me-3 folder-img d-none d-sm-block"
                                                        style="width:68px; height:68px;">
                                                    <div class="text-truncate w-100">
                                                        <div class="text-sbold text-22">Welcome back, Prof. James!</div>
                                                        <div class="text-reg text-16">Resume your work and keep
                                                            developing your
                                                            course.</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Button for Mobile (below text) -->
                                            <div
                                                class="d-block d-md-none mt-3 d-flex justify-content-center align-items-center">
                                                <div class="dropdown">
                                                    <button
                                                        class="btn btn-custom text-sbold rounded-pill px-4 dropdown-toggle"
                                                        type="button" id="dropdownMenuButtonMobile"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        + Create
                                                    </button>
                                                    <ul class="dropdown-menu shadow "
                                                        aria-labelledby="dropdownMenuButtonMobile">
                                                        <li><a class="dropdown-item d-flex align-items-center mt-1"
                                                                href="#">
                                                                <img src="../shared/assets/img/profIndex/file.png"
                                                                    class="me-2" alt="icon"> Create course</a></li>
                                                        <li><a class="dropdown-item d-flex align-items-center" href="#">
                                                                <img src="../shared/assets/img/profIndex/announcement.png"
                                                                    class="me-2" alt="icon"> Post announcement</a></li>
                                                        <li><a class="dropdown-item d-flex align-items-center" href="#">
                                                                <img src="../shared/assets/img/profIndex/uploadLesson.png"
                                                                    class="me-2" alt="icon"> Upload lesson</a></li>
                                                        <li><a class="dropdown-item d-flex align-items-center" href="#">
                                                                <img src="../shared/assets/img/profIndex/createExam.png"
                                                                    class="me-2" alt="icon"> Create exam</a></li>
                                                        <li><a class="dropdown-item d-flex align-items-center" href="#">
                                                                <img src="../shared/assets/img/profIndex/assignTask.png"
                                                                    class="me-2" alt="icon"> Assign task</a></li>
                                                    </ul>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <!-- Right side button for desktop-->
                                    <div class="col-12 col-md-5">
                                        <div class="row position-relative">
                                            <!-- Button placed on the upper right -->
                                            <div
                                                class="col-12 d-flex justify-content-md-end mb-2 position-relative order-2 order-md-1">
                                                <div class="dropdown">
                                                    <button
                                                        class="btn btn-custom text-sbold rounded-pill px-4 dropdown-toggle"
                                                        type="button" id="dropdownMenuButton" data-bs-toggle="dropdown"
                                                        aria-expanded="false">
                                                        + Create
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow "
                                                        aria-labelledby="dropdownMenuButton">
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center mt-1"
                                                                href="#">
                                                                <img src="../shared/assets/img/profIndex/file.png"
                                                                    class="me-2" alt="icon"> Create course
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center" href="#">
                                                                <img src="../shared/assets/img/profIndex/announcement.png"
                                                                    class="me-2" alt="icon"> Post announcement
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center" href="#">
                                                                <img src="../shared/assets/img/profIndex/uploadLesson.png"
                                                                    class="me-2" alt="icon"> Upload lesson
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center" href="#">
                                                                <img src="../shared/assets/img/profIndex/createExam.png"
                                                                    class="me-2" alt="icon"> Create exam
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center" href="#">
                                                                <img src="../shared/assets/img/profIndex/assignTask.png"
                                                                    class="me-2" alt="icon"> Assign task
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Stats Section -->
                                    <div class="row stats mt-5 align-items-center">
                                        <div class="col-12 col-md-3 mb-3">
                                            <div class="d-flex align-items-center">
                                                <img src="../shared/assets/img/profIndex/people.png" alt="Students"
                                                    width="26" height="26" class="me-2">
                                                <div class="stats-count text-22 text-bold">55</div>
                                            </div>
                                            <div class="stats-label text-18 text-sbold">currently enrolled</div>
                                            <div class="text-reg text-16">55 students taught all-time</div>
                                        </div>
                                        <div class="col-12 col-md-3 mb-3">
                                            <div class="d-flex align-items-center">
                                                <img src="../shared/assets/img/courses.png" alt="Courses" width="26"
                                                    height="26" class="me-2">
                                                <div class="stats-count text-22 text-bold">2</div>
                                            </div>
                                            <div class="stats-label text-18 text-sbold">active courses</div>
                                            <div class="text-reg text-16">55 courses created all-time</div>
                                        </div>
                                        <div class="col-12 col-md-3 mb-3">
                                            <div class="d-flex align-items-center">
                                                <img src="../shared/assets/img/profIndex/tasks.png" alt="Tasks"
                                                    width="26" height="26" class="me-2">
                                                <div class="stats-count text-22 text-bold">80</div>
                                            </div>
                                            <div class="stats-label text-18 text-sbold">tasks to grade</div>
                                            <div class="text-reg text-16">+50 in the past 24 hours</div>
                                        </div>
                                        <div class="col-12 col-md-3 mb-3">
                                            <div class="d-flex align-items-center">
                                                <img src="../shared/assets/img/profIndex/assess.png" alt="Assessments"
                                                    width="26" height="26" class="me-2">
                                                <div class="stats-count text-22 text-bold">5</div>
                                            </div>
                                            <div class="stats-label text-18 text-sbold">active assessments</div>
                                            <div class="text-reg text-16">55 students yet to complete</div>
                                        </div>
                                    </div>




                                    <!-- Cards Section -->
                                    <div class="row mt-5">
                                        <div class="row pt-1 text-sbold text-18 ms-2">

                                            <!-- Left Side -->
                                            <div class="col-12 col-md-7">
                                                <!-- Main Card -->
                                                <div class="card left-card">
                                                    <!-- Top Header -->
                                                    <div
                                                        class="p-4 pb-0 d-flex justify-content-between align-items-center mb-3">
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-folder"
                                                                style="color: var(--black); font-size: 20px; width: 26px; margin-right: 5px;"></i>
                                                            <span>Your Courses</span>
                                                        </div>
                                                        <div><?php echo (int) $totalCourses; ?></div>
                                                    </div>
                                                    <!-- Scrollable course -->
                                                    <div class="ps-4 pb-4 pe-4"
                                                        style="overflow-x: auto; white-space: nowrap; scrollbar-width: none; -ms-overflow-style: none; scroll-behavior: smooth;">
                                                        <div style="display: inline-flex; gap: 20px;">
                                                            <?php if ($totalCourses === 0) { ?>
                                                                <div class="text-reg text-14"
                                                                    style="color: var(--black); opacity: 0.85;">No courses
                                                                    found.</div>
                                                            <?php } else {
                                                                foreach ($courses as $course) {
                                                                    $courseCode = ($course['courseCode'] ?? '');
                                                                    $courseTitle = ($course['courseTitle'] ?? '');
                                                                    $yearSection = ($course['yearSection'] ?? '');
                                                                    $schedule = ($course['schedule'] ?? '');
                                                                    $imageFile = trim((string) ($course['courseImage'] ?? ''));
                                                                    $imagePath = "../shared/assets/img/home/" . $imageFile;
                                                                    $fallbackImage = "../shared/assets/img/home/webdev.jpg";
                                                                    ?>
                                                                    <div class="card custom-course-card">
                                                                        <img src="<?php echo $imageFile ? $imagePath : $fallbackImage; ?>"
                                                                            class="card-img-top"
                                                                            alt="<?php echo $courseTitle; ?>"
                                                                            onerror="this.onerror=null;this.src='<?php echo $fallbackImage; ?>';">
                                                                        <div class="card-body px-3 py-2">
                                                                            <div class="text-sbold text-16">
                                                                                <?php echo $courseCode; ?>
                                                                            </div>
                                                                            <p class="text-reg text-14 mb-0">
                                                                                <?php echo $courseTitle; ?>
                                                                            </p>
                                                                            <div class="d-flex align-items-center mb-2 mt-4">
                                                                                <img src="../shared/assets/img/profIndex/people.png"
                                                                                    alt="people" width="26" height="26">
                                                                                <span class="text-reg text-14 ms-2">0
                                                                                    Students</span>
                                                                            </div>
                                                                            <div class="d-flex align-items-start mb-2 mt-4">
                                                                                <img src="../shared/assets/img/profIndex/calendar.png"
                                                                                    alt="calendar" width="26" height="26"
                                                                                    class="mt-2">
                                                                                <div class="calendar-schedule ms-2">
                                                                                    <div class="text-reg">
                                                                                        <?php echo nl2br(($course['schedule'] ?: 'Schedule TBA')); ?>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="d-flex align-items-center mb-2 mt-4">
                                                                                <img src="../shared/assets/img/profIndex/tag.png"
                                                                                    alt="tag">
                                                                                <span
                                                                                    class="text-reg text-14 ms-2"><?php echo $yearSection ?: '—'; ?></span>
                                                                            </div>
                                                                            <div class="text-reg fst-italic text-12 mt-4"
                                                                                style="color: var(--black); opacity: 0.75;">Last
                                                                                updated recently</div>
                                                                        </div>
                                                                    </div>
                                                                <?php }
                                                            } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Right Side -->
                                            <div class="col-12 col-md-5">
                                                <!-- Main Card -->
                                                <div class="card left-card">
                                                    <!-- Top Header -->
                                                    <div
                                                        class="p-4 pb-0 d-flex justify-content-between align-items-center mb-3">
                                                        <div class="d-flex align-items-center">
                                                            <img src="../shared/assets/img/profIndex/assess.png"
                                                                alt="Assess Icon"
                                                                style="width: 26px; height: 26px; margin-right: 5px; object-fit: contain;">
                                                            <span>Active Assessments</span>
                                                        </div>
                                                        <div>5</div>
                                                    </div>

                                                    <!-- Scrollable tasks -->
                                                    <div class="scrollable-tasks ps-4 pe-4 mb-4"
                                                        style="max-height: 500px; overflow-y: auto; scrollbar-width: none; -ms-overflow-style: none; scroll-behavior: smooth;">

                                                        <!-- Assessment Card -->
                                                        <div class="card mb-3"
                                                            style="border-radius: 12px; border: 1px solid var(--black); padding: 15px;">
                                                            <div
                                                                class="d-flex align-items-center justify-content-between">
                                                                <!-- Left Info -->
                                                                <div class="flex-grow-1 ">
                                                                    <div class="mb-2 text-reg">
                                                                        <span class="badge rounded-pill"
                                                                            style="background: var(--highlight50); color: var(--black); font-size:12px;">Task</span>
                                                                    </div>
                                                                    <div class="text-bold">Assignment #1</div>
                                                                    <div class="text-sbold text-14 pt-1">COMP–006<br>
                                                                        <div class="text-reg text-14">Database Systems
                                                                        </div>
                                                                    </div>
                                                                    <div class="text-reg text-12 mt-2"
                                                                        style="color: var(--black);">
                                                                        <span class="text-sbold">29</span> of 59
                                                                        students
                                                                        submitted<br>
                                                                        <span class="text-reg">Due Sep 9</span>
                                                                    </div>

                                                                </div>

                                                                <!-- Right Side: Graph + Arrow -->
                                                                <div class="d-flex flex-column align-items-center ms-3">
                                                                    <!-- Graph -->
                                                                    <div class="me-5 mt-3">
                                                                        <canvas id="chart1" width="100"
                                                                            height="100"></canvas>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- Arrow at the bottom -->
                                                            <div class="d-flex justify-content-end">
                                                                <a href="#">
                                                                    <i class="fa-solid fa-arrow-right"
                                                                        style="color: var(--black);"></i>
                                                                </a>
                                                            </div>
                                                        </div>

                                                        <!-- Duplicate another card -->
                                                        <div class="card mb-3"
                                                            style="border-radius: 12px; border: 1px solid var(--black); padding: 15px;">
                                                            <div
                                                                class="d-flex align-items-center justify-content-between">
                                                                <!-- Left Info -->
                                                                <div class="flex-grow-1">
                                                                    <div class="mb-2 text-reg">
                                                                        <span class="badge rounded-pill"
                                                                            style="background: var(--highlight50); color: var(--black); font-size:12px;">Task</span>
                                                                    </div>
                                                                    <div class="text-bold">Assignment #2</div>
                                                                    <div class="text-sbold text-14 pt-1">COMP–007<br>
                                                                        <div class="text-reg text-14">Database Systems
                                                                        </div>
                                                                    </div>
                                                                    <div class="text-reg text-12 mt-2"
                                                                        style="color: var(--black);">
                                                                        <span class="text-sbold">45</span> of 60
                                                                        students
                                                                        submitted<br>
                                                                        <span class="text-reg">Due Sep 12</span>
                                                                    </div>
                                                                </div>

                                                                <!-- Right Side: Graph + Arrow -->
                                                                <div class="d-flex flex-column align-items-center ms-3">
                                                                    <!-- Graph -->
                                                                    <div class="me-5 mt-3">
                                                                        <canvas id="chart2" width="100"
                                                                            height="100"></canvas>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                            <!-- Arrow at the bottom -->
                                                            <div class="d-flex justify-content-end">
                                                                <a href="#">
                                                                    <i class="fa-solid fa-arrow-right"
                                                                        style="color: var(--black);"></i>
                                                                </a>
                                                            </div>
                                                        </div>
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
                            legend: { display: false },
                            tooltip: { enabled: false }
                        }
                    }
                });
            }

            createDoughnutChart('chart1', 29, 59);
            createDoughnutChart('chart2', 45, 60);
        </script>

</body>


</html>