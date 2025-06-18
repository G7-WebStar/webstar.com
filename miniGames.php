<?php $activePage = 'minigames'; ?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Mini Games</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="shared/assets/css/miniGames.css">
</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-3">
        <div class="row w-100">


            <!-- Sidebar (only shows on mobile) -->
            <?php include 'shared/components/sidebar-for-mobile.php'; ?>

            <!-- Sidebar Column (fixed on desktop) -->
            <?php include 'shared/components/sidebar-for-desktop.php'; ?>

            <!-- Main Container Column-->
            <div class="col main-container m-0 p-0 mx-2 p-4 ">
                <div class="card border-0 p-3 h-100 w-100 rounded-0 shadow-none">

                    <!-- Navbar for mobile -->
                    <?php include 'shared/components/navbar-for-mobile.php'; ?>

                    <!-- PUT CONTENT HERE -->
                    <div class="container-fluid">
                        <div class="col">
                            <!-- GAME ONE -->
                            <div class="row m-0 mb-2 w-100">
                                <div class="card border-0 w-100 rounded-4 border">
                                    <div class="cardBody">
                                        <div class="cardTitle mx-1 d-none d-md-block">
                                            Mini Game 1
                                        </div>
                                        <div class="d-flex justify-content-center">
                                            <!-- Game Card -->
                                            <div class="card p-4 mx-1 w-100 rounded-4 gameContainer">
                                                <a href="#" class="stretched-link d-md-none position-static"></a>
                                                <div class="row w-100">
                                                    <div class="col-12 col-md-3 d-flex justify-content-center">
                                                        <img src="shared/assets/img/miniGamesImg/typing.svg"
                                                            class="img-fluid smallImg" alt="Game Image">
                                                    </div>
                                                    <div
                                                        class="gameTitle col-md-5 d-flex flex-column justify-content-center text-center text-md-start">
                                                        <p class="mb-0">Typing Game</p>
                                                        <p class="description mb-0 d-none d-md-block">Challenge your
                                                            typing skills</p>
                                                    </div>
                                                    <div
                                                        class="col-md-4 d-none d-md-flex align-items-center justify-content-end">
                                                        <a href="#">
                                                            <img src="shared/assets/img/miniGamesImg/arrow.svg"
                                                                class="img-fluid smallImgArrow" alt="Arrow">
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- GAME TWO -->
                            <div class="row m-0 mb-2 w-100">
                                <div class="card border-0 p-3 my-3 w-100 rounded-4 border">
                                    <div class="cardBody">
                                        <div class="cardTitle mx-1 d-none d-md-block">
                                            Mini Game 2
                                        </div>
                                        <div class="d-flex justify-content-center">
                                            <div class="card p-4 mx-1 w-100 rounded-4 gameContainer">
                                                <a href="#" class="stretched-link d-md-none position-static"></a>
                                                <div class="row w-100">
                                                    <div class="col-12 col-md-3 d-flex justify-content-center">
                                                        <img src="shared/assets/img/miniGamesImg/empty.svg"
                                                            class="img-fluid smallImg" alt="Game Image">
                                                    </div>
                                                    <div
                                                        class="gameTitle col-md-5 d-flex flex-column justify-content-center">
                                                        <p class="mb-0">Game Name</p>
                                                        <p class="description mb-0 d-none d-md-block">Game Description
                                                        </p>
                                                    </div>
                                                    <div
                                                        class="col-md-4 d-none d-md-flex align-items-center justify-content-end">
                                                        <a href="#">
                                                            <img src="shared/assets/img/miniGamesImg/arrow.svg"
                                                                class="img-fluid smallImgArrow" alt="Arrow">
                                                        </a>
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
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.min.js"
        integrity="sha384-RuyvpeZCxMJCqVUGFI0Do1mQrods/hhxYlcVfGPOfQtPJh0JCw12tUAZ/Mv10S7D"
        crossorigin="anonymous"></script>

</body>

</html>