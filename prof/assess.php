<?php $activePage = 'assess'; ?>
<?php
include("../shared/assets/database/connect.php");
include("../shared/assets/processes/prof-session-process.php");

$toastMessage = '';
$toastType = '';

if (isset($_SESSION['toast'])) {
    $toastMessage = $_SESSION['toast']['message'];
    $toastType = $_SESSION['toast']['type'];
    unset($_SESSION['toast']);
}

$assessments = [];
$assessmentsQuery = "SELECT 
assessments.type, 
assessments.assessmentTitle, 
courses.courseID,
courses.courseCode, 
courses.courseTitle, 
assessments.assessmentID,
assessments.isArchived,
DATE_FORMAT(assessments.deadline, '%b %e') AS assessmentDeadline
FROM assessments
INNER JOIN courses
	ON assessments.courseID = courses.courseID
WHERE courses.userID = $userID AND courses.isActive = '1'";
$assessmentsResult = executeQuery($assessmentsQuery);
if ($assessmentsResult && mysqli_num_rows($assessmentsResult) > 0) {
    while ($rowAssessment = mysqli_fetch_assoc($assessmentsResult)) {
        $assessmentCourseID = $rowAssessment['courseID'];
        $assessmentID = $rowAssessment['assessmentID'];

        $countPendingQuery = "SELECT COUNT(*) AS pending FROM todo 
                                WHERE assessmentID = '$assessmentID' AND status = 'Pending'";
        $countPendingResult = executeQuery($countPendingQuery);

        $countSubmittedQuery = "SELECT COUNT(*) AS submittedTodo FROM todo 
                                WHERE assessmentID = '$assessmentID' AND status = 'Submitted'";
        $countSubmittedResult = executeQuery($countSubmittedQuery);

        $countReturnedQuery = "SELECT COUNT(*) AS returned FROM todo 
                                WHERE assessmentID = '$assessmentID' AND status = 'Returned'";
        $countReturnedResult = executeQuery($countReturnedQuery);

        $countMissingQuery = "SELECT COUNT(*) AS missing FROM todo 
                                WHERE assessmentID = '$assessmentID' AND status = 'Missing'";
        $countMissingResult = executeQuery($countMissingQuery);

        $getSubmissionIDQuery = "SELECT submissions.submissionID 
        FROM submissions 
        INNER JOIN todo 
            ON todo.userID = submissions.userID
        WHERE todo.status != 'Returned' AND todo.assessmentID = '$assessmentID' AND submissions.assessmentID = '$assessmentID'
        ORDER BY todo.updatedAt ASC
        LIMIT 1";
        $getSubmissionIDResult = executeQuery($getSubmissionIDQuery);

        $checkRubricQuery = "SELECT rubricID FROM assignments WHERE assessmentID = '$assessmentID'";
        $checkRubricResult = executeQuery($checkRubricQuery);

        $submittedTodoCount = 0;

        if (mysqli_num_rows($countSubmittedResult) > 0) {
            $countRowSubmitted = mysqli_fetch_assoc($countSubmittedResult);
            $submittedTodoCount = $countRowSubmitted['submittedTodo'];
        }

        $returnedTodoCount = 0;

        if (mysqli_num_rows($countReturnedResult) > 0) {
            $countRowReturned = mysqli_fetch_assoc($countReturnedResult);
            $returnedTodoCount = $countRowReturned['returned'];
        }

        $missingTodoCount = 0;

        if (mysqli_num_rows($countMissingResult) > 0) {
            $countRowMissing = mysqli_fetch_assoc($countMissingResult);
            $missingTodoCount = $countRowMissing['missing'];
        }

        $pendingTodoCount = 0;

        if (mysqli_num_rows($countPendingResult) > 0) {
            $countRowPending = mysqli_fetch_assoc($countPendingResult);
            $pendingTodoCount = $countRowPending['pending'];
        }

        $submissionID = 0;

        if (mysqli_num_rows($getSubmissionIDResult) > 0) {
            $submissionIDRow = mysqli_fetch_assoc($getSubmissionIDResult);
            $submissionID = $submissionIDRow['submissionID'];
        }

        if (mysqli_num_rows($checkRubricResult) > 0) {
            $rubricRow = mysqli_fetch_assoc($checkRubricResult);
            $rubricID = $rubricRow['rubricID'];
        } else {
            $rubricID = null;
        }

        $rowAssessment['returned'] = $returnedTodoCount;
        $rowAssessment['submittedTodo'] = $submittedTodoCount;
        $rowAssessment['pending'] = $pendingTodoCount;
        $rowAssessment['missing'] = $missingTodoCount;
        $rowAssessment['submissionID'] = $submissionID;
        $rowAssessment['rubricID'] = $rubricID;
        $assessments[] = $rowAssessment;
    }
}

