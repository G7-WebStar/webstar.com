<?php $activePage = 'create-exam'; ?>

<?php
include("../shared/assets/database/connect.php");
include("../shared/assets/processes/prof-session-process.php");
?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];

    if ($action === 'save_criterion') {
        $rubricTitle = isset($_POST['rubricTitle']) ? $_POST['rubricTitle'] : '';
        $rubricType = 'Created';
        $criteriaTitle = isset($_POST['criteriaTitle']) ? $_POST['criteriaTitle'] : '';
        $criteriaDescription = isset($_POST['criteriaDescription']) ? $_POST['criteriaDescription'] : '';
        $levelsPayload = isset($_POST['levelsPayload']) ? $_POST['levelsPayload'] : '[]';
        $levels = json_decode($levelsPayload, true);

        if (!isset($_SESSION)) { session_start(); }
        $currentRubricID = isset($_SESSION['currentRubricID']) ? intval($_SESSION['currentRubricID']) : 0;

        if ($currentRubricID <= 0) {
            if ($rubricTitle === '') { echo json_encode(['success'=>false,'message'=>'Missing rubric title']); exit; }
            $rubricTitleEsc = mysqli_real_escape_string($conn, $rubricTitle);
            $rubricTypeEsc = mysqli_real_escape_string($conn, $rubricType);
            $rq = "INSERT INTO rubric (rubricTitle, rubricType, userID, totalPoints) VALUES ('$rubricTitleEsc', '$rubricTypeEsc', '$userID', 0)";
            if (!executeQuery($rq)) { echo json_encode(['success'=>false,'message'=>'Failed to create rubric']); exit; }
            $currentRubricID = mysqli_insert_id($conn);
            $_SESSION['currentRubricID'] = $currentRubricID;
        }

        $cTitleEsc = mysqli_real_escape_string($conn, $criteriaTitle);
        $cDescEsc  = mysqli_real_escape_string($conn, $criteriaDescription);
        $cq = "INSERT INTO criteria (rubricID, criteriaTitle, criteriaDescription) VALUES ('$currentRubricID', '$cTitleEsc', '$cDescEsc')";
        if (!executeQuery($cq)) { echo json_encode(['success'=>false,'message'=>'Failed to save criteria']); exit; }
        $criterionID = mysqli_insert_id($conn);

        if (is_array($levels)) {
            foreach ($levels as $lvl) {
                $lTitle = isset($lvl['levelTitle']) ? mysqli_real_escape_string($conn, $lvl['levelTitle']) : '';
                $lDesc  = isset($lvl['levelDescription']) ? mysqli_real_escape_string($conn, $lvl['levelDescription']) : '';
                $lPts   = isset($lvl['points']) && $lvl['points'] !== '' ? floatval($lvl['points']) : 0;
                $lq = "INSERT INTO level (criterionID, levelTitle, levelDescription, points) VALUES ('$criterionID', '$lTitle', '$lDesc', '$lPts')";
                executeQuery($lq);
            }
        }

        echo json_encode(['success'=>true,'rubricID'=>$currentRubricID,'criterionID'=>$criterionID]);
        exit;
    }

    if ($action === 'update_total') {
        $total = isset($_POST['totalPoints']) ? floatval($_POST['totalPoints']) : 0;
        if (!isset($_SESSION)) { session_start(); }
        $currentRubricID = isset($_SESSION['currentRubricID']) ? intval($_SESSION['currentRubricID']) : 0;
        if ($currentRubricID > 0) {
            $uq = "UPDATE rubric SET totalPoints = '$total' WHERE rubricID = '$currentRubricID' AND userID = '$userID'";
            executeQuery($uq);
            // clear current rubric session after finalize
            unset($_SESSION['currentRubricID']);
            echo json_encode(['success'=>true]);
            exit;
        }
        echo json_encode(['success'=>false]);
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

    if (!isset($_SESSION)) { session_start(); }
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
            executeQuery($rubricQuery);
            $rubricID = mysqli_insert_id($conn);
        }

        if ($criteriaTitle !== '' || $criteriaDescription !== '' || $levelTitle !== '' || $levelDescription !== '') {
            $cTitleEsc = mysqli_real_escape_string($conn, $criteriaTitle);
            $cDescEsc  = mysqli_real_escape_string($conn, $criteriaDescription);
            $criteriaQuery = "INSERT INTO criteria (rubricID, criteriaTitle, criteriaDescription) VALUES ('$rubricID', '$cTitleEsc', '$cDescEsc')";
            executeQuery($criteriaQuery);
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
                                    <div class="col text-md-start">
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
                                                            <input type="text" autocomplete="off"
                                                                class="form-control textbox mb-1 p-3 text-reg text-14 text-muted level-points"
                                                                id="pointsLabel" aria-describedby="pointsLabel"
                                                                placeholder="0">
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
                var createBtn = document.querySelector('.criteria-add-btn');
                var addLevelBtns = document.getElementsByClassName('add-level-btn');
                var addCriteriaBtn = document.getElementById('addCriteriaBtn');
                var singleCriterionWrapper = document.querySelector('.criterion-wrapper');
                var criterionIndex = 1;
                var previousCriterionMaxes = [];
                if (!createBtn) return;

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

                function getValue(id) {
                    var el = document.getElementById(id);
                    return el && el.value ? el.value.trim() : '';
                }

                function setBorder(id, hasError) {
                    var el = document.getElementById(id);
                    if (!el) return;
                    el.style.border = hasError ? '2px solid red' : '';
                }

                // Add Level: clone the first level card and append to container
                function bindLevelButtons(scope) {
                    var buttons = (scope || document).getElementsByClassName('add-level-btn');
                    for (var i = 0; i < buttons.length; i++) {
                        buttons[i].onclick = function() {
                            // find nearest criterion wrapper
                            var wrapper = this.closest ? this.closest('.criterion-wrapper') : null;
                            if (!wrapper) return;
                            var container = wrapper.querySelector('.levelsContainer');
                            if (!container) return;
                            var firstCard = container.querySelector('.criterion-description-card');
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

                // Criteria button: save current criterion+levels to DB, then switch to next criterion session
                if (addCriteriaBtn) {
                    addCriteriaBtn.addEventListener('click', function() {
                        if (!singleCriterionWrapper) return;
                        // Collect current inputs
                        var rubricTitle = getValue('rubricInfo');
                        var cTitle = getValue('criteriaTitle');
                        var cDesc = getValue('criterionDescription');
                        var levels = (function(){
                            var arr = [];
                            var cards = singleCriterionWrapper.querySelectorAll('.criterion-description-card');
                            for (var i = 0; i < cards.length; i++) {
                                var t = cards[i].querySelector('#levelTitle');
                                var d = cards[i].querySelector('#levelDescription');
                                var p = cards[i].querySelector('#pointsLabel');
                                arr.push({ levelTitle: t ? t.value.trim() : '', levelDescription: d ? d.value.trim() : '', points: p ? p.value.trim() : '' });
                            }
                            return arr;
                        })();

                        if (!rubricTitle || !cTitle || !cDesc) {
                            var focusEl = !rubricTitle ? document.getElementById('rubricInfo') : (!cTitle ? document.getElementById('criteriaTitle') : document.getElementById('criterionDescription'));
                            if (focusEl && focusEl.reportValidity) focusEl.reportValidity();
                            return;
                        }

                        // Save to server via AJAX
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', window.location.href, true);
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        var payload = 'action=save_criterion' +
                                      '&rubricTitle=' + encodeURIComponent(rubricTitle) +
                                      '&rubricType=' + encodeURIComponent('Created') +
                                      '&criteriaTitle=' + encodeURIComponent(cTitle) +
                                      '&criteriaDescription=' + encodeURIComponent(cDesc) +
                                      '&levelsPayload=' + encodeURIComponent(JSON.stringify(levels));
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState === 4) {
                                try { var data = JSON.parse(xhr.responseText); } catch (e) { data = null; }
                                if (!data || !data.success) { showAlert('Failed to save criterion.'); return; }

                                // Save current criterion's max points
                                var currentMax = updateCriterionPoints(singleCriterionWrapper);
                                previousCriterionMaxes.push(currentMax);

                                // Next criterion index
                                criterionIndex += 1;

                                // Clear inputs
                                var inputs = singleCriterionWrapper.querySelectorAll('input');
                                for (var i = 0; i < inputs.length; i++) { inputs[i].value = ''; inputs[i].style.border = ''; }

                                // Keep only first level card
                                var levelsContainer = singleCriterionWrapper.querySelector('.levelsContainer');
                                if (levelsContainer) {
                                    var cards = levelsContainer.querySelectorAll('.criterion-description-card');
                                    for (var k = 1; k < cards.length; k++) { cards[k].parentNode.removeChild(cards[k]); }
                                }

                                // Reset header and totals
                                var header = singleCriterionWrapper.querySelector('.criterion-header-points');
                                if (header) header.textContent = 'Criterion ' + criterionIndex + ' · 0 Points';
                                updateTotalPoints();
                            }
                        };
                        xhr.send(payload);
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
                        if (total > 1) {
                            card.parentNode.removeChild(card);
                            updateCriterionPoints(wrapper);
                            updateTotalPoints();
                        }
                    });
                }
                attachRemoveHandler();

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
                    if (header) header.textContent = 'Criterion ' + criterionIndex + ' · ' + max + ' Points';
                    return max;
                }

                function getTotalPoints() {
                    var total = 0;
                    for (var i = 0; i < previousCriterionMaxes.length; i++) total += previousCriterionMaxes[i];
                    total += updateCriterionPoints(singleCriterionWrapper);
                    return total;
                }

                function updateTotalPoints() {
                    var total = getTotalPoints();
                    var totalLbl = document.querySelector('.total-points-label');
                    if (totalLbl) totalLbl.textContent = 'Total Points: ' + total;
                    return total;
                }

                // Recalculate when points inputs change
                document.addEventListener('input', function(e) {
                    if (e.target && e.target.classList && e.target.classList.contains('level-points')) {
                        var wrap = e.target.closest ? e.target.closest('.criterion-wrapper') : null;
                        updateCriterionPoints(wrap);
                        updateTotalPoints();
                    }
                });

                createBtn.addEventListener('click', function() {
                    var rubricTitle = getValue('rubricInfo');
                    var criteriaTitle = getValue('criteriaTitle');
                    var criteriaDescription = getValue('criterionDescription');
                    var points = getValue('pointsLabel');

                    // Use native HTML5 validation bubbles (like Assign Task)
                    // Validate top-level fields
                    var topReq = [
                        { id: 'rubricInfo', label: 'Rubric Title' },
                        { id: 'criteriaTitle', label: 'Criteria Title' },
                        { id: 'criterionDescription', label: 'Criterion Description' }
                    ];
                    for (var i = 0; i < topReq.length; i++) {
                        var t = topReq[i];
                        var tel = document.getElementById(t.id);
                        if (!tel) continue;
                        tel.required = true;
                        if (!tel.value || tel.value.trim() === '') {
                            tel.reportValidity();
                            return;
                        }
                    }

                    // Validate all level cards
                    var containers = document.querySelectorAll('#criteriaList .levelsContainer');
                    for (var ci = 0; ci < containers.length; ci++) {
                        var cards = containers[ci].querySelectorAll('.criterion-description-card');
                        for (var c = 0; c < cards.length; c++) {
                            var card = cards[c];
                            var lt = card.querySelector('#levelTitle');
                            var ld = card.querySelector('#levelDescription');
                            if (lt) lt.required = true;
                            if (ld) ld.required = true;
                            if (lt && (!lt.value || lt.value.trim() === '')) { lt.reportValidity(); return; }
                            if (ld && (!ld.value || ld.value.trim() === '')) { ld.reportValidity(); return; }
                        }
                    }

                    // Populate hidden form and submit
                    document.getElementById('post_rubricTitle').value = rubricTitle;
                    document.getElementById('post_rubricType').value = 'Task';
                    document.getElementById('post_criteriaTitle').value = criteriaTitle;
                    document.getElementById('post_criteriaDescription').value = criteriaDescription;
                    // For now, submit only the first level (server supports single level)
                    var firstLevel = document.querySelector('#criteriaList .levelsContainer .criterion-description-card');
                    var firstTitle = firstLevel ? firstLevel.querySelector('#levelTitle') : null;
                    var firstDesc = firstLevel ? firstLevel.querySelector('#levelDescription') : null;
                    var firstPointsInput = firstLevel ? firstLevel.querySelector('#pointsLabel') : null;
                    document.getElementById('post_levelTitle').value = firstTitle ? firstTitle.value : '';
                    document.getElementById('post_levelDescription').value = firstDesc ? firstDesc.value : '';
                    // Store rubric total points and level points separately
                    document.getElementById('post_rubricTotalPoints').value = getTotalPoints();
                    document.getElementById('post_points').value = firstPointsInput && firstPointsInput.value ? firstPointsInput.value : '0';

                    document.getElementById('post_submit').click();
                });
                // Ensure default Total Points shows 0 on first load
                updateTotalPoints();

                // Clear any autofilled values on load and reset UI state
                (function resetOnLoad() {
                    var ids = ['rubricInfo','criteriaTitle','criterionDescription','levelTitle','levelDescription','pointsLabel'];
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
                    previousCriterionMaxes = [];
                    criterionIndex = 1;
                    updateTotalPoints();
                })();
            })();
        </script>

        <!-- Alert Container (Centered Top) -->
        <div id="toastContainer"
            class="position-absolute top-0 start-50 translate-middle-x p-3 d-flex flex-column align-items-center"
            style="z-index:1100; pointer-events:none;"></div>

</body>

</html>