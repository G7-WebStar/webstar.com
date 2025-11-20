<?php
$activePage = 'assess-exam-submissions';
$activeTab = $_GET['tab'] ?? 'submissions';

include('../shared/assets/database/connect.php');
include("../shared/assets/processes/prof-session-process.php");
date_default_timezone_set('Asia/Manila');

if (!isset($_GET['assessmentID'])) {
    echo "Assessment ID is missing in the URL.";
    exit;
}
$assessmentID = intval($_GET['assessmentID']);

// Default filters
$sortBy = $_GET['sortBy'] ?? 'All';
$statusFilter = $_GET['status'] ?? 'All';

$testInfoQuery = "
    SELECT *
    FROM tests
    LEFT JOIN assessments ON tests.assessmentID = assessments.assessmentID
    WHERE assessments.assessmentID = $assessmentID
";
$testInfoResult = executeQuery($testInfoQuery);

if (!$test = mysqli_fetch_assoc($testInfoResult)) {
    echo "Test not found.";
    exit;
}

$testID       = $test['testID'];
$testTitle    = $test['assessmentTitle'] ?? 'No Submissions';
$deadline     = $test['deadline'] ?? date("Y-m-d H:i:s");
$currentDate  = date("Y-m-d H:i:s");
$isCompleted  = (strtotime($currentDate) > strtotime($deadline));
$examStatus   = $isCompleted ? "Completed" : "Active";
$examDuration = (isset($test['testTimelimit']) && $test['testTimelimit'] > 0)
    ? round($test['testTimelimit'] / 60)
    : 0;
$courseID     = $test['courseID'] ?? 0;

// Fetch distinct course codes for dropdown
$courseCodesQuery = "
    SELECT DISTINCT courseCode 
    FROM courses 
    ORDER BY courseCode ASC
";
$courseCodesResult = executeQuery($courseCodesQuery);

// Status filter
$statusSQL = "";
if ($statusFilter === "Submitted") {
    $statusSQL = "AND (todo.status = 'Submitted' OR todo.status = 'Returned') AND scores.score IS NOT NULL";
} elseif ($statusFilter === "Pending") {
    $statusSQL = "AND todo.status IS NULL AND scores.score IS NULL AND NOW() < assessments.deadline";
} elseif ($statusFilter === "Missing") {
    $statusSQL = "AND todo.status IS NULL AND scores.score IS NULL AND NOW() > assessments.deadline";
}

// Fetch student submissions
$testSubmissionQuery = "
    SELECT *
    FROM users
    LEFT JOIN userinfo ON users.userID = userinfo.userID
    LEFT JOIN enrollments ON users.userID = enrollments.userID
    LEFT JOIN courses ON enrollments.courseID = courses.courseID
    LEFT JOIN assessments ON courses.courseID = assessments.courseID
    LEFT JOIN todo ON todo.userID = users.userID AND todo.assessmentID = assessments.assessmentID
    LEFT JOIN tests ON assessments.assessmentID = tests.assessmentID
    LEFT JOIN scores ON scores.userID = users.userID AND scores.testID = tests.testID
    WHERE assessments.assessmentID = $assessmentID
    $statusSQL
    ORDER BY lastName ASC
";

$testSubmissionResult = executeQuery($testSubmissionQuery);

// Total exam items
$totalItemsQuery = "SELECT COUNT(*) AS totalItems FROM testquestions WHERE testID = $testID";
$totalItemsResult = executeQuery($totalItemsQuery);
$totalItems = mysqli_fetch_assoc($totalItemsResult)['totalItems'] ?? 0;

// Total exam points
$totalPointsQuery = "SELECT SUM(testQuestionPoints) AS totalPoints FROM testquestions WHERE testID = $testID";
$totalPointsResult = executeQuery($totalPointsQuery);
$totalPoints = mysqli_fetch_assoc($totalPointsResult)['totalPoints'] ?? 0;

// Total students enrolled
$totalStudentsQuery = "SELECT COUNT(*) AS totalStudents FROM enrollments WHERE courseID = $courseID";
$totalStudentsResult = executeQuery($totalStudentsQuery);
$totalStudents = mysqli_fetch_assoc($totalStudentsResult)['totalStudents'] ?? 0;

