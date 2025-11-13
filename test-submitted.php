<?php
$activePage = 'todo';
include('shared/assets/database/connect.php');

include("shared/assets/processes/session-process.php");
$testID = $_GET['testID'];
if ($testID == null) {
    header("Location: 404.php");
    exit();
}

$selectTestQuery = "SELECT assessmentTitle FROM tests 
                    INNER JOIN assessments
                        ON tests.assessmentID = assessments.assessmentID
                    WHERE testID = $testID";
$selectTestResult = executeQuery($selectTestQuery);

$selectQuestionsQuery = "SELECT 
testquestions.*
FROM tests 
INNER JOIN testquestions 
    ON tests.testID = testquestions.testID 
WHERE tests.testID = $testID";
$selectQuestionsResult = executeQuery($selectQuestionsQuery);

$validateTestIDQuery = "SELECT 
                        todo.* 
                        FROM todo 
                        INNER JOIN tests 
                        ON todo.assessmentID = tests.assessmentID 
                        WHERE todo.userID = '$userID' AND tests.testID = '$testID' AND todo.status = 'Submitted';";
$validateTestIDResult = executeQuery($validateTestIDQuery);

if (mysqli_num_rows($validateTestIDResult) <= 0) {
    $checkStatusQuery = "SELECT 
                        todo.* 
                        FROM todo 
                        INNER JOIN tests 
                        ON todo.assessmentID = tests.assessmentID 
                        WHERE todo.userID = '$userID' AND tests.testID = '$testID' AND todo.status = 'Pending';";
    $checkStatusResult = executeQuery($checkStatusQuery);
    if (mysqli_num_rows($checkStatusResult) > 0) {
        header("Location: test.php?testID=" . $testID);
        exit();
    } else {
        $checkStatusQuery = "SELECT 
                            todo.* FROM todo 
                            INNER JOIN tests 
                                ON todo.assessmentID = tests.assessmentID 
                            WHERE todo.userID = '$userID' AND tests.testID = '$testID' AND todo.status = 'Graded';";
        $checkStatusResult = executeQuery($checkStatusQuery);

        if (mysqli_num_rows($checkStatusResult) > 0) {
            header("Location: test-result.php?testID=" . $testID);
            exit();
        }
    }
}

$timeStartQuery = "SELECT 
        todo.timeStart AS testTimeStart,
        (SELECT tests.testTimelimit - todo.timeSpent) AS remainingTime
        FROM todo
        INNER JOIN tests
            ON todo.assessmentID = tests.assessmentID
        WHERE todo.userID = '$userID' AND tests.testID = '$testID' AND todo.timeStart IS NOT null";
$timeStartResult = executeQuery($timeStartQuery);
$timeStartRow = mysqli_fetch_assoc($timeStartResult);

$totalPointsQuery = "SELECT SUM(testQuestionPoints) AS totalPoints FROM testquestions WHERE testID = '$testID'";
$totalPointsResult = executeQuery($totalPointsQuery);
$totalPointsRow = mysqli_fetch_assoc($totalPointsResult);
$totalPoints = $totalPointsRow['totalPoints'];

$scoreQuery = "SELECT COUNT(isCorrect) AS correct FROM testresponses WHERE isCorrect = '1' AND userID = '$userID' AND testID = '$testID'";
$scoreResult = executeQuery($scoreQuery);
$scoreRow = mysqli_fetch_assoc($scoreResult);
$score = $scoreRow['correct'];

$totalItemsQuery = "SELECT COUNT(*) AS totalItems FROM testquestions WHERE testID = '$testID'";
$totalItemsResult = executeQuery($totalItemsQuery);
$totalItemsRow = mysqli_fetch_assoc($totalItemsResult);
$totalItems = $totalItemsRow['totalItems'];

$timeFactorQuery = "SELECT tests.testTimeLimit, todo.timeSpent FROM tests INNER JOIN todo ON tests.assessmentID = todo.assessmentID WHERE tests.testID = '$testID'";
$timeFactorResult = executeQuery($timeFactorQuery);
$timeFactorRow = mysqli_fetch_assoc($timeFactorResult);
$timelimit = $timeFactorRow['testTimeLimit'];
$timeSpent = $timeFactorRow['timeSpent'];

$baseXP = 10 * $totalPoints;
$baseWebstars = 1 * $totalPoints;
$correctBonusXP =  $baseXP * ($score / $totalPoints);
$correctBonusWebstars = $baseWebstars * ($score / $totalPoints);
$timeFactor = 1 + ($timelimit - $timeSpent) / $timelimit;
$finalXP = $baseXP + ($correctBonusXP * $timeFactor);
$finalWebstars = $baseWebstars + ($correctBonusWebstars * $timeFactor);
$multipliedXP = $finalXP * 2;

$bonusXP = $finalXP - $baseXP;
$bonusWebstars = $finalWebstars - $baseWebstars;

