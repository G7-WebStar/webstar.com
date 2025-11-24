<?php
$activePage = 'todo';
include('shared/assets/database/connect.php');
include("shared/assets/processes/session-process.php");
$testID = $_GET['testID'];

if ($testID == null) {
    header("Location: 404.html");
    exit();
}

$testExistenceQuery = "SELECT tests.testID FROM tests 
INNER JOIN assessments 
    ON tests.assessmentID = assessments.assessmentID 
INNER JOIN todo 
    ON assessments.assessmentID = todo.assessmentID 
WHERE tests.testID = '$testID' AND todo.userID = '$userID'";
$testExistenceResult = executeQuery($testExistenceQuery);

if (mysqli_num_rows($testExistenceResult) <= 0) {
    echo "Test doesn't exists.";
    exit();
}

$selectTestQuery = "SELECT assessmentTitle FROM tests 
                    INNER JOIN assessments
                        ON tests.assessmentID = assessments.assessmentID
                    WHERE testID = $testID";
$selectTestResult = executeQuery($selectTestQuery);
$pageTitleRow = mysqli_fetch_assoc($selectTestResult);
$pageTitle = $pageTitleRow['assessmentTitle'];

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
                            WHERE todo.userID = '$userID' AND tests.testID = '$testID' AND todo.status = 'Returned';";
        $checkStatusResult = executeQuery($checkStatusQuery);

        if (mysqli_num_rows($checkStatusResult) > 0) {
            header("Location: test-result.php?testID=" . $testID);
            exit();
        } else {
            echo "Test doesn't exists.";
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

$scoreQuery = "SELECT SUM(testquestions.testQuestionPoints) AS correctPoints
               FROM testresponses
               INNER JOIN testquestions
                 ON testresponses.testQuestionID = testquestions.testQuestionID
               WHERE testresponses.isCorrect = 1 
                 AND testresponses.userID = '$userID' 
                 AND testresponses.testID = '$testID'";
$scoreResult = executeQuery($scoreQuery);
$scoreRow = mysqli_fetch_assoc($scoreResult);
$score = $scoreRow['correctPoints'] ?? 0;

$totalPointsQuery = "SELECT SUM(testQuestionPoints) AS totalPoints FROM testquestions WHERE testID = '$testID'";
$totalPointsResult = executeQuery($totalPointsQuery);
$totalPointsRow = mysqli_fetch_assoc($totalPointsResult);
$totalPoints = $totalPointsRow['totalPoints'];

$timeFactorQuery = "SELECT tests.testTimeLimit, todo.timeSpent FROM tests INNER JOIN todo ON tests.assessmentID = todo.assessmentID WHERE tests.testID = '$testID'";
$timeFactorResult = executeQuery($timeFactorQuery);
$timeFactorRow = mysqli_fetch_assoc($timeFactorResult);
$timelimit = (mysqli_num_rows($timeFactorResult) > 0) ? $timeFactorRow['testTimeLimit'] : '0';
$timeSpent = (mysqli_num_rows($timeFactorResult) > 0) ? $timeFactorRow['timeSpent'] : '0';


$baseXP = 10 * $totalPoints;
$baseWebstars = 1 * $totalPoints;

if ($totalPoints == 0) {
    $correctBonusXP =  0;
    $correctBonusWebstars = 0;
} else {
    $correctBonusXP =  $baseXP * ($score / $totalPoints);
    $correctBonusWebstars = $baseWebstars * ($score / $totalPoints);
}

if ($timelimit == 0) {
    $timeFactor = 0;
} else {
    $timeFactor = 1 + ($timelimit - $timeSpent) / $timelimit;
}
$finalXP = $baseXP + ($correctBonusXP * $timeFactor);
$finalWebstars = $baseWebstars + ($correctBonusWebstars * $timeFactor);
$multipliedXP = round($finalXP) * 2;

$bonusXP = $finalXP - $baseXP;
$bonusWebstars = $finalWebstars - $baseWebstars;

//Get Assessment ID of current test
$assessmentIDQuery = "SELECT assessmentID FROM tests WHERE testID = '$testID'";
$assessmentIDResult = executeQuery($assessmentIDQuery);
$assessmentIDRow = mysqli_fetch_assoc($assessmentIDResult);
$assessmentID = (mysqli_num_rows($assessmentIDResult) > 0) ? $assessmentIDRow['assessmentID'] : null;

//Checks if multiplier is already used
$checkMultiplierUseQuery = "SELECT * FROM webstars WHERE userID = '$userID' AND assessmentID = '$assessmentID' AND sourceType = 'XP Multiplier Usage'";
$checkMultiplierUseResult = executeQuery($checkMultiplierUseQuery);
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $pageTitle; ?> ✦ Webstar</title>
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
                                            <a href="exam-info.php?testID=<?php echo $testID; ?>" class="text-decoration-none"><i class="d-none d-md-block announcement-arrow fa-lg fa-solid fa-arrow-left text-reg text-12 me-3"
                                                    style="color: var(--black);"></i></a>
                                            <?php
                                            if (mysqli_num_rows($selectTestResult) > 0) {
                                                mysqli_data_seek($selectTestResult, 0);
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
                                                <div class="text-bold">You scored <?php echo $score; ?>/<?php echo $totalPoints; ?> !</div>
                                                <div class="text-sbold mt-4 mt-md-5 text-18">Rewards</div>
                                                <div class="row mt-2">
                                                    <div class="col-12">
                                                        <img class="img-fluid object-fit-contain mx-0" width="20px" src="shared/assets/img/xp.png" alt="xp">
                                                        <span class="text-bold text-20 text-sm-18">+<?php echo round($baseXP); ?> XPs</span>
                                                        <span class="text-sbold text-16 text-sm-14">+<?php echo round($bonusXP); ?> Bonus XPs</span>
                                                        <?php echo (mysqli_num_rows($checkMultiplierUseResult) > 0) ? '<span class="text-sbold text-16 text-sm-14">+' . round($finalXP) . ' XPs boosted!</span>' : ''; ?>
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
                                            <button class="gradient-bg btn d-flex align-items-center justify-content-end gap-2 border border-black rounded-5 px-lg-4 py-lg-2 interactable" id="useBtn"
                                                style="background-color: var(--primaryColor);" data-bs-toggle="modal" data-bs-target="#multiplier">
                                                <span class="m-0 fs-sm-6 text-16 text-sm-12">Use XP Multiplier</span>
                                            </button>

                                            <a href="exam-info.php?testID=<?php echo $testID; ?>" class="text-decoration-none text-dark">
                                                <button class="btn d-flex align-items-center justify-content-start gap-2 border border-black rounded-5 px-lg-4 py-lg-2 interactable" id="returnBtn"
                                                    style="background-color: var(--primaryColor);">
                                                    <span class="m-0 fs-sm-6 text-16 text-sm-12">Return to Test Info</span>
                                                </button></a>
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


                <div class="modal-body">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-12 d-flex justify-content-center flex-column text-center text-30">
                                <p class="text-bold">Multiply your XPs</p>
                            </div>
                            <div class="row">
                                <div class="mx-auto col-8 text-center">
                                    <p class="text-reg text-18 text-reg mb-0">Cost: <span class="text-sbold">1000 Webstars</span></p>
                                    <p class="text-reg text-18 text-reg">Current XPs: <span class="text-sbold"><?php echo round($finalXP); ?> → <?php echo $multipliedXP; ?> XPs </span>after boost</p>
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
                    <button id="multiplierBtn" class="gradient-bg my-auto text-sbold btn d-flex align-items-center justify-content-center border border-black rounded-5 px-lg-4 py-lg-2 interactable"
                        data-bs-toggle="modal" data-bs-target="#multiplier" <?php echo (mysqli_num_rows($checkMultiplierUseResult) <= 0) ? 'onclick="applyXPMultiplier();"' : ''; ?>>
                        <span class="m-0 fs-sm-6 text-16 text-sm-12">Use XP Multiplier</span>
                    </button>
                </div>

            </div>
        </div>
    </div>
    <!-- Toast Container -->
    <div id="toastContainer"
        class="position-absolute top-0 start-50 translate-middle-x pt-5 pt-md-1 d-flex flex-column align-items-center text-med text-14"
        style="z-index:1100; pointer-events:none;">
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
    <script>
        // Function to show toast with icon
        function showToast(message, type = 'success') {
            const alert = document.createElement('div');
            alert.className = `alert mb-2 shadow-lg d-flex align-items-center gap-2 px-3 py-2 
                       ${type === 'success' ? 'alert-success' : 'alert-danger'}`;
            alert.style.opacity = "0";
            alert.style.transition = "opacity 0.3s ease";
            alert.style.pointerEvents = "none";

            alert.innerHTML = `
                <i class="fa-solid ${type === 'success' ? 'fa-circle-check' : 'fa-circle-xmark'}"></i>
                <span>${message}</span>
            `;

            document.getElementById('toastContainer').appendChild(alert);

            // Fade in
            setTimeout(() => alert.style.opacity = "1", 10);

            // Fade out & remove after 3s
            setTimeout(() => {
                alert.style.opacity = "0";
                setTimeout(() => alert.remove(), 300);
            }, 3000);
        }
    </script>
    <script>
        // Trigger confetti when the page loads
        window.onload = function() {
            const duration = 5000; // Duration in milliseconds
            const animationEnd = Date.now() + duration;
            const defaults = {
                startVelocity: 25, // slower fall
                spread: 360,
                ticks: 200, // longer visibility (higher = stays longer)
                zIndex: 9999
            };

            function randomInRange(min, max) {
                return Math.random() * (max - min) + min;
            }

            (function frame() {
                confetti({
                    ...defaults,
                    particleCount: 5,
                    origin: {
                        x: randomInRange(0, 1), // random horizontal positions
                        y: 0 // start at top
                    }
                });

                if (Date.now() < animationEnd) {
                    requestAnimationFrame(frame);
                }
            })();
        };

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

        <?php
        if (mysqli_num_rows($checkMultiplierUseResult) <= 0) {
            echo "async function applyXPMultiplier() {
            try {
                const response = await fetch('shared/assets/processes/xp-multiplier.php?testID=" . $testID . "');

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const result = await response.json();

                if (result.success) {
                    await showToast(result.message, 'success');
                    document.getElementById('multiplierBtn').disabled = true;
                    document.getElementById('useBtn').disabled = true;
                    setTimeout(() => {
                        window.location.reload();
                    }, 3300);
                } else {
                    showToast('Failed to apply multiplier: ' + result.error, 'danger');
                }
            } catch (error) {
                console.error('Error applying XP multiplier:', error);
            }
        
        }";
        } else {
            echo "document.getElementById('multiplierBtn').disabled = true;
                  document.getElementById('useBtn').disabled = true;";
        }
        ?>
    </script>
</body>

</html>