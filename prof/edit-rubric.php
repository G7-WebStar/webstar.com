<?php $activePage = 'create-exam'; ?>
<?php
include("../shared/assets/database/connect.php");
include("../shared/assets/processes/prof-session-process.php");
?>
<?php
// Load rubric data for edit
$rubricID = isset($_GET['rubricID']) ? intval($_GET['rubricID']) : 0;
$rubricTitle = '';
$totalPoints = 0;
$hasCriteria = false;

if ($rubricID > 0) {
    $rubricSql = "SELECT rubricTitle, IFNULL(totalPoints,0) AS totalPoints FROM rubric WHERE rubricID='$rubricID' AND userID='$userID' LIMIT 1";
    $rubricResult = executeQuery($rubricSql);
    if ($rubricResult && $rubricResult->num_rows > 0) {
        $rubricRow = $rubricResult->fetch_assoc();
        $rubricTitle = $rubricRow['rubricTitle'];
        $totalPoints = (float)$rubricRow['totalPoints'];
    }

    $criteriaSql = "SELECT criterionID, criteriaTitle, criteriaDescription FROM criteria WHERE rubricID='$rubricID' ORDER BY criterionID ASC";
    $criteriaResult = executeQuery($criteriaSql);
    if ($criteriaResult && $criteriaResult->num_rows > 0) {
        $hasCriteria = true;
    }
}

