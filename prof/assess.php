<?php
include("../shared/assets/database/connect.php");
$userID = '1';

$assessmentsQuery = "SELECT 
assessments.type, 
assessments.assessmentTitle, 
courses.courseID,
courses.courseCode, 
courses.courseTitle, 
DATE_FORMAT(assessments.deadline, '%b %e') AS assessmentDeadline
FROM assessments
INNER JOIN courses
	ON assessments.courseID = courses.courseID
WHERE courses.userID = $userID AND courses.isActive = 'Yes'";
$assessmentsResult = executeQuery($assessmentsQuery);
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Assess</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/profIndex.css">
    <link rel="stylesheet" href="../shared/assets/css/assess.css">
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
                            <!-- Header Section -->
                            <div class="row align-items-center mb-3 text-center text-lg-start">
                                <!-- Title -->
                                <div class="col-12 col-lg-auto mb-3 mb-lg-0">
                                    <h1 class="text-bold text-25 mb-0 mt-4" style="color: var(--black);">Assess
                                    </h1>
                                </div>

                                <!-- Dropdowns-->
                                <div class="col-12 col-lg-auto mt-4">
                                    <div
                                        class="d-flex flex-nowrap justify-content-center justify-content-lg-start gap-3">

                                        <!-- Type dropdown -->
                                        <div class="d-flex align-items-center flex-nowrap dropdown-container">
                                            <span class="dropdown-label me-2">Type</span>
                                            <button class="btn dropdown-toggle dropdown-custom" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <span>All</span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item text-reg" href="#">All</a></li>
                                                <li><a class="dropdown-item text-reg" href="#">Oldest</a></li>
                                                <li><a class="dropdown-item text-reg" href="#">Unread first</a></li>
                                            </ul>
                                        </div>

                                        <!-- Course dropdown -->
                                        <div class="d-flex align-items-center flex-nowrap dropdown-container">
                                            <span class="dropdown-label me-2">Course</span>
                                            <button class="btn dropdown-toggle dropdown-custom" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <span>All</span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item text-reg" href="#">All</a></li>
                                                <li><a class="dropdown-item text-reg" href="#">COMP-006</a></li>
                                                <li><a class="dropdown-item text-reg" href="#">Other courses</a>
                                                </li>
                                            </ul>
                                        </div>

                                        <!-- Sort By dropdown -->
                                        <div class="d-flex align-items-center flex-nowrap dropdown-container">
                                            <span class="dropdown-label me-2">Sort By</span>
                                            <button class="btn dropdown-toggle dropdown-custom" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <span>Newest</span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item text-reg" href="#">Newest</a></li>
                                                <li><a class="dropdown-item text-reg" href="#">COMP-006</a></li>
                                                <li><a class="dropdown-item text-reg" href="#">Other courses</a>
                                                </li>
                                            </ul>
                                        </div>

                                        <!-- Status dropdown -->
                                        <div class="d-flex align-items-center flex-nowrap dropdown-container">
                                            <span class="dropdown-label me-2">Status</span>
                                            <button class="btn dropdown-toggle dropdown-custom" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <span>Assigned</span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item text-reg" href="#">Assigned</a></li>
                                                <li><a class="dropdown-item text-reg" href="#">COMP-006</a></li>
                                                <li><a class="dropdown-item text-reg" href="#">Other courses</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Assessment Card -->
                                <?php
                                $chartsIDs = [];
                                if (mysqli_num_rows($assessmentsResult) > 0) {
                                    $i = 1;
                                    while ($assessments = mysqli_fetch_assoc($assessmentsResult)) {
                                        $chartsIDs[] = "chart$i";
                                ?>
                                        <div class="assessment-card mb-3">
                                            <div class="card-content">
                                                <!-- Top Row: Left Info and Submission Stats -->
                                                <div class="top-row">
                                                    <!-- Left Info -->
                                                    <div class="left-info">
                                                        <div class="mb-2 text-reg">
                                                            <span class="badge rounded-pill task-badge"><?php echo $assessments['type']; ?></span>
                                                        </div>
                                                        <div class="text-bold text-18 mb-2"><?php echo $assessments['assessmentTitle']; ?></div>
                                                        <div class="text-sbold text-14"><?php echo $assessments['courseCode']; ?><br>
                                                            <div class="text-reg text-14"><?php echo $assessments['courseTitle']; ?></div>
                                                        </div>
                                                    </div>

                                                    <!-- Submission Stats -->
                                                    <div class="submission-stats">
                                                        <div class="text-reg text-14 mb-1"><span class="stat-value">10</span> submitted</div>
                                                        <div class="text-reg text-14 mb-1"><span class="stat-value">11</span> pending submission</div>
                                                        <div class="text-reg text-14 mb-1"><span class="stat-value">0</span> graded</div>
                                                        <div class="text-reg text-14">Due <?php echo $assessments['assessmentDeadline']; ?></div>
                                                    </div>

                                                    <!-- Right Side: Progress Chart and Options -->
                                                    <div class="right-section">
                                                        <div class="chart-container">
                                                            <canvas id="chart<?php echo $i; ?>" width="120" height="120"></canvas>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Bottom Row: Action Buttons -->
                                                <div class="bottom-row">
                                                    <div class="action-buttons">
                                                        <button class="btn btn-action">
                                                            <img src="../shared/assets/img/assess/info.png"
                                                                alt="Assess Icon"
                                                                style="width: 20px; height: 20px; margin-right: 5px; object-fit: contain;">Task Details
                                                        </button>
                                                        <button class="btn btn-action">
                                                            <img src="../shared/assets/img/assess/assess.png"
                                                                alt="Assess Icon"
                                                                style="width: 20px; height: 20px; margin-right: 5px; object-fit: contain;">Grading
                                                            Sheet
                                                        </button>
                                                    </div>
                                                    <!-- More Options aligned with buttons on the right -->
                                                    <div class="options-container">
                                                        <div class="dropdown dropend">
                                                            <button class="btn btn-link more-options" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="fas fa-ellipsis-v"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li><a class="dropdown-item" href="#"><i class="fas fa-archive me-2"></i>Mark as Archived</a></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                <?php
                                        $i++;
                                    }
                                }
                                ?>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function createDoughnutChart(canvasId, submitted, pending, graded) {
            const ctx = document.getElementById(canvasId).getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [submitted, pending, graded],
                        backgroundColor: ['#3DA8FF', '#C7C7C7', '#E0E0E0'],
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

        document.addEventListener("DOMContentLoaded", function() {
            const chartsIDs = <?php echo json_encode($chartsIDs); ?>;
            chartsIDs.forEach(id => createDoughnutChart(id, 10, 11, 0));
        });
    </script>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>

</body>

</html>