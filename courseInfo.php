<?php $activePage = 'courseInfo'; ?>

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
                                                            <strong class="text-sbold" style="text-12">Christian James
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

                                            <ul class="nav nav-tabs custom-nav-tabs mb-3" id="mobileTabScroll">
                                                <li class="nav-item me-3">
                                                    <a class="nav-link" href="#"
                                                        data-label="Announcements">Announcements</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" href="#" data-label="Lessons">Lessons</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" href="#" data-label="To-do">To-do</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" href="#" data-label="Files">Files</a>
                                                </li>
                                                <li class="nav-item2 nav-leaderboard">
                                                    <a class="nav-link" href="#"
                                                        data-label="Leaderboard">Leaderboard</a>
                                                </li>
                                            </ul>
                                        </div>

                                        <!-- Desktop Tabs -->
                                        <div class="tab-carousel-wrapper d-none d-md-block">
                                            <ul class="nav nav-tabs custom-nav-tabs mb-3">
                                                <li class="nav-item">
                                                    <a class="nav-link" href="#"
                                                        data-label="Announcements">Announcements</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" href="#" data-label="Lessons">Lessons</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" href="#" data-label="To-do">To-do</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" href="#" data-label="Files">Files</a>
                                                </li>
                                                <li class="nav-item nav-leaderboard">
                                                    <a class="nav-link" href="#"
                                                        data-label="Leaderboard">Leaderboard</a>
                                                </li>
                                            </ul>
                                        </div>

                                        <!-- Sort by dropdown -->
                                        <div class="d-flex align-items-center flex-nowrap" id="header">
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

                                        <div class="tab-content-area" id="tabContentArea">
                                            <!-- JIT content will be injected here -->
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

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const navLinks = document.querySelectorAll('.custom-nav-tabs .nav-link');

        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                navLinks.forEach(el => el.classList.remove('active'));
                this.classList.add('active');
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            const tabs = document.querySelectorAll('.custom-nav-tabs .nav-link, #desktopTabs .nav-link');
            const contentArea = document.getElementById('tabContentArea');


            const jitContent = {
                "Announcements": `
                    <div class="announcement-card d-flex align-items-start mb-3">
                        <!-- Instructor Image -->
                        <div class="flex-shrink-0 me-3">
                            <img src="shared/assets/img/courseInfo/prof.png" alt="Instructor Image"
                                style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">
                        </div>

                        <!-- Text Content -->
                        <div class="text-start">
                            <div class="text-reg text-12" style="color: var(--black); line-height: 140%;">
                                <strong>Prof. Christian James</strong><br>
                                <span style="font-weight: normal;">January 12, 2024 8:00AM</span>
                            </div>


                            <p class="d-none d-md-block mb-0 mt-3 text-reg text-14" style="color: var(--black); line-height: 140%;">
                                Welcome to our course! Please make sure to check the Course Overview
                                under the “Lessons” tab before our first face-to-face session this week.
                            </p>


                            <!-- For mobile -->
                            <p class="text-reg d-md-none mb-0 mt-3 text-reg text-12" style="color: var(--black); line-height: 140%;">
                                Welcome to our course! Please make sure to check the Course Overview
                                under the “Lessons” tab before our first face-to-face session this week.
                            </p>
                        </div>
                    </div>

                `,
                "Lessons": `
                    <div class="customCard text-sbold p-3">
                        <p>Course Overview</p>
                        <p>Here you will find all the lessons and learning materials.</p>
                    </div>
                `,
                "To-do": `
                <!-- Task container -->
                <div class="row mb-0 mt-3">
                    <div class="col-12 col-md-10">
                        <div class="todo-card d-flex align-items-stretch">
                            <!-- Date -->
                            <div
                                class="date d-flex align-items-center justify-content-center text-sbold text-20">
                                SEP 9
                            </div>
                            <!-- Main content -->
                            <div class="d-flex flex-grow-1 flex-wrap justify-content-between p-2 w-100">
                                <!-- For small screen of main content -->
                                <div class="px-3 py-0">
                                    <div class="text-sbold text-16">Activity #1</div>
                                        <span
                                        class="course-badge rounded-pill px-3 text-reg text-12 mt-2 d-inline d-md-none">Task</span>
                                </div>
                                <!-- Pill and Arrow on Large screen-->
                                <div class="d-flex align-items-center gap-2 ms-auto">
                                    <span
                                        class="course-badge rounded-pill px-3 text-reg text-12 d-none d-md-inline">Task</span>
                                    <i class="fa-solid fa-arrow-right text-reg text-12 pe-2"
                                        style="color: var(--black);"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                `,
                "Files": `
                <div class="row mb-0 mt-3">
                    <div class="col">
                        <div class="todo-card d-flex align-items-stretch p-2">
                            <div class="d-flex w-100 align-items-center justify-content-between">

                                <!-- Left side: File icon and Text -->
                                <div class="d-flex align-items-center flex-grow-1">
                                    <!-- File icon -->
                                    <div class="mx-4">
                                        <img src="shared/assets/img/doc.png" alt="File Icon"
                                            style="width: 16px; height: 20px;">
                                    </div>

                                    <!-- Content -->
                                    <div>
                                        <div class="text-sbold text-16 py-1" style="line-height: 1;">
                                             Web Development Course Material
                                        </div>
                                        <div class="text-reg text-12" style="line-height: 1;">Uploaded January 12, 2024</div>
                                    </div>
                                </div>

                                <!-- Download icon aligned to the right end -->
                                <div class="mx-4">
                                    <img src="shared/assets/img/dl.png" alt="Download Icon"
                                        style="width: 16px; height: 20px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                `,
                "Leaderboard": `
                    <div class="customCard text-sbold p-3">
                            <div class="row">
                                <div class="col-12 col-xl-4 mt-3 px-0 mx-auto mx-md-0 d-flex d-md-block justify-content-center justify-content-md-auto px-1">
                                    <div class="card rounded-4 col-6 col-md-12">
                                        <div class="card-body border border-black rounded-4">
                                            <div class="row">
                                                <div class="col-6 d-flex align-items-center">
                                                    <img src="https://avatars.githubusercontent.com/u/181800261?s=96&amp;v=4" alt="" width="90" height="90" class="rounded-circle float-start leaderboard-img">
                                                </div>
                                                <div class="col-6">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="float-end text-xl-36 text-xs-28 text-40">1</div>
                                                        </div>
                                                    </div>
                                                    <div class="badge rounded-pill text-dark text-reg float-end d-flex flex-row d-xxs-none" style="background-color: #C8ECC1;">
                                                        <i class="bi bi-caret-up-fill me-1"></i><div class="me-1">2</div>
                                                    </div>
                                                </div>
                                                <div class="col-12 mt-3 text-xl-12 text-lg-16 text-xs-12">
                                                    Christian James D. Torrillo
                                                </div>
                                                <div class="col-12 text-reg text-xl-12 text-lg-16 text-xs-12">
                                                    3160 XPs
                                                </div>
                                            </div>                           
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-12 col-xl-4 mt-3 px-0 px-1">
                                    <div class="card rounded-4">
                                        <div class="card-body border border-black rounded-4">
                                            <div class="row">
                                                <div class="col-6 d-flex align-items-center">
                                                    <img src="https://avatars.githubusercontent.com/u/181800261?s=96&amp;v=4" alt="" width="90" height="90" class="rounded-circle float-start leaderboard-img">
                                                </div>
                                                <div class="col-6">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="float-end text-xl-36 text-xs-28 text-40">2</div>
                                                        </div>
                                                    </div>
                                                    <div class="badge rounded-pill text-dark text-reg float-end d-flex flex-row d-xxs-none" style="background-color: #ECC1C1;">
                                                        <i class="bi bi-caret-down-fill me-1"></i><div class="me-1">1</div>
                                                    </div>
                                                </div>
                                                <div class="col-12 mt-3 text-xl-12 text-lg-16 text-xs-12">
                                                    Christian James D. Torrillo
                                                </div>
                                                <div class="col-12 text-reg text-xl-12 text-lg-16 text-xs-12">
                                                    3160 XPs
                                                </div>
                                            </div>                           
                                        </div>
                                    </div>
                                </div>
                                 <div class="col-6 col-md-12 col-xl-4 mt-3 px-0 px-1">
                                    <div class="card rounded-4">
                                        <div class="card-body border border-black rounded-4">
                                            <div class="row">
                                                <div class="col-6 d-flex align-items-center">
                                                    <img src="https://avatars.githubusercontent.com/u/181800261?s=96&amp;v=4" alt="" width="90" height="90" class="rounded-circle float-start leaderboard-img">
                                                </div>
                                                <div class="col-6">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="float-end text-xl-36 text-xs-28 text-40">3</div>
                                                        </div>
                                                    </div>
                                                    <div class="badge rounded-pill text-dark text-reg float-end d-flex flex-row d-xxs-none" style="background-color: #DFDFDF;">
                                                        =
                                                    </div>
                                                </div>
                                                <div class="col-12 mt-3 text-xl-12 text-lg-16 text-xs-12">
                                                    Christian James D. Torrillo
                                                </div>
                                                <div class="col-12 text-reg text-xl-12 text-lg-16 text-xs-12">
                                                    3160 XPs
                                                </div>
                                            </div>                           
                                        </div>
                                    </div>
                                </div>
                                <div class="container-fluid">
                                    <div class="row px-1">
                                        <div class="col-12 border border-black mx-auto mt-3 rounded-4 px-4 py-2 bg-white">
                                            <div class="row">
                                                <div class="col-3 d-flex align-items-center justify-content-around">
                                                    <span class="text-xl-36 text-xs-28 text-30">
                                                        4
                                                    </span>
                                                    <span class="badge rounded-pill text-dark text-reg float-end d-flex flex-row d-xs-none d-md-none d-lg-flex" style="background-color: #C8ECC1;">
                                                        <i class="bi bi-caret-up-fill me-1"></i><div class="me-1">2</div>
                                                    </span>
                                                </div>
                                                <div class="col-9 d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <img src="https://avatars.githubusercontent.com/u/181800261?s=96&v=4" 
                                                            alt="" width="40" height="40" 
                                                            class="rounded-circle me-2">
                                                        <span class="text-xl-12">Christian James D. Torrillo</span>
                                                    </div>
                                                    <div class="text-reg text-xl-12 d-block d-md-none d-lg-block">
                                                        3160 XPs
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                `
            };

            function loadContent(label) {
                contentArea.innerHTML = jitContent[label] || `<p>No content available for ${label}</p>`;

                document.getElementById('header').innerHTML =
                    `
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
                    `;
            }

            tabs.forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    loadContent(this.getAttribute('data-label'));
                });
            });

            tabs[0].classList.add('active');
            loadContent(tabs[0].getAttribute('data-label'));
        });

        document.addEventListener("DOMContentLoaded", function() {
            const tabContainer = document.getElementById('mobileTabScroll');
            const scrollLeftBtn = document.getElementById('scrollLeftBtn');
            const scrollRightBtn = document.getElementById('scrollRightBtn');

            function updateArrowVisibility() {
                if (!tabContainer) return;

                scrollLeftBtn.classList.toggle('d-none', tabContainer.scrollLeft === 0);
                scrollRightBtn.classList.toggle('d-none', tabContainer.scrollLeft + tabContainer.clientWidth >= tabContainer.scrollWidth);
            }

            scrollLeftBtn.addEventListener('click', () => {
                tabContainer.scrollBy({
                    left: -100,
                    behavior: 'smooth'
                });
            });

            scrollRightBtn.addEventListener('click', () => {
                tabContainer.scrollBy({
                    left: 100,
                    behavior: 'smooth'
                });
            });

            tabContainer.addEventListener('scroll', updateArrowVisibility);

            updateArrowVisibility(); // Initial check
        });


        const dropdownHeader = document.getElementById('dropdownHeader');
        const dropdownContent = document.getElementById('mobileCourseCard');
        const courseCard = dropdownHeader.closest('.course-card'); // find the parent card

        // Show dropdown
        dropdownContent.addEventListener('show.bs.collapse', function() {
            dropdownHeader.style.borderBottom = '1px solid var(--black)';
            courseCard.style.borderBottomLeftRadius = '10px';
            courseCard.style.borderBottomRightRadius = '10px';
        });

        // Hide dropdown
        dropdownContent.addEventListener('hide.bs.collapse', function() {
            dropdownHeader.style.borderBottom = 'none';
            courseCard.style.borderBottomLeftRadius = '0';
            courseCard.style.borderBottomRightRadius = '0';
        });

        var navLeaderboards = document.querySelectorAll('.nav-leaderboard');
        var isActive = false;

        navLeaderboards.forEach(function(navLeaderboard) {
            navLeaderboard.addEventListener("click", function() {
                document.getElementById('header').innerHTML =
                    `
                <div class="container-fluid">
                    <div class="row align-items-center justify-content-center flex-column flex-md-row">
                        <div class="col-8 col-sm-6 col-md-12 col-lg-6 d-flex search-container mb-2 mb-lg-0">
                            <input type="text" placeholder="Search classmates" class="form-control py-1 text-reg text-lg-12 text-14">
                            <button type="button" class="btn btn-outline-secondary ms-2">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                        <div class="col-6 d-flex justify-content-center justify-content-lg-start align-items-center">
                            <span class="dropdown-label me-2">View by:</span>
                            <button class="btn dropdown-toggle dropdown-custom" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span>Weekly</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item text-reg" href="#">Monthly</a></li>
                                <li><a class="dropdown-item text-reg" href="#">Weekly</a></li>
                                <li><a class="dropdown-item text-reg" href="#">Daily</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                    `;
            });
        });
    </script>
</body>

</html>