// Handle update (hidden form submission)
if (isset($_POST['update_rubric'])) {
    $rubricID = intval($_POST['rubricID']);
    $criterionID = intval($_POST['criterionID']);
    $levelID = intval($_POST['levelID']);

    $rubricTitle = mysqli_real_escape_string($conn, $_POST['rubricTitle'] ?? '');
    $criteriaTitle = mysqli_real_escape_string($conn, $_POST['criteriaTitle'] ?? '');
    $criteriaDescription = mysqli_real_escape_string($conn, $_POST['criteriaDescription'] ?? '');
    $levelTitle = mysqli_real_escape_string($conn, $_POST['levelTitle'] ?? '');
    $levelDescription = mysqli_real_escape_string($conn, $_POST['levelDescription'] ?? '');
    $points = isset($_POST['points']) && $_POST['points'] !== '' ? floatval($_POST['points']) : 0;

    if ($criterionID > 0) {
        $updateCriteriaQuery = "UPDATE criteria SET criteriaTitle='$criteriaTitle', criteriaDescription='$criteriaDescription' WHERE criterionID='$criterionID'";
        executeQuery($updateCriteriaQuery);
    }

    if ($levelID > 0) {
        $updateLevelQuery = "UPDATE level SET levelTitle='$levelTitle', levelDescription='$levelDescription', points='$points' WHERE levelID='$levelID'";
        executeQuery($updateLevelQuery);
    }

    // Bulk update levels from submitted JSON (if provided)
    if (isset($_POST['allLevelsData']) && $_POST['allLevelsData'] !== '') {
        $json = $_POST['allLevelsData'];
        $levelItems = json_decode($json, true);
        if (is_array($levelItems)) {
            for ($i = 0; $i < count($levelItems); $i++) {
                $item = $levelItems[$i];
                // Defensive reads
                $postedLevelId = isset($item['levelID']) ? intval($item['levelID']) : 0;
                $postedCriterionId = isset($item['criterionID']) ? intval($item['criterionID']) : 0;
                $postedLevelTitle = mysqli_real_escape_string($conn, isset($item['levelTitle']) ? $item['levelTitle'] : '');
                $postedLevelDescription = mysqli_real_escape_string($conn, isset($item['levelDescription']) ? $item['levelDescription'] : '');
                $postedPoints = isset($item['points']) ? floatval($item['points']) : 0;
                if ($postedLevelId > 0) {
                    $bulkUpdateLevelQuery = "UPDATE level SET levelTitle='$postedLevelTitle', levelDescription='$postedLevelDescription', points='$postedPoints' WHERE levelID='$postedLevelId'";
                    executeQuery($bulkUpdateLevelQuery);
                }
            }
        }
    }

    // Recalculate total points for the rubric from DB: sum of max points per criterion
    $totalCalculated = 0;
    $criteriaCalculationSql = "SELECT criterionID FROM criteria WHERE rubricID='$rubricID'";
    $criteriaCalculationResult = executeQuery($criteriaCalculationSql);
    if ($criteriaCalculationResult && $criteriaCalculationResult->num_rows > 0) {
        while ($criteriaCalculationRow = $criteriaCalculationResult->fetch_assoc()) {
            $criterionIdForCalculation = intval($criteriaCalculationRow['criterionID']);
            $maxPointsSql = "SELECT MAX(points) AS maxPoints FROM level WHERE criterionID='$criterionIdForCalculation'";
            $maxPointsResult = executeQuery($maxPointsSql);
            if ($maxPointsResult && $maxPointsResult->num_rows > 0) {
                $maxPointsRow = $maxPointsResult->fetch_assoc();
                $maxPointsValue = isset($maxPointsRow['maxPoints']) ? floatval($maxPointsRow['maxPoints']) : 0;
                $totalCalculated += $maxPointsValue;
            }
        }
    }

    $updateRubricQuery = "UPDATE rubric SET rubricTitle='$rubricTitle', totalPoints='$totalCalculated' WHERE rubricID='$rubricID' AND userID='$userID'";
    executeQuery($updateRubricQuery);

    header('Location: assign-task.php?selectedRubricID=' . $rubricID);
    exit;
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Assign Task Edit Rubric</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/assign-task-edit-rubric.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=close" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
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

                    <div class="container-fluid py-3 overflow-y-auto row-padding-top">
                        <div class="row">
                            <div class="col-12">

                                <!-- Header -->
                                <div class="row mb-3 align-items-center">
                                    <div class="col-auto">
                                        <a href="assign-task.php" class="text-decoration-none">
                                            <i class="fa-solid fa-arrow-left text-reg text-16"
                                                style="color: var(--black);"></i>
                                        </a>
                                    </div>
                                    <div class="col text-center text-md-start">
                                        <span class="text-sbold text-25">Edit rubric</span>
                                    </div>
                                </div>

                                <!-- Rubric Form Information -->
                                <form class="create-rubric-form">
                                    <div class="row">
                                        <div class="col-12 pt-3 mb-2">
                                            <label for="rubricInfo" class="form-label text-med text-16">Rubric
                                                Information</label>
                                            <input type="text" value="<?php echo htmlspecialchars($rubricTitle); ?>"
                                                class="form-control textbox mb-3 p-3 text-reg text-14 text-muted"
                                                id="rubricInfo" aria-describedby="rubricInfo"
                                                placeholder="Rubric Title">
                                        </div>
                                    </div>
                                </form>

                                <!-- Criteria Information -->
                                <div class="row">
                                    <div class="col-12 pt-1 mb-0">
                                        <span class="form-label text-med text-16">Criteria
                                            Information</span>
                                        <hr class="section-divider">
                                    </div>
                                </div>

                                <div id="criteriaList">
                                <?php
                                $computedTotal = 0;
                                if ($hasCriteria && $rubricID > 0) {
                                    $criteriaSql = "SELECT criterionID, criteriaTitle, criteriaDescription FROM criteria WHERE rubricID='$rubricID' ORDER BY criterionID ASC";
                                    $criteriaResult = executeQuery($criteriaSql);
                                    $criterionIndex = 0;
                                    if ($criteriaResult && $criteriaResult->num_rows > 0) {
                                        while ($criteriaRow = $criteriaResult->fetch_assoc()) {
                                            $criterionIndex++;
                                            $criterionId = intval($criteriaRow['criterionID']);
                                            $criteriaTitleValue = $criteriaRow['criteriaTitle'];
                                            $criteriaDescriptionValue = $criteriaRow['criteriaDescription'];
                                            
        	                                // Fetch levels once: collect rows and compute max points
                                            $levelRows = [];
                                            $maxPointsForCriterion = 0;
                                            $levelsSql = "SELECT levelID, levelTitle, levelDescription, points FROM level WHERE criterionID='$criterionId' ORDER BY levelID ASC";
                                            $levelsResult = executeQuery($levelsSql);
                                            if ($levelsResult && $levelsResult->num_rows > 0) {
                                                while ($levelRow = $levelsResult->fetch_assoc()) {
                                                    $levelIdVal = intval($levelRow['levelID']);
                                                    $levelTitleVal = $levelRow['levelTitle'];
                                                    $levelDescVal = $levelRow['levelDescription'];
                                                    $levelPointsVal = isset($levelRow['points']) ? floatval($levelRow['points']) : 0;
                                                    if ($levelPointsVal > $maxPointsForCriterion) {
                                                        $maxPointsForCriterion = $levelPointsVal;
                                                    }
                                                    // store as numeric array to avoid associative arrow syntax
                                                    $levelRows[] = [$levelIdVal, $levelTitleVal, $levelDescVal, $levelPointsVal];
                                                }
                                            }
                                            $computedTotal += $maxPointsForCriterion;
                                ?>
                                <div class="criterion-wrapper" data-index="<?php echo $criterionIndex; ?>" data-criterion-id="<?php echo $criterionId; ?>">
                                <div class="row">
                                    <div class="col-12 pt-0 mb-2">
                                        <div class="d-flex align-items-center">
                                                <span class="form-label text-med text-16 m-0 criterion-header-points">Criterion <?php echo $criterionIndex; ?> · <?php echo $maxPointsForCriterion; ?> Points</span>
                                                <div class="flex" style="border-top: 1px solid transparent;"></div>
                                                <button type="button" class="criterion-remove-btn" aria-label="Remove criterion">
                                                    <span class="material-symbols-outlined">close</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Criteria Title -->
                                <form class="criteria-title">
                                    <div class="row">
                                        <div class="col-12 pt-2 mb-2">
                                                <label for="criteriaTitle" class="form-label text-med text-16">Criteria Title</label>
                                                <input type="text" value="<?php echo htmlspecialchars($criteriaTitleValue); ?>" class="form-control textbox mb-1 p-3 text-reg text-14 text-muted" id="criteriaTitle" aria-describedby="criteriaTitle" placeholder="Criteria Title">
                                        </div>
                                    </div>
                                </form>

                                <!-- Criterion Description-->
                                <form class="criterion-description">
                                    <div class="row">
                                        <div class="col-12 pt-2 mb-2">
                                                <input type="text" value="<?php echo htmlspecialchars($criteriaDescriptionValue); ?>" class="form-control textbox mb-2 p-3 text-reg text-14 text-muted" id="criterionDescription" aria-describedby="criterionDescription" placeholder="Criterion Description">
                                        </div>
                                    </div>
                                </form>

                                    <!-- Levels container -->
                                    <div class="row">
                                        <div class="col-12 pt-1 levelsContainer">
                                        <?php 
                                        if (!empty($levelRows)) {
                                            for ($i = 0; $i < count($levelRows); $i++) {
                                                $levelId = $levelRows[$i][0];
                                                $levelTitleValue = $levelRows[$i][1];
                                                $levelDescriptionValue = $levelRows[$i][2];
                                                $levelPointsValue = $levelRows[$i][3];
                                        ?>
                                            <div class="criterion-description-card mb-3" data-level-id="<?php echo $levelId; ?>">
                                                <div class="card-body">
                                                    <form class="level-title">
                                                        <div class="row">
                                                            <div class="col-12 pt-2 mb-2">
                                                                <input type="text" value="<?php echo htmlspecialchars($levelTitleValue); ?>" class="form-control textbox mb-1 p-3 text-reg text-14 text-muted" id="levelTitle" aria-describedby="levelTitle" placeholder="Level Title">
                                                                <button type="button" class="card-remove-btn" aria-label="Remove criterion">
                                                                    <span class="material-symbols-outlined">close</span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                    <form class="level-description">
                                                        <div class="row">
                                                            <div class="col-12 pt-2 mb-2">
                                                                <input type="text" value="<?php echo htmlspecialchars($levelDescriptionValue); ?>" class="form-control textbox mb-1 p-3 text-reg text-14 text-muted" id="levelDescription" aria-describedby="levelDescription" placeholder="Level Description">
                                                            </div>
                                                        </div>
                                                    </form>
                                                    <form class="points-label">
                                <div class="row">
                                                            <div class="col-12 pt-2 mb-3">
                                                                <label for="criteriaTitle" class="form-label">Points</label>
                                                                <input type="text" value="<?php echo htmlspecialchars($levelPointsValue); ?>" class="form-control textbox mb-1 p-3 text-reg text-14 text-muted level-points" id="pointsLabel" aria-describedby="pointsLabel" placeholder="0">
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        <?php 
                                            }
                                        } else { 
                                        ?>
                                            <div class="criterion-description-card mb-3">
                                            <div class="card-body">
                                                    <form class="level-title">
                                                        <div class="row">
                                                            <div class="col-12 pt-2 mb-2">
                                                                <input type="text" value="" class="form-control textbox mb-1 p-3 text-reg text-14 text-muted" id="levelTitle" aria-describedby="levelTitle" placeholder="Level Title">
                                                                <button type="button" class="card-remove-btn" aria-label="Remove criterion">
                                                                    <span class="material-symbols-outlined">close</span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                    <form class="level-description">
                                                        <div class="row">
                                                            <div class="col-12 pt-2 mb-2">
                                                                <input type="text" value="" class="form-control textbox mb-1 p-3 text-reg text-14 text-muted" id="levelDescription" aria-describedby="levelDescription" placeholder="Level Description">
                                                            </div>
                                                        </div>
                                                    </form>
                                                    <form class="points-label">
                                                        <div class="row">
                                                            <div class="col-12 pt-2 mb-3">
                                                                <label for="criteriaTitle" class="form-label">Points</label>
                                                                <input type="text" value="0" class="form-control textbox mb-1 p-3 text-reg text-14 text-muted level-points" id="pointsLabel" aria-describedby="pointsLabel" placeholder="0">
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 pt-3">
                                            <button type="button" class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14 mt-2 d-inline-flex align-items-center add-level-btn" style="background-color: var(--primaryColor); border: 1px solid var(--black); margin-left: 0; margin-right: auto;">
                                                <span class="material-symbols-outlined me-1" aria-hidden="true">add_circle</span>
                                                Level
                                            </button>
                                            <hr class="section-divider mt-3 mb-3">
                                        </div>
                                    </div>
                                </div>
                                <?php 
                                        }
                                    }
                                } else { 
                                ?>
                                <!-- No criteria: render one empty criterion like create page -->
                                <div class="criterion-wrapper" data-index="1">
                                    <div class="row">
                                        <div class="col-12 pt-0 mb-2">
                                            <div class="d-flex align-items-center">
                                                <span class="form-label text-med text-16 m-0 criterion-header-points">Criterion 1 · 0 Points</span>
                                                <div class="flex" style="border-top: 1px solid transparent;"></div>
                                                <button type="button" class="criterion-remove-btn" aria-label="Remove criterion">
                                                    <span class="material-symbols-outlined">close</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <form class="criteria-title">
                                        <div class="row">
                                            <div class="col-12 pt-2 mb-2">
                                                <label for="criteriaTitle" class="form-label text-med text-16">Criteria Title</label>
                                                <input type="text" value="" class="form-control textbox mb-1 p-3 text-reg text-14 text-muted" id="criteriaTitle" aria-describedby="criteriaTitle" placeholder="Criteria Title">
                                            </div>
                                        </div>
                                    </form>
                                    <form class="criterion-description">
                                        <div class="row">
                                            <div class="col-12 pt-2 mb-2">
                                                <input type="text" value="" class="form-control textbox mb-2 p-3 text-reg text-14 text-muted" id="criterionDescription" aria-describedby="criterionDescription" placeholder="Criterion Description">
                                            </div>
                                        </div>
                                    </form>
                                    <div class="row">
                                        <div class="col-12 pt-1 levelsContainer">
                                            <div class="criterion-description-card mb-3">
                                                <div class="card-body">
                                                <form class="level-title">
                                                    <div class="row">
                                                        <div class="col-12 pt-2 mb-2">
                                                                <input type="text" value="" class="form-control textbox mb-1 p-3 text-reg text-14 text-muted" id="levelTitle" aria-describedby="levelTitle" placeholder="Level Title">
                                                                <button type="button" class="card-remove-btn" aria-label="Remove criterion">
                                                                    <span class="material-symbols-outlined">close</span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                                <form class="level-description">
                                                    <div class="row">
                                                        <div class="col-12 pt-2 mb-2">
                                                                <input type="text" value="" class="form-control textbox mb-1 p-3 text-reg text-14 text-muted" id="levelDescription" aria-describedby="levelDescription" placeholder="Level Description">
                                                        </div>
                                                    </div>
                                                </form>
                                                <form class="points-label">
                                                    <div class="row">
                                                        <div class="col-12 pt-2 mb-3">
                                                            <label for="criteriaTitle" class="form-label">Points</label>
                                                                <input type="text" value="0" class="form-control textbox mb-1 p-3 text-reg text-14 text-muted level-points" id="pointsLabel" aria-describedby="pointsLabel" placeholder="0">
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 pt-3">
                                            <button type="button" class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14 mt-2 d-inline-flex align-items-center add-level-btn" style="background-color: var(--primaryColor); border: 1px solid var(--black); margin-left: 0; margin-right: auto;">
                                                <span class="material-symbols-outlined me-1" aria-hidden="true">add_circle</span>
                                            Level
                                        </button>
                                        <hr class="section-divider mt-3 mb-3">
                                    </div>
                                    </div>
                                </div>
                                <?php } ?>
                                </div>

                                <!-- Add Criteria Button -->
                                <div class="row">
                                    <div class="col-12">
                                        <button type="button" id="addCriteriaBtn"
                                            class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14 d-inline-flex align-items-center"
                                            style="background-color: var(--primaryColor); border: 1px solid var(--black); margin-left: 0; margin-right: auto;">
                                            <span class="material-symbols-outlined me-1" aria-hidden="true">add_circle</span>
                                            Criteria
                                        </button>
                                        <span class="text-med text-16 total-points-label">Total Points: <?php echo (float)($computedTotal > 0 ? $computedTotal : $totalPoints); ?></span>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <button type="button" id="updateBtn"
                                            class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14 d-inline-flex align-items-center criteria-add-btn"
                                            style="background-color: var(--primaryColor); border: 1px solid var(--black); margin-right: auto;">
                                            Update
                                        </button>
                                    </div>
                                </div>

                                <!-- Toast Container -->
                                <div id="toastContainer"
                                    class="position-absolute top-0 start-50 translate-middle-x pt-5 pt-md-1 d-flex flex-column align-items-center"
                                    style="z-index:1100; pointer-events:none;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
        <form method="POST" id="hiddenEditPost" style="display:none;">
            <input type="hidden" name="update_rubric" value="1">
            <input type="hidden" name="rubricID" value="<?php echo $rubricID; ?>">
            <input type="hidden" name="criterionID" value="<?php echo $criterionID; ?>">
            <input type="hidden" name="levelID" value="<?php echo $levelID; ?>">
            <input type="hidden" name="rubricTitle" id="post_rubricTitle">
            <input type="hidden" name="criteriaTitle" id="post_criteriaTitle">
            <input type="hidden" name="criteriaDescription" id="post_criteriaDescription">
            <input type="hidden" name="levelTitle" id="post_levelTitle">
            <input type="hidden" name="levelDescription" id="post_levelDescription">
            <input type="hidden" name="points" id="post_points">
            <input type="hidden" name="allLevelsData" id="post_allLevelsData">
        </form>
        <script>
            (function(){
                function showAlert(message) {
                    var container = document.getElementById('toastContainer');
                    if (!container) return alert(message);

                    var alertEl = document.createElement('div');
                    alertEl.className = 'alert alert-danger alert-dismissible fade show mb-2 text-center d-flex align-items-center justify-content-center shadow-lg text-reg text-16';
                    alertEl.role = 'alert';
                    alertEl.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2 fs-6"></i>' +
                        '<span>' + message + '</span>' +
                        '<button type="button" class="btn-close ms-2" data-bs-dismiss="alert" aria-label="Close"></button>';

                    container.appendChild(alertEl);

                    setTimeout(function() {
                        alertEl.classList.remove('show');
                        alertEl.classList.add('fade');
                        setTimeout(function() { if (alertEl && alertEl.parentNode) alertEl.parentNode.removeChild(alertEl); }, 500);
                    }, 4000);
                }

                function showSuccessToast(message) {
                    var container = document.getElementById('toastContainer');
                    if (!container) return;

                    var alertEl = document.createElement('div');
                    alertEl.className = 'alert alert-success mb-2 shadow-lg text-med text-12 d-flex align-items-center justify-content-center gap-2 px-3 py-2';
                    alertEl.role = 'alert';
                    alertEl.style.transition = 'opacity 2s ease';
                    alertEl.style.opacity = '1';
                    alertEl.innerHTML = '<i class="bi bi-check-circle-fill fs-6"></i>' +
                        '<span>' + message + '</span>';

                    container.appendChild(alertEl);

                    setTimeout(function() {
                        alertEl.style.opacity = '0';
                        setTimeout(function() { alertEl.remove(); }, 2000);
                    }, 3000);
                }

                var btn = document.getElementById('updateBtn');
                if (!btn) return;
                btn.addEventListener('click', function(){
                    var get = function(id){ var el = document.getElementById(id); return el && el.value ? el.value.trim() : ''; };
                    document.getElementById('post_rubricTitle').value = get('rubricInfo');
                    document.getElementById('post_criteriaTitle').value = get('criteriaTitle');
                    document.getElementById('post_criteriaDescription').value = get('criterionDescription');
                    document.getElementById('post_levelTitle').value = get('levelTitle');
                    document.getElementById('post_levelDescription').value = get('levelDescription');
                    document.getElementById('post_points').value = get('pointsLabel');

                    // Collect all level edits across all criteria
                    var levelsData = [];
                    var wrappers = document.querySelectorAll('.criterion-wrapper');
                    for (var i = 0; i < wrappers.length; i++){
                        var criterionIdAttr = wrappers[i].getAttribute('data-criterion-id');
                        var criterionIdVal = criterionIdAttr ? parseInt(criterionIdAttr, 10) : 0;
                        var levelCards = wrappers[i].querySelectorAll('.criterion-description-card');
                        for (var j = 0; j < levelCards.length; j++){
                            var levelIdAttr = levelCards[j].getAttribute('data-level-id');
                            var levelIdVal = levelIdAttr ? parseInt(levelIdAttr, 10) : 0;
                            var titleInput = levelCards[j].querySelector('#levelTitle');
                            var descInput = levelCards[j].querySelector('#levelDescription');
                            var pointsInput = levelCards[j].querySelector('.level-points');
                            var titleVal = titleInput && titleInput.value ? titleInput.value.trim() : '';
                            var descVal = descInput && descInput.value ? descInput.value.trim() : '';
                            var pts = pointsInput && pointsInput.value ? parseFloat(pointsInput.value) : 0;
                            if (isNaN(pts)) { pts = 0; }
                            levelsData.push({ criterionID: criterionIdVal, levelID: levelIdVal, levelTitle: titleVal, levelDescription: descVal, points: pts });
                        }
                    }
                    try { document.getElementById('post_allLevelsData').value = JSON.stringify(levelsData); } catch (e) { document.getElementById('post_allLevelsData').value = '[]'; }

                    // Submit via AJAX instead of form submission
                    var form = document.getElementById('hiddenEditPost');
                    var formData = new FormData(form);
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', window.location.href, true);
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4) {
                            if (xhr.status === 200 || xhr.status === 302) {
                                // Success - show toast then redirect
                                var rubricIDInput = form.querySelector('input[name="rubricID"]');
                                var rubricID = rubricIDInput ? rubricIDInput.value : '';
                                showSuccessToast('Rubric successfully updated');
                                setTimeout(function() {
                                    window.location.href = 'assign-task.php?selectedRubricID=' + encodeURIComponent(rubricID);
                                }, 1500);
                            } else {
                                showAlert('Failed to update rubric.');
                            }
                        }
                    };
                    xhr.send(formData);
                });
            })();
        </script>

        <script>
            (function(){
                function parsePoints(val){ var n = parseFloat(val); return isNaN(n) ? 0 : n; }
                function calculateCriterionMax(wrapper){
                    if (!wrapper) return 0;
                    var inputs = wrapper.getElementsByClassName('level-points');
                    var max = 0;
                    for (var i = 0; i < inputs.length; i++) { var v = parsePoints(inputs[i].value); if (v > max) max = v; }
                    return max;
                }
                function updateCriterionHeader(wrapper){
                    if (!wrapper) return 0;
                    var max = calculateCriterionMax(wrapper);
                    var header = wrapper.querySelector('.criterion-header-points');
                    var idx = wrapper.dataset && wrapper.dataset.index ? wrapper.dataset.index : '1';
                    if (header) header.textContent = 'Criterion ' + idx + ' · ' + max + ' Points';
                    return max;
                }
                function updateTotal(){
                    var total = 0;
                    var wrappers = document.querySelectorAll('.criterion-wrapper');
                    for (var i = 0; i < wrappers.length; i++) total += calculateCriterionMax(wrappers[i]);
                    var lbl = document.querySelector('.total-points-label');
                    if (lbl) lbl.textContent = 'Total Points: ' + total;
                }
                document.addEventListener('input', function(e){
                    if (e.target && e.target.classList && e.target.classList.contains('level-points')){
                        var wrap = e.target.closest ? e.target.closest('.criterion-wrapper') : null;
                        updateCriterionHeader(wrap);
                        updateTotal();
                    }
                });
                document.addEventListener('click', function(e){
                    var btn = e.target && (e.target.closest ? e.target.closest('.card-remove-btn') : null);
                    if (!btn) return;
                    var card = btn.closest ? btn.closest('.criterion-description-card') : null;
                    if (!card) return;
                    var wrap = btn.closest ? btn.closest('.criterion-wrapper') : null;
                    var container = wrap ? wrap.querySelector('.levelsContainer') : null;
                    if (!container) return;
                    var total = container.querySelectorAll('.criterion-description-card').length;
                    if (total > 1) {
                        card.parentNode.removeChild(card);
                        updateCriterionHeader(wrap);
                        updateTotal();
                    }
                });
                function bindLevelButtons(scope){
                    var buttons = (scope || document).getElementsByClassName('add-level-btn');
                    for (var i = 0; i < buttons.length; i++){
                        buttons[i].onclick = function(){
                            var wrap = this.closest ? this.closest('.criterion-wrapper') : null;
                            if (!wrap) return;
                            var container = wrap.querySelector('.levelsContainer');
                            var firstCard = container ? container.querySelector('.criterion-description-card') : null;
                            if (!firstCard) return;
                            var clone = firstCard.cloneNode(true);
                            var inputs = clone.querySelectorAll('input');
                            for (var j = 0; j < inputs.length; j++) { inputs[j].value = ''; inputs[j].style.border = ''; }
                            container.appendChild(clone);
                            updateCriterionHeader(wrap);
                            updateTotal();
                        };
                    }
                }
                bindLevelButtons();
                // Add criteria button handler
                (function(){
                    var btn = document.getElementById('addCriteriaBtn');
                    if (!btn) return;
                    btn.addEventListener('click', function(){
                        var list = document.getElementById('criteriaList');
                        if (!list) return;
                        var wrappers = list.querySelectorAll('.criterion-wrapper');
                        var nextIndex = wrappers.length + 1;
                        // clone first wrapper as template
                        var base = wrappers.length > 0 ? wrappers[0] : null;
                        if (!base) return;
                        var wrapper = base.cloneNode(true);
                        // clear inputs
                        var inputs = wrapper.querySelectorAll('input');
                        for (var i = 0; i < inputs.length; i++) { inputs[i].value = ''; inputs[i].style.border = ''; }
                        // keep only first level card
                        var levelsContainer = wrapper.querySelector('.levelsContainer');
                        if (levelsContainer) {
                            var cards = levelsContainer.querySelectorAll('.criterion-description-card');
                            for (var k = 1; k < cards.length; k++) { cards[k].parentNode.removeChild(cards[k]); }
                        }
                        wrapper.dataset.index = String(nextIndex);
                        var header = wrapper.querySelector('.criterion-header-points');
                        if (header) header.textContent = 'Criterion ' + nextIndex + ' · 0 Points';
                        bindLevelButtons(wrapper);
                        list.appendChild(wrapper);
                        updateTotal();
                        if (wrapper.scrollIntoView) wrapper.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    });
                })();
                (function init(){
                    var wrappers = document.querySelectorAll('.criterion-wrapper');
                    for (var i = 0; i < wrappers.length; i++) updateCriterionHeader(wrappers[i]);
                    updateTotal();
                })();
            })();
        </script>

</body>

</html>