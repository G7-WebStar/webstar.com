<?php
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: ../login.php");
    exit;
} else {
    $userID = $_SESSION['userID'];
}

if ($_SESSION['role'] != 'admin' && $_SESSION['role'] == 'student') {
    header("Location: ../index.php");
    exit();
}
