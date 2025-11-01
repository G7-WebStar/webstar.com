<?php
session_start();

// fetch programs for dropdown
$programQuery = "SELECT programID, programName FROM program ORDER BY programName ASC";
$programResult = executeQuery($programQuery);

if (isset($_POST['nextBtn'])) {
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'];
    $lastName = $_POST['lastName'];
    $userName = strtolower ($_POST['userName']);
    $studentID = strtoupper($_POST['studentID']);
    $programID = $_POST['program'];
    $gender = $_POST['gender'];
    $yearLevel = $_POST['yearLevel'];
    $yearSection = $_POST['yearSection'];

    $htmlfileupload = $_FILES['fileUpload']['name'];
    $htmlfileuploadTMP = $_FILES['fileUpload']['tmp_name'];

    $htmlfolder = "shared/assets/pfp-uploads/";
    move_uploaded_file($htmlfileuploadTMP, $htmlfolder . $htmlfileupload);

    if (!isset($_SESSION['userID'])) {
        header("Location: registration.php");
        exit();
    }

    $userID = $_SESSION['userID'];

    // insert user info gamit ang programID (wala nang SELECT)
    $nextQuery = "INSERT INTO userinfo 
        (userID, firstName, middleName, lastName, studentID, programID, gender, yearLevel, yearSection, profilePicture, createdAt) 
        VALUES 
        ('$userID', '$firstName', '$middleName', '$lastName', '$studentID', '$programID', '$gender', '$yearLevel', '$yearSection', '$htmlfolder$htmlfileupload', NOW())";
    $nextResult = executeQuery($nextQuery);

    if ($nextResult) {
        $userQuery = "UPDATE users SET userName = '$userName' WHERE userID = '$userID'";
        executeQuery($userQuery);

        header("Location: registration-next.php");
        exit();
    }

    header("Location: registration-profile.php");
    exit();
}
?>