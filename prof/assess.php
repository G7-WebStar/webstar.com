<?php $activePage = 'assess'; ?>
<?php
include("../shared/assets/database/connect.php");
include("../shared/assets/processes/prof-session-process.php");

$assessments = [];
$assessmentsQuery = "SELECT 
assessments.type, 
assessments.assessmentTitle, 
courses.courseID,
courses.courseCode, 
courses.courseTitle, 
assessments.assessmentID,
assessments.isArchived,
DATE_FORMAT(assessments.deadline, '%b %e') AS assessmentDeadline
FROM assessments
INNER JOIN courses
	ON assessments.courseID = courses.courseID
WHERE courses.userID = $userID AND courses.isActive = '1'";
$assessmentsResult = executeQuery($assessmentsQuery);
if ($assessmentsResult && mysqli_num_rows($assessmentsResult) > 0) {
    while ($rowAssessment = mysqli_fetch_assoc($assessmentsResult)) {
        $assessmentCourseID = $rowAssessment['courseID'];
        $assessmentID = $rowAssessment['assessmentID'];

        $countPendingQuery = "SELECT COUNT(*) AS pending FROM todo 
                                WHERE assessmentID = '$assessmentID' AND status = 'Pending'";
        $countPendingResult = executeQuery($countPendingQuery);

        $countSubmittedQuery = "SELECT COUNT(*) AS submittedTodo FROM todo 
                                WHERE assessmentID = '$assessmentID' AND status = 'Submitted'";
        $countSubmittedResult = executeQuery($countSubmittedQuery);

        $countGradedQuery = "SELECT COUNT(*) AS graded FROM todo 
                                WHERE assessmentID = '$assessmentID' AND status = 'Graded'";
        $countGradedResult = executeQuery($countGradedQuery);

        $countMissingQuery = "SELECT COUNT(*) AS missing FROM todo 
                                WHERE assessmentID = '$assessmentID' AND status = 'Missing'";
        $countMissingResult = executeQuery($countMissingQuery);

        $getSubmissionIDQuery = "SELECT submissions.submissionID 
        FROM submissions 
        INNER JOIN todo 
            ON todo.userID = submissions.userID
        WHERE todo.status != 'Graded' AND todo.assessmentID = '$assessmentID' AND submissions.assessmentID = '$assessmentID'
        ORDER BY todo.updatedAt ASC
        LIMIT 1";
        $getSubmissionIDResult = executeQuery($getSubmissionIDQuery);

        $checkRubricQuery = "SELECT rubricID FROM assignments WHERE assessmentID = '$assessmentID'";
        $checkRubricResult = executeQuery($checkRubricQuery);

        $submittedTodoCount = 0;

        if (mysqli_num_rows($countSubmittedResult) > 0) {
            $countRowSubmitted = mysqli_fetch_assoc($countSubmittedResult);
            $submittedTodoCount = $countRowSubmitted['submittedTodo'];
        }

        $gradedTodoCount = 0;

        if (mysqli_num_rows($countGradedResult) > 0) {
            $countRowGraded = mysqli_fetch_assoc($countGradedResult);
            $gradedTodoCount = $countRowGraded['graded'];
        }

        $missingTodoCount = 0;

        if (mysqli_num_rows($countMissingResult) > 0) {
            $countRowMissing = mysqli_fetch_assoc($countMissingResult);
            $missingTodoCount = $countRowMissing['missing'];
        }

        $pendingTodoCount = 0;

        if (mysqli_num_rows($countPendingResult) > 0) {
            $countRowPending = mysqli_fetch_assoc($countPendingResult);
            $pendingTodoCount = $countRowPending['pending'];
        }

        $submissionID = 0;

        if (mysqli_num_rows($getSubmissionIDResult) > 0) {
            $submissionIDRow = mysqli_fetch_assoc($getSubmissionIDResult);
            $submissionID = $submissionIDRow['submissionID'];
        }

        if (mysqli_num_rows($checkRubricResult) > 0) {
            $rubricRow = mysqli_fetch_assoc($checkRubricResult);
            $rubricID = $rubricRow['rubricID'];
        } else {
            $rubricID = null;
        }

        $rowAssessment['graded'] = $gradedTodoCount;
        $rowAssessment['submittedTodo'] = $submittedTodoCount;
        $rowAssessment['pending'] = $pendingTodoCount;
        $rowAssessment['missing'] = $missingTodoCount;
        $rowAssessment['submissionID'] = $submissionID;
        $rowAssessment['rubricID'] = $rubricID;
        $assessments[] = $rowAssessment;
    }
}
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

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />

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
                                <div class="col-12 col-xl-2 mb-3 mb-lg-0">
                                    <h1 class="text-bold text-25 mx-0 mb-0 mt-4" style="color: var(--black);">Assess
                                    </h1>
                                </div>

                                <!-- Dropdowns-->
                                <div class="col-12 col-xl-10 mt-4">
                                    <div
                                        class="d-flex flex-wrap flex-lg-nowrap justify-content-center justify-content-lg-start gap-3">

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
                                $submitted = [];
                                if (mysqli_num_rows($assessmentsResult) > 0) {
                                    mysqli_data_seek($assessmentsResult, 0);
                                    $i = 1;
                                    foreach ($assessments as $assessment) {
                                        $ID = $assessment['assessmentID'];
                                        $type = $assessment['type'];
                                        $assessmentTitle = $assessment['assessmentTitle'];
                                        $courseCode = $assessment['courseCode'];
                                        $courseTitle = $assessment['courseTitle'];
                                        $deadline = $assessment['assessmentDeadline'];
                                        $submittedCount = $assessment['submittedTodo'];
                                        $pendingCount = $assessment['pending'];
                                        $gradedCount = $assessment['graded'];
                                        $missingCount = $assessment['missing'];
                                        $cardSubmissionID = $assessment['submissionID'];
                                        $archiveStatus = $assessment['isArchived'];
                                        $rubricIDs = $assessment['rubricID'];

                                        $chartsIDs[] = "chart$i";
                                        $submitted[] = $submittedCount;
                                        $pending[] = $pendingCount;
                                        $graded[] = $gradedCount;
                                        $missing[] = $missingCount;
                                        $isArchived[] = $archiveStatus;
                                ?>
                                        <div class="assessment-card mb-3">
                                            <div class="card-content">
                                                <!-- Top Row: Left Info and Submission Stats -->
                                                <div class="top-row overflow-hidden">
                                                    <!-- Left Info -->
                                                    <div class="left-info">
                                                        <div class="mb-2 text-reg">
                                                            <span class="badge rounded-pill task-badge"><?php echo $type; ?></span>
                                                        </div>
                                                        <div class="text-bold text-18 mb-2"><?php echo $assessmentTitle; ?></div>
                                                        <div class="text-sbold text-14"><?php echo $courseCode; ?><br>
                                                            <div class="text-reg text-14"><?php echo $courseTitle; ?></div>
                                                        </div>
                                                    </div>

                                                    <!-- Submission Stats -->

                                                    <div class="submission-stats">
                                                        <div class="text-reg text-14 mb-1"><span class="stat-value"><?php echo $submittedCount; ?></span> submitted</div>
                                                        <div class="text-reg text-14 mb-1"><span class="stat-value"><?php echo $pendingCount; ?></span> pending submission</div>
                                                        <div class="text-reg text-14 mb-1"><span class="stat-value"><?php echo $gradedCount; ?></span> graded</div>
                                                        <div class="text-reg text-14">Due <?php echo $deadline; ?></div>
                                                    </div>

                                                    <!-- Right Side: Progress Chart and Options -->
                                                    <div class="right-section">
                                                        <div class="chart-container" id="chart-container<?php echo $i; ?>">
                                                            <canvas id="chart<?php echo $i; ?>" width="120" height="120"></canvas>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Bottom Row: Action Buttons -->
                                                <div class="bottom-row">
                                                    <div class="action-buttons">
                                                        <a href="<?php echo ($type == 'Task') ? 'assess-task-details.php?assessmentID=' . $ID : 'assess-exam-details.php?assessmentID=' . $ID; ?>"><button class="btn btn-action">
                                                                <img src="../shared/assets/img/assess/info.png"
                                                                    alt="Assess Icon"
                                                                    style="width: 20px; height: 20px; margin-right: 5px; object-fit: contain;"><?php echo ($type == 'Task') ? 'Task' : 'Test'; ?> Details
                                                            </button></a>
                                                        <?php if ($type == 'Task') { ?>
                                                            <?php if ($cardSubmissionID != null) { ?><a href="<?php echo ($rubricIDs == null) ? 'grading-sheet.php?' : 'grading-sheet-rubrics.php?'; ?>submissionID=<?php echo $cardSubmissionID; ?>"><?php } ?>
                                                                <?php if ($cardSubmissionID == null) { ?><div title="No submissions in this assessment yet"><?php } ?>
                                                                    <button class="btn btn-action" <?php echo ($cardSubmissionID == null) ? 'disabled' : '' ?>>
                                                                        <img src="../shared/assets/img/assess/assess.png"
                                                                            alt="Assess Icon"
                                                                            style="width: 20px; height: 20px; margin-right: 5px; object-fit: contain;">Grading
                                                                        Sheet
                                                                    </button>
                                                                    <?php if ($cardSubmissionID == null) { ?>
                                                                    </div><?php } ?>
                                                                <?php if ($cardSubmissionID != null) { ?></a><?php } ?>
                                                        <?php
                                                        }
                                                        ?>
                                                    </div>
                                                    <!-- More Options aligned with buttons on the right -->
                                                    <div class="options-container">
                                                        <div class="dropdown dropend">
                                                            <button class="btn btn-link more-options" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="fas fa-ellipsis-v"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li><a class="dropdown-item" href="#" id="<?php echo $i; ?>" onclick="archive(this, <?php echo $ID; ?>);"><i class="fas fa-archive me-2"></i>
                                                                        <?php echo ($archiveStatus == 0) ? 'Mark as Archived' : 'Unarchive'; ?></a></li>
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
        function createDoughnutChart(canvasId, submitted, pending, graded, missing) {
            const ctx = document.getElementById(canvasId).getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [submitted, pending, graded, missing],
                        backgroundColor: ['#3DA8FF', '#C7C7C7', '#d9ffe4ff', '#ffd9d9ff'],
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

        function showDoughnut() {
            document.addEventListener("DOMContentLoaded", function() {
                const chartsIDs = <?php echo json_encode($chartsIDs); ?>;
                const submitted = <?php echo json_encode($submitted); ?>;
                const pending = <?php echo json_encode($pending); ?>;
                const graded = <?php echo json_encode($graded); ?>;
                const missing = <?php echo json_encode($missing); ?>;
                const isArchived = <?php echo json_encode($isArchived); ?>

                chartsIDs.forEach(function(id, index) {
                    if (isArchived[index] == 0) {
                        createDoughnutChart(id, submitted[index], pending[index], graded[index], missing[index])
                    } else {
                        const archiveLabel = document.getElementById('chart-container' + (index + 1));
                        archiveLabel.innerHTML = `<span class="badge rounded-pill text-bg-secondary text-reg">Archived</span>`;
                        console.log('chart-container' + (index + 1));
                    }
                });
            });
        }


        function archive(element, ID) {
            const archiveLabel = document.getElementById('chart-container' + element.id);
            const archiveDropdown = document.getElementById(element.id);

            if (archiveDropdown.textContent == 'Mark as Archived') {
                archiveDropdown.innerHTML = `<i class="fas fa-archive me-2"></i>Unarchive`;
            } else if (archiveDropdown.textContent == 'Unarchive') {
                archiveDropdown.innerHTML = `<i class="fas fa-archive me-2"></i>Mark as Archived`;
            }

            fetchArchiveQuery(ID);
            showDoughnut();
        }

        function fetchArchiveQuery(assessmentID) {
            fetch('../shared/assets/processes/archive-assessment.php?assessmentID=' + assessmentID)
                .then(response => {
                    if (!response.ok) {
                        console.log("There was a problem with your request :(");
                    } else {
                        console.log("Successful!");
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error archiving assessment:', error);
                    alert('Failed to archive. Please try again.');
                });
        }

        showDoughnut();
    </script>

</body>

</html>