echo "<script>console.log(" . $baseXP . ");</script>";
echo "<script>console.log(" . $baseWebstars . ");</script>";
echo "<script>console.log(" . $correctBonusXP . ");</script>";
echo "<script>console.log(" . $correctBonusWebstars . ");</script>";
echo "<script>console.log(" . $timeFactor . ");</script>";
echo "<script>console.log(" . $finalXP . ");</script>";
echo "<script>console.log(" . $finalWebstars . ");</script>";
echo "<script>console.log(" . $multipliedXP . ");</script>";
echo "<script>console.log(" . $bonusXP . ");</script>";
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Index</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="shared/assets/css/index.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="shared/assets/img/webstar-icon.png">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />
    <style>
        .gradient-bg {
            background: linear-gradient(to right, #FDE29C, #C1ECC4, #C1C8EC, #ECC1C1);
        }


        @media screen and (max-width: 1199px) {
            .text-lg-16 {
                font-size: 16px !important;
            }
        }

        @media screen and (max-width: 767px) {
            .fs-sm-6 {
                font-size: 1rem !important;
            }

            .medal-img {
                width: 200px;
            }

            .btn-mobile {
                margin-bottom: calc(1.5rem + 80px) !important;
            }

            .text-sm-12 {
                font-size: 12px !important;
            }

            .text-sm-14 {
                font-size: 14px !important;
            }

            .text-sm-18 {
                font-size: 18px !important;
            }
        }

        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: var(--dirtyWhite);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primaryColor);
            /* Your accent color */
            border-radius: 10px;
            border: 2px solid var(--dirtyWhite);
        }

        * {
            scrollbar-width: thin;
            scrollbar-color: var(--primaryColor) var(--dirtyWhite);
        }
    </style>
</head>

