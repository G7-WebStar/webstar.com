<?php
$activePage = 'exam-info';
include('shared/assets/database/connect.php');
include("shared/assets/processes/session-process.php");
$testID = $_GET['testID'];

if ($testID == null) {
    header("Location: 404.html");
    exit();
}

// Check if the test belongs to the current user
$accessCheckQuery = "SELECT tests.testID 
                     FROM tests
                     INNER JOIN assessments ON tests.assessmentID = assessments.assessmentID
                     INNER JOIN todo ON assessments.assessmentID = todo.assessmentID
                     WHERE tests.testID = '$testID' AND todo.userID = '$userID'";
$accessCheckResult = executeQuery($accessCheckQuery);

if (mysqli_num_rows($accessCheckResult) <= 0) {
    header("Location: 404.html");
    exit();
}

$testExistenceQuery = "SELECT tests.testID, tests.testTimeLimit FROM tests 
INNER JOIN assessments 
    ON tests.assessmentID = assessments.assessmentID 
INNER JOIN todo 
    ON assessments.assessmentID = todo.assessmentID 
WHERE tests.testID = '$testID' AND todo.userID = '$userID'";
$testExistenceResult = executeQuery($testExistenceQuery);
$testTimeLimitRow = mysqli_fetch_assoc($testExistenceResult);
$testTimeLimit = $testTimeLimitRow['testTimeLimit'];

if (mysqli_num_rows($testExistenceResult) <= 0) {
    echo "Test doesn't exists.";
    exit();
}

$selectTestQuery = "SELECT assessments.assessmentTitle, DATE_FORMAT(assessments.deadline, '%b %e') AS assessmentDeadline, assessments.assessmentID FROM tests 
                    INNER JOIN assessments
                        ON tests.assessmentID = assessments.assessmentID
                    WHERE tests.testID = $testID";
$selectTestResult = executeQuery($selectTestQuery);
$assessmentIDRow = mysqli_fetch_assoc($selectTestResult);
$assessmentID = $assessmentIDRow['assessmentID'];

$scoreQuery = "SELECT SUM(testquestions.testQuestionPoints) AS correctPoints
               FROM testresponses
               INNER JOIN testquestions
                 ON testresponses.testQuestionID = testquestions.testQuestionID
               WHERE testresponses.isCorrect = 1 
                 AND testresponses.userID = '$userID' 
                 AND testresponses.testID = '$testID'";
$scoreResult = executeQuery($scoreQuery);
$scoreRow = mysqli_fetch_assoc($scoreResult);
$correctPoints = $scoreRow['correctPoints'] ?? 0;


$checkPendingQuery = "SELECT todo.status FROM todo 
INNER JOIN tests
    ON todo.assessmentID = tests.assessmentID
WHERE tests.testID = '$testID'";
$checkPendingResult = executeQuery($checkPendingQuery);
$statusRow = mysqli_fetch_assoc($checkPendingResult);
$status = $statusRow['status'];

$score = ($status == 'Pending') ? '-' : $correctPoints;

$btnText = 'Answer Now';
$btnLink = 'test.php';

if ($status == 'Pending') {
    $btnText = 'Answer Now';
    $btnLink = 'test.php';
} else if ($status == 'Submitted') {
    $btnText = 'View Submission';
    $btnLink = 'test-submitted.php';
} else if ($status == 'Returned') {
    $btnText = 'View Results';
    $btnLink = 'test-result.php';
}

$totalItemsQuery = "SELECT COUNT(*) AS totalItems FROM testquestions WHERE testID = '$testID'";
$totalItemsResult = executeQuery($totalItemsQuery);
$totalItemsRow = mysqli_fetch_assoc($totalItemsResult);
$totalItems = $totalItemsRow['totalItems'];

$totalPointsQuery = "SELECT SUM(testQuestionPoints) AS totalPoints FROM testquestions WHERE testID = '$testID'";
$totalPointsResult = executeQuery($totalPointsQuery);
$totalPointsRow = mysqli_fetch_assoc($totalPointsResult);
$totalPoints = $totalPointsRow['totalPoints'];

