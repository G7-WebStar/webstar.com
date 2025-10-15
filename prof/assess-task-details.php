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
                                    <span class="text-sbold text-25">Assignment #1</span>
                                    <div class="text-reg text-18">Due Sep 9, 2024</div>
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
                                        <span class="text-sbold text-25">Assignment #1</span>
                                        <div class="text-reg text-18">Due Sep 9, 2024</div>
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
                                                            <a class="nav-link" id="lessons-tab" href="assess-submissions.php" role="tab">Submissions</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <!-- Tab Content -->
                                            <div class="tab-content" id="myTabContent">
                                                <!-- Task Details Tab - Active -->
                                                <div class="tab-pane fade show active" id="announcements" role="tabpanel" aria-labelledby="announcements-tab">
                                                    <div class="text-sbold text-14 mt-5">Task Instructions</div>
                                                    <p class="mb-5 mt-2 text-med text-14">Attached is a Google Doc that you can
                                                        edit.
                                                    </p>
                                                    <p class="mb-5 mt-2 text-med text-14">In Figma, design a "404 Not Found"
                                                        page.</p>
                                                    <p class="mb-1 mt-2 text-med text-14">Create two versions, one for the
                                                        mobile and
                                                        one for the desktop.</p>
                                                    <p class="mb-5  text-med text-14">Turn in when done.</p>

                                                    <hr>

                                                    <div class="text-sbold text-14 mt-3">Task Materials</div>
                                                    <div class="cardFile my-3 w-lg-25 d-flex align-items-center"
                                                        style="width:400px; max-width:100%; min-width:310px;">
                                                        <i class="px-4 py-3 fa-solid fa-file"></i>
                                                        <div class="ms-2 d-flex align-items-center">
                                                            <div class="text-sbold text-16">ADET A03</div>
                                                        </div>
                                                    </div>

                                                    <hr>

                                                    <div class="text-sbold text-14 pb-3">Prepared by</div>
                                                    <div class="d-flex align-items-center pb-5">
                                                        <div class="rounded-circle me-2"
                                                            style="width: 50px; height: 50px; background-color: var(--highlight75);">
                                                            <img src="../shared/assets/img/assess/prof.png" alt="professor"
                                                                class="rounded-circle" style="width:50px;height:50px;">
                                                        </div>
                                                        <div>
                                                            <div class="text-sbold text-14">Prof. Jane Smith</div>
                                                            <div class="text-med text-12">January 12, 2024 8:00AM</div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Submissions Tab - Disabled -->
                                                <div class="tab-pane fade" id="lessons" role="tabpanel" aria-labelledby="lessons-tab">
                                                    <!-- Empty content - tab is disabled -->
                                                </div>
                                            </div>
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
                                                                    class="stat-value">10</span> submitted</div>
                                                            <div class="text-reg text-14 mb-1"><span
                                                                    class="stat-value">11</span> did not submit</div>
                                                            <div class="text-reg text-14 mb-1"><span
                                                                    class="stat-value">0</span>
                                                                graded</div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="d-flex justify-content-center pt-3">
                                                    <button class="btn btn-action">
                                                        <img src="../shared/assets/img/assess/assess.png"
                                                            alt="Assess Icon"
                                                            style="width: 20px; height: 20px; margin-right: 5px; object-fit: contain;">Grading
                                                        Sheet
                                                    </button>
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