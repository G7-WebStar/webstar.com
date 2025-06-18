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
    <link rel="stylesheet" href="shared/assets/css/home-styles.css">
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
            <div class="col main-container m-0 p-0 mx-2 p-2">
                <div class="card border-0 p-2 h-100 w-100 rounded-0 shadow-none">

                    <!-- Navbar for mobile -->
                    <?php include 'shared/components/navbar-for-mobile.php'; ?>

                    <!-- PUT CONTENT HERE -->
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="row pt-0" style="height: 30vh; font-family: var(--Regular);">
                                    <div class="col p-0">
                                        <div class="p-0 pb-1" style="font-size: 1em;">Continue Your Course</div>
                                        <div class="card containerCard1" style="height: 100%; padding: 1rem; position: relative; overflow: hidden;">
                                            <div class="d-flex align-items-center flex-column flex-md-row">

                                                <div class="card mt-2 me-3 d-none d-md-block" style="height: 100px; width: 140px; border-radius: 25px; margin-left: 15px;">
                                                    <img src="shared/assets/img/html.png" alt="Streak" style="width: 100%; height: 100%; object-fit: contain;">
                                                </div>
                                                <div class="mt-3 d-flex align-items-center justify-content-center w-100 pe-3">
                                                    <div class="text-start" style="color: var(--white);">
                                                        <div class="mb-1" style="font-size: 0.95em;">
                                                            CSS Course
                                                        </div>
                                                        <div class="mb-2" style="font-size: 1.5em; font-family: var(--Bold);">
                                                            Fonts and Formatting
                                                        </div>
                                                    </div>

                                                    <!-- papaltan na lang ng link-->
                                                    <a href="lesson.php">
                                                        <img src="shared/assets/img/arrowRight.png" alt="Arrow" style="height: 3em; width: 2em; margin-left: 15px;">
                                                    </a>
                                                </div>
                                            </div>

                                            <!-- Progress bar-->
                                            <div class="progress" style="position: absolute; bottom: 1.5rem; left: 2rem; right: 2rem; background-color: transparent; border: 1px solid #ffffff; border-radius: 22px; height: 10px;">
                                                <div class="progress-bar" role="progressbar"
                                                    style="width: 50%; background-color:var(--white); border-radius: 22px;"
                                                    aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="row pt-5 mt-1 flex-grow-1" style="font-family: var(--Regular);">
                                    <div class="col p-0">
                                        <div style="font-size: 1em;">Your Next Achievements</div>
                                        <!-- Reach 2 week streak -->
                                        <div class="card" style="height: 52vh; border: none; overflow-y: auto; background-color: transparent;">
                                            <div class="card achievementCard d-flex align-items-center my-1 px-3" style="height: 93px;">
                                                <div class="d-flex align-items-center w-100 gap-3">
                                                    <img src="shared/assets/img/streak.png" alt="Streak" class="img-fluid" style="max-width: 40px; margin-left: 15px;">

                                                    <div class="d-flex align-items-center w-100">
                                                        <div class="w-100">
                                                            <div class="d-flex align-items-center" style="color: var(--blue)">
                                                                <div style="font-size: 1em;">Reach 2 week streak</div>
                                                                <div class="blueColorText" style="font-size: 0.8em; margin-left: 20px;font-family: var(--Bold);">+120 XPs</div>
                                                            </div>
                                                            <!-- Progress bar-->
                                                            <div class="d-flex align-items-center mt-1">
                                                                <div class="progress flex-grow-1" style="height: 8px; background-color: transparent; border: 1px solid var(--blue); max-width: 85%;">
                                                                    <div class="progress-bar" role="progressbar" style="width: 64%; background-color: var(--blue);" aria-valuenow="64" aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                                <div class="blueColorText" style="font-size: 0.8em; margin-left: 8px;">64%</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Add 10 friends -->
                                            <div class="card achievementCard d-flex align-items-center my-1 px-3" style="height: 93px;">
                                                <div class="d-flex align-items-center w-100 gap-3">
                                                    <img src="shared/assets/img/friend.png" alt="Friend" class="img-fluid" style="max-width: 40px; margin-left: 15px;">
                                                    <div class="d-flex align-items-center w-100">
                                                        <div class="w-100">
                                                            <div class="d-flex align-items-center">
                                                                <div class="blueColorText" style="font-size: 1em;">Add 10 friends</div>
                                                                <div class="blueColorText" style="font-size: 0.8em; margin-left: 20px; font-family: var(--Bold);">+90 XPs</div>
                                                            </div>
                                                            <!-- Progress bar-->
                                                            <div class="d-flex align-items-center mt-1">
                                                                <div class="progress flex-grow-1" style="height: 8px; background-color: transparent; border: 1px solid var(--blue); max-width: 85%;">
                                                                    <div class="progress-bar" role="progressbar" style="width: 80%; background-color: var(--blue);" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                                <div class="blueColorText" style="font-size: 0.8em; margin-left: 8px;">80%</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Finish HTML Course -->
                                            <div class="card achievementCard d-flex align-items-center my-1 px-3" style="height: 93px;">
                                                <div class="d-flex align-items-center w-100 gap-3">
                                                    <img src="shared/assets/img/book.png" alt="Book" class="img-fluid" style="max-width: 40px; margin-left: 15px;">
                                                    <div class="d-flex align-items-center w-100">
                                                        <div class="w-100">
                                                            <div class="d-flex align-items-center">
                                                                <div class="blueColorText" style="font-size: 1em;">Finish HTML Course</div>
                                                                <div class="blueColorText" style="font-size: 0.8em; margin-left: 20px; font-family: var(--Bold);">+300 XPs</div>
                                                            </div>
                                                            <!-- Progress bar-->
                                                            <div class="d-flex align-items-center mt-1">
                                                                <div class="progress flex-grow-1" style="height: 8px; background-color: transparent; border: 1px solid var(--blue); max-width: 85%;">
                                                                    <div class="progress-bar" role="progressbar" style="width: 50%; background-color: var(--blue);" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                                <div class="blueColorText" style="font-size: 0.8em; margin-left: 8px;">50%</div>
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
                            <div class="col-12 col-md-6">
                                <div class="row pt-2" style="height: 40vh;">
                                    <div class="col">
                                        <div class="card card containerCard" style="height: 95%;">
                                            <div class="row" style="font-family: var(--Regular);">
                                                <div class="col ps-5 py-3">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="blueColorText mb-0" style="font-size: 1.20em; font-family: var(--Bold);">
                                                            Daily Streak
                                                        </div>
                                                        <div class="d-flex align-items-center">
                                                            <img src="shared/assets/img/streak.png" class="d-none d-md-block" alt="Icon" style="width: 25px; height: 30px;">
                                                            <span class="blueColorText" style="font-size: 1.2em; font-family: var(--Bold); margin-left: 10px; margin-right: 39px;">9 Days</span>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row gx-3 flex-nowrap overflow-auto" style="font-family: var(--Regular); margin-left: 29px; margin-right: 29px;">
                                                <div class="col text-center">
                                                    <div class="card calenderCard d-flex align-items-center justify-content-center">
                                                        <div style="font-size: 1.1em; line-height: 1;">5</div>
                                                        MON
                                                    </div>
                                                    <div style="width: 16px; height: 16px; background-color: var(--blue); border-radius: 50%; margin: 10px auto 0;"></div>
                                                </div>
                                                <div class="col text-center">
                                                    <div class="card calenderCard d-flex align-items-center justify-content-center">
                                                        <div style="font-size: 1.1em; line-height: 1;">6</div>
                                                        TUE
                                                    </div>
                                                    <div style="width: 16px; height: 16px; background-color: var(--blue); border-radius: 50%; margin: 10px auto 0;"></div>
                                                </div>
                                                <div class="col text-center">
                                                    <div class="card blueCard1 d-flex align-items-center justify-content-center">
                                                        <div style="font-size: 1.1em; line-height: 1; color: var(--white);">7</div>
                                                        WED
                                                    </div>
                                                    <div style="width: 16px; height: 16px; background-color: var(--blue); border-radius: 50%; margin: 10px auto 0;"></div>
                                                </div>
                                                <div class="col text-center">
                                                    <div class="card calenderCard card d-flex align-items-center justify-content-center">
                                                        <div style="font-size: 1.1em; line-height: 1;">8</div>
                                                        THU
                                                    </div>
                                                </div>
                                                <div class="col text-center">
                                                    <div class="card calenderCard d-flex align-items-center justify-content-center">
                                                        <div style="font-size: 1.1em; line-height: 1;">9</div>
                                                        FRI
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="height: 55vh;">
                                    <div class="col">
                                        <div class="card card containerCard" style="height: 90%;">
                                            <div class="row">
                                                <div class="col ps-5 py-3">
                                                    <div class="blueColorText" style="font-size: 1.2em; font-family: var(--Bold);">Weekly Leaderboard</div>
                                                </div>
                                            </div>
                                            <div class="row" style="font-family: var(--Regular);">
                                                <div class="col px-5" id="scrollable-order-list1" style="max-height: 240px; overflow-y: auto;">
                                                    <div class="card containerCard my-1" style="height: 50px;">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="d-flex align-items-center me-3 px-4 py-2">
                                                                <span class="blueColorText2">1</span>
                                                                <div class="card blueCard" style="width: 23px; height: 23px; border-radius: 50%; margin-left: 10px;"></div>
                                                                <div class="blueColorText" style="font-size: 0.7em; margin-left: 12px;">John Doe</div>
                                                            </div>
                                                            <div class="d-flex ms-auto">
                                                                <span class="blueColorText me-4" style="font-size: 0.65em;">360 WebStar</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card containerCard my-1" style="height: 50px;">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="d-flex align-items-center me-3 px-4 py-2">
                                                                <span class="blueColorText3">2</span>
                                                                <div class="card blueCard" style="width: 23px; height: 23px; border-radius: 50%; margin-left: 10px;"></div>
                                                                <div class="blueColorText" style="font-size: 0.7em; margin-left: 12px;">Jane Smith</div>
                                                            </div>
                                                            <div class="d-flex ms-auto">
                                                                <span class="blueColorText me-4" style="font-size: 0.65em;">350 WebStar</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card containerCard my-1" style="height: 50px;">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="d-flex align-items-center me-3 px-4 py-2">
                                                                <span class="blueColorText3">3</span>
                                                                <div class="card blueCard" style="width: 23px; height: 23px; border-radius: 50%; margin-left: 10px;"></div>
                                                                <div class="blueColorText" style="font-size: 0.7em; margin-left: 12px;">John Doe</div>
                                                            </div>
                                                            <div class="d-flex ms-auto">
                                                                <span class="blueColorText me-4" style="font-size: 0.65em;">340 WebStar</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card containerCard my-1" style="height: 50px;">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="d-flex align-items-center me-3 px-4 py-2">
                                                                <span class="blueColorText3">4</span>
                                                                <div class="card blueCard" style="width: 23px; height: 23px; border-radius: 50%; margin-left: 10px;"></div>
                                                                <div style="font-size: 0.7em; color: var(--blue); margin-left: 12px;">Jane Smith</div>
                                                            </div>
                                                            <div class="d-flex ms-auto">
                                                                <span class="blueColorText me-4" style="font-size: 0.65em;">330 WebStar</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card containerCard my-1" style="height: 50px;">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="d-flex align-items-center me-3 px-4 py-2">
                                                                <span class="blueColorText3">5</span>
                                                                <div class="card blueCard" style="width: 23px; height: 23px; border-radius: 50%; margin-left: 10px;"></div>
                                                                <div class="blueColorText" style="font-size: 0.7em; margin-left: 12px;">John Doe</div>
                                                            </div>
                                                            <div class="d-flex ms-auto">
                                                                <span class=" blueColorText me-4" style="font-size: 0.65em;">320 WebStar</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card containerCard my-1" style="height: 50px;">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="d-flex align-items-center me-3 px-4 py-2">
                                                                <span class="blueColorText3">6</span>
                                                                <div class="card blueCard" style="width: 23px; height: 23px; border-radius: 50%; margin-left: 10px;"></div>
                                                                <div class="blueColorText" style="font-size: 0.7em; margin-left: 12px;">Jane Smith</div>
                                                            </div>
                                                            <div class="d-flex ms-auto">
                                                                <span class="blueColorText me-4" style="font-size: 0.65em;">310 WebStar</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card containerCard my-1" style="height: 50px;">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="d-flex align-items-center me-3 px-4 py-2">
                                                                <span class="blueColorText3">7</span>
                                                                <div class="card blueCard" style="width: 23px; height: 23px; border-radius: 50%; margin-left: 10px;"></div>
                                                                <div class="blueColorText" style="font-size: 0.7em; margin-left: 12px;">Jane Smith</div>
                                                            </div>
                                                            <div class="d-flex ms-auto">
                                                                <span class="blueColorText me-4" style="font-size: 0.65em;">305 WebStar</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card containerCard my-1" style="height: 50px;">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="d-flex align-items-center me-3 px-4 py-2">
                                                                <span class="blueColorText3">8</span>
                                                                <div class="card blueCard" style="width: 23px; height: 23px; border-radius: 50%; margin-left: 10px;"></div>
                                                                <div class="blueColorText" style="font-size: 0.7em; margin-left: 12px;">Jane Smith</div>
                                                            </div>
                                                            <div class="d-flex ms-auto">
                                                                <span class="blueColorText me-4" style="font-size: 0.65em;">300 WebStar</span>
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