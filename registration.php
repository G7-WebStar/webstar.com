<?php $activePage = 'signUp'; ?>
<?php
include("shared/assets/database/connect.php");
include("shared/assets/processes/registration-process.php");
?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/registration.css">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />
    
</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center" style="background: var(--black);">
        <div class="row w-100 h-100 registration-container">
            <!-- Logo -->
            <div class="col-md-6 d-flex flex-column justify-content-center align-items-center p-0 registration-logo-col">
                <div class="w-100 d-flex justify-content-center align-items-center" style="height: 100%;">
                    <img src="shared/assets/img/webstar-logo-black.png" class="img-fluid logo" style="max-width: 270px;">
                </div>
            </div>

            <!-- Registration Form -->
            <div class="col-md-7 d-flex flex-column justify-content-center align-items-center p-4 h-100 registration-form-col" style="background: var(--dirtyWhite);">
                <div class="w-100" style="max-width: 400px;">
                    <div class="text-center mb-3 mt-4 pt-3 registration-title text-bold text-30" style="color: var(--black);">Create an account</div>

                    <form method="POST" action="" class="text-reg mx-auto custom-form" style="max-width: 315px;">
                        <div class="form-floating mb-3">
                            <input type="email" name="email" class="form-control"
                                placeholder="Email" pattern=".+@gmail\.com"
                                title="Only Gmail addresses allowed"
                                value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                            <label for="email">Email</label>
                        </div>

                        <div class="password-wrapper mb-3 form-floating ">
                            <input type="password" name="password" id="password" class="form-control"
                                placeholder="Password"
                                value="<?php echo isset($_POST['password']) ? htmlspecialchars($_POST['password']) : ''; ?>" required>
                            <label for="password">Password</label>
                            <i class="fas fa-eye toggle-password" onclick="togglePassword('password', this)"
                                style="font-size: 0.85rem; color:#6c757d; position:absolute; right:15px; top:50%; transform: translateY(-50%); cursor:pointer;"></i>
                        </div>

                        <div class="password-wrapper mb-3 form-floating">
                            <input type="password" name="confirmPassword" id="confirmPassword" class="form-control"
                                placeholder="Confirm password"
                                value="<?php echo isset($_POST['confirmPassword']) ? htmlspecialchars($_POST['confirmPassword']) : ''; ?>" required>
                            <label for="confirm password">Confirm password</label>
                            <i class="fas fa-eye toggle-password" onclick="togglePassword('confirmPassword', this)"
                                style="font-size: 0.85rem; color:#6c757d; position:absolute; right:15px; top:50%; transform: translateY(-50%); cursor:pointer;"></i>
                        </div>

                        <?php if (!empty($error) && isset($errorMessages[$error])) { ?>
                            <div class="text-reg alert alert-danger alert-dismissible fade show d-flex align-items-center mb-3"
                                role="alert" style="font-size: 11px; border-radius: 8px; padding: 0.5rem 0.75rem;">
                                <i class="fa-solid fa-triangle-exclamation me-2" style="font-size: 13px;"></i>
                                <span class="flex-grow-1"><?= $errorMessages[$error]; ?></span>
                            </div>
                        <?php } ?>

                        <button type="submit" name="signUpBtn" class="d-block mx-auto btn btn-primary text-sbold mb-4 mt-3"
                            style="background-color: var(--primaryColor); border: 1px solid var(--black); color: var(--black); width: 100%; font-size: 18px; border-radius: 8px;">
                            Sign up
                        </button>
                    </form>
                    <div class="text-14 text-center mb-4">
                        <span class="text-reg">Already have an account?</span>
                        <a href="login.php" class="text-decoration-none text-sbold" style="color: var(--black);">
                            Log in
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === "password") {
                input.type = "text";
                icon.classList.replace("fa-eye", "fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.replace("fa-eye-slash", "fa-eye");
            }
        }
    </script>
</body>

</html>