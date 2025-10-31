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


$fileQuery = "SELECT * FROM files WHERE courseID = '$courseID' ORDER BY $attachmentOrderBy";
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
            <?php if (empty($file['fileAttachment'])) continue; // skip empty titles 
            ?>

            <?php
            $filePath = $file['fileLink'];
            if (!preg_match('/^https?:\/\//', $filePath)) {
                $filePath = $file['fileLink']; // local path
            }
            ?>
            <div class="row mb-0 mt-2">
                <div class="col">
                    <a href="<?php echo $filePath; ?>"
                        <?php if (!preg_match('/^https?:\/\//', $filePath)) : ?>
                        download="<?php echo htmlspecialchars($file['fileAttachment']); ?>"
                        <?php endif; ?>
                        style="text-decoration: none; color: inherit; display: block;">
                        <div class="todo-card d-flex align-items-stretch p-2">
                            <div class="d-flex w-100 align-items-center justify-content-between">
                                <!-- Attachment Info -->
                                <div class="d-flex align-items-center flex-grow-1">
                                    <div class="mx-4 d-flex align-items-center">
                                        <span class="material-symbols-outlined" style="line-height: 1;">
                                            draft
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-sbold text-16 py-1" style="line-height: 1;">
                                            <?php echo htmlspecialchars($file['fileAttachment']); ?>
                                        </div>
                                        <div class="text-reg text-12" style="line-height: 1;">
                                            Uploaded <?php echo date("F d, Y", strtotime($file['uploadedAt'])); ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Download Icon -->
                                <div class="mx-4 d-flex align-items-center">
                                    <span class="material-symbols-outlined">
                                        download_2
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

<?php else: ?>
    <!-- No Files Placeholder -->
    <div class="empty-state text-center">
        <img src="shared/assets/img/courseInfo/bookmark.png"
            alt="No Files"
            class="empty-state-img">
        <div class="empty-state-text text-16 d-flex flex-column align-items-center">
            <p class="text-med mb-0">No files yet.</p>
            <p class="text-reg">Attachments from announcements and quests appear here.</p>
        </div>
    </div>
<?php endif; ?>