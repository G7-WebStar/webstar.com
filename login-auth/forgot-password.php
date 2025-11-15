<?php
session_start();
include('../shared/assets/database/connect.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../shared/assets/phpmailer/src/Exception.php';
require '../shared/assets/phpmailer/src/PHPMailer.php';
require '../shared/assets/phpmailer/src/SMTP.php';

$errorMessages = [
    "emailNotFound" => "The email you entered is not registered in our system.",
    "emailSendFail" => "Error sending email. Please try again later."
];

if (isset($_SESSION['alert'])) {
    $error = $_SESSION['alert'];
    unset($_SESSION['alert']); // para 1-time lang mag-display
}

if (isset($_POST['send'])) { // Send Code button
    $email = $_POST['email'];

    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = executeQuery($query);

    if (mysqli_num_rows($result) > 0) {
        // Generate and save code
        $verificationCode = random_int(100000, 999999);
        $_SESSION['verificationCode'] = $verificationCode;
        $verificationCodeExpiry = date('Y-m-d H:i:s', time() + (5 * 60));
        $_SESSION['verificationCodeExpiry'] = $verificationCodeExpiry;

        $_SESSION['email'] = $email; // save email for next step

        // --- Send via PHPMailer ---
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
            $mail->Subject = "Reset Password";
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
                                                We received a request to reset your passowrd for <strong>Webstar</strong>.
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

            $_SESSION['success'] = 'A verification code has been sent to your email.';

            header("Location: check-your-email.php");
            exit;
        } catch (Exception $e) {
            $error = "emailSendFail"; // trigger alert box in HTML
        }
    } else {
        $error = "emailNotFound"; // trigger alert box in HTML
    }
}
?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WebStar | Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/forgot-password.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../shared/assets/img/webstar-icon.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

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


                <!-- Forgot Password Heading -->
                <div class="container text-center mb-4">
                    <h1 class="forgot-password-heading">Forgot password?</h1>
                </div>

                <!-- Forgot Password Description -->
                <div class="container text-center mb-4">
                    <p class="forgot-password-description">A code will be sent to your email to help reset your
                        password.</p>
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
                    </div>
                    <br class="mobile-only">

                    <!-- Error Message -->
                    <?php if (!empty($error) && isset($errorMessages[$error])) { ?>
                        <div class="container login-form">
                            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mt-2 mb-0"
                                role="alert"
                                style="font-size: 11px; border-radius: 8px; padding: 0.5rem 0.75rem; text-align:left;">
                                <i class="fa-solid fa-triangle-exclamation me-2" style="font-size: 13px;"></i>
                                <span class="flex-grow-1"><?= $errorMessages[$error]; ?></span>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Code Button -->
                    <div class="container d-flex justify-content-center">
                        <button type="submit" name="send"
                            class="btn btn-code text-dark rounded-4 px-4 my-md-4 my-3 mx-auto border-blue"
                            style="width: 73%;">
                            Send Code
                        </button>
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

</body>

</html>