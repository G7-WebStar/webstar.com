<?php
include("../shared/assets/database/connect.php");
include("../shared/assets/processes/prof-session-process.php");

if (!isset($_GET['assessmentID'])) {
    echo "Assessment ID is missing in the URL.";
    exit;
}

$assessmentID = $_GET['assessmentID'];

if ($assessmentID == null) {
    echo "Assessment ID is missing in the URL.";
    exit;
}
$selectAssessmentQuery = "SELECT assessmentTitle, DATE_FORMAT(deadline, '%b %e') AS assessmentDeadline, type, DATE_FORMAT(createdAt, '%b %e, %Y %l:%i %p') AS creationDate
                          FROM assessments WHERE assessmentID = '$assessmentID'";
$selectAssessmentResult = executeQuery($selectAssessmentQuery);

$assessmentResultRow = mysqli_fetch_assoc($selectAssessmentResult);
$type = (mysqli_num_rows($selectAssessmentResult) > 0) ? $assessmentResultRow['type'] : null;
if ($type == null) {
    echo "Assessment doesn't exists.";
    exit();
}

if ($type != 'Task') {
    echo "Assessment doesn't exists.";
    exit;
}

$countPendingQuery = "SELECT COUNT(*) AS pending FROM todo 
                      WHERE assessmentID = '$assessmentID' AND status = 'Pending'";
$countPendingResult = executeQuery($countPendingQuery);
$pending = mysqli_fetch_assoc($countPendingResult);

$countSubmittedQuery = "SELECT COUNT(*) AS submittedTodo FROM todo 
                        WHERE assessmentID = '$assessmentID' AND status = 'Submitted'";
$countSubmittedResult = executeQuery($countSubmittedQuery);
$submitted = mysqli_fetch_assoc($countSubmittedResult);

$countGradedQuery = "SELECT COUNT(*) AS graded FROM todo 
                     WHERE assessmentID = '$assessmentID' AND status = 'Graded'";
$countGradedResult = executeQuery($countGradedQuery);
$graded = mysqli_fetch_assoc($countGradedResult);

$countMissingQuery = "SELECT COUNT(*) AS missing FROM todo 
                     WHERE assessmentID = '$assessmentID' AND status = 'Missing'";
$countMissingResult = executeQuery($countMissingQuery);
$missing = mysqli_fetch_assoc($countMissingResult);

$studentIDs = [];
$studentTodoStatusQuery = "SELECT todo.userID, userinfo.firstName, userinfo.middleName, userinfo.lastName, todo.status, submissions.submissionID FROM todo
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
$studentTodoStatusResult = executeQuery($studentTodoStatusQuery);

if (mysqli_num_rows($studentTodoStatusResult) > 0) {
    while ($studentIDRow = mysqli_fetch_assoc($studentTodoStatusResult)) {
        $studentIDs[] = ["studentID" => $studentIDRow['userID']];
    }
}

$getAssessmentStatusQuery = "SELECT * FROM assessments WHERE CURRENT_DATE <= deadline AND assessmentID = $assessmentID;";
$getAssessmentStatusResult = executeQuery($getAssessmentStatusQuery);

if (mysqli_num_rows($getAssessmentStatusResult) > 0) {
    $statusText = 'pending';
} else {
    $statusText = 'did not submit';
}

$getSubmissionIDQuery = "SELECT submissions.submissionID 
        FROM submissions 
        INNER JOIN todo 
            ON todo.userID = submissions.userID
        WHERE todo.status != 'Graded' AND todo.assessmentID = '$assessmentID' AND submissions.assessmentID = '$assessmentID'
        ORDER BY todo.updatedAt ASC
        LIMIT 1";
$getSubmissionIDResult = executeQuery($getSubmissionIDQuery);
$submissionIDRow = (mysqli_num_rows($getSubmissionIDResult) > 0) ? mysqli_fetch_assoc($getSubmissionIDResult) : null;
$submissionID = ($submissionIDRow == null) ? null : $submissionIDRow['submissionID'];

