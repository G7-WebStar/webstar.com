<?php
$activePage = 'task-info';

include('../shared/assets/database/connect.php');
include("../shared/assets/processes/prof-session-process.php");

$assignmentID = intval($_GET['assignmentID']);

$userQuery = "SELECT * FROM users 
              LEFT JOIN userinfo ON users.userID = userinfo.userID 
              WHERE users.userID = $userID";
$userResult = executeQuery($userQuery);

$assignmentQuery = "SELECT
                    assessments.assessmentID, 
                    assessments.assessmentTitle,
                    assessments.deadline,
                    assignments.assignmentDescription,
                    assessments.createdAt AS assessmentCreatedAt,
                    assignments.assignmentPoints,
                    assignments.rubricID,
                    userinfo.firstName,
                    userinfo.lastName,
                    userinfo.profilePicture
                FROM courses 
                INNER JOIN assessments ON courses.courseID = assessments.courseID 
                INNER JOIN assignments ON assessments.assessmentID = assignments.assessmentID
                INNER JOIN userinfo ON courses.userID = userinfo.userID 
                WHERE assignments.assignmentID = $assignmentID";

$assignmentResult = executeQuery($assignmentQuery);

$assignmentRow = mysqli_fetch_assoc($assignmentResult);

$assessmentID = $assignmentRow['assessmentID'];
$assignmentTitle = $assignmentRow['assessmentTitle'];
$assignmentDescription = $assignmentRow['assignmentDescription'];
$profName = $assignmentRow['firstName'] . ' ' . $assignmentRow['lastName'];
$profilePic = !empty($assignmentRow['profilePicture'])
    ? '../shared/assets/pfp-uploads/' . $assignmentRow['profilePicture']
    : '../shared/assets/pfp-uploads/defaultProfile.png';
$deadline = $assignmentRow['deadline'];
$score = $assignmentRow['score'] ?? null;
$totalPoints = $assignmentRow['assignmentPoints'] ?? 0;
$displayTime = $assignmentRow['assessmentCreatedAt'];
$formattedTime = !empty($displayTime) ? date("F j, Y g:i A", strtotime($displayTime)) : "";

$filesQuery = "SELECT * FROM files WHERE assignmentID = '$assignmentID'";
$filesResult = executeQuery($filesQuery);

$fileLinks = [];
$linksArray = [];

while ($file = mysqli_fetch_assoc($filesResult)) {
    if (!empty($file['fileAttachment'])) {
        // fileAttachment has the actual uploaded file(s)
        $attachments = array_map('trim', explode(',', $file['fileAttachment']));

        // fileTitle stores the display name(s) for the files
        $titles = !empty($file['fileTitle']) ? array_map('trim', explode(',', $file['fileTitle'])) : [];

        foreach ($attachments as $index => $att) {
            $filePath = "../shared/assets/files/" . basename($att);

            // Determine display name: use fileTitle if exists, otherwise fallback to filename
            $displayName = $titles[$index] ?? basename($att);

            // Calculate size in MB
            $fileSize = file_exists($filePath) ? filesize($filePath) : 0;
            $fileSizeMB = $fileSize > 0 ? round($fileSize / 1048576, 2) . " MB" : "Unknown size";

            $fileLinks[] = [
                'name' => $displayName,   // display name from DB
                'path' => $filePath,      // actual file path
                'ext' => strtolower(pathinfo($filePath, PATHINFO_EXTENSION)),
                'size' => $fileSizeMB
            ];
        }
    }

    if (!empty($file['fileLink'])) {
        $links = array_map('trim', explode(',', $file['fileLink']));
        foreach ($links as $l) {
            $linksArray[] = [
                'title' => $file['fileTitle'] ?? $l,
                'url' => $l
            ];
        }
    }
}

// Rubric Info
$rubricID = isset($assignmentRow['rubricID']) ? $assignmentRow['rubricID'] : null;
$rubricTitle = '';
$rubricPoints = 0;
$criteriaList = [];
$levelsByCriterion = [];

