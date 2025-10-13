<?php
$activePage = 'assignment';

include('shared/assets/database/connect.php');

session_start();

include("shared/assets/processes/session-process.php");

$assignmentID = intval($_GET['assignmentID']);

$userQuery = "SELECT * FROM users 
              LEFT JOIN userinfo ON users.userID = userInfo.userID 
              WHERE users.userID = $userID";
$userResult = executeQuery($userQuery);

$assignmentQuery = "SELECT 
                    assessments.assessmentTitle,
                    assessments.deadline,
                    assignments.assignmentDescription,
                    assignments.assignmentPoints,
                    userinfo.firstName,
                    userinfo.lastName,
                    userinfo.profilePicture,
                    scores.score
                    FROM courses 
                    INNER JOIN assessments ON courses.courseID = assessments.courseID 
                    INNER JOIN assignments ON assessments.assessmentID = assignments.assessmentID
                    INNER JOIN userinfo ON courses.userID = userInfo.userID 
                    INNER JOIN scores ON assignments.assignmentID = scores.assignmentID
                    WHERE assignments.assignmentID = $assignmentID";
$assignmentResult = executeQuery($assignmentQuery);

$assignmentRow = mysqli_fetch_assoc($assignmentResult);


$assignmentTitle = $assignmentRow['assessmentTitle'];
$assignmentDescription = $assignmentRow['assignmentDescription'];
$profName = $assignmentRow['firstName'] . ' ' . $assignmentRow['lastName'];
$profProfile = $assignmentRow['profilePicture'];
$deadline = $assignmentRow['deadline'];
$score = $assignmentRow['score'] ?? null;
$totalPoints = $assignmentRow['assignmentPoints'] ?? 0;

$filesQuery = "SELECT * FROM files WHERE assignmentID = '$assignmentID'";
$filesResult = executeQuery($filesQuery);

$attachmentsArray = [];
$linksArray = [];

