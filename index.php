<?php $activePage = 'home'; ?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="shared/assets/css/home.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-3">

        <div class="row w-100">

            <!-- Sidebar (only shows on mobile) -->
            <?php include 'shared/components/sidebar-for-mobile.php'; ?>

            <!-- Sidebar Column (fixed on desktop) -->
            <div class="col-auto d-none d-md-block">
                <?php include 'shared/components/sidebar-for-desktop.php'; ?>
            </div>

            <!-- Main Container Column-->
            <div class="col main-container m-0 p-0 mx-2 p-4 overflow-y-auto">
                <div class="card border-0 p-3 h-100 w-100 rounded-0 shadow-none">

                    <!-- Navbar for mobile -->
                    <?php include 'shared/components/navbar-for-mobile.php'; ?>

                    <!-- PUT CONTENT HERE -->
                    <div class="container-fluid py-3 overflow-y-auto">
                        <div class="row">
                            <div class="col-12 col-sm-12 col-md-6">
                                <div class="row" style="font-family: var(--Regular);">
                                    <div class="col-12 pt-2">
                                        <div
                                            class="card welcomeCard mb-3 d-flex align-items-center justify-content-center text-center">
                                            <div>
                                                <div style="font-size: 1.6em; font-family: var(--Bold);">Welcome back,
                                                    James!</div>
                                                <div style="font-size: 1em;">Pick up where you left off and keep
                                                    building you skills.</div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="row pt-1" style="font-family: var(--Regular);">
                                    <div class="col pt-2">
                                        <div class="mb-2">Continue Your Course</div>
                                        <div
                                            class="card cycContainerCard p-4 flex justify-content-center align-items-center">
                                            <div
                                                class="d-flex flex-column flex-sm-row flex-lg-column flex-xl-row gap-3">
                                                <div
                                                    class="img-placeholder imageProfile flex-shrink-0 d-none d-md-block">
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <div class="text-white htmlText">HTML Course</div>
                                                    <div class="fw-bold text-white addImageText">Adding Images to Your
                                                        Website</div>
                                                </div>
                                                <div class="justify-content-end d-none d-sm-flex d-lg-none d-xl-flex ms-auto align-items-center"
                                                    style="width: 120px;">
                                                    <a href="lesson.php" class="w-100 d-flex align-items-center">
                                                        <img src="shared/assets/img/courses/keyboard_arrow_right.png"
                                                            alt="arrow" style="width: 100%;">
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="mt-sm-3 w-100">
                                                <div class="w-100 mx-auto mt-2 border progress-bar"
                                                    style="background-color: transparent; height: 10px; border-radius: 20px;">
                                                    <div class="w-50 progress" style="background-color: var(--white);">
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <p class="mb-0 text-white cProgress-text">Course Progress</p>
                                                    <p class="mb-0 text-white cProgress-text">50%</p>
                                                </div>
                                            </div>
                                            <div
                                                class="d-flex d-lg-flex justify-content-center d-sm-none d-lg-block d-xl-none mt-2 w-100">
                                                <a href="lesson.php" class="d-flex justify-content-center"
                                                    style="width: 100%; text-decoration: none;">
                                                    <div class="btn continueButton text-center border fw-bold"
                                                        style="width: 100%;">Continue</div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row py-2 mt-1 flex-grow-1" style="font-family: var(--Regular);">
                                    <div class="col">
                                        <div class="p-0 mt-5 pb-2" style="font-size: 1em;">Your Next Achievements</div>
                                        <!-- Reach 2 week streak -->
                                        <div class="card" style="border: none; background-color: transparent;">
                                            <div class="card achievementCard d-flex align-items-center justify-content-center my-1 px-3"
                                                style="height: 107px;">
                                                <div class="d-flex align-items-center w-100 gap-3">
                                                    <img src="shared/assets/img/streak.png" alt="Streak"
                                                        class="img-fluid" style="max-width: 40px; margin-left: 15px;">
                                                    <div class="d-flex align-items-center w-100">
                                                        <div class="w-100">
                                                            <div class="d-flex align-items-center">
                                                                <div class="p-0 blueColorText"
                                                                    style="font-size: 1.1em;">Reach 2 week</div>
                                                                <div class="p-0 blueColorText"
                                                                    style="font-size: 0.9em; margin-left: 20px; font-family: var(--Bold);">
                                                                    +120 XPs</div>
                                                            </div>
                                                            <!-- Progress bar-->
                                                            <div class="d-flex align-items-center mt-1">
                                                                <div class="progress flex-grow-1"
                                                                    style="height: 10px; background-color: transparent; border: 1px solid var(--blue); max-width: 85%;">
                                                                    <div class="progress-bar" role="progressbar"
                                                                        style="width: 64%; background-color: var(--blue);"
                                                                        aria-valuenow="64" aria-valuemin="0"
                                                                        aria-valuemax="100"></div>
                                                                </div>
                                                                <div class="blueColorText d-none d-sm-block"
                                                                    style="font-size: 0.8em; margin-left: 10px;">64%
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Add 10 friends -->
                                            <div class="card achievementCard d-flex align-items-center my-1 px-3"
                                                style="height: 107px;">
                                                <div class="d-flex align-items-center w-100 gap-3">
                                                    <img src="shared/assets/img/friend.png" alt="Friend"
                                                        class="img-fluid" style="max-width: 40px; margin-left: 15px;">
                                                    <div class="d-flex align-items-center w-100">
                                                        <div class="w-100">
                                                            <div class="d-flex align-items-center">
                                                                <div class="p-0 blueColorText"
                                                                    style="font-size: 1.1em;">Add 10 friends</div>
                                                                <div class="p-0 blueColorText"
                                                                    style="font-size: 0.9em; margin-left: 20px; font-family: var(--Bold);">
                                                                    +90 XPs</div>
                                                            </div>
                                                            <!-- Progress bar-->
                                                            <div class="d-flex align-items-center mt-1">
                                                                <div class="progress flex-grow-1"
                                                                    style="height: 10px; background-color: transparent; border: 1px solid var(--blue); max-width: 85%;">
                                                                    <div class="progress-bar" role="progressbar"
                                                                        style="width: 80%; background-color: var(--blue);"
                                                                        aria-valuenow="80" aria-valuemin="0"
                                                                        aria-valuemax="100"></div>
                                                                </div>
                                                                <div class="blueColorText d-none d-sm-block"
                                                                    style="font-size: 0.8em; margin-left: 10px;">80%
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Finish HTML Course -->
                                            <div class="card achievementCard d-flex align-items-center my-1 px-3"
                                                style="height: 107px;">
                                                <div class="d-flex align-items-center w-100 gap-3">
                                                    <img src="shared/assets/img/book.png" alt="Book" class="img-fluid"
                                                        style="max-width: 40px; margin-left: 15px;">
                                                    <div class="d-flex align-items-center w-100">
                                                        <div class="w-100">
                                                            <div class="d-flex align-items-center">
                                                                <div class="p-0 blueColorText"
                                                                    style="font-size: 1.1em;">Finish HTML Course</div>
                                                                <div class="p-0 blueColorText"
                                                                    style="font-size: 0.9em; margin-left: 20px; font-family: var(--Bold);">
                                                                    +300 XPs</div>
                                                            </div>
                                                            <!-- Progress bar-->
                                                            <div class="d-flex align-items-center mt-1">
                                                                <div class="progress flex-grow-1"
                                                                    style="height: 10px; background-color: transparent; border: 1px solid var(--blue); max-width: 85%;">
                                                                    <div class="progress-bar" role="progressbar"
                                                                        style="width: 50%; background-color: var(--blue);"
                                                                        aria-valuenow="50" aria-valuemin="0"
                                                                        aria-valuemax="100"></div>
                                                                </div>
                                                                <div class="blueColorText d-none d-sm-block"
                                                                    style="font-size: 0.8em; margin-left: 10px;">50%
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

                            <!-- Streak -->
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                <div class="row pt-2">
                                    <div class="col">
                                        <div class="card card containerCard" style="height: 315px;">
                                            <div class="row" style="font-family: var(--Regular);">
                                                <div class="dailyStreak col ps-5 pb-3 pt-5">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="blueColorText mb-0"
                                                            style="font-size: 1.50em; font-family: var(--Bold);">
                                                            Daily Streak
                                                        </div>
                                                        <div class="d-flex align-items-center">
                                                            <img src="shared/assets/img/streak.png"
                                                                class="d-none d-md-block" alt="Icon"
                                                                style="width: 25px; height: 30px;">
                                                            <span class="blueColorText"
                                                                style="font-size: 1.4em; font-family: var(--Bold); margin-left: 10px; margin-right: 39px;">9
                                                                Days</span>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row gx-3 flex-nowrap overflow-auto"
                                                style="font-family: var(--Regular); margin-left: 29px; margin-right: 29px;">
                                                <div class="col text-center">
                                                    <div
                                                        class="card calenderCard d-flex align-items-center justify-content-center">
                                                        <div style="font-size: 1.1em; line-height: 1;">5</div>
                                                        MON
                                                    </div>
                                                    <div
                                                        style="width: 16px; height: 16px; background-color: var(--blue); border-radius: 50%; margin: 10px auto 0;">
                                                    </div>
                                                </div>
                                                <div class="col text-center">
                                                    <div
                                                        class="card calenderCard d-flex align-items-center justify-content-center">
                                                        <div style="font-size: 1.1em; line-height: 1;">6</div>
                                                        TUE
                                                    </div>
                                                    <div
                                                        style="width: 16px; height: 16px; background-color: var(--blue); border-radius: 50%; margin: 10px auto 0;">
                                                    </div>
                                                </div>
                                                <div class="col text-center">
                                                    <div class="card calenderCard d-flex align-items-center justify-content-center"
                                                        style="background-color: var(--blue); color: var(--white);">
                                                        <div style="font-size: 1.1em; line-height: 1;">7</div>
                                                        WED
                                                    </div>
                                                    <div
                                                        style="width: 16px; height: 16px; background-color: var(--blue); border-radius: 50%; margin: 10px auto 0;">
                                                    </div>
                                                </div>
                                                <div class="col text-center">
                                                    <div
                                                        class="card calenderCard card d-flex align-items-center justify-content-center">
                                                        <div style="font-size: 1.1em; line-height: 1;">8</div>
                                                        THU
                                                    </div>
                                                </div>
                                                <div class="col text-center">
                                                    <div
                                                        class="card calenderCard d-flex align-items-center justify-content-center">
                                                        <div style="font-size: 1.1em; line-height: 1;">9</div>
                                                        FRI
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row py-4">
                                    <div class="col">
                                        <div class="card card containerCard" style="height: 542px;">
                                            <div class="row">
                                                <div class="col ps-5 pb-3 pt-4">
                                                    <div class="blueColorText"
                                                        style="font-size: 1.5em; font-family: var(--Bold);">Weekly
                                                        Leaderboard</div>
                                                </div>
                                            </div>
                                            <div class="row" style="font-family: var(--Regular);">
                                                <div class="col px-5" id="scrollable-order-list1"
                                                    style="max-height: 442px; overflow-y: auto;">
                                                    <div class="card containerCard my-1"
                                                        style="height: 70px; background-color: var(--blue);">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center h-100">
                                                            <div class="d-flex align-items-center h-100"
                                                                style="padding-left: 1.5rem;">
                                                                <span class="whiteColorText">1</span>
                                                                <div class="card d-flex align-items-center justify-content-center"
                                                                    style="width: 30px; height: 30px; border-radius: 50%; margin-left: 10px; overflow: hidden;">
                                                                    <img src="https://avatars.githubusercontent.com/u/181800261?s=96&v=4"
                                                                        alt="User"
                                                                        style="width: 100%; height: 100%; object-fit: contain;">
                                                                </div>
                                                                <div class="whiteColorText"
                                                                    style="font-size: 1em; margin-left: 12px;">James Doe
                                                                </div>
                                                            </div>
                                                            <div class="d-flex align-items-center h-100"
                                                                style="padding-right: 1.5rem;">
                                                                <span class="whiteColorText"
                                                                    style="font-size: 0.90em;">360 WebStar</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="card containerCard my-2" style="height: 70px;">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center h-100">
                                                            <div class="d-flex align-items-center h-100"
                                                                style="padding-left: 1.5rem;">
                                                                <span class="blueColorText3">2</span>
                                                                <div class="card d-flex align-items-center justify-content-center"
                                                                    style="width: 30px; height: 30px; border-radius: 50%; margin-left: 10px; overflow: hidden;">
                                                                    <img src="https://i.pinimg.com/originals/05/09/9d/05099d40c391f5a5ff8d23af41e344fa.jpg"
                                                                        alt="User"
                                                                        style="width: 100%; height: 100%; object-fit: contain;">
                                                                </div>
                                                                <div class="blueColorText"
                                                                    style="font-size: 1em; margin-left: 12px;">Jane
                                                                    Smith</div>
                                                            </div>
                                                            <div class="d-flex align-items-center h-100"
                                                                style="padding-right: 1.5rem;">
                                                                <span class="blueColorText"
                                                                    style="font-size: 0.90em;">350 WebStar</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="card containerCard my-2" style="height: 70px;">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center h-100">
                                                            <div class="d-flex align-items-center h-100"
                                                                style="padding-left: 1.5rem;">
                                                                <span class="blueColorText3">3</span>
                                                                <div class="card blueCard"
                                                                    style="width: 30px; height: 30px; border-radius: 50%; margin-left: 10px;">
                                                                </div>
                                                                <div class="blueColorText"
                                                                    style="font-size: 1em; margin-left: 12px;">John Doe
                                                                </div>
                                                            </div>
                                                            <div class="d-flex align-items-center h-100"
                                                                style="padding-right: 1.5rem;">
                                                                <span class="blueColorText"
                                                                    style="font-size: 0.90em;">340 WebStar</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card containerCard my-2" style="height: 70px;">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center h-100">
                                                            <div class="d-flex align-items-center h-100"
                                                                style="padding-left: 1.5rem;">
                                                                <span class="blueColorText3">4</span>
                                                                <div class="card blueCard"
                                                                    style="width: 30px; height: 30px; border-radius: 50%; margin-left: 10px;">
                                                                </div>
                                                                <div class="blueColorText"
                                                                    style="font-size: 1em; margin-left: 12px;">Jane
                                                                    Smith</div>
                                                            </div>
                                                            <div class="d-flex align-items-center h-100"
                                                                style="padding-right: 1.5rem;">
                                                                <span class="blueColorText"
                                                                    style="font-size: 0.90em;">330 WebStar</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card containerCard my-2" style="height: 70px;">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center h-100">
                                                            <div class="d-flex align-items-center h-100"
                                                                style="padding-left: 1.5rem;">
                                                                <span class="blueColorText3">5</span>
                                                                <div class="card blueCard"
                                                                    style="width: 30px; height: 30px; border-radius: 50%; margin-left: 10px;">
                                                                </div>
                                                                <div class="blueColorText"
                                                                    style="font-size: 1em; margin-left: 12px;">John Doe
                                                                </div>
                                                            </div>
                                                            <div class="d-flex align-items-center h-100"
                                                                style="padding-right: 1.5rem;">
                                                                <span class="blueColorText"
                                                                    style="font-size: 0.90em;">320 WebStar</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card containerCard my-2" style="height: 70px;">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center h-100">
                                                            <div class="d-flex align-items-center h-100"
                                                                style="padding-left: 1.5rem;">
                                                                <span class="blueColorText3">6</span>
                                                                <div class="card blueCard"
                                                                    style="width: 30px; height: 30px; border-radius: 50%; margin-left: 10px;">
                                                                </div>
                                                                <div class="blueColorText"
                                                                    style="font-size: 1em; margin-left: 12px;">Jane
                                                                    Smith</div>
                                                            </div>
                                                            <div class="d-flex align-items-center h-100"
                                                                style="padding-right: 1.5rem;">
                                                                <span class="blueColorText"
                                                                    style="font-size: 0.90em;">310 WebStar</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card containerCard my-2" style="height: 70px;">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center h-100">
                                                            <div class="d-flex align-items-center h-100"
                                                                style="padding-left: 1.5rem;">
                                                                <span class="blueColorText3">7</span>
                                                                <div class="card blueCard"
                                                                    style="width: 30px; height: 30px; border-radius: 50%; margin-left: 10px;">
                                                                </div>
                                                                <div class="blueColorText"
                                                                    style="font-size: 1em; margin-left: 12px;">John Doe
                                                                </div>
                                                            </div>
                                                            <div class="d-flex align-items-center h-100"
                                                                style="padding-right: 1.5rem;">
                                                                <span class="blueColorText"
                                                                    style="font-size: 0.90em;">305 WebStar</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card containerCard my-2" style="height: 70px;">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center h-100">
                                                            <div class="d-flex align-items-center h-100"
                                                                style="padding-left: 1.5rem;">
                                                                <span class="blueColorText3">8</span>
                                                                <div class="card blueCard"
                                                                    style="width: 30px; height: 30px; border-radius: 50%; margin-left: 10px;">
                                                                </div>
                                                                <div class="blueColorText"
                                                                    style="font-size: 1em; margin-left: 12px;">Jane
                                                                    Smith</div>
                                                            </div>
                                                            <div class="d-flex align-items-center h-100"
                                                                style="padding-right: 1.5rem;">
                                                                <span class="blueColorText"
                                                                    style="font-size: 0.90em;">300 WebStar</span>
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
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

</body>


</html>