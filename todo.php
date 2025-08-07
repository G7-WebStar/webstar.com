<?php $activePage = 'todo'; ?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | My To-do</title>
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
                        <div class="row">
                            <div class="col-12">

                                <!-- Header Section -->
                                <div class="row align-items-center mb-3 text-center text-lg-start">
                                    <!-- Title -->
                                    <div class="col-12 col-lg-auto mb-3 mb-lg-0">
                                        <div class="text-bold text-25 mb-0 mt-4">
                                            My To-do
                                        </div>
                                    </div>

                                    <div class="col-12 col-lg-auto mt-4">
                                        <div class="row g-3 justify-content-center justify-content-lg-start">

                                            <!-- Sort by dropdown -->
                                            <div class="col-6 col-md-auto">
                                                <div class="d-flex align-items-center flex-nowrap">
                                                    <span class="text-reg me-2 fs-6 fs-lg-5"
                                                        style="color: var(--black); white-space: nowrap;">Sort
                                                        by:</span>
                                                    <button
                                                        class="btn text-reg dropdown-toggle d-flex justify-content-between align-items-center fs-6 fs-lg-5 dropdown-custom"
                                                        style="opacity: 1;" type="button" data-bs-toggle="dropdown"
                                                        aria-expanded="false">
                                                        <span class="me-2">Newest</span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item text-reg" href="#">Newest</a></li>
                                                        <li><a class="dropdown-item text-reg" href="#">Oldest</a></li>
                                                        <li><a class="dropdown-item text-reg" href="#">Unread first</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <!-- Course dropdown -->
                                            <div class="col-6 col-md-auto">
                                                <div class="d-flex align-items-center flex-nowrap">
                                                    <span class="text-reg me-2 fs-6 fs-lg-5"
                                                        style="color: var(--black); white-space: nowrap;">Course:</span>
                                                    <button
                                                        class="btn text-reg dropdown-toggle d-flex justify-content-between align-items-center fs-6 fs-lg-5 dropdown-custom"
                                                        style="opacity: 1;" type="button" data-bs-toggle="dropdown"
                                                        aria-expanded="false">
                                                        <span>All</span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item text-reg" href="#">All</a></li>
                                                        <li><a class="dropdown-item text-reg" href="#">COMP-006</a></li>
                                                        <li><a class="dropdown-item text-reg" href="#">Other courses</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <!-- Status dropdown -->
                                            <div class="col-6 col-md-auto mx-auto">
                                                <div class="d-flex align-items-center flex-nowrap">
                                                    <span class="text-reg me-2 fs-6 fs-lg-5"
                                                        style="color: var(--black); white-space: nowrap;">Status:</span>
                                                    <button
                                                        class="btn text-reg dropdown-toggle d-flex justify-content-between align-items-center fs-6 fs-lg-5 dropdown-custom"
                                                        style="opacity: 1;" type="button" data-bs-toggle="dropdown"
                                                        aria-expanded="false">
                                                        <span>Assigned</span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item text-reg" href="#">Assigned</a></li>
                                                        <li><a class="dropdown-item text-reg" href="#">Completed</a>
                                                        </li>
                                                        <li><a class="dropdown-item text-reg" href="#">Overdue</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-0 mt-3">
                                    <div class="col-12 col-md-10">
                                        <div class="todo-card d-flex align-items-stretch">
                                            <div class="date d-flex align-items-center justify-content-center text-sbold text-20">
                                                SEP 9
                                            </div>
                                            <div class="d-flex align-items-center flex-wrap flex-grow-1 p-2 gap-3">
                                                <div class="flex-grow-1 px-3 py-0">
                                                    <div class="text-sbold text-16">Activity #1</div>
                                                    <div class="text-reg text-12">COMP-006</div>
                                                </div>
                                                <div class="course-badge rounded-pill px-3 text-reg text-12">Task</div>
                                                <div style="margin-left: auto; margin-right: 10px;">
                                                    <i class="fa-solid fa-arrow-right text-reg text-12"
                                                        style="color: var(--black);"></i>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

</body>


</html>