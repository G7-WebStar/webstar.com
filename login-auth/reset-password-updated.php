<?php
session_start();
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WebStar | Reset Password Updated</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/reset-password-updated.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../shared/assets/img/webstar-icon.png">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />

</head>


<body>
    <div class="container min-vh-100 d-flex justify-content-center align-items-center">
        <div class="row">
            <div class="col-12 col-md-6 col-lg-4 p-3 rounded-4 login-container border-blue mx-auto">
                <div class="container d-flex justify-content-center py-sm-4 py-3">
                    <img src="../shared/assets/img/webstar-logo-black.png" class="img-fluid px-3 my-4 logo" width="275px">
                </div>

                <!-- Reset Password Heading -->
                <div class="container text-center mb-3">
                    <h1 class="reset-password-updated-heading">Password has been updated.</h1>
                </div>

                <!-- Reset Password Description -->
                <div class="container text-center mb-2">
                    <p class="reset-password--updated-description">You can now use your new password to log in next time.</p>
                </div>

                <!-- Lock Image -->
                <div class="container text-center mt-4 mb-4">
                    <img src="../shared/assets/img/resetPassword/Lock.png" class="img-fluid bottom-image" alt="Success" style="max-width: 200px;">
                </div>

                <!-- Back to Login Redirect -->
                <div class="container text-center text-small">
                    <?php
                    // Determine redirect URL based on role
                    $redirectUrl = '../login.php'; // default
                    if (isset($_SESSION['role'])) {
                        if ($_SESSION['role'] === 'professor') {
                            $redirectUrl = '../prof/index.php';
                        } elseif ($_SESSION['role'] === 'student') {
                            $redirectUrl = '../index.php';
                        }
                    }
                    ?>
                    <a href="<?= $redirectUrl ?>" class="text-decoration-none">
                        <span class="back-to-login">
                            Continue
                            <span class="material-symbols-outlined arrow-back-icon">arrow_forward</span>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            var passwordField = document.getElementById(fieldId);
            var toggleBtn = passwordField.nextElementSibling.nextElementSibling; // skip label, then button
            var showIcon = toggleBtn.querySelector('.eye-icon.show');
            var hideIcon = toggleBtn.querySelector('.eye-icon.hide');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                showIcon.style.display = 'block';
                hideIcon.style.display = 'none';
            } else {
                passwordField.type = 'password';
                showIcon.style.display = 'none';
                hideIcon.style.display = 'block';
            }
        }


        function closeAlert() {
            document.getElementById('errorModal').style.display = 'none';
        }

        // modal and button animation
        // function closeAlert() {
        //     const modal = document.getElementById('errorModal');
        //     modal.style.display = 'none';
        //     document.querySelector('.btn-login').classList.remove('modal-shift');
        // }

        // Add this when the modal is displayed
        window.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('errorModal');
            if (modal && modal.style.display !== 'none') {
                document.querySelector('.btn-login').classList.add('modal-shift');
            }
        });
    </script>
</body>

</html>