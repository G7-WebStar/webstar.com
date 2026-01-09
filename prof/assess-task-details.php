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

$profInfoQuery = "SELECT * FROM userinfo WHERE userID = '$userID'";
$profInfoResult = executeQuery($profInfoQuery);

$countPendingQuery = "SELECT COUNT(*) AS pending FROM todo 
                      WHERE assessmentID = '$assessmentID' AND status = 'Pending'";
$countPendingResult = executeQuery($countPendingQuery);
$pending = mysqli_fetch_assoc($countPendingResult);

$countSubmittedQuery = "SELECT COUNT(*) AS submittedTodo FROM todo 
                        WHERE assessmentID = '$assessmentID' AND status = 'Submitted'";
$countSubmittedResult = executeQuery($countSubmittedQuery);
$submitted = mysqli_fetch_assoc($countSubmittedResult);

$countReturnedQuery = "SELECT COUNT(*) AS returned FROM todo 
                     WHERE assessmentID = '$assessmentID' AND status = 'Returned'";
$countReturnedResult = executeQuery($countReturnedQuery);
$returned = mysqli_fetch_assoc($countReturnedResult);

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
        WHERE todo.status != 'Returned' AND todo.assessmentID = '$assessmentID' AND submissions.assessmentID = '$assessmentID'
        ORDER BY todo.updatedAt ASC
        LIMIT 1";
$getSubmissionIDResult = executeQuery($getSubmissionIDQuery);
$submissionIDRow = (mysqli_num_rows($getSubmissionIDResult) > 0) ? mysqli_fetch_assoc($getSubmissionIDResult) : null;
$submissionID = ($submissionIDRow == null) ? null : $submissionIDRow['submissionID'];

$checkRubricQuery = "SELECT rubricID FROM assignments WHERE assessmentID = '$assessmentID'";
$checkRubricResult = executeQuery($checkRubricQuery);
$rubricIDRow = (mysqli_num_rows($checkRubricResult) > 0) ? mysqli_fetch_assoc($checkRubricResult) : null;
$rubricID = ($rubricIDRow == null) ? null : $rubricIDRow['rubricID'];

// Rubric Info
$rubricID = isset($assignmentRow['rubricID']) ? $assignmentRow['rubricID'] : null;
$rubricTitle = '';
$rubricPoints = 0;
$criteriaList = [];
$levelsByCriterion = [];

