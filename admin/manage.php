<?php $activePage = 'manage'; ?>
<?php
include('../shared/assets/database/connect.php');
// include("../shared/assets/processes/prof-session-process.php");

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Manage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/course.css">
    <link rel="stylesheet" href="../shared/assets/css/admin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="icon" type="image/png" href="../shared/assets/img/webstar-icon.png">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />

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

                    <!-- Left side -->
                    <div class="container-fluid py-1">
                        <div class="row">
                            <!-- Header Title -->
                            <div class="col-12 mb-3">
                                <div class="d-flex align-items-center ps-4">
                                    <div class="text-sbold text-22">Manage instructors</div>
                                    <img src="../shared/assets/img/explore.png" alt="Folder"
                                        class="img-fluid rounded-circle ms-3 folder-img" width="26" height="26">
                                    <div class="stats-count text-22 text-bold ms-1">1</div>
                                </div>
                            </div>

                            <!-- Header Section (Search, Sort, Status, Add Button) -->
                            <div class="row align-items-center g-2 flex-wrap px-2">
                                <!-- Search -->
                                <div class="col-12 col-lg-4 px-0 px-md-auto">
                                    <div class="search-container d-flex mx-sm-auto">
                                        <form method="GET" class="form-control bg-transparent border-0">
                                            <input type="text" placeholder="Search" name="search"
                                                class="form-control py-1 text-reg text-14">
                                            <button type="submit" class="btn-outline-secondary">
                                                <i class="bi bi-search me-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Sort By -->
                                <div class="col-auto ms-2">
                                    <div class="d-flex align-items-center flex-nowrap">
                                        <span class="dropdown-label me-2 text-reg">Sort by:</span>
                                        <button class="btn dropdown-toggle dropdown-custom" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="text-reg text-14">Name</span>
                                        </button>
                                        <ul class="dropdown-menu text-reg text-14">
                                            <li><a class="dropdown-item text-reg text-14" href="#">Name</a></li>
                                            <li><a class="dropdown-item text-reg text-14" href="#">Date</a></li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="col-auto ms-3">
                                    <div class="d-flex align-items-center flex-nowrap">
                                        <span class="dropdown-label me-2 text-reg">Status:</span>
                                        <button class="btn dropdown-toggle dropdown-custom" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="text-reg text-14">Active</span>
                                        </button>
                                        <ul class="dropdown-menu text-reg text-14">
                                            <li><a class="dropdown-item text-reg text-14" href="#">Active</a></li>
                                            <li><a class="dropdown-item text-reg text-14" href="#">Archived</a></li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Add Course Button -->
                                <div class="col-auto d-none d-lg-block ms-auto">
                                    <button class="add-course-btn btn btn-primary px-3 py-1 rounded-pill text-reg text-md-14">
                                        <div style="text-decoration: none; color: black;">
                                            + Register new instructor
                                        </div>
                                    </button>
                                </div>

                                <!-- Mobile Button -->
                                <div class="col-12 d-lg-none d-flex justify-content-center mt-2">
                                    <button class="add-course-btn btn btn-primary px-3 py-1 rounded-pill text-reg text-md-14">
                                        <div style="text-decoration: none; color: black;">
                                            + Register new instructor
                                        </div>
                                    </button>
                                </div>
                            </div>

                            <!-- Table Section -->
                            <div class="table-container mt-4 px-3" style="max-height: 400px; overflow-y: auto;">
                                <div class="table-responsive-sm">
                                    <table class="custom-table align-middle mb-0 text-med text-14">
                                        <thead>
                                            <tr>
                                                <th scope="col">Name</th>
                                                <th scope="col">Username</th>
                                                <th scope="col">Email</th>
                                                <th scope="col">Status</th>
                                                <th scope="col">Date Created</th>
                                                <th scope="col">Date Updated</th>
                                                <th scope="col" class="text-end"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Mark Otto</td>
                                                <td>-</td>
                                                <td>mark@example.com</td>
                                                <td>Active</td>
                                                <td>10-21-25 00:11:11</td>
                                                <td>10-21-25 00:11:11</td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-link text-dark p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="bi bi-three-dots-vertical fs-5"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li><a class="dropdown-item" href="#">Edit</a></li>
                                                            <li><a class="dropdown-item" href="#">View Profile</a></li>
                                                            <li><a class="dropdown-item" href="#">Delete</a></li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Jacob Thornton</td>
                                                <td>-</td>
                                                <td>jacob@example.com</td>
                                                <td>Created</td>
                                                <td>10-21-25 00:11:11</td>
                                                <td>10-21-25 00:11:11</td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-link text-dark p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="bi bi-three-dots-vertical fs-5"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li><a class="dropdown-item" href="#">Edit</a></li>
                                                            <li><a class="dropdown-item" href="#">View Profile</a></li>
                                                            <li><a class="dropdown-item" href="#">Delete</a></li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>John Doe</td>
                                                <td>-</td>
                                                <td>john@example.com</td>
                                                <td>Inactive</td>
                                                <td>10-21-25 00:11:11</td>
                                                <td>10-21-25 00:11:11</td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-link text-dark p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="bi bi-three-dots-vertical fs-5"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li><a class="dropdown-item" href="#">Edit</a></li>
                                                            <li><a class="dropdown-item" href="#">View Profile</a></li>
                                                            <li><a class="dropdown-item" href="#">Delete</a></li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>


                        </div>
                    </div>

                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

</body>


</html>