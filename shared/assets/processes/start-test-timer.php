<?php
include('../database/connect.php');
include('../processes/session-process.php');
$testID = $_GET['testID'];

$checkIfStartedQuery = "SELECT todo.timeStart 
    FROM todo 
    INNER JOIN tests
        ON todo.assessmentID = tests.assessmentID
        WHERE todo.userID = '$userID' AND tests.testID = '$testID' AND todo.timeStart IS null";
$checkIfStartedResult = executeQuery($checkIfStartedQuery);

if (mysqli_num_rows($checkIfStartedResult) > 0) {
    $testStartQuery = "UPDATE todo 
        INNER JOIN tests 
            ON todo.assessmentID = tests.assessmentID 
        SET todo.timeStart = CURRENT_TIMESTAMP
        WHERE todo.userID = '$userID' AND tests.testID = '$testID' AND todo.timeStart IS null";
    $testStartResult = executeQuery($testStartQuery);
}
