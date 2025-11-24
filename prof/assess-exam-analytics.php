<?php
$activePage = 'assess-exam-analytics';
$activeTab = $_GET['tab'] ?? 'analytics';

include('../shared/assets/database/connect.php');
include("../shared/assets/processes/prof-session-process.php");

// Get the assessmentID from URL
if (!isset($_GET['assessmentID'])) {
    echo "Assessment ID is missing in the URL.";
    exit;
}
$assessmentID = intval($_GET['assessmentID']);

// Get testID from assessmentID
$testQuery = "SELECT testID FROM tests WHERE assessmentID = $assessmentID LIMIT 1";
$testResult = executeQuery($testQuery);
$testRow = mysqli_fetch_assoc($testResult);
$testID = $testRow['testID'] ?? 0; // fallback to 0 if not found

// Get test info directly from tests so it exists even if no students
$testInfoQuery = "
    SELECT 
    tests.testID, 
    assessments.assessmentTitle, 
    assessments.deadline, 
    assessments.courseID, 
    tests.testTimelimit, 
    assessments.assessmentID
    FROM tests
    INNER JOIN assessments ON tests.assessmentID = assessments.assessmentID
    WHERE tests.testID = $testID
    LIMIT 1
";
$testInfoResult = executeQuery($testInfoQuery);
$test = mysqli_fetch_assoc($testInfoResult);

if (!$test) {
    echo "Test not found.";
    exit;
}

// Total exam points
$totalPointsQuery = "SELECT SUM(testQuestionPoints) AS totalPoints FROM testquestions WHERE testID = $testID";
$totalPointsResult = executeQuery($totalPointsQuery);
$totalPoints = mysqli_fetch_assoc($totalPointsResult)['totalPoints'] ?? 0;

$testAnalyticQuery = "
    SELECT *
    FROM users
    LEFT JOIN userinfo ON users.userID = userinfo.userID
    LEFT JOIN enrollments ON users.userID = enrollments.userID
    LEFT JOIN courses ON enrollments.courseID = courses.courseID
    LEFT JOIN assessments ON courses.courseID = assessments.courseID
    LEFT JOIN tests ON assessments.assessmentID = tests.assessmentID
    LEFT JOIN todo ON todo.userID = users.userID AND todo.assessmentID = assessments.assessmentID
    LEFT JOIN scores ON scores.userID = users.userID AND scores.testID = tests.testID
    LEFT JOIN testresponses on testresponses.userID = users.userID and testresponses.testID = tests.testID
    LEFT JOIN testquestions on testquestions.testID = tests.testID
    WHERE assessments.assessmentID = $assessmentID
";

$testAnalyticResult = executeQuery($testAnalyticQuery);

$totalScore = 0;
$scoreCount = 0;
$passedCount = 0;
$passingThreshold = 0.5; // 50% of total points
$totalTimeSpent = 0;
$timeCount = 0;

// Reset pointer to loop all rows including first row we fetched
mysqli_data_seek($testAnalyticResult, 0);
while ($row = mysqli_fetch_assoc($testAnalyticResult)) {
    if ($row['score'] !== null) {
        $totalScore += $row['score'];
        $scoreCount++;
        if ($row['score'] >= ($totalPoints * $passingThreshold)) {
            $passedCount++;
        }
    }

    if (isset($row['timeSpent']) && $row['timeSpent'] !== null) {
        $totalTimeSpent += $row['timeSpent'];
        $timeCount++;
    }
}

// Average score and percentage
$averageScore = ($scoreCount > 0) ? $totalScore / $scoreCount : 0;
$averagePercent = ($totalPoints > 0) ? round(($averageScore / $totalPoints) * 100) : 0;
// Passing rate percentage
$passingPercent = ($scoreCount > 0) ? round(($passedCount / $scoreCount) * 100) : 0;
// Compute average time spent
$averageTimeSpent = ($timeCount > 0) ? round(($totalTimeSpent / $timeCount) / 60) : 0;

