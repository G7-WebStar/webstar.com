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
            <!-- Main Container Column-->
            <div class="col">
                <!-- Navbar for mobile -->
                <?php include 'shared/components/navbar-for-mobile.php'; ?>
                <div class="row">
                    <!-- Main Container -->
                    <div class="col main-container mx-lg-2 p-2 overflow-y-auto d-flex flex-column">
                        <div class="card border-0 p-2 d-flex flex-lg-row flex-column justify-content-center">
                            <!-- Left Column -->
                            <div class="col-lg-6 col-12 px-4 d-flex flex-column">
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-12 p-4 box">
                                            <div class="mb-3 continue-text">Continue Your Course</div>
                                            <div class="px-5 py-4 border continue-course-container">
                                                <div class="d-flex flex-column flex-sm-row flex-lg-column flex-xl-row gap-3">
                                                    <div class="img-placeholder"></div>
                                                    <div class="m-auto">
                                                        <div class="course-title">HTML Course</div>
                                                        <div class="fw-bold text-white lesson-title">Adding Images to Your Website</div>
                                                    </div>
                                                    <img src="shared/assets/img/courses/keyboard_arrow_right.png" alt="arrow" class="d-none d-sm-block d-lg-none d-xl-block img-fluid ms-auto arrow" width="120px">
                                                </div>
                                                <div class="mt-sm-3">
                                                    <div class="w-100 mx-auto mt-2 border progress-bar">
                                                        <div class="w-50 progress"></div>
                                                    </div>
                                                    <div class="d-flex justify-content-between">
                                                        <p class="ms-3 mt-2 mb-0 progress-text">Course Progress</p>
                                                        <p class="me-3 mt-2 mb-0 progress-text">50%</p>
                                                    </div>
                                                </div>
                                                <div class="d-flex d-lg-flex justify-content-center d-sm-none d-lg-block d-xl-none mt-2">
                                                    <div class="btn text-center w-75 border continue-btn fw-bold">Continue</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 d-flex flex-column">
                                    <div class="row d-flex align-items-center">
                                        <div class="col-12 mt-3 p-0">
                                            <div class="webdev-courses">Web Development Courses</div>
                                            <div class="container-fluid py-3 mt-3 mx-auto courses-container">
                                                <div class="grid-container">
                                                    <div class="box d-flex flex-column align-items-center p-2 p-sm-4" style="grid-area: box-1;">
                                                        <div class="d-flex flex-row align-items-center">
                                                            <div class="fw-bold course-text">HTML Course</div>
                                                            <img src="shared/assets/img/courses/keyboard_arrow_right.png" alt="arrow" class="d-none d-sm-block d-lg-none d-xl-block img-fluid ms-auto course-arrow" width="90px">
                                                        </div>
                                                        <div class="w-100 mx-auto mt-2 border progress-bar">
                                                            <div class="w-50 progress"></div>
                                                        </div>
                                                        <div class="done-text w-100 mt-2">50% done</div>
                                                    </div>
                                                    <div class="box-locked d-flex justify-content-center align-items-center p-2 p-sm-4 opacity-50" style="grid-area: box-2;">
                                                        <div class="d-flex flex-column">
                                                            <div class="d-flex flex-row align-items-center">
                                                                <div class="fw-bold course-text">CSS Course</div>
                                                                <img src="shared/assets/img/courses/locked.png" alt="arrow" class="d-none d-sm-block d-lg-none d-xl-block img-fluid ms-auto course-lock" width="60px">
                                                            </div>
                                                            <div class="done-text me-auto">Finish HTML Course to unlock</div>
                                                        </div>
                                                    </div>
                                                    <div class="box-locked more-text fw-bold p-md-5 p-2 p-sm-4 text-center d-flex justify-content-center align-items-center" style="grid-area: box-3;">
                                                        <div class="m-md-5 m-3">More Courses Coming Soon!</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Right Column -->
                            <div class="col-lg-6 col-12 px-lg-4 px-2 d-flex flex-column">
                                <div class="px-lg-3 px-0 mt-lg-0 mt-lg-2 mt-3 d-flex flex-column flex-grow-1">
                                    <div class="lesson-available mb-3">Lessons Available</div>
                                    <div class="lessons-container px-4 p-3 py-md-5 lesson-available overflow-y-auto flex-grow-1 text-center">
                                        <div class="h5 mb-3 fw-bold">HTML Lessons</div>
                                        <div class="lessons-container mb-2 p-1 p-sm-3 d-flex align-items-center">
                                            <div class="me-auto ms-1 lesson-text">01</div>
                                            <div class="mx-auto lesson-text">Introduction to HTML</div>
                                            <img src="shared/assets/img/courses/check_circle.png" alt="arrow" class="img-fluid ms-auto me-1 lesson-image" width="28px">
                                        </div>
                                        <div class="lessons-container mb-2 p-1 p-sm-3 d-flex align-items-center">
                                            <div class="me-auto ms-1 lesson-text">02</div>
                                            <div class="mx-auto lesson-text">Text Formatting and Headings</div>
                                            <img src="shared/assets/img/courses/check_circle.png" alt="arrow" class="img-fluid ms-auto me-1 lesson-image" width="28px">
                                        </div>
                                        <div class="box fw-bold mb-2 p-1 p-sm-3 d-flex align-items-center">
                                            <div class="me-auto ms-1 lesson-text">03</div>
                                            <div class="mx-auto lesson-text">Adding Images to Your Website</div>
                                            <img src="shared/assets/img/courses/keyboard_arrow_right.png" alt="arrow" class="img-fluid ms-auto me-1 lesson-image" width="28px">
                                        </div>
                                        <div class="lessons-container mb-2 p-1 p-sm-3 d-flex align-items-center opacity-25">
                                            <div class="me-auto ms-1 lesson-text">04</div>
                                            <div class="mx-auto lesson-text">Lists and Tables</div>
                                            <img src="shared/assets/img/courses/locked.png" alt="arrow" class="img-fluid ms-auto me-1 lesson-image" width="28px">
                                        </div>
                                        <div class="lessons-container mb-2 p-1 p-sm-3 d-flex align-items-center opacity-25">
                                            <div class="me-auto ms-1 lesson-text">05</div>
                                            <div class="mx-auto lesson-text">Forms and Inputs</div>
                                            <img src="shared/assets/img/courses/locked.png" alt="arrow" class="img-fluid ms-auto me-1 lesson-image" width="28px">
                                        </div>
                                    </div>
                                </div>
                                <!--</div>-->
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