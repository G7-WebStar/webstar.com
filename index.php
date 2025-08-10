<?php $activePage = 'home'; ?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="shared/assets/css/index.css">
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

                    <div class="container-fluid py-1 overflow-y-auto">
                        <div class="row">
                            <!-- PUT CONTENT HERE -->
                            <!-- left side -->
                            <div class="col-12 col-sm-12 col-md-7">
                                <div class="row ps-4">
                                    <div class="col-12">
                                        <div class="text-sbold text-22">Welcome back,
                                            James!
                                        </div>
                                        <div class="text-reg text-16">Pick up where you left off and keep
                                            building you skills.
                                        </div>
                                    </div>
                                </div>
                                <!-- Another row for foldering -->
                                <div class="row pt-1 text-sbold text-18">
                                    <div class="col pt-3">
                                        <!-- Main Card -->
                                        <div class="card left-card">

                                            <!-- Top Header -->
                                            <div class="p-4 pb-0 d-flex justify-content-between align-items-center mb-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-folder" style="color: var(--black); font-size: 20px; width: 26px; margin-right: 5px;"></i>
                                                    <span>Your Courses</span>
                                                </div>
                                                <div>5</div>
                                            </div>
                                            <!-- Scrollable course -->
                                            <div class="ps-4 pb-4" style="overflow-x: auto; white-space: nowrap; scrollbar-width: none; -ms-overflow-style: none; scroll-behavior: smooth;">
                                                <div style="display: inline-flex; gap: 12px;">
                                                    <!-- Card 1 -->
                                                    <div class="card custom-course-card">
                                                        <img src="shared/assets/img/home/webdev.jpg" class="card-img-top" alt="...">
                                                        <div class="card-body px-3 py-2">
                                                            <div class="text-sbold text-16">COMP–006</div>
                                                            <p class="text-reg text-14 mb-0">Web Development</p>
                                                        </div>
                                                    </div>
                                                    <!-- Card 2 -->
                                                    <div class="card custom-course-card">
                                                        <img src="shared/assets/img/home/webdev.jpg" class="card-img-top" alt="...">
                                                        <div class="card-body px-3 py-2">
                                                            <div class="text-sbold text-16">COMP–006</div>
                                                            <p class="text-reg text-14 mb-0">Web Development</p>
                                                        </div>
                                                    </div>
                                                    <!-- Card 3 -->
                                                    <div class="card custom-course-card">
                                                        <img src="shared/assets/img/home/webdev.jpg" class="card-img-top" alt="...">
                                                        <div class="card-body px-3 py-2">
                                                            <div class="text-sbold text-16">COMP–006</div>
                                                            <p class="text-reg text-14 mb-0">Web Development</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Another row for Announcement -->
                                <div class="row pt-1 text-sbold text-18">
                                    <div class="col pt-3">
                                        <!-- Main Card -->
                                        <div class="card p-4 mb-4 left-card">

                                            <!-- Top Header  -->
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="fa-solid fa-bullhorn" style="color: var(--black); font-size: 20px; width: 26px; margin-right: 5px;"></i>
                                                    <span>Recent Announcements</span>
                                                </div>
                                            </div>
                                            <!-- Scrollable Card List -->
                                            <div style="max-height: 200px; overflow-y: auto; padding-right: 5px; scroll-behavior: smooth; scrollbar-width: none; -ms-overflow-style: none;">
                                                <!-- Card 1 -->
                                                <div class="card mb-3" style="border-radius: 12px; border: 1px solid rgba(44, 44, 44, 1); padding: 15px;">
                                                    <div class="announcement-card d-flex align-items-start mb-3">
                                                        <!-- Instructor Image -->
                                                        <div class="flex-shrink-0 me-3">
                                                            <img src="shared/assets/img/courseInfo/prof.png" alt="Instructor Image"
                                                                style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">
                                                        </div>

                                                        <!-- Text Content -->
                                                        <div class="prof-header text-start">
                                                            <div class="prof-info text-reg text-12" style="color: var(--black); line-height: 140%; position: relative;">
                                                                <div class="main-row d-flex align-items-center justify-content-between flex-wrap">
                                                                    <div class="d-flex align-items-center name-badge">
                                                                        <strong>Prof. Christian James</strong>
                                                                        <span class="text-reg text-12 badge rounded-pill ms-2 courses-badge">COMP-006</span>
                                                                    </div>
                                                                    <i class="announcement-arrow ms-auto pe-2 fa-solid fa-arrow-right text-reg text-12 "
                                                                        style="color: var(--black);"></i>
                                                                </div>
                                                                <div class="date-row d-flex justify-content-between align-items-center mt-1" style="position: relative;">
                                                                    <span style="font-weight: normal;">January 12, 2024 8:00AM</span>
                                                                </div>
                                                            </div>

                                                            <p class="announcement-text mb-0 mt-3 text-reg text-12" style="color: var(--black); line-height: 140%;">
                                                                Welcome to our course! Please make sure to check the Course Overview
                                                                under the “Lessons” tab before our first face-to-face session this week.
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>


                                                <!-- Card 2 -->
                                                <div class="card mb-3" style="border-radius: 12px; border: 1px solid rgba(44, 44, 44, 1); padding: 15px;">
                                                    <div class="announcement-card d-flex align-items-start mb-3">
                                                        <!-- Instructor Image -->
                                                        <div class="flex-shrink-0 me-3">
                                                            <img src="shared/assets/img/courseInfo/prof.png" alt="Instructor Image"
                                                                style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">
                                                        </div>

                                                        <!-- Text Content -->
                                                        <div class="prof-header text-start">
                                                            <div class="prof-info text-reg text-12" style="color: var(--black); line-height: 140%; position: relative;">
                                                                <div class="main-row d-flex align-items-center justify-content-between flex-wrap">
                                                                    <div class="name-badge d-flex align-items-center">
                                                                        <strong>Prof. Christian James</strong>
                                                                        <span class="text-reg text-12 badge rounded-pill ms-2 courses-badge">COMP-006</span>
                                                                    </div>
                                                                    <i class="announcement-arrow ms-auto pe-2 fa-solid fa-arrow-right text-reg text-12 "
                                                                        style="color: var(--black);"></i>
                                                                </div>
                                                                <div class="date-row d-flex justify-content-between align-items-center mt-1" style="position: relative;">
                                                                    <span style="font-weight: normal;">January 12, 2024 8:00AM</span>
                                                                </div>
                                                            </div>

                                                            <p class="announcement-text mb-0 mt-3 text-reg text-12" style="color: var(--black); line-height: 140%;">
                                                                Welcome to our course! Please make sure to check the Course Overview
                                                                under the “Lessons” tab before our first face-to-face session this week.
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Card 3 -->
                                                <div class="card mb-3" style="border-radius: 12px; border: 1px solid rgba(44, 44, 44, 1); padding: 15px;">
                                                    <div class="announcement-card d-flex align-items-start mb-3">
                                                        <!-- Instructor Image -->
                                                        <div class="flex-shrink-0 me-3">
                                                            <img src="shared/assets/img/courseInfo/prof.png" alt="Instructor Image"
                                                                style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">
                                                        </div>

                                                        <!-- Text Content -->
                                                        <div class="prof-header text-start">
                                                            <div class="prof-info text-reg text-12" style="color: var(--black); line-height: 140%; position: relative;">
                                                                <div class="main-row d-flex align-items-center justify-content-between flex-wrap">
                                                                    <div class="name-badge d-flex align-items-center">
                                                                        <strong>Prof. Christian James</strong>
                                                                        <span class="text-reg text-12 badge rounded-pill ms-2 courses-badge">COMP-006</span>
                                                                    </div>
                                                                    <i class="announcement-arrow ms-auto pe-2 fa-solid fa-arrow-right text-reg text-12 "
                                                                        style="color: var(--black);"></i>
                                                                </div>
                                                                <div class="date-row d-flex justify-content-between align-items-center mt-1" style="position: relative;">
                                                                    <span style="font-weight: normal;">January 12, 2024 8:00AM</span>
                                                                </div>
                                                            </div>

                                                            <p class="announcement-text mb-0 mt-3 text-reg text-12" style="color: var(--black); line-height: 140%;">
                                                                Welcome to our course! Please make sure to check the Course Overview
                                                                under the “Lessons” tab before our first face-to-face session this week.
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Right side -->
                            <div class="col-12 col-sm-12 col-md-5">
                                <div class="row text-sbold text-18">
                                    <div class="col">
                                        <div class="card p-4 mb-3 left-card">

                                            <!-- Top Header -->
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div class="d-flex align-items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        width="20" height="20"
                                                        fill="currentColor"
                                                        class="bi bi-arrow-down-right-square-fill"
                                                        viewBox="0 0 16 16"
                                                        style="color: var(--black); width: 26px; margin-right: 5px;">
                                                        <path d="M14 16a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12zM5.904 5.197 10 9.293V6.525a.5.5 0 0 1 1 0V10.5a.5.5 0 0 1-.5.5H6.525a.5.5 0 0 1 0-1h2.768L5.197 5.904a.5.5 0 0 1 .707-.707z" />
                                                    </svg>

                                                    <span>Upcoming</span>
                                                </div>
                                                <div>21</div>
                                            </div>
                                            <!-- Scrollable course -->
                                            <div style="max-height: 230px; overflow-y: auto; padding-right: 5px; scroll-behavior: smooth; scrollbar-width: none; -ms-overflow-style: none;">
                                                <!-- Card 1 -->
                                                <div class="todo-card mb-2 d-flex align-items-stretch" style="min-height: 60px;">
                                                    <div class="date d-flex align-items-center justify-content-center text-sbold text-20"
                                                        style="min-width: 100px;">
                                                        SEP 9
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="flex-grow-1 px-3 py-0">
                                                            <div class="text-sbold text-16">Activity #1</div>
                                                            <div class="text-reg text-12">COMP-006</div>
                                                        </div>
                                                        <div class="d-flex align-items-center">
                                                            <div class="course-badge rounded-pill px-3 text-reg text-12" style="margin-left: auto; margin-right: 11px;">Task</div>
                                                            <i class="fa-solid fa-arrow-right text-reg text-12" style="color: var(--black);"></i>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Card 2 -->
                                                <div class="todo-card mb-2 d-flex align-items-stretch" style="min-height: 60px;">
                                                    <div class="date d-flex align-items-center justify-content-center text-sbold text-20"
                                                        style="min-width: 100px;">
                                                        SEP 9
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="flex-grow-1 px-3 py-0">
                                                            <div class="text-sbold text-16">Activity #1</div>
                                                            <div class="text-reg text-12">COMP-006</div>
                                                        </div>
                                                        <div class="d-flex align-items-center">
                                                            <div class="course-badge rounded-pill px-3 text-reg text-12" style="margin-left: auto; margin-right: 11px;">Task</div>
                                                            <i class="fa-solid fa-arrow-right text-reg text-12" style="color: var(--black);"></i>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Card 3 -->
                                                <div class="todo-card mb-3 d-flex align-items-stretch" style="min-height: 60px;">
                                                    <div class="date d-flex align-items-center justify-content-center text-sbold text-20"
                                                        style="min-width: 100px;">
                                                        SEP 9
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="flex-grow-1 px-3 py-0">
                                                            <div class="text-sbold text-16">Activity #1</div>
                                                            <div class="text-reg text-12">COMP-006</div>
                                                        </div>
                                                        <div class="d-flex align-items-center">
                                                            <div class="course-badge rounded-pill px-3 text-reg text-12" style="margin-left: auto; margin-right: 11px;">Task</div>
                                                            <i class="fa-solid fa-arrow-right text-reg text-12" style="color: var(--black);"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div style="display:flex; justify-content: flex-end; align-items: center; gap:6px; margin-right: 10px;">
                                                    <span class="text-reg text-12" style="color: var(--black);">View More</span>
                                                    <i class="fa-solid fa-arrow-right text-reg text-12" style="color: var(--black);"></i>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Another row for leaderboard -->
                                <div class="row text-sbold text-18">
                                    <div class="col">
                                        <div class="card p-4 mb-3 left-card">

                                            <!-- Top Header -->
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="fa-solid fa-ranking-star" style="color: var(--black); font-size: 22px; width: 26px; margin-right: 10px;"></i>
                                                    <span>Lederboard Rank</span>
                                                </div>
                                                <div class="d-flex align-items-center flex-nowrap">
                                                    <button
                                                        class="btn text-reg dropdown-toggle d-flex justify-content-between align-items-center fs-6 fs-lg-5 dropdown-custom"
                                                        style="opacity: 1;"
                                                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <span class="text-reg text-14">Weekly</span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item text-reg" href="#">Weekly</a></li>
                                                        <li><a class="dropdown-item text-reg" href="#">All-time</a></li>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <!-- Scrollable leaderboard -->
                                            <div style="max-height: 240px; overflow-y: auto; padding-right: 5px; display: flex; flex-wrap: wrap; gap: 8px; scroll-behavior: smooth; scrollbar-width: none; -ms-overflow-style: none;">
                                                <!-- Card 1 -->
                                                <div class="card custom-leaderboard-card">
                                                    <div class="card-body p-4">
                                                        <div style="display: inline-flex; align-items: center;">
                                                            <span class="rank-number text-bold text-18">11</span>
                                                            <span class="text-reg text-12 badge rounded-pill ms-2 learderboard-badge" style="display: inline-flex; align-items: center; gap: 4px;">
                                                                <i class="fa-solid fa-caret-up"></i>
                                                                2
                                                            </span>
                                                        </div>

                                                        <!-- NEW WRAPPER -->
                                                        <div class="info-block">
                                                            <div class="comp-code text-sbold text-16">COMP–006</div>
                                                            <div class="subj-code text-reg text-12 mb-0">Web Development</div>

                                                            <div class="xp-container">
                                                                <div class="xp-block text-reg text-12 mb-0">3160 XPs</div>
                                                                <div class="xp-arrow">
                                                                    <i class="fa-solid fa-arrow-right text-reg text-12" style="color: var(--black);"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="card custom-leaderboard-card">
                                                    <div class="card-body p-4">
                                                        <div style="display: inline-flex; align-items: center;">
                                                            <span class="rank-number text-bold text-18">10</span>
                                                            <span class="text-reg text-12 badge rounded-pill ms-2 learderboard-badge" style="display: inline-flex; align-items: center; gap: 4px;">
                                                                <i class="fa-solid fa-caret-up"></i>
                                                                2
                                                            </span>
                                                        </div>

                                                        <div class="info-block">
                                                            <div class="comp-code text-sbold text-16">COMP–006</div>
                                                            <div class="subj-code text-reg text-12 mb-0">Web Development</div>

                                                            <div class="xp-container">
                                                                <div class="xp-block text-reg text-12 mb-0">3160 XPs</div>
                                                                <div class="xp-arrow">
                                                                    <i class="fa-solid fa-arrow-right text-reg text-12" style="color: var(--black);"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="card custom-leaderboard-card">
                                                    <div class="card-body p-4">
                                                        <div style="display: inline-flex; align-items: center;">
                                                            <span class="rank-number text-bold text-18">21</span>
                                                            <span class="text-reg text-12 badge rounded-pill ms-2 learderboard-badge" style="display: inline-flex; align-items: center; gap: 4px;">
                                                                <i class="fa-solid fa-caret-up"></i>
                                                                2
                                                            </span>
                                                        </div>

                                                        <div class="info-block">
                                                            <div class="comp-code text-sbold text-16">COMP–006</div>
                                                            <div class="subj-code text-reg text-12 mb-0">Web Development</div>

                                                            <div class="xp-container">
                                                                <div class="xp-block text-reg text-12 mb-0">3160 XPs</div>
                                                                <div class="xp-arrow">
                                                                    <i class="fa-solid fa-arrow-right text-reg text-12" style="color: var(--black);"></i>
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
                </div> <!-- End here -->
            </div>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

</body>


</html>