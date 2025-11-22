<?php
function getRelativeTime($datetime, $fullDateFallback = true)
{
    $now = new DateTime("now", new DateTimeZone('Asia/Manila'));
    $past = new DateTime($datetime, new DateTimeZone('Asia/Manila'));
    $diff = $now->getTimestamp() - $past->getTimestamp();

    if ($diff < 0) {
        $diff = 0;
    }

    if ($diff < 3600) { // less than 1 hour → minutes
        $minutes = max(1, floor($diff / 60));
        return $minutes . 'm ago';
    } elseif ($diff < 86400) { // less than 1 day → hours
        $hours = floor($diff / 3600);
        return $hours . 'h ago';
    } elseif ($diff < 604800) { // less than 1 week → days
        $days = floor($diff / 86400);
        return $days . 'd ago';
    } else { // older → show full date
        return $fullDateFallback ? date("F j, Y", strtotime($datetime)) : floor($diff / 604800) . 'w ago';
    }
}

// HANDLE POST FIRST
if (isset($_POST['announcementID'])) {
    $announcementID = $_POST['announcementID'];

    if (isset($_POST['noted'])) {
        $insertQuery = "INSERT IGNORE INTO announcementnotes (announcementID, userID, notedAt)
                        VALUES ('$announcementID', '$userID', NOW())";
        executeQuery($insertQuery);
    } else {
        $deleteQuery = "DELETE FROM announcementnotes 
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
    LEFT JOIN announcementnotes n ON a.announcementID = n.announcementID 
    WHERE a.courseID = '$courseID'
    GROUP BY a.announcementID
    ORDER BY $orderBy
";

$announcementResult = executeQuery($announcementQuery);
?>

<div class="d-flex flex-column flex-nowrap overflow-x-hidden">

    <?php if (mysqli_num_rows($announcementResult) > 0): ?>

        <!-- Sort By Dropdown -->
        <div class="d-flex align-items-center flex-wrap mb-2" id="header">
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
                : "shared/assets/img/courseInfo/prof.png";
            $fullName = $row['firstName'] . " " . $row['lastName'];
            $announcementDate = getRelativeTime($row['announcementDate'] . " " . $row['announcementTime']);
            $announcementContent = $row['announcementContent'];
            $announcementID = $row['announcementID'];
            $totalNoted = $row['totalNoted'];
            $totalStudents = $row['totalStudents'];
            $isChecked = ($row['isUserNoted']) ? 'checked' : '';

            $attachmentsArray = [];
            $fileTitlesMap = []; // NEW
            $linksArray = [];
            $filesQuery = "SELECT * FROM files WHERE announcementID = '$announcementID'";
            $filesResult = executeQuery($filesQuery);

            while ($file = mysqli_fetch_assoc($filesResult)) {
                if (!empty($file['fileAttachment'])) {
                    $attachments = array_map('trim', explode(',', $file['fileAttachment']));
                    $attachmentsArray = array_merge($attachmentsArray, $attachments);

                    // Map each attachment to its title
                    foreach ($attachments as $att) {
                        $fileTitlesMap[$att] = !empty($file['fileTitle']) ? $file['fileTitle'] : $att;
                    }
                }

                if (!empty($file['fileLink'])) {
                    $links = array_map('trim', explode(',', $file['fileLink']));
                    $linksArray = array_merge($linksArray, $links);
                }

                $fileTitle = !empty($file['fileTitle']) ? $file['fileTitle'] : '';
            }
            ?>

            <!-- Announcement Card -->
            <div class="announcement-card d-flex align-items-start mb-1">
                <!-- Instructor Image -->
                <div class="flex-shrink-0 me-3">
                    <img src="shared/assets/pfp-uploads/<?php echo $profilePicture; ?>" alt="Instructor Image"
                        style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">
                </div>

                <!-- Text Content -->
                <div class="text-start">
                    <div class="text-reg text-12" style="color: var(--black); line-height: 140%;">
                        <strong>Prof. <?php echo $fullName; ?></strong><br>
                        <span style="font-weight: normal;"><?php echo $announcementDate; ?></span>
                    </div>

                    <!-- Desktop -->
                    <p class="d-none d-md-block mb-0 text-reg text-14"
                        style="color: var(--black); line-height: 140%; white-space: pre-line;">
                        <?php echo nl2br($announcementContent); ?>
                    </p>

                    <!-- Mobile -->
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
                    <div class="form-check d-none d-md-flex align-items-center mt-4" style="gap: 20px;">
                        <form method="POST">
                            <input type="hidden" name="announcementID" value="<?php echo $announcementID; ?>">
                            <input class="form-check-input" type="checkbox" name="noted" onchange="this.form.submit()"
                                style="margin-top:0;" <?php echo $isChecked; ?>>

                            <label class="form-check-label text-med text-12 mb-0"
                                style="color: var(--black); position: relative; top: -5px;">
                                Noted
                            </label>
                        </form>
                    </div>

                    <!-- Checker (Mobile) -->
                    <div class="form-check d-flex d-md-none align-items-center mt-4" style="gap: 6px;">
                        <form method="POST">
                            <input type="hidden" name="announcementID" value="<?php echo $announcementID; ?>">
                            <input class="form-check-input" type="checkbox" name="noted" onchange="this.form.submit()"
                                style="margin-top:0;" <?php echo $isChecked; ?>>
                            <label class="form-check-label text-med text-12 mb-0"
                                style="color: var(--black); position: relative; top: -5px;">
                                Noted
                            </label>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Attachments + Links Modal -->
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
                                                <div class="text-sbold text-16 mt-1 pe-4 file-name text-truncate"
                                                    style="max-width:330px;"
                                                    title="<?php echo htmlspecialchars($fileTitlesMap[$decodedAttachment]); ?>">
                                                    <?php echo htmlspecialchars($fileTitlesMap[$decodedAttachment]); ?>
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
                                <div class="text-sbold text-16 <?php echo !empty($attachmentsArray) ? 'mt-4' : 'mt-0'; ?> mb-2">
                                    Links</div>
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

            <!-- FILE VIEWER MODAL -->
            <div class="modal fade" id="viewerModal" tabindex="-1" style="z-index:10000">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content" style="border-radius:12px; overflow:hidden;">

                        <!-- Header -->
                        <div class="modal-header d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="modal-title text-sbold text-16 mb-0 text-truncate" style="max-width:150px;"
                                    id="viewerModalLabel">File Viewer</h5>

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
            <div class="modal fade" id="linkViewerModal" tabindex="-1" style="z-index:10000">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content" style="border-radius:12px; overflow:hidden;">

                        <!-- Header -->
                        <div class="modal-header d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="modal-title text-sbold text-16 mb-0 text-truncate" style="max-width:150px;"
                                    id="linkViewerModalLabel">Link Viewer</h5>

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
            <img src="shared/assets/img/empty/announcements.png" alt="No Announcements" class="empty-state-img">
            <div class="empty-state-text text-reg text-14">
                No announcements have been posted yet.
            </div>
        </div>

    <?php endif; ?>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['announcementID'])) {
        $toastMessage = isset($_POST['noted'])
            ? 'Marked as Noted'
            : 'Removed from Noted';
        $toastType = isset($_POST['noted'])
            ? 'alert-success'
            : 'alert-danger';
        ?>
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                const container = document.getElementById("toastContainer");
                const alert = document.createElement("div");
                alert.className = `alert mb-2 shadow-lg text-med text-12
        d-flex align-items-center justify-content-center gap-2 px-3 py-2 <?php echo $toastType; ?>`;
                alert.role = "alert";
                alert.innerHTML = `
        <i class="bi <?php echo ($toastType === 'alert-success') ? 'bi-check-circle-fill' : 'bi-x-circle-fill'; ?> fs-6"></i>
        <span><?php echo $toastMessage; ?></span>
    `;
                container.appendChild(alert);
                setTimeout(() => {
                    alert.style.opacity = "0";
                    setTimeout(() => alert.remove(), 2000);
                }, 3000);
            });
        </script>
    <?php } ?>

