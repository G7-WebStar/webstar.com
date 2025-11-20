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

// $filesQuery = "SELECT * FROM files WHERE lessonID = '$lessonID'";
// $filesResult = executeQuery($filesQuery);

$fileLinks = [];   // for viewer modal
$linksArray = [];  // for link list display

while ($file = mysqli_fetch_assoc($filesResult)) {

    // ---------- FILE ATTACHMENTS ----------
    if (!empty($file['fileAttachment'])) {

        $attachments = array_map('trim', explode(',', $file['fileAttachment']));

        foreach ($attachments as $att) {

            $fileName = basename($att);
            $filePath = "shared/assets/files/" . $fileName;

            $fileLinks[] = [
                'name' => $fileName,
                'path' => $filePath,
                'ext' => strtolower(pathinfo($fileName, PATHINFO_EXTENSION)),
                'title' => $file['fileTitle']
            ];
        }
    }

    // ---------- LINK ATTACHMENTS ----------
    if (!empty($file['fileLink'])) {
        $links = array_map('trim', explode(',', $file['fileLink']));

        foreach ($links as $l) {
            $linksArray[] = [
                'title' => $file['fileTitle'],
                'url' => $l
            ];
        }
    }
}

$fileCount = count($fileLinks);
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="icon" type="image/png" href="shared/assets/img/webstar-icon.png">

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
                                        <a href="course-info.php?courseID=<?= $courseID ?>"
                                            class="text-decoration-none">
                                            <span class="material-symbols-outlined"
                                                style="color: var(--black); font-size: 22px;">
                                                arrow_back
                                            </span>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <span class="text-sbold text-25"><?php echo $lessonTitle; ?></span>
                                        <div class="text-reg text-18"><?php echo $fileCount ?>
                                            <?php echo $fileCount == 1 ? "file" : "files" ?> · <?php echo $linkCount ?>
                                            <?php echo $linkCount == 1 ? "link" : "links" ?></div>
                                    </div>
                                </div>

                                <!-- MOBILE VIEW -->
                                <div class="d-block d-sm-none mobile-assignment">
                                    <div class="mobile-top">
                                        <div class="arrow">
                                            <a href="course-info.php?courseID=<?php echo $courseID ?>"
                                                class="text-decoration-none">
                                                <span class="material-symbols-outlined"
                                                    style="color: var(--black); font-size: 22px;">
                                                    arrow_back
                                                </span>
                                            </a>
                                        </div>
                                        <div class="title text-sbold text-25"><?php echo $lessonTitle; ?></div>
                                    </div>
                                    <div class="due text-reg text-18"><?php echo $fileCount ?>
                                        <?php echo $fileCount == 1 ? "file" : "files" ?> · <?php echo $linkCount ?>
                                        <?php echo $linkCount == 1 ? "link" : "links" ?></div>
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
                                    <?php foreach ($fileLinks as $f): ?>
                                        <?php
                                        $file = $f['name'];
                                        $filePath = $f['path'];

                                        $fileExt = strtoupper(pathinfo($file, PATHINFO_EXTENSION));
                                        $fileSize = file_exists($filePath) ? filesize($filePath) : 0;
                                        $fileSizeMB = $fileSize > 0 ? round($fileSize / 1048576, 2) . " MB" : "Unknown size";

                                        $fileNameOnly = pathinfo($file, PATHINFO_FILENAME);
                                        ?>

                                        <div onclick="openViewerModal('<?php echo $file; ?>', '<?php echo $filePath; ?>')"
                                            style="cursor:pointer; text-decoration:none; color:inherit;">

                                            <div class="cardFile my-3 w-lg-25 d-flex align-items-start"
                                                style="width:400px; max-width:100%; min-width:310px;">

                                                <span class="px-3 py-3 material-symbols-outlined">draft</span>

                                                <div class="ms-2">
                                                    <div class="text-sbold text-16 mt-1 text-truncate" style="width:225px;"
                                                        title="<?php echo $fileNameOnly; ?>">
                                                        <?php echo $fileNameOnly ?>
                                                    </div>
                                                    <div class="due text-reg text-14 mb-1">
                                                        <?php echo $fileExt ?> · <?php echo $fileSizeMB ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    <?php endforeach; ?>


                                    <?php foreach ($linksArray as $linkItem): ?>
                                        <div onclick="openLinkViewerModal('<?php echo htmlspecialchars($linkItem['title']); ?>',
                                      '<?php echo htmlspecialchars($linkItem['url']); ?>')"
                                            style="cursor:pointer; text-decoration:none; color:inherit;">

                                            <div class="cardFile my-3 w-lg-25 d-flex align-items-start overflow-hidden"
                                                style="width:400px; max-width:100%; min-width:310px;">

                                                <span class="px-3 py-3 material-symbols-outlined">public</span>

                                                <div class="ms-2">
                                                    <div class="text-sbold text-16 mt-1 text-truncate" style="width: 250px;"
                                                        title="<?php echo htmlspecialchars($linkItem['title']); ?>">
                                                        <?php echo htmlspecialchars($linkItem['title']); ?>
                                                    </div>

                                                    <div class="text-reg text-12 mt-0 text-truncate" style="color: var(--black); width: 230px;"
                                                        title="<?php echo htmlspecialchars($linkItem['url']); ?>">
                                                        <?php echo htmlspecialchars($linkItem['url']); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>


                                    <hr>

                                    <div class="text-sbold text-14 pb-3">Prepared by</div>
                                    <div class="d-flex align-items-center pb-5">
                                        <div class="rounded-circle me-2"
                                            style="width: 50px; height: 50px; background-color: var(--highlight75);">
                                            <img src="<?php echo !empty($lesson['profilePicture']) ? 'shared/assets/pfp-uploads/' . $lesson['profilePicture'] : 'shared/assets/img/default-profile.png'; ?>"
                                                alt="Prof Picture" class="rounded-circle"
                                                style="width:50px;height:50px;">
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
    <!-- FILE VIEWER MODAL -->
    <div class="modal fade" id="viewerModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content" style="border-radius:12px; overflow:hidden;">

                <!-- Header -->
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="modal-title text-sbold text-16 mb-0 text-truncate" style="max-width:150px;" id="viewerModalLabel">File Viewer</h5>

                        <a id="modalDownloadBtn" class="btn py-1 px-3 rounded-pill text-sbold text-md-14 ms-1"
                            style="background-color: var(--primaryColor); border: 1px solid var(--black);" download>
                            <span class="" style="display: flex; align-items: center; gap: 4px;">
                                <span class="material-symbols-outlined" style="font-size:18px;">download_2</span>
                                Download
                            </span>
                        </a>
                    </div>

                    <!-- Right side: close button -->
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
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

                <!-- Header -->
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="modal-title text-sbold text-16 mb-0 text-truncate" style="max-width:150px;" id="linkViewerModalLabel">Link Viewer</h5>

                        <a id="modalOpenInNewTab" class="btn py-1 px-3 rounded-pill text-sbold text-md-14 ms-1"
                            style="background-color: var(--primaryColor); border: 1px solid var(--black);"
                            target="_blank">
                            <span class="" style="display: flex; align-items: center; gap: 4px;">
                                <span class="material-symbols-outlined" style="font-size:18px;">open_in_new</span>
                                Open
                            </span>
                        </a>
                    </div>

                    <!-- Right side: close button -->
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body p-0" style="background:#2e2e2e; height:75vh;">
                    <iframe id="linkViewerIframe"
                        style="width:100%; height:100%; border:none; border-radius:10px;"></iframe>
                </div>

            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toastContainer"
        class="position-absolute top-0 start-50 translate-middle-x pt-5 pt-md-1 d-flex flex-column align-items-center"
        style="z-index:1100; pointer-events:none;">
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toastContainer = document.getElementById('toastContainer');

            // Function to create a toast
            function showToast(message) {
                const toastEl = document.createElement('div');
                toastEl.className = "alert alert-success mb-2 shadow-lg text-med text-12 d-flex align-items-center justify-content-center gap-2 px-3 py-2";
                toastEl.style.cssText = "border-radius:8px; display:flex; align-items:center; gap:8px; padding:0.5rem 0.75rem; text-align:center; background-color:#d1e7dd; color:#0f5132;";
                toastEl.innerHTML = `<i class="bi bi-check-circle-fill fs-6" style="color: var(--black);"></i>
                             <span style="color: var(--black);">${message}</span>`;

                toastContainer.appendChild(toastEl);

                // Auto remove after 3s
                setTimeout(() => {
                    toastEl.remove();
                }, 3000);
            }

            // Add click listener to all download links
            document.querySelectorAll('a[download]').forEach(link => {
                link.addEventListener('click', () => {
                    showToast('File successfully downloaded!');
                });
            });
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
                viewer.innerHTML = `
            <div class="text-white text-center mt-5">
                <p class="text-sbold text-16" style="color: var(--pureWhite);">This file type cannot be previewed.</p>
                <a href="${filePath}" download class="btn text-sbold text-16" style="background-color: var(--primaryColor); color: var(--black); border: none;"> Download File </a>
            </div>`;
            }

            new bootstrap.Modal(document.getElementById("viewerModal")).show();
        }

        function openLinkViewerModal(title, url) {
            document.getElementById("linkViewerModalLabel").textContent = title;
            document.getElementById("modalOpenInNewTab").href = url;

            let iframe = document.getElementById("linkViewerIframe");
            iframe.src = url;

            new bootstrap.Modal(document.getElementById("linkViewerModal")).show();
        }
    </script>

</body>

</html>