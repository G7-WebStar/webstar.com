<?php
session_start();

// para maiwasan yung warning sa unang load
$error = "";

// define error messages
$errorMessages = [
    "invalidPasswordFormat" => "Password must be at least 8 characters long and include uppercase letters, lowercase letters, numbers, and special characters.",
    "passwordMismatch" => "Passwords do not match.",
    "emailExists"      => "The email address you entered is already registered.",
    "signupFailed"     => "Something went wrong. Please try again.",
];

if (isset($_SESSION['alert'])) {
    $error = $_SESSION['alert'];
    unset($_SESSION['alert']); // para 1-time lang mag-display
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'shared/assets/phpmailer/src/Exception.php';
require 'shared/assets/phpmailer/src/PHPMailer.php';
require 'shared/assets/phpmailer/src/SMTP.php';

if (isset($_POST['signUpBtn'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    $checkEmailSql = "SELECT * FROM users WHERE email = '$email'";
    $checkEmailResult = executeQuery($checkEmailSql);
    // Check if password mismatch
    if (mysqli_num_rows($checkEmailResult) > 0) {
        $_SESSION['alert'] = 'emailExists';
    } elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
        $_SESSION['alert'] = 'invalidPasswordFormat';
    } elseif ($password !== $confirmPassword) {
        $_SESSION['alert'] = 'passwordMismatch';
    } else {
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
            $mail->AddEmbeddedImage('shared/assets/img/webstar-logo-black.png', 'logoWebstar');

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
        } catch (Exception $e) {
            echo "Email failed. Error: {$mail->ErrorInfo}";
        }

        $_SESSION['email'] = $email;
        $_SESSION['password'] = $password;

        header("Location: email-verification.php");
    }
}
