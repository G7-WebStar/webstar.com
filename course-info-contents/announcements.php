<?php
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
                : "shared/assets/img/courseInfo/prof.png";
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
                    <p class="d-none d-md-block mb-0 mt-3 text-reg text-14" style="color: var(--black); line-height: 140%;">
                        <?php echo $announcementContent; ?>
                    </p>

                    <!-- Mobile -->
                    <p class="text-reg d-md-none mb-0 mt-3 text-reg text-12 mobile-announcement-content"
                        style="color: var(--black); line-height: 140%;">
                        <?php echo $announcementContent; ?>
                    </p>

                    <!-- View Attachments Button -->
                    <?php if (!empty($attachmentsArray)): ?>
                        <button type="button" class="btn btn-attachments mt-3 text-med text-12" data-bs-toggle="modal"
                            data-bs-target="#attachmentsModal<?php echo $announcementID; ?>">
                            View <?php echo count($attachmentsArray); ?> Attachments
                        </button>
                    <?php endif; ?>

                    <!-- Checker (Desktop) -->
                    <div class="form-check d-none d-md-flex align-items-center mt-4" style="gap: 20px;">
                        <form method="POST">
                            <input type="hidden" name="announcementID" value="<?php echo $announcementID; ?>">
                            <input class="form-check-input" type="checkbox" name="noted" style="margin-top:0;" <?php echo $isChecked; ?>>

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
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <!-- Body -->
                        <div class="modal-body flex-grow-1 overflow-y-auto" style="max-height: 40vh;">
                            <?php foreach ($attachmentsArray as $file):
                                $filePath = $file;
                                if (!preg_match('/^https?:\/\//', $filePath)) {
                                    $filePath = "shared/assets/files/" . $file;
                                }
                                $fileExt = strtoupper(pathinfo($file, PATHINFO_EXTENSION));
                                $fileSize = (file_exists("shared/assets/files/" . $file)) ? filesize("shared/assets/files/" . $file) : 0;
                                $fileSizeMB = $fileSize > 0 ? round($fileSize / 1048576, 2) . " MB" : "Unknown size";
                                $fileNameOnly = pathinfo($file, PATHINFO_FILENAME);
                            ?>
                                <a href="<?php echo $filePath; ?>" class="text-decoration-none d-block mb-2"
                                    style="color: var(--black);" <?php if (!preg_match('/^https?:\/\//', $filePath)): ?>
                                    download="<?php echo htmlspecialchars($file); ?>" <?php endif; ?>>
                                    <div class="cardFile d-flex align-items-start w-100" style="cursor:pointer;">
                                        <i class="px-4 py-3 fa-solid fa-file"></i>
                                        <div class="ms-2">
                                            <div class="text-sbold text-16 mt-1 pe-4 file-name">
                                                <?php echo $fileNameOnly ?>
                                            </div>
                                            <div class="due text-reg text-14 mb-1">
                                                <?php echo $fileExt ?> · <?php echo $fileSizeMB ?>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>

                        <div class="modal-footer border-top" style="padding-top: 45px;"></div>
                    </div>
                </div>
            </div>

        <?php } // end while 
        ?>

    <?php else: ?>

        <!-- No Announcements -->
        <div class="empty-state text-center">
            <img src="shared/assets/img/courseInfo/megaphone.png" alt="No Announcements" class="empty-state-img"
                style="filter: grayscale(100%) brightness(2.8) contrast(0.4) opacity(0.85);">
            <div class="empty-state-text text-reg text-16">
                No announcements have been posted yet.
            </div>
        </div>

    <?php endif; ?>
</div>

<!-- Toast Container -->
<div id="toastContainer"
    class="position-absolute top-0 start-50 translate-middle-x p-3 d-flex flex-column align-items-center"
    style="z-index:1100; pointer-events:none;">
</div>

<!-- Toast Script -->
<script>
    document.querySelectorAll('input[name="noted"]').forEach(checkbox => {
        checkbox.addEventListener('change', function(e) {
            e.preventDefault();

            const form = this.closest('form');
            const formData = new FormData(form);
            const isChecked = this.checked;
            const container = document.getElementById("toastContainer");

            fetch(form.action || window.location.href, {
                method: "POST",
                body: formData
            });

            const alert = document.createElement("div");
            alert.className = `
                alert mb-2 shadow-lg text-reg text-12
                d-flex align-items-center justify-content-center gap-2
                ${isChecked ? 'alert-success' : 'alert-danger'}
            `;
            alert.role = "alert";
            alert.style.transition = "opacity 2s ease";
            alert.style.opacity = "1";

            alert.innerHTML = `
                <i class="bi ${isChecked ? 'bi-check-circle-fill' : 'bi-x-circle-fill'} fs-6"></i>
                <span>${isChecked ? 'Marked as Noted' : 'Removed from Noted'}</span>
            `;

            container.appendChild(alert);

            setTimeout(() => {
                alert.style.opacity = "0";
                setTimeout(() => alert.remove(), 2000);
            }, 3000);
        });
    });
</script>