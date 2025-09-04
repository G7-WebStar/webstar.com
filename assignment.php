<?php $activePage = 'assignment'; ?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Assignment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/assignment.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="shared/assets/img/webstar-icon.png">
</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-0 p-md-3"
        style="background-color: var(--black);">

        <div class="row w-100">

            <!-- Sidebar (only shows on mobile) -->
            <?php include 'shared/components/sidebar-for-mobile.php'; ?>

            <!-- Sidebar Column (fixed on desktop) -->
            <?php include 'shared/components/sidebar-for-desktop.php'; ?>

            <!-- Main Container Column-->
            <div class="col main-container m-0 p-0 mx-0 mx-md-2 p-0 p-md-4 overflow-y-auto">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">

                    <!-- Navbar for mobile -->
                    <?php include 'shared/components/navbar-for-mobile.php'; ?>

                    <div class="container-fluid py-3 overflow-y-auto">
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
                                        <div class="text-reg text-18">Due Sept 9</div>
                                    </div>
                                    <div class="col-auto text-end">
                                        <div class="text-reg text-18">Graded</div>
                                        <div class="text-sbold text-25">
                                            100<span class="text-muted">/100</span>
                                        </div>
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
                                        <div class="title text-sbold text-25">Assignment #1</div>
                                    </div>
                                    <div class="due text-reg text-18">Due Sept 9</div>
                                    <div class="graded text-reg text-18 mt-4">Graded</div>
                                    <div class="score text-sbold text-25">100<span class="text-muted">/100</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Left Content -->
                            <div class="col-12 col-lg-8">
                                <div class="p-0 px-lg-5">
                                    <div class="text-sbold text-14 mt-3">Instructions</div>
                                    <p class="mb-5 mt-3 text-med text-14">Attached is a Google Doc that you can edit.
                                    </p>
                                    <p class="mb-4 text-med text-14">In Figma, design a “404 Not Found” page.</p>
                                    <p class="mb-1 text-med text-14">Create two versions, one for the mobile and one for
                                        the desktop.
                                        Turn in when done.</p>
                                    <p class="mb-4 text-med text-14">Turn in when done.</p>

                                    <hr>

                                    <div class="text-sbold text-14 mt-4">Attachments</div>
                                    <div class="cardFile text-sbold text-16 my-3 w-lg-25" style="width:200px;">
                                        <i class="px-4 py-3 fa-solid fa-file"></i> ADET A03
                                    </div>

                                    <hr>

                                    <div class="text-sbold text-14 pb-3">Prepared by</div>
                                    <div class="d-flex align-items-center pb-5">
                                        <div class="rounded-circle me-2"
                                            style="width: 50px; height: 50px; background-color: var(--highlight75);">
                                        </div>
                                        <div>
                                            <div class="text-sbold text-14">Prof. Christian James</div>
                                            <div class="text-med text-12">January 12, 2024 8:00AM</div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-12 col-lg-4">
                                <div class="cardSticky position-sticky" style="top: 20px;">
                                    <div class="p-2">
                                        <div class="text-sbold text-16">My work</div>
                                        <div class="cardFile text-sbold text-16 my-3">
                                            <i class="p-3 fa-solid fa-file"></i> Submission
                                        </div>

                                        <div class="text-sbold text-16">Status</div>
                                        <ul class="timeline list-unstyled small my-3">
                                            <li class="timeline-item">
                                                <div class="timeline-circle bg-dark"></div>
                                                <div class="timeline-content">
                                                    <div class="text-reg text-16">Assignment is ready to work on.</div>
                                                    <div class="text-reg text-12">Sep 9, 2024, 10:00PM</div>
                                                </div>
                                            </li>
                                            <li class="timeline-item">
                                                <div class="timeline-circle bg-dark"></div>
                                                <div class="timeline-content">
                                                    <div class="text-reg text-16">Your assignment has been submitted.
                                                    </div>
                                                    <div class="text-reg text-12">Sep 9, 2024, 10:00PM</div>
                                                </div>
                                            </li>
                                            <li class="timeline-item">
                                                <div class="timeline-circle big"
                                                    style="background-color: var(--primaryColor);"></div>
                                                <div class="timeline-content">
                                                    <div class="text-reg text-16">Your assignment has been graded.</div>
                                                    <div class="text-reg text-12">Sep 9, 2024, 10:00PM</div>
                                                </div>
                                            </li>
                                        </ul>

                                        <div class="d-flex gap-2 pt-3">
                                            <button class="button px-3 py-1 flex-fill rounded-pill text-reg text-md-14">
                                                + Attach Files
                                            </button>
                                                <button class="button px-3 py-1 flex-fill rounded-pill text-reg text-md-14"
                                                    style="background-color: var(--primaryColor);">
                                                    Turn In
                                                </button>
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
</body>

</html>