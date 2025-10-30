<?php
include('../shared/assets/database/connect.php');

session_start();

$errorMessages = [
    "invalidCode" => "Invalid code. Please try again.",
    "verificationCodeExpired" => "Your verification code has expired. Please resend a new one."
];

if (isset($_SESSION['alert'])) {
    $error = $_SESSION['alert'];
    unset($_SESSION['alert']); // para 1-time lang mag-display
}

if (isset($_POST['continue'])) {
    $email = $_SESSION['email'];
    $password = $_SESSION['password'];
    $verificationCode = $_POST['code'];

    $userQuery = "SELECT * FROM users WHERE email = '$email'";
    $userResult = executeQuery($userQuery);
    $userRow = mysqli_fetch_assoc($userResult);

    if (trim((string)$verificationCode) === trim((string)$_SESSION['verificationCode'])) {
        $currentTime = time();
        $expiryTime = strtotime($_SESSION['verificationCodeExpiry']);

        if ($currentTime <= $expiryTime) {
            // valid, not expired yet
            $_SESSION['email'] = $email;
            $_SESSION['userID'] = $userRow['userID'];
            $_SESSION['success'] = 'Your email has been successfully verified.';

            $updateUser = "UPDATE users SET password = '$password' WHERE email = '$email'";
            $updateUserResult = executeQuery($updateUser);

            header("Location: reset-password.php");
            exit;
        }
    } else {
        $_SESSION['alert'] = 'invalidCode';
        header("Location: check-your-email.php");
    }
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../shared/assets/phpmailer/src/Exception.php';
require '../shared/assets/phpmailer/src/PHPMailer.php';
require '../shared/assets/phpmailer/src/SMTP.php';

if (isset($_POST['resend'])) {
    $email = $_SESSION['email'];

    $verificationCode = random_int(100000, 999999);
    $_SESSION['verificationCode'] = $verificationCode;
    $verificationCodeExpiry = date('Y-m-d H:i:s', time() + (5 * 60));
    $_SESSION['verificationCodeExpiry'] = $verificationCodeExpiry;

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'learn.webstar@gmail.com';
        $mail->Password   = 'mtls vctd rhai cdem';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        $mail->AddEmbeddedImage('../shared/assets/img/webstar-logo-black.png', 'logoWebstar');

        $mail->setFrom('learn.webstar@gmail.com', 'Webstar');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = "Your Webstar LMS Verification Code";
        $mail->Body = '<div style="font-family: Arial, sans-serif; background-color:#f4f6f7; padding: 0; margin: 0;">
                    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f7; padding: 40px 0;">
                        <tr>
                            <td align="center">
                                <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.1);">
                                    <tr style="background-color: #FDDF94;">
                                        <td align="center" style="padding: 20px;">
                                            <img src="cid:logoWebstar" alt="Webstar Logo" style="height:80px;">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 30px;">
                                            <p style="font-size:15px; color:#333;">Hi <strong>user</strong>,</p>

                                            <p style="font-size:15px; color:#333;">
                                                We received a request to verify your account for <strong>Webstar</strong>.
                                            </p>

                                            <p style="font-size:15px; color:#333;">Your One-Time Password (OTP) is:</p>

                                            <h2 style="text-align:center; letter-spacing:5px; font-size:32px; color:#2C2C2C; margin:20px 0;">' . $verificationCode . '</h2>

                                            <p style="font-size:15px; color:#333;">
                                                Please enter this code on the verification page to complete your process. This code will expire in <strong>5 minutes</strong> for your security.
                                            </p>

                                            <p style="font-size:15px; color:#333;">
                                                If you didn’t request this verification, please ignore this email or contact our support team immediately.
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
                                    <tr style="background-color:#FDDF94;">
                                        <td align="center" style="padding:15px; color:black; font-size:13px;">
                                            © 2025 Webstar. All Rights Reserved.
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>';
        $mail->send();

        $_SESSION['success'] = 'A new verification code has been sent to your email.';
        header("Location: check-your-email.php");
        exit;
    } catch (Exception $e) {
        echo "Email failed. Error: {$mail->ErrorInfo}";
    }
}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WebStar | Check Your Email</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/check-your-email.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../shared/assets/img/webstar-icon.png">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />

</head>


<body>
    <?php if (isset($_SESSION['success'])): ?>
        <div class="toast-container position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 1100;">
            <div id="successToast"
                class="toast align-items-center text-bg-success border-0 show shadow-lg"
                role="alert"
                aria-live="assertive"
                aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body text-center">
                        <i class="fa-solid fa-circle-check me-2"></i>
                        <?= $_SESSION['success']; ?>
                    </div>
                    <button type="button"
                        class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
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


                <!-- Check Your Email Heading -->
                <div class="container text-center mb-4">
                    <h1 class="check-email-heading">Check your email</h1>
                </div>

                <!-- Check Your Email Description -->
                <div class="container text-center mb-4">
                    <p class="check-email-description">Input the code that was sent to your email.</p>
                </div>

                <!-- Code Input Form -->
                <form method="POST" action="">
                    <div class="container login-form py-md-0">
                        <div class="form-floating">
                            <input type="text" name="code" class="input-style form-control rounded-4 border-blue"
                                id="floatingInput" placeholder="0 0 0 0 0 0" required
                                inputmode="numeric" pattern="^[0-9]{6}$" maxlength="6"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,6);">
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

                    <!-- Code Button -->
                    <div class="container d-flex justify-content-center">
                        <button type="submit" name="continue"
                            class="btn btn-code text-dark rounded-4 px-4 my-md-4 my-3 mx-auto border-blue"
                            style="width: 73%;">
                            Continue
                        </button>
                    </div>

                    <div class="container d-flex justify-content-center mb-3 text-16">
                        <span class="text-reg" style="color: black;">Didn’t get the code?</span>
                        <button type="submit" name="resend" formnovalidate class="btn btn-link p-0 text-bold"
                            style="color: var(--black); text-decoration: none;"> Resend Code</button>
                    </div>
                </form>

                <!-- Back to Login Redirect -->
                <div class="container text-center text-small">
                    <a href="../login.php" class="text-decoration-none">
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
</body>

</html>