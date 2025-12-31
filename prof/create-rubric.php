<?php $activePage = 'create-exam'; ?>

<?php
include("../shared/assets/database/connect.php");
include("../shared/assets/processes/prof-session-process.php");
?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];

    // New: create entire rubric atomically only when Create is clicked
    if ($action === 'create_full') {
        $rubricTitle = isset($_POST['rubricTitle']) ? $_POST['rubricTitle'] : '';
        $criteriaPayload = isset($_POST['criteriaPayload']) ? $_POST['criteriaPayload'] : '[]';
        $criteriaList = json_decode($criteriaPayload, true);

        if ($rubricTitle === '') {
            echo '{"success":false,"message":"Missing rubric title"}';
            exit;
        }

        $rubricTitleEsc = mysqli_real_escape_string($conn, $rubricTitle);
        $createRubricSql = "INSERT INTO rubric (rubricTitle, rubricType, userID, totalPoints) VALUES ('$rubricTitleEsc', 'Created', '$userID', 0)";
        if (!empty($rubricTitle)) {
            if (!executeQuery($createRubricSql)) {
                echo '{"success":false,"message":"Failed to create rubric"}';
                exit;
            }
        }
        $newRubricID = mysqli_insert_id($conn);

        $totalCalculated = 0;
        if (is_array($criteriaList)) {
            for ($i = 0; $i < count($criteriaList); $i++) {
                $crit = $criteriaList[$i];
                $critTitle = isset($crit['criteriaTitle']) ? mysqli_real_escape_string($conn, $crit['criteriaTitle']) : '';
                $critDesc  = isset($crit['criteriaDescription']) ? mysqli_real_escape_string($conn, $crit['criteriaDescription']) : '';
                $insertCritSql = "INSERT INTO criteria (rubricID, criteriaTitle, criteriaDescription) VALUES ('$newRubricID', '$critTitle', '$critDesc')";
                if (!empty($critTitle) && !empty($critDesc)) {
                    executeQuery($insertCritSql);
                }
                $newCriterionID = mysqli_insert_id($conn);

                $levels = isset($crit['levels']) && is_array($crit['levels']) ? $crit['levels'] : [];
                $maxForCriterion = 0;
                for ($j = 0; $j < count($levels); $j++) {
                    $lvl = $levels[$j];
                    $lvlTitle = isset($lvl['levelTitle']) ? mysqli_real_escape_string($conn, $lvl['levelTitle']) : '';
                    $lvlDesc  = isset($lvl['levelDescription']) ? mysqli_real_escape_string($conn, $lvl['levelDescription']) : '';
                    $lvlPts   = isset($lvl['points']) && $lvl['points'] !== '' ? floatval($lvl['points']) : 0;
                    if ($lvlPts > $maxForCriterion) {
                        $maxForCriterion = $lvlPts;
                    }
                    $insertLvlSql = "INSERT INTO level (criterionID, levelTitle, levelDescription, points) VALUES ('$newCriterionID', '$lvlTitle', '$lvlDesc', '$lvlPts')";
                    executeQuery($insertLvlSql);
                }
                $totalCalculated += $maxForCriterion;
            }
        }

        $updateRubricTotalSql = "UPDATE rubric SET totalPoints = '$totalCalculated' WHERE rubricID = '$newRubricID' AND userID = '$userID'";
        executeQuery($updateRubricTotalSql);

        echo '{"success":true,"rubricID":' . $newRubricID . '}';
        exit;
    }

    if ($action === 'update_total') {
        if (!isset($_SESSION)) {
            session_start();
        }
        $currentRubricID = isset($_SESSION['currentRubricID']) ? intval($_SESSION['currentRubricID']) : 0;
        if ($currentRubricID > 0) {
            // Calculate total points server-side by summing max points from all criteria
            $totalCalculated = 0;
            $criteriaSql = "SELECT criterionID FROM criteria WHERE rubricID = '$currentRubricID'";
            $criteriaResult = executeQuery($criteriaSql);
            if ($criteriaResult && $criteriaResult->num_rows > 0) {
                while ($criteriaRow = $criteriaResult->fetch_assoc()) {
                    $criterionId = intval($criteriaRow['criterionID']);
                    $maxPointsSql = "SELECT MAX(points) AS maxPoints FROM level WHERE criterionID = '$criterionId'";
                    $maxPointsResult = executeQuery($maxPointsSql);
                    if ($maxPointsResult && $maxPointsResult->num_rows > 0) {
                        $maxRow = $maxPointsResult->fetch_assoc();
                        $maxPts = isset($maxRow['maxPoints']) ? floatval($maxRow['maxPoints']) : 0;
                        $totalCalculated += $maxPts;
                    }
                }
            }
            $updateQuery = "UPDATE rubric SET totalPoints = '$totalCalculated' WHERE rubricID = '$currentRubricID' AND userID = '$userID'";
            executeQuery($updateQuery);
            // respond with rubricID so client can redirect
            echo '{"success":true,"rubricID":' . $currentRubricID . '}';
            // clear current rubric session after respond
            unset($_SESSION['currentRubricID']);
            exit;
        }
        echo '{"success":false}';
        exit;
    }
}

