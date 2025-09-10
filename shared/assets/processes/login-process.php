<?php
session_start();

$login_error = false; // default

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $conn = new mysqli("localhost", "root", "", "webstar");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($dbPassword);
        $stmt->fetch();

        if ($password === $dbPassword) {
            $_SESSION['email'] = $email;
            $_SESSION['userID'] = $userID;
            header("Location: index.php");
            exit();
        } else {
            $login_error = true; // wrong password
        }
    } else {
        $login_error = true; // email not found
    }

    $stmt->close();
    $conn->close();
}
?>