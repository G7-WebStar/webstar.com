<?php
$currentPage = basename($_SERVER['PHP_SELF']);

$selectInProgressQuery = "SELECT todo.*, tests.testID FROM todo 
INNER JOIN tests 
    ON todo.assessmentID = tests.assessmentID 
WHERE todo.userID = '$userID' AND todo.status = 'Pending' AND todo.timeStart IS NOT null";
$selectInProgressResult = executeQuery($selectInProgressQuery);

if ((mysqli_num_rows($selectInProgressResult) > 0) && ($currentPage != 'test.php')) {
    $testIDRow = mysqli_fetch_assoc($selectInProgressResult);
    $testID = $testIDRow['testID'];
    header("Location: test.php?testID=" . $testID);
}
