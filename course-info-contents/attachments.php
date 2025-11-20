<?php
$sortAttachment = $_POST['sortAttachment'] ?? 'Newest';

switch ($sortAttachment) {
    case 'Oldest':
        $attachmentOrderBy = "uploadedAt ASC";
        break;

    default: // Newest
        $attachmentOrderBy = "uploadedAt DESC";
        break;
}


$fileQuery = "SELECT * FROM files WHERE courseID = '$courseID' AND submissionID IS NULL ORDER BY $attachmentOrderBy";
$fileResult = executeQuery($fileQuery);

// Check if there is at least one file with a non-empty title
$hasFiles = false;
while ($file = mysqli_fetch_assoc($fileResult)) {
    if (!empty($file['fileAttachment'])) {
        $hasFiles = true;
        break;
    }
}

// Reset pointer to start of result set
mysqli_data_seek($fileResult, 0);
?>

<?php if ($hasFiles): ?>

    <!-- Sort By Dropdown (Shown only when there are attachments) -->
    <div class="d-flex align-items-center flex-nowrap mb-1">
        <div class="d-flex align-items-center flex-nowrap">
            <span class="dropdown-label me-2 text-reg text-14">Sort by</span>
            <form method="POST">
                <input type="hidden" name="activeTab" value="attachments">
                <select class="select-modern text-reg text-14" name="sortAttachment" onchange="this.form.submit()">
                    <option value="Newest" <?php echo ($sortAttachment == 'Newest') ? 'selected' : ''; ?>>Newest</option>
                    <option value="Oldest" <?php echo ($sortAttachment == 'Oldest') ? 'selected' : ''; ?>>Oldest</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Attachments List -->
    <div class="d-flex flex-column flex-nowrap overflow-x-hidden">
        <?php while ($file = mysqli_fetch_assoc($fileResult)): ?>
            <?php if (empty($file['fileAttachment']))
                continue; ?>

            <?php
            $fileName = $file['fileAttachment'];
            $filePath = $file['fileLink'];
            if (!preg_match('/^https?:\/\//', $filePath)) {
                $filePath = "shared/assets/files/" . $fileName; // local path
            }
            ?>

            <div class="row mb-0 mt-2">
                <div class="col">
                    <div class="todo-card d-flex align-items-stretch p-2">
                        <div class="d-flex w-100 align-items-center justify-content-between">

                            <!-- Attachment Info (click opens modal) -->
                            <div class="d-flex align-items-center flex-grow-1" style="cursor:pointer;"
                                onclick="openTodoViewer('<?php echo addslashes($fileName); ?>', '<?php echo addslashes($filePath); ?>')">
                                <div class="mx-4 d-flex align-items-center">
                                    <span class="material-symbols-outlined" style="line-height: 1;">
                                        draft
                                    </span>
                                </div>
                                <div class="file-info">
                                    <div class="text-sbold text-16 py-1 text-truncate" style="line-height: 1;"
                                        title="<?php echo htmlspecialchars($fileName); ?>">
                                        <?php echo htmlspecialchars($fileName); ?>
                                    </div>
                                    <div class="text-reg text-12" style="line-height: 1;">
                                        Uploaded <?php echo date("F d, Y", strtotime($file['uploadedAt'])); ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Download Icon (click downloads file) -->
                            <div class="mx-4 d-flex align-items-center">
                                <a href="<?php echo $filePath; ?>" download="<?php echo htmlspecialchars($fileName); ?>"
                                    style="color:inherit;">
                                    <span class="material-symbols-outlined" style="cursor:pointer;">
                                        download_2
                                    </span>
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- FILE VIEWER MODAL -->
    <div class="modal fade" id="todoViewerModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content" style="border-radius:12px; overflow:hidden;">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="modal-title text-sbold text-16 mb-0 text-truncate" style="max-width:150px;" id="todoViewerModalLabel">File Viewer</h5>
                        <a id="todoModalDownloadBtn" class="btn py-1 px-3 rounded-pill text-sbold text-md-14 ms-1"
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
                    <div id="todoViewerContainer" style="width:100%; height:100%;"></div>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- No Files Placeholder -->
    <div class="empty-state text-center">
        <img src="shared/assets/img/empty/files.png"
            alt="No Files"
            class="empty-state-img">
        <div class="empty-state-text text-14 d-flex flex-column align-items-center">
            <p class="text-med mt-1 mb-0">No files yet.</p>
            <p class="text-reg mt-1">Attachments from announcements and quests appear here.</p>
        </div>
    </div>
<?php endif; ?>

<!-- Toast Container -->
<div id="toastContainerFiles"
    class="position-absolute top-0 start-50 translate-middle-x pt-5 pt-md-1 d-flex flex-column align-items-center"
    style="z-index:1100; pointer-events:none;">
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toastContainer = document.getElementById('toastContainerFiles');

        function showToast(message) {
            const toastEl = document.createElement('div');
            toastEl.className = "alert alert-success mb-2 shadow-lg text-med text-12 d-flex align-items-center justify-content-center gap-2 px-3 py-2";
            toastEl.style.cssText = "border-radius:8px; display:flex; align-items:center; gap:8px; padding:0.5rem 0.75rem; text-align:center; background-color:#d1e7dd; color:#0f5132;";
            toastEl.innerHTML = `<i class="bi bi-check-circle-fill fs-6" style="color: var(--black);"></i>
                             <span style="color: var(--black);">${message}</span>`;

            toastContainer.appendChild(toastEl);

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


    function openTodoViewer(fileName, filePath) {
        document.getElementById("todoViewerModalLabel").textContent = fileName;
        document.getElementById("todoModalDownloadBtn").href = filePath;

        const viewer = document.getElementById("todoViewerContainer");
        viewer.innerHTML = "";
        const ext = fileName.split(".").pop().toLowerCase();

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

        new bootstrap.Modal(document.getElementById("todoViewerModal")).show();
    }
</script>
</script>