<?php $activePage = 'course'; ?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | My Courses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
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
                        <div class="row m-4">
                            <!-- PUT CONTENT HERE -->
                            <div class="row">
                                <div class="col-3 text-25 text-bold">
                                    My Courses
                                </div>
                                <div class="col-6 text-25 text-bold">
                                    My Courses
                                </div>
                                <div class="col-3 text-25 text-bold">
                                    My Courses
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4 mt-4">
                                    <div class="card border border-black rounded-3" style="width: 18rem;">
                                        <img src="" class="card-img-top p-2 rounded-top-3" alt="..." style="background-color: #FDDF94;">
                                        <div class="card-body border-top border-black">
                                            <div class="row lh-1">
                                                <p class="card-text text-bold text-18 m-0">COMP-006</p>
                                                <p class="card-text text-reg text-12 mb-2">Web Development</p>
                                            </div>
                                            <div class="row">
                                                <div class="col-1 d-flex justify-content-center align-items-center m-0 p-0">
                                                    <img src="https://avatars.githubusercontent.com/u/181800261?s=96&amp;v=4" alt="" width="32" height="32" class="rounded-circle">
                                                </div>
                                                <div class="col-11">
                                                    <p class="card-text text-bold text-12 m-0">Christian James Torillo</p>
                                                    <p class="card-text text-med text-12 mb-2">Professor</p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <p class="card-text">World</p>
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