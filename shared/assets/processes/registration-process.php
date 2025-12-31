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
    "fieldsRequired"   => "All fields are required. Please fill in all fields."
];

// Keep form data if needed
if (isset($_SESSION['formData'])) {
    $formData = $_SESSION['formData'];
    unset($_SESSION['formData']);
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'shared/assets/phpmailer/src/Exception.php';
require 'shared/assets/phpmailer/src/PHPMailer.php';
require 'shared/assets/phpmailer/src/SMTP.php';

if (isset($_POST['signUpBtn'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // <-- Fix: Do not redirect on error, show it immediately
    if (empty($email) || empty($password) || empty($confirmPassword)) {
        $error = 'fieldsRequired';
    } else {
        // sql injection prevention
        $stmt = $conn->prepare("SELECT userID FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $checkEmailResult = $stmt->get_result();

        if ($checkEmailResult->num_rows > 0) {
            $error = 'emailExists';
        } elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
            $error = 'invalidPasswordFormat';
        } elseif ($password !== $confirmPassword) {
            $error = 'passwordMismatch';
        } else {
            $verificationCode = random_int(100000, 999999);
            $_SESSION['verificationCode'] = $verificationCode;
            $verificationCodeExpiry = date('Y-m-d H:i:s', time() + (5 * 60));
            $_SESSION['verificationCodeExpiry'] = $verificationCodeExpiry;

            $mail = new PHPMailer(true);

            $credentialQuery = "SELECT email, password FROM emailcredentials WHERE credentialID = 1";
            $credentialResult = executeQuery($credentialQuery);

            if ($credentialRow = mysqli_fetch_assoc($credentialResult)) {
                $smtpEmail = $credentialRow['email'];
                $smtpPassword = $credentialRow['password'];

                try {
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = $smtpEmail;
                    $mail->Password   = $smtpPassword;
                    $mail->SMTPSecure = 'tls';
                    $mail->Port       = 587;
                    $mail->setFrom('learn.webstar@gmail.com', 'Webstar');

                    $headerPath = __DIR__ . '/../img/email/email-header.png';
                    if (file_exists($headerPath)) {
                        $mail->AddEmbeddedImage($headerPath, 'emailHeader');
                    }
                    $footerPath = __DIR__ . '/../img/email/email-footer.png';
                    if (file_exists($footerPath)) {
                        $mail->AddEmbeddedImage($footerPath, 'emailFooter');
                    }

                    $mail->addAddress($email);

                    $mail->isHTML(true);
                    $mail->Subject = "Your Webstar LMS Verification Code";
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
                    echo "Email failed. Error: {$mail->ErrorInfo}";
                }

                $_SESSION['email'] = $email;
                $_SESSION['password'] = $hashedPassword;

                // Redirect only on success
                header("Location: email-verification.php");
                exit;
            }
        }
    }
}
?>
