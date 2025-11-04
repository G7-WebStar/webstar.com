<?php
$activePage = 'assignment';

include('shared/assets/database/connect.php');
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
                    assignments.assignmentPoints
                    FROM courses 
                    INNER JOIN assessments ON courses.courseID = assessments.courseID 
                    INNER JOIN assignments ON assessments.assessmentID = assignments.assessmentID
                    INNER JOIN userinfo ON courses.userID = userInfo.userID 
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

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

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

                    <div class="container-fluid py-3 overflow-y-auto row-padding-top">
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
                                        <div class="text-reg text-18">Due
                                            <?php echo date("M d, Y", strtotime($deadline)); ?>
                                        </div>
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
                                    <div class="due text-reg text-18">Due
                                        <?php echo date("M d, Y", strtotime($deadline)); ?>
                                    </div>
                                    <div class="graded text-reg text-18 mt-4">
                                        <?php echo $score !== null ? 'Graded' : 'Pending'; ?>
                                    </div>
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
                                    <p class="mb-5 mt-3 text-med text-14"><?php echo nl2br($assignmentDescription) ?>
                                    </p>

                                    <hr>

                                    <div class="text-sbold text-14 mt-4">Task Materials</div>
                                    <?php foreach ($attachmentsArray as $file):
                                        $filePath = "shared/uploads/" . $file;
                                        $fileExt = strtoupper(pathinfo($file, PATHINFO_EXTENSION));
                                        $fileSize = (file_exists($filePath)) ? filesize($filePath) : 0;
                                        $fileSizeMB = $fileSize > 0 ? round($fileSize / 1048576, 2) . " MB" : "Unknown size";

                                        // Remove extension from display name
                                        $fileNameOnly = pathinfo($file, PATHINFO_FILENAME);
                                        ?>
                                        <div class="cardFile my-3 w-lg-25 d-flex align-items-start"
                                            style="width:400px; max-width:100%; min-width:310px;">
                                            <i class="px-4 py-3 fa-solid fa-file"></i>
                                            <div class="ms-2">
                                                <div class="text-sbold text-16 mt-1"><?php echo $fileNameOnly ?></div>
                                                <div class="due text-reg text-14 mb-1"><?php echo $fileExt ?> ·
                                                    <?php echo $fileSizeMB ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>


                                    <?php foreach ($linksArray as $link): ?>
                                        <div class="cardFile my-3 w-lg-25 d-flex align-items-start"
                                            style="width:400px; max-width:100%; min-width:310px;">
                                            <i class="px-4 py-3 fa-solid fa-link" style="font-size: 13px;"></i>
                                            <div class="ms-2">
                                                <!-- temoparary lang ang filename here -->
                                                <div class="text-sbold text-16 mt-1"><?php echo $fileNameOnly ?></div>
                                                <div class="text-reg link text-12 mt-0">
                                                    <a href="<?php echo $link ?>" target="_blank" rel="noopener noreferrer"
                                                        style="text-decoration: none; color: var(--black);">
                                                        <?php echo $link ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>

                                    <hr>

                                    <div class="text-sbold text-14 mt-4">Rubric</div>
                                    <div class="cardFile my-3 w-lg-25 d-flex align-items-start"
                                        style="width:400px; max-width:100%; min-width:310px; cursor:pointer;"
                                        data-bs-toggle="modal" data-bs-target="#rubricModal">

                                        <span class="material-symbols-outlined ps-3 pe-2 py-3"
                                            style="font-variation-settings:'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 48;">
                                            rate_review
                                        </span>

                                        <div class="ms-2">
                                            <div class="text-sbold text-16 mt-1">Essay Rubric</div>
                                            <div class="due text-reg text-14 mb-1">20 points</div>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="text-sbold text-14 pb-3">Prepared by</div>
                                    <div class="d-flex align-items-center pb-5">
                                        <div class="rounded-circle me-2"
                                            style="width: 50px; height: 50px; background-color: var(--highlight75);">
                                            <img src="shared/assets/pfp-uploads/<?php echo $profProfile ?>"
                                                alt="professor" class="rounded-circle" style="width:50px;height:50px;">
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
                                        <div
                                            class="cardFile text-sbold text-16 my-3 d-flex align-items-center justify-content-between">
                                            <!-- Left: File Icon and Name -->
                                            <div class="d-flex align-items-center">
                                                <span class="material-symbols-outlined p-2 pe-2"
                                                    style="font-variation-settings:'FILL' 1;">draft</span>
                                                <span class="ms-2">Submission</span>
                                            </div>

                                            <!-- Right: Close Button -->
                                            <button type="button" class="border-0 bg-transparent mt-2"
                                                aria-label="Close" onclick="removeFileCard(this)">
                                                <span class="material-symbols-outlined">close</span>
                                            </button>
                                        </div>

                                        <div class="text-sbold text-16 mt-3">Status</div>
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

                                        <div class="mt-0 mb-4 d-flex flex-column align-items-center">
                                            <!-- Hidden File Input -->
                                            <input type="file" name="materials[]" class="d-none" id="fileUpload"
                                                multiple>

                                            <!-- Top Buttons: File & Link -->
                                            <div class="d-flex gap-2 mb-3">
                                                <button type="button"
                                                    class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14"
                                                    style="border: 1px solid var(--black);"
                                                    onclick="document.getElementById('fileUpload').click();">
                                                    <div class="d-flex align-items-center gap-1">
                                                        <span class="material-symbols-outlined"
                                                            style="font-size:20px">upload</span>
                                                        <span>File</span>
                                                    </div>
                                                </button>

                                                <button type="button"
                                                    class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14"
                                                    style="border: 1px solid var(--black);">
                                                    <div class="d-flex align-items-center gap-1">
                                                        <span class="material-symbols-rounded"
                                                            style="font-size:20px">link</span>
                                                        <span>Link</span>
                                                    </div>
                                                </button>
                                            </div>

                                            <!-- Full-width Turn In Button -->
                                            <button type="button"
                                                class="btn px-4 py-2 text-reg text-md-14 rounded-4 w-75"
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

    <!-- Modal -->
    <div class="modal fade" id="rubricModal" tabindex="-1" aria-labelledby="rubricModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered py-4">
            <div class="modal-content" style="max-height:450px; overflow:hidden;">

                <!-- HEADER -->
                <div class="modal-header border-bottom">
                    <h5 class="modal-title text-sbold text-20 ms-3" id="rubricModalLabel">Essay Rubric</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- BODY -->
                <div class="modal-body" style="overflow-y:auto; scrollbar-width:thin;">
                    <div class="container text-center px-5">

                        <!-- Section Title -->
                        <div class="row mb-3">
                            <div class="col">
                                <div class="text-sbold text-15" style="color: var(--black);">
                                    Content Relevance
                                </div>
                            </div>
                        </div>
                        <!-- Accordion -->
                        <div id="ratingAccordion" class="row justify-content-center">
                            <div class="col-12 col-md-10">
                                <!-- Excellent -->
                                <div class="mb-2">
                                    <button
                                        class="btn w-100 d-flex align-items-center justify-content-center flex-column text-med text-14"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#excellent"
                                        aria-expanded="false" aria-controls="excellent"
                                        style="background-color: var(--pureWhite); border-radius: 10px; border: 1px solid var(--black);">

                                        <div class="d-flex justify-content-between align-items-center w-100 px-3">
                                            <span class="flex-grow-1 text-center ps-3">Excellent · 5 pts</span>
                                            <span class="material-symbols-rounded transition">expand_more</span>
                                        </div>

                                        <div class="collapse w-100 mt-2" id="excellent"
                                            data-bs-parent="#ratingAccordion">
                                            <p class="mb-0 px-3 pb-2 text-reg text-14">
                                                Ideas are insightful, well-developed, and directly address the topic.
                                            </p>
                                        </div>
                                    </button>
                                </div>

                                <!-- Good -->
                                <div class="mb-2">
                                    <button
                                        class="btn w-100 d-flex align-items-center justify-content-center flex-column text-med text-14"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#good"
                                        aria-expanded="false" aria-controls="good"
                                        style="background-color: var(--pureWhite); border-radius: 10px; border: 1px solid var(--black);">

                                        <div class="d-flex justify-content-between align-items-center w-100 px-3">
                                            <span class="flex-grow-1 text-center ps-3">Good · 4 pts</span>
                                            <span class="material-symbols-rounded transition">expand_more</span>
                                        </div>

                                        <div class="collapse w-100 mt-2" id="good" data-bs-parent="#ratingAccordion">
                                            <p class="mb-0 px-3 pb-2 text-reg text-14">
                                                Ideas are clear and relevant but may need further development.
                                            </p>
                                        </div>
                                    </button>
                                </div>

                                <!-- Fair -->
                                <div class="mb-2">
                                    <button
                                        class="btn w-100 d-flex align-items-center justify-content-center flex-column text-med text-14"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#fair"
                                        aria-expanded="false" aria-controls="fair"
                                        style="background-color: var(--pureWhite); border-radius: 10px; border: 1px solid var(--black);">

                                        <div class="d-flex justify-content-between align-items-center w-100 px-3">
                                            <span class="flex-grow-1 text-center ps-3">Fair · 3 pts</span>
                                            <span class="material-symbols-rounded transition">expand_more</span>
                                        </div>

                                        <div class="collapse w-100 mt-2" id="fair" data-bs-parent="#ratingAccordion">
                                            <p class="mb-0 px-3 pb-2 text-reg text-14">
                                                Ideas are limited or partially address the topic.
                                            </p>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Section Title -->
                        <div class="row mb-3">
                            <div class="col">
                                <div class="text-sbold text-15" style="color: var(--black);">
                                    Content Relevance
                                </div>
                            </div>
                        </div>
                        <!-- Accordion -->
                        <div id="ratingAccordion2" class="row justify-content-center">
                            <div class="col-12 col-md-10">
                                <!-- Excellent -->
                                <div class="mb-2">
                                    <button
                                        class="btn w-100 d-flex align-items-center justify-content-center flex-column text-med text-14"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#excellent2"
                                        aria-expanded="false" aria-controls="excellent2"
                                        style="background-color: var(--pureWhite); border-radius: 10px; border: 1px solid var(--black);">

                                        <div class="d-flex justify-content-between align-items-center w-100 px-3">
                                            <span class="flex-grow-1 text-center ps-3">Excellent · 5 pts</span>
                                            <span class="material-symbols-rounded transition">expand_more</span>
                                        </div>

                                        <div class="collapse w-100 mt-2" id="excellent2"
                                            data-bs-parent="#ratingAccordion2">
                                            <p class="mb-0 px-3 pb-2 text-reg text-14">
                                                Ideas are insightful, well-developed, and directly address the topic.
                                            </p>
                                        </div>
                                    </button>
                                </div>

                                <!-- Good -->
                                <div class="mb-2">
                                    <button
                                        class="btn w-100 d-flex align-items-center justify-content-center flex-column text-med text-14"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#good2"
                                        aria-expanded="false" aria-controls="good2"
                                        style="background-color: var(--pureWhite); border-radius: 10px; border: 1px solid var(--black);">

                                        <div class="d-flex justify-content-between align-items-center w-100 px-3">
                                            <span class="flex-grow-1 text-center ps-3">Good · 4 pts</span>
                                            <span class="material-symbols-rounded transition">expand_more</span>
                                        </div>

                                        <div class="collapse w-100 mt-2" id="good2" data-bs-parent="#ratingAccordion2">
                                            <p class="mb-0 px-3 pb-2 text-reg text-14">
                                                Ideas are clear and relevant but may need further development.
                                            </p>
                                        </div>
                                    </button>
                                </div>

                                <!-- Fair -->
                                <div class="mb-2">
                                    <button
                                        class="btn w-100 d-flex align-items-center justify-content-center flex-column text-med text-14"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#fair2"
                                        aria-expanded="false" aria-controls="fair2"
                                        style="background-color: var(--pureWhite); border-radius: 10px; border: 1px solid var(--black);">

                                        <div class="d-flex justify-content-between align-items-center w-100 px-3">
                                            <span class="flex-grow-1 text-center ps-3">Fair · 3 pts</span>
                                            <span class="material-symbols-rounded transition">expand_more</span>
                                        </div>

                                        <div class="collapse w-100 mt-2" id="fair2" data-bs-parent="#ratingAccordion2">
                                            <p class="mb-0 px-3 pb-2 text-reg text-14">
                                                Ideas are limited or partially address the topic.
                                            </p>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer">
                    <div class="container">
                        <div class="row justify-content-end py-2">

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const buttons = document.querySelectorAll('[data-bs-toggle="collapse"]');

            buttons.forEach(button => {
                const target = button.getAttribute('data-bs-target');
                const icon = button.querySelector('.material-symbols-rounded');
                const collapse = document.querySelector(target);

                if (collapse && icon) {
                    collapse.addEventListener('show.bs.collapse', () => {
                        // Reset all others
                        buttons.forEach(btn => btn.style.backgroundColor = 'var(--pureWhite)');
                        document.querySelectorAll('.material-symbols-rounded').forEach(ic => ic.style.transform = 'rotate(0deg)');

                        // Highlight this one
                        icon.style.transform = 'rotate(180deg)';
                        icon.style.transition = 'transform 0.3s';
                        button.style.backgroundColor = 'var(--primaryColor)';
                    });

                    collapse.addEventListener('hide.bs.collapse', () => {
                        icon.style.transform = 'rotate(0deg)';
                        button.style.backgroundColor = 'var(--pureWhite)';
                    });
                }
            });
        });
        function removeFileCard(button) {
            const card = button.closest('.cardFile'); // get the parent card
            if (card) {
                card.remove(); // remove it from the DOM
            }
        }
    </script>
</body>

</html>