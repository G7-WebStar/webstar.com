<?php
session_start();
include('../shared/assets/database/connect.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../shared/assets/phpmailer/src/Exception.php';
require '../shared/assets/phpmailer/src/PHPMailer.php';
require '../shared/assets/phpmailer/src/SMTP.php';

// LOGOUT
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: ../login.php");
    exit();
}

// Define error messages
$errorMessages = [
    "mismatch" => "Passwords do not match. Please try again.",
    "emptyFields" => "Please fill in both password fields.",
    "updateFail" => "Something went wrong while updating your password. Please try again.",
];

// For alert handling
if (isset($_SESSION['alert'])) {
    $error = $_SESSION['alert'];
    unset($_SESSION['alert']); // one-time only
}

if (isset($_POST['reset'])) { // Reset password button
    $email = $_SESSION['email'] ?? null;
    $newPassword = trim($_POST['password']);
    $confirm = trim($_POST['confirmPassword']);

    if (empty($newPassword) || empty($confirm)) {
        $_SESSION['alert'] = "emptyFields";
        header("Location: reset-password.php");
        exit();
    } elseif ($newPassword !== $confirm) {
        $_SESSION['alert'] = "mismatch";
        header("Location: reset-password.php");
        exit();
    } else {
        $roleQuery = "SELECT role FROM users WHERE email = '$email'";
        $roleResult = executeQuery($roleQuery);
        $roleRow = mysqli_fetch_assoc($roleResult);
        $role = $roleRow['role'] ?? null;

        $_SESSION['role'] = $role;

        if ($role === 'professor') {
            $update = "UPDATE users SET password = '$newPassword', status = 'active' WHERE email = '$email'";
        } else {
            $update = "UPDATE users SET password = '$newPassword' WHERE email = '$email'";
        }

        $result = executeQuery($update);

        if ($result) {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'learn.webstar@gmail.com';
                $mail->Password = 'mtls vctd rhai cdem';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;
                $mail->setFrom('learn.webstar@gmail.com', 'Webstar');
                $headerPath = __DIR__ . '/../shared/assets/img/email/email-header.png';
                if (file_exists($headerPath)) {
                    $mail->AddEmbeddedImage($headerPath, 'emailHeader');
                }
                $footerPath = __DIR__ . '/../shared/assets/img/email/email-footer.png';
                if (file_exists($footerPath)) {
                    $mail->AddEmbeddedImage($footerPath, 'emailFooter');
                }
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = "Password Reset Successful";
                $mail->Body = '<div style="font-family: Arial, sans-serif; background-color:#f4f6f7; padding: 0; margin: 0;">
                    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f7; padding: 40px 0;">
                        <tr>
                            <td align="center">
                                <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.1);">
                                    <tr>
                                        <td align="center" style="padding: 0;">
                                            <img src="cid:emailHeader" alt="Webstar Header" style="width:600px; height:auto; display:block;">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 30px;">
                                            <p style="font-size:15px; color:#333;">Hi <strong>user</strong>,</p>

                                            <p style="font-size:15px; color:#333;">
                                                Your password has been successfully reset for <strong>Webstar</strong>.
                                            </p>

                                            <p style="font-size:15px; color:#333;">
                                                Your new password has been successfully changed.
                                            </p>

                                            <p style="font-size:15px; color:#333;">
                                                If you didn’t request this change, please contact our support team immediately.
                                            </p>

                                            <p style="font-size:15px; color:#333;">Thank you for keeping your account secure!</p>

                                            <p style="margin-top:30px; color:#333;">
                                                Warm regards,<br>
                                                <strong>The Webstar Team</strong><br>
                                            </p>

                                            <div style="text-align:center; font-size:13px; color:#888; margin-top:20px;">
                                                Telefax: (043) 784-3812 | learn.webstar@gmail.com
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center" style="padding: 0;">
                                            <img src="cid:emailFooter" alt="Webstar Footer" style="width:600px; height:auto; display:block; border:0; outline:none; text-decoration:none;" />
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>';
                $mail->send();
            } catch (Exception $e) {
                // skip email error
            }
            header("Location: reset-password-updated.php");
            exit();
        } else {
            $_SESSION['alert'] = "updateFail";
            header("Location: reset-password.php");
            exit();
        }
    }
}
?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WebStar | Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/reset-password.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../shared/assets/img/webstar-icon.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />

</head>


