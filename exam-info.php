<?php
$activePage = 'exam-info';
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Exam Info</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/exam-info.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="shared/assets/img/webstar-icon.png">
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
                                        <span class="text-reg text-18">Due Sept 9</span>
                                    </div>
                                    <div class="col-auto text-end">
                                        Score <div class="text-sbold text-25">
                                            - <span class="text-muted">/0</span>
                                        </div>
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
                                    <div class="graded text-reg text-18 mt-4">Score</div>
                                    <div class="score text-sbold text-25">
                                        - <span class="text-muted">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Left Content -->
                            <div class="col-12 col-lg-8">
                                <div class="p-0 px-lg-5">
                                    <div class="text-sbold text-14 mt-3">Exam General Guidelines</div>
                                    <p class="mt-3 text-med text-14 ">Attached is a Google Docs you can edit.</p>
                                    <p class="mt-3 text-med text-14 ">In Figma, design a "404 Not Found" page.</p>
                                    <p class="mt-3 text-med text-14 ">Create two versions, one for the mobile and one
                                        for the desktop.</p>
                                    <hr>
                                    <div class="text-sbold text-14 pb-3">Prepared by</div>
                                    <div class="d-flex align-items-center pb-5">
                                        <div class="rounded-circle me-2" style="width: 50px; height: 50px; background-color: var(--highlight75);">
                                            <img src="shared/assets/pfp-uploads/prof.png"
                                                alt="Prof Picture" class="rounded-circle" style="width:50px;height:50px;">
                                        </div>
                                        <div>
                                            <div class="text-sbold text-14">Chistian James Torillo</div>
                                            <div class="text-med text-12">January 12, 2024 8:00AM</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Content -->
                            <div class="col-12 col-lg-4">
                                <div class="cardSticky position-sticky" style="top: 20px;">
                                    <div class="p-2">
                                        <div class="text-sbold text-16">Exam Details</div>

                                        <div class="text-sbold text-16 text-center mt-4">50</div>
                                        <div class="text-reg text-14 text-center">Total Exam Items</div>

                                        <div class="text-sbold text-16 text-center mt-4">50</div>
                                        <div class="text-reg text-14 text-center">Total Exam Points</div>

                                        <div class="text-sbold text-16 text-center mt-4">50 mins</div>
                                        <div class="text-reg text-14 text-center">Exam Duration</div>

                                        <div id="examStatusText" class="text-reg text-14 text-center mt-4">
                                            The exam will be automatically submitted when the timer ends.
                                        </div>

                                        <!-- Buttons Section -->
                                        <div class="pt-3 text-center">
                                            <!-- ✅ Visible by default -->
                                            <a href="#">
                                                <button id="answerBtn"
                                                    class="button px-3 py-1 rounded-pill text-reg text-md-14"
                                                    style="background-color: var(--primaryColor);" onclick="takeExam()">
                                                    Answer Now
                                                </button>
                                            </a>
                                        </div>

                                        <!-- ✅ Hidden by default (separate section) -->
                                        <div id="viewResultContainer" class="pt-3 text-center" style="display: none;">
                                            <a href="#">
                                                <button id="viewResultBtn"
                                                    class="button px-3 py-1 rounded-pill text-reg text-md-14"
                                                    style="background-color: var(--primaryColor);"
                                                    onclick="viewResult()">
                                                    View Result
                                                </button>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function takeExam() {
            // Hide "Answer Now" button
            document.getElementById('answerBtn').style.display = 'none';
            document.getElementById('examStatusText').innerText =
                'Please wait for your instructor to release the test results.';

            // Simulate instructor grading after a delay (for demo only)
            setTimeout(() => {
                // Show "View Result" section only when graded
                document.getElementById('viewResultContainer').style.display = 'block';
                document.getElementById('examStatusText').innerText =
                    'Tests Results Available.';
            }, 5000);
        }

        function viewResult() {
            alert('Viewing your results...');
        }
    </script>
</body>

</html>