<?php
include('../database/connect.php');
$assessmentID = $_GET['assessmentID'];

$checkArchiveStatusQuery = "SELECT* FROM assessments WHERE assessmentID = $assessmentID";
$checkArchiveStatusResult = executeQuery($checkArchiveStatusQuery);

$archiveStatus = mysqli_fetch_assoc($checkArchiveStatusResult);

if ($archiveStatus['isArchived'] == 0) {
    $archiveAssessmentQuery = "UPDATE assessments SET isArchived ='1' WHERE assessmentID = $assessmentID";
    $archiveAssessmentResult = executeQuery($archiveAssessmentQuery);
} else {
    $archiveAssessmentQuery = "UPDATE assessments SET isArchived ='0' WHERE assessmentID = $assessmentID";
    $archiveAssessmentResult = executeQuery($archiveAssessmentQuery);
}

if ($archiveAssessmentQuery) {
     $_SESSION['toast'] = [
            'type' => 'alert-success',
            'message' => 'Assignment assigned successfully!'
        ];
}
