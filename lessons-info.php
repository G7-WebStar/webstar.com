<?php
$activePage = 'lessons-info';
include('shared/assets/database/connect.php');
include("shared/assets/processes/session-process.php");
if (!isset($_GET['lessonID'])) {
    echo "Lesson ID is missing in the URL.";
    exit;
}

$lessonID = intval($_GET['lessonID']);

$lessonInfoQuery = "
    SELECT * FROM users
    LEFT JOIN userInfo ON users.userID = userInfo.userID
    LEFT JOIN courses ON users.userID = courses.userID
    LEFT JOIN lessons ON courses.courseID = lessons.courseID
    WHERE lessons.lessonID = $lessonID
";

$lessonInfoResult = executeQuery($lessonInfoQuery);

if (!$lesson = mysqli_fetch_assoc($lessonInfoResult)) {
    echo "Lesson not found.";
    exit;
}

$courseID = $lesson['courseID'];
$lessonTitle = $lesson['lessonTitle'];
$lessonDescription = $lesson['lessonDescription'];
$profName = $lesson['firstName'] . " " . $lesson['lastName'];
$profPic = !empty($lesson['profilePicture']) ? $lesson['profilePicture'] : "shared/assets/img/courseInfo/prof.png";

$displayTime = !empty($lesson['updatedAt']) ? $lesson['updatedAt'] : $lesson['createdAt'];
$formattedTime = !empty($displayTime) ? date("F j, Y g:i A", strtotime($displayTime)) : "";

$filesQuery = "SELECT * FROM files WHERE lessonID = '$lessonID'";
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

// Update counts
$fileCount = count($attachmentsArray);
$linkCount = count($linksArray);
?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Lessons-info</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/lessons-info.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="shared/assets/img/webstar-icon.png">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0" rel="stylesheet" />

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
                                        <a href="course-info.php?courseID=<?= $courseID ?>" class="text-decoration-none">
                                            <i class="fa-solid fa-arrow-left text-reg text-16"
                                                style="color: var(--black);"></i>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <span class="text-sbold text-25"><?php echo $lessonTitle; ?></span>
                                        <div class="text-reg text-18"><?php echo $fileCount ?> <?php echo $fileCount == 1 ? "file" : "files" ?> · <?php echo $linkCount ?> <?php echo $linkCount == 1 ? "link" : "links" ?></div>
                                    </div>
                                </div>

                                <!-- MOBILE VIEW -->
                                <div class="d-block d-sm-none mobile-assignment">
                                    <div class="mobile-top">
                                        <div class="arrow">
                                            <a href="course-info.php?courseID=<?php echo $courseID ?>" class="text-decoration-none">
                                                <i class="fa-solid fa-arrow-left text-reg text-16" style="color: var(--black);"></i>
                                            </a>
                                        </div>
                                        <div class="title text-sbold text-25"><?php echo $lessonTitle; ?></div>
                                    </div>
                                    <div class="due text-reg text-18"><?php echo $fileCount ?> <?php echo $fileCount == 1 ? "file" : "files" ?> · <?php echo $linkCount ?> <?php echo $linkCount == 1 ? "link" : "links" ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Left Content -->
                            <div class="col-12 col-lg-8">
                                <div class="p-0 px-lg-5">
                                    <div class="text-sbold text-14 mt-3">Lesson Objectives</div>
                                    <p class="mt-3 text-med text-14"><?php echo nl2br($lessonDescription) ?></p>
                                    <hr>

                                    <div class="text-sbold text-14 mt-4">Learning Materials</div>
                                    <!-- Temporary filename -->
                                    <?php foreach ($attachmentsArray as $file):
                                        $filePath = "shared/uploads/" . $file;
                                        $fileExt = strtoupper(pathinfo($file, PATHINFO_EXTENSION));
                                        $fileSize = (file_exists($filePath)) ? filesize($filePath) : 0;
                                        $fileSizeMB = $fileSize > 0 ? round($fileSize / 1048576, 2) . " MB" : "Unknown size";

                                        // Remove extension from display name
                                        $fileNameOnly = pathinfo($file, PATHINFO_FILENAME);
                                    ?>
                                        <div class="cardFile my-3 w-lg-25 d-flex align-items-start" style="width:400px; max-width:100%; min-width:310px;">
                                            <span class="px-3 py-3 material-symbols-outlined">
                                                draft
                                            </span>
                                            <div class="ms-2">
                                                <div class="text-sbold text-16 mt-1"><?php echo $fileNameOnly ?></div>
                                                <div class="due text-reg text-14 mb-1"><?php echo $fileExt ?> · <?php echo $fileSizeMB ?></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>


                                    <?php foreach ($linksArray as $link): ?>
                                        <div class="cardFile my-3 w-lg-25 d-flex align-items-start" style="width:400px; max-width:100%; min-width:310px;">
                                            <span class="px-3 py-3 material-symbols-outlined">
                                                public
                                            </span>
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
                                        <div class="rounded-circle me-2" style="width: 50px; height: 50px; background-color: var(--highlight75);">
                                            <img src="<?php echo !empty($lesson['profilePicture']) ? 'shared/assets/pfp-uploads/' . $lesson['profilePicture'] : 'shared/assets/img/default-profile.png'; ?>"
                                                alt="Prof Picture" class="rounded-circle" style="width:50px;height:50px;">
                                        </div>
                                        <div>
                                            <div class="text-sbold text-14"><?php echo $profName; ?></div>
                                            <div class="text-med text-12"><?php echo $formattedTime; ?></div>
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