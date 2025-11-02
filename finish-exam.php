<?php
$activePage = 'finish-exam';
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Finish Exam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/finish-exam.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="shared/assets/img/webstar-icon.png">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=alarm" />
</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-0 p-md-3"
        style="background-color: var(--black);">

        <div class="row w-100">

            <?php include 'shared/components/sidebar-for-mobile.php'; ?>
            <?php include 'shared/components/sidebar-for-desktop.php'; ?>

            <div class="col main-container m-0 p-0 mx-0 mx-md-2 p-0 p-md-4 overflow-y-auto">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">
                    <?php include 'shared/components/navbar-for-mobile.php'; ?>

                    <div class="container-fluid py-3 overflow-y-auto">
                        <div class="row mb-3 row-padding-top">
                            <div class="col-12 cardHeader p-3 mb-4">

                                <!-- DESKTOP VIEW -->
                                <div class="row desktop-header d-none d-sm-flex">
                                    <div class="col-auto me-2">
                                        <a href="#" class="text-decoration-none">
                                            <i class="fa-solid fa-arrow-left text-reg text-16"
                                                style="color: var(--black);"></i>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <div class="text-sbold text-25">Quiz #1</div>
                                    </div>
                                    <div class="col-auto text-end d-flex align-items-center gap-1 justify-content-end">
                                        <i class="fa-regular fa-clock text-reg text-18 me-1"
                                            style="color: var(--black);"></i>
                                        <div class="text-reg text-25">10:00</div>
                                    </div>
                                </div>

                                <!-- MOBILE VIEW -->
                                <div class="d-block d-sm-none mobile-assignment">
                                    <div class="mobile-top">
                                        <div class="arrow">
                                            <a href="#" class="text-decoration-none">
                                                <i class="fa-solid fa-arrow-left text-reg text-16"
                                                    style="color: var(--black);"></i>
                                            </a>
                                        </div>
                                        <div class="title text-sbold text-25">Quiz #1</div>
                                    </div>
                                    <div
                                        class="graded text-reg text-18 mt-4 d-flex align-items-center gap-1 justify-content-center">
                                        <i class="fa-regular fa-clock text-reg text-18 me-1"
                                            style="color: var(--black);"></i>
                                        10:00
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- RESULT SECTION -->
                        <div class="text-center p-4 rounded-4 result-section mx-auto" style="max-width: 600px;">
                            <img src="shared/assets/img/medal.png" alt="Medal" class="img-fluid mb-3"
                                style="max-width: 140px;">
                            <div class="fw-bold text-dark text-30">
                                You scored
                                <span style="color: var(--black);">85 / 100</span>!
                            </div>

                            <div class="mt-4">
                                <h6 class="text-uppercase fw-bold text-dark">Rewards</h6>
                                <div class="text-dark mt-2">
                                    <div class="text-bold text-16">
                                        ⚡ +150 XPs <span class="text-sbold text-14">+20 Bonus XPs</span>
                                    </div>
                                    <div class="text-bold text-16">
                                        💰 +50 Webstars <span class="text-sbold text-14">+20 Bonus Webstars</span>
                                    </div>
                                </div>
                            </div>

                            <p class="mt-4 mb-4 text-reg text-14 px-3">
                                Please wait for your instructor to release the test results. Once returned,
                                you'll be able to review which questions you got correct and get feedback.
                            </p>

                            <div class="d-flex justify-content-center gap-3 flex-wrap">
                                <!-- Opens the modal -->
                                <a href="#" data-bs-toggle="modal" data-bs-target="#xpMultiplierModal"
                                    class="button gradient-btn px-3 py-1 rounded-pill text-reg text-md-14 text-decoration-none text-center text-sbold">
                                    Use XP Multiplier
                                </a>
                                <a href="#"
                                    class="button button-hover-effect px-3 py-1 rounded-pill text-reg text-md-14 text-decoration-none text-center text-sbold"
                                    style="background-color: var(--primaryColor);">
                                    Return to Test Info
                                </a>

                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- XP MULTIPLIER MODAL -->
    <div class="modal fade" id="xpMultiplierModal" tabindex="-1" aria-labelledby="xpMultiplierLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-3 text-center position-relative">
                <button type="button" class="btn-close position-absolute end-0 top-0 m-3" data-bs-dismiss="modal"
                    aria-label="Close"></button>

                <hr>

                <div id="xpMultiplierLabel" class="text-bold mb-3 text-dark mt-2 text-25">Multiply your XPs</div>

                <div class="text-dark mb-3">
                    <div class="text-sbold mb-1">Cost: <span class="text-bold">25 Webstars</span></div>
                    <div class="text-sbold">Current XPs: <span class="text-bold">150 → 300 XPs</span> after boost</div>
                </div>

                <p class="text-reg mb-4 px-3">
                    Are you sure you want to use <b>25 Webstars</b> to activate this multiplier?
                </p>

                <hr>

                <div class="d-flex justify-content-center">
                    <a href="#"
                        class="button gradient-btn px-4 py-2 rounded-pill text-sbold text-decoration-none text-center">
                        Use XP Multiplier
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Confetti effect -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
    <script>
        // Trigger confetti when the page loads
        window.onload = function () {
            const duration = 5000; // Duration in milliseconds
            const animationEnd = Date.now() + duration;
            const defaults = {
                startVelocity: 25, // slower fall
                spread: 360,
                ticks: 200, // longer visibility (higher = stays longer)
                zIndex: 9999
            };

            function randomInRange(min, max) {
                return Math.random() * (max - min) + min;
            }

            (function frame() {
                confetti({
                    ...defaults,
                    particleCount: 5,
                    origin: {
                        x: randomInRange(0, 1), // random horizontal positions
                        y: 0 // start at top
                    }
                });

                if (Date.now() < animationEnd) {
                    requestAnimationFrame(frame);
                }
            })();
        };
    </script>





</body>

</html>