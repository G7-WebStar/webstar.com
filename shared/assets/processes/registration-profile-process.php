<?php
session_start();

// fetch programs for dropdown
$programQuery = "SELECT programID, programName FROM program ORDER BY programName ASC";
$programResult = executeQuery($programQuery);

$errorMessages = [
    "fieldsRequired" => "Please fill in all required fields."
];

$error = "";
$formData = []; // to store previous input

if (isset($_SESSION['alert'])) {
    $error = $_SESSION['alert'];
    unset($_SESSION['alert']);
}

// Check if there’s old form data
if (isset($_SESSION['formData'])) {
    $formData = $_SESSION['formData'];
    unset($_SESSION['formData']);
}

if (isset($_POST['nextBtn'])) {
    $firstName = trim($_POST['firstName']);
    $middleName = trim($_POST['middleName']);
    $lastName = trim($_POST['lastName']);
    $userName = strtolower($_POST['userName']);
    $studentID = strtoupper($_POST['studentID']);
    $programID = $_POST['program'];
    $gender = $_POST['gender'];
    $yearLevel = $_POST['yearLevel'];
    $yearSection = $_POST['yearSection'];

    // check required fields
    if (
        empty($firstName) || empty($lastName) || empty($userName) ||
        empty($studentID) || empty($programID) || empty($gender) ||
        empty($yearLevel) || empty($yearSection)
    ) {
        $_SESSION['alert'] = 'fieldsRequired';
        $_SESSION['formData'] = $_POST; // store previous input
        header("Location: registration-profile.php");
        exit;
    }

    if (!empty($_FILES['fileUpload']['name'])) {
        $htmlfileupload = $_FILES['fileUpload']['name'];
        $htmlfileuploadTMP = $_FILES['fileUpload']['tmp_name'];
        $htmlfolder = "shared/assets/pfp-uploads/";

        // append datetime to filename
        $ext = pathinfo($htmlfileupload, PATHINFO_EXTENSION);
        $nameOnly = pathinfo($htmlfileupload, PATHINFO_FILENAME);
        $datetime = date("YmdHis");
        $newFileName = $nameOnly . '_' . $datetime . '.' . $ext;

        // move file with new name
        move_uploaded_file($htmlfileuploadTMP, $htmlfolder . $newFileName);

        $profilePictureValue = "'$newFileName'";
    } else {
        $profilePictureValue = "DEFAULT";
    }

    if (!isset($_SESSION['userID'])) {
        header("Location: registration.php");
        exit();
    }

    $userID = $_SESSION['userID'];

    // insert user info gamit ang programID
    $nextQuery = "INSERT INTO userinfo 
        (userID, firstName, middleName, lastName, studentID, programID, gender, yearLevel, yearSection, profilePicture, createdAt) 
        VALUES 
        ('$userID', '$firstName', '$middleName', '$lastName', '$studentID', '$programID', '$gender', '$yearLevel', '$yearSection', $profilePictureValue, NOW())";
    $nextResult = executeQuery($nextQuery);

    if ($nextResult) {
        $userQuery = "UPDATE users SET userName = '$userName' WHERE userID = '$userID'";
        executeQuery($userQuery);

        header("Location: registration-next.php");
        exit();
    }

    // formData
    $_SESSION['formData'] = $_POST;
    header("Location: registration-profile.php");
    exit();
}
