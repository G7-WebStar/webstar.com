
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

                    <!-- Fixed Header -->
                    <div class="row mb-3">
                        <div class="col-12 cardHeader p-3 mb-4">

                            <!-- DESKTOP VIEW -->
                            <div class="row desktop-header d-none d-sm-flex">
                                <div class="col-auto me-2">
                                    <a href="#" class="text-decoration-none">
                                        <i class="fa-solid fa-arrow-left text-reg text-16"
                                            style="color: var(--black);"></i>
                                    </a>
                                </div>
                                <div class="col">
                                    <span class="text-sbold text-25">Quiz #1</span>
                                    <div class="text-reg text-18">Active · Due Sept 9</div>
                                </div>
                            </div>


                            <!-- MOBILE VIEW -->
                            <div class="d-block d-sm-none mobile-assignment">
                                <div class="mobile-top">
                                    <div class="arrow">
                                        <a href="#" class="text-decoration-none">
                                            <i class="fa-solid fa-arrow-left text-reg text-16"
                                                style="color: var(--black);"></i>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <span class="text-sbold text-25">Quiz #1</span>
                                        <div class="text-reg text-18">Active · Due Sept 9</div>
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
                                                            <a class="nav-link" href="assess-exam-details.php">Exam
                                                                Details</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link active" id="announcements-tab"
                                                                data-bs-toggle="tab" href="#announcements"
                                                                role="tab">Submissions</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link"
                                                                href="assess-exam-analytics.php">Analytics</a>
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
                                                        <div class="d-flex align-items-center flex-nowrap me-3">
                                                            <span class="dropdown-label me-2">Sort By</span>
                                                            <button class="btn dropdown-toggle dropdown-custom"
                                                                type="button" data-bs-toggle="dropdown"
                                                                aria-expanded="false">
                                                                <span>Newest</span>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li><a class="dropdown-item text-reg"
                                                                        href="#">Newest</a></li>
                                                                <li><a class="dropdown-item text-reg"
                                                                        href="#">COMP-006</a></li>
                                                                <li><a class="dropdown-item text-reg" href="#">Other
                                                                        courses</a></li>
                                                            </ul>
                                                        </div>
                                                        <!-- Status -->
                                                        <div class="d-flex align-items-center flex-nowrap me-3">
                                                            <span class="dropdown-label me-2">Status</span>
                                                            <button class="btn dropdown-toggle dropdown-custom"
                                                                type="button" data-bs-toggle="dropdown"
                                                                aria-expanded="false">
                                                                <span>Missing</span>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li><a class="dropdown-item text-reg"
                                                                        href="#">Missing</a></li>
                                                                <li><a class="dropdown-item text-reg"
                                                                        href="#">Pending</a></li>
                                                                <li><a class="dropdown-item text-reg"
                                                                        href="#">Submitted</a></li>
                                                            </ul>
                                                        </div>
                                                        <!-- Return All -->
                                                        <button type="button"
                                                            class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14 d-inline-flex align-items-center btn-return-all"
                                                            style="background-color: var(--primaryColor); border: 1px solid var(--black);  margin-right: auto; height: 27px;">
                                                            <span class="material-symbols-outlined">
                                                                assignment_return
                                                            </span>
                                                            Return All
                                                        </button>
                                                    </div>

                                                    <!-- Submissions List -->
                                                    <div class="submissions-list mt-4">
                                                        <div
                                                            class="submission-item d-flex align-items-center py-3 border-bottom">
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar me-3"
                                                                    style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden;">
                                                                    <img src="../shared/assets/img/assess/prof.png"
                                                                        alt="Profile"
                                                                        style="width: 100%; height: 100%; object-fit: cover;">
                                                                </div>
                                                                <span class="text-sbold text-16">Christian James D.
                                                                    Torrillo</span>
                                                            </div>
                                                            <div
                                                                class="flex-grow-1 d-flex justify-content-center submission-center">
                                                                <span class="badge badge-pending">Pending</span>
                                                            </div>
                                                            <div
                                                                class="ms-auto d-flex align-items-center submission-right">
                                                                <span class="badge-time">-</span>
                                                            </div>
                                                        </div>

                                                        <div
                                                            class="submission-item d-flex align-items-center py-3 border-bottom">
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar me-3"
                                                                    style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden;">
                                                                    <img src="../shared/assets/img/assess/prof.png"
                                                                        alt="Profile"
                                                                        style="width: 100%; height: 100%; object-fit: cover;">
                                                                </div>
                                                                <span class="text-sbold text-16">Christian James D.
                                                                    Torrillo</span>
                                                            </div>
                                                            <div
                                                                class="flex-grow-1 d-flex justify-content-center submission-center">
                                                                <span class="badge badge-score">100/100</span>
                                                            </div>
                                                            <div
                                                                class="ms-auto d-flex align-items-center submission-right">
                                                                <span class="badge-time">35 mins</span>
                                                            </div>
                                                        </div>

                                                        <div
                                                            class="submission-item d-flex align-items-center py-3 border-bottom">
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar me-3"
                                                                    style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden;">
                                                                    <img src="../shared/assets/img/assess/prof.png"
                                                                        alt="Profile"
                                                                        style="width: 100%; height: 100%; object-fit: cover;">
                                                                </div>
                                                                <span class="text-sbold text-16">Christian James D.
                                                                    Torrillo</span>
                                                            </div>
                                                            <div
                                                                class="flex-grow-1 d-flex justify-content-center submission-center">
                                                                <span class="badge badge-missing">Missing</span>
                                                            </div>
                                                            <div
                                                                class="ms-auto d-flex align-items-center submission-right">
                                                                <span class="badge-time">-</span>
                                                            </div>
                                                        </div>

                                                        <div class="submission-item d-flex align-items-center py-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar me-3"
                                                                    style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden;">
                                                                    <img src="../shared/assets/img/assess/prof.png"
                                                                        alt="Profile"
                                                                        style="width: 100%; height: 100%; object-fit: cover;">
                                                                </div>
                                                                <span class="text-sbold text-16">Christian James D.
                                                                    Torrillo</span>
                                                            </div>
                                                            <div
                                                                class="flex-grow-1 d-flex justify-content-center submission-center">
                                                                <span class="badge badge-score">100/100</span>
                                                            </div>
                                                            <div
                                                                class="ms-auto d-flex align-items-center submission-right">
                                                                <span class="badge-time">5 mins</span>
                                                            </div>
                                                        </div>
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
                                                    <div class="exam-stat-value mb-2">50</div>
                                                    <div class="exam-stat-label mb-5">Total Exam Items</div>

                                                    <div class="exam-stat-value mb-2">50</div>
                                                    <div class="exam-stat-label mb-5">Total Exam Points</div>

                                                    <div class="exam-stat-value mb-2">50</div>
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
                                                            <div class="text-reg text-14"><span
                                                                    class="stat-value">10</span> submitted</div>
                                                            <div class="text-reg text-14"><span
                                                                    class="stat-value">11</span> pending submission
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
                            legend: { display: false },
                            tooltip: { enabled: false }
                        }
                    }
                });
            }

            createDoughnutChart('taskChart', 10, 11, 0);
        </script>
</body>

</html>