$testGuidelinesQuery = "SELECT generalGuidance FROM tests WHERE testID = '$testID'";
$testGuidelinesResult = executeQuery($testGuidelinesQuery);
$testGuidelinesRow = mysqli_fetch_assoc($testGuidelinesResult);
$testGuidelines = $testGuidelinesRow['generalGuidance'];

$getProfIDQuery = "SELECT userinfo.userID FROM userinfo
INNER JOIN courses
    ON userinfo.userID = courses.userID
INNER JOIN assessments 
    ON courses.courseID = assessments.courseID
INNER JOIN tests
    ON assessments.assessmentID = tests.assessmentID
INNER JOIN todo
    ON tests.assessmentID = todo.assessmentID
WHERE todo.userID = '$userID' AND tests.testID = '$testID'
";
$getProfIDResult = executeQuery($getProfIDQuery);
$profUserIDRow = mysqli_fetch_assoc($getProfIDResult);
$profUserID = $profUserIDRow['userID'];

$profInfoQuery = "SELECT * FROM userinfo WHERE userID = '$profUserID'";
$profInfoResult = executeQuery($profInfoQuery);

$assessmentCreationDateQuery = "SELECT DATE_FORMAT(createdAt, '%b %e, %Y %l:%i %p') AS creationDate FROM assessments WHERE assessmentID = '$assessmentID'";
$assessmentCreationDateResult = executeQuery($assessmentCreationDateQuery);
$assessmentCreationDateRow = mysqli_fetch_assoc($assessmentCreationDateResult);
$assessmentCreationDate = $assessmentCreationDateRow['creationDate'];

$profilePic = !empty($assignmentRow['profilePicture'])
    ? 'shared/assets/pfp-uploads/' . $assignmentRow['profilePicture']
    : 'shared/assets/pfp-uploads/defaultProfile.png';