$testTitle = $test['assessmentTitle'];
$deadline = $test['deadline'];
$currentDate = date("Y-m-d H:i:s");
$isCompleted = (strtotime($currentDate) > strtotime($deadline));
$examStatus = $isCompleted ? "Completed" : "Active";
$examDuration = (isset($test['testTimelimit']) && $test['testTimelimit'] > 0)
    ? round($test['testTimelimit'] / 60)
    : 0;
$courseID = $test['courseID'];
$assessmentID = $test['assessmentID'];

// Total exam items
$totalItemsQuery = "SELECT COUNT(*) AS totalItems FROM testquestions WHERE testID = $testID";
$totalItemsResult = executeQuery($totalItemsQuery);
$totalItems = mysqli_fetch_assoc($totalItemsResult)['totalItems'] ?? 0;

// Score Range Computation (fixed to 6 bars)
$segments = 6;
$scoreRanges = array_fill(0, $segments, 0);
$scoreLabels = [];

if ($totalPoints > 0) {
    $rangeSize = ceil($totalPoints / $segments);

    // Get all scored students
    $allScoresQuery = "SELECT score FROM scores WHERE testID = $testID";
    $allScoresResult = executeQuery($allScoresQuery);

    while ($row = mysqli_fetch_assoc($allScoresResult)) {
        $score = floatval($row['score']);
        $segmentIndex = min(floor($score / $rangeSize), $segments - 1);
        $scoreRanges[$segmentIndex]++;
    }

    for ($i = 0; $i < $segments; $i++) {
        $start = $i * $rangeSize;
        $end = min(($i + 1) * $rangeSize - 1, $totalPoints);
        $scoreLabels[] = $start . '-' . $end;
    }
}

