<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Courses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="shared/assets/css/course.css">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-3">

        <div class="row w-100">

            <!-- Sidebar (only shows on mobile) -->
            <?php include 'shared/components/sidebar-for-mobile.php'; ?>

            <!-- Sidebar Column (fixed on desktop) -->
            <?php include 'shared/components/sidebar-for-desktop.php'; ?>

            <!-- Navbar for mobile -->
            <?php include 'shared/components/navbar-for-mobile.php'; ?>

            <!-- Main Container Column-->
            <div class="col">
                <div class="row">
                    <!-- Main Container -->
                    <div class="col main-container mx-2 p-2 ">
                        <div class="card border-0 p-4">
                            <div class="row">
                                <div class="col-7 p-4 box">
                                    <div class="mb-3 continue-text">Continue Your Course</div>
                                    <div class="container px-xxl-4 p-xl-3 p-lg-2 py-3 border course-container d-flex flex-xl-row flex-column justify-content-start justify-content-xl-around gap-3">
                                        <img src="shared/assets/img/courses/html-logo.png" alt="html-logo" class="logo mx-1" width="80px">
                                        <div class="d-flex flex-column">
                                            <div class="course-title">HTML Course</div>
                                            <div class="fw-bold text-white lesson-title">Adding Images to Your Website</div>
                                        </div>
                                        <img src="shared/assets/img/courses/keyboard_arrow_right.png" alt="arrow" class="img-fluid float-right arrow" width="120px">
                                    </div>
                                </div>
                                <div class="col-5">Lessons Available</div>
                            </div>
                            <div class="row">
                                <div class="col-7">Hi</div>
                            </div>
                            <!-- PUT CONTENT HERE -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

</body>


</html>