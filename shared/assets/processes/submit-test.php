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
}
