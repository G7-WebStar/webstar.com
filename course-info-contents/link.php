<?php
$sortLink = $_POST['sortLink'] ?? 'Newest';

switch ($sortLink) {
    case 'Oldest':
        $linkOrderBy = "uploadedAt ASC";
        break;

    default: // Newest
        $linkOrderBy = "uploadedAt DESC";
        break;
}


$fileQuery = "SELECT * FROM files WHERE courseID = '$courseID' ORDER BY $linkOrderBy";
$fileResult = executeQuery($fileQuery);

// Check if there is at least one valid link with title and link
$hasLinks = false;
while ($file = mysqli_fetch_assoc($fileResult)) {
    if (!empty($file['fileTitle']) && !empty($file['fileLink'])) {
        $hasLinks = true;
        break;
    }
}

// Reset pointer to start of result set
mysqli_data_seek($fileResult, 0);
?>

<?php if ($hasLinks): ?>
    <!-- Sort By Dropdown (Shown only when there are files) -->
    <div class="d-flex align-items-center flex-nowrap mb-2" id="header">
        <div class="d-flex align-items-center flex-nowrap">
            <span class="dropdown-label me-2 text-reg text-12">Sort by</span>
            <form method="POST">
                <input type="hidden" name="activeTab" value="link">
                <select class="select-modern text-reg text-12" name="sortLink" onchange="this.form.submit()">
                    <option value="Newest" <?php echo ($sortLink == 'Newest') ? 'selected' : ''; ?>>Newest</option>
                    <option value="Oldest" <?php echo ($sortLink == 'Oldest') ? 'selected' : ''; ?>>Oldest</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Files list -->
    <div class="d-flex flex-column flex-nowrap overflow-x-hidden">
        <?php while ($file = mysqli_fetch_assoc($fileResult)): ?>
            <?php
            $fileLink = $file['fileLink'];
            $fileTitle = $file['fileTitle'];

            if (empty($fileTitle) || empty($fileLink)) continue;
            ?>
            <div class="row mb-0 mt-2">
                <div class="col">
                    <a href="<?php echo htmlspecialchars($fileLink); ?>"
                        target="_blank" rel="noopener noreferrer"
                        style="text-decoration: none; color: inherit; display: block;">
                        <div class="todo-card d-flex align-items-stretch px-2 py-3">
                            <div class="d-flex w-100 align-items-center">

                                <!-- File Info -->
                                <div class="d-flex align-items-center flex-grow-1">
                                    <div class="mx-4 d-flex align-items-center">
                                        <span class="material-symbols-rounded" style="line-height: 1;">
                                            public
                                        </span>
                                    </div>
                                    <div class="file-info">
                                        <div class="text-sbold text-16 text-truncate" style="line-height: 1.2;"
                                            title="<?php echo htmlspecialchars($fileTitle); ?>">
                                            <?php echo htmlspecialchars($fileTitle); ?>
                                        </div>
                                        <div class="text-reg text-12 mt-1 text-truncate" style="line-height: 1.2;"
                                            title="<?php echo htmlspecialchars($fileLink); ?>">
                                            <?php echo htmlspecialchars($fileLink); ?>
                                        </div>
                                    </div>
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
        <img src="shared/assets/img/empty/files.png"
            alt="No Links"
            class="empty-state-img">
        <div class="empty-state-text text-14 d-flex flex-column align-items-center">
            <p class="text-med mt-1 mb-0">No links yet.</p>
            <p class="text-reg mt-1">Links from announcements and quests appear here.</p>
        </div>
    </div>
<?php endif; ?>