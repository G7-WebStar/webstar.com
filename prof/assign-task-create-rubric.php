<?php $activePage = 'create-exam'; ?>

<?php
include("../shared/assets/database/connect.php");
include("../shared/assets/processes/prof-session-process.php");
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

                    <div class="container-fluid py-3 overflow-y-auto">
                        <div class="row">
                            <div class="col-12">

                                <!-- Header -->
                                <div class="row mb-3 align-items-center">
                                    <div class="col-auto">
                                        <a href="#" class="text-decoration-none">
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
                                            <input type="text"
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
                                            <input type="text"
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
                                            <input type="text"
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
                                                            <input type="text"
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
                                                            <input type="text"
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
                                                            <input type="text"
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
                                         <span class="text-med text-16 total-points-label">Total Points: 10</span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>