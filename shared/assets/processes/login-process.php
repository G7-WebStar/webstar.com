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

    $stmt = $conn->prepare("SELECT userID, role, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userID, $role, $dbPassword);
        $stmt->fetch();

        if (password_verify($password, $dbPassword) || $password === $dbPassword) {
            $_SESSION['email'] = $email;
            $_SESSION['userID'] = $userID;
            $_SESSION['role'] = $role;

            if ($role === "admin") {
                header("Location: admin/index.php");
                exit();
            } elseif ($role === "professor") {
                // Check if new professor
                $stmtNew = $conn->prepare("SELECT status FROM users WHERE userID = ?");
                $stmtNew->bind_param("i", $userID);
                $stmtNew->execute();
                $stmtNew->bind_result($status);
                $stmtNew->fetch();
                $stmtNew->close();

                if ($status === "created") {
                    header("Location: login-auth/temporary-credentials.php");
                } else {
                    header("Location: prof/index.php");
                }
                exit();
            } else {
                // student
                header("Location: index.php");
                exit();
            }
        } else {
            $login_error = true;
        }
    } else {
        $login_error = true;
    }

    $stmt->close();
    $conn->close();
}