$getCoursesQuery = "SELECT courseCode FROM courses WHERE userID = '$userID'";
$getCoursesResult = executeQuery($getCoursesQuery);
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Assess</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/profIndex.css">
    <link rel="stylesheet" href="../shared/assets/css/assess.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../shared/assets/img/webstar-icon.png">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />


    <style>
        @media screen and (max-width: 767px) {
            .img-small {
                width: 150px !important;
            }

            .mobile-view {
                margin-bottom: 80px !important;
            }

            .text-sm-20 {
                font-size: 20px !important;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-0 p-md-3"
        style="background-color: var(--black);">

        <div class="row w-100">

            <!-- Sidebar (only shows on mobile) -->
            <?php include '../shared/components/prof-sidebar-for-mobile.php'; ?>

            <!-- Sidebar Column (fixed on desktop) -->
            <?php include '../shared/components/prof-sidebar-for-desktop.php'; ?>

            <!-- Main Container Column-->
            <div class="col main-container m-0 p-0 mx-0 mx-md-2 p-0 p-md-4 overflow-y-auto">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">

                      <!-- Toast container -->
                    <div id="toastContainer"
                        class="position-absolute top-0 start-50 translate-middle-x pt-5 pt-md-1 d-flex flex-column align-items-center"
                        style="z-index: 1100;"></div>

                    <!-- Navbar for mobile -->
                    <?php include '../shared/components/prof-navbar-for-mobile.php'; ?>

                    <div class="container-fluid py-3 overflow-y-auto row-padding-top">
                        <div class="row justify-content-center mobile-view ">
                            <!-- Header Section -->
                            <div class="row p-0 align-items-center mb-3 text-center text-lg-start">
                                <!-- Title -->
                                <div class="col-12 col-md-auto mb-3 col-12 col-md-auto text-center text-md-start position-relative">
                                    <h1 class="text-sbold text-25 mb-0 mt-2" style="color: var(--black);">Assess
                                    </h1>
                                    <span id="filterToggle"
                                            class="position-absolute end-0 top-50 translate-middle-y d-md-none px-2"
                                            role="button" tabindex="0" aria-label="Show filters"
                                            style="cursor: pointer; user-select: none; ">
                                            <span class="material-symbols-rounded mt-2"
                                                style="font-size: 30px; color: var(--black);">
                                                tune
                                            </span>
                                        </span>
                                </div>

                                <!-- Dropdowns-->
                                <div class="col-12 col-md-auto d-flex flex-wrap justify-content-center justify-content-md-start gap-3 mt-2 mt-md-0 d-none d-md-flex"
                                        style="row-gap: 0!important;" id="mobileFilters">
                                    <div
                                        class="d-flex flex-wrap flex-lg-nowrap justify-content-center justify-content-lg-start text-reg">

                                        <!-- Course dropdown -->
                                        <div class="d-flex align-items-center flex-nowrap dropdown-container">
                                            <span class="dropdown-label me-2">Course</span>
                                            <button class="btn dropdown-toggle dropdown-custom" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <span>All</span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item text-reg" data-value="All">All</a></li>
                                                <?php
                                                if (mysqli_num_rows($getCoursesResult) > 0) {
                                                    while ($courseCodes = mysqli_fetch_assoc($getCoursesResult)) {
                                                        ?>
                                                        <li><a class="dropdown-item text-reg"
                                                                data-value="<?php echo $courseCodes['courseCode']; ?>">
                                                                <?php echo $courseCodes['courseCode']; ?>
                                                            </a></li>

                                                        <?php
                                                    }
                                                }
                                                ?>

                                            </ul>
                                        </div>

                                        <!-- Sort By dropdown -->
                                        <div class="d-flex align-items-center flex-nowrap dropdown-container">
                                            <span class="dropdown-label me-2">Sort By</span>
                                            <button class="btn dropdown-toggle dropdown-custom" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <span>Newest</span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item text-reg" data-value="Desc">Newest</a></li>
                                                <li><a class="dropdown-item text-reg" data-value="Asc">Oldest</a></li>
                                                </li>
                                            </ul>
                                        </div>

                                        <!-- Status dropdown -->
                                        <div class="d-flex align-items-center flex-nowrap dropdown-container">
                                            <span class="dropdown-label me-2">Status</span>
                                            <button class="btn dropdown-toggle dropdown-custom" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <span>Active</span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item text-reg" data-value="0">Active</a></li>
                                                <li><a class="dropdown-item text-reg" data-value="1">Archived</a></li>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Assessment Card -->
                                <?php
                                $chartsIDs = [];
                                $submitted = [];
                                if (mysqli_num_rows($assessmentsResult) > 0) {
                                    mysqli_data_seek($assessmentsResult, 0);
                                    $i = 1;
                                    $cardIndex = 0;
                                    foreach ($assessments as $assessment) {
                                        $cardIndex++;
                                        $ID = $assessment['assessmentID'];
                                        $type = $assessment['type'];
                                        $assessmentTitle = $assessment['assessmentTitle'];
                                        $courseCode = $assessment['courseCode'];
                                        $courseTitle = $assessment['courseTitle'];
                                        $deadline = $assessment['assessmentDeadline'];
                                        $submittedCount = $assessment['submittedTodo'];
                                        $pendingCount = $assessment['pending'];
                                        $returnedCount = $assessment['returned'];
                                        $missingCount = $assessment['missing'];
                                        $cardSubmissionID = $assessment['submissionID'];
                                        $archiveStatus = $assessment['isArchived'];
                                        $rubricIDs = $assessment['rubricID'];

                                        $chartsIDs[] = "chart$i";
                                        $submitted[] = $submittedCount;
                                        $pending[] = $pendingCount;
                                        $returned[] = $returnedCount;
                                        $missing[] = $missingCount;
                                        $isArchived[] = $archiveStatus;
                                        ?>
                                        <div class="row p-0 m-0 ps-3 ps-md-1 pe-3 pe-md-0 m-0 mt-3 pt-1">
                                            <div class="assessment-card mb-3 ms-0 ms-lg-2 text-start" data-course="<?php echo $courseCode; ?>"
                                                data-sort="<?php echo $cardIndex; ?>"
                                                data-status="<?php echo $archiveStatus; ?>">
                                                <div class="card-content">
                                                    <!-- Top Row: Left Info and Submission Stats -->
                                                    <div class="top-row overflow-hidden">
                                                        <!-- Left Info -->
                                                        <div class="left-info">
                                                            <div class="mb-2 text-reg">
                                                                <span
                                                                    class="badge rounded-pill task-badge"><?php echo $type; ?></span>
                                                            </div>
                                                            <div class="text-sbold text-18 mb-2 text-truncate"
                                                                title="<?php echo $assessmentTitle; ?>" style="width: 200px;">
                                                                <?php echo $assessmentTitle; ?>
                                                            </div>
                                                            <div class="text-sbold text-14"><?php echo $courseCode; ?><br>
                                                                <div class="text-reg text-14"><?php echo $courseTitle; ?></div>
                                                            </div>
                                                        </div>

                                                        <!-- Submission Stats -->

                                                        <div class="submission-stats">
                                                            <div class="text-reg text-14 mb-1"><span
                                                                    class="stat-value"><?php echo $submittedCount; ?></span>
                                                                submitted</div>
                                                            <div class="text-reg text-14 mb-1"><span
                                                                    class="stat-value"><?php echo $pendingCount; ?></span>
                                                                pending submission</div>
                                                            <div class="text-reg text-14 mb-1"><span
                                                                    class="stat-value"><?php echo $returnedCount; ?></span>
                                                                returned</div>
                                                            <div class="text-reg text-14">Due <?php echo $deadline; ?></div>
                                                        </div>

                                                        <!-- Right Side: Progress Chart and Options -->
                                                        <div class="right-section">
                                                            <div class="chart-container" id="chart-container<?php echo $i; ?>">
                                                                <canvas id="chart<?php echo $i; ?>" width="120"
                                                                    height="120"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Bottom Row: Action Buttons -->
                                                    <div class="bottom-row">
                                                        <div class="action-buttons">
                                                            <a
                                                                href="<?php echo ($type == 'Task') ? 'assess-task-details.php?assessmentID=' . $ID : 'assess-exam-details.php?assessmentID=' . $ID; ?>"><button
                                                                    class="btn btn-action" style="background: var(--primaryColor); border-color: var(--black); color:var(--black)">
                                                                    <span class="material-symbols-rounded me-2"
                                                                        style="font-size: 15px; color: var(--black);">
                                                                        info
                                                                    </span><?php echo ($type == 'Task') ? 'Task' : 'Test'; ?>
                                                                    Details
                                                                </button></a>
                                                            <?php if ($type == 'Task') { ?>
                                                                <?php if ($cardSubmissionID != null) { ?><a
                                                                        href="<?php echo ($rubricIDs == null || $rubricIDs == 0) ? 'grading-sheet.php?' : 'grading-sheet-rubrics.php?'; ?>submissionID=<?php echo $cardSubmissionID; ?>"><?php } ?>
                                                                    <?php if ($cardSubmissionID == null) { ?>
                                                                        <div title="No submissions in this assessment yet"><?php } ?>
                                                                        <button class="btn btn-action" <?php echo ($cardSubmissionID == null) ? 'disabled' : 'style="background: var(--primaryColor); border-color: var(--black); color:var(--black)"' ?>>
                                                                            <span class="material-symbols-rounded me-2"
                                                                                style="font-size: 15px; color: var(--black);">
                                                                                assignment
                                                                            </span>Grading
                                                                            Sheet
                                                                        </button>
                                                                        <?php if ($cardSubmissionID == null) { ?>
                                                                        </div><?php } ?>
                                                                    <?php if ($cardSubmissionID != null) { ?>
                                                                    </a><?php } ?>
                                                                <?php
                                                            }
                                                            ?>
                                                        </div>
                                                        <!-- More Options aligned with buttons on the right -->
                                                        <div class="options-container">
                                                            <div class="dropdown dropend text-reg">
                                                                <button class="btn btn-link more-options" type="button"
                                                                    data-bs-toggle="dropdown" aria-expanded="false"
                                                                    style="transform: none !important; box-shadow: none !important; background-color: white!important; border:0px!important">
                                                                    <i class="fas fa-ellipsis-v"></i>
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li><a class="dropdown-item" href="#" id="<?php echo $i; ?>"
                                                                            onclick="archive(this, <?php echo $ID; ?>);"><i
                                                                                class="fas fa-archive me-2"></i>
                                                                            <?php echo ($archiveStatus == 0) ? 'Mark as Archived' : 'Unarchive'; ?></a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <?php
                                        $i++;
                                    }
                                } else {
                                    ?>
                                    <div
                                        class="text-sbold text-center mt-5 text-25 d-flex flex-column align-items-center text-sm-20">
                                        <img src="../shared/assets/img/empty/todo.png" alt="No Assessments"
                                            class="mx-auto mt-5 img-fluid img-small" width="250px">
                                        Nothing to assess here.
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            function createDoughnutChart(canvasId, submitted, pending, returned, missing) {
                const ctx = document.getElementById(canvasId).getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: [submitted, pending, returned, missing],
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

            function showDoughnut() {
                document.addEventListener("DOMContentLoaded", function () {
                    const chartsIDs = <?php echo json_encode($chartsIDs); ?>;
                    const submitted = <?php echo json_encode($submitted); ?>;
                    const pending = <?php echo json_encode($pending); ?>;
                    const returned = <?php echo json_encode($returned); ?>;
                    const missing = <?php echo json_encode($missing); ?>;
                    const isArchived = <?php echo json_encode($isArchived); ?>

                    chartsIDs.forEach(function (id, index) {
                        if (isArchived[index] == 0) {
                            createDoughnutChart(id, submitted[index], pending[index], returned[index], missing[index])
                        } else {
                            const archiveLabel = document.getElementById('chart-container' + (index + 1));
                            archiveLabel.innerHTML = `<span class="badge rounded-pill text-bg-secondary text-reg">Archived</span>`;
                        }
                    });
                });
            }


            function archive(element, ID) {
                const archiveLabel = document.getElementById('chart-container' + element.id);
                const archiveDropdown = document.getElementById(element.id);

                if (archiveDropdown.textContent == 'Mark as Archived') {
                    archiveDropdown.innerHTML = `<i class="fas fa-archive me-2"></i>Unarchive`;
                } else if (archiveDropdown.textContent == 'Unarchive') {
                    archiveDropdown.innerHTML = `<i class="fas fa-archive me-2"></i>Mark as Archived`;
                }

                fetchArchiveQuery(ID);
                showDoughnut();
            }

            function fetchArchiveQuery(assessmentID) {
                fetch('../shared/assets/processes/archive-assessment.php?assessmentID=' + assessmentID)
                    .then(response => {
                        if (!response.ok) {
                            
                        } else {
                            
                            window.location.reload();
                        }
                    })
                    .catch(error => {
                        
                    });
            }

            showDoughnut();
        </script>

         <!-- Toggle JS -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const filterToggle = document.getElementById("filterToggle");
            const mobileFilters = document.getElementById("mobileFilters");
            const icon = filterToggle.querySelector(".material-symbols-rounded");

            const storageKey = "filtersVisible_" + "<?php echo $activePage; ?>";

            if (localStorage.getItem(storageKey) === "true") {
                mobileFilters.classList.remove("d-none");
                filterToggle.classList.add("active");
                icon.textContent = "close";
            }

            filterToggle.addEventListener("click", () => {
                const isVisible = !mobileFilters.classList.contains("d-none");

                // Toggle panel
                mobileFilters.classList.toggle("d-none");

                // Toggle icon
                filterToggle.classList.toggle("active");
                icon.textContent = filterToggle.classList.contains("active") ? "close" : "tune";

                // Save state
                localStorage.setItem(storageKey, !isVisible);
            });
        });
        
    </script>

    <!-- Toast Handling -->
            <?php if (!empty($toastMessage)): ?>
                <script>
                    window.addEventListener('DOMContentLoaded', () => {
                        const container = document.getElementById("toastContainer");
                        if (!container) return;

                        const alert = document.createElement("div");
                        alert.className = `alert mb-2 shadow-lg text-med text-12 d-flex align-items-center justify-content-center gap-2 px-3 py-2 <?= $toastType ?>`;
                        alert.role = "alert";
                        alert.innerHTML = `
            <i class="bi <?= ($toastType === 'alert-success') ? 'bi-check-circle-fill' : 'bi-x-circle-fill'; ?> fs-6"></i>
            <span><?= addslashes($toastMessage) ?></span>
        `;
                        container.appendChild(alert);

                        setTimeout(() => alert.remove(), 3000);
                    });
                </script>
            <?php endif; ?>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                //Get dropdown containers and all cards
                const dropdownContainers = document.querySelectorAll('.dropdown-container');
                const cards = document.querySelectorAll('.assessment-card');

                //Current selected values
                let currentCourse = 'All';
                let currentStatus = '0';
                let currentSort = 'Desc';

                //Filter and sort cards
                function filterCards() {
                    cards.forEach(card => {
                        const cardCourse = card.dataset.course || '';
                        const cardStatus = card.dataset.status || '';

                        const matchCourse = (currentCourse === 'All') || (cardCourse === currentCourse);
                        const matchStatus = (cardStatus === currentStatus);

                        card.style.display = (matchCourse && matchStatus) ? '' : 'none';
                    });

                    //Sorting by data-sort (cardIndex)
                    const container = cards[0].parentNode;
                    const sortedCards = Array.from(cards).sort((a, b) => {
                        const aIndex = parseInt(a.dataset.sort || 0, 10);
                        const bIndex = parseInt(b.dataset.sort || 0, 10);
                        return (currentSort === 'Asc') ? aIndex - bIndex : bIndex - aIndex;
                    });

                    sortedCards.forEach(c => container.appendChild(c));

                    // Remove previous empty-state if it exists
                    const existingEmpty = container.querySelector(".empty-assessments");
                    if (existingEmpty) existingEmpty.remove();

                    // Append empty-state ONLY if no visible cards
                    const visibleCards = sortedCards.filter(c => c.style.display !== 'none');
                    if (visibleCards.length === 0) {
                        let empty = document.createElement('div');
                        empty.className = "empty-assessments text-sbold text-center mt-5 text-25 d-flex flex-column align-items-center text-sm-20";
                        empty.innerHTML = `
                                              <img src="../shared/assets/img/empty/todo.png" alt="No Assessments" class="mx-auto mt-5 img-fluid img-small" width="250px">
                                              Nothing to assess here.
                                          `;
                        container.appendChild(empty);
                    }


                }

                //Handle dropdown clicks
                dropdownContainers.forEach(container => {
                    const labelSpan = container.querySelector('.dropdown-toggle span');
                    const dropdownLabel = container.querySelector('.dropdown-label').textContent.trim();

                    container.querySelectorAll('.dropdown-item').forEach(item => {
                        item.addEventListener('click', function (e) {
                            e.preventDefault();
                            const value = this.dataset.value;

                            //Update dropdown label text
                            labelSpan.textContent = this.textContent.trim();

                            //Update current filter/sort values
                            if (dropdownLabel === 'Course') {
                                currentCourse = value;
                            } else if (dropdownLabel === 'Status') {
                                currentStatus = value;
                            } else if (dropdownLabel === 'Sort By') {
                                currentSort = value; // 'Asc' or 'Desc'
                            }

                            //Apply filtering and sorting
                            filterCards();
                        });
                    });
                });

                //Initialize filter and sort on page load
                filterCards();
            });
        </script>
</body>

</html>