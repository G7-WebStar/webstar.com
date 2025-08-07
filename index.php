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
                                        <div class="card p-4 left-card">

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
                                                 <!-- Not final, for pill muna -->
                                                <div class="card mb-3" style="border-radius: 12px; border: 1px solid rgba(44, 44, 44, 1); padding: 15px;">
                                                    <div class="d-flex align-items-center">
                                                        <img src="https://i.pinimg.com/736x/e5/68/9b/e5689b2b0dac5d6b95d454da950e0eab.jpg" style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover; margin-right: 10px;">

                                                        <div class="d-flex flex-column justify-content-center">
                                                            <div class="d-flex align-items-center">
                                                                <span class="text-sbold text-16">Prof Name</span>
                                                                <span class="text-reg text-12 badge rounded-pill ms-2 course-badge">COMP-006</span>
                                                            </div>
                                                            <span class="text-med text-12">Date and Time</span>
                                                        </div>
                                                        <div style="margin-left: auto; margin-right: 10px;">
                                                            <i class="fa-solid fa-arrow-right text-reg text-12" style="color: var(--black);"></i>
                                                        </div>
                                                    </div>

                                                    <!-- <div class="px-5 py-3 text-reg text-16">Short announcement details or title goes here. This can be a brief summary.</div> -->
                                                </div>

                                                <!-- Card 2 -->
                                                <div class="card mb-3" style="border: 1px solid rgba(44, 44, 44, 1); border-radius: 15px; padding: 15px;">
                                                    <div class="text-sbold text-16 mb-1">[Announcement]</div>
                                                    <div class="text-reg text-14">Another announcement detail here with slightly different content for variety.</div>
                                                </div>

                                                <!-- Card 3 -->
                                                <div class="card mb-3" style="border: 1px solid rgba(44, 44, 44, 1); border-radius: 15px; padding: 15px;">
                                                    <div class="text-sbold text-16 mb-1">[Announcement]</div>
                                                    <div class="text-reg text-14">More announcement text that could wrap into two or more lines depending on width.</div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Right side -->
                        </div>
                    </div> <!-- End here -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

</body>


</html>