</div>

<!-- Toast Container -->
<div id="toastContainer"
    class="position-absolute top-0 start-50 translate-middle-x pt-5 pt-md-1 d-flex flex-column align-items-center"
    style="z-index:1100; pointer-events:none;">
</div>

<!-- Toast Script -->
<script>
    document.querySelectorAll('input[name="noted"]').forEach(checkbox => {
        checkbox.addEventListener('change', function (e) {
            // e.preventDefault();

            const form = this.closest('form');
            const formData = new FormData(form);
            const isChecked = this.checked;
            const container = document.getElementById("toastContainer");

            // fetch(form.action || window.location.href, {
            //     method: "POST",
            //     body: formData
            // });

            checkbox.addEventListener('change', function () {
                this.closest('form').submit();
            });

            // const alert = document.createElement("div");
            // alert.className = `
            //     alert mb-2 shadow-lg text-med text-12
            //     d-flex align-items-center justify-content-center gap-2 px-3 py-2
            //     ${isChecked ? 'alert-success' : 'alert-danger'}
            // `;
            // alert.role = "alert";
            // alert.style.transition = "opacity 2s ease";
            // alert.style.opacity = "1";

            // alert.innerHTML = `
            //     <i class="bi ${isChecked ? 'bi-check-circle-fill' : 'bi-x-circle-fill'} fs-6"></i>
            //     <span>${isChecked ? 'Marked as Noted' : 'Removed from Noted'}</span>
            // `;

            // container.appendChild(alert);

            // setTimeout(() => {
            //     alert.style.opacity = "0";
            //     setTimeout(() => alert.remove(), 2000);
            // }, 3000);
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        // FILE VIEWER 
        document.querySelectorAll(".openFileViewer").forEach(fileBtn => {
            fileBtn.addEventListener("click", function (e) {
                e.preventDefault();

                const fileName = this.dataset.file;
                const ext = this.dataset.extension.toLowerCase();
                const filePath = "shared/assets/files/" + fileName;

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
            linkBtn.addEventListener("click", function (e) {
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