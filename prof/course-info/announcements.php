<?php

if (isset($_POST['deleteAnnouncementBtn'])) {
    $announcementID = $_POST['deleteAnnouncementID'];

    $deleteAnnouncementQuery = "DELETE FROM announcements WHERE announcementID='$announcementID'";
    executeQuery($deleteAnnouncementQuery);

    $deleteAnnouncementNotesQuery = "DELETE FROM announcementNotes WHERE announcementID='$announcementID'";
    executeQuery($deleteAnnouncementNotesQuery);

    $deleteFilessQuery = "DELETE FROM files WHERE announcementID='$announcementID'";
    executeQuery($deleteFilessQuery);

    $_SESSION['success'] = "Announcement deleted successfully!";
}

// HANDLE POST FIRST
if (isset($_POST['announcementID'])) {
    $announcementID = $_POST['announcementID'];

    if (isset($_POST['noted'])) {
        $insertQuery = "INSERT IGNORE INTO announcementNotes (announcementID, userID, notedAt)
                        VALUES ('$announcementID', '$userID', NOW())";
        executeQuery($insertQuery);
    } else {
        $deleteQuery = "DELETE FROM announcementNotes 
                        WHERE announcementID='$announcementID' AND userID='$userID'";
        executeQuery($deleteQuery);
    }
}

// Sort
$sortBy = $_POST['sortBy'] ?? 'Newest';

switch ($sortBy) {
    case 'Oldest':
        $orderBy = "a.announcementDate ASC, a.announcementTime ASC";
        break;

    case 'Unread':
        $orderBy = "isUserNoted ASC, a.announcementDate DESC, a.announcementTime DESC";
        break;

    default: // Newest
        $orderBy = "a.announcementDate DESC, a.announcementTime DESC";
        break;
}

// FETCH ANNOUNCEMENTS
$announcementQuery = "
    SELECT 
        a.announcementID,
        a.announcementContent,
        a.announcementDate,
        a.announcementTime,
        u.profilePicture,
        u.firstName,
        u.lastName,
        COUNT(DISTINCT n.userID) AS totalNoted,
        (SELECT COUNT(e.userID) 
         FROM enrollments e 
         WHERE e.courseID = a.courseID) AS totalStudents,
        MAX(CASE WHEN n.userID = '$userID' THEN 1 ELSE 0 END) AS isUserNoted 
    FROM announcements a
    INNER JOIN userinfo u ON a.userID = u.userID
    LEFT JOIN announcementNotes n ON a.announcementID = n.announcementID 
    WHERE a.courseID = '$courseID'
    GROUP BY a.announcementID
    ORDER BY $orderBy
";

