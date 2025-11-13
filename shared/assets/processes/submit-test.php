<?php
include('../database/connect.php');
include('../processes/session-process.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    foreach ($data['answers'] as $answer) {
        $testID = $_GET['testID'];
        $testQuestionID = $answer['testQuestionID'];
        $correctAnswerQuery = "SELECT correctAnswer FROM testquestions WHERE testID = $testID AND testQuestionID = $testQuestionID";
        $correctAnswerResult = executeQuery($correctAnswerQuery);
        $correctAnswer;
        if (mysqli_num_rows($correctAnswerResult) > 0) {
            $correctAnswerRow = mysqli_fetch_assoc($correctAnswerResult);
            $correctAnswer = $correctAnswerRow['correctAnswer'];
        }
        $userAnswer = $answer['userAnswer'];
        $userID = $userID; // from session
        $isCorrect = (($userAnswer == $correctAnswer) || ($userAnswer == strtolower($correctAnswer))) ? 1 : 0;

        $insertQuery = "INSERT INTO testresponses (testID, testQuestionID, userID, userAnswer, isCorrect)
                        VALUES ('$testID', '$testQuestionID', '$userID', '$userAnswer', '$isCorrect')";
        executeQuery($insertQuery);
    }

    echo json_encode(["status" => "success"]);

    //Indicates in the DB that the test has been submitted and determines the amount of time to answer and submit the test
    $timeSpent = $data['timeSpent'];
    $updateTodoStatusQuery = "UPDATE todo
                                      INNER JOIN assessments
                                      	ON todo.assessmentID = assessments.assessmentID
                                      INNER JOIN courses
                                      	ON assessments.courseID = courses.courseID
                                      LEFT JOIN assignments
                                      	ON assignments.assessmentID = todo.assessmentID
                                      LEFT JOIN tests
                                      	ON tests.assessmentID = todo.assessmentID
                                      SET todo.status ='Submitted',
                                          todo.timeSpent = '$timeSpent',
                                          todo.updatedAt = CURRENT_TIMESTAMP
                                      WHERE todo.userID = '$userID' AND todo.status = 'Pending' AND assessments.type = 'Test' AND tests.testID = '$testID'";
    $updateTodoStatusResult = executeQuery($updateTodoStatusQuery);

    $scoreQuery = "SELECT COUNT(isCorrect) AS correct FROM testresponses WHERE isCorrect = '1' AND userID = '$userID' AND testID = '$testID'";
    $scoreResult = executeQuery($scoreQuery);
    $scoreRow = mysqli_fetch_assoc($scoreResult);
    $score = $scoreRow['correct'];

    $insertScoreQuery = "INSERT INTO scores (userID, testID, score) VALUES ('$userID','$testID','$score')";
    $insertScoreResult = executeQuery($insertScoreQuery);

    $assessmentIDQuery = "SELECT assessmentID FROM tests WHERE testID = '$testID'";
    $assessmentIDResult = executeQuery($assessmentIDQuery);
    $assessmentIDRow = mysqli_fetch_assoc($assessmentIDResult);
    $assessmentID = $assessmentIDRow['assessmentID'];

    $scoreIDQuery = "SELECT scoreID FROM scores WHERE testID = '$testID' AND userID = '$userID'";
    $scoreIDResult = executeQuery($scoreIDQuery);
    $scoreIDRow = mysqli_fetch_assoc($scoreIDResult);
    $scoreID = $scoreIDRow['scoreID'];

    $insertSubmissionQuery = "INSERT INTO submissions (userID, assessmentID, scoreID, isSubmitted) 
                                                VALUES ('$userID','$assessmentID','$scoreID', 'Yes')";
    $insertSubmissionResult = executeQuery($insertSubmissionQuery);

    $submissionIDQuery = "SELECT submissionID FROM submissions WHERE scoreID = '$scoreID'";
    $submissionIDResult = executeQuery($submissionIDQuery);
    $submissionIDRow = mysqli_fetch_assoc($submissionIDResult);
    $submissionID = $submissionIDRow['submissionID'];

    $submissionIDInScoresQuery = "UPDATE scores SET submissionID = '$submissionID'";
    $submissionIDInScoresResult = executeQuery($submissionIDInScoresQuery);

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

    $baseWebstars = 1 * $totalPoints;
    $correctBonusWebstars = $baseWebstars * ($score / $totalPoints);
    $timeFactor = 1 + ($timelimit - $getTimeSpent) / $timelimit;
    $finalWebstars = $baseWebstars + ($correctBonusWebstars * $timeFactor);
    $earnedWebstars = round($finalWebstars);

    $earnedWebstarsQuery = "INSERT INTO webstars (userID, assessmentID, sourceType, pointsChanged, dateEarned) VALUES ('$userID','$assessmentID','Tests','$earnedWebstars', CURRENT_TIME)";
    $earnedWebstarsResult = executeQuery($earnedWebstarsQuery);

    $getUserWebstarsQuery = "SELECT webstars FROM profile WHERE userID = '$userID'";
    $getUserWebstarsResult = executeQuery($getUserWebstarsQuery);
    $webstarRow = mysqli_fetch_assoc($getUserWebstarsResult);
    $webstars = $webstarRow['webstars'];

    $pointsChangedQuery = "SELECT pointsChanged FROM webstars WHERE userID = '$userID' AND assessmentID = '$assessmentID'";
    $pointsChangedResult = executeQuery($pointsChangedQuery);
    $pointsChangedRow = mysqli_fetch_assoc($pointsChangedResult);
    $pointsChanged = $pointsChangedRow['pointsChanged'];

    $newWebstars = $webstars + $pointsChanged;

    $updateCurrentWebstarsQuery = "UPDATE profile SET webstars = '$newWebstars' WHERE userID = '$userID'";
    $updateCurrentWebstarsResult = executeQuery($updateCurrentWebstarsQuery);
}
