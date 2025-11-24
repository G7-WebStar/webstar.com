<?php
include('../database/connect.php');
include("prof-session-process.php");
$enrollmentID = $_GET['enrollmentID'];
$userID = $_GET['userID'];
$courseID = $_GET['$courseID'];

$studentIDQuery = "SELECT userID FROM enrollments WHERE enrollmentID = '$enrollmentID'";
$studentIDResult = executeQuery($studentIDQuery);
$studentIDRow = (mysqli_num_rows($studentIDResult) > 0) ? mysqli_fetch_assoc($studentIDResult) : null;
$studentID = ($studentIDRow == null) ? null : $studentIDRow['userID'];

$kickoutStudentQuery = "DELETE enrollments FROM enrollments 
INNER JOIN courses
    ON enrollments.courseID = courses.courseID
WHERE enrollments.enrollmentID = '$enrollmentID' AND courses.userID = '$userID'";
$kickoutStudentResult = executeQuery($kickoutStudentQuery);

$deleteLeaderboardEntryQuery = "DELETE leaderboard FROM leaderboard 
WHERE enrollmentID = '$enrollmentID'";
$deleteLeaderboardEntryResult = executeQuery($deleteLeaderboardEntryQuery);

$deleteTodoQuery = "DELETE todo FROM todo 
    WHERE userID = '$userID'
    AND assessmentID IN (
    SELECT assessmentID FROM assessments WHERE courseID = '$courseID'
    )";
$deleteTodoResult = executeQuery($deleteTodoQuery);

if ($deleteTodoQuery) {
    $_SESSION['toast'] = [
        'type' => 'alert-success',
        'message' => 'Student kicked out successfully!'
    ];
}

$_SESSION['activeTab'] = 'student';
header("Location: course-info.php?courseID=" . intval($_POST['courses'][0]));
exit();