if (!empty($rubricID)) {
    // Get rubric title and total points
    $rubricQuery = "SELECT rubricTitle, totalPoints FROM rubric WHERE rubricID = $rubricID LIMIT 1";
    $rubricResult = executeQuery($rubricQuery);
    $rubricRow = mysqli_fetch_assoc($rubricResult);

    if ($rubricRow) {
        $rubricTitle = $rubricRow['rubricTitle'];
        $rubricPoints = $rubricRow['totalPoints'];
    }

    // Get criteria for this rubric
    $criteriaQuery = "SELECT criterionID, criteriaTitle FROM criteria WHERE rubricID = $rubricID ORDER BY criterionID ASC";
    $criteriaResult = executeQuery($criteriaQuery);
    while ($criterion = mysqli_fetch_assoc($criteriaResult)) {
        $criteriaList[] = $criterion;

        // Get levels for this criterion
        $criterionID = $criterion['criterionID'];
        $levelsQuery = "SELECT levelID, levelTitle, levelDescription, points 
                        FROM level 
                        WHERE criterionID = $criterionID 
                        ORDER BY points DESC";
        $levelsResult = executeQuery($levelsQuery);
        $levelsByCriterion[$criterionID] = [];
        while ($level = mysqli_fetch_assoc($levelsResult)) {
            $levelsByCriterion[$criterionID][] = $level;
        }
    }
}

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
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0"
        rel="stylesheet" />

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
                    <div class="row mb-3 row-padding-top">
                        <div class="col-12 cardHeader p-3 mb-4">

                            <!-- DESKTOP VIEW -->
                            <div class="row desktop-header d-none d-sm-flex">
                                <div class="col-auto me-2">
                                    <button onclick="history.back()" class="btn p-0" style="background:none; border:none;tranform:none!important; box-shadow:none!important">
                                        <i class="fa-solid fa-arrow-left text-reg text-16" style="color: var(--black);"></i>
                                    </button>
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
                                        <div class="arrow">
                                            <button onclick="history.back()" class="btn p-0" style="background:none; border:none;">
                                                <i class="fa-solid fa-arrow-left text-reg text-16" style="color: var(--black);"></i>
                                            </button>
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
                                                            <?php if (!empty($assignmentRow['assignmentDescription'])): ?>
                                                                <div class="text-sbold text-14 mt-5">Task Instructions</div>
                                                                <p class="mb-4 mt-3 text-med text-14">
                                                                    <?php echo $assignmentRow['assignmentDescription']; ?>
                                                                </p>
                                                                <hr>
                                                            <?php endif; ?>

                                                            <?php if (!empty($attachmentsArray) || !empty($linksArray)): ?>
                                                                <div class="text-sbold text-14 mt-3">Task Materials</div>
                                                                <!-- FILES -->
                                                                <?php foreach ($attachmentsArray as $file):
                                                                    $filePath = "../shared/assets/files/" . $file;
                                                                    $fileExt = strtoupper(pathinfo($file, PATHINFO_EXTENSION));
                                                                    $fileSize = (file_exists($filePath)) ? filesize($filePath) : 0;
                                                                    $fileSizeMB = $fileSize > 0 ? round($fileSize / 1048576, 2) . " MB" : "Unknown size";
                                                                    $fileNameOnly = pathinfo($file, PATHINFO_FILENAME);
                                                                ?>
                                                                    <div onclick="openViewerModal('<?php echo addslashes($file); ?>', '<?php echo $filePath; ?>')"
                                                                        style="cursor:pointer;">
                                                                        <div class="cardFile my-3 w-lg-25 d-flex align-items-start"
                                                                            style="width:100%; max-width:400px;">
                                                                            <span class="px-3 py-3 material-symbols-outlined">draft</span>
                                                                            <div class="ms-2">
                                                                                <div class="text-sbold text-16 mt-1 text-truncate" style="max-width:320px;"><?php echo $fileNameOnly ?></div>
                                                                                <div class="due text-reg text-14 mb-1">
                                                                                    <?php echo $fileExt ?> · <?php echo $fileSizeMB ?>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <?php endforeach; ?>

                                                                <!-- LINKS -->
                                                                <?php foreach ($linksArray as $link): ?>
                                                                    <?php
                                                                    $linkTitle = htmlspecialchars($link);
                                                                    ?>
                                                                    <div onclick="openLinkViewerModal('<?php echo $linkTitle; ?>', '<?php echo $linkTitle; ?>')"
                                                                        style="cursor:pointer;">
                                                                        <div class="cardFile my-3 w-lg-25 d-flex align-items-start"
                                                                            style="width:100%; max-width:400px;">
                                                                            <span class="px-3 py-3 material-symbols-outlined">public</span>
                                                                            <div class="ms-2 overflow-hidden" style="flex:1;">
                                                                                <div class="text-sbold text-16 mt-1 text-truncate" style="max-width:320px;"><?php echo $linkTitle ?></div>
                                                                                <div class="text-reg link text-12 mt-0 text-truncate"
                                                                                    style="color: var(--black); max-width: 320px;"><?php echo $linkTitle ?></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                                <hr>
                                                            <?php endif; ?>

                                                            <?php if (!empty($rubricID) && !empty($rubricTitle)): ?>
                                                                <div class="text-sbold text-14 mt-4">Rubric</div>
                                                                <div class="cardFile my-3 w-lg-25 d-flex align-items-start"
                                                                    style="max-width:100%; min-width:310px; cursor:pointer;"
                                                                    data-bs-toggle="modal" data-bs-target="#rubricModal">

                                                                    <span class="material-symbols-outlined ps-3 pe-2 py-3"
                                                                        style="font-variation-settings:'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 48;">
                                                                        rate_review
                                                                    </span>

                                                                    <div class="ms-2">
                                                                        <div class="text-sbold text-16 mt-1 text-truncate" style="max-width:320px;"><?php echo $rubricTitle; ?>
                                                                        </div>
                                                                        <div class="due text-reg text-14 mb-1">
                                                                            <?php echo $rubricPoints; ?> points
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <hr>
                                                            <?php endif; ?>

                                                            <?php
                                                            if (mysqli_num_rows($profInfoResult) > 0) {
                                                                while ($prof = mysqli_fetch_assoc($profInfoResult)) {
                                                                    $profilePic = !empty($prof['profilePicture'])
                                                                        ? '../shared/assets/pfp-uploads/' . $prof['profilePicture']
                                                                        : '../shared/assets/pfp-uploads/defaultProfile.png';
                                                            ?>
                                                                    <div class="text-sbold text-14 pb-3">Prepared by</div>
                                                                    <div class="d-flex align-items-center pb-5">
                                                                        <div class="rounded-circle me-2"
                                                                            style="width: 50px; height: 50px; background-color: var(--highlight75);">
                                                                            <img src="<?php echo $profilePic ?>" alt="Profile"
                                                                                alt="professor" class="rounded-circle"
                                                                                style="width:50px;height:50px;">
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
                                                                    class="stat-value"><?php echo (mysqli_num_rows($getAssessmentStatusResult) > 0) ? $pending['pending'] : $missing['missing']; ?></span> <?php echo $statusText ?></div>
                                                            <div class="text-reg text-14 mb-1"><span
                                                                    class="stat-value"><?php echo $returned['returned']; ?></span>
                                                                returned</div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="d-flex justify-content-center pt-3">
                                                    <?php if ($submissionID != null) { ?><a class="text-decoration-none" href="<?php echo ($rubricID == null) ? 'grading-sheet.php?submissionID=' . $submissionID : 'grading-sheet-rubrics.php?submissionID=' . $submissionID; ?>"><?php } ?>
                                                        <?php if ($submissionID == null) { ?><div title="No submissions in this assessment yet"><?php } ?>
                                                            <button class="btn btn-action" <?php echo ($submissionID == null) ? 'disabled' : '' ?>>
                                                                <img src="../shared/assets/img/assess/assess.png"
                                                                    alt="Assess Icon"
                                                                    style="width: 20px; height: 20px; margin-right: 5px; object-fit: contain;">Grading
                                                                Sheet
                                                            </button>
                                                            <?php if ($submissionID == null) { ?>
                                                            </div><?php } ?>
                                                        <?php if ($submissionID != null) { ?></a><?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Close content-scroll-container -->
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="rubricModal" tabindex="-1" aria-labelledby="rubricModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered py-4">
                <div class="modal-content rounded-4" style="max-height:450px; overflow:hidden;">

                    <!-- HEADER -->
                    <div class="modal-header border-bottom">
                        <div class="modal-title text-sbold text-20 ms-3" id="rubricModalLabel">
                            <?php echo $rubricTitle; ?>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- BODY -->
                    <div class="modal-body" style="overflow-y:auto; scrollbar-width:thin;">
                        <div class="container text-center px-5">
                            <?php foreach ($criteriaList as $criterionIndex => $criterion): ?>
                                <!-- Section Title -->
                                <div class="row mb-3">
                                    <div class="col">
                                        <div class="text-sbold text-15" style="color: var(--black);">
                                            <?php echo $criterion['criteriaTitle']; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Accordion for levels -->
                                <div id="ratingAccordion<?php echo $criterionIndex; ?>" class="row justify-content-center">
                                    <div class="col-12 col-md-10">
                                        <?php foreach ($levelsByCriterion[$criterion['criterionID']] as $levelIndex => $level):
                                            $collapseID = strtolower(preg_replace('/\s+/', '', $level['levelTitle'])) . $criterionIndex;
                                        ?>
                                            <div class="mb-2">
                                                <div class="w-100 d-flex align-items-center justify-content-center flex-column text-med text-14"
                                                    style="background-color: var(--pureWhite); border-radius: 10px; border: 1px solid var(--black);">

                                                    <div class="d-flex justify-content-between align-items-center w-100 px-3 py-2">
                                                        <span class="flex-grow-1 text-center ps-3">
                                                            <?php echo $level['levelTitle']; ?> · <?php echo $level['points']; ?>
                                                            pts
                                                        </span>

                                                        <!-- only the icon is clickable -->
                                                        <span class="material-symbols-rounded collapse-toggle"
                                                            data-bs-toggle="collapse" data-bs-target="#<?php echo $collapseID; ?>"
                                                            aria-expanded="false" aria-controls="<?php echo $collapseID; ?>"
                                                            style="cursor:pointer;">
                                                            expand_more
                                                        </span>
                                                    </div>

                                                    <div class="collapse w-100 mt-2" id="<?php echo $collapseID; ?>"
                                                        data-bs-parent="#ratingAccordion<?php echo $criterionIndex; ?>">
                                                        <p class="mb-0 px-3 pb-2 text-reg text-14">
                                                            <?php echo $level['levelDescription']; ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <!-- FOOTER -->
                    <div class="modal-footer">
                        <div class="container">
                            <div class="row justify-content-end py-2">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- FILE VIEWER MODAL -->
    <div class="modal fade" id="viewerModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content" style="border-radius:12px; overflow:hidden;">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="modal-title text-sbold text-16 mb-0 text-truncate" style="max-width:150px;" id="viewerModalLabel">File Viewer</h5>
                        <a id="modalDownloadBtn" class="btn py-1 px-3 rounded-pill text-sbold text-md-14 ms-1"
                            style="background-color: var(--primaryColor); border: 1px solid var(--black);" download>
                            <span class="" style="display:flex;align-items:center;gap:4px;">
                                <span class="material-symbols-outlined" style="font-size:18px;">download_2</span>
                                Download
                            </span>
                        </a>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0" style="background:#2e2e2e; height:75vh;">
                    <div id="viewerContainer" style="width:100%; height:100%;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- LINK VIEWER MODAL -->
    <div class="modal fade" id="linkViewerModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content" style="border-radius:12px; overflow:hidden;">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="modal-title text-sbold text-16 mb-0 text-truncate" style="max-width:150px;" id="linkViewerModalLabel">Link Viewer</h5>
                        <a id="modalOpenInNewTab" class="btn py-1 px-3 rounded-pill text-sbold text-md-14 ms-1"
                            style="background-color: var(--primaryColor); border: 1px solid var(--black);" target="_blank">
                            <span class="" style="display:flex;align-items:center;gap:4px;">
                                <span class="material-symbols-outlined" style="font-size:18px;">open_in_new</span>
                                Open
                            </span>
                        </a>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0" style="background:#2e2e2e; height:75vh;">
                    <iframe id="linkViewerIframe"
                        style="width:100%; height:100%; border:none; border-radius:10px;"></iframe>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function createDoughnutChart(canvasId, submitted, pending, returned, missing) {
            const ctx = document.getElementById(canvasId).getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [submitted, pending, returned, missing],
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
        createDoughnutChart('taskChart', <?php echo $submitted['submittedTodo']; ?>, <?php echo $pending['pending']; ?>, <?php echo $returned['returned']; ?>, <?php echo $missing['missing']; ?>);
    </script>
    <script>
        function openViewerModal(fileName, filePath) {
            document.getElementById("viewerModalLabel").textContent = fileName;
            document.getElementById("modalDownloadBtn").href = filePath;
            let viewer = document.getElementById("viewerContainer");
            viewer.innerHTML = "";
            let ext = fileName.split(".").pop().toLowerCase();
            if (["jpg", "jpeg", "png", "gif", "webp", "bmp", "svg"].includes(ext)) {
                viewer.innerHTML = `<img src="${filePath}" style="width:100%; height:100%; object-fit:contain; background:#333;">`;
            } else if (ext === "pdf") {
                viewer.innerHTML = `<iframe src="${filePath}" width="100%" height="100%" style="border:none; border-radius:10px;"></iframe>`;
            } else {
                viewer.innerHTML = `<div class="text-white text-center mt-5">
                    <p class="text-sbold text-16" style="color: var(--pureWhite);">This file type cannot be previewed.</p>
                    <a href="${filePath}" download class="btn text-sbold text-16" style="background-color: var(--primaryColor); color: var(--black); border: none;"> Download File </a>
                </div>`;
            }
            new bootstrap.Modal(document.getElementById("viewerModal")).show();
        }

        function openLinkViewerModal(title, url) {
            document.getElementById("linkViewerModalLabel").textContent = title;
            document.getElementById("modalOpenInNewTab").href = url;
            document.getElementById("linkViewerIframe").src = url;
            new bootstrap.Modal(document.getElementById("linkViewerModal")).show();
        }
    </script>


</body>

</html>