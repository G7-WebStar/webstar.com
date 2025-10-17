<?php $activePage = 'course'; ?>
<?php
include("shared/assets/processes/session-process.php");
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Comic+Neue&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="shared/assets/css/feedback.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />
    
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
                    <div class="container-fluid overflow-y-auto" style="max-height: 100vh;">
                        <div class="col">
                            <!-- Main panel -->
                            <div class="main px-4 py-5">
                                <h1 class="feedback-heading mb-3">Send Feedback</h1>

                                <div class="feedback-wrapper py-5">
                                    <p class="feedback-description mb-4">
                                        Let us know about your experience with WebStar. Your feedback helps us build a
                                        better learning platform for everyone.
                                    </p>

                                    <form class="feedback-form">
                                        <div class="mb-4 w-100">
                                            <textarea id="feedback" name="feedback" class="feedback-box form-control"
                                                rows="6" placeholder=""></textarea>
                                        </div>
                                    </form>
                                    <div class="text-end w-100" style="max-width: 50%;">
                                        <button type="button" class="btn btn-primary feedback-btn"
                                            data-bs-toggle="modal" data-bs-target="#cardModal">
                                            Submit
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal -->
                    <div class="modal fade" id="cardModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content custom-modal position-relative rounded-4 overflow-hidden">

                                <!-- Close Button -->
                                <button type="button"
                                    class="position-absolute top-0 end-0 m-3 p-0 bg-transparent border-0"
                                    data-bs-dismiss="modal" aria-label="Close"
                                    style="font-size: 1.5rem; color: var(--blue);">
                                    ✕
                                </button>

                                <!-- Divider Line Under X -->
                                <hr class="modal-divider mb-3 mt-5" style="color: var(--blue);">

                                <!-- Modal Body -->
                                <div class="maincontainer mb-2 px-5">
                                    <div class="d-flex flex-column flex-md-row align-items-center gap-4">
                                        <!-- Right Details -->
                                        <div class="d-flex justify-content-center align-items-center">
                                            <div class="mb-1 modalTitle text-center text-md-start" id="modalTitle">
                                                Feedback submitted
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