$checkRubricQuery = "SELECT rubricID FROM assignments WHERE assessmentID = '$assessmentID'";
$checkRubricResult = executeQuery($checkRubricQuery);
$rubricIDRow = (mysqli_num_rows($checkRubricResult) > 0) ? mysqli_fetch_assoc($checkRubricResult) : null;
$rubricID = ($rubricIDRow == null) ? null : $rubricIDRow['rubricID'];

$profilePic = !empty($test['profilePicture'])
    ? '../shared/assets/pfp-uploads/' . $test['profilePicture']
    : '../shared/assets/pfp-uploads/defaultProfile.png';
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Assess Submissions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/profIndex.css">
    <link rel="stylesheet" href="../shared/assets/css/assess-submissions.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../shared/assets/img/webstar-icon.png">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />

</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-0 p-md-3"
        style="background-color: var(--black);">

        <div class="row w-100">

            <!-- Sidebar (mobile) -->
            <?php include '../shared/components/prof-sidebar-for-mobile.php'; ?>

            <!-- Sidebar (desktop) -->
            <?php include '../shared/components/prof-sidebar-for-desktop.php'; ?>

            <!-- Main Container -->
            <div class="col main-container m-0 p-0 mx-0 mx-md-2 p-md-4 overflow-y-auto">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">

                    <!-- Navbar (mobile) -->
                    <?php include '../shared/components/prof-navbar-for-mobile.php'; ?>

                    <!-- Fixed Header -->
                    <div class="row mb-3">
                        <div class="col-12 cardHeader p-3 mb-4">

                            <!-- DESKTOP VIEW -->
                            <div class="row desktop-header d-none d-sm-flex">
                                <div class="col-auto me-2">
                                    <a href="assess.php" class="text-decoration-none">
                                        <i class="fa-solid fa-arrow-left text-reg text-16"
                                            style="color: var(--black);"></i>
                                    </a>
                                </div>
                                <?php
                                if (mysqli_num_rows($selectAssessmentResult) > 0) {
                                    mysqli_data_seek($selectAssessmentResult, 0);
                                    while ($assessmentRow = mysqli_fetch_assoc($selectAssessmentResult)) {
                                ?>
                                        <div class="col">
                                            <span class="text-sbold text-25"><?php echo $assessmentRow['assessmentTitle']; ?></span>
                                            <div class="text-reg text-18">Due <?php echo $assessmentRow['assessmentDeadline']; ?></div>
                                        </div>
                                <?php
                                    }
                                } ?>
                            </div>


                            <!-- MOBILE VIEW -->
                            <div class="d-block d-sm-none mobile-assignment">
                                <div class="mobile-top">
                                    <div class="arrow">
                                        <a href="assess.php" class="text-decoration-none">
                                            <i class="fa-solid fa-arrow-left text-reg text-16"
                                                style="color: var(--black);"></i>
                                        </a>
                                    </div>
                                    <?php
                                    if (mysqli_num_rows($selectAssessmentResult) > 0) {
                                        mysqli_data_seek($selectAssessmentResult, 0);
                                        while ($assessmentRow = mysqli_fetch_assoc($selectAssessmentResult)) {
                                    ?>
                                            <div class="col">
                                                <span class="text-sbold text-25"><?php echo $assessmentRow['assessmentTitle']; ?></span>
                                                <div class="text-reg text-18">Due <?php echo $assessmentRow['assessmentDeadline']; ?></div>
                                            </div>
                                    <?php
                                        }
                                    } ?>
                                </div>
                            </div>
                        </div>

                        <!-- Scrollable Content Container -->
                        <div class="content-scroll-container">
                            <div class="container-fluid py-3">
                                <div class="row">
                                    <!-- Left Content -->
                                    <div class="col-12 col-lg-8">
                                        <div class="p-0 px-lg-5">
                                            <div class="tab-carousel-wrapper d-block"
                                                style="--tabs-right-extend: 60px;">
                                                <div class="tab-scroll">
                                                    <ul class="nav nav-tabs custom-nav-tabs mb-3 flex-nowrap" id="myTab"
                                                        role="tablist">
                                                        <li class="nav-item">
                                                            <a class="nav-link" id="announcements-tab" href="assess-task-details.php?assessmentID=<?php echo $assessmentID; ?>" role="tab">Task Details</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link active" id="lessons-tab" data-bs-toggle="tab"
                                                                href="#lessons" role="tab">Submissions</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <!-- Tab Content -->
                                            <div class="tab-content" id="myTabContent">
                                                <!-- Task Details Tab - Disabled -->
                                                <div class="tab-pane fade" id="announcements" role="tabpanel" aria-labelledby="announcements-tab">
                                                    <!-- Empty content - tab is disabled -->
                                                </div>

                                                <!-- Submissions Tab -->
                                                <div class="tab-pane fade show active" id="lessons" role="tabpanel" aria-labelledby="lessons-tab">
                                                    <!-- Sort By dropdown -->
                                                    <div class="d-flex align-items-center flex-nowrap dropdown-container">
                                                        <span class="dropdown-label me-2">Sort By</span>
                                                        <button class="btn dropdown-toggle dropdown-custom" type="button"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            <span>Newest</span>
                                                        </button>
                                                        <ul class="dropdown-menu" id="filter">
                                                            <li data-value="Newest"><a class="dropdown-item text-reg">Newest</a></li>
                                                            <li data-value="Oldest"><a class="dropdown-item text-reg">Oldest</a></li>
                                                            <li data-value="Graded"><a class="dropdown-item text-reg">Graded</a>
                                                            <li data-value="<?php echo ucfirst($statusText); ?>"><a class="dropdown-item text-reg"><?php echo ucfirst($statusText); ?></a>
                                                            <li data-value="Submitted"><a class="dropdown-item text-reg">Submitted</a>
                                                            </li>
                                                        </ul>
                                                        <button class="btn btn-action btn-return-all" onclick="returnAll();">
                                                            <img src="../shared/assets/img/assess/assignment.png"
                                                                alt="Assess Icon"
                                                                style="width: 18px; height: 18px; margin-right: 5px; object-fit: contain;">Return All
                                                        </button>
                                                    </div>

                                                    <!-- Submissions List -->
                                                    <div class="submissions-list mt-4" id="submission-container">
                                                        <?php
                                                        if (mysqli_num_rows($studentTodoStatusResult) > 0) {
                                                            mysqli_data_seek($studentTodoStatusResult, 0);
                                                            while ($studentsTodoRow = mysqli_fetch_assoc($studentTodoStatusResult)) {
                                                        ?>
                                                                <?php
                                                                $gradingLink = ($rubricID == null) ? 'grading-sheet.php?submissionID=' : 'grading-sheet-rubrics.php?submissionID=';
                                                                ?>
                                                                <a class="text-decoration-none" href="<?php echo ($studentsTodoRow['status'] != 'Graded') ? $gradingLink . $studentsTodoRow['submissionID'] : '#'; ?>">
                                                                    <div class="submission-item d-flex align-items-center py-3 border-bottom">
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="avatar me-3" style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden;">
                                                                                <img src="<?php echo $profilePic ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                                                                            </div>
                                                                            <span class="text-sbold text-16"><?php echo $studentsTodoRow['lastName'] . ", " . $studentsTodoRow['firstName'] . " " . $studentsTodoRow['middleName']; ?></span>
                                                                        </div>
                                                                        <div class="flex-grow-1 d-flex justify-content-center">
                                                                            <span class="badge badge-<?php echo strtolower($studentsTodoRow['status']) ?>"><?php echo $studentsTodoRow['status']; ?></span>
                                                                        </div>
                                                                        <div class="d-flex align-items-center">
                                                                            <img src="../shared/assets/img/assess/arrow.png" alt="Arrow" style="width: 20px; height: 20px;">
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                        <?php
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-12 col-lg-4">
                                        <div class="cardSticky position-sticky" style="top: 20px;">
                                            <div class="p-2">
                                                <div class="row align-items-center justify-content-center mb-3">
                                                    <div class="col-auto">
                                                        <div class="chart-container"
                                                            style="width: 100px; height: 100px;">
                                                            <canvas id="taskChart" width="100" height="100"></canvas>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <!-- Submission Stats -->
                                                        <div class="submission-stats">
                                                            <div class="text-reg text-14 mb-1"><span
                                                                    class="stat-value"><?php echo $submitted['submittedTodo']; ?></span> submitted</div>
                                                            <div class="text-reg text-14 mb-1"><span
                                                                    class="stat-value"><?php echo $pending['pending']; ?></span> <?php echo $statusText; ?></div>
                                                            <div class="text-reg text-14 mb-1"><span
                                                                    class="stat-value"><?php echo $graded['graded']; ?></span>
                                                                graded</div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="d-flex justify-content-center pt-3">
                                                    <?php if ($submissionID != null) { ?><a class="text-decoration-none" href="<?php echo ($rubricID == null) ? 'grading-sheet.php?submissionID=' . $submissionID : 'grading-sheet-rubrics.php?submissionID=' . $submissionID; ?>"><?php } ?>
                                                        <?php if ($submissionID == null) { ?><div title="No submissions in this assessment yet"><?php } ?>
                                                            <button class="btn btn-action <?php echo ($submissionID == null) ? 'disabled' : '' ?>">
                                                                <img src="../shared/assets/img/assess/assess.png"
                                                                    alt="Assess Icon"
                                                                    style="width: 20px; height: 20px; margin-right: 5px; object-fit: contain;">Grading
                                                                Sheet
                                                            </button>
                                                            <?php if ($submissionID == null) { ?>
                                                            </div><?php } ?>
                                                        <?php if ($submissionID != null) { ?></a><?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- Close content-scroll-container -->
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            function createDoughnutChart(canvasId, submitted, pending, graded, missing) {
                const ctx = document.getElementById(canvasId).getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: [submitted, pending, graded, missing],
                            backgroundColor: ['#3DA8FF', '#C7C7C7', '#d9ffe4ff', '#ffd9d9ff'],
                            borderWidth: 0,
                        }]
                    },
                    options: {
                        cutout: '75%',
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: false
                            }
                        }
                    }
                });
            }

            createDoughnutChart('taskChart', <?php echo $submitted['submittedTodo']; ?>, <?php echo $pending['pending']; ?>, <?php echo $graded['graded']; ?>, <?php echo $missing['missing']; ?>);

            const studentIDs = <?php echo json_encode($studentIDs); ?>;
            console.log(studentIDs);

            function returnAll() {
                fetch('../shared/assets/processes/return-all.php?assessmentID=' + <?php echo $assessmentID; ?>, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            studentID: studentIDs
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        alert("Successfully Returned All");
                        window.location.reload();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        window.location.reload();
                    });
            }

            let selected = {};

            document.querySelectorAll("#filter li").forEach(function(li) {
                li.addEventListener("click", function() {

                    selected = {
                        selected: this.dataset.value
                    };
                    console.log(JSON.stringify(selected));
                    fetch('../shared/assets/processes/assess-task-filter.php?assessmentID=' + <?php echo $assessmentID; ?>, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                selected: selected
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log(data);
                            var submissionContainer = document.getElementById('submission-container');
                            submissionContainer.innerHTML = "";

                            data.results.forEach(function(result) {
                                document.getElementById('submission-container').innerHTML +=
                                    `
                                <a class="text-decoration-none" href="` + ((result.status != 'Graded') ? `<?php echo ($rubricID == null) ? 'grading-sheet.php?submissionID=' . $submissionID : 'grading-sheet-rubrics.php?submissionID=' ?>` + result.submissionID : '#') + `">
                                    <div class="submission-item d-flex align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-3" style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden;">
                                                <img src="../shared/assets/img/assess/prof.png" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                                            </div>
                                            <span class="text-sbold text-16">` + result.lastname + `, ` + result.firstName + ` ` + result.middleName + `</span>
                                        </div>
                                        <div class="flex-grow-1 d-flex justify-content-center">
                                            <span class="badge badge-` + result.status.toLowerCase() + `">` + result.status + `</span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <img src="../shared/assets/img/assess/arrow.png" alt="Arrow" style="width: 20px; height: 20px;">
                                        </div>
                                    </div>
                                 </a>`;
                            });
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                });
            });
        </script>
</body>

</html>