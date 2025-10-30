<?php
session_start();

// kapag submit ng finish button
if (isset($_POST['nextBtn'])) {
    $schoolEmail   = $_POST['schoolEmail'];
    $facebookLink  = $_POST['facebookLink'];
    $linkedinLink  = $_POST['linkedinLink'];
    $githubLink = $_POST['githubLink'];

    if (!isset($_SESSION['userID'])) {
        header("Location: registration.php");
        exit();
    }

    $userID = $_SESSION['userID'];

    // save sa DB
    $nextQuery = "UPDATE userinfo SET 
                    schoolEmail   = '$schoolEmail',
                    facebookLink  = '$facebookLink',
                    linkedinLink  = '$linkedinLink',
                    githubLink = '$githubLink'
                  WHERE userID = '$userID'";

    $nextResult = executeQuery($nextQuery);

    if ($nextResult) {
        $_SESSION['profile_setup_success'] = true;
    }
}
?>