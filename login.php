<?php $activePage = 'login'; ?>
<?php
include("shared/assets/database/connect.php");
include("shared/assets/processes/login-process.php");
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WebStar | Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/login.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="shared/assets/img/webstar-icon.png">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />

</head>


<body>
    <div class="container min-vh-100 d-flex justify-content-center align-items-center ">
        <div class="row">
            <div class="col-12 col-md-6 col-lg-4 p-3 rounded-4 login-container border-blue mx-auto">
                <div class="container d-flex justify-content-center py-sm-4 py-3">
                    <img src="shared/assets/img/webstar-logo-black.png" class="img-fluid px-3 mb-2 mb-sm-4 mt-0 mt-sm-4 logo" width="275px">
                </div>

                <!-- Login Form -->
                <form method="POST" action="">
                    <div class="container login-form py-md-2">
                        <div class="form-floating">
                            <input type="email" name="email" class="input-style form-control rounded-4 border-blue"
                                id="floatingInput" placeholder="name@example.com" required>
                            <label for="floatingInput">
                                <div class="px-2">Email</div>
                            </label>
                        </div>
                        <div class="form-floating pt-2 pt-md-3 pb-md-4 pb-2 position-relative">
                            <input type="password" name="password" class="form-control rounded-4 border-blue"
                                id="password" style="padding-left: 20px;" placeholder="Password" required>
                            <label for="password">
                                <div class="pt-2 pt-md-3 px-2">Password</div>
                            </label>
                            <button type="button" class="password-toggle-btn" onclick="togglePassword('password')">
                                <i class="fas fa-eye eye-icon show" style="display: none;"></i>
                                <i class="fas fa-eye-slash eye-icon hide" style="display: block;"></i>
                            </button>
                            <div class="forgot-password float-end mt-3">
                                <a href="login-auth/forgot-password.php" class="text-decoration-none text-dark highlight text-small">Forgot
                                    Password?</a>
                            </div>
                        </div>
                    </div>
                    <br class="mobile-only">

                    <?php if ($login_error): ?>
                        <!-- Bootstrap Icons (only include once in your layout) -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
                            <symbol id="exclamation-triangle-fill" viewBox="0 0 16 16">
                                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 
                                         1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 
                                         1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 
                                         0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 
                                         5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                            </symbol>
                        </svg>

                        <!-- Error Alert -->
                        <div class="alert alert-danger d-flex align-items-center alert-dismissible fade show custom-alert"
                            role="alert">
                            <svg class="bi flex-shrink-0 me-2" width="20" height="20" role="img" aria-label="Danger:">
                                <use xlink:href="#exclamation-triangle-fill" />
                            </svg>
                            <div class="alert-message mx-1">
                                Oops! Email or password incorrect.
                            </div>
                        </div>
                    <?php endif; ?>



                    <!-- Login Button -->
                    <div class="container d-flex justify-content-center">
                        <button type="submit" name="login"
                            class="btn btn-login text-dark rounded-4 px-4 my-md-4 my-3 mx-auto border-blue"
                            style="width: 73%;">
                            Sign in
                        </button>
                    </div>


                </form>

                <!-- Registration Redirect -->
                <div class="container text-center text-small">
                    <a href="registration.php" class="text-decoration-none">
                        <span class="text-dark">Don't have an account? </span>
                        <span class="fw-bold text-dark highlight">Sign up</span>
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