if (!empty($rubricID)) {
    // Get rubric title and total points
    $rubricQuery = "SELECT rubricTitle, totalPoints FROM rubric WHERE rubricID = $rubricID LIMIT 1";
    $rubricResult = executeQuery($rubricQuery);
    $rubricRow = mysqli_fetch_assoc($rubricResult);

    if ($rubricRow) {
        $rubricTitle = $rubricRow['rubricTitle'];
        $rubricPoints = $rubricRow['totalPoints'];
    }

    // Get criteria for this rubric
    $criteriaQuery = "SELECT criterionID, criteriaTitle FROM criteria WHERE rubricID = $rubricID ORDER BY criterionID ASC";
    $criteriaResult = executeQuery($criteriaQuery);
    while ($criterion = mysqli_fetch_assoc($criteriaResult)) {
        $criteriaList[] = $criterion;

        // Get levels for this criterion
        $criterionID = $criterion['criterionID'];
        $levelsQuery = "SELECT levelID, levelTitle, levelDescription, points 
                        FROM level 
                        WHERE criterionID = $criterionID 
                        ORDER BY points DESC";
        $levelsResult = executeQuery($levelsQuery);
        $levelsByCriterion[$criterionID] = [];
        while ($level = mysqli_fetch_assoc($levelsResult)) {
            $levelsByCriterion[$criterionID][] = $level;
        }
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Task Info</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/assignment.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../shared/assets/img/webstar-icon.png">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0"
        rel="stylesheet" />

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
                            <div class="row desktop-header d-none d-md-flex">
                                <div class="col-auto me-2">
                                    <button onclick="history.back()" class="btn p-0"
                                        style="background:none; border:none;">
                                        <span class="material-symbols-outlined"
                                            style="color: var(--black); font-size: 22px;">
                                            arrow_back
                                        </span>
                                    </button>
                                </div>
                                <div class="col">
                                    <span class="text-sbold text-25"><?php echo $assignmentTitle ?></span>
                                    <div class="text-reg text-18">Due
                                        <?php echo date("M d, Y", strtotime($deadline)); ?>
                                    </div>
                                </div>
                                <div class="col-auto d-flex align-items-center">
                                    <a href="assess-task-details.php?assessmentID=<?php echo $assessmentID; ?>"
                                        style="text-decoration: none;">
                                        <button
                                            class="btn d-flex align-items-center justify-content-center text-med text-14"
                                            style="background-color: var(--primaryColor); border: 1px solid #000; border-radius: 50px; width: 120px; padding: 2px 5px; gap:5px; color: black;"
                                            onmouseover="this.style.color='black';"
                                            onmouseout="this.style.color='black';">
                                            <span class="material-symbols-outlined">assignment</span>
                                            Assess
                                        </button>
                                    </a>
                                </div>
                            </div>

                            <!-- MOBILE VIEW -->
                            <div class="d-flex d-md-none mobile-assignment">
                                <div class="mobile-top">
                                    <div class="arrow">
                                        <button onclick="history.back()" class="btn p-0"
                                            style="background:none; border:none;">
                                            <span class="material-symbols-outlined"
                                                style="color: var(--black); font-size: 22px;">
                                                arrow_back
                                            </span>
                                        </button>
                                    </div>
                                    <div class="col">
                                        <span class="text-sbold text-25"><?php echo $assignmentTitle ?></span>
                                        <div class="text-reg text-18">Due
                                            <?php echo date("M d, Y", strtotime($deadline)); ?>
                                        </div>
                                    </div>
                                    <div class="col-auto d-flex align-items-center">
                                        <a href="assess-task-details.php?assessmentID=<?php echo $assessmentID; ?>"
                                            style="text-decoration: none;">
                                            <button
                                                class="btn d-flex align-items-center justify-content-center text-reg text-14"
                                                style="background-color: var(--primaryColor); border: 1px solid #000; border-radius: 50px; width: 120px; padding: 2px 5px; gap:5px; color: black;"
                                                onmouseover="this.style.color='black';"
                                                onmouseout="this.style.color='black';">
                                                <span class="material-symbols-outlined">assignment</span>
                                                Assess
                                            </button>
                                        </a>
                                    </div>
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
                                            <!-- Task Details Tab - Active -->
                                            <div class="tab-pane fade show active" id="announcements" role="tabpanel"
                                                aria-labelledby="announcements-tab">
                                                <?php if (!empty($assignmentDescription)): ?>
                                                    <div class="text-sbold text-14 mt-3">Task Instructions</div>
                                                    <p class="mb-1 mt-3 text-med text-14">
                                                        <?php echo nl2br($assignmentDescription) ?>
                                                    </p>
                                                    <hr>
                                                <?php endif; ?>

                                                <?php if (!empty($fileLinks) || !empty($linksArray)): ?>
                                                    <div class="text-sbold text-14 mt-4">Task Materials</div>
                                                    <!-- FILES -->
                                                    <?php foreach ($fileLinks as $f):
                                                        $fileNameOnly = pathinfo($f['name'], PATHINFO_FILENAME);
                                                        ?>
                                                        <div onclick="openViewerModal('<?php echo $f['name']; ?>', '<?php echo $f['path']; ?>')"
                                                            style="cursor:pointer;">
                                                            <div class="cardFile my-3 w-lg-25 d-flex align-items-start"
                                                                style="width:400px; max-width:100%; min-width:310px;">
                                                                <span class="px-3 py-3 material-symbols-outlined">draft</span>
                                                                <div class="ms-2 flex-grow-1">
                                                                    <!-- Truncated title -->
                                                                    <div class="text-sbold text-16 mt-1 text-truncate"
                                                                        style="max-width:320px;">
                                                                        <?php echo $fileNameOnly; ?>
                                                                    </div>
                                                                    <div class="due text-reg text-14 mb-1">
                                                                        <?php echo strtoupper($f['ext']); ?> ·
                                                                        <?php echo $f['size']; ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                    <!-- LINKS -->
                                                    <?php foreach ($linksArray as $linkItem): ?>
                                                        <div onclick="openLinkViewerModal('<?php echo htmlspecialchars($linkItem['title']); ?>', '<?php echo htmlspecialchars($linkItem['url']); ?>')"
                                                            style="cursor:pointer;">
                                                            <div class="cardFile my-3 w-lg-25 d-flex align-items-start"
                                                                style="width:400px; max-width:100%; min-width:310px;">
                                                                <span class="px-3 py-3 material-symbols-outlined">public</span>
                                                                <div class="ms-2 flex-grow-1">
                                                                    <!-- Truncated link title -->
                                                                    <div class="text-sbold text-16 mt-1 text-truncate"
                                                                        style="max-width:320px;">
                                                                        <?php echo htmlspecialchars($linkItem['title']); ?>
                                                                    </div>
                                                                    <div class="text-reg text-12 mt-0 text-truncate"
                                                                        style="color: var(--black); max-width:320px;">
                                                                        <?php echo htmlspecialchars($linkItem['url']); ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                    <hr>
                                                <?php endif; ?>

                                                <?php if (!empty($rubricID) && !empty($rubricTitle)): ?>
                                                    <div class="text-sbold text-14 mt-4">Rubric</div>
                                                    <div class="cardFile my-3 w-lg-25 d-flex align-items-start"
                                                        style="max-width:100%; min-width:310px; cursor:pointer;"
                                                        data-bs-toggle="modal" data-bs-target="#rubricModal">

                                                        <span class="material-symbols-outlined ps-3 pe-2 py-3"
                                                            style="font-variation-settings:'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 48;">
                                                            rate_review
                                                        </span>

                                                        <div class="ms-2">
                                                            <div class="text-sbold text-16 mt-1 text-truncate" style="max-width:320px;"><?php echo $rubricTitle; ?>
                                                            </div>
                                                            <div class="due text-reg text-14 mb-1">
                                                                <?php echo $rubricPoints; ?> points
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                <?php endif; ?>

                                                <div class="text-sbold text-14 pb-3">Prepared by</div>
                                                <div class="d-flex align-items-center pb-5">
                                                    <div class="rounded-circle me-2"
                                                        style="width: 50px; height: 50px; background-color: var(--highlight75);">
                                                        <img src="<?php echo $profilePic ?>" alt="Profile"
                                                            alt="professor" class="rounded-circle"
                                                            style="width:50px;height:50px;">
                                                    </div>
                                                    <div>
                                                        <div class="text-sbold text-14"><?php echo $profName ?></div>
                                                        <div class="text-med text-12"><?php echo $formattedTime; ?>
                                                        </div>
                                                    </div>
                                                </div>
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

    <!-- FILE VIEWER MODAL -->
    <div class="modal fade" id="viewerModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content" style="border-radius:12px; overflow:hidden;">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="modal-title text-sbold text-16 mb-0 text-truncate" style="max-width:150px;"
                            id="viewerModalLabel">File Viewer</h5>
                        <a id="modalDownloadBtn" class="btn py-1 px-3 rounded-pill text-sbold text-md-14 ms-1"
                            style="background-color: var(--primaryColor); border: 1px solid var(--black);" download>
                            <span class="" style="display:flex;align-items:center;gap:4px;">
                                <span class="material-symbols-outlined" style="font-size:18px;">download_2</span>
                                Download
                            </span>
                        </a>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0" style="background:#2e2e2e; height:75vh;">
                    <div id="viewerContainer" style="width:100%; height:100%;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- LINK VIEWER MODAL -->
    <div class="modal fade" id="linkViewerModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content" style="border-radius:12px; overflow:hidden;">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="modal-title text-sbold text-16 mb-0 text-truncate" style="max-width:150px;"
                            id="linkViewerModalLabel">Link Viewer</h5>
                        <a id="modalOpenInNewTab" class="btn py-1 px-3 rounded-pill text-sbold text-md-14 ms-1"
                            style="background-color: var(--primaryColor); border: 1px solid var(--black);"
                            target="_blank">
                            <span class="" style="display:flex;align-items:center;gap:4px;">
                                <span class="material-symbols-outlined" style="font-size:18px;">open_in_new</span>
                                Open
                            </span>
                        </a>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0" style="background:#2e2e2e; height:75vh;">
                    <iframe id="linkViewerIframe"
                        style="width:100%; height:100%; border:none; border-radius:10px;"></iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- Rubric Modal -->
    <div class="modal fade" id="rubricModal" tabindex="-1" aria-labelledby="rubricModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered py-4">
            <div class="modal-content rounded-4" style="max-height:450px; overflow:hidden;">

                <!-- HEADER -->
                <div class="modal-header border-bottom">
                    <div class="modal-title text-sbold text-20 ms-3" id="rubricModalLabel">
                        <?php echo $rubricTitle; ?>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- BODY -->
                <div class="modal-body" style="overflow-y:auto; scrollbar-width:thin;">
                    <div class="container text-center px-5">
                        <?php foreach ($criteriaList as $criterionIndex => $criterion): ?>
                            <!-- Section Title -->
                            <div class="row mb-3">
                                <div class="col">
                                    <div class="text-sbold text-15" style="color: var(--black);">
                                        <?php echo $criterion['criteriaTitle']; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Accordion for levels -->
                            <div id="ratingAccordion<?php echo $criterionIndex; ?>" class="row justify-content-center">
                                <div class="col-12 col-md-10">
                                    <?php foreach ($levelsByCriterion[$criterion['criterionID']] as $levelIndex => $level):
                                        $collapseID = strtolower(preg_replace('/\s+/', '', $level['levelTitle'])) . $criterionIndex;
                                        ?>
                                        <div class="mb-2">
                                            <div class="w-100 d-flex align-items-center justify-content-center flex-column text-med text-14"
                                                style="background-color: var(--pureWhite); border-radius: 10px; border: 1px solid var(--black);">

                                                <div class="d-flex justify-content-between align-items-center w-100 px-3 py-2">
                                                    <span class="flex-grow-1 text-center ps-3">
                                                        <?php echo $level['levelTitle']; ?> · <?php echo $level['points']; ?>
                                                        pts
                                                    </span>

                                                    <!-- only the icon is clickable -->
                                                    <span class="material-symbols-rounded collapse-toggle"
                                                        data-bs-toggle="collapse" data-bs-target="#<?php echo $collapseID; ?>"
                                                        aria-expanded="false" aria-controls="<?php echo $collapseID; ?>"
                                                        style="cursor:pointer;">
                                                        expand_more
                                                    </span>
                                                </div>

                                                <div class="collapse w-100 mt-2" id="<?php echo $collapseID; ?>"
                                                    data-bs-parent="#ratingAccordion<?php echo $criterionIndex; ?>">
                                                    <p class="mb-0 px-3 pb-2 text-reg text-14">
                                                        <?php echo $level['levelDescription']; ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
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
        const icons = document.querySelectorAll('.collapse-toggle');

        icons.forEach(icon => {
            const target = icon.getAttribute('data-bs-target');
            const collapse = document.querySelector(target);
            const container = icon.closest('div.d-flex.justify-content-between'); // parent row

            if (collapse && container) {
                collapse.addEventListener('show.bs.collapse', () => {
                    document.querySelectorAll('.material-symbols-rounded')
                        .forEach(ic => ic.style.transform = 'rotate(0deg)');
                    icon.style.transform = 'rotate(180deg)';
                    icon.style.transition = 'transform 0.3s';
                    container.parentElement.style.backgroundColor = 'var(--primaryColor)';
                });

                collapse.addEventListener('hide.bs.collapse', () => {
                    icon.style.transform = 'rotate(0deg)';
                    container.parentElement.style.backgroundColor = 'var(--pureWhite)';
                });
            }
        });

        function openViewerModal(fileName, filePath) {
            document.getElementById("viewerModalLabel").textContent = fileName;
            document.getElementById("modalDownloadBtn").href = filePath;
            let viewer = document.getElementById("viewerContainer");
            viewer.innerHTML = "";
            let ext = fileName.split(".").pop().toLowerCase();
            if (["jpg", "jpeg", "png", "gif", "webp", "bmp", "svg"].includes(ext)) {
                viewer.innerHTML = `<img src="${filePath}" style="width:100%; height:100%; object-fit:contain; background:#333;">`;
            } else if (ext === "pdf") {
                viewer.innerHTML = `<iframe src="${filePath}" width="100%" height="100%" style="border:none; border-radius:10px;"></iframe>`;
            } else {
                viewer.innerHTML = `<div class="text-white text-center mt-5">
                    <p class="text-sbold text-16" style="color: var(--pureWhite);">This file type cannot be previewed.</p>
                    <a href="${filePath}" download class="btn text-sbold text-16" style="background-color: var(--primaryColor); color: var(--black); border: none;"> Download File </a>
                </div>`;
            }
            new bootstrap.Modal(document.getElementById("viewerModal")).show();
        }

        function openLinkViewerModal(title, url) {
            document.getElementById("linkViewerModalLabel").textContent = title;
            document.getElementById("modalOpenInNewTab").href = url;
            document.getElementById("linkViewerIframe").src = url;
            new bootstrap.Modal(document.getElementById("linkViewerModal")).show();
        }
    </script>

</body>

</html>