<?php 
$activePage = 'courseInfo'; 

include("shared/assets/database/connect.php");
?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | My Course Info</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="shared/assets/css/courseInfo.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="shared/assets/img/webstar-icon.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-0 p-md-3"
        style="background-color: var(--black);">

        <div class="row w-100">

            <?php include 'shared/components/sidebar-for-mobile.php'; ?>
            <?php include 'shared/components/sidebar-for-desktop.php'; ?>

            <div class="col main-container m-0 p-0 mx-0 mx-md-2 p-0 p-md-4 overflow-y-auto">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">

                    <?php include 'shared/components/navbar-for-mobile.php'; ?>

                    <div class="container-fluid py-3 ms-2 overflow-y-auto">
                        <div class="row">
                            <div class="row mt-0">

                                <!-- LEFT: Course Card -->
                                <div class="col-md-4">

                                    <!-- Mobile Dropdown Course Card -->
                                    <div class="course-card-mobile d-block d-md-none">
                                        <div class="course-card p-0"
                                            style="width: 100%; margin: 0 auto; outline: 1px solid var(--black); border-radius: 10px; border-bottom-left-radius: 0; border-bottom-right-radius: 0; overflow: hidden;">
                                            <!-- Yellow header section -->
                                            <div id="dropdownHeader"
                                                class="d-flex justify-content-between align-items-center px-3 py-2"
                                                style="background-color: var(--primaryColor);">

                                                <div class="flex-grow-1 text-center">
                                                    <h5 class="text-bold mb-1">COMP-006</h5>
                                                    <p class="text-reg mb-0">Web Development</p>
                                                </div>
                                                <button class="btn p-0 d-md-none" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#mobileCourseCard"
                                                    aria-expanded="false" aria-controls="mobileCourseCard">
                                                    <i class="fa fa-chevron-down text-dark"></i>
                                                </button>
                                            </div>

                                            <!-- White dropdown section -->
                                            <div class="collapse d-md-block px-3 pt-2 pb-3 bg-white"
                                                id="mobileCourseCard">
                                                <div class="course-image w-100 mb-3"
                                                    style="height: 250px; overflow: hidden; border-radius: 10px;">
                                                    <img src="shared/assets/img/home/webdev.jpg" alt="Course Image"
                                                        class="img-fluid w-100 h-100" style="object-fit: cover;">
                                                </div>

                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="avatar-image">
                                                        <img src="shared/assets/img/courseInfo/prof.png"
                                                            alt="Instructor Image" class="img-fluid">
                                                    </div>
                                                    <div class="ms-2">
                                                        <strong class="text-sbold" style="font-size: 12px;">Christian
                                                            James
                                                            Torrillo</strong>
                                                        <br>
                                                        <small class="text-reg">Professor</small>
                                                    </div>
                                                </div>

                                                <div class="mb-2">
                                                    <div class="d-flex align-items-start">
                                                        <img src="shared/assets/img/courseInfo/calendar.png"
                                                            alt="Calendar" width="20" class="me-2 mt-3">
                                                        <div>
                                                            <small class="text-reg">Thursdays 8:00AM – 10:00AM</small>
                                                            <br>
                                                            <small class="text-reg">Fridays 9:00AM – 12:00PM</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mb-2">
                                                    <label class="text-reg small mb-1">Class Standing</label>
                                                    <div
                                                        class="class-standing d-flex justify-content-between align-items-center">
                                                        <span><img src="shared/assets/img/courseInfo/star.png"
                                                                alt="Star" width="14" class="me-2">1ST</span>
                                                        <span class="fw-medium">3160 WBSTRS</span>
                                                        <span><i class="fas fa-arrow-right"></i></span>
                                                    </div>
                                                </div>
                                                <label class="text-reg small mb-1 ">To-do</label>
                                                <div class="todo-card d-flex align-items-stretch rounded-4 mt-2">
                                                    <div class="date-section text-sbold text-12">SEP 9</div>
                                                    <div
                                                        class="d-flex align-items-center flex-wrap flex-grow-1 p-2 gap-3">
                                                        <div class="flex-grow-1 px-2">
                                                            <div class="text-sbold text-12">Activity #1</div>
                                                        </div>
                                                        <div class="course-badge rounded-pill px-3 text-reg text-12">
                                                            Task</div>

                                                        <!-- Arrow icon that always shows and aligns to the right -->
                                                        <div class="ms-auto">
                                                            <i class="fa-solid fa-arrow-right text-reg text-12"
                                                                style="color: var(--black);"></i>
                                                        </div>
                                                    </div>
                                                </div>


                                            </div>
                                        </div>

                                    </div>

                                    <!-- Desktop Course Card -->
                                    <div class="course-card-desktop d-none d-md-block">
                                        <div class="course-card mx-auto"
                                            style="outline: 1px solid var(--black); border-radius: 10px;">
                                            <!-- Back Button -->
                                            <div class="mb-3">
                                                <a href="course.php" class="text-dark fs-5">
                                                    <i class="fas fa-arrow-left"></i>
                                                </a>
                                            </div>

                                            <!-- Course Image -->
                                            <div class="course-image w-100 mb-3"
                                                style="height: 250px; overflow: hidden; border-radius: 10px;">
                                                <img src="shared/assets/img/home/webdev.jpg" alt="Course Image"
                                                    class="img-fluid w-100 h-100" style="object-fit: cover;">
                                            </div>

                                            <!-- Course Info -->
                                            <h5 class="text-bold text-center mb-1">COMP-006</h5>
                                            <p class="text-center text-reg mb-3">Web Development</p>

                                            <div class="row mb-2">
                                                <div class="col">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-image">
                                                            <img src="shared/assets/img/courseInfo/prof.png"
                                                                alt="Instructor Image" class="img-fluid">
                                                        </div>
                                                        <div class="ms-2">
                                                            <strong class="text-sbold text-12">Christian James
                                                                Torrillo</strong><br>
                                                            <small class="text-reg">Professor</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col">
                                                    <div class="d-flex align-items-start">
                                                        <img src="shared/assets/img/courseInfo/calendar.png"
                                                            alt="Calendar" width="20" class="me-2 mt-3">
                                                        <div>
                                                            <small class="text-reg">Thursdays 8:00AM –
                                                                10:00AM</small><br>
                                                            <small class="text-reg">Fridays 9:00AM – 12:00PM</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col">
                                                    <label class="text-reg small mb-1">Class Standing</label>
                                                    <div
                                                        class="text-reg class-standing d-flex justify-content-between align-items-center">
                                                        <span><img src="shared/assets/img/courseInfo/star.png"
                                                                alt="Star" width="14" class="me-2">1ST</span>
                                                        <span class="text-sbold">3160 WBSTRS</span>
                                                        <span><i class="fas fa-arrow-right"></i></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-12">
                                                    <label class="text-reg small mb-1">To-do</label>
                                                    <div class="todo-card d-flex align-items-stretch rounded-4">
                                                        <div class="date-section text-sbold text-12">SEP 9</div>
                                                        <div
                                                            class="d-flex align-items-center flex-wrap flex-grow-1 p-2 gap-3">
                                                            <div class="flex-grow-1 px-2">
                                                                <div class="text-sbold text-12">Activity #1</div>
                                                            </div>
                                                            <div
                                                                class="course-badge rounded-pill px-3 text-reg text-12">
                                                                Task
                                                            </div>
                                                            <div class="d-none d-lg-block"
                                                                style="margin-left: auto; margin-right: 10px;">
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

                                <!-- RIGHT: Tabs and Content -->
                                <div class="col-md-8">
                                    <div class="tab-section">

                                        <div class="tab-carousel-wrapper position-relative d-md-none">
                                            <!-- Left Arrow -->
                                            <button id="scrollLeftBtn" class="scroll-arrow-btn start-0 d-none"
                                                aria-label="Scroll Left">
                                                <i class="fa-solid fa-chevron-left"></i>
                                            </button>

                                            <!-- Right Arrow -->
                                            <button id="scrollRightBtn" class="scroll-arrow-btn end-0"
                                                aria-label="Scroll Right">
                                                <i class="fa-solid fa-chevron-right"></i>
                                            </button>

                                            <!-- Tab Navigation -->
                                            <ul class="nav nav-tabs custom-nav-tabs mb-3" id="mobileTabScroll"
                                                role="tablist">
                                                <li class="nav-item me-3" role="presentation">
                                                    <a class="nav-link active" id="announcements-tab"
                                                        data-bs-toggle="tab" href="#announcements" role="tab"
                                                        aria-controls="announcements" aria-selected="true">
                                                        Announcements
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link" id="lessons-tab" data-bs-toggle="tab"
                                                        href="#lessons" role="tab" aria-controls="lessons"
                                                        aria-selected="false">
                                                        Lessons
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link" id="todo-tab" data-bs-toggle="tab" href="#todo"
                                                        role="tab" aria-controls="todo" aria-selected="false">
                                                        To-do
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link" id="files-tab" data-bs-toggle="tab"
                                                        href="#files" role="tab" aria-controls="files"
                                                        aria-selected="false">
                                                        Files
                                                    </a>
                                                </li>
                                                <li class="nav-item2 nav-leaderboard" role="presentation">
                                                    <a class="nav-link" id="leaderboard-tab" data-bs-toggle="tab"
                                                        href="#leaderboard" role="tab" aria-controls="leaderboard"
                                                        aria-selected="false">
                                                        Leaderboard
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>

                                        <!-- Desktop Tabs -->
                                        <div class="tab-carousel-wrapper d-none d-md-block">
                                            <ul class="nav nav-tabs custom-nav-tabs mb-3" id="myTab" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link active" id="announcements-tab"
                                                        data-bs-toggle="tab" href="#announcements"
                                                        role="tab">Announcements</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="lessons-tab" data-bs-toggle="tab"
                                                        href="#lessons" role="tab">Lessons</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="todo-tab" data-bs-toggle="tab" href="#todo"
                                                        role="tab">To-do</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="files-tab" data-bs-toggle="tab"
                                                        href="#files" role="tab">Files</a>
                                                </li>
                                                <li class="nav-item nav-leaderboard">
                                                    <a class="nav-link" id="leaderboard-tab" data-bs-toggle="tab"
                                                        href="#leaderboard" role="tab">Leaderboard</a>
                                                </li>
                                            </ul>
                                        </div>

                                        <!-- Sort by dropdown -->
                                        <div class="d-flex align-items-center flex-nowrap mb-3" id="header">
                                            <span class="dropdown-label me-2">Sort by:</span>
                                            <button class="btn dropdown-toggle dropdown-custom" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <span>Newest</span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item text-reg" href="#">Newest</a></li>
                                                <li><a class="dropdown-item text-reg" href="#">Oldest</a></li>
                                                <li><a class="dropdown-item text-reg" href="#">Unread first</a></li>
                                            </ul>
                                        </div>

                                        <!-- Tab Content -->
                                        <div class="tab-content" id="myTabContent">

                                            <!-- Announcements -->
                                            <div class="tab-pane fade show active" id="announcements" role="tabpanel">
                                                <?php include 'course-info-contents/announcements.php'; ?>
                                            </div>

                                            <!-- Lessons -->
                                            <div class="tab-pane fade" id="lessons" role="tabpanel">
                                                <?php include 'course-info-contents/lessons.php'; ?>
                                            </div>

                                            <!-- To-do -->
                                            <div class="tab-pane fade" id="todo" role="tabpanel">
                                                <?php include 'course-info-contents/to-do.php'; ?>
                                            </div>

                                            <!-- Files -->
                                            <div class="tab-pane fade" id="files" role="tabpanel">
                                                <?php include 'course-info-contents/files.php'; ?>
                                            </div>

                                            <!-- Leaderboard -->
                                            <div class="tab-pane fade" id="leaderboard" role="tabpanel">
                                                <?php include 'course-info-contents/leaderboard.php'; ?>
                                            </div>
                                        </div>

                                        <!-- Bootstrap JS -->
                                        <script
                                            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>