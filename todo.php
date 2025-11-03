<?php $activePage = 'todo';
include('shared/assets/database/connect.php');
include("shared/assets/processes/session-process.php");

// Get filter parameters from URL
$sortBy = isset($_GET['sortBy']) ? $_GET['sortBy'] : 'Newest';
$courseFilter = isset($_GET['course']) ? $_GET['course'] : 'All';
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'All';

// Get enrolled courses for dropdown
$selectEnrolledQuery = "SELECT DISTINCT courses.courseCode
    FROM courses
    INNER JOIN enrollments ON courses.courseID = enrollments.courseID
    WHERE enrollments.userID = '$userID'
    ORDER BY courses.courseCode ASC";
$selectEnrolledResult = executeQuery($selectEnrolledQuery);

// Build filter clauses
$statusWhere = "";
if ($statusFilter == 'Pending') {
    $statusWhere = "AND todo.status = 'Pending'";
} elseif ($statusFilter == 'Missing') {
    $statusWhere = "AND todo.status = 'Missing'";
} elseif ($statusFilter == 'Done') {
    $statusWhere = "AND todo.status IN ('Graded', 'Submitted')";
} else {
    $statusWhere = "AND todo.status IN ('Pending', 'Missing', 'Submitted', 'Graded')";
}

$courseWhere = "";
if ($courseFilter != 'All') {
    $courseWhere = "AND courses.courseCode = '" . mysqli_real_escape_string($GLOBALS['conn'], $courseFilter) . "'";
}

$orderBy = ($sortBy == 'Oldest') ? "ORDER BY assessments.deadline ASC" : "ORDER BY assessments.deadline DESC";

$selectAssessmentQuery = "SELECT
    tests.testID,
    assignments.assignmentID,
    assessments.*,
    assessments.assessmentTitle AS assessmentTitle,
    todo.*,
    courses.courseCode,
    DATE_FORMAT(assessments.deadline, '%b %e') AS assessmentDeadline,
    assessments.deadline AS deadline_raw
    FROM assessments
    INNER JOIN courses ON assessments.courseID = courses.courseID
    INNER JOIN todo ON assessments.assessmentID = todo.assessmentID
    LEFT JOIN assignments ON assignments.assessmentID = todo.assessmentID
    LEFT JOIN tests ON tests.assessmentID = todo.assessmentID
    WHERE todo.userID = '$userID' $statusWhere $courseWhere
    GROUP BY assessments.assessmentID
    $orderBy";
$selectAssessmentResult = executeQuery($selectAssessmentQuery);
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | My To-do</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/todo.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="shared/assets/img/webstar-icon.png">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />

