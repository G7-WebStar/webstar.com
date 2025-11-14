<?php
include("../database/connect.php");
include("prof-session-process.php");
$assessmentID = $_GET['assessmentID'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $filters = $data['selected']['selected'];

    if (in_array($filters, ['Pending', 'Graded', 'Submitted', 'Missing'])) {
        $filterQuery = "SELECT todo.userID, userinfo.firstName, userinfo.middleName, userinfo.lastName, todo.status, submissions.submissionID FROM todo
                        INNER JOIN userinfo
                        	ON todo.userID = userinfo.userID
                        INNER JOIN assessments
                        	ON todo.assessmentID = assessments.assessmentID
                        INNER JOIN courses
                        	ON assessments.courseID = courses.courseID
                        INNER JOIN submissions
                            ON todo.userID = submissions.userID
                        WHERE courses.userID = '$userID' AND todo.assessmentID = '$assessmentID' AND submissions.assessmentID = '$assessmentID' AND todo.status = '$filters'";
        $filterResult = executeQuery($filterQuery);
    } else if (in_array($filters, ['Newest'])) {
        $filterQuery = "SELECT todo.userID, userinfo.firstName, userinfo.middleName, userinfo.lastName, todo.status, submissions.submissionID FROM todo
                        INNER JOIN userinfo
                        	ON todo.userID = userinfo.userID
                        INNER JOIN assessments
                        	ON todo.assessmentID = assessments.assessmentID
                        INNER JOIN courses
                        	ON assessments.courseID = courses.courseID
                        INNER JOIN submissions
                            ON todo.userID = submissions.userID
                        WHERE courses.userID = '$userID' AND todo.assessmentID = '$assessmentID' AND submissions.assessmentID = '$assessmentID'
                        ORDER BY todo.updatedAt DESC";
        $filterResult = executeQuery($filterQuery);
    } else if (in_array($filters, ['Oldest'])) {
        $filterQuery = "SELECT todo.userID, userinfo.firstName, userinfo.middleName, userinfo.lastName, todo.status, submissions.submissionID FROM todo
                        INNER JOIN userinfo
                        	ON todo.userID = userinfo.userID
                        INNER JOIN assessments
                        	ON todo.assessmentID = assessments.assessmentID
                        INNER JOIN courses
                        	ON assessments.courseID = courses.courseID
                        INNER JOIN submissions
                            ON todo.userID = submissions.userID
                        WHERE courses.userID = '$userID' AND todo.assessmentID = '$assessmentID' AND submissions.assessmentID = '$assessmentID'
                        ORDER BY todo.updatedAt ASC";
        $filterResult = executeQuery($filterQuery);
    }

    $filter = [];
    while ($filterRow = mysqli_fetch_assoc($filterResult)) {
        $filter[] = $filterRow;
    }
    $response['results'] = $filter;
    echo json_encode($response);
}
