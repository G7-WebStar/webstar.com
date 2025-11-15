<?php
include("../database/connect.php");
include("prof-session-process.php");
$assessmentID = $_GET['assessmentID'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    foreach ($data['studentID'] as $studentIDs) {
        $studentID = $studentIDs['studentID'];

        $returnAllQuery = "UPDATE todo SET status = 'Graded' WHERE assessmentID = '$assessmentID' AND userID = '$studentID' AND status != 'Graded'";
        $returnAllResult = executeQuery($returnAllQuery);
    }

    echo json_encode(["status" => "success"]);
}