while ($file = mysqli_fetch_assoc($filesResult)) {
    if (!empty($file['fileAttachment'])) {
        $attachments = array_map('trim', explode(',', $file['fileAttachment']));
        $attachmentsArray = array_merge($attachmentsArray, $attachments);
    }

    if (!empty($file['fileLink'])) {
        $links = array_map('trim', explode(',', $file['fileLink']));
        $linksArray = array_merge($linksArray, $links);
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Assignment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/assignment.css">
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
                        <div class="row mb-3">
                            <div class="col-12 cardHeader p-3 mb-4">

                                <!-- DESKTOP VIEW -->
                                <div class="row desktop-header d-none d-sm-flex">
                                    <div class="col-auto me-2">
                                        <a href="todo.php" class="text-decoration-none">
                                            <i class="fa-solid fa-arrow-left text-reg text-16"
                                                style="color: var(--black);"></i>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <span class="text-sbold text-25"><?php echo $assignmentTitle ?></span>
                                        <div class="text-reg text-18">Due <?php echo date("M d, Y", strtotime($deadline)); ?></div>
                                    </div>
                                    <div class="col-auto text-end">
                                        <?php echo $score !== null ? 'Graded' : 'Pending'; ?>
                                        <div class="text-sbold text-25">
                                            <?php
                                            echo $score !== null ? $score : '-';
                                            ?>
                                            <span class="text-muted">/<?php echo $totalPoints; ?></span>
                                        </div>
                                    </div>
                                </div>


                                <!-- MOBILE VIEW -->
                                <div class="d-block d-sm-none mobile-assignment">
                                    <div class="mobile-top">
                                        <div class="arrow">
                                            <a href="todo.php" class="text-decoration-none">
                                                <i class="fa-solid fa-arrow-left text-reg text-16"
                                                    style="color: var(--black);"></i>
                                            </a>
                                        </div>
                                        <div class="title text-sbold text-25"><?php echo $assignmentTitle ?></div>
                                    </div>
                                    <div class="due text-reg text-18">Due <?php echo date("M d, Y", strtotime($deadline)); ?></div>
                                    <div class="graded text-reg text-18 mt-4"><?php echo $score !== null ? 'Graded' : 'Pending'; ?></div>
                                    <div class="score text-sbold text-25">
                                        <?php
                                        echo $score !== null ? $score : '-';
                                        ?>
                                        <span class="text-muted"><?php echo $totalPoints; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Left Content -->
                            <div class="col-12 col-lg-8">
                                <div class="p-0 px-lg-5">
                                    <div class="text-sbold text-14 mt-3">Instructions</div>
                                    <p class="mb-5 mt-3 text-med text-14"><?php echo nl2br($assignmentDescription) ?></p>

                                    <hr>

                                    <div class="text-sbold text-14 mt-4">Attachments</div>
                                    <?php foreach ($attachmentsArray as $file):
                                        $filePath = "shared/uploads/" . $file;
                                        $fileExt = strtoupper(pathinfo($file, PATHINFO_EXTENSION));
                                        $fileSize = (file_exists($filePath)) ? filesize($filePath) : 0;
                                        $fileSizeMB = $fileSize > 0 ? round($fileSize / 1048576, 2) . " MB" : "Unknown size";

                                        // Remove extension from display name
                                        $fileNameOnly = pathinfo($file, PATHINFO_FILENAME);
                                    ?>
                                        <div class="cardFile my-3 w-lg-25 d-flex align-items-start" style="width:400px; max-width:100%; min-width:310px;">
                                            <i class="px-4 py-3 fa-solid fa-file"></i>
                                            <div class="ms-2">
                                                <div class="text-sbold text-16 mt-1"><?php echo $fileNameOnly ?></div>
                                                <div class="due text-reg text-14 mb-1"><?php echo $fileExt ?> · <?php echo $fileSizeMB ?></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>


                                    <?php foreach ($linksArray as $link): ?>
                                        <div class="cardFile my-3 w-lg-25 d-flex align-items-start" style="width:400px; max-width:100%; min-width:310px;">
                                            <i class="px-4 py-3 fa-solid fa-link" style="font-size: 13px;"></i>
                                            <div class="ms-2">
                                                <!-- temoparary lang ang filename here -->
                                                <div class="text-sbold text-16 mt-1"><?php echo $fileNameOnly ?></div>
                                                <div class="text-reg link text-12 mt-0">
                                                    <a href="<?php echo $link ?>" target="_blank" rel="noopener noreferrer" style="text-decoration: none; color: var(--black);">
                                                        <?php echo $link ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>

                                    <hr>

                                    <div class="text-sbold text-14 pb-3">Prepared by</div>
                                    <div class="d-flex align-items-center pb-5">
                                        <div class="rounded-circle me-2"
                                            style="width: 50px; height: 50px; background-color: var(--highlight75);">
                                            <img src="shared/assets/pfp-uploads/<?php echo $profProfile ?>" alt="professor" class="rounded-circle" style="width:50px;height:50px;">
                                        </div>
                                        <div>
                                            <div class="text-sbold text-14"><?php echo $profName ?></div>
                                            <div class="text-med text-12">January 12, 2024 8:00AM</div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-12 col-lg-4">
                                <div class="cardSticky position-sticky" style="top: 20px;">
                                    <div class="p-2">
                                        <div class="text-sbold text-16">My work</div>
                                        <div class="cardFile text-sbold text-16 my-3">
                                            <i class="p-3 fa-solid fa-file"></i> Submission
                                        </div>

                                        <div class="text-sbold text-16">Status</div>
                                        <ul class="timeline list-unstyled small my-3">
                                            <li class="timeline-item">
                                                <div class="timeline-circle bg-dark"></div>
                                                <div class="timeline-content">
                                                    <div class="text-reg text-16">Assignment is ready to work on.</div>
                                                    <div class="text-reg text-12">Sep 9, 2024, 10:00PM</div>
                                                </div>
                                            </li>
                                            <li class="timeline-item">
                                                <div class="timeline-circle bg-dark"></div>
                                                <div class="timeline-content">
                                                    <div class="text-reg text-16">Your assignment has been submitted.
                                                    </div>
                                                    <div class="text-reg text-12">Sep 9, 2024, 10:00PM</div>
                                                </div>
                                            </li>
                                            <li class="timeline-item">
                                                <div class="timeline-circle big"
                                                    style="background-color: var(--primaryColor);"></div>
                                                <div class="timeline-content">
                                                    <div class="text-reg text-16">Your assignment has been graded.</div>
                                                    <div class="text-reg text-12">Sep 9, 2024, 10:00PM</div>
                                                </div>
                                            </li>
                                        </ul>

                                        <div class="d-flex gap-2 pt-3">
                                            <button class="button px-3 py-1 flex-fill rounded-pill text-reg text-md-14">
                                                + Attach Files
                                            </button>
                                            <button class="button px-3 py-1 flex-fill rounded-pill text-reg text-md-14"
                                                style="background-color: var(--primaryColor);">
                                                Turn In
                                            </button>
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