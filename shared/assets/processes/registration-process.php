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

if (isset($_POST['signUpBtn'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Check if password mismatch
    if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*\W).{8,}$/', $password)) {
        $_SESSION['alert'] = 'invalidPasswordFormat';
    } elseif ($password !== $confirmPassword) {
        $_SESSION['alert'] = 'passwordMismatch';
    } else {
        $checkEmailSql = "SELECT * FROM users WHERE email = '$email'";
        $checkEmailResult = executeQuery($checkEmailSql);

        if (mysqli_num_rows($checkEmailResult) > 0) {
            $_SESSION['alert'] = 'emailExists';
        } else {
            $insertUser = "INSERT INTO users (email, password) VALUES ('$email', '$password')";
            if (executeQuery($insertUser)) {
                $userID = mysqli_insert_id($conn);
                $_SESSION['userID'] = $userID;
                header("Location: registration-profile.php");
                exit();
            } else {
                $_SESSION['alert'] = 'signupFailed';
            }
        }
    }
}
?>