<body oncopy="return false" onpaste="return false" oncut="return false" oncontextmenu="return false" onselectstart="return false">
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-0 p-md-3"
        style="background-color: var(--black);">

        <div class="row w-100 m-0">

            <!-- Sidebar (only shows on mobile) -->
            <?php include 'shared/components/sidebar-for-mobile.php'; ?>

            <!-- Sidebar Column (fixed on desktop) -->
            <?php include 'shared/components/sidebar-for-desktop.php'; ?>

            <!-- Main Container Column -->
            <div class="col-12 col-md main-container m-0 p-0 mx-0 mx-md-2 p-md-4 overflow-auto">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">

                    <!-- Navbar for mobile -->
                    <?php include 'shared/components/navbar-for-mobile.php'; ?>

                    <div class="container-fluid py-1 overflow-y-auto d-flex flex-column h-100 row-padding-top">
                        <div class="row flex-grow-1">
                            <div class="col-12 d-flex flex-column h-100">

                                <!-- Quiz Nav -->
                                <div class="row bg-white border border-black rounded-4 my-3 text-sbold mx-0 mx-md-1">
                                    <i class="d-block d-md-none announcement-arrow fa-lg fa-solid fa-arrow-left text-reg text-12 mt-3 me-3"
                                        style="color: var(--black);"></i>
                                    <div class="quiz-nav col-12 d-flex flex-column flex-md-row align-items-center justify-content-between my-2 px-3 px-md-5 py-2 py-md-3">
                                        <div class="d-flex flex-row align-items-center mb-0">
                                            <a href="todo.php" class="text-decoration-none"><i class="d-none d-md-block announcement-arrow fa-lg fa-solid fa-arrow-left text-reg text-12 me-3"
                                                    style="color: var(--black);"></i></a>
                                            <?php
                                            if (mysqli_num_rows($selectTestResult) > 0) {
                                                while ($guideLines = mysqli_fetch_assoc($selectTestResult)) {
                                            ?>
                                                    <div class="text-center text-md-auto h2 m-0">
                                                        <?php echo $guideLines['assessmentTitle']; ?>
                                                    </div>
                                            <?php
                                                }
                                            } ?>
                                        </div>
                                        <div class="h2 mt-3 mt-md-0 mb-0 text-center text-md-end" id="timer">

                                        </div>
                                    </div>
                                </div>
                                <!-- End of Quiz Nav -->

                                <!-- Content -->
                                <div class="row flex-grow-1">
                                    <div class="col-12 d-flex flex-column flex-grow-1 align-items-center">
                                        <div class="question-container">
                                            <div class="h2 text-sbold text-center fs-sm-6" id="question-container">
                                                <img class="medal-img" src="shared/assets/img/medal.png" alt="medal">
                                                <div class="text-bold">You scored <?php echo $score; ?>/<?php echo $totalItems; ?> !</div>
                                                <div class="text-sbold mt-4 mt-md-5 text-18">Rewards</div>
                                                <div class="row mt-2">
                                                    <div class="col-12">
                                                        <img class="img-fluid object-fit-contain mx-0" width="20px" src="shared/assets/img/xp.png" alt="xp">
                                                        <span class="text-bold text-20 text-sm-18">+<?php echo round($baseXP); ?> XPs</span>
                                                        <span class="text-sbold text-16 text-sm-14">+<?php echo round($bonusXP); ?> Bonus XPs</span>
                                                    </div>
                                                </div>
                                                <div class="row mt-0">
                                                    <div class="col-12">
                                                        <img class="img-fluid object-fit-contain mx-0" width="20px" src="shared/assets/img/webstar.png" alt="xp">
                                                        <span class="text-bold text-20 text-sm-18">+<?php echo round($baseWebstars); ?> Webstars</span>
                                                        <span class="text-sbold text-16 text-sm-14">+<?php echo round($bonusWebstars); ?> Bonus Webstars</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-8 text-sbold text-20 text-lg-16 d-flex justify-content-center mt-4 mt-md-5" id="choices">
                                            Please wait for your instructor to release the test results. Once returned,
                                            you’ll be able to review which questions you got correct and get feedback.
                                        </div>
                                    </div>
                                    <div class="mt-auto text-sbold">
                                        <div class="d-flex justify-content-center align-items-center btn-mobile mb-4 gap-3 mt-5" id="buttonSection">
                                            <button class="gradient-bg btn d-flex align-items-center justify-content-end gap-2 border border-black rounded-5 px-lg-4 py-lg-2 interactable" id="prevBtn"
                                                style="background-color: var(--primaryColor);" data-bs-toggle="modal" data-bs-target="#multiplier">
                                                <span class="m-0 fs-sm-6 text-16 text-sm-12">Use XP Multiplier</span>
                                            </button>

                                            <button class="btn d-flex align-items-center justify-content-start gap-2 border border-black rounded-5 px-lg-4 py-lg-2 interactable" id="nextBtn"
                                                style="background-color: var(--primaryColor);">
                                                <span class="m-0 fs-sm-6 text-16 text-sm-12">Return to Test Info</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Content -->

                            </div>
                        </div>
                    </div>
                </div> <!-- End Card -->
            </div>
        </div>
    </div>

    <div class="modal fade" id="multiplier" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered py-4" style="max-width: 700px;  height: 25px;">
            <div class="modal-content">

                <!-- HEADER -->
                <div class="modal-header border-bottom">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form class="my-0" id="feedbackForm" action="" method="POST">
                    <div class="modal-body">
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="col-12 d-flex justify-content-center flex-column text-center text-30">
                                    <p class="text-bold">Multiply your XPs</p>
                                </div>
                                <div class="row">
                                    <div class="mx-auto col-8 text-center">
                                        <p class="text-reg text-18 text-reg mb-0">Cost: <span class="text-sbold">1000 Webstars</span></p>
                                        <p class="text-reg text-18 text-reg">Current XPs: <span class="text-sbold"><?php echo round($finalXP); ?> → <?php echo round($multipliedXP); ?> XPs </span>after boost</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <span class="text-16 text-center text-reg mb-3">
                                        Are you sure you want to use
                                        <span class="text-sbold">1000 Webstars</span> to activate this multiplier?
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FOOTER -->
                    <div class="modal-footer justify-content-center">
                        <button class="gradient-bg my-auto text-sbold btn d-flex align-items-center justify-content-center border border-black rounded-5 px-lg-4 py-lg-2 interactable"
                            data-bs-toggle="modal" data-bs-target="#multiplier">
                            <span class="m-0 fs-sm-6 text-16 text-sm-12">Use XP Multiplier</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let timerHtml = document.getElementById('timer');
        let seconds = <?php echo $timeStartRow['remainingTime']; ?>;
        //Format time
        function formatTime(sec) {
            let days = Math.floor(sec / 86400)
            let hours = Math.floor(sec / 3600);
            let minutes = Math.floor(sec / 60);
            let secondsTime = sec % 60;

            if (days < 10) {
                days = "0" + days;
            }

            if (hours < 10) {
                hours = "0" + hours;
            } else if (hours >= 24) {
                hours = hours % 24;
                if ((hours % 24) < 10) {
                    hours = "0" + hours % 24;
                }
            }

            if (minutes < 10) {
                minutes = "0" + minutes;
            } else if (minutes >= 60) {
                minutes = minutes % 60;
                if ((minutes % 60) < 10) {
                    minutes = "0" + (minutes % 60);
                }
            }

            if (secondsTime < 10) {
                secondsTime = "0" + secondsTime;
            }

            //Return results
            if (sec < 86400 && sec >= 3600) {
                return time = hours + ":" + minutes + ":" + secondsTime;
            } else if (sec < 3600) {
                return time = minutes + ":" + secondsTime;
            } else {
                return time = days + ":" + hours + ":" + minutes + ":" + secondsTime;
            }
        }

        timerHtml.innerHTML = `<i class="bi bi-clock fa-xs me-2" style="color: var(--black);"></i>` + formatTime(seconds);
    </script>
</body>

</html>