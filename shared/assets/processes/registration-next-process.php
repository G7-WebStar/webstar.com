<?php
session_start();

$toastMessage = "";
if (isset($_SESSION['alert'])) {
    $toastMessage = $_SESSION['alert'];
    unset($_SESSION['alert']); // 1-time display
}

// kapag submit ng finish button
if (isset($_POST['nextBtn'])) {
    $schoolEmail   = $_POST['schoolEmail'];
    $facebookLink  = $_POST['facebookLink'];
    $linkedinLink  = $_POST['linkedinLink'];
    $githubLink = $_POST['githubLink'];

    if (empty($schoolEmail)) {
        $_SESSION['alert'] = "School Email is required.";
        $_SESSION['prevSchoolEmail'] = $schoolEmail;
        header("Location: registration-next.php");
        exit();
    }

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