$announcementResult = executeQuery($announcementQuery);
?>
<?php if (isset($_SESSION['success'])): ?>
    <div class="position-absolute top-0 start-50 translate-middle-x pt-5 pt-md-1 d-flex flex-column align-items-center"
        style="z-index:1100; pointer-events:none;">
        <div class="alert alert-success mb-2 shadow-lg text-med text-12
                d-flex align-items-center justify-content-center gap-2 px-3 py-2" role="alert"
            style="border-radius:8px; display:flex; align-items:center; gap:8px; padding:0.5rem 0.75rem; text-align:center; background-color:#d1e7dd; color:#0f5132;">
            <i class="bi bi-check-circle-fill fs-6" style="color: var(--black);"></i>
            <span style="color: var(--black);"><?= $_SESSION['success']; ?></span>
        </div>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<div class="d-flex flex-column flex-nowrap overflow-x-hidden">

    <?php if (mysqli_num_rows($announcementResult) > 0): ?>

        <!-- Sort By Dropdown -->
        <div class="d-flex align-items-center flex-wrap mb-1" id="header">
            <div class="d-flex align-items-center flex-wrap">
                <span class="dropdown-label me-2 text-reg text-14">Sort by</span>
                <form method="POST">
                    <input type="hidden" name="activeTab" value="announcements">
                    <select class="select-modern text-reg text-14" name="sortBy" onchange="this.form.submit()">
                        <option value="Newest" <?php echo ($sortBy == 'Newest') ? 'selected' : ''; ?>>Newest</option>
                        <option value="Oldest" <?php echo ($sortBy == 'Oldest') ? 'selected' : ''; ?>>Oldest</option>
                        <option value="Unread" <?php echo ($sortBy == 'Unread') ? 'selected' : ''; ?>>Unread</option>
                    </select>
                </form>

            </div>
        </div>

        <?php
        while ($row = mysqli_fetch_assoc($announcementResult)) {
            $profilePicture = !empty($row['profilePicture'])
                ? $row['profilePicture']
                : "../shared/assets/img/courseInfo/prof.png";
            $fullName = $row['firstName'] . " " . $row['lastName'];
            $announcementDate = date("F j, Y g:iA", strtotime($row['announcementDate'] . " " . $row['announcementTime']));
            $announcementContent = $row['announcementContent'];
            $announcementID = $row['announcementID'];
            $totalNoted = $row['totalNoted'];
            $totalStudents = $row['totalStudents'];
            $isChecked = ($row['isUserNoted']) ? 'checked' : '';

            $attachmentsArray = [];
            $linksArray = [];

            $filesQuery = "SELECT * FROM files WHERE announcementID = '$announcementID'";
            $filesResult = executeQuery($filesQuery);

            while ($file = mysqli_fetch_assoc($filesResult)) {
                if (!empty($file['fileAttachment'])) {
                    $attachments = array_map('trim', explode(',', $file['fileAttachment']));
                    $attachmentsArray = array_merge($attachmentsArray, $attachments);
                }

                if (!empty($file['fileLink'])) {
                    $links = array_map('trim', explode(',', $file['fileLink']));
                    $linksArray = array_merge($linksArray, $links);
                }

                $fileTitle = !empty($file['fileTitle']) ? $file['fileTitle'] : '';
            }
        ?>

            <!-- Announcement Card -->
            <div class="announcement-card d-flex align-items-start mb-1 position-relative">

                <!-- Instructor Image -->
                <div class="flex-shrink-0 me-3">
                    <img src="../shared/assets/pfp-uploads/<?php echo $profilePicture; ?>" alt="Instructor Image"
                        style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">
                </div>

                <!-- Text Content -->
                <div class="text-start w-100 position-relative">

                    <!-- Name + Date + DROPDOWN -->
                    <div class="d-flex justify-content-between align-items-start text-reg text-12"
                        style="color: var(--black); line-height: 140%;">

                        <div>
                            <strong>Prof. <?php echo $fullName; ?></strong><br>
                            <span style="font-weight: normal;"><?php echo $announcementDate; ?></span>
                        </div>

                        <!-- DROPDOWN MENU -->
                        <div class="dropdown ms-2">
                            <button class="btn btn-light btn-sm p-1 px-2 border-0 bg-transparent" type="button"
                                id="dropdownMenuButton<?php echo $announcementID; ?>" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end"
                                aria-labelledby="dropdownMenuButton<?php echo $announcementID; ?>">
                                <li><a class="dropdown-item text-reg text-14" href="post-announcement.php?edit=<?php echo $announcementID; ?>">Edit</a></li>
                                <li>
                                    <button type="button" class="dropdown-item text-reg text-14 text-danger"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $announcementID; ?>">
                                        Delete
                                    </button>
                                </li>
                            </ul>
                        </div>

                    </div>

                    <!-- Desktop Announcement Text -->
                    <p class="d-none d-md-block mb-0 mt-3 text-reg text-14" style="color: var(--black); line-height: 140%; white-space: pre-line;">
                        <?php echo nl2br($announcementContent); ?>
                    </p>

                    <!-- Mobile Announcement Text -->
                    <p class="text-reg d-md-none mb-0 mt-3 text-reg text-12 mobile-announcement-content"
                        style="color: var(--black); line-height: 140%;">
                        <?php echo $announcementContent; ?>
                    </p>

                    <!-- View Attachments Button -->
                    <?php if (!empty($attachmentsArray) || !empty($linksArray)): ?>
                        <?php
                        $totalItems = count($attachmentsArray) + count($linksArray);
                        ?>
                        <button type="button" class="btn btn-attachments mt-3 text-med text-12" data-bs-toggle="modal"
                            data-bs-target="#attachmentsModal<?php echo $announcementID; ?>">
                            View <?php echo $totalItems; ?> Attachment<?php echo $totalItems > 1 ? 's' : ''; ?>
                        </button>
                    <?php endif; ?>

                    <!-- Checker (Desktop) -->
                    <div class="form-check d-none d-md-flex align-items-center mt-4"
                        style="gap: 20px; padding-left: 0px!important;">
                        <form method="POST">
                            <input type="hidden" name="announcementID" value="<?php echo $announcementID; ?>">
                            <div class="text-med text-12" style="color: var(--black); position: relative; top: -3px;">
                                <?php echo $totalNoted . " of " . $totalStudents . " students noted"; ?>
                            </div>
                        </form>
                    </div>

                    <!-- Checker (Mobile) -->
                    <div class="form-check d-flex d-md-none align-items-center mt-4"
                        style="gap: 6px; padding-left: 0px!important;">
                        <form method="POST">
                            <input type="hidden" name="announcementID" value="<?php echo $announcementID; ?>">
                            <div class="text-med text-12" style="color: var(--black); position: relative; top: -3px;">
                                <?php echo $totalNoted . " of " . $totalStudents . " students noted"; ?>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

            <!-- Attachments Modal -->
            <div class="modal fade" id="attachmentsModal<?php echo $announcementID; ?>" tabindex="-1"
                aria-labelledby="attachmentsModalLabel<?php echo $announcementID; ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered centered" style="max-width: 580px;">
                    <div class="modal-content d-flex flex-column shadow-sm rounded-4" style="max-height: 80vh;">

                        <!-- Header -->
                        <div class="modal-header border-bottom">
                            <div class="modal-title text-sbold text-20 ps-1"
                                id="attachmentsModalLabel<?php echo $announcementID; ?>">
                                Attachments
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <!-- Body -->
                        <div class="modal-body flex-grow-1 overflow-y-auto" style="max-height: 40vh;">

                            <!-- Files Section -->
                            <?php if (!empty($attachmentsArray)): ?>
                                <div class="text-sbold text-16 mb-2">Files</div>
                                <?php foreach ($attachmentsArray as $attachment): ?>

                                    <?php
                                    $decodedAttachment = htmlspecialchars($attachment);
                                    $fileExtension = strtolower(pathinfo($decodedAttachment, PATHINFO_EXTENSION));
                                    ?>

                                    <a href="#" class="openFileViewer text-decoration-none d-block mb-2"
                                        data-file="<?php echo $decodedAttachment; ?>" data-extension="<?php echo $fileExtension; ?>"
                                        style="color: var(--black);">

                                        <div class="cardFile d-flex align-items-start w-100 overflow-hidden" style="cursor:pointer;">
                                            <span class="px-4 py-3 material-symbols-outlined">draft</span>
                                            <div class="ms-2">
                                                <div class="text-sbold text-16 mt-1 pe-4 file-name text-truncate" style="max-width:330px;"
                                                    title="<?php echo $decodedAttachment; ?>">
                                                    <?php echo $decodedAttachment; ?>
                                                </div>
                                                <div class="due text-reg text-14 mb-1">
                                                    <?php echo strtoupper($fileExtension); ?> file
                                                </div>
                                            </div>
                                        </div>

                                    </a>


                                <?php endforeach; ?>
                            <?php endif; ?>

                            <!-- Links Section -->
                            <?php if (!empty($linksArray)): ?>
                                <div class="text-sbold text-16 <?php echo !empty($attachmentsArray) ? 'mt-4' : 'mt-0'; ?> mb-2">Links</div>
                                <?php foreach ($linksArray as $index => $link): ?>

                                    <a href="#" class="openLinkViewer text-decoration-none d-block mb-2"
                                        data-url="<?php echo htmlspecialchars($link); ?>" style="color: var(--black);">

                                        <div class="cardFile d-flex align-items-start w-100 overflow-hidden" style="cursor:pointer;">
                                            <span class="px-4 py-3 material-symbols-outlined">public</span>
                                            <div class="ms-2">

                                                <!-- FILE TITLE -->
                                                <div class="text-sbold text-16 mt-1 pe-4 text-truncate" style="max-width:330px;"
                                                    title="<?php echo htmlspecialchars($fileTitle); ?>">
                                                    <?php echo htmlspecialchars($fileTitle); ?>
                                                </div>

                                                <!-- ACTUAL LINK BELOW -->
                                                <div class="text-reg text-12 mb-1 text-truncate" style="max-width:330px;"
                                                    title="<?php echo htmlspecialchars($link); ?>">
                                                    <?php echo htmlspecialchars($link); ?>
                                                </div>

                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>

                        </div>

                        <div class="modal-footer border-top" style="padding-top: 45px;"></div>

                    </div>
                </div>
            </div>

            <!-- Delete Modal-->
            <div class="modal" id="deleteModal<?php echo $announcementID; ?>" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                style="transform: scale(0.8);"></button>
                        </div>
                        <div class="modal-body d-flex flex-column justify-content-center align-items-center text-center">
                            <span class="mt-4 text-bold text-22">This action cannot be undone.</span>
                            <span class="mb-4 text-reg text-14">Are you sure you want to delete this Announcement?</span>
                        </div>
                        <div class="modal-footer text-sbold text-18">
                            <button type="button" class="btn rounded-pill px-4"
                                style="background-color: var(--primaryColor); border: 1px solid var(--black);"
                                data-bs-dismiss="modal">Cancel</button>

                            <form method="POST" class="m-0">
                                <input type="hidden" value="<?php echo $announcementID; ?>" name="deleteAnnouncementID">
                                <button type="submit" name="deleteAnnouncementBtn" class="btn rounded-pill px-4"
                                    style="background-color: rgba(255, 80, 80, 1); border: 1px solid var(--black);">
                                    Delete
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
            <!-- FILE VIEWER MODAL -->
            <div class="modal fade" id="viewerModal" tabindex="-1"style="z-index:10000">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content" style="border-radius:12px; overflow:hidden;">

                        <!-- Header -->
                        <div class="modal-header d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="modal-title text-sbold text-16 mb-0" id="viewerModalLabel">File Viewer</h5>

                                <a id="modalDownloadBtn" class="btn py-1 px-3 rounded-pill text-sbold text-md-14 ms-1"
                                    style="background-color: var(--primaryColor); border: 1px solid var(--black);" download>
                                    <span class="" style="display: flex; align-items: center; gap: 4px;">
                                        <span class="material-symbols-outlined" style="font-size:18px;">download_2</span>
                                        Download
                                    </span>
                                </a>
                            </div>

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
            <div class="modal fade" id="linkViewerModal" tabindex="-1"style="z-index:10000">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content" style="border-radius:12px; overflow:hidden;">

                        <!-- Header -->
                        <div class="modal-header d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="modal-title text-sbold text-16 mb-0" id="linkViewerModalLabel">Link Viewer</h5>

                                <a id="modalOpenInNewTab" class="btn py-1 px-3 rounded-pill text-sbold text-md-14 ms-1"
                                    style="background-color: var(--primaryColor); border: 1px solid var(--black);"
                                    target="_blank">
                                    <span class="" style="display: flex; align-items: center; gap: 4px;">
                                        <span class="material-symbols-outlined" style="font-size:18px;">open_in_new</span>
                                        Open
                                    </span>
                                </a>
                            </div>

                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <!-- Body -->
                        <div class="modal-body p-0" style="background:#2e2e2e; height:75vh;">
                            <iframe id="linkViewerIframe" style="width:100%; height:100%; border:none;"></iframe>
                        </div>

                    </div>
                </div>
            </div>




        <?php } // end while 
        ?>
    <?php else: ?>

        <!-- No Announcements -->
        <div class="empty-state text-center">
            <img src="../shared/assets/img/empty/announcements.png" alt="No Announcements" class="empty-state-img">
            <div class="empty-state-text text-reg text-14">
                No announcements have been posted yet.
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Toast Container -->
<div id="toastContainer"
    class="position-absolute top-0 start-50 translate-middle-x pt-5 pt-md-1 d-flex flex-column align-items-center"
    style="z-index:1100; pointer-events:none;">
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const alertEl = document.querySelector('.alert.alert-success');
        if (alertEl) {
            setTimeout(() => {
                alertEl.style.transition = "opacity 0.5s ease-out";
                alertEl.style.opacity = 0;
                setTimeout(() => alertEl.remove(), 500);
            }, 3000);
        }
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        // FILE VIEWER 
        document.querySelectorAll(".openFileViewer").forEach(fileBtn => {
            fileBtn.addEventListener("click", function(e) {
                e.preventDefault();

                const fileName = this.dataset.file;
                const ext = this.dataset.extension.toLowerCase();
                const filePath = "../shared/assets/files/" + fileName;

                // HEADER title
                document.getElementById("viewerModalLabel").textContent = fileName;

                // Download button
                const dl = document.getElementById("modalDownloadBtn");
                dl.href = filePath;
                dl.setAttribute("download", fileName);

                // Viewer container
                const viewer = document.getElementById("viewerContainer");
                viewer.innerHTML = "";

                // Preview rules
                if (ext === "pdf") {
                    viewer.innerHTML = `
                    <iframe src="${filePath}" style="width:100%; height:100%; border:none;"></iframe>
                `;
                } else if (["jpg", "jpeg", "png", "gif", "webp"].includes(ext)) {
                    viewer.innerHTML = `
                    <img src="${filePath}" style="width:100%; height:100%; object-fit:contain;">
                `;
                } else {
                    viewer.innerHTML = `
                    <div style="padding:25px; color:white;">Preview unavailable. Please download the file.</div>
                `;
                }

                new bootstrap.Modal(document.getElementById("viewerModal")).show();
            });
        });

        // LINK VIEWER 
        document.querySelectorAll(".openLinkViewer").forEach(linkBtn => {
            linkBtn.addEventListener("click", function(e) {
                e.preventDefault();

                const url = this.dataset.url;

                // Set modal title
                document.getElementById("linkViewerModalLabel").textContent = url;

                // Set iframe
                document.getElementById("linkViewerIframe").src = url;

                // Set "open in new tab" button
                document.getElementById("modalOpenInNewTab").href = url;

                new bootstrap.Modal(document.getElementById("linkViewerModal")).show();
            });
        });

    });
</script>