// Handle Return All submission
if (isset($_POST['returnAll']) && isset($_POST['assessmentID'])) {
    $updateAssessmentID = intval($_POST['assessmentID']);
    $updateQuery = "
        UPDATE todo
        SET todo.status = 'Returned'
        WHERE todo.assessmentID = $assessmentID
        AND todo.status = 'Submitted'
    ";
    executeQuery($updateQuery);

    // Get professor name
    $profNameQuery = "
        SELECT CONCAT(userinfo.firstName, ' ', userinfo.lastName) AS profName
        FROM courses
        INNER JOIN userinfo ON courses.userID = userinfo.userID
        WHERE courses.courseID = $courseID
        LIMIT 1
    ";
    $profNameResult = executeQuery($profNameQuery);
    $profNameRow = mysqli_fetch_assoc($profNameResult);
    $profName = mysqli_real_escape_string($conn, $profNameRow['profName'] ?? 'Professor');

    // Escape test title for SQL
    $testTitleEscaped = mysqli_real_escape_string($conn, $testTitle);

    // Insert notifications for all students who have submissions
    $notificationQuery = "
        INSERT INTO inbox (enrollmentID, messageText, notifType, createdAt)
        SELECT 
            enrollments.enrollmentID,
            CONCAT('\"', '$testTitleEscaped', '\" was returned by your instructor. You can now view the results.'),
            'Submissions Update',
            NOW()
        FROM todo
        INNER JOIN enrollments ON todo.userID = enrollments.userID AND enrollments.courseID = $courseID
        WHERE todo.assessmentID = $assessmentID 
        AND todo.status = 'Returned'
    ";
    executeQuery($notificationQuery);

    // Set success message in session
    $_SESSION['success'] = 'All submissions have been returned successfully!';

    // Redirect to prevent form resubmission
    header("Location: assess-exam-submissions.php?assessmentID=" . $assessmentID . "&tab=submissions");
    exit();
}

// Fetch submissions **after** potential update
$testSubmissionResult = executeQuery($testSubmissionQuery);

// Calculate submitted and pending counts
$submittedQuery = "
    SELECT COUNT(*) AS submittedCount
    FROM todo
    WHERE assessmentID = $assessmentID AND (status = 'Submitted' OR status = 'Returned')
";
$submittedResult = executeQuery($submittedQuery);
$submittedCount = mysqli_fetch_assoc($submittedResult)['submittedCount'] ?? 0;

$pendingCount = $totalStudents - $submittedCount;
if ($pendingCount < 0) $pendingCount = 0;

// Count ungraded submissions for button logic
$ungradedQuery = "
    SELECT COUNT(*) AS ungradedCount
    FROM todo
    LEFT JOIN scores ON todo.userID = scores.userID AND scores.testID = $testID
    WHERE todo.assessmentID = $assessmentID
    AND (todo.status = 'Submitted' OR todo.status = 'Pending')
    AND scores.score IS NULL