</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-0 p-md-3"
        style="background-color: var(--black);">

        <div class="row w-100">

            <!-- Sidebar (only shows on mobile) -->
            <?php include 'shared/components/sidebar-for-mobile.php'; ?>

            <!-- Sidebar Column (fixed on desktop) -->
            <?php include 'shared/components/sidebar-for-desktop.php'; ?>

            <!-- Main Container Column-->
            <div class="col main-container m-0 p-0 mx-0 mx-md-2 p-0 p-md-4 overflow-y-auto">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">

                    <!-- Navbar for mobile -->
                    <?php include 'shared/components/navbar-for-mobile.php'; ?>

                    <div class="container-fluid py-3 row-padding-top">
                        <div class="row">
                            <div class="col-12">

                                <!-- Header Section -->
                                <div class="row align-items-center text-center text-md-start">
                                    <div class="col-12 col-md-auto text-center text-md-start position-relative">
                                        <h1 class="text-sbold text-25 my-2" style="color: var(--black);">My Quests
                                        </h1>

                                        <!-- Filter Icon (mobile only) -->
                                        <span id="filterToggle"
                                            class="position-absolute end-0 top-50 translate-middle-y d-md-none px-2"
                                            role="button" tabindex="0" aria-label="Show filters"
                                            style="cursor: pointer; user-select: none;">
                                            <span class="material-symbols-rounded"
                                                style="font-size: 30px; color: var(--black);">
                                                tune
                                            </span>
                                        </span>
                                    </div>

                                    <!-- Dropdowns -->
                                    <div class="col-12 col-md-auto d-flex flex-wrap justify-content-center justify-content-md-start gap-3 mt-2 mt-md-0 d-none d-md-flex"
                                        style="row-gap: 0!important;" id="mobileFilters">

                                        <!-- Sort By -->
                                        <div class="col-auto mobile-dropdown p-0">
                                            <div class="d-flex align-items-center flex-nowrap my-2">
                                                <span class="dropdown-label me-2 text-reg">Sort by</span>
                                                <div class="custom-dropdown" data-filter="sortBy">
                                                    <button class="dropdown-btn text-reg text-14"
                                                        data-current="<?php echo $sortBy; ?>">
                                                        <?php echo $sortBy; ?>
                                                    </button>
                                                    <ul class="dropdown-list text-reg text-14">
                                                        <li data-value="Newest">Newest</li>
                                                        <li data-value="Oldest">Oldest</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Course -->
                                        <div class="col-auto mobile-dropdown p-0">
                                            <div class="d-flex align-items-center flex-nowrap my-2">
                                                <span class="dropdown-label me-2 text-reg">Courses</span>
                                                <div class="custom-dropdown" data-filter="course">
                                                    <button class="dropdown-btn text-reg text-14"
                                                        data-current="<?php echo $courseFilter; ?>">
                                                        <?php echo $courseFilter; ?>
                                                    </button>
                                                    <ul class="dropdown-list text-reg text-14">
                                                        <li data-value="All">All</li>
                                                        <?php
                                                        if ($selectEnrolledResult && mysqli_num_rows($selectEnrolledResult) > 0) {
                                                            while ($course = mysqli_fetch_assoc($selectEnrolledResult)) {
                                                                ?>
                                                                <li data-value="<?php echo $course['courseCode']; ?>">
                                                                    <?php echo $course['courseCode']; ?>
                                                                </li>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Status -->
                                        <div class="col-auto mobile-dropdown p-0">
                                            <div class="d-flex align-items-center flex-nowrap my-2">
                                                <span class="dropdown-label me-2 text-reg">Status</span>
                                                <div class="custom-dropdown" data-filter="status">
                                                    <button class="dropdown-btn text-reg text-14"
                                                        data-current="<?php echo $statusFilter; ?>">
                                                        <?php echo $statusFilter; ?>
                                                    </button>
                                                    <ul class="dropdown-list text-reg text-14">
                                                        <li data-value="All">All</li>
                                                        <li data-value="Pending">Pending</li>
                                                        <li data-value="Missing">Missing</li>
                                                        <li data-value="Done">Done</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>

                                <!-- Task container -->
                                <div class="row mb-0 mt-2 mx-auto">
                                    <div class="col-12 col-md-10 mt-3 mx-auto mx-md-0 p-0">
                                        <?php
                                        if (mysqli_num_rows($selectAssessmentResult) > 0) {
                                            while ($todo = mysqli_fetch_assoc($selectAssessmentResult)) {
                                                $type = strtolower(trim($todo['type']));
                                                $link = "#";

                                                if ($type === 'task') {
                                                    $link = "assignment.php?assignmentID=" . $todo['assignmentID'];
                                                } elseif ($type === 'test') {
                                                    $link = "test.php?testID=" . $todo['testID'];
                                                }
                                                ?>
                                                <a href="<?php echo $link; ?>" class="text-decoration-none"
                                                    style="display: block;">
                                                    <div class="todo-card d-flex align-items-stretch mb-2 mx-auto mx-lg-0"
                                                        style="cursor: pointer;">
                                                        <!-- Date -->
                                                        <div class="date d-flex align-items-center justify-content-center text-sbold text-20"
                                                            style="text-transform:uppercase; background-color: var(--primaryColor)">
                                                            <?php echo $todo['assessmentDeadline']; ?>
                                                        </div>

                                                        <!-- Main content -->
                                                        <div
                                                            class="d-flex flex-grow-1 flex-wrap justify-content-between p-2 w-100">
                                                            <!-- For small screen of main content -->
                                                            <div class="px-3 py-0">
                                                                <div class="text-sbold text-16 text-start assessment-title">
                                                                    <?php echo $todo['assessmentTitle']; ?>
                                                                </div>
                                                                <div class="text-reg text-12 text-start">
                                                                    <?php echo $todo['courseCode']; ?>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex align-items-center justify-content-center"
                                                                style="height:100%;">
                                                                <span
                                                                    class="course-badge rounded-pill px-3 text-reg text-12 d-none d-lg-inline me-3">
                                                                    <?php echo $todo['type']; ?>
                                                                </span>

                                                                <div class="d-flex align-items-center justify-content-center">
                                                                    <i class="fa-solid fa-arrow-right text-reg text-12 pe-2"
                                                                        style="color:var(--black);"></i>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </a>
                                                <?php
                                            }
                                        } else {
                                            $emptyImage = "shared/assets/img/empty/todo.png";
                                            if ($statusFilter == 'Missing') {
                                                $emptyImage = "shared/assets/img/empty/quest.png";
                                            } elseif ($statusFilter == 'Done') {
                                                $emptyImage = "shared/assets/img/empty/folder.png";
                                            }
                                            ?>
                                            <div
                                                class="empty-state text-center py-5 d-flex flex-column align-items-center justify-content-center mx-auto">
                                                <img src="<?php echo $emptyImage; ?>" alt="Empty state">
                                                <?php if ($statusFilter == 'Missing') { ?>
                                                    <div class="mt-3 text-center empty-text">
                                                        No missing quests.
                                                    </div>
                                                    <span class="empty-subtext">
                                                        You’re right on track, adventurer!
                                                    </span>
                                                <?php } elseif ($statusFilter == 'Done') { ?>
                                                    <div class="mt-3 text-center empty-text">
                                                        You haven’t submitted any quests yet.
                                                    </div>
                                                    <span class="empty-subtext">
                                                        Complete one to earn XPs!
                                                    </span>
                                                <?php } else { ?>
                                                    <div class="mt-3 text-center empty-text">
                                                        No quests have been assigned yet.
                                                    </div>
                                                    <span class="empty-subtext">
                                                        Your next adventure awaits!
                                                    </span>
                                                <?php } ?>
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
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Filters Toggle -->
        <script>
            document.getElementById("filterToggle").addEventListener("click", () => {
                document.getElementById("mobileFilters").classList.toggle("d-none");
            });
        </script>

        <!-- Dropdown js -->
        <script>
            document.querySelectorAll('.custom-dropdown').forEach(dropdown => {
                const btn = dropdown.querySelector('.dropdown-btn');
                const list = dropdown.querySelector('.dropdown-list');
                const filterType = dropdown.getAttribute('data-filter');

                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    list.style.display = list.style.display === 'block' ? 'none' : 'block';
                });

                list.querySelectorAll('li').forEach(item => {
                    item.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const selectedValue = item.dataset.value;
                        btn.textContent = selectedValue;
                        list.style.display = 'none';

                        // Update URL with new filter value
                        const urlParams = new URLSearchParams(window.location.search);
                        urlParams.set(filterType, selectedValue);
                        window.location.search = urlParams.toString();
                    });
                });

                // Close dropdown if clicked outside
                document.addEventListener('click', (e) => {
                    if (!dropdown.contains(e.target)) {
                        list.style.display = 'none';
                    }
                });
            });
        </script>

</body>


</html>