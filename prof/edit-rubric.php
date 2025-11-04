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
$criterionID = 0; $criteriaTitle = ''; $criteriaDescription = '';
$levelID = 0; $levelTitle = ''; $levelDescription = ''; $points = 0;

if ($rubricID > 0) {
    $rubSql = "SELECT rubricTitle, IFNULL(totalPoints,0) AS totalPoints FROM rubric WHERE rubricID='$rubricID' AND userID='$userID' LIMIT 1";
    $rub = executeQuery($rubSql);
    if ($rub && $rub->num_rows > 0) {
        $r = $rub->fetch_assoc();
        $rubricTitle = $r['rubricTitle'];
        $totalPoints = (float)$r['totalPoints'];
    }

    $critSql = "SELECT criterionID, criteriaTitle, criteriaDescription FROM criteria WHERE rubricID='$rubricID' ORDER BY criterionID ASC LIMIT 1";
    $crit = executeQuery($critSql);
    if ($crit && $crit->num_rows > 0) {
        $c = $crit->fetch_assoc();
        $criterionID = intval($c['criterionID']);
        $criteriaTitle = $c['criteriaTitle'];
        $criteriaDescription = $c['criteriaDescription'];
    }

    if ($criterionID > 0) {
        $lvlSql = "SELECT levelID, levelTitle, levelDescription, points FROM level WHERE criterionID='$criterionID' ORDER BY levelID ASC LIMIT 1";
        $lvl = executeQuery($lvlSql);
        if ($lvl && $lvl->num_rows > 0) {
            $l = $lvl->fetch_assoc();
            $levelID = intval($l['levelID']);
            $levelTitle = $l['levelTitle'];
            $levelDescription = $l['levelDescription'];
            $points = (float)$l['points'];
        }
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

    $qr = "UPDATE rubric SET rubricTitle='$rubricTitle', totalPoints='$points' WHERE rubricID='$rubricID' AND userID='$userID'";
    executeQuery($qr);

    if ($criterionID > 0) {
        $qc = "UPDATE criteria SET criteriaTitle='$criteriaTitle', criteriaDescription='$criteriaDescription' WHERE criterionID='$criterionID'";
        executeQuery($qc);
    }

    if ($levelID > 0) {
        $ql = "UPDATE level SET levelTitle='$levelTitle', levelDescription='$levelDescription', points='$points' WHERE levelID='$levelID'";
        executeQuery($ql);
    }

    header('Location: assign-task.php');
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

                                <!-- Criterion-->
                                <div class="row">
                                    <div class="col-12 pt-0 mb-2">
                                        <div class="d-flex align-items-center">
                                            <span class="form-label text-med text-16 m-0">Criterion 1 · 20
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
                                            <input type="text" value="<?php echo htmlspecialchars($criteriaTitle); ?>"
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
                                            <input type="text" value="<?php echo htmlspecialchars($criteriaDescription); ?>"
                                                class="form-control textbox mb-2 p-3 text-reg text-14 text-muted"
                                                id="criterionDescription" aria-describedby="criterionDescription"
                                                placeholder="Criterion Description">
                                        </div>
                                    </div>
                                </form>

                                <!-- Criterion Description Card -->
                                <div class="row">
                                    <div class="col-12 pt-1">
                                        <div class="criterion-description-card">
                                            <div class="card-body">

                                                <!-- Level Title -->
                                                <form class="level-title">
                                                    <div class="row">
                                                        <div class="col-12 pt-2 mb-2">
                                                            <input type="text" value="<?php echo htmlspecialchars($levelTitle); ?>"
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
                                                            <input type="text" value="<?php echo htmlspecialchars($levelDescription); ?>"
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
                                                            <input type="text" value="<?php echo htmlspecialchars($points); ?>"
                                                                class="form-control textbox mb-1 p-3 text-reg text-14 text-muted"
                                                                id="pointsLabel" aria-describedby="pointsLabel"
                                                                placeholder="1">
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 pt-3">
                                        <button type="button"
                                            class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14 mt-2 d-inline-flex align-items-center"
                                            style="background-color: var(--primaryColor); border: 1px solid var(--black); margin-left: 0; margin-right: auto;">
                                            <span class="material-symbols-outlined me-1"
                                                aria-hidden="true">add_circle</span>
                                            Level
                                        </button>
                                        <hr class="section-divider mt-3 mb-3">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <button type="button"
                                            class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14 d-inline-flex align-items-center"
                                            style="background-color: var(--primaryColor); border: 1px solid var(--black); margin-left: 0; margin-right: auto;">
                                            <span class="material-symbols-outlined me-1"
                                                aria-hidden="true">add_circle</span>
                                            Criteria
                                        </button>
                                        <span class="text-med text-16 total-points-label">Total Points: <?php echo (float)$totalPoints; ?></span>
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
        </form>
        <script>
            (function(){
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
                    document.getElementById('hiddenEditPost').submit();
                });
            })();
        </script>

</body>

</html>