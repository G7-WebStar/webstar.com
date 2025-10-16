<?php $activePage = 'inbox';
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

$selectInboxQuery = "SELECT 
inbox.createdAt AS inboxCreatedAt,
inbox.messageText,
courses.courseCode,
assessments.assessmentTitle AS assessmentTitle,
userinfo.profilePicture AS profPFP
FROM inbox
INNER JOIN enrollments
	ON inbox.enrollmentID = enrollments.enrollmentID
INNER JOIN todo
	ON enrollments.userID = todo.userID
    AND enrollments.courseID = (SELECT courseID 
                               FROM assessments 
                               WHERE assessments.assessmentID = todo.assessmentID)
INNER JOIN assessments
	ON todo.assessmentID = assessments.assessmentID
INNER JOIN courses
	ON assessments.courseID = courses.courseID
INNER JOIN userinfo
	ON courses.userID = userinfo.userID
WHERE enrollments.userID = '$userID' AND todo.status = 'Pending'
ORDER BY inbox.inboxID DESC
";
$selectInboxResult = executeQuery($selectInboxQuery);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Webstar | My Inbox</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css" />
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link rel="icon" type="image/png" href="shared/assets/img/webstar-icon.png" />
    <style>
        @media (min-width: 992px) {
            .responsive-circle {
                width: 45.52px !important;
                height: 45.52px !important;
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

            <div class="col main-container m-0 p-0 mx-0 mx-md-2 p-md-4 overflow-y-auto">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">
                    <?php include 'shared/components/navbar-for-mobile.php'; ?>

                    <div class="container-fluid py-3 overflow-y-auto">
                        <div class="row">
                            <div class="col-12">

                                <!-- Header Section -->
                                <div class="row align-items-center mb-3 text-center text-lg-start">
                                    <!-- Title -->
                                    <div class="col-12 col-lg-auto mb-3 mb-lg-0">
                                        <h1 class="text-bold text-25 mb-0 mt-4" style="color: var(--black);">My Inbox
                                        </h1>
                                    </div>

                                    <!-- Dropdowns-->
                                    <div class="col-12 col-lg-auto mt-4">
                                        <div
                                            class="d-flex flex-nowrap justify-content-center justify-content-lg-start gap-3">

                                            <!-- Sort by dropdown -->
                                            <div class="d-flex align-items-center flex-nowrap">
                                                <span class="dropdown-label me-2">Sort by:</span>
                                                <button class="btn dropdown-toggle dropdown-custom" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <span>Newest</span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item text-reg" href="#">Newest</a></li>
                                                    <li><a class="dropdown-item text-reg" href="#">Oldest</a></li>
                                                    <li><a class="dropdown-item text-reg" href="#">Unread first</a></li>
                                                </ul>
                                            </div>

                                            <!-- Course dropdown -->
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
                                    </div>

                                    <!-- Message Content -->
                                    <div class="message-container mt-4 mt-lg-4 pb-4">
                                        <?php
                                        if (mysqli_num_rows($selectInboxResult) > 0) {
                                            while ($inbox = mysqli_fetch_assoc($selectInboxResult)) {
                                        ?>
                                                <div class="card mb-3 me-3 w-100 mt-3"
                                                    style="max-width: 1101px; border: 1px solid var(--black); border-radius: 15px; background-color: var(--pureWhite); opacity: 1;">
                                                    <div class="card-body p-3">
                                                        <div class="row align-items-start">
                                                            <!-- Profile Image -->
                                                            <div
                                                                class="col-auto mb-3 mb-lg-0 mt-3 mt-lg-2 d-flex justify-content-center justify-content-lg-start ms-3">
                                                                <div class="avatar-image">
                                                                    <img src="shared/assets/pfp-uploads/<?php echo $inbox['profPFP']; ?>" alt="" width="40" height="40"
                                                                        class="rounded-circle responsive-circle ">
                                                                </div>
                                                            </div>

                                                            <!-- Message Text -->
                                                            <div class="col d-flex flex-column text-start mt-3 mt-lg-2 mb-2">
                                                                <p class="mb-2 text-sbold text-17"
                                                                    style="color: var(--black); line-height: 140%;">
                                                                    <?php echo $inbox['messageText'] . " " . $inbox['assessmentTitle']; ?>
                                                                </p>
                                                                <small class="text-reg text-12"
                                                                    style="color: var(--black); opacity: 0.7;">January 12, 2024
                                                                    8:00AM</small>

                                                                <!-- Course tag on small screen below message text -->
                                                                <div class="d-block d-lg-none mt-2">
                                                                    <span
                                                                        class="text-reg text-12 badge rounded-pill course-badge"
                                                                        style="width: 99px; height: 19px; border-radius: 50px; padding: 4px 10px;">
                                                                        <?php echo $inbox['courseCode']; ?>
                                                                    </span>
                                                                </div>
                                                            </div>

                                                            <!-- Course tag on large screen right side, vertically centered -->
                                                            <div
                                                                class="col-auto d-none d-lg-flex justify-content-end align-items-center mt-4">
                                                                <span class="text-reg text-12 badge rounded-pill course-badge"
                                                                    style="width: 99px; height: 19px; border-radius: 50px; padding: 4px 10px;">
                                                                    <?php echo $inbox['courseCode']; ?>
                                                                </span>
                                                            </div>
                                                        </div>

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
    </div>

    </div>
    </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>