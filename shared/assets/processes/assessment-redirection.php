<?php
include('../database/connect.php');
include('../processes/session-process.php');

$selectInProgressQuery = "SELECT todo.*, tests.testID FROM todo INNER JOIN tests ON todo.assessmentID = tests.assessmentID WHERE todo.userID = '$userID' AND todo.status = 'Pending' AND todo.timeStart IS NOT null";
$selectInProgressResult = executeQuery($selectInProgressQuery);
