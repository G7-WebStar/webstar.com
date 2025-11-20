<?php
session_start();

$login_error = false; // default
$email_not_found = false;  // Email/username not found

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $conn = new mysqli("localhost", "root", "", "webstart");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $loginInput = trim($_POST['loginInput']); // email or username
    $password = $_POST['password'];

    // Check if loginInput matches email OR userName
    $stmt = $conn->prepare("SELECT userID, role, password, email FROM users WHERE email = ? OR userName = ?");
    $stmt->bind_param("ss", $loginInput, $loginInput);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userID, $role, $dbPassword, $emailFromDB);
        $stmt->fetch();

        if ($password === $dbPassword || password_verify($password, $dbPassword)) {
            // Store session using email from DB
            $_SESSION['email'] = $emailFromDB;
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
        $email_not_found = true;
    }

    $stmt->close();
    $conn->close();
}
?>
