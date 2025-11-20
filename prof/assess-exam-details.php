<?php
$activePage = 'assess-exam-details';
$activeTab = $_GET['tab'] ?? 'details';

include('../shared/assets/database/connect.php');
include("../shared/assets/processes/prof-session-process.php");

if (!isset($_GET['assessmentID'])) {
    echo "Assessment ID is missing in the URL.";
    exit;
}
$assessmentID = intval($_GET['assessmentID']);

$testInfoQuery = "
    SELECT *, assessments.createdAt AS assessmentCreatedAt
    FROM tests
    LEFT JOIN assessments ON tests.assessmentID = assessments.assessmentID
    LEFT JOIN courses ON assessments.courseID = courses.courseID
    LEFT JOIN users ON courses.userID = users.userID
    LEFT JOIN userinfo ON users.userID = userinfo.userID
    LEFT JOIN testquestions ON tests.testID = testquestions.testID
    WHERE assessments.assessmentID = $assessmentID
";

$testInfoResult = executeQuery($testInfoQuery);

if (!$test = mysqli_fetch_assoc($testInfoResult)) {
    echo "Test not found.";
    exit;
}
$testID = $test['testID'];

if ($testID == null) {
    echo "Test not found.";
    exit;
}

$testTitle = $test['assessmentTitle'];
$testDescription = $test['generalGuidance'];
$profName = $test['firstName'] . " " . $test['lastName'];
$profilePic = !empty($test['profilePicture'])
    ? '../shared/assets/pfp-uploads/' . $test['profilePicture']
    : '../shared/assets/img/default-profile.png';

$deadline = $test['deadline'];
$currentDate = date("Y-m-d H:i:s");
$isCompleted = (strtotime($currentDate) > strtotime($deadline));
$examStatus = $isCompleted ? "Completed" : "Active";
$examDuration = (isset($test['testTimelimit']) && $test['testTimelimit'] > 0)
    ? round($test['testTimelimit'] / 60)
    : 0;
$displayTime = $test['assessmentCreatedAt'];
$formattedTime = !empty($displayTime) ? date("F j, Y g:i A", strtotime($displayTime)) : "";

// Total exam items
$totalItemsQuery = "SELECT COUNT(*) AS totalItems FROM testquestions WHERE testID = $testID";
$totalItemsResult = executeQuery($totalItemsQuery);
$totalItems = mysqli_fetch_assoc($totalItemsResult)['totalItems'] ?? 0;

// Total exam points
$totalPointsQuery = "SELECT SUM(testQuestionPoints) AS totalPoints FROM testquestions WHERE testID = $testID";
$totalPointsResult = executeQuery($totalPointsQuery);
$totalPoints = mysqli_fetch_assoc($totalPointsResult)['totalPoints'] ?? 0;

// Total students enrolled in course
$courseID = $test['courseID'];
$totalStudentsQuery = "SELECT COUNT(*) AS totalStudents FROM enrollments WHERE courseID = $courseID";
$totalStudentsResult = executeQuery($totalStudentsQuery);
$totalStudents = mysqli_fetch_assoc($totalStudentsResult)['totalStudents'] ?? 0;
$assessmentID = $test['assessmentID'];

// Submitted count (Submitted + Graded)
$submittedQuery = "
    SELECT COUNT(*) AS submittedCount
    FROM todo
    WHERE assessmentID = $assessmentID AND (status = 'Submitted' OR status = 'Returned')
";

$submittedResult = executeQuery($submittedQuery);
$submittedCount = mysqli_fetch_assoc($submittedResult)['submittedCount'] ?? 0;

