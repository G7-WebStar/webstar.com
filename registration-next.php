<?php $activePage = 'signUp'; ?>
<?php
include("shared/assets/database/connect.php");
include("shared/assets/processes/registration-next-process.php");
?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Registration | Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/registration-profile.css">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />
    
</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center profile-setup-container" style="background: var(--black);">
        <div class="registration-container">

            <!-- Logo on top for all screens -->
            <div class="w-100 d-none d-md-flex flex-column justify-content-center header-part">
                <p class="text-bold mb-1" style="font-size: 28px; color: var(--black); margin-top: 5px;">Set up your profile</p>
                <p class="text-med text-12 mb-0" style="color: var(--black);">
                    Complete your profile to help others recognize you and <br> connect with you on the platform
                </p>
            </div>

            <div class="d-block d-md-none text-center p-3">
                <!-- Optional logo -->
                <div class="mb-3 mt-0 text-center mobile-logo">
                    <img src="shared/assets/img/webstar-logo-black.png" alt="Logo" class="img-fluid" style="width: 200px;">
                </div>

                <!-- Move the heading & instruction here -->
                <p class="text-bold mb-1 mt-5" style="font-size: 24px; color: var(--black);">
                    Set up your profile
                </p>
                <p class="text-med text-12 mb-3" style="color: var(--black);">
                    Complete your profile to help others recognize you and connect with you on the platform
                </p>
            </div>

            <!-- Registration Form -->
            <div class="container p-4 d-flex justify-content-center">
                <form method="POST" action="" class="text-reg row justify-content-center" style="width: 100%; max-width: 900px;">

                    <!-- Left: Registration Form -->
                    <div class="col-md-7 ps-md-5 ps-3 d-flex flex-column form-col">
                        <div>
                            <div class="mb-3 mt-2 text-med text-16 text-center text-md-start" style="color: var(--black);">
                                Contact Information
                            </div>

                            <!-- School Email -->
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control custom-input" id="schoolEmail" name="schoolEmail" placeholder="School Email" required>
                                <label for="schoolEmail">School Email</label>
                            </div>

                            <div class="mb-3 pt-2 text-med text-16 text-center text-md-start" style="color: var(--black);">
                                Social Media (<i>optional</i>)
                            </div>

                            <!-- Social Links -->
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control custom-input" id="facebookLink" name="facebookLink" placeholder="Facebook Profile Link">
                                <label for="facebookLink">Facebook Profile Link</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="text" class="form-control custom-input" id="linkedinLink" name="linkedinLink" placeholder="Linkedin Profile Link">
                                <label for="linkedinLink">Linkedin Profile Link</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="text" class="form-control custom-input" id="instagramLink" name="instagramLink" placeholder="Instagram Profile Link">
                                <label for="instagramLink">Github Portfolio Link</label>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Finish Button -->
                    <div class="col-md-5 text-center d-flex flex-column align-items-center justify-content-end ps-md-4">
                        <button type="submit" name="nextBtn"
                            class="text-sbold text-12 btn btn-finish mt-3 mb-3 ms-md-5">
                            Finish
                        </button>
                    </div>
                </form>
            </div>

            <!-- Modal (outside form) -->
            <div class="modal fade" id="finishModal" tabindex="-1" aria-labelledby="finishModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <div class="modal-header">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body text-center">
                            <img src="shared/assets/img/notebook.png"
                                alt="Logo"
                                style="width: 165px; height: 165px; margin-bottom: 8px;">
                            <div class="d-flex justify-content-center text-bold text-22 mt-1">You're all set!</div>
                            <div class="d-flex justify-content-center text-reg text-12">Head to your dashboard to set up your first course.</div>
                        </div>

                        <div class="modal-footer">
                            <a href="course-join.php" class="text-sbold btn btn-go-custom">
                                Let's go!
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

    <?php if (isset($_SESSION['profile_setup_success'])): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var myModal = new bootstrap.Modal(document.getElementById('finishModal'));
                myModal.show();
            });
        </script>
    <?php unset($_SESSION['profile_setup_success']);
    endif; ?>

</body>

</html>