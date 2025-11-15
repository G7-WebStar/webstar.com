<?php
include("../shared/assets/database/connect.php");
include("../shared/assets/processes/prof-session-process.php");

if (!isset($_GET['assessmentID'])) {
    echo "Assessment ID is missing in the URL.";
    exit;
}

$assessmentID = $_GET['assessmentID'];

if ($assessmentID == null) {
    echo "Assessment ID is missing in the URL.";
    exit;
}

$selectAssignmentQuery = "SELECT * FROM assignments WHERE assessmentID = '$assessmentID'";
$selectAssignmentResult = executeQuery($selectAssignmentQuery);

$selectAssessmentQuery = "SELECT assessmentTitle, DATE_FORMAT(deadline, '%b %e') AS assessmentDeadline, type, DATE_FORMAT(createdAt, '%b %e, %Y %l:%i %p') AS creationDate
                          FROM assessments WHERE assessmentID = '$assessmentID'";
$selectAssessmentResult = executeQuery($selectAssessmentQuery);

$assessmentResultRow = mysqli_fetch_assoc($selectAssessmentResult);
$type = (mysqli_num_rows($selectAssessmentResult) > 0) ? $assessmentResultRow['type'] : null;
if ($type == null) {
    echo "Assessment doesn't exists.";
    exit();
}

if ($type != 'Task') {
    echo "Assessment doesn't exists.";
    exit;
}

$assignmentRow = (mysqli_num_rows($selectAssignmentResult) > 0) ? mysqli_fetch_assoc($selectAssignmentResult) : null;

$assignmentID = ($assignmentRow == null) ? null : $assignmentRow['assignmentID'];
$filesQuery = "SELECT * FROM files WHERE assignmentID = '$assignmentID'";
$filesResult = executeQuery($filesQuery);

$attachmentsArray = [];
$linksArray = [];

while ($file = mysqli_fetch_assoc($filesResult)) {
    if (!empty($file['fileAttachment'])) {
        $attachments = array_map('trim', explode(',', $file['fileAttachment']));
        $attachmentsArray = array_merge($attachmentsArray, $attachments);
    }

    if (!empty($file['fileLink'])) {
        $links = array_map('trim', explode(',', $file['fileLink']));
        $linksArray = array_merge($linksArray, $links);
    }
}

$profInfoQuery = "SELECT * FROM userInfo WHERE userID = '$userID'";
$profInfoResult = executeQuery($profInfoQuery);

$countPendingQuery = "SELECT COUNT(*) AS pending FROM todo 
                      WHERE assessmentID = '$assessmentID' AND status = 'Pending'";
$countPendingResult = executeQuery($countPendingQuery);
$pending = mysqli_fetch_assoc($countPendingResult);

$countSubmittedQuery = "SELECT COUNT(*) AS submittedTodo FROM todo 
                        WHERE assessmentID = '$assessmentID' AND status = 'Submitted'";
$countSubmittedResult = executeQuery($countSubmittedQuery);
$submitted = mysqli_fetch_assoc($countSubmittedResult);

$countGradedQuery = "SELECT COUNT(*) AS graded FROM todo 
                     WHERE assessmentID = '$assessmentID' AND status = 'Graded'";
$countGradedResult = executeQuery($countGradedQuery);
$graded = mysqli_fetch_assoc($countGradedResult);

$countMissingQuery = "SELECT COUNT(*) AS missing FROM todo 
                     WHERE assessmentID = '$assessmentID' AND status = 'Missing'";
$countMissingResult = executeQuery($countMissingQuery);
$missing = mysqli_fetch_assoc($countMissingResult);

$getAssessmentStatusQuery = "SELECT * FROM assessments WHERE CURRENT_DATE <= deadline AND assessmentID = $assessmentID;";
$getAssessmentStatusResult = executeQuery($getAssessmentStatusQuery);

if (mysqli_num_rows($getAssessmentStatusResult) > 0) {
    $statusText = 'pending';
} else {
    $statusText = 'did not submit';
}

$getSubmissionIDQuery = "SELECT submissions.submissionID 
        FROM submissions 
        INNER JOIN todo 
            ON todo.userID = submissions.userID
        WHERE todo.status != 'Graded' AND todo.assessmentID = '$assessmentID' AND submissions.assessmentID = '$assessmentID'
        ORDER BY todo.updatedAt ASC
        LIMIT 1";
$getSubmissionIDResult = executeQuery($getSubmissionIDQuery);
$submissionIDRow = (mysqli_num_rows($getSubmissionIDResult) > 0) ? mysqli_fetch_assoc($getSubmissionIDResult) : null;
$submissionID = ($submissionIDRow == null) ? null : $submissionIDRow['submissionID'];

