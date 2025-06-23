<?php $activePage = 'profile'; ?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">


    <style>
        body {
            font-family: var(--Regular);
        }

        .profile {
            background-color: var(--blue);
        }

        .logo-badge {
            background-color: var(--black);
        }

        .rank-edit-button-md {
            display: block;
        }

        .rank-edit-button-sm {
            display: none;
        }



        @media (max-width: 1300px) {
            .first-column {
                width: 100%;
                margin-bottom: 10px;
            }

            .second-column {
                width: 100%;
            }

            .course-name-col {
                width: 100%;
            }

            .course-name-text {
                text-align: center;
            }

            .progress-bar-col {
                width: 100%;
            }

        }

        @media (max-width: 480px) {
            .course-progress-text {
                display: none !important;
            }

            .course-info-col {
                padding: 30px !important;
            }
        }

        @media (max-width: 350px) {
            .stats-text {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .course-progress-text {
                display: none !important;
            }

            .course-info-col {
                padding: 30px !important;
            }
        }

        @media (max-width: 768px) {
            .bio-text {
                padding-left: 28px !important;
                padding-right: 28px !important;
            }
        }

        @media (max-width: 800px) {
            .rank-edit-button-md {
                display: none !important;
            }

            .rank-edit-button-sm {
                display: block !important;
            }
        }
    </style>

</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-0 p-md-3">

        <div class="row w-100">

            <!-- Sidebar (only shows on mobile) -->
            <?php include 'shared/components/sidebar-for-mobile.php'; ?>

            <!-- Sidebar Column (fixed on desktop) -->
            <?php include 'shared/components/sidebar-for-desktop.php'; ?>

            <!-- Main Container Column-->
            <div class="col main-container m-0 p-0 mx-0 mx-md-2 p-0 p-md-4 overflow-y-auto">
                <div class="card border-0 p-3 m-0 h-100 w-100 rounded-0 shadow-none">

                    <!-- Navbar for mobile -->
                    <?php include 'shared/components/navbar-for-mobile.php'; ?>

                    <div class="container-fluid py-3 overflow-y-auto">
                        <div class="row g-0 w-100">

                            <!-- First Column -->
                            <div class="col-12 col-md-6 first-column d-flex flex-column">
                                <!-- Profile -->
                                <div class="row m-0 w-100">
                                    <div class="col m-0 p-0 ">
                                        <div class="card profile rounded-4 border-0 p-2 me-md-2">
                                            <!-- General Info -->
                                            <div class="row m-0 px-3 pt-3 pb-1 pb-md-3 d-flex align-items-center">
                                                <div class="col-auto m-0 p-0">
                                                    <img src="https://avatars.githubusercontent.com/u/181800261?v=4"
                                                        width="100" height="100" class="rounded-4 m-0">
                                                </div>
                                                <div class="col mt-1">
                                                    <h5 class="mb-0 text-white"
                                                        style="font-family: var(--Bold); letter-spacing: -1px; font-size:25px;">
                                                        Marielle Alyssa</h5>
                                                    <p class="mb-1 text-white"
                                                        style="font-family: var(--Medium); letter-spacing: -1px; font-size:20px;">
                                                        @jamesdoe</p>
                                                    <!-- Rank and Edit button (only visible on md and up) -->
                                                    <div
                                                        class="d-flex flex-column flex-sm-row gap-2 profile-pills mt-2 rank-edit-button-md">
                                                        <span
                                                            class="px-3 rounded-5 bg-light text-primary fw-bold d-flex align-items-center"><i
                                                                class="fa-solid fa-trophy me-1"></i> Rank
                                                            5</span>
                                                        <span
                                                            class="px-3 rounded-5 bg-light text-primary fw-bold d-flex align-items-center"><i
                                                                class="fa-solid fa-pen me-2"></i>Edit</span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Bio -->
                                            <div class="row">
                                                <div class="col py-1 px-5 text-white bio-text"
                                                    style=" font-family: var(--Medium);">
                                                    Hi! I’m a web development student passionate about building clean,
                                                    functional, and user-friendly websites. Always learning, always
                                                    building! ⭐
                                                </div>
                                            </div>
                                            <div class="row rank-edit-button-sm ms-1 mb-2 mt-2">
                                                <!-- Rank and Edit button (for smaller screens) -->
                                                <div class="d-flex flex-row gap-2 profile-pills mt-2 d-flex justify-content-center">
                                                    <span
                                                        class="px-3 rounded-5 bg-light text-primary fw-bold d-flex align-items-center"><i
                                                            class="fa-solid fa-trophy me-2"></i> Rank
                                                        5</span>
                                                    <span
                                                        class="px-3 rounded-5 bg-light text-primary fw-bold d-flex align-items-center"><i
                                                            class="fa-solid fa-pen me-2"></i>Edit</span>
                                                </div>
                                            </div>
                                            <!-- Stats -->
                                            <div class="row px-4 mt-3 mb-2 d-none d-md-block">
                                                <div
                                                    class="bg-white text-primary rounded-3 p-3 px-5 d-flex justify-content-between align-items-center text-center">
                                                    <div class="flex-fill">
                                                        <div style="font-family: var(--Black); font-size: 1.25rem;">340
                                                        </div>
                                                        <small>webstars</small>
                                                    </div>
                                                    <div class="vr mx-3"></div>
                                                    <div class="flex-fill">
                                                        <div style="font-family: var(--Black); font-size: 1.25rem;">50
                                                        </div>
                                                        <small>followers</small>
                                                    </div>
                                                    <div class="vr mx-3"></div>
                                                    <div class="flex-fill">
                                                        <div style="font-family: var(--Black); font-size: 1.25rem;">50
                                                        </div>
                                                        <small>following</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Stats for Small Screen -->
                                            <div
                                                class="row text-white px-4 mt-3 mb-3 d-md-none d-flex justify-content-between align-items-center text-center">

                                                <div class="col-4 stats-text">
                                                    <div style="font-family: var(--Black); font-size: 1.25rem;">340
                                                    </div>
                                                    <small>webstars</small>
                                                </div>
                                                <div class="col-4 stats-text">
                                                    <div style="font-family: var(--Black); font-size: 1.25rem;">50
                                                    </div>
                                                    <small>followers</small>
                                                </div>
                                                <div class="col-4 stats-text">
                                                    <div style="font-family: var(--Black); font-size: 1.25rem;">50
                                                    </div>
                                                    <small>following</small>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                </div>


                                <!-- Logo Badge -->
                                <div class="row m-0 w-100 mt-2 flex-grow-1">
                                    <div class="col m-0 p-0">
                                        <div
                                            class="card logo-badge rounded-4 border-0 p-2 me-md-2 h-100 d-flex justify-content-center align-items-center">
                                            <img src="shared/assets/img/badge.png" class="h-auto w-100"
                                                style="max-height: 100%;">
                                        </div>

                                    </div>

                                </div>

                            </div>

                            <!-- Second Column -->
                            <div class="col-12 col-md-6 m-0 second-column">
                                <div class="card logo-badge rounded-4 border-0">
                                    <!-- Currently Learning -->
                                    <div class="row m-0 w-100 px-4 py-3">
                                        <h5 class="text-white m-0 p-0 mt-2 mb-2">
                                            Currently learning
                                        </h5>
                                        <div
                                            class="card px-5 py-4 bg-transparent rounded-4 text-white border-white mt-2 course-info-col">
                                            <div class="row d-flex align-items-center">
                                                <!-- Course Name -->
                                                <div class="col-md-3 col-12 m-0 p-0 text-center text-md-start course-name-col"
                                                    style="font-family: var(--Bold);">
                                                    <h4 class="m-0 p-0 course-name-text">HTML Course</h4>
                                                </div>
                                                <!-- Course Progress -->
                                                <div class="col-md-9 col-12 progress-bar-col">
                                                    <div class="progress rounded-3 border border-white mt-2"
                                                        role="progressbar" aria-label="Basic example" aria-valuenow="25"
                                                        aria-valuemin="0" aria-valuemax="100"
                                                        style="height: 15px; background-color: transparent;">

                                                        <div class="progress-bar"
                                                            style="width: 60%; background-color: rgb(255, 255, 255);">
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="d-flex justify-content-between align-items-center mt-1 text-white course-progress-text">
                                                        <span>Course Progress</span>
                                                        <span>60%</span>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <!-- My Badges -->
                                    <div class="row m-0 w-100 px-4 py-3">
                                        <div class="col m-0 p-0">
                                            <h5 class="text-white m-0 p-0 mt-2 mb-2">
                                                My Badges
                                            </h5>

                                            <div class="d-flex justify-content-center align-items-center"
                                                style="height:600px;">
                                                <h5 class="text-white m-0">
                                                    No badges yet.
                                                </h5>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

</body>


</html>