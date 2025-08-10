<?php $activePage = 'course'; ?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | My Courses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="shared/assets/img/webstar-icon.png">
    <link rel="stylesheet" href="course/css/styles.css">
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
                        <div class="row m-4 justify-content-center">

                            <!-- Header Section -->
                            <div class="row header-section align-items-center justify-content-between">
                                <div class="col-12 col-xl-6 mb-3 mb-xl-0">
                                    <div class="row g-3">
                                        <div class="col-12 col-lg-4 px-0">
                                            <p class="text-sbold mb-0 lh-md text-25" id="dynamic-text">My Courses</p>
                                        </div>
                                        <div class="col-12 col-lg-8 px-0 px-xl-auto">
                                            <div class="search-container w-100 d-flex">
                                                <input type="text" placeholder="Search" class="form-control py-1 text-reg">
                                                <button type="button" class="btn btn-outline-secondary ms-2">
                                                    <i class="bi bi-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-xl-6">
                                    <div class="row g-3">
                                        <div class="col-12 col-sm-6 d-flex align-items-center px-0 px-xl-auto">
                                            <span class="me-2 text-reg">Status</span>
                                            <select class="form-select px-3 py-1 border-black rounded-4 w-50 text-reg" id="selectTag">
                                                <option value="Active">Active</option>
                                                <option value="Archived">Archived</option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-sm-6 d-flex justify-content-end align-items-center px-0">
                                            <button class="add-course-btn btn btn-primary px-3 py-1 rounded-pill text-reg">
                                                + Add Course
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Cards Section -->
                            <div class="row px-0">
                                <!-- Card -->
                                <div class="col-12 col-lg-6 col-xl-4 mt-4">
                                    <div class="card border border-black rounded-4">
                                        <img src="" class="card-img-top p-2 rounded-top-4" alt="..." style="background-color: #FDDF94; height: 190px;">
                                        <div class="card-body border-top border-black">
                                            <div class="row lh-1 mb-2">
                                                <p class="card-text text-bold text-18 m-0">COMP-006</p>
                                                <p class="card-text text-reg text-14 mb-2">Web Development</p>
                                            </div>
                                            <div class="row px-3 mb-2">
                                                <div class="col-1 d-flex justify-content-center align-items-center m-0 p-0">
                                                    <img src="https://avatars.githubusercontent.com/u/181800261?s=96&amp;v=4" alt="" width="32" height="32" class="rounded-circle">
                                                </div>
                                                <div class="col-11 my-0 lh-sm">
                                                    <p class="card-text text-bold text-14 m-0">Christian James Torillo</p>
                                                    <p class="card-text text-med text-12 mb-2">Professor</p>
                                                </div>
                                            </div>
                                            <div class="row px-3 mb-2">
                                                <div class="col-1 d-flex justify-content-center align-items-center m-0 p-0">
                                                    <img src="course/img/Calendar.png" alt="" width="24" height="24">
                                                </div>
                                                <div class="col-11 my-0 lh-sm">
                                                    <p class="card-text text-reg text-14 mb-1"><span class="text-med">Thursdays</span> 8:00AM - 10:00AM</p>
                                                    <p class="card-text text-reg text-14 mb-0"><span class="text-med">Fridays</span> 9:00AM - 12:00PM</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6 col-xl-4 mt-4">
                                    <div class="card border border-black rounded-4">
                                        <img src="" class="card-img-top p-2 rounded-top-4" alt="..." style="background-color: #FDDF94; height: 190px;">
                                        <div class="card-body border-top border-black">
                                            <div class="row lh-1 mb-2">
                                                <p class="card-text text-bold text-18 m-0">COMP-006</p>
                                                <p class="card-text text-reg text-14 mb-2">Web Development</p>
                                            </div>
                                            <div class="row px-3 mb-2">
                                                <div class="col-1 d-flex justify-content-center align-items-center m-0 p-0">
                                                    <img src="https://avatars.githubusercontent.com/u/181800261?s=96&amp;v=4" alt="" width="32" height="32" class="rounded-circle">
                                                </div>
                                                <div class="col-11 my-0 lh-sm">
                                                    <p class="card-text text-bold text-14 m-0">Christian James Torillo</p>
                                                    <p class="card-text text-med text-12 mb-2">Professor</p>
                                                </div>
                                            </div>
                                            <div class="row px-3 mb-2">
                                                <div class="col-1 d-flex justify-content-center align-items-center m-0 p-0">
                                                    <img src="course/img/Calendar.png" alt="" width="24" height="24">
                                                </div>
                                                <div class="col-11 my-0 lh-sm">
                                                    <p class="card-text text-reg text-14 mb-1"><span class="text-med">Thursdays</span> 8:00AM - 10:00AM</p>
                                                    <p class="card-text text-reg text-14 mb-0"><span class="text-med">Fridays</span> 9:00AM - 12:00PM</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6 col-xl-4 mt-4">
                                    <div class="card border border-black rounded-4">
                                        <img src="" class="card-img-top p-2 rounded-top-4" alt="..." style="background-color: #FDDF94; height: 190px;">
                                        <div class="card-body border-top border-black">
                                            <div class="row lh-1 mb-2">
                                                <p class="card-text text-bold text-18 m-0">COMP-006</p>
                                                <p class="card-text text-reg text-14 mb-2">Web Development</p>
                                            </div>
                                            <div class="row px-3 mb-2">
                                                <div class="col-1 d-flex justify-content-center align-items-center m-0 p-0">
                                                    <img src="https://avatars.githubusercontent.com/u/181800261?s=96&amp;v=4" alt="" width="32" height="32" class="rounded-circle">
                                                </div>
                                                <div class="col-11 my-0 lh-sm">
                                                    <p class="card-text text-bold text-14 m-0">Christian James Torillo</p>
                                                    <p class="card-text text-med text-12 mb-2">Professor</p>
                                                </div>
                                            </div>
                                            <div class="row px-3 mb-2">
                                                <div class="col-1 d-flex justify-content-center align-items-center m-0 p-0">
                                                    <img src="course/img/Calendar.png" alt="" width="24" height="24">
                                                </div>
                                                <div class="col-11 my-0 lh-sm">
                                                    <p class="card-text text-reg text-14 mb-1"><span class="text-med">Thursdays</span> 8:00AM - 10:00AM</p>
                                                    <p class="card-text text-reg text-14 mb-0"><span class="text-med">Fridays</span> 9:00AM - 12:00PM</p>
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

        <!-- Responsiveness -->
        <script>
            var screenSize = document.getElementById("dynamic-text");
            var selectTag = document.getElementById("selectTag");

            function checkScreenSize() {
                if (window.matchMedia("(max-width: 1348px)").matches) {
                    screenSize.classList.add("text-20");
                    screenSize.classList.remove("text-25");
                } else {
                    screenSize.classList.remove("text-20");
                    screenSize.classList.add("text-25");
                }

                if (window.matchMedia("(max-width: 575px)").matches) {
                    selectTag.classList.add("w-100");
                    selectTag.classList.remove("w-50");
                } else {
                    selectTag.classList.remove("w-100");
                    selectTag.classList.add("w-50");
                }
            }
            window.addEventListener('resize', checkScreenSize);
            checkScreenSize();
        </script>

</body>

</html>