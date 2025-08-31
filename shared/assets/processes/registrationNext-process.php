<?php
session_start();

// kapag submit ng finish button
if (isset($_POST['nextBtn'])) {
    $schoolEmail   = $_POST['schoolEmail'];
    $contactNumber = $_POST['contactNumber'];
    $facebookLink  = $_POST['facebookLink'];
    $linkedinLink  = $_POST['linkedinLink'];
    $instagramLink = $_POST['instagramLink'];

    if (!isset($_SESSION['userID'])) {
        header("Location: registration.php");
        exit();
    }

    $userID = $_SESSION['userID'];

    // save sa DB
    $nextQuery = "UPDATE userinfo SET 
                    schoolEmail   = '$schoolEmail',
                    contactNumber = '$contactNumber',
                    facebookLink  = '$facebookLink',
                    linkedinLink  = '$linkedinLink',
                    instagramLink = '$instagramLink'
                  WHERE userID = '$userID'";

    $nextResult = executeQuery($nextQuery);

    if ($nextResult) {
        $_SESSION['profile_setup_success'] = true;
    }
}
?>