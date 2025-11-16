<?php
include('../database/connect.php');
include("prof-session-process.php");
$enrollmentID = $_GET['enrollmentID'];
$userID = $_GET['userID'];

$kickoutStudentQuery = "DELETE enrollments FROM enrollments 
INNER JOIN courses
    ON enrollments.courseID = courses.courseID
WHERE enrollments.enrollmentID = '$enrollmentID' AND courses.userID = '$userID'";
$kickoutStudentResult = executeQuery($kickoutStudentQuery);
