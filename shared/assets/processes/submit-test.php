<?php
include('../database/connect.php');
include('../processes/session-process.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    foreach ($data['answers'] as $answer) {
        $testQuestionID = $answer['testQuestionID'];
        $correctAnswerQuery = "SELECT correctAnswer FROM testquestions WHERE testID = 1 AND testQuestionID = $testQuestionID";
        $correctAnswerResult = executeQuery($correctAnswerQuery);
        $correctAnswer;
        if (mysqli_num_rows($correctAnswerResult) > 0) {
            $correctAnswerRow = mysqli_fetch_assoc($correctAnswerResult);
                $correctAnswer = $correctAnswerRow['correctAnswer'];
        }
        $testID = 1;
        $userAnswer = $answer['userAnswer'];
        $userID = $userID; // from session
        $isCorrect = ($userAnswer == $correctAnswer) ? 1 : 0;

        $insertQuery = "INSERT INTO testresponses (testID, testQuestionID, userID, userAnswer, isCorrect)
                        VALUES ('$testID', '$testQuestionID', '$userID', '$userAnswer', '$isCorrect')";
        executeQuery($insertQuery);
    }

    echo json_encode(["status" => "success"]);
}
