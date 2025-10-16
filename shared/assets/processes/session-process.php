<?php
session_start();

if (isset($_SESSION['userID'])) {
    $userID = $_SESSION['userID'];
} else {
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] != 'student' && $_SESSION['role'] == 'admin') {
    header("Location: prof/index.php");
    exit();
}
