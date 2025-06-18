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
    </style>
</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-3">

        <div class="row w-100">

            <!-- Sidebar (only shows on mobile) -->
            <?php include 'shared/components/sidebar-for-mobile.php'; ?>

            <!-- Sidebar Column (fixed on desktop) -->
            <?php include 'shared/components/sidebar-for-desktop.php'; ?>

            <!-- Main Container Column-->
            <div class="col main-container m-0 p-0 mx-2 p-4">
                <div class="card border-0 p-3 w-100 rounded-0 shadow-none">

                    <!-- Navbar for mobile -->
                    <?php include 'shared/components/navbar-for-mobile.php'; ?>

                    <div class="row g-0 w-100 h-100">

                        <!-- First Column -->
                        <div class="col-12 col-md-6 mb-2 h-100">

                            <!-- Profile -->
                            <div class="row m-0 h-50 h-sm-100 w-100">
                                <div class="col m-0 p-0 ">
                                    <div class="card profile rounded-4 border-0 p-2 me-md-2">
                                        <!-- General Info -->
                                        <div class="row m-0 p-3">
                                            <div class="col-auto m-0 p-0 d-flex align-items-center">
                                                <img src="https://avatars.githubusercontent.com/u/181800261?v=4"
                                                    width="100" height="100" class="rounded-4 m-0">
                                            </div>
                                            <div class="col mt-1">
                                                <h5 class="mb-0 text-white"
                                                    style="font-family: var(--Bold); letter-spacing: -1px; font-size:25px;">
                                                    James</h5>
                                                <p class="mb-1 text-white"
                                                    style="font-family: var(--Medium); letter-spacing: -1px; font-size:20px;">
                                                    @jamesdoe</p>
                                                <div class="d-flex gap-2 profile-pills mt-2">
                                                    <span class="px-3 rounded-5 bg-light text-primary fw-bold">LVL
                                                        0</span>
                                                    <span
                                                        class="px-3 rounded-5 bg-light text-primary fw-bold">Edit</span>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Bio -->
                                        <div class="row">
                                            <div class="col py-1 px-5 text-white" style=" font-family: var(--Medium);">
                                                Hi! I’m a web development student passionate about building clean,
                                                functional, and user-friendly websites. Always learning, always
                                                building! ⭐
                                            </div>
                                        </div>
                                        <!-- Stats -->
                                        <div class="row px-4 mt-3 mb-2">
                                            <div
                                                class="bg-white text-primary rounded-3 p-3 px-5 d-flex justify-content-between align-items-center text-center">
                                                <div class="flex-fill">
                                                    <div style="font-family: var(--Black); font-size: 1.25rem;">100
                                                    </div>
                                                    <small>rank</small>
                                                </div>
                                                <div class="vr mx-3"></div>
                                                <div class="flex-fill">
                                                    <div style="font-family: var(--Black); font-size: 1.25rem;">340
                                                    </div>
                                                    <small>webstars</small>
                                                </div>
                                                <div class="vr mx-3"></div>
                                                <div class="flex-fill">
                                                    <div style="font-family: var(--Black); font-size: 1.25rem;">50</div>
                                                    <small>followers</small>
                                                </div>
                                                <div class="vr mx-3"></div>
                                                <div class="flex-fill">
                                                    <div style="font-family: var(--Black); font-size: 1.25rem;">50</div>
                                                    <small>following</small>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <!-- Logo Badge -->
                            <div class="row m-0 mt-2 h-50 h-sm-100 w-100">
                                <div class="col m-0 p-0 ">
                                    <div class="card logo-badge rounded-4 border-0 p-2 me-md-2">
                                        <img src="shared/assets/img/badge.png" style="width: 310px; height:100%"
                                            class="mx-auto">
                                    </div>
                                </div>

                            </div>

                        </div>

                        <!-- Second Column -->
                        <div class="col-12 col-md-6 m-0 h-100">
                            <div class="card logo-badge rounded-4 border-0 h-100">
                                <!-- Currently Learning -->
                                <div class="row m-0 h-50 w-100 px-4 py-3">
                                    <h5 class="text-white m-0 p-0 mt-2 mb-2">
                                        Currently learning
                                    </h5>
                                    <div class="card px-5 py-4 bg-transparent rounded-4 text-white border-white mt-2">
                                        <div class="row d-flex align-items-center">
                                            <!-- Course Name -->
                                            <div class="col-3 m-0 p-0" style="font-family: var(--Bold);">
                                                <h4 class="m-0 p-0">HTML Course</h4>
                                            </div>
                                            <!-- Course Progress -->
                                            <div class="col-9">
                                                <div class="progress rounded-3 border border-white mt-2"
                                                    role="progressbar" aria-label="Basic example" aria-valuenow="25"
                                                    aria-valuemin="0" aria-valuemax="100"
                                                    style="height: 15px; background-color: transparent;">

                                                    <div class="progress-bar"
                                                        style="width: 60%; background-color: rgb(255, 255, 255);">
                                                    </div>
                                                </div>
                                                <div
                                                    class="d-flex justify-content-between align-items-center mt-1 text-white">
                                                    <span>Course Progress</span>
                                                    <span>60%</span>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <!-- My Badges -->
                                <div class="row m-0 h-50 w-100 px-4 py-3">
                                    <h5 class="text-white m-0 p-0 mt-2 mb-2">
                                        My Badges
                                    </h5>

                                    <div class="d-flex justify-content-center align-items-center"
                                        style="height: 275px;">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

</body>


</html>