?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Exam Info ✦ Webstar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="shared/assets/css/exam-info.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="shared/assets/img/webstar-icon.png">

    <style>
        .interactable:hover {
            cursor: pointer;
        }

        @media screen and (max-width: 767px) {
            .mobile-view {
                margin-bottom: 80px !important;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-0 p-md-3"
        style="background-color: var(--black);">

        <div class="row w-100">

            <?php include 'shared/components/sidebar-for-mobile.php'; ?>
            <?php include 'shared/components/sidebar-for-desktop.php'; ?>

            <div class="col main-container m-0 p-0 mx-0 mx-md-2 p-0 p-md-4 overflow-y-auto">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">
                    <?php include 'shared/components/navbar-for-mobile.php'; ?>

                    <div class="container-fluid py-3 overflow-y-auto row-padding-top">
                        <div class="row mb-3">
                            <div class="col-12 cardHeader p-3 mb-4">

                                <!-- DESKTOP VIEW -->
                                <div class="row desktop-header d-none d-sm-flex d-flex align-items-center">
                                    <div class="col-auto me-2">
                                        <a onclick="history.back()" class="text-decoration-none interactable">
                                            <i class="fa-solid fa-arrow-left text-reg text-16"
                                                style="color: var(--black);"></i>
                                        </a>
                                    </div>
                                    <?php
                                    if (mysqli_num_rows($selectTestResult) > 0) {
                                        mysqli_data_seek($selectTestResult, 0);
                                        while ($testTitle = mysqli_fetch_assoc($selectTestResult)) {
                                    ?>
                                            <div class="col">
                                                <div class="text-sbold text-25"><?php echo $testTitle['assessmentTitle']; ?>
                                                </div>
                                                <span class="text-reg text-18">Due
                                                    <?php echo $testTitle['assessmentDeadline']; ?></span>
                                            </div>
                                            <?php
                                            if ($status != 'Pending') {
                                            ?>
                                                <div class="col-auto text-end text-reg">
                                                    Score <div class="text-sbold text-25">
                                                        <?php echo $score; ?><span class="text-muted">/<?php echo $totalPoints; ?>
                                                    </div>
                                                </div>

                                            <?php
                                            }
                                            ?>
                                </div>

                                <!-- MOBILE VIEW -->
                                <div class="d-block d-sm-none mobile-assignment">
                                    <div class="mobile-top">
                                        <div class="arrow">
                                            <a onclick="history.back()" class="text-decoration-none">
                                                <i class="fa-solid fa-arrow-left text-reg text-16"
                                                    style="color: var(--black);"></i>
                                            </a>
                                        </div>

                                        <div class="title text-sbold text-25"
                                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block; max-width: 220px;">
                                            <?php echo $testTitle['assessmentTitle']; ?>
                                        </div>

                                    </div>
                                    <?php
                                            if ($status != 'Pending') {
                                    ?>
                                        <div class="graded text-reg text-18 mt-4">Score</div>
                                        <div class="score text-sbold text-25">
                                            <?php echo $score; ?>/<span class="text-muted"><?php echo $totalPoints; ?></span>
                                        </div>
                                    <?php
                                            }
                                    ?>

                                </div>
                        <?php
                                        }
                                    }
                        ?>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Left Content -->
                            <div class="col-12 col-lg-8">
                                <div class="p-0 px-lg-5">
                                    <div class="text-sbold text-14 mt-3">Exam General Guidelines</div>
                                    <div class="mt-3 text-med text-14">
                                        <?php echo nl2br($testGuidelines) ?></p>
                                    </div>
                                    <hr>
                                    <div class="text-sbold text-14 pb-3">Prepared by</div>
                                    <?php
                                    if (mysqli_num_rows($profInfoResult) > 0) {
                                        while ($prof = mysqli_fetch_assoc($profInfoResult)) {
                                    ?>
                                            <div class="d-flex align-items-center pb-5">
                                                <div class="rounded-circle me-2"
                                                    style="width: 50px; height: 50px; background-color: var(--highlight75);">
                                                    <img src="<?php echo $profilePic ?>" alt="Prof Picture"
                                                        class="rounded-circle" style="width:50px;height:50px;">
                                                </div>
                                                <div>
                                                    <div class="text-sbold text-14">Prof.
                                                        <?php echo $prof['firstName'] . " " . $prof['middleName'] . " " . $prof['lastName']; ?>
                                                    </div>
                                                    <div class="text-med text-12"><?php echo $assessmentCreationDate; ?></div>
                                                </div>
                                            </div>
                                    <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </div>

                            <!-- Right Content -->
                            <div class="col-12 col-lg-4">
                                <div class="cardSticky position-sticky mobile-view" style="top: 20px;">
                                    <div class="p-2">
                                        <div class="text-sbold text-16">Exam Details</div>

                                        <div class="text-sbold text-16 text-center mt-4"><?php echo $totalItems; ?>
                                        </div>
                                        <div class="text-reg text-14 text-center">Total Exam Items</div>

                                        <div class="text-sbold text-16 text-center mt-4"><?php echo $totalPoints; ?>
                                        </div>
                                        <div class="text-reg text-14 text-center">Total Exam Points</div>

                                        <div class="text-sbold text-16 text-center mt-4">
                                            <?php echo ($testTimeLimit / 60); ?> mins
                                        </div>
                                        <div class="text-reg text-14 text-center">Exam Duration</div>

                                        <div id="examStatusText" class="text-reg text-14 text-center mt-4">
                                            The exam will be automatically submitted when the timer ends.
                                        </div>

                                        <!-- Buttons Section -->
                                        <div class="pt-3 text-center">
                                            <!-- ✅ Visible by default -->
                                            <a href="<?php echo $btnLink; ?>?testID=<?php echo $testID; ?>">
                                                <button id="answerBtn"
                                                    class="button px-3 py-1 rounded-pill text-reg text-md-14"
                                                    style="background-color: var(--primaryColor);">
                                                    <?php echo $btnText; ?>
                                                </button>
                                            </a>
                                        </div>

                                        <!-- ✅ Hidden by default (separate section) -->
                                        <div id="viewResultContainer" class="pt-3 text-center" style="display: none;">
                                            <a href="#">
                                                <button id="viewResultBtn"
                                                    class="button px-3 py-1 rounded-pill text-reg text-md-14"
                                                    style="background-color: var(--primaryColor);"
                                                    onclick="viewResult()">
                                                    View Result
                                                </button>
                                            </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>