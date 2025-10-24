<?php $activePage = 'feedback'; ?>
<?php
include('../shared/assets/database/connect.php');
// include("../shared/assets/processes/prof-session-process.php");

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Feedback</title>
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
                            <div class="col-12 mb-3">
                                <div class="d-flex align-items-center ps-2">
                                    <div class="text-sbold text-22">Feedbacks</div>
                                    <span class="material-symbols-outlined ms-4" style="font-size: 30px;">
                                        feedback
                                    </span>
                                    <div class="stats-count text-22 text-bold ms-1">2</div>
                                </div>
                            </div>

                            <!-- Header Section -->
                            <div class="row align-items-center g-2 flex-wrap px-2">
                                <!-- Sort By -->
                                <div class="col-auto ms-3 mobile-dropdown">
                                    <div class="d-flex align-items-center flex-nowrap">
                                        <span class="dropdown-label me-2 text-reg">Sort by</span>
                                        <div class="custom-dropdown">
                                            <button class="dropdown-btn text-reg text-14">Newest</button>
                                            <ul class="dropdown-list text-reg text-14">
                                                <li data-value="Newest">Newest</li>
                                                <li data-value="Oldest">Oldest</li>
                                                <li data-value="Unread">Unread</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="col-auto ms-3 mobile-dropdown">
                                    <div class="d-flex align-items-center flex-nowrap">
                                        <span class="dropdown-label me-2 text-reg">Status</span>
                                        <div class="custom-dropdown">
                                            <button class="dropdown-btn text-reg text-14">All</button>
                                            <ul class="dropdown-list text-reg text-14">
                                                <li data-value="Active">Active</li>
                                                <li data-value="Created">Created</li>
                                                <li data-value="Inactive">Inactive</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Role -->
                                <div class="col-auto ms-3 mobile-dropdown">
                                    <div class="d-flex align-items-center flex-nowrap">
                                        <span class="dropdown-label me-2 text-reg">Role</span>
                                        <div class="custom-dropdown">
                                            <button class="dropdown-btn text-reg text-14">All</button>
                                            <ul class="dropdown-list text-reg text-14">
                                                <li data-value="Student">Student</li>
                                                <li data-value="Professor">Professor</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Add Course Button -->
                                <div class="col-auto d-none d-lg-flex align-items-center ms-auto">
                                    <span class="me-3 text-reg text-md-14">1 selected</span>
                                    <button class="add-course-btn btn btn-primary px-3 py-1 rounded-pill text-reg text-md-14">
                                        <div style="text-decoration: none; color: var(--black);">
                                            Mark as reviewed
                                        </div>
                                    </button>
                                </div>

                                <!-- Mobile Button -->
                                <div class="col-12 d-lg-none d-flex justify-content-center align-items-center mt-2">
                                    <span class="me-3 text-reg text-md-14">1 selected</span>
                                    <button class="add-course-btn btn btn-primary px-3 py-1 rounded-pill text-reg text-md-14">
                                        <div style="text-decoration: none; color: black;">
                                            Mark as reviewed
                                        </div>
                                    </button>
                                </div>

                                <!-- Table Section -->
                                <div class="table-container mt-4 px-3" style="max-height: 400px; overflow-y: auto;">
                                    <div class="table-responsive-sm">
                                        <table class="custom-table align-middle mb-0 text-med text-14">
                                            <thead>
                                                <tr>
                                                    <th scope="col"></th>
                                                    <th scope="col">Name</th>
                                                    <th scope="col">Role</th>
                                                    <th scope="col">Content</th>
                                                    <th scope="col">Date Submitted</th>
                                                    <th scope="col">Status</th>
                                                    <th scope="col" class="text-end"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <form>
                                                            <input type="checkbox" class="form-check-input ms-2" >
                                                        </form>
                                                    </td>
                                                    <td>Torrillo, Christian James D. </td>
                                                    <td>Student</td>
                                                    <!-- Lalabas ang modal pag-pinindot 'to -->
                                                    <td data-bs-toggle="modal" data-bs-target="#feedbackModal">The lessons are engaging!</td>
                                                    <td>10-21-25 00:11:11</td>
                                                    <td>Pending</td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <button class="btn btn-link text-dark p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="bi bi-three-dots-vertical fs-5"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li><a class="dropdown-item" href="#">Mark as reviewed</a></li>
                                                                <li><a class="dropdown-item" href="#">Mark as pending</a></li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <form>
                                                            <input type="checkbox" class="form-check-input ms-2">
                                                        </form>
                                                    </td>
                                                    <td>Torrillo, Christian James D. </td>
                                                    <td>Student</td>
                                                    <td data-bs-toggle="modal" data-bs-target="#feedbackModal">The lessons are engaging!</td>
                                                    <td>10-21-25 00:11:11</td>
                                                    <td>Pending</td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <button class="btn btn-link text-dark p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="bi bi-three-dots-vertical fs-5"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li><a class="dropdown-item" href="#">Mark as reviewed</a></li>
                                                                <li><a class="dropdown-item" href="#">Mark as pending</a></li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Delete Modal-->
                                <div class="modal" id="feedbackModal" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header border-bottom">
                                                <div class="text-sbold text-22">Feedback</div>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="transform: scale(0.8);"></button>
                                            </div>
                                            <div class="modal-body d-flex flex-column justify-content-start align-items-start mt-2 text-medium text-14 w-100" style="text-align: justify;">
                                                <p class="mb-0">Torrillo, Christian James D.</p>
                                                <p class="mb-0">10-21-25 00:11:21</p>
                                                <p class="mb-0">Student</p>
                                                <p class="mt-3 mb-4">
                                                    Webstar has made learning more engaging and fun. The gamified system motivates students to participate actively in every lesson,
                                                    and earning XP through quests adds an exciting twist to traditional coursework. The interface is intuitive, and the progress tracking features help
                                                    students see their improvement over time. As a professor, managing courses and monitoring student activity has become much easier. The dashboard
                                                    provides clear insights, and the feedback tools make communication seamless. Overall, Webstar creates an enjoyable and efficient learning environment
                                                    for both teachers and students.
                                                </p>
                                            </div>

                                            <div class="modal-footer border-top" style="padding-top: 45px;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
                
                <!-- Dropdown js -->
                <script>
                    document.querySelectorAll('.custom-dropdown').forEach(dropdown => {
                        const btn = dropdown.querySelector('.dropdown-btn');
                        const list = dropdown.querySelector('.dropdown-list');

                        btn.addEventListener('click', () => {
                            list.style.display = list.style.display === 'block' ? 'none' : 'block';
                        });

                        list.querySelectorAll('li').forEach(item => {
                            item.addEventListener('click', () => {
                                btn.textContent = item.dataset.value;
                                list.style.display = 'none';
                            });
                        });

                        // Close dropdown if clicked outside
                        document.addEventListener('click', (e) => {
                            if (!dropdown.contains(e.target)) {
                                list.style.display = 'none';
                            }
                        });
                    });
                </script>

</body>


</html>