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

    $_SESSION['userID'] = $userID;

    if ($nextResult) {
        $_SESSION['profile_setup_success'] = true;
    }
}

if (isset($_POST['letsGo'])) {
    $userID = $_SESSION['userID'];

    $userQuery = "SELECT * FROM users WHERE userID = $userID";
    $userResult = executeQuery($userQuery);
    $userRow = mysqli_fetch_assoc($userResult);

    $role = $userRow['role'];

    $_SESSION['userID'] = $userID;
    $_SESSION['role'] = $role;
    
    header("Location: course-join.php");
    exit();
}

?>