<body>
    <?php if (isset($_SESSION['success'])): ?>
        <div id="successToast" class="position-absolute top-0 start-50 translate-middle-x pt-5 pt-md-1 d-flex flex-column align-items-center"
            style="z-index:1100; pointer-events:none;">
            <div class="alert alert-success mb-2 shadow-lg text-med text-12
            d-flex align-items-center justify-content-center gap-2 px-3 py-2"
                role="alert"
                style="border-radius:8px; display:flex; align-items:center; gap:8px; padding:0.5rem 0.75rem; text-align:center; background-color:#d1e7dd; color:#0f5132;">
                <i class="bi bi-check-circle-fill fs-6" style="color: var(--black);"></i>
                <span style="color: var(--black);"><?= $_SESSION['success']; ?></span>
            </div>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="container min-vh-100 d-flex justify-content-center align-items-center">
        <div class="row">
            <div class="col-12 col-md-6 col-lg-4 p-3 rounded-4 login-container border-blue mx-auto">
                <div class="container d-flex justify-content-center py-sm-4 py-3">
                    <img src="../shared/assets/img/webstar-logo-black.png" class="img-fluid px-3 my-4 logo" width="275px">
                </div>

                <!-- Reset Password Heading -->
                <div class="container text-center mb-3">
                    <h1 class="reset-password-heading">Reset password</h1>
                </div>

                <!-- Reset Password Description -->
                <div class="container text-center mb-2">
                    <p class="reset-password-description">Choose a new password that hasn’t been used before.</p>
                </div>


                <!-- Login Form -->
                <form method="POST" action="">
                    <div class="container login-form">
                        <div class="form-floating pt-1 pt-md-3 pb-1 position-relative">
                            <input type="password" name="password" class="form-control rounded-4 border-blue"
                                id="newPassword" style="padding-left: 20px;" placeholder="Password" required>
                            <label for="newPassword">
                                <div class="pt-2 pt-md-3 px-2">New Password</div>
                            </label>
                            <button type="button" class="password-toggle-btn" onclick="togglePassword('newPassword')">
                                <i class="fas fa-eye eye-icon show" style="display: none;"></i>
                                <i class="fas fa-eye-slash eye-icon hide" style="display: block;"></i>
                            </button>
                        </div>
                        <div class="form-floating pt-1 pt-md-2 pb-1 position-relative">
                            <input type="password" name="confirmPassword" class="form-control rounded-4 border-blue"
                                id="confirmPassword" style="padding-left: 20px;" placeholder="Password" required>
                            <label for="confirmPassword">
                                <div class="pt-2 pt-md-2 px-2">Confirm Password</div>
                            </label>
                            <button type="button" class="password-toggle-btn" onclick="togglePassword('confirmPassword')">
                                <i class="fas fa-eye eye-icon show" style="display: none;"></i>
                                <i class="fas fa-eye-slash eye-icon hide" style="display: block;"></i>
                            </button>
                        </div>
                    </div>
                    <br class="mobile-only">

                    <!-- Error Message -->
                    <div class="container login-form py-0">
                        <?php if (!empty($error) && isset($errorMessages[$error])) { ?>
                            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mt-2 mb-0"
                                role="alert"
                                style="font-size: 11px; border-radius: 8px; padding: 0.5rem 0.75rem; text-align:left;">
                                <i class="fa-solid fa-triangle-exclamation me-2" style="font-size: 13px;"></i>
                                <span class="flex-grow-1"><?= $errorMessages[$error]; ?></span>
                            </div>
                        <?php } ?>
                    </div>

                    <!-- Reset Password Button -->
                    <div class="container d-flex justify-content-center">
                        <button type="submit" name="reset"
                            class="btn btn-reset text-dark rounded-4 px-4 my-md-4 my-3 mx-auto border-blue"
                            style="width: 73%;">
                            Reset password
                        </button>
                    </div>
                </form>

                <!-- Back to Login Redirect -->
                <div class="container text-center text-small">
                    <a href="reset-password.php?logout=true" class="text-decoration-none">
                        <span class="back-to-login">
                            <span class="material-symbols-outlined arrow-back-icon">arrow_back</span>
                            Back to login
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Toasts -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toastEl = document.getElementById('successToast');
            if (toastEl) {
                const toast = new bootstrap.Toast(toastEl, {
                    delay: 4000
                });
                toast.show();
            }
        });
    </script>
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
    </script>
</body>

</html>