if (isset($_POST['save_rubric'])) {
    $rubricTitle = isset($_POST['rubricTitle']) ? $_POST['rubricTitle'] : '';

    $rubricType = 'Created';
    $rubricTotalPoints = isset($_POST['rubricTotalPoints']) && $_POST['rubricTotalPoints'] !== '' ? $_POST['rubricTotalPoints'] : 0;

    $criteriaTitle = isset($_POST['criteriaTitle']) ? $_POST['criteriaTitle'] : '';
    $criteriaDescription = isset($_POST['criteriaDescription']) ? $_POST['criteriaDescription'] : '';

    $levelTitle = isset($_POST['levelTitle']) ? $_POST['levelTitle'] : '';
    $levelDescription = isset($_POST['levelDescription']) ? $_POST['levelDescription'] : '';
    $points = isset($_POST['points']) && $_POST['points'] !== '' ? $_POST['points'] : 0;

    if (!isset($_SESSION)) {
        session_start();
    }
    $existingRubricID = isset($_SESSION['currentRubricID']) ? intval($_SESSION['currentRubricID']) : 0;

    if ($rubricTitle !== '') {
        if ($existingRubricID > 0) {
            $rubricID = $existingRubricID;

            $rtEsc = mysqli_real_escape_string($conn, $rubricTitle);
            $rtyEsc = mysqli_real_escape_string($conn, $rubricType);
            $upd = "UPDATE rubric SET rubricTitle='$rtEsc', rubricType='Created', totalPoints='$rubricTotalPoints' WHERE rubricID='$rubricID' AND userID='$userID'";
            executeQuery($upd);
        } else {
            $rtEsc = mysqli_real_escape_string($conn, $rubricTitle);
            $rubricQuery = "INSERT INTO rubric (rubricTitle, rubricType, userID, totalPoints) VALUES ('$rtEsc', 'Created', '$userID', '$rubricTotalPoints')";
            if (!empty($rubricTitle)) {
                executeQuery($rubricQuery);
            }
            $rubricID = mysqli_insert_id($conn);
        }

        if ($criteriaTitle !== '' || $criteriaDescription !== '' || $levelTitle !== '' || $levelDescription !== '') {
            $cTitleEsc = mysqli_real_escape_string($conn, $criteriaTitle);
            $cDescEsc  = mysqli_real_escape_string($conn, $criteriaDescription);
            $criteriaQuery = "INSERT INTO criteria (rubricID, criteriaTitle, criteriaDescription) VALUES ('$rubricID', '$cTitleEsc', '$cDescEsc')";
            if (!empty($criteriaTitle) && !empty($criteriaDescription)) {
                executeQuery($criteriaQuery);
            }
            $criterionID = mysqli_insert_id($conn);

            $lTitleEsc = mysqli_real_escape_string($conn, $levelTitle);
            $lDescEsc  = mysqli_real_escape_string($conn, $levelDescription);
            $ptsVal    = $points !== '' ? floatval($points) : 0;
            $levelQuery = "INSERT INTO level (criterionID, levelTitle, levelDescription, points) VALUES ('$criterionID', '$lTitleEsc', '$lDescEsc', '$ptsVal')";
            executeQuery($levelQuery);
        }

        unset($_SESSION['currentRubricID']);

        header('Location: assign-task.php?selectedRubricID=' . $rubricID);
        exit;
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Assign Task Create Rubric</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/assign-task-create-rubric.css">
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
                                    <div class="col text-md-start rubric-header-title">
                                        <span class="text-sbold text-25">Create rubric</span>
                                    </div>
                                </div>

                                <!-- Rubric Form Information -->
                                <form class="create-rubric-form">
                                    <div class="row">
                                        <div class="col-12 pt-3 mb-2">
                                            <label for="rubricInfo" class="form-label text-med text-16">Rubric
                                                Information</label>
                                            <input type="text" autocomplete="off"
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

                                <!-- Criterion List Container -->
                                <div id="criteriaList">

                                    <!-- Criterion-->
                                    <div class="criterion-wrapper">
                                        <div class="row">
                                            <div class="col-12 pt-0 mb-2">
                                                <div class="d-flex align-items-center">
                                                    <span class="form-label text-med text-16 m-0 criterion-header-points">Criterion 1 · 0
                                                        Points</span>
                                                    <div class="flex" style="border-top: 1px solid transparent;">
                                                    </div>
                                                    <button type="button" class="criterion-remove-btn"
                                                        aria-label="Remove criterion">
                                                        <span class="material-symbols-outlined">
                                                            close
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Criteria Title -->
                                        <form class="criteria-title">
                                            <div class="row">
                                                <div class="col-12 pt-2 mb-2">
                                                    <label for="criteriaTitle" class="form-label text-med text-16">Criteria
                                                        Title</label>
                                                    <input type="text" autocomplete="off"
                                                        class="form-control textbox mb-1 p-3 text-reg text-14 text-muted"
                                                        id="criteriaTitle" aria-describedby="criteriaTitle"
                                                        placeholder="Criteria Title">
                                                </div>
                                            </div>
                                        </form>

                                        <!-- Criterion Description-->
                                        <form class="criterion-description">
                                            <div class="row">
                                                <div class="col-12 pt-2 mb-2">
                                                    <input type="text" autocomplete="off"
                                                        class="form-control textbox mb-2 p-3 text-reg text-14 text-muted"
                                                        id="criterionDescription" aria-describedby="criterionDescription"
                                                        placeholder="Criterion Description">
                                                </div>
                                            </div>
                                        </form>

                                        <!-- Criterion Description Card -->
                                        <div class="row">
                                            <div class="col-12 pt-1 levelsContainer">
                                                <div class="criterion-description-card mb-3">
                                                    <div class="card-body">

                                                        <!-- Level Title -->
                                                        <form class="level-title">
                                                            <div class="row">
                                                                <div class="col-12 pt-2 mb-2">
                                                                    <input type="text" autocomplete="off"
                                                                        class="form-control textbox mb-1 p-3 text-reg text-14 text-muted"
                                                                        id="levelTitle" aria-describedby="levelTitle"
                                                                        placeholder="Level Title">
                                                                    <button type="button" class="card-remove-btn"
                                                                        aria-label="Remove criterion">
                                                                        <span class="material-symbols-outlined">
                                                                            close
                                                                        </span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </form>

                                                        <!-- Level Description -->
                                                        <form class="level-description">
                                                            <div class="row">
                                                                <div class="col-12 pt-2 mb-2">
                                                                    <input type="text" autocomplete="off"
                                                                        class="form-control textbox mb-1 p-3 text-reg text-14 text-muted"
                                                                        id="levelDescription"
                                                                        aria-describedby="levelDescription"
                                                                        placeholder="Level Description">
                                                                </div>
                                                            </div>
                                                        </form>

                                                        <!-- Points Label -->
                                                        <form class="points-label">
                                                            <div class="row">
                                                                <div class="col-12 pt-2 mb-3">
                                                                    <label for="criteriaTitle" class="form-label">Points</label>
                                                                    <input type="number" step="any" autocomplete="off" required
                                                                        class="form-control textbox mb-1 p-3 text-reg text-14 text-muted level-points"
                                                                        id="pointsLabel" aria-describedby="pointsLabel"
                                                                        placeholder="0" value="">
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12 pt-3">
                                                <button type="button" class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14 mt-2 d-inline-flex align-items-center add-level-btn"
                                                    style="background-color: var(--primaryColor); border: 1px solid var(--black); margin-left: 0; margin-right: auto;">
                                                    <span class="material-symbols-outlined me-1"
                                                        aria-hidden="true">add_circle</span>
                                                    Level
                                                </button>
                                                <hr class="section-divider mt-3 mb-3">
                                            </div>
                                        </div>

                                    </div> <!-- end .criterion-wrapper -->
                                </div> <!-- end #criteriaList -->

                                <div class="row">
                                    <div class="col-12">
                                        <button type="button" id="addCriteriaBtn"
                                            class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14 d-inline-flex align-items-center"
                                            style="background-color: var(--primaryColor); border: 1px solid var(--black); margin-left: 0; margin-right: auto;">
                                            <span class="material-symbols-outlined me-1"
                                                aria-hidden="true">add_circle</span>
                                            Criteria
                                        </button>
                                        <span class="text-med text-16 total-points-label">Total Points: 0</span>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <button type="button"
                                            class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14 d-inline-flex align-items-center criteria-add-btn"
                                            style="background-color: var(--primaryColor); border: 1px solid var(--black); margin-right: auto;">
                                            Create
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

        <form method="POST" id="hiddenRubricPost" style="display:none;">
            <input type="hidden" name="rubricTitle" id="post_rubricTitle">
            <input type="hidden" name="rubricType" id="post_rubricType" value="Created">
            <input type="hidden" name="rubricTotalPoints" id="post_rubricTotalPoints" value="0">
            <input type="hidden" name="criteriaTitle" id="post_criteriaTitle">
            <input type="hidden" name="criteriaDescription" id="post_criteriaDescription">
            <input type="hidden" name="levelTitle" id="post_levelTitle">
            <input type="hidden" name="levelDescription" id="post_levelDescription">
            <input type="hidden" name="points" id="post_points">
            <button type="submit" name="save_rubric" id="post_submit"></button>
        </form>

        <script>
            (function() {
                var createButton = document.querySelector('.criteria-add-btn');
                var addCriteriaButton = document.getElementById('addCriteriaBtn');
                var criteriaList = document.getElementById('criteriaList');
                var currentCriterionWrapper = document.querySelector('.criterion-wrapper');
                var criterionIndex = 1;
                if (!createButton) return;

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
                        setTimeout(function() {
                            if (alertEl && alertEl.parentNode) alertEl.parentNode.removeChild(alertEl);
                        }, 500);
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
                        setTimeout(function() {
                            alertEl.remove();
                        }, 2000);
                    }, 3000);
                }

                function getInputValueById(id) {
                    var el = document.getElementById(id);
                    return el && el.value ? el.value.trim() : '';
                }

                // Helper: get the last criterion wrapper
                function getLastCriterionWrapper() {
                    var wrappers = document.querySelectorAll('.criterion-wrapper');
                    if (!wrappers || wrappers.length === 0) return null;
                    return wrappers[wrappers.length - 1];
                }

                // Add Level: always append to bottom-most criterion's levels container
                function bindLevelButtons(scope) {
                    var buttons = (scope || document).getElementsByClassName('add-level-btn');
                    for (var i = 0; i < buttons.length; i++) {
                        buttons[i].onclick = function() {
                            var wrapper = getLastCriterionWrapper();
                            if (!wrapper) {
                                criterionIndex = 1;
                                var created = createCriterionWrapper(criterionIndex);
                                if (created && criteriaList) {
                                    criteriaList.appendChild(created);
                                    wrapper = created;
                                    currentCriterionWrapper = created;
                                } else {
                                    return;
                                }
                            }
                            var container = wrapper.querySelector('.levelsContainer');
                            var firstCard = container ? container.querySelector('.criterion-description-card') : null;
                            if (!firstCard) return;
                            var clone = firstCard.cloneNode(true);
                            var inputs = clone.querySelectorAll('input');
                            for (var j = 0; j < inputs.length; j++) {
                                inputs[j].value = '';
                                inputs[j].style.border = '';
                            }
                            container.appendChild(clone);
                            updateCriterionPoints(wrapper);
                            updateTotalPoints();
                        };
                    }
                }
                bindLevelButtons();

                // Create a fresh criterion wrapper based on the first one
                function createCriterionWrapper(index) {
                    var base = document.querySelector('.criterion-wrapper');
                    if (!base) return null;
                    var wrapper = base.cloneNode(true);

                    // Clear all inputs inside the clone
                    var inputs = wrapper.querySelectorAll('input');
                    for (var i = 0; i < inputs.length; i++) {
                        inputs[i].value = '';
                        inputs[i].style.border = '';
                    }

                    // Keep only the first level card
                    var levelsContainer = wrapper.querySelector('.levelsContainer');
                    if (levelsContainer) {
                        var cards = levelsContainer.querySelectorAll('.criterion-description-card');
                        for (var k = 1; k < cards.length; k++) {
                            cards[k].parentNode.removeChild(cards[k]);
                        }
                    }

                    // Update header and index
                    wrapper.dataset.index = String(index);
                    var header = wrapper.querySelector('.criterion-header-points');
                    if (header) header.textContent = 'Criterion ' + index + ' · 0 Points';

                    // Re-bind level add buttons for this wrapper
                    bindLevelButtons(wrapper);

                    return wrapper;
                }

                // Ensure initial wrapper has index 1
                if (currentCriterionWrapper) {
                    currentCriterionWrapper.dataset.index = '1';
                }

                // Criteria button: only append a new criterion form locally (no DB save)
                if (addCriteriaButton) {
                    addCriteriaButton.addEventListener('click', function() {
                        // Append a new blank criterion at the bottom
                        criterionIndex += 1;
                        var newWrapper = createCriterionWrapper(criterionIndex);
                        if (newWrapper && criteriaList) {
                            criteriaList.appendChild(newWrapper);
                            currentCriterionWrapper = newWrapper;
                            updateTotalPoints();
                            if (newWrapper.scrollIntoView) newWrapper.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }
                    });
                }

                // Remove a level card
                function attachRemoveHandler(scope) {
                    (scope || document).addEventListener('click', function(e) {
                        var btn = e.target && (e.target.closest ? e.target.closest('.card-remove-btn') : null);
                        if (!btn) return;
                        var card = btn.closest ? btn.closest('.criterion-description-card') : null;
                        if (!card) return;
                        var wrapper = btn.closest ? btn.closest('.criterion-wrapper') : null;
                        var container = wrapper ? wrapper.querySelector('.levelsContainer') : null;
                        if (!container) return;
                        var total = container.querySelectorAll('.criterion-description-card').length;
                        if (total <= 1) {
                            if (typeof showAlert === 'function') showAlert('Each criterion needs at least one level.');
                            return;
                        }
                        card.parentNode.removeChild(card);
                        updateCriterionPoints(wrapper);
                        updateTotalPoints();
                    });
                }
                attachRemoveHandler();

                // Remove an entire criterion (if more than 1)
                document.addEventListener('click', function(e) {
                    var rbtn = e.target && (e.target.closest ? e.target.closest('.criterion-remove-btn') : null);
                    if (!rbtn) return;
                    var wrap = rbtn.closest ? rbtn.closest('.criterion-wrapper') : null;
                    if (!wrap) return;
                    var all = document.querySelectorAll('.criterion-wrapper');
                    if (all.length <= 1) {
                        if (typeof showAlert === 'function') showAlert('At least one criterion is required.');
                        return;
                    }
                    wrap.parentNode.removeChild(wrap);
                    // Re-index remaining criteria headers
                    var remaining = document.querySelectorAll('.criterion-wrapper');
                    criterionIndex = remaining.length;
                    for (var i = 0; i < remaining.length; i++) {
                        remaining[i].dataset.index = String(i + 1);
                        var h = remaining[i].querySelector('.criterion-header-points');
                        if (h) {
                            var maxPts = calculateCriterionMax(remaining[i]);
                            h.textContent = 'Criterion ' + (i + 1) + ' · ' + maxPts + ' Points';
                        }
                    }
                    // Adjust current wrapper pointer to last
                    currentCriterionWrapper = remaining[remaining.length - 1];
                    updateTotalPoints();
                });

                // Update points helpers
                function parsePoints(val) {
                    var n = parseFloat(val);
                    return isNaN(n) ? 0 : n;
                }

                function updateCriterionPoints(wrapper) {
                    if (!wrapper) return;
                    var pointsInputs = wrapper.getElementsByClassName('level-points');
                    var max = 0;
                    for (var i = 0; i < pointsInputs.length; i++) {
                        var v = parsePoints(pointsInputs[i].value);
                        if (v > max) max = v;
                    }
                    var header = wrapper.querySelector('.criterion-header-points');
                    var idx = wrapper.dataset && wrapper.dataset.index ? wrapper.dataset.index : '1';
                    if (header) header.textContent = 'Criterion ' + idx + ' · ' + max + ' Points';
                    return max;
                }

                function calculateCriterionMax(wrapper) {
                    if (!wrapper) return 0;
                    var pointsInputs = wrapper.getElementsByClassName('level-points');
                    var max = 0;
                    for (var i = 0; i < pointsInputs.length; i++) {
                        var v = parsePoints(pointsInputs[i].value);
                        if (v > max) max = v;
                    }
                    return max;
                }

                function getTotalPoints() {
                    var total = 0;
                    var wrappers = document.querySelectorAll('.criterion-wrapper');
                    for (var i = 0; i < wrappers.length; i++) total += calculateCriterionMax(wrappers[i]);
                    return total;
                }

                function updateTotalPoints() {
                    var total = getTotalPoints();
                    var totalLbl = document.querySelector('.total-points-label');
                    if (totalLbl) totalLbl.textContent = 'Total Points: ' + total;
                    return total;
                }

                // Recalculate when points inputs change and reset validation state
                document.addEventListener('input', function(e) {
                    if (e.target && typeof e.target.setCustomValidity === 'function') {
                        e.target.setCustomValidity('');
                    }
                    if (e.target && e.target.classList && e.target.classList.contains('level-points')) {
                        var wrap = e.target.closest ? e.target.closest('.criterion-wrapper') : null;
                        updateCriterionPoints(wrap);
                        updateTotalPoints();
                    }
                });

                createButton.addEventListener('click', function() {
                    var rubricTitle = getInputValueById('rubricInfo');

                    // Require rubric title using native validation
                    var rubricEl = document.getElementById('rubricInfo');
                    if (rubricEl) {
                        rubricEl.required = true;
                        if (!rubricEl.value || rubricEl.value.trim() === '') {
                            rubricEl.reportValidity();
                            return;
                        }
                    }

                    // Validate ALL criteria and their levels (including newly added ones)
                    var wrappers = document.querySelectorAll('.criterion-wrapper');
                    if (!wrappers || wrappers.length === 0) {
                        showAlert('Add at least one criterion before creating.');
                        return;
                    }
                    for (var w = 0; w < wrappers.length; w++) {
                        var wrap = wrappers[w];

                        // Criterion fields
                        var cTitleEl = wrap.querySelector('#criteriaTitle');
                        var cDescEl = wrap.querySelector('#criterionDescription');
                        if (cTitleEl) {
                            cTitleEl.required = true;
                            if (!cTitleEl.value || cTitleEl.value.trim() === '') {
                                cTitleEl.reportValidity();
                                return;
                            }
                        }
                        if (cDescEl) {
                            cDescEl.required = true;
                            if (!cDescEl.value || cDescEl.value.trim() === '') {
                                cDescEl.reportValidity();
                                return;
                            }
                        }

                        // Levels under this criterion
                        var cards = wrap.querySelectorAll('.criterion-description-card');
                        if (!cards || cards.length === 0) {
                            showAlert('Each criterion needs at least one level.');
                            return;
                        }
                        for (var c = 0; c < cards.length; c++) {
                            var card = cards[c];
                            var lt = card.querySelector('#levelTitle');
                            var ld = card.querySelector('#levelDescription');
                            var lp = card.querySelector('#pointsLabel');
                            if (lt) {
                                lt.required = true;
                                if (typeof lt.setCustomValidity === 'function') lt.setCustomValidity('');
                            }
                            if (ld) {
                                ld.required = true;
                                if (typeof ld.setCustomValidity === 'function') ld.setCustomValidity('');
                            }
                            if (lp) {
                                lp.required = true;
                                if (typeof lp.setCustomValidity === 'function') lp.setCustomValidity('');
                            }
                            if (lt && (!lt.value || lt.value.trim() === '')) {
                                lt.reportValidity();
                                return;
                            }
                            if (ld && (!ld.value || ld.value.trim() === '')) {
                                ld.reportValidity();
                                return;
                            }
                            if (lp) {
                                var lpVal = lp.value ? lp.value.trim() : '';
                                if (lpVal === '') {
                                    lp.reportValidity();
                                    return;
                                }
                                var lpNum = parseFloat(lpVal);
                                if (isNaN(lpNum)) {
                                    lp.setCustomValidity('Please enter a valid number.');
                                    lp.reportValidity();
                                    return;
                                }
                            }
                        }
                    }

                    // Helper to finalize rubric and redirect
                    function finalizeRubric() {
                        // Gather all criteria and levels from the UI
                        var allCriteria = [];
                        var wrappers = document.querySelectorAll('.criterion-wrapper');
                        for (var i = 0; i < wrappers.length; i++) {
                            var cTitleEl = wrappers[i].querySelector('#criteriaTitle');
                            var cDescEl = wrappers[i].querySelector('#criterionDescription');
                            var cTitle = cTitleEl ? cTitleEl.value.trim() : '';
                            var cDesc = cDescEl ? cDescEl.value.trim() : '';
                            var levelsArr = [];
                            var cards = wrappers[i].querySelectorAll('.criterion-description-card');
                            for (var j = 0; j < cards.length; j++) {
                                var t = cards[j].querySelector('#levelTitle');
                                var d = cards[j].querySelector('#levelDescription');
                                var p = cards[j].querySelector('#pointsLabel');
                                levelsArr.push({
                                    levelTitle: t ? t.value.trim() : '',
                                    levelDescription: d ? d.value.trim() : '',
                                    points: p ? p.value.trim() : ''
                                });
                            }
                            allCriteria.push({
                                criteriaTitle: cTitle,
                                criteriaDescription: cDesc,
                                levels: levelsArr
                            });
                        }

                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', window.location.href, true);
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        var createPayload = 'action=create_full' +
                            '&rubricTitle=' + encodeURIComponent(rubricTitle) +
                            '&criteriaPayload=' + encodeURIComponent(JSON.stringify(allCriteria));
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState === 4) {
                                var resp = null;
                                try {
                                    resp = JSON.parse(xhr.responseText);
                                } catch (e) {
                                    showAlert('Failed to create rubric: Invalid server response.');
                                    return;
                                }
                                if (resp && resp.success && resp.rubricID) {
                                    showSuccessToast('Rubric successfully created');
                                    setTimeout(function() {
                                        window.location.href = 'assign-task.php?selectedRubricID=' + encodeURIComponent(resp.rubricID);
                                    }, 1500);
                                    return;
                                }
                                var errorMsg = (resp && resp.message) ? resp.message : 'Failed to create rubric.';
                                showAlert(errorMsg);
                            }
                        };
                        xhr.send(createPayload);
                    }

                    // Directly finalize (no incremental save)
                    finalizeRubric();
                });
                // Ensure default Total Points shows 0 on first load
                updateTotalPoints();

                // Clear any autofilled values on load and reset UI state
                (function resetOnLoad() {
                    var ids = ['rubricInfo', 'criteriaTitle', 'criterionDescription', 'levelTitle', 'levelDescription', 'pointsLabel'];
                    for (var i = 0; i < ids.length; i++) {
                        var el = document.getElementById(ids[i]);
                        if (el) el.value = '';
                    }
                    // Remove extra level cards beyond the first
                    var levelsContainer = document.querySelector('.levelsContainer');
                    if (levelsContainer) {
                        var cards = levelsContainer.querySelectorAll('.criterion-description-card');
                        for (var k = 1; k < cards.length; k++) {
                            cards[k].parentNode.removeChild(cards[k]);
                        }
                    }
                    // Reset header and counters
                    var header = document.querySelector('.criterion-header-points');
                    if (header) header.textContent = 'Criterion 1 · 0 Points';
                    criterionIndex = 1;
                    if (currentCriterionWrapper) currentCriterionWrapper.dataset.index = '1';
                    updateTotalPoints();
                })();
            })();
        </script>

        <!-- Toast Script -->
        <script>
            document.querySelectorAll('input[name="noted"]').forEach(function(checkbox) {
                checkbox.addEventListener('change', function(e) {
                    e.preventDefault();

                    var form = this.closest('form');
                    var formData = new FormData(form);
                    var isChecked = this.checked;
                    var container = document.getElementById("toastContainer");

                    fetch(form.action || window.location.href, {
                        method: "POST",
                        body: formData
                    });

                    var alert = document.createElement("div");
                    alert.className = 'alert mb-2 shadow-lg text-med text-12 d-flex align-items-center justify-content-center gap-2 px-3 py-2 ' + (isChecked ? 'alert-success' : 'alert-danger');
                    alert.role = "alert";
                    alert.style.transition = "opacity 2s ease";
                    alert.style.opacity = "1";

                    alert.innerHTML = '<i class="bi ' + (isChecked ? 'bi-check-circle-fill' : 'bi-x-circle-fill') + ' fs-6"></i>' +
                        '<span>' + (isChecked ? 'Marked as Noted' : 'Removed from Noted') + '</span>';

                    container.appendChild(alert);

                    setTimeout(function() {
                        alert.style.opacity = "0";
                        setTimeout(function() {
                            alert.remove();
                        }, 2000);
                    }, 3000);
                });
            });
        </script>

</body>

</html>