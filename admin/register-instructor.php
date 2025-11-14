<?php $activePage = 'manage'; ?>
<?php
include('../shared/assets/database/connect.php');
include("../shared/assets/processes/admin-session-process.php");

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/course.css">
    <link rel="stylesheet" href="../shared/assets/css/admin.css">
    <link rel="stylesheet" href="../shared/assets/css/registration.css">
    <link rel="stylesheet" href="../shared/assets/css/registration-profile.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="icon" type="image/png" href="../shared/assets/img/webstar-icon.png">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0" rel="stylesheet" />

</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-0 p-md-3"
        style="background-color: var(--black);">

        <div class="row w-100">

            <!-- Sidebar (only shows on mobile) -->
            <?php include '../shared/components/admin-sidebar-for-mobile.php'; ?>

            <!-- Sidebar Column (fixed on desktop) -->
            <?php include '../shared/components/admin-sidebar-for-desktop.php'; ?>

            <!-- Main Container Column-->
            <div class="col main-container m-0 p-0 mx-0 mx-md-2 p-0 p-md-4 overflow-y-auto">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">

                    <!-- Navbar for mobile -->
                    <?php include '../shared/components/admin-navbar-for-mobile.php'; ?>


                    <div class="container-fluid py-1">
                        <div class="row">
                            <!-- Header Title -->
                            <div class="col-12 mb-1">
                                <div class="d-flex align-items-center ps-1">
                                    <a href="manage.php" style="text-decoration: none; color: inherit; line-height: 1;">
                                        <span class="material-symbols-outlined me-2">
                                            arrow_left_alt
                                        </span>
                                    </a>
                                    <div class="text-sbold text-22 ms-1">Register new instructor</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Registration Form -->
                    <div class="container p-4 d-flex">
                        <form method="POST" action="" class="w-100 custom-form" id="registration-instructor-form">
                            <div class="row" style="max-width: 900px;">

                                <!-- Left: Registration Form -->
                                <div class="col-12 col-md-6">
                                    <div>
                                        <div class="mb-3 text-med text-16 text-center text-md-start" style="color: var(--black);">
                                            Basic Information
                                        </div>

                                        <!-- First & Middle Name -->
                                        <div class="row mb-3 gx-3">
                                            <div class="col">
                                                <div class="form-floating">
                                                    <input type="text" name="firstName" class="form-control" id="firstName" placeholder=" ">
                                                    <label for="firstName">First Name</label>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-floating">
                                                    <input type="text" name="middleName" class="form-control" id="middleName" placeholder=" ">
                                                    <label for="middleName">Middle Name</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Last Name -->
                                        <div class="row mb-4 gx-3">
                                            <div class="col">
                                                <div class="form-floating">
                                                    <input type="text" name="lastName" class="form-control" id="lastName" placeholder="Last Name">
                                                    <label for="lastName">Last Name</label>
                                                </div>
                                            </div>
                                            <div class="col"></div>
                                        </div>

                                        <div class="mb-3 mt-3 text-med text-16 text-center text-md-start" style="color: var(--black);">
                                            Login Information
                                        </div>

                                        <!-- Temporary Username -->
                                        <div class="mb-3">
                                            <div class="form-floating">
                                                <input type="text" name="tempUsernameID" class="form-control" id="tempUsernameID" placeholder="Temporary Username">
                                                <label for="studentNo">Temporary Username</label>
                                            </div>
                                            <div class="text-danger small mt-1 ps-2">
                                                Username has already been taken.
                                            </div>
                                        </div>

                                        <!-- Temporary Password -->
                                        <div class="mb-3">
                                            <div class="form-floating">
                                                <input type="text" name="tempPasswordID" class="form-control" id="tempPasswordID" placeholder="Temporary Password">
                                                <label for="studentNo">Temporary Password</label>
                                            </div>
                                        </div>

                                        <!-- Register Button  -->
                                        <div class="text-center d-flex flex-column align-items-end justify-content-end">
                                            <button type="submit" name="registerBtn" class="text-sbold text-12 btn btn-finish mt-3 mb-3">
                                                Register
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

</body>


</html>