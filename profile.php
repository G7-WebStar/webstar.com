<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@jamesdoe's Webstar Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="shared/assets/css/profile.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="shared/assets/img/webstar-icon.png">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0"
        rel="stylesheet" />

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
                    <nav class="navbar navbar-light px-3 d-md-none">
                        <div class="container-fluid position-relative">

                            <!-- Toggler -->
                            <button class="navbar-toggler position-absolute start-0 p-1" type="button"
                                data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas">
                                <span class="navbar-toggler-icon"></span>
                            </button>

                            <!-- Logo -->
                            <a class="navbar-brand mx-auto" href="#">
                                <img src="shared/assets/img/webstar-logo-black.png" alt="Webstar"
                                    style="height: 40px; padding-left: 30px">
                            </a>

                        </div>
                    </nav>

                    <div class="container-fluid py-3 overflow-y-auto">
                        <div class="row g-0 w-100">

                            <!-- First Column -->
                            <div class="col-12 col-md-4 first-column d-flex flex-column"
                                style="position: sticky; top: 0px; z-index: 5; align-self: flex-start; height: fit-content;">

                                <!-- Profile -->
                                <div class="row m-0 w-100">
                                    <div class="col m-0 p-0 ">
                                        <div class="card profile rounded-4 me-md-2"
                                            style="border: 1px solid var(--black);">
                                            <!-- General Info -->
                                            <div class="row m-0 pb-md-3 d-flex align-items-center">
                                                <div class="cover-photo"></div>

                                                <!-- Profile Block -->
                                                <div class="profile-block px-4">
                                                    <div class="profile-pic"></div>
                                                    <div class="profile-text mt-3">
                                                        <!-- Name and Username -->
                                                        <div class="div">
                                                            <div class="user-name text-bold">Christian James D. Torrillo
                                                            </div>
                                                            <div class="user-username text-med text-muted">@jamesdoe
                                                            </div>
                                                            <!-- Bio -->
                                                            <div class="bio mt-4">
                                                                <div class="text-med text-14">Hi! I’m a web development
                                                                    student passionate about building clean, functional,
                                                                    and user-friendly websites. Always learning, always
                                                                    building! ⭐
                                                                </div>
                                                            </div>
                                                            <!-- Stats -->
                                                            <div class="stats mt-4">
                                                                <div
                                                                    class="d-flex justify-content-between align-items-center text-center">
                                                                    <div class="flex-fill text-center mx-1">
                                                                        <span class="text-16 text-bold">340</span>
                                                                        <small
                                                                            class="text-med text-muted">badges</small>
                                                                    </div>

                                                                    <div class="flex-fill text-center mx-1">
                                                                        <span class="text-bold">340</span>
                                                                        <small class="text-med text-muted">
                                                                            courses</small>
                                                                    </div>

                                                                    <div class="flex-fill text-center mx-1">
                                                                        <span class="text-bold">340</span>
                                                                        <small
                                                                            class="text-med text-muted">webstars</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- Send an Email -->
                                                            <div
                                                                class="d-flex justify-content-center align-items-center mt-4">
                                                                <div class="btn d-flex align-items-center justify-content-center gap-2 rounded-3 text-sbold text-14 m-0"
                                                                    style="background-color: var(--primaryColor); border: 1px solid var(--black); width: 100%;">
                                                                    <span class="material-symbols-rounded">mail</span>
                                                                    <span>Email</span>
                                                                </div>
                                                            </div>
                                                            <!-- Socials -->
                                                            <div class="d-flex justify-content-center align-items-center gap-4 mt-3 text-20 mb-3"
                                                                style="color: var(--black);">
                                                                <a href="#" style="color: inherit;"><i
                                                                        class="fab fa-facebook"></i></a>
                                                                <a href="#" style="color: inherit;"><i
                                                                        class="fab fa-github"></i></a>
                                                                <a href="#" style="color: inherit;"><i
                                                                        class="fab fa-linkedin"></i></a>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- Second Column -->
                            <div class="col-12 col-md-4 m-0 second-column">
                                <!-- My Emblem Row-->
                                <div class="row m-0 w-100 mb-2">
                                    <!-- My Emblem Col-->
                                    <div class="col m-0 p-0 ">
                                        <!-- My Emblem Card-->
                                        <div class="card second rounded-4 p-4 me-md-2"
                                            style="border: 1px solid var(--black);">
                                            <!-- My Emblem Header-->
                                            <div class="d-flex align-items-center" style="margin-bottom:150px">
                                                <span class="material-symbols-rounded me-2">
                                                    favorite
                                                </span>
                                                <span class="text-sbold">My Emblem</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- My Badges Row -->
                                <div class="row m-0 w-100 mb-2">
                                    <!-- My Badges Col-->
                                    <div class="col m-0 p-0 ">
                                        <!-- My Badges Card-->
                                        <div class="card second rounded-4 p-4 me-md-2"
                                            style="border: 1px solid var(--black);">
                                            <!-- My Badges Header-->
                                            <div class="d-flex align-items-center" style="margin-bottom:150px">
                                                <span class="material-symbols-rounded me-2">
                                                    editor_choice
                                                </span>
                                                <span class="text-sbold">My Badges</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- My Courses Row -->
                                <div class="row m-0 w-100">
                                    <!-- My Courses Col-->
                                    <div class="col m-0 p-0 ">
                                        <!-- My Courses Card-->
                                        <div class="card second rounded-4 p-4 me-md-2"
                                            style="border: 1px solid var(--black);">
                                            <!-- My Courses Header-->
                                            <div class="d-flex align-items-center" style="margin-bottom:150px">
                                                <span class="material-symbols-rounded me-2">
                                                    folder
                                                </span>
                                                <span class="text-sbold">My Courses</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Third Column -->
                            <div class="col-12 col-md-4 m-0 third-column">
                                <!-- My Star Card Row-->
                                <div class="row m-0 w-100 mb-2">
                                    <!-- My Star Card Col-->
                                    <div class="col m-0 p-0 ">
                                        <!-- My Star Card Card-->
                                        <div class="card second rounded-4 p-4 me-md-2"
                                            style="border: 1px solid var(--black);">
                                            <!-- My Star Card Header-->
                                            <div class="d-flex align-items-center" style="margin-bottom:150px">
                                                <span class="material-symbols-rounded me-2">
                                                    kid_star
                                                </span>
                                                <span class="text-sbold">My Star Card</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Recent Activity Row -->
                                <div class="row m-0 w-100 mb-2">
                                    <!-- Recent Activity Col-->
                                    <div class="col m-0 p-0 ">
                                        <!-- Recent Activity Card-->
                                        <div class="card second rounded-4 p-4 me-md-2"
                                            style="border: 1px solid var(--black);">
                                            <!-- Recent Activity Header-->
                                            <div class="d-flex align-items-center" style="margin-bottom:150px">
                                                <span class="material-symbols-rounded me-2">
                                                    bolt
                                                </span>
                                                <span class="text-sbold">Recent Activity</span>
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