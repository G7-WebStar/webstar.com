<?php $activePage = 'todo';
include('shared/assets/database/connect.php');
include("shared/assets/processes/session-process.php");

$selectEnrolledQuery = "SELECT
courses.courseCode
FROM courses
INNER JOIN enrollments
    ON courses.courseID = enrollments.courseID
WHERE enrollments.userID = '$userID'
";
$selectEnrolledResult = executeQuery($selectEnrolledQuery);

$selectAssessmentQuery = "SELECT
	assignments.assignmentID,
    assessments.*,
    assessments.assessmentTitle AS assessmentTitle,
    todo.*,
    todo.title AS todoTitle,
    courses.courseCode,
    DATE_FORMAT(assessments.deadline, '%b %e') AS assessmentDeadline
    FROM assessments
    INNER JOIN courses
        ON assessments.courseID = courses.courseID
    INNER JOIN todo
    	ON assessments.assessmentID = todo.assessmentID
    INNER JOIN assignments
    	ON assignments.assessmentID = todo.assessmentID
    WHERE todo.userID = '$userID' AND todo.status = 'Pending'
    GROUP BY assignments.assignmentID
    ORDER BY todo.assessmentID DESC
";
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
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="shared/assets/img/webstar-icon.png">

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

                    <div class="container-fluid py-3 overflow-y-auto">
                        <div class="row">
                            <div class="col-12">

                                <!-- Header Section -->
                                <div class="row align-items-center mb-5 text-center text-lg-start">
                                    <!-- Title -->
                                    <div class="col-12 col-lg-auto mb-3 mb-lg-0">
                                        <h1 class="text-bold text-25 mb-0 mt-4" style="color: var(--black);">My To-do
                                        </h1>
                                    </div>

                                    <div class="col-12 col-lg-auto mt-4">
                                        <div class="row g-3 justify-content-center justify-content-lg-start">

                                            <!-- Sort by dropdown -->
                                            <div class="col-6 col-md-auto">
                                                <div class="d-flex align-items-center flex-nowrap">
                                                    <span class="dropdown-label me-2">Sort by:</span>
                                                    <button class="btn dropdown-toggle dropdown-custom" type="button"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <span>Newest</span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item text-reg" href="#">Newest</a></li>
                                                        <li><a class="dropdown-item text-reg" href="#">Oldest</a></li>
                                                        <li><a class="dropdown-item text-reg" href="#">Unread first</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <!-- Course dropdown -->
                                            <div class="col-6 col-md-auto">
                                                <div class="d-flex align-items-center flex-nowrap">
                                                    <span class="dropdown-label me-2">Course:</span>
                                                    <button class="btn dropdown-toggle dropdown-custom" type="button"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <span>All</span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item text-reg" href="#">All</a></li>
                                                        <?php
                                                        if (mysqli_num_rows($selectEnrolledResult) > 0) {
                                                            while ($inboxSelectTag = mysqli_fetch_assoc($selectEnrolledResult)) {
                                                        ?>
                                                                <li><a class="dropdown-item text-reg" href="#"><?php echo $inboxSelectTag['courseCode']; ?></a></li>
                                                        <?php
                                                            }
                                                        }
                                                        ?>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <!-- Status dropdown -->
                                            <div class="col-6 col-md-auto mx-auto">
                                                <div class="d-flex align-items-center flex-nowrap">
                                                    <span class="dropdown-label me-2">Status:</span>
                                                    <button class="btn dropdown-toggle dropdown-custom" type="button"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <span>Assigned</span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item text-reg" href="#">Assigned</a></li>
                                                        <li><a class="dropdown-item text-reg" href="#">Completed</a></li>
                                                        <li><a class="dropdown-item text-reg" href="#">Overdue</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Task container -->
                                <div class="row mb-0 mt-3">
                                    <div class="col-12 col-md-10">
                                        <?php
                                        if (mysqli_num_rows($selectAssessmentResult) > 0) {
                                            while ($todo = mysqli_fetch_assoc($selectAssessmentResult)) {
                                        ?>
                                                <div class="todo-card d-flex align-items-stretch mb-2">
                                                    <!-- Date -->
                                                    <div
                                                        class="date d-flex align-items-center justify-content-center text-sbold text-20">
                                                        <?php echo $todo['assessmentDeadline']; ?>
                                                    </div>
                                                    <!-- Main content -->
                                                    <div class="d-flex flex-grow-1 flex-wrap justify-content-between p-2 w-100">
                                                        <!-- For small screen of main content -->
                                                        <div class="px-3 py-0">
                                                            <div class="text-sbold text-16">
                                                                <?php echo $todo['assessmentTitle']; ?>
                                                            </div>
                                                            <div class="text-reg text-12">
                                                                <?php echo $todo['courseCode']; ?>
                                                            </div>
                                                            <span
                                                                class="course-badge rounded-pill px-3 text-reg text-12 mt-2 d-inline d-md-none">
                                                                <?php echo $todo['type']; ?>
                                                            </span>

                                                            <?php
                                                            $type = strtolower(trim($todo['type']));
                                                            $link = "#";

                                                            if ($type === 'task') {
                                                                $link = "assignment.php?assignmentID=" . $todo['assignmentID'];
                                                            } elseif ($type === 'test') {
                                                                $link = "test.php?testID=" . $todo['examID'];
                                                            }
                                                            ?>

                                                        </div>
                                                        <!-- Pill and Arrow on Large screen-->
                                                        <a href="<?php echo $link; ?>" class="text-decoration-none">
                                                            <i class="fa-solid fa-arrow-right text-reg text-12 pe-2" style="color: var(--black);"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                        <?php
                                            }
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

</body>


</html>