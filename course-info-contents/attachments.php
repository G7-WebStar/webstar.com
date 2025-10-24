<?php
$fileQuery = "SELECT * FROM files WHERE courseID = '$courseID'";
$fileResult = executeQuery($fileQuery);
?>

<?php if (mysqli_num_rows($fileResult) > 0): ?>

    <!-- Sort By Dropdown (Shown only when there are attachments) -->
    <div class="d-flex align-items-center flex-nowrap mb-1" id="header">
        <div class="d-flex align-items-center flex-nowrap">
            <span class="dropdown-label me-2 text-reg">Sort by</span>
            <div class="custom-dropdown">
                <button class="dropdown-btn text-reg text-14">Newest</button>
                <ul class="dropdown-list text-reg text-14">
                    <li data-value="Newest">Newest</li>
                    <li data-value="Oldest">Oldest</li>
                    <li data-value="Unread">Unread</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Attachments List -->
    <div class="d-flex flex-column flex-nowrap overflow-y-auto overflow-x-hidden"
        style="max-height: 70vh;">
        <?php while ($file = mysqli_fetch_assoc($fileResult)): ?>
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
