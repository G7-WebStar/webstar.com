<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

session_start();

if (isset($_SESSION['userID'])) {
    $userID = $_SESSION['userID'];
} else {
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] == 'professor') {
    header("Location: prof/index.php");
    exit();
}

if ($_SESSION['role'] == 'admin') {
    header("Location: admin/index.php");
    exit();
}