// Total students enrolled
$totalStudentsQuery = "SELECT COUNT(*) AS totalStudents FROM enrollments WHERE courseID = $courseID";
$totalStudentsResult = executeQuery($totalStudentsQuery);
$totalStudents = mysqli_fetch_assoc($totalStudentsResult)['totalStudents'] ?? 0;

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
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Assess Exam Analytics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/profIndex.css">
    <link rel="stylesheet" href="../shared/assets/css/assess-exam-analytics.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
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
                                                <!-- Task Details Tab - Disabled -->
                                                <div class="tab-pane fade" id="announcements" role="tabpanel"
                                                    aria-labelledby="announcements-tab">
                                                    <!-- Empty content - tab is disabled -->
                                                </div>

                                                <!-- Analytics Summary (replaces submissions list) -->
                                                <div class="analytics-summary mt-5">
                                                    <div class="text-center mb-3">
                                                        <div class="mb-2">
                                                            <span class="material-symbols-rounded"
                                                                style="vertical-align: middle;color: var(--black);">analytics</span>
                                                            <span class="analytics-score-value"
                                                                style="margin-left: 6px;"><?php echo $averagePercent; ?>%</span>
                                                        </div>
                                                        <div class="analytics-score-label" style="margin-top: -6px;">
                                                            average score</div>
                                                        <div class="analytics-score-desc mt-3"
                                                            style="max-width: 520px; margin: 0 auto;">
                                                            Most students scored nearly the same,<br>
                                                            showing balanced understanding.
                                                        </div>
                                                    </div>

                                                    <div class="row g-4 g-md-5 mt-2">
                                                        <div class="col-12 col-md-6">
                                                            <div class="analytics-metric text-center">
                                                                <div class="mb-2">
                                                                    <span class="material-symbols-rounded"
                                                                        style="vertical-align: middle;color: var(--black);">
                                                                        check_circle
                                                                    </span>
                                                                    <span class="analytics-score-value"
                                                                        style=" margin-left: 8px;"><?php echo $passingPercent; ?>%</span>
                                                                </div>
                                                                <div class="analytics-score-label">passing rate</div>
                                                                <div class="analytics-score-desc mt-3">
                                                                    who met the passing score</div>
                                                            </div>
                                                        </div>

                                                        <div class="col-12 col-md-6">
                                                            <div class="analytics-metric text-center">
                                                                <div class="mb-2">
                                                                    <span class="material-symbols-rounded alarm-icon"
                                                                        style="vertical-align: middle;">
                                                                        alarm
                                                                    </span>
                                                                    <span class="analytics-score-value"
                                                                        style=" margin-left: 8px;"><?php echo $averageTimeSpent . ' ' . ($averageTimeSpent == 1 ? 'min' : 'mins'); ?></span>
                                                                </div>
                                                                <div class="analytics-score-label">average time spent
                                                                </div>
                                                                <div class="analytics-score-desc mt-3">
                                                                    typical completion time</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-4 ms-lg-auto">
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
                                                                    class="stat-value"><?php echo $submittedCount; ?></span>
                                                                submitted</div>
                                                            <div class="text-reg text-14"><span
                                                                    class="stat-value"><?php echo $pendingCount; ?></span>
                                                                pending submission</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Full Width Score Range Overview Chart -->
                                <div class="row chart-section">
                                    <div class="col-12">
                                        <!-- Chart Title (outside chart container) -->
                                        <div class="chart-title mb-3">
                                            <h5 class="text-sbold text-18 mb-1">Score Range Overview</h5>
                                            <p class="text-reg text-14 text-muted">Displays the number of students in each score bracket</p>
                                        </div>

                                        <!-- Chart Container -->
                                        <div class="score-range-chart-full">
                                            <div class="chart-container-full">
                                                <canvas id="scoreRangeChartFull" width="800" height="200"></canvas>
                                            </div>
                                        </div>

                                        <!-- Item Analysis section (content before badges) -->
                                        <div class="item-analysis mt-4">
                                            <div class="section-title">Item Analysis (Per Question)</div>
                                            <div class="section-desc">Performance insights per question to spot unclear or overly difficult items.</div>
                                            <hr class="item-divider mt-4 mb-4">

                                            <?php
                                            // Fetch all questions for this test
                                            $questionsQuery = "SELECT * FROM testquestions WHERE testID = $testID";
                                            $questionsResult = executeQuery($questionsQuery);
                                            $questions = [];

                                            while ($q = mysqli_fetch_assoc($questionsResult)) {
                                                $qID = $q['testQuestionID'];
                                                $questions[$qID] = [
                                                    'questionText' => $q['testQuestion'],
                                                    'questionType' => $q['questionType'],
                                                    'correctAnswer' => $q['correctAnswer'],
                                                    'choices' => [],
                                                    'totalResponses' => 0,
                                                    'correctCount' => 0,
                                                    'wrongCount' => 0,
                                                    'studentsCorrect' => [],
                                                    'studentsWrong' => []
                                                ];
                                                // Fetch choices for Multiple Choice
                                                if ($q['questionType'] == "Multiple Choice") {
                                                    $choicesQuery = "SELECT * FROM testquestionchoices WHERE testQuestionID = $qID";
                                                    $choicesResult = executeQuery($choicesQuery);
                                                    while ($c = mysqli_fetch_assoc($choicesResult)) {
                                                        $questions[$qID]['choices'][$c['choiceText']] = 0;
                                                    }
                                                }
                                            }
                                            // Count student responses
                                            $responsesQuery = "
                                                SELECT * 
                                                FROM users 
                                                LEFT JOIN userinfo ON users.userID = userinfo.userID 
                                                LEFT JOIN testresponses ON users.userID = testresponses.userID AND testresponses.testID = $testID 
                                                LEFT JOIN scores ON users.userID = scores.userID AND scores.testID = $testID 
                                                LEFT JOIN tests ON tests.testID = $testID
                                            ";
                                            $responsesResult = executeQuery($responsesQuery);

                                            while ($r = mysqli_fetch_assoc($responsesResult)) {

                                                // Skip rows without a questionID (happens when no students yet)
                                                if (!isset($r['testQuestionID']) || $r['testQuestionID'] === null) {
                                                    continue;
                                                }

                                                $qID = intval($r['testQuestionID']);

                                                // Skip if question not found
                                                if (!isset($questions[$qID])) continue;

                                                // Count total responses for this question
                                                $questions[$qID]['totalResponses']++;

                                                $fullName = trim(($r['firstName'] ?? '') . ' ' . ($r['lastName'] ?? ''));

                                                // Correct or wrong classification
                                                if (isset($r['isCorrect']) && $r['isCorrect'] == 1) {
                                                    $questions[$qID]['correctCount']++;
                                                    $questions[$qID]['studentsCorrect'][] = $fullName;
                                                } else {
                                                    $questions[$qID]['wrongCount']++;
                                                    $questions[$qID]['studentsWrong'][] = $fullName;
                                                }

                                                // Multiple choice answers
                                                if ($questions[$qID]['questionType'] == "Multiple Choice") {
                                                    $answer = isset($r['userAnswer']) ? trim($r['userAnswer']) : '';
                                                    if ($answer !== '' && isset($questions[$qID]['choices'][$answer])) {
                                                        $questions[$qID]['choices'][$answer]++;
                                                    }
                                                }
                                            }

                                            // Render questions
                                            foreach ($questions as $qID => $q):
                                                $total = $q['totalResponses'];
                                                $correct = $q['correctCount'];
                                                $wrong = $q['wrongCount'];
                                                $correctPercent = ($total > 0) ? round(($correct / $total) * 100) : 0;
                                            ?>

                                                <div class="question-block mt-4">
                                                    <div class="question-text mb-2">
                                                        <?php echo htmlspecialchars($q['questionText']); ?>
                                                    </div>
                                                    <!-- Correct answer for Identification -->
                                                    <?php if ($q['questionType'] == "Identification"): ?>
                                                        <div class="correct-answer mb-2 choice-ok">
                                                            Correct Answer: <?php echo htmlspecialchars($q['correctAnswer']); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="chart-badges mt-3">
                                                        <span class="metric-badge badge-blue">
                                                            <?php echo $correctPercent; ?>% answered correctly
                                                        </span>
                                                        <span class="metric-badge badge-green"
                                                            style="cursor: <?php echo $correct > 0 ? 'pointer' : 'default'; ?>;"
                                                            <?php echo $correct > 0 ? 'data-bs-toggle="modal" data-bs-target="#modalCorrect' . $qID . '"' : ''; ?>>
                                                            <?php echo $correct; ?> students got it right
                                                        </span>
                                                        <span class="metric-badge badge-red"
                                                            style="cursor: <?php echo $wrong > 0 ? 'pointer' : 'default'; ?>;"
                                                            <?php echo $wrong > 0 ? 'data-bs-toggle="modal" data-bs-target="#modalWrong' . $qID . '"' : ''; ?>>
                                                            <?php echo $wrong; ?> students got it wrong
                                                        </span>
                                                    </div>

                                                    <?php if ($q['questionType'] == "Multiple Choice"): ?>
                                                        <ul class="choice-list mt-3">
                                                            <?php foreach ($q['choices'] as $choice => $count):
                                                                $percent = ($total > 0 ? round(($count / $total) * 100) : 0);
                                                                $isCorrect = ($choice == $q['correctAnswer']);
                                                            ?>
                                                                <li class="<?php echo $isCorrect ? 'choice-ok' : ''; ?>">
                                                                    <?php echo htmlspecialchars($choice); ?> -
                                                                    <?php echo $count; ?> students -
                                                                    <?php echo $percent; ?>%
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    <?php endif; ?>
                                                </div>
                                                <hr class="item-divider mt-4 mb-4">
                                            <?php endforeach; ?>
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

    <!-- Modal for correct and wrong answers -->
    <?php foreach ($questions as $qID => $q): ?>
        <!-- Correct Students Modal -->
        <div class="modal fade" id="modalCorrect<?php echo $qID; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-sbold text-20 ps-1">Students Who Got It Right</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            style="width: 20px; height: 20px; font-size: 0.75rem;"></button>
                    </div>
                    <div class="modal-body text-med text-16">
                        <ul>
                            <?php foreach ($q['studentsCorrect'] as $student): ?>
                                <li><?php echo htmlspecialchars($student); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="modal-footer border-top" style="padding-top: 45px;"></div>
                </div>
            </div>
        </div>
        <!-- Wrong Students Modal -->
        <div class="modal fade" id="modalWrong<?php echo $qID; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-sbold text-20 ps-1">Students Who Got It Wrong</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            style="width: 20px; height: 20px; font-size: 0.75rem;"></button>
                    </div>
                    <div class="modal-body text-med text-16">
                        <ul>
                            <?php foreach ($q['studentsWrong'] as $student): ?>
                                <li><?php echo htmlspecialchars($student); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="modal-footer border-top" style="padding-top: 45px;"></div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Doughnut Chart
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
        //Initialize doughnut chart
        createDoughnutChart('taskChart', <?php echo $submittedCount; ?>, <?php echo $pendingCount; ?>);
        //REQUIRED: Auto-generate score ranges
        function generateScoreRanges(totalItems) {
            const ranges = [];
            const segments = 6;

            if (totalItems <= 0) return ranges;

            const rangeSize = Math.ceil(totalItems / segments);
            let start = 0;

            for (let i = 0; i < segments; i++) {
                let end = start + rangeSize - 1;

                if (end > totalItems) {
                    end = totalItems;
                }

                ranges.push(`${start}-${end}`);
                start = end + 1;
            }

            return ranges;
        }

        // Score range values from PHP
        const scoreRangeData = <?php echo json_encode($scoreRanges); ?>;

        // FULL WIDTH SCORE RANGE BAR CHART
        function createScoreRangeChartFull(totalPoints) {
            const ctx = document.getElementById('scoreRangeChartFull').getContext('2d');

            const labels = generateScoreRanges(totalPoints);
            const step = Math.ceil(totalPoints / 5);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Number of Students',
                        data: scoreRangeData,
                        backgroundColor: '#8DA9F7',
                        borderColor: '#8DA9F7',
                        borderWidth: 0,
                        borderRadius: 4,
                        borderSkipped: false,
                        maxBarThickness: 120
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: '#8DA9F7',
                            borderWidth: 1,
                            cornerRadius: 6,
                            displayColors: false,
                            callbacks: {
                                title: (ctx) => 'Score Range: ' + ctx[0].label,
                                label: (ctx) => 'Students: ' + ctx.parsed.y
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: totalPoints,
                            ticks: {
                                stepSize: step,
                                color: '#666',
                                font: {
                                    family: 'Regular, sans-serif',
                                    size: 12
                                }
                            },
                            grid: {
                                color: '#E0E0E0',
                                lineWidth: 1,
                                drawBorder: false,
                                drawTicks: false
                            },
                            border: {
                                display: false
                            }
                        },
                        x: {
                            ticks: {
                                color: '#666',
                                font: {
                                    family: 'Regular, sans-serif',
                                    size: 12
                                }
                            },
                            grid: {
                                display: false
                            },
                            border: {
                                display: false
                            }
                        }
                    },
                    layout: {
                        padding: {
                            top: 20,
                            bottom: 10,
                            left: 10,
                            right: 10
                        }
                    }
                }
            });
        }
        // Initialize Score Range Chart
        createScoreRangeChartFull(<?php echo $totalPoints; ?>);
    </script>

</body>

</html>