// Pending = total students - submitted
$pendingCount = $totalStudents - $submittedCount;
if ($pendingCount < 0) $pendingCount = 0;
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Assess Task Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/profIndex.css">
    <link rel="stylesheet" href="../shared/assets/css/assess-exam-details.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../shared/assets/img/webstar-icon.png">

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

                    <!-- Fixed Header -->
                    <div class="row mb-3">
                        <div class="col-12 cardHeader p-3 mb-4">

                            <!-- DESKTOP VIEW -->
                            <div class="row desktop-header d-none d-sm-flex">
                                <div class="col-auto me-2">
                                    <a href="assess.php" class="text-decoration-none">
                                        <i class="fa-solid fa-arrow-left text-reg text-16"
                                            style="color: var(--black);"></i>
                                    </a>
                                </div>
                                <div class="col">
                                    <span class="text-sbold text-25"><?php echo $testTitle; ?></span>
                                    <div class="text-reg text-18"><?php echo $examStatus; ?> · <?php echo date("M d, Y", strtotime($deadline)); ?></div>
                                </div>
                            </div>

                            <!-- MOBILE VIEW -->
                            <div class="d-block d-sm-none mobile-assignment">
                                <div class="mobile-top">
                                    <div class="arrow">
                                        <a href="assess.php" class="text-decoration-none">
                                            <i class="fa-solid fa-arrow-left text-reg text-16"
                                                style="color: var(--black);"></i>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <span class="text-sbold text-25"><?php echo $testTitle; ?></span>
                                        <div class="text-reg text-18"><?php echo $examStatus; ?> · <?php echo date("M d, Y", strtotime($deadline)); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Scrollable Content Container -->
                        <div class="content-scroll-container">
                            <div class="container-fluid py-3">
                                <div class="row">
                                    <!-- Left Content -->
                                    <div class="col-12 col-lg-8">
                                        <div class="p-0 px-lg-5">
                                            <div class="tab-carousel-wrapper d-block"
                                                style="--tabs-right-extend: 60px;">
                                                <div class="tab-scroll">
                                                    <ul class="nav nav-tabs custom-nav-tabs mb-3 flex-nowrap">
                                                        <li class="nav-item">
                                                            <a class="nav-link <?php echo $activeTab == 'details' ? 'active' : ''; ?>" href="assess-exam-details.php?assessmentID=<?php echo $assessmentID; ?>&tab=details">Exam Details</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link <?php echo $activeTab == 'submissions' ? 'active' : ''; ?>" href="assess-exam-submissions.php?assessmentID=<?php echo $assessmentID; ?>&tab=submissions">Submissions</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link <?php echo $activeTab == 'analytics' ? 'active' : ''; ?>" href="assess-exam-analytics.php?assessmentID=<?php echo $assessmentID; ?>&tab=analytics">Analytics</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <!-- Tab Content -->
                                            <div class="tab-content" id="myTabContent">
                                                <!-- Task Details Tab - Active -->
                                                <div class="tab-pane fade show active" id="announcements"
                                                    role="tabpanel" aria-labelledby="announcements-tab">
                                                    <div class="text-sbold text-14 mt-5">Exam General Instructions</div>
                                                    <p class="mb-5 mt-2 text-med text-14"><?php echo nl2br($testDescription) ?>
                                                    </p>

                                                    <hr>

                                                    <div class="text-sbold text-14 pb-3">Prepared by</div>
                                                    <div class="d-flex align-items-center pb-5">
                                                        <div class="rounded-circle me-2"
                                                            style="width: 50px; height: 50px; background-color: var(--highlight75);">
                                                            <img src="<?php echo $profilePic ?>" alt="Profile"
                                                                alt="professor" class="rounded-circle"
                                                                style="width:50px;height:50px;">
                                                        </div>
                                                        <div>
                                                            <div class="text-sbold text-14"><?php echo $profName; ?></div>
                                                            <div class="text-med text-12"><?php echo $formattedTime; ?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-lg-4">
                                        <div class="cardSticky position-sticky" style="top: 20px;">
                                            <div class="p-2">
                                                <div class="exam-details-title mb-5">Exam Details</div>
                                                <div class="text-center mb-3">
                                                    <div class="exam-stat-value mb-2"><?php echo $totalItems; ?></div>
                                                    <div class="exam-stat-label mb-5">Total Exam Items</div>

                                                    <div class="exam-stat-value mb-2"><?php echo $totalPoints; ?></div>
                                                    <div class="exam-stat-label mb-5">Total Exam Points</div>

                                                    <div class="exam-stat-value mb-2"><?php echo $examDuration; ?> mins</div>
                                                    <div class="exam-stat-label">Exam Duration</div>
                                                </div>

                                                <div class="row align-items-center justify-content-center mt-5 mb-3">
                                                    <div class="col-auto">
                                                        <div class="chart-container"
                                                            style="width: 100px; height: 100px;">
                                                            <canvas id="taskChart" width="100" height="100"></canvas>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <!-- Submission Stats -->
                                                        <div class="submission-stats">
                                                            <div class="text-reg text-14 mt-2"><span
                                                                    class="stat-value"><?php echo $submittedCount; ?></span> submitted</div>
                                                            <div class="text-reg text-14"><span
                                                                    class="stat-value"><?php echo $pendingCount; ?></span> pending submission</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- Close content-scroll-container -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
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

        createDoughnutChart('taskChart', <?php echo $submittedCount; ?>, <?php echo $pendingCount; ?>);
    </script>
</body>

</html>