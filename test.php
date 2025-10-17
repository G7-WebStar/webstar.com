<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Index</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="shared/assets/css/index.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="shared/assets/img/webstar-icon.png">

</head>

<body>
    <div class="container-fluid min-vh-100  justify-content-center align-items-center p-0 p-md-3"
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

                    <div class="container-fluid py-1 overflow-y-auto">
                        <div class="row">
                            <!-- PUT CONTENT HERE -->
                            <div class="container">
                                <div class="row">
                                    <div class="col-12">
                                        <!--Quiz Nav-->
                                        <div class="row bg-white border border-black rounded-4 my-3 text-sbold">
                                            <div class="quiz-nav col-12 d-flex flex-row align-items-center my-2 px-5 py-3">
                                                <div class="d-flex flex-row">
                                                    <i class="announcement-arrow fa-lg fa-solid fa-arrow-left text-reg text-12 mt-3" style="color: var(--black);"></i>
                                                    <div class="h2 ms-5 my-0">
                                                        Quiz #1
                                                    </div>
                                                </div>
                                                <div class="h2 ms-auto my-0">
                                                    <i class="bi bi-clock fa-xs" style="color: var(--black);"></i>
                                                    10:00
                                                </div>
                                            </div>
                                        </div>
                                        <!--End of Quiz Nav-->
                                        <div class="row mt-5">
                                            <div class="col-12">
                                                <div class="h2 text-sbold text-center">
                                                    Multiple Choice
                                                </div>
                                                <div class="col-8 h4 text-reg mx-auto mt-5">
                                                    Read each question carefully and choose the best answer from the given options. 
                                                    Only one option is correct for each question. Once you move to the next question, 
                                                    you will not be able to return to the previous one, so review your answer before proceeding. 
                                                    The exam will automatically submit when the timer ends. Avoid refreshing or closing the browser 
                                                    during the exam to prevent submission issues.
                                                </div>
                                                <div class="button-container mt-5">
                                                    <div class="btn border border-black rounded-4">
                                                        Prev
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- End here -->
            </div>