";
$ungradedResult = executeQuery($ungradedQuery);
$ungradedCount = mysqli_fetch_assoc($ungradedResult)['ungradedCount'] ?? 0;
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Assess Exam Submissions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/profIndex.css">
    <link rel="stylesheet" href="../shared/assets/css/assess-exam-submissions.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=assignment_return">
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

                    <!-- Toast Container -->
                    <div id="toastContainer"
                        class="position-absolute top-0 start-50 translate-middle-x pt-5 pt-md-1 d-flex flex-column align-items-center"
                        style="z-index:1100; pointer-events:none;">
                    </div>

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
                                                <!-- Task Details Tab - Disabled -->
                                                <div class="tab-pane fade" id="announcements" role="tabpanel"
                                                    aria-labelledby="announcements-tab">
                                                    <!-- Empty content - tab is disabled -->
                                                </div>

                                                <!-- Submissions Tab -->
                                                <div class="tab-pane fade show active" id="lessons" role="tabpanel"
                                                    aria-labelledby="lessons-tab">
                                                    <!-- Filters row: Sort By + Status + Return All -->
                                                    <div class="d-flex align-items-center flex-wrap dropdown-container">
                                                        <!-- Sort By -->
                                                        <form method="GET" class="d-flex align-items-center flex-nowrap me-3">
                                                            <input type="hidden" name="assessmentID" value="<?php echo $assessmentID; ?>">
                                                            <input type="hidden" name="status" id="statusInput" value="<?php echo $statusFilter; ?>">
                                                            <span class="dropdown-label me-2">Status</span>
                                                            <div class="custom-dropdown">
                                                                <button type="button" class="dropdown-btn text-reg text-14"><?php echo $statusFilter; ?></button>
                                                                <ul class="dropdown-list text-reg text-14">
                                                                    <li data-value="All">All</li>
                                                                    <li data-value="Pending">Pending</li>
                                                                    <li data-value="Submitted">Submitted</li>
                                                                    <li data-value="Missing">Missing</li>
                                                                </ul>
                                                            </div>
                                                        </form>

                                                        <?php
                                                        // Determine if the Return All button should be disabled
                                                        $buttonDisabled = false;

                                                        if ($currentDate < $deadline && $ungradedCount > 0) {
                                                            $buttonDisabled = true;
                                                        } elseif ($currentDate < $deadline && $ungradedCount == 0) {
                                                            $buttonDisabled = false;
                                                        } elseif ($currentDate >= $deadline && $ungradedCount > 0) {
                                                            $buttonDisabled = true;
                                                        } else {
                                                            $buttonDisabled = false;
                                                        }
                                                        ?>

                                                        <!-- Return All -->
                                                        <form method="POST" class="ms-auto flex-shrink-0">
                                                            <input type="hidden" name="assessmentID" value="<?php echo $assessmentID; ?>">
                                                            <button type="submit" name="returnAll"
                                                                class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14 d-inline-flex align-items-center btn-return-all"
                                                                style="background-color: var(--primaryColor); border: 1px solid var(--black); margin-right: auto; height: 27px; pointer-events:auto;"
                                                                <?php echo $buttonDisabled ? 'disabled title="Return All available after deadline or successful test completion"' : ''; ?>>
                                                                <span class="material-symbols-outlined">assignment_return</span>
                                                                Return All
                                                            </button>
                                                        </form>
                                                    </div>


                                                    <!-- Submissions List -->
                                                    <div class="submissions-list mt-4">
                                                        <?php if (mysqli_num_rows($testSubmissionResult) > 0): ?>
                                                            <?php while ($test = mysqli_fetch_assoc($testSubmissionResult)): ?>
                                                                <?php
                                                                $profilePic = !empty($test['profilePicture'])
                                                                    ? '../shared/assets/pfp-uploads/' . $test['profilePicture']
                                                                    : '../shared/assets/pfp-uploads/defaultProfile.png';
                                                                $score = $test['score'];
                                                                $status = $test['status'];

                                                                if (!is_null($score)) {
                                                                    $submissionStatus = $score . ' / ' . $totalPoints;
                                                                    $badgeClass = 'badge-score';
                                                                } else {
                                                                    if ($currentDate > $deadline) {
                                                                        $submissionStatus = 'Missing';
                                                                        $badgeClass = 'badge-missing';
                                                                    } else {
                                                                        $submissionStatus = 'Pending';
                                                                        $badgeClass = 'badge-pending';
                                                                    }
                                                                }
                                                                ?>
                                                                <div class="submission-item d-flex align-items-center py-3 border-bottom">
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="avatar me-3" style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden;">
                                                                            <img src="<?php echo $profilePic ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                                                                        </div>
                                                                        <span class="text-sbold text-16 text-truncate"
                                                                            style="width:100px"
                                                                            title="<?php echo $test['lastName'] . ', ' . $test['firstName']; ?>">
                                                                            <?php echo $test['lastName'] . ', ' . $test['firstName']; ?>
                                                                        </span>

                                                                    </div>
                                                                    <div class="flex-grow-1 d-flex justify-content-center submission-center">
                                                                        <span class="badge <?php echo $badgeClass; ?>"><?php echo $submissionStatus; ?></span>
                                                                    </div>
                                                                    <div class="ms-auto d-flex align-items-center submission-right">
                                                                        <?php
                                                                        // Convert seconds to minutes
                                                                        $timeSpentMinutes = (!isset($test['timeSpent']) || $test['timeSpent'] == 0)
                                                                            ? 0
                                                                            : ceil($test['timeSpent'] / 60);

                                                                        $timeSpentDisplay = ($timeSpentMinutes == 0) ? '-' : $timeSpentMinutes . ' mins';
                                                                        ?>
                                                                        <span class="badge-time"><?php echo $timeSpentDisplay; ?></span>
                                                                    </div>
                                                                </div>
                                                            <?php endwhile; ?>

                                                        <?php elseif (!empty($statusFilter) && $statusFilter != 'All'): ?>
                                                            <div class="empty-state d-flex flex-column justify-content-center align-items-center text-center py-5">
                                                                <?php if ($statusFilter == 'Pending'): ?>
                                                                    <img src="../shared/assets/img/courseInfo/puzzle.png" alt="No Submissions" class="empty-state-img" style="filter: grayscale(100%) brightness(2) contrast(0.6) opacity(0.8); width: 100px;">
                                                                    <div class="empty-state-text text-reg text-14">
                                                                        <p class="text-med mt-1 mb-0">No pending submissions at the moment.</p>
                                                                        <p class="text-reg mt-1">All students have either submitted or passed the deadline.</p>
                                                                    </div>

                                                                <?php elseif ($statusFilter == 'Missing' || $sortTodo == 'Missing'): ?>
                                                                    <img src="../shared/assets/img/courseInfo/thumbs-up.png" alt="No Submissions" class="empty-state-img" style="filter: grayscale(100%) brightness(2) contrast(0.6) opacity(0.8); width: 100px;">
                                                                    <div class="empty-state-text text-reg text-14">
                                                                        <p class="text-med mt-1 mb-0">No submissions before the deadline.</p>
                                                                        <p class="text-reg mt-1">They are now marked as missing.</p>
                                                                    </div>

                                                                <?php elseif ($statusFilter == 'Submitted' || $statusFilter == 'Returned'): ?>
                                                                    <img src="../shared/assets/img/courseInfo/file.png" alt="No Submissions" class="empty-state-img" style="filter: grayscale(100%) brightness(2) contrast(0.6) opacity(0.8); width: 100px;">
                                                                    <div class="empty-state-text text-reg text-14">
                                                                        <p class="text-med mt-1 mb-0">No submissions yet.</p>
                                                                        <p class="text-reg mt-1">No students have submitted or returned their work for this assessment.</p>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endif; ?>
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

                                                    <div class="exam-stat-value mb-2"><?php echo $examDuration . ' ' . ($examDuration == 1 ? 'min' : 'mins'); ?></div>
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
                                                                    class="stat-value"><?php echo $pendingCount; ?></span> pending submission
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
        <!-- Dropdown js -->
        <script>
            document.querySelectorAll('.custom-dropdown').forEach(dropdown => {
                const btn = dropdown.querySelector('.dropdown-btn');
                const list = dropdown.querySelector('.dropdown-list');

                btn.addEventListener('click', () => {
                    list.style.display = list.style.display === 'block' ? 'none' : 'block';
                });

                list.querySelectorAll('li').forEach(item => {
                    item.addEventListener('click', () => {
                        const value = item.dataset.value;
                        btn.textContent = value;

                        // Update hidden input(s) in the same form
                        const form = dropdown.closest('form');
                        if (form.querySelector('input[name="status"]')) {
                            form.querySelector('input[name="status"]').value = value;
                        }

                        form.submit(); // submit **this form**
                        list.style.display = 'none';
                    });
                });

                document.addEventListener('click', (e) => {
                    if (!dropdown.contains(e.target)) {
                        list.style.display = 'none';
                    }
                });
            });
        </script>

        <!-- Toast Script -->
        <script>
            function showSuccessToast(message) {
                var container = document.getElementById('toastContainer');
                if (!container) return;

                var alertEl = document.createElement('div');
                alertEl.className = 'alert alert-success mb-2 shadow-lg text-med text-12 d-flex align-items-center justify-content-center gap-2 px-3 py-2';
                alertEl.role = 'alert';
                alertEl.style.borderRadius = '8px';
                alertEl.style.display = 'flex';
                alertEl.style.alignItems = 'center';
                alertEl.style.gap = '8px';
                alertEl.style.padding = '0.5rem 0.75rem';
                alertEl.style.textAlign = 'center';
                alertEl.style.backgroundColor = '#d1e7dd';
                alertEl.style.color = '#0f5132';
                alertEl.style.transition = 'opacity 0.5s ease-out';
                alertEl.style.opacity = '1';
                alertEl.innerHTML = '<i class="bi bi-check-circle-fill fs-6" style="color: var(--black);"></i>' +
                    '<span style="color: var(--black);">' + message + '</span>';

                container.appendChild(alertEl);

                setTimeout(function() {
                    alertEl.style.opacity = '0';
                    setTimeout(function() {
                        if (alertEl && alertEl.parentNode) {
                            alertEl.parentNode.removeChild(alertEl);
                        }
                    }, 500);
                }, 3000);
            }

            // Show toast on page load if success message exists
            <?php if (isset($_SESSION['success'])): ?>
                document.addEventListener('DOMContentLoaded', function() {
                    showSuccessToast('<?php echo addslashes($_SESSION['success']); ?>');
                });
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
        </script>

</body>

</html>