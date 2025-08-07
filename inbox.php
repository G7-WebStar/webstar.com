<?php $activePage = 'inbox'; ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Webstar | My Inbox</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="shared/assets/css/global-styles.css" />
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link rel="icon" type="image/png" href="shared/assets/img/webstar-icon.png" />
    <style>
        @media (min-width: 992px) {
            .responsive-circle {
                width: 45.52px !important;
                height: 45.52px !important;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-0 p-md-3"
        style="background-color: var(--black);">
        <div class="row w-100">
            <?php include 'shared/components/sidebar-for-mobile.php'; ?>
            <?php include 'shared/components/sidebar-for-desktop.php'; ?>

            <div class="col main-container m-0 p-0 mx-0 mx-md-2 p-md-4 overflow-y-auto">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">
                    <?php include 'shared/components/navbar-for-mobile.php'; ?>

                    <div class="container-fluid py-3 overflow-y-auto">
                        <div class="row">
                            <div class="col-12">

                                <!-- Header Section -->
                                <div class="row align-items-center mb-3 text-center text-lg-start">
                                    <!-- Title -->
                                    <div class="col-12 col-lg-auto mb-3 mb-lg-0">
                                        <h1 class="text-bold text-25 mb-0 mt-4" style="color: var(--black);">My Inbox
                                        </h1>
                                    </div>

                                    <!-- Dropdowns side-by-side, centered on mobile and aligned on larger screens -->
                                    <div class="col-12 col-lg-auto mt-4">
                                        <div
                                            class="d-flex flex-nowrap justify-content-center justify-content-lg-start gap-3">

                                            <!-- Sort by dropdown -->
                                            <div class="d-flex align-items-center flex-nowrap">
                                                <span class="text-reg me-2"
                                                    style="white-space: nowrap;">Sort by:</span>
                                                <button
                                                    class="btn dropdown-toggle dropdown-custom"
                                                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <span>Newest</span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item text-reg" href="#">Newest</a></li>
                                                    <li><a class="dropdown-item text-reg" href="#">Oldest</a></li>
                                                    <li><a class="dropdown-item text-reg" href="#">Unread first</a></li>
                                                </ul>
                                            </div>

                                            <!-- Course dropdown -->
                                            <div class="d-flex align-items-center flex-nowrap">
                                                <span class="text-reg me-2"
                                                    style="white-space: nowrap;">Course:</span>
                                                <button
                                                    class="btn dropdown-toggle dropdown-custom"
                                                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <span>All Courses</span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item text-reg" href="#">All</a></li>
                                                    <li><a class="dropdown-item text-reg" href="#">COMP-006</a></li>
                                                    <li><a class="dropdown-item text-reg" href="#">Other courses</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Message Content -->
                                    <div class="message-container mt-4 mt-lg-4 pb-4">
                                        <div class="card mb-3 me-3 w-100 mt-3"
                                            style="max-width: 1101px; border: 1px solid var(--black); border-radius: 15px; background-color: var(--pureWhite); opacity: 1;">
                                            <div class="card-body p-3">
                                                <div class="row align-items-start">
                                                    <!-- Circle -->
                                                    <div
                                                        class="col-auto mb-3 mb-lg-0 mt-3 mt-lg-2 d-flex justify-content-center justify-content-lg-start">
                                                        <div class="rounded-circle responsive-circle"
                                                            style="width: 40px; height: 40px; background-color: var(--highlight75); opacity: 1;">
                                                        </div>
                                                    </div>

                                                    <!-- Message Text -->
                                                    <div class="col d-flex flex-column text-start mt-3 mt-lg-2 mb-2">
                                                        <p class="mb-2 text-sbold text-17"
                                                            style="color: var(--black); line-height: 140%;">
                                                            Prof. Christian James has posted a new assignment 'Activity
                                                            3'.
                                                        </p>
                                                        <small class="text-reg text-12"
                                                            style="color: var(--black); opacity: 0.7;">January 12, 2024
                                                            8:00AM</small>

                                                        <!-- Course tag on small screen below message text -->
                                                        <div class="d-block d-lg-none mt-2">
                                                            <span
                                                                class="text-reg text-12 badge rounded-pill course-badge"
                                                                style="width: 99px; height: 19px; border-radius: 50px; padding: 4px 10px;">
                                                                COMP-006
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <!-- Course tag on large screen right side, vertically centered -->
                                                    <div
                                                        class="col-auto d-none d-lg-flex justify-content-end align-items-center mt-4">
                                                        <span class="text-reg text-12 badge rounded-pill course-badge"
                                                            style="width: 99px; height: 19px; border-radius: 50px; padding: 4px 10px;">
                                                            COMP-006
                                                        </span>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>

                                        <!-- Second Message -->
                                        <div class="card mb-3 me-3 w-100 mt-3"
                                            style="max-width: 1101px; border: 1px solid var(--black); border-radius: 15px; background-color: var(--pureWhite); opacity: 1;">
                                            <div class="card-body p-3">
                                                <div class="row align-items-start">
                                                    <!-- Circle -->
                                                    <div
                                                        class="col-auto mb-3 mb-lg-0 mt-3 mt-lg-2d-flex justify-content-center justify-content-lg-start">
                                                        <div class="rounded-circle responsive-circle"
                                                            style="width: 40px; height: 40px; background-color: var(--highlight75); opacity: 1;">
                                                        </div>
                                                    </div>

                                                    <!-- Message Text -->
                                                    <div class="col d-flex flex-column text-start mt-3 mt-lg-2 mb-2">
                                                        <p class="mb-2 text-sbold text-17"
                                                            style="color: var(--black); line-height: 140%;">
                                                            Prof. Sarah Johnson has posted a new announcement 'Midterm Schedule'.
                                                        </p>
                                                        <small class="text-reg text-12"
                                                            style="color: var(--black); opacity: 0.7;">January 11, 2024
                                                            2:30PM</small>

                                                        <!-- Course tag on small screen below message text -->
                                                        <div class="d-block d-lg-none mt-2">
                                                            <span
                                                                class="text-reg text-12 badge rounded-pill course-badge"
                                                                style="width: 99px; height: 19px; border-radius: 50px; padding: 4px 10px;">
                                                                COMP-007
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <!-- Course tag on large screen right side, vertically centered -->
                                                    <div
                                                        class="col-auto d-none d-lg-flex justify-content-end align-items-center mt-4">
                                                        <span class="text-reg text-12 badge rounded-pill course-badge"
                                                            style="width: 99px; height: 19px; border-radius: 50px; padding: 4px 10px;">
                                                            COMP-007
                                                        </span>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>

                                        <!-- Third Message -->
                                        <div class="card mb-3 me-3 w-100 mt-3"
                                            style="max-width: 1101px; border: 1px solid var(--black); border-radius: 15px; background-color: var(--pureWhite); opacity: 1;">
                                            <div class="card-body p-3">
                                                <div class="row align-items-start">
                                                    <!-- Circle -->
                                                    <div
                                                        class="col-auto mb-3 mb-lg-0 mt-3 mt-lg-2 d-flex justify-content-center justify-content-lg-start">
                                                        <div class="rounded-circle responsive-circle"
                                                            style="width: 40px; height: 40px; background-color: var(--highlight75); opacity: 1;">
                                                        </div>
                                                    </div>

                                                    <!-- Message Text -->
                                                    <div class="col d-flex flex-column text-start mt-3 mt-lg-2 mb-2">
                                                        <p class="mb-2 text-sbold text-17"
                                                            style="color: var(--black); line-height: 140%;">
                                                            Prof. Michael Chen has updated the course materials for 'Database Design'.
                                                        </p>
                                                        <small class="text-reg text-12"
                                                            style="color: var(--black); opacity: 0.7;">January 10, 2024
                                                            11:15AM</small>

                                                        <!-- Course tag on small screen below message text -->
                                                        <div class="d-block d-lg-none mt-2">
                                                            <span
                                                                class="text-reg text-12 badge rounded-pill course-badge"
                                                                style="width: 99px; height: 19px; border-radius: 50px; padding: 4px 10px;">
                                                                COMP-008
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <!-- Course tag on large screen right side, vertically centered -->
                                                    <div
                                                        class="col-auto d-none d-lg-flex justify-content-end align-items-center mt-4">
                                                        <span class="text-reg text-12 badge rounded-pill course-badge"
                                                            style="width: 99px; height: 19px; border-radius: 50px; padding: 4px 10px;">
                                                            COMP-008
                                                        </span>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>

                                        <!-- Fourth Message -->
                                        <div class="card mb-3 me-3 w-100 mt-3"
                                            style="max-width: 1101px; border: 1px solid var(--black); border-radius: 15px; background-color: var(--pureWhite); opacity: 1;">
                                            <div class="card-body p-3">
                                                <div class="row align-items-start">
                                                    <!-- Circle -->
                                                    <div
                                                        class="col-auto mb-3 mb-lg-0 mt-3 mt-lg-2 d-flex justify-content-center justify-content-lg-start">
                                                        <div class="rounded-circle responsive-circle"
                                                            style="width: 40px; height: 40px; background-color: var(--highlight75); opacity: 1;">
                                                        </div>
                                                    </div>

                                                    <!-- Message Text -->
                                                    <div class="col d-flex flex-column text-start mt-3 mt-lg-2 mb-2">
                                                        <p class="mb-2 text-sbold text-17"
                                                            style="color: var(--black); line-height: 140%;">
                                                            Prof. Lisa Rodriguez has posted grades for 'Final Project Submission'.
                                                        </p>
                                                        <small class="text-reg text-12"
                                                            style="color: var(--black); opacity: 0.7;">January 9, 2024
                                                            4:45PM</small>

                                                        <!-- Course tag on small screen below message text -->
                                                        <div class="d-block d-lg-none mt-2">
                                                            <span
                                                                class="text-reg text-12 badge rounded-pill course-badge"
                                                                style="width: 99px; height: 19px; border-radius: 50px; padding: 4px 10px;">
                                                                COMP-009
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <!-- Course tag on large screen right side, vertically centered -->
                                                    <div
                                                        class="col-auto d-none d-lg-flex justify-content-end align-items-center mt-4">
                                                        <span class="text-reg text-12 badge rounded-pill course-badge"
                                                            style="width: 99px; height: 19px; border-radius: 50px; padding: 4px 10px;">
                                                            COMP-009
                                                        </span>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>
                                        
                                           <!-- Fifth Message -->
                                           <div class="card mb-3 me-3 w-100 mt-3"
                                            style="max-width: 1101px; border: 1px solid var(--black); border-radius: 15px; background-color: var(--pureWhite); opacity: 1;">
                                            <div class="card-body p-3">
                                                <div class="row align-items-start">
                                                    <!-- Circle -->
                                                    <div
                                                        class="col-auto mb-3 mb-lg-0 mt-3 mt-lg-2 d-flex justify-content-center justify-content-lg-start">
                                                        <div class="rounded-circle responsive-circle"
                                                            style="width: 40px; height: 40px; background-color: var(--highlight75); opacity: 1;">
                                                        </div>
                                                    </div>

                                                    <!-- Message Text -->
                                                    <div class="col d-flex flex-column text-start mt-3 mt-lg-2 mb-2">
                                                        <p class="mb-2 text-sbold text-17"
                                                            style="color: var(--black); line-height: 140%;">
                                                            Prof. Lisa Rodriguez has posted grades for 'Final Project Submission'.
                                                        </p>
                                                        <small class="text-reg text-12"
                                                            style="color: var(--black); opacity: 0.7;">January 9, 2024
                                                            4:45PM</small>

                                                        <!-- Course tag on small screen below message text -->
                                                        <div class="d-block d-lg-none mt-2">
                                                            <span
                                                                class="text-reg text-12 badge rounded-pill course-badge"
                                                                style="width: 99px; height: 19px; border-radius: 50px; padding: 4px 10px;">
                                                                COMP-009
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <!-- Course tag on large screen right side, vertically centered -->
                                                    <div
                                                        class="col-auto d-none d-lg-flex justify-content-end align-items-center mt-4">
                                                        <span class="text-reg text-12 badge rounded-pill course-badge"
                                                            style="width: 99px; height: 19px; border-radius: 50px; padding: 4px 10px;">
                                                            COMP-009
                                                        </span>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>

                                           <!-- Sixth Message -->
                                           <div class="card mb-3 me-3 w-100 mt-3"
                                            style="max-width: 1101px; border: 1px solid var(--black); border-radius: 15px; background-color: var(--pureWhite); opacity: 1;">
                                            <div class="card-body p-3">
                                                <div class="row align-items-start">
                                                    <!-- Circle -->
                                                    <div
                                                        class="col-auto mb-3 mb-lg-0 mt-3 mt-lg-2 d-flex justify-content-center justify-content-lg-start">
                                                        <div class="rounded-circle responsive-circle"
                                                            style="width: 40px; height: 40px; background-color: var(--highlight75); opacity: 1;">
                                                        </div>
                                                    </div>

                                                    <!-- Message Text -->
                                                    <div class="col d-flex flex-column text-start mt-3 mt-lg-2 mb-2">
                                                        <p class="mb-2 text-sbold text-17"
                                                            style="color: var(--black); line-height: 140%;">
                                                            Prof. Lisa Rodriguez has posted grades for 'Final Project Submission'.
                                                        </p>
                                                        <small class="text-reg text-12"
                                                            style="color: var(--black); opacity: 0.7;">January 9, 2024
                                                            4:45PM</small>

                                                        <!-- Course tag on small screen below message text -->
                                                        <div class="d-block d-lg-none mt-2">
                                                            <span
                                                                class="text-reg text-12 badge rounded-pill course-badge"
                                                                style="width: 99px; height: 19px; border-radius: 50px; padding: 4px 10px;">
                                                                COMP-009
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <!-- Course tag on large screen right side, vertically centered -->
                                                    <div
                                                        class="col-auto d-none d-lg-flex justify-content-end align-items-center mt-4">
                                                        <span class="text-reg text-12 badge rounded-pill course-badge"
                                                            style="width: 99px; height: 19px; border-radius: 50px; padding: 4px 10px;">
                                                            COMP-009
                                                        </span>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>

                                           <!-- Seventh Message -->
                                           <div class="card mb-3 me-3 w-100 mt-3"
                                            style="max-width: 1101px; border: 1px solid var(--black); border-radius: 15px; background-color: var(--pureWhite); opacity: 1;">
                                            <div class="card-body p-3">
                                                <div class="row align-items-start">
                                                    <!-- Circle -->
                                                    <div
                                                        class="col-auto mb-3 mb-lg-0 mt-3 mt-lg-2 d-flex justify-content-center justify-content-lg-start">
                                                        <div class="rounded-circle responsive-circle"
                                                            style="width: 40px; height: 40px; background-color: var(--highlight75); opacity: 1;">
                                                        </div>
                                                    </div>

                                                    <!-- Message Text -->
                                                    <div class="col d-flex flex-column text-start mt-3 mt-lg-2 mb-2">
                                                        <p class="mb-2 text-sbold text-17"
                                                            style="color: var(--black); line-height: 140%;">
                                                            Prof. Lisa Rodriguez has posted grades for 'Final Project Submission'.
                                                        </p>
                                                        <small class="text-reg text-12"
                                                            style="color: var(--black); opacity: 0.7;">January 9, 2024
                                                            4:45PM</small>

                                                        <!-- Course tag on small screen below message text -->
                                                        <div class="d-block d-lg-none mt-2">
                                                            <span
                                                                class="text-reg text-12 badge rounded-pill course-badge"
                                                                style="width: 99px; height: 19px; border-radius: 50px; padding: 4px 10px;">
                                                                COMP-009
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <!-- Course tag on large screen right side, vertically centered -->
                                                    <div
                                                        class="col-auto d-none d-lg-flex justify-content-end align-items-center mt-4">
                                                        <span class="text-reg text-12 badge rounded-pill course-badge"
                                                            style="width: 99px; height: 19px; border-radius: 50px; padding: 4px 10px;">
                                                            COMP-009
                                                        </span>
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
    </div>
    </div>

    </div>
    </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>