$checkRubricQuery = "SELECT rubricID FROM assignments WHERE assessmentID = '$assessmentID'";
$checkRubricResult = executeQuery($checkRubricQuery);
$rubricIDRow = (mysqli_num_rows($checkRubricResult) > 0) ? mysqli_fetch_assoc($checkRubricResult) : null;
$rubricID = ($rubricIDRow == null) ? null : $rubricIDRow['rubricID'];
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
    <link rel="stylesheet" href="../shared/assets/css/assess-task-details.css">
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
                                <?php
                                if (mysqli_num_rows($selectAssessmentResult) > 0) {
                                    mysqli_data_seek($selectAssessmentResult, 0);
                                    while ($assessmentRow = mysqli_fetch_assoc($selectAssessmentResult)) {
                                ?>
                                        <div class="col">
                                            <span class="text-sbold text-25"><?php echo $assessmentRow['assessmentTitle']; ?></span>
                                            <div class="text-reg text-18">Due <?php echo $assessmentRow['assessmentDeadline']; ?></div>
                                        </div>
                                <?php
                                    }
                                } ?>
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
                                    <?php
                                    if (mysqli_num_rows($selectAssessmentResult) > 0) {
                                        mysqli_data_seek($selectAssessmentResult, 0);
                                        while ($assessmentRow = mysqli_fetch_assoc($selectAssessmentResult)) {
                                    ?>
                                            <div class="col">
                                                <span class="text-sbold text-25"><?php echo $assessmentRow['assessmentTitle']; ?></span>
                                                <div class="text-reg text-18">Due <?php echo $assessmentRow['assessmentDeadline']; ?></div>
                                            </div>
                                    <?php
                                        }
                                    } ?>
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
                                                    <ul class="nav nav-tabs custom-nav-tabs mb-3 flex-nowrap" id="myTab"
                                                        role="tablist">
                                                        <li class="nav-item">
                                                            <a class="nav-link active" id="announcements-tab"
                                                                data-bs-toggle="tab" href="#announcements"
                                                                role="tab">Task Details</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link" id="lessons-tab" href="assess-submissions.php?assessmentID=<?php echo $assessmentID; ?>" role="tab">Submissions</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <!-- Tab Content -->
                                            <?php
                                            if (mysqli_num_rows($selectAssignmentResult) > 0) {
                                                mysqli_data_seek($selectAssignmentResult, 0);
                                                while ($assignmentRow = mysqli_fetch_assoc($selectAssignmentResult)) {
                                            ?>
                                                    <div class="tab-content" id="myTabContent">
                                                        <!-- Task Details Tab - Active -->
                                                        <div class="tab-pane fade show active" id="announcements" role="tabpanel" aria-labelledby="announcements-tab">
                                                            <div class="text-sbold text-14 mt-5">Task Instructions</div>
                                                            <p class="mb-5 mt-2 text-med text-14">
                                                                <?php echo $assignmentRow['assignmentDescription']; ?>
                                                            </p>

                                                            <hr>

                                                            <div class="text-sbold text-14 mt-3">Task Materials</div>
                                                            <?php foreach ($attachmentsArray as $file):
                                                                $filePath = "shared/assets/files/" . $file;
                                                                $fileExt = strtoupper(pathinfo($file, PATHINFO_EXTENSION));
                                                                $fileSize = (file_exists($filePath)) ? filesize($filePath) : 0;
                                                                $fileSizeMB = $fileSize > 0 ? round($fileSize / 1048576, 2) . " MB" : "Unknown size";

                                                                // Remove extension from display name
                                                                $fileNameOnly = pathinfo($file, PATHINFO_FILENAME);
                                                            ?>
                                                                <a href="<?php echo $filePath; ?>"
                                                                    <?php if (!preg_match('/^https?:\/\//', $filePath)) : ?>
                                                                    download="<?php echo htmlspecialchars($file); ?>"
                                                                    <?php endif; ?>
                                                                    style="text-decoration:none; color:inherit;">

                                                                    <div class="cardFile my-3 w-lg-25 d-flex align-items-start"
                                                                        style="width:400px; max-width:100%; min-width:310px;">
                                                                        <span class="px-3 py-3 material-symbols-outlined">draft</span>
                                                                        <div class="ms-2">
                                                                            <div class="text-sbold text-16 mt-1"><?php echo $fileNameOnly ?></div>
                                                                            <div class="due text-reg text-14 mb-1">
                                                                                <?php echo $fileExt ?> · <?php echo $fileSizeMB ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            <?php endforeach; ?>


                                                            <?php foreach ($linksArray as $link): ?>
                                                                <div class="cardFile my-3 w-lg-25 d-flex align-items-start"
                                                                    style="width:400px; max-width:100%; min-width:310px;">
                                                                    <span class="px-3 py-3 material-symbols-outlined">public</span>
                                                                    <div class="ms-2">
                                                                        <!-- temoparary lang ang filename here -->
                                                                        <div class="text-sbold text-16 mt-1"><?php echo $fileNameOnly ?></div>
                                                                        <div class="text-reg link text-12 mt-0">
                                                                            <a href="<?php echo $link ?>" target="_blank" rel="noopener noreferrer"
                                                                                style="text-decoration: none; color: var(--black);">
                                                                                <?php echo $link ?>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; ?>

                                                            <hr>
                                                            <?php
                                                            if (mysqli_num_rows($profInfoResult) > 0) {
                                                                while ($prof = mysqli_fetch_assoc($profInfoResult)) {


                                                            ?>
                                                                    <div class="text-sbold text-14 pb-3">Prepared by</div>
                                                                    <div class="d-flex align-items-center pb-5">
                                                                        <div class="rounded-circle me-2"
                                                                            style="width: 50px; height: 50px; background-color: var(--highlight75);">
                                                                            <img src="../shared/assets/img/assess/prof.png" alt="professor"
                                                                                class="rounded-circle" style="width:50px;height:50px;">
                                                                        </div>
                                                                        <div>
                                                                            <div class="text-sbold text-14">Prof. <?php echo $prof['firstName'] . " " . $prof['middleName'] . " " . $prof['lastName']; ?></div>
                                                                    <?php
                                                                }
                                                            }
                                                                    ?>
                                                                    <?php
                                                                    if (mysqli_num_rows($selectAssessmentResult) > 0) {
                                                                        mysqli_data_seek($selectAssessmentResult, 0);
                                                                        while ($createdAt = mysqli_fetch_assoc($selectAssessmentResult)) {
                                                                    ?>
                                                                            <div class="text-med text-12"><?php echo $createdAt['creationDate']; ?></div>
                                                                    <?php
                                                                        }
                                                                    }
                                                                    ?>
                                                                        </div>
                                                                    </div>
                                                        </div>

                                                        <!-- Submissions Tab - Disabled -->
                                                        <div class="tab-pane fade" id="lessons" role="tabpanel" aria-labelledby="lessons-tab">
                                                            <!-- Empty content - tab is disabled -->
                                                        </div>
                                                    </div>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>


                                    <div class="col-12 col-lg-4">
                                        <div class="cardSticky position-sticky" style="top: 20px;">
                                            <div class="p-2">
                                                <div class="row align-items-center justify-content-center mb-3">
                                                    <div class="col-auto">
                                                        <div class="chart-container"
                                                            style="width: 100px; height: 100px;">
                                                            <canvas id="taskChart" width="100" height="100"></canvas>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <!-- Submission Stats -->
                                                        <div class="submission-stats">
                                                            <div class="text-reg text-14 mb-1"><span
                                                                    class="stat-value"><?php echo $submitted['submittedTodo']; ?></span> submitted</div>
                                                            <div class="text-reg text-14 mb-1"><span
                                                                    class="stat-value"><?php echo $pending['pending']; ?></span> <?php echo $statusText ?></div>
                                                            <div class="text-reg text-14 mb-1"><span
                                                                    class="stat-value"><?php echo $graded['graded']; ?></span>
                                                                graded</div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="d-flex justify-content-center pt-3">
                                                    <?php if ($submissionID != null) { ?><a class="text-decoration-none" href="<?php echo ($rubricID == null) ? 'grading-sheet.php?submissionID=' . $submissionID : 'grading-sheet-rubrics.php?submissionID=' . $submissionID; ?>"><?php } ?>
                                                        <button class="btn btn-action" <?php echo ($submissionID == null) ? 'disabled' : '' ?>>
                                                            <img src="../shared/assets/img/assess/assess.png"
                                                                alt="Assess Icon"
                                                                style="width: 20px; height: 20px; margin-right: 5px; object-fit: contain;">Grading
                                                            Sheet
                                                        </button>
                                                        <?php if ($submissionID != null) { ?></a><?php } ?>
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

        createDoughnutChart('taskChart', <?php echo $submitted['submittedTodo']; ?>, <?php echo $pending['pending']; ?>, <?php echo $graded['graded']; ?>, <?php echo $missing['missing']; ?>);
    </script>
</body>

</html>