<?php
include('../database/connect.php');
include("session-process.php");
date_default_timezone_set('Asia/Manila');

header('Content-Type: application/json');

if (!isset($_SESSION['userID'])) {
    echo json_encode(['success' => false, 'message' => 'You are not logged in.']);
    exit();
}

$userID = $_SESSION['userID'];

if (isset($_POST['access_code'])) {
    $code = strtoupper(trim($_POST['access_code']));

    // Check if course exists
    $checkCourseQuery = "SELECT * FROM courses WHERE code = '$code';";
    $checkCourseResult = mysqli_query($conn, $checkCourseQuery);

    if (mysqli_num_rows($checkCourseResult) > 0) {
        $course = mysqli_fetch_assoc($checkCourseResult);
        $courseID = $course['courseID'];

        // Check if user is already enrolled
        $checkEnrollmentQuery = "SELECT * FROM enrollments WHERE userID = '$userID' AND courseID = '$courseID';";
        $checkEnrollmentResult = mysqli_query($conn, $checkEnrollmentQuery);

        if (mysqli_num_rows($checkEnrollmentResult) > 0) {
            echo json_encode(['success' => false, 'message' => 'You are already enrolled in this course.']);
            exit();
        } else {
            // Get user yearSection
            $selectUserQuery = "SELECT yearSection FROM userinfo WHERE userID = '$userID';";
            $selectUserResult = mysqli_query($conn, $selectUserQuery);
            $user = mysqli_fetch_assoc($selectUserResult);
            $yearSection = $user['yearSection'];

            // Enroll user
            $enrollQuery = "INSERT INTO enrollments (userID, courseID, yearSection) VALUES ('$userID', '$courseID', '$yearSection')";
            if (mysqli_query($conn, $enrollQuery)) {
                $getEnrollmentIDQuery = "SELECT enrollmentID FROM enrollments WHERE userID = '$userID' AND courseID = '$courseID'";
                $getEnrollmentIDResult = executeQuery($getEnrollmentIDQuery);
                if (mysqli_num_rows($getEnrollmentIDResult) > 0) {
                    $enrollentIDRow = mysqli_fetch_assoc($getEnrollmentIDResult);
                    $enrollmentID = $enrollentIDRow['enrollmentID'];
                    executeQuery("INSERT INTO leaderboard (enrollmentID, xpPoints) VALUES ('$enrollmentID', '0')");
                }
                echo json_encode(['success' => true, 'message' => 'Successfully enrolled!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Enrollment failed. Try again later.']);
            }
            exit();
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid access code.']);
        exit();
    }
}
