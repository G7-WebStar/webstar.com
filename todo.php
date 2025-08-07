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
                            <!-- PUT CONTENT HERE -->
                            <div class="col-12 col-sm-12 col-md-7">
                                <div class="row py-4">
                                    <div class="col-12">
                                        <div class="text-sbold text-22">
                                            My To-do
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-10">
                                <div class="todo-card d-flex align-items-stretch rounded-4">
                                    <div class="date d-flex align-items-center justify-content-center text-sbold text-20"
                                        style="min-width: 100px;">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

</body>


</html>