<?php
session_start();

// If user is not logged in, redirect to login page
if (!isset($_SESSION['userID']) || empty($_SESSION['userID'])) {
    header("Location: ../login.php");
    exit;
}

// Only allow admin users
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php"); // Or another page for unauthorized users
    exit;
}

// Assign userID for convenience
$userID = $_SESSION['userID'];
