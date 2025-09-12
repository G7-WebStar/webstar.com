<?php $activePage = 'lessons-info'; ?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Lessons-info</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/lessons-info.css">
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
                                        <span class="text-sbold text-25">Lesson 1: Introduction to HTML</span>
                                        <div class="text-reg text-18">3 files . 1 link</div>
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
                                        <div class="title text-sbold text-25">Lesson 1:</div>
                                    </div>
                                    <div class="title text-sbold text-25">Introduction to HTML</div>
                                    <div class="due text-reg text-18">3 files · 1 link</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Left Content -->
                            <div class="col-12 col-lg-8">
                                <div class="p-0 px-lg-5">
                                    <div class="text-sbold text-14 mt-3">Lesson Objectives</div>
                                    <p class="mt-3 text-med text-14">By the end of this lesson, students should be
                                        able to:
                                    </p>
                                    <p class="mb-4 text-med text-14">
                                        1. Explain what HTML is and its role in web development.<br>
                                        2. Identify the basic structure of an HTML document.<br>
                                        3. Use common HTML tags such as headings, paragraphs, and links.<br>
                                        4. Create a simple webpage using basic HTML elements.</p>
                                    <hr>

                                    <div class="text-sbold text-14 mt-4">Learning Materials</div>
                                    <div class="cardFile my-3 w-lg-25 d-flex align-items-start"
                                        style="width:400px; max-width:100%; min-width:310px;">
                                        <i class="px-4 py-3 fa-solid fa-file"></i>
                                        <div class="ms-2">
                                            <div class="text-sbold text-16 mt-1">Web Development Course Material</div>
                                            <div class="due text-reg text-14 mb-1">PPTX · 2 MB</div>
                                        </div>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>