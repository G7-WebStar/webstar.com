<?php
if (isset($_SESSION['userID'])) {
    $userID = $_SESSION['userID'];
} else {
    header("Location: login.php");
    exit();
}
