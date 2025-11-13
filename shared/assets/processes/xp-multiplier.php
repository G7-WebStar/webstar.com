<?php
include('../database/connect.php');
include('../processes/session-process.php');
$testID = $_GET['testID'];

//Check multiplier affordability
$checkWebstarsQuery = "SELECT webstars FROM profile WHERE userID ='$userID'";
$checkWebstarsResult = executeQuery($checkWebstarsQuery);
$checkWebstarsRow = mysqli_fetch_assoc($checkWebstarsResult);
$ownedWebstars = $checkWebstarsRow['webstars'];

if ($ownedWebstars >= 1000) {
    //Get variables necessary for calculating finalXP
    $totalPointsQuery = "SELECT SUM(testQuestionPoints) AS totalPoints FROM testquestions WHERE testID = '$testID'";
    $totalPointsResult = executeQuery($totalPointsQuery);
    $totalPointsRow = mysqli_fetch_assoc($totalPointsResult);
    $totalPoints = $totalPointsRow['totalPoints'];

    $scoreQuery = "SELECT COUNT(isCorrect) AS correct FROM testresponses WHERE isCorrect = '1' AND userID = '$userID' AND testID = '$testID'";
    $scoreResult = executeQuery($scoreQuery);
    $scoreRow = mysqli_fetch_assoc($scoreResult);
    $score = $scoreRow['correct'];

    $timeFactorQuery = "SELECT tests.testTimeLimit, todo.timeSpent FROM tests INNER JOIN todo ON tests.assessmentID = todo.assessmentID WHERE tests.testID = '$testID'";
    $timeFactorResult = executeQuery($timeFactorQuery);
    $timeFactorRow = mysqli_fetch_assoc($timeFactorResult);
    $timelimit = $timeFactorRow['testTimeLimit'];
    $getTimeSpent = $timeFactorRow['timeSpent'];

    $baseXP = 10 * $totalPoints;
    $correctBonusXP =  $baseXP * ($score / $totalPoints);
    $timeFactor = 1 + ($timelimit - $getTimeSpent) / $timelimit;
    $finalXP = $baseXP + ($correctBonusXP * $timeFactor);

    //Get Assessment ID of current test
    $assessmentIDQuery = "SELECT assessmentID FROM tests WHERE testID = '$testID'";
    $assessmentIDResult = executeQuery($assessmentIDQuery);
    $assessmentIDRow = mysqli_fetch_assoc($assessmentIDResult);
    $assessmentID = $assessmentIDRow['assessmentID'];

    //Get Course ID of current assessment
    $courseIDQuery = "SELECT courseID FROM assessments WHERE assessmentID = '$assessmentID'";
    $courseIDResult = executeQuery($courseIDQuery);
    $courseIDRow = mysqli_fetch_assoc($courseIDResult);
    $courseID = $courseIDRow['courseID'];

    //Get Course ID of current assessment
    $getEnrollmentIDQuery = "SELECT enrollments.enrollmentID FROM leaderboard 
                       INNER JOIN enrollments
                        ON leaderboard.enrollmentID = enrollments.enrollmentID
                       WHERE enrollments.userID = '$userID' AND enrollments.courseID = '$courseID'";
    $getEnrollmentIDResult = executeQuery($getEnrollmentIDQuery);
    $enrollmentIDRow = mysqli_fetch_assoc($getEnrollmentIDResult);
    $enrollmentID = $enrollmentIDRow['enrollmentID'];

    //Get Current XP of student before applying multiplier
    $currentXPQuery = "SELECT xpPoints FROM leaderboard WHERE enrollmentID = '$enrollmentID'";
    $currentXPResult = executeQuery($currentXPQuery);
    $currentXPRow = mysqli_fetch_assoc($currentXPResult);
    $currentXP = $currentXPRow['xpPoints'];

    //Final XP
    $newXP = $currentXP + round($finalXP);

    //Update the final XP
    $xpMultiplierQuery = "UPDATE leaderboard SET xpPoints = '$newXP' WHERE enrollmentID = '$enrollmentID'";
    $xpMultiplierResult = executeQuery($xpMultiplierQuery);

    //Deduct price of multiplier from user funds
    $buyMultiplierQuery = "";
    $buyMultiplierResult = executeQuery($buyMultiplierQuery);

    //Record Usage of XP multiplier
    $recordMultiplierQuery = "INSERT INTO webstars (userID, assessmentID, sourceType, pointsChanged, dateEarned) 
                                        VALUES ('$userID', '$assessmentID', 'XP Multiplier Usage', '-1000', CURRENT_TIMESTAMP)";
    $recordMultiplierResult = executeQuery($recordMultiplierQuery);
} else {
    echo "<script>alert('Insufficient Webstars');</script>";
}
