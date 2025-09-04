<?php
session_start();

// Include database connection
include 'shared/assets/database/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $access_code = trim($_POST['access_code']);
    
    // Basic validation
    if (empty($access_code)) {
        $_SESSION['error'] = 'Please enter an access code.';
        header('Location: courseJoin.php');
        exit();
    }
    
    // Here you would typically validate the access code against your database
    // For now, we'll do a simple check
    if (strlen($access_code) >= 6) {
        // Success - redirect to course page or dashboard
        $_SESSION['success'] = 'Successfully enrolled in course!';
        header('Location: index.php');
        exit();
    } else {
        $_SESSION['error'] = 'Invalid access code. Please check and try again.';
        header('Location: courseJoin.php');
        exit();
    }
} else {
    // If not POST request, redirect back
    header('Location: courseJoin.php');
    exit();
}
?>

