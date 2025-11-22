<?php
// LESSON SORTING BACKEND (NO UNREAD — only createdAt)
$sortLesson = $_POST['sortLesson'] ?? 'Newest';

switch ($sortLesson) {
    case 'Oldest':
        $lessonOrderBy = "createdAt ASC";
        break;

    default: // Newest
        $lessonOrderBy = "createdAt DESC";
        break;
}


$lessonQuery = "SELECT * FROM lessons WHERE courseID = '$courseID' ORDER BY $lessonOrderBy";
$lessonResult = executeQuery($lessonQuery);

// Check if there is at least one lesson with a valid title
$hasLessons = false;
while ($lesson = mysqli_fetch_assoc($lessonResult)) {
    if (!empty($lesson['lessonTitle'])) {
        $hasLessons = true;
        break;
    }
}

// Reset pointer to start of result set
mysqli_data_seek($lessonResult, 0);
?>

<?php if ($hasLessons): ?>

    <!-- Sort By Dropdown (only shown if there are lessons) -->
    <div class="d-flex align-items-center flex-nowrap mb-2">
        <div class="d-flex align-items-center flex-nowrap">
            <span class="dropdown-label me-2 text-reg text-12">Sort by</span>
            <form method="POST">
                <input type="hidden" name="activeTab" value="lessons">
                <select class="select-modern text-reg text-12" name="sortLesson" onchange="this.form.submit()">
                    <option value="Newest" <?php echo ($sortLesson == 'Newest') ? 'selected' : ''; ?>>Newest</option>
                    <option value="Oldest" <?php echo ($sortLesson == 'Oldest') ? 'selected' : ''; ?>>Oldest</option>
                </select>
            </form>

        </div>
    </div>

    <!-- Lessons List -->
    <div class="d-flex flex-column flex-nowrap overflow-x-hidden">
        <?php
        while ($lesson = mysqli_fetch_assoc($lessonResult)) {
            $lessonID = $lesson['lessonID'];
            $lessonTitle = $lesson['lessonTitle'];

            if (empty($lessonTitle)) continue; // skip lessons with empty title

            $fileQuery = "SELECT * FROM files WHERE lessonID = '$lessonID'";
            $fileResult = executeQuery($fileQuery);

            $attachmentsArray = [];
            $linksArray = [];

            while ($file = mysqli_fetch_assoc($fileResult)) {
                if (!empty($file['fileAttachment'])) {
                    $attachments = array_map('trim', explode(',', $file['fileAttachment']));
                    $attachmentsArray = array_merge($attachmentsArray, $attachments);
                }

                if (!empty($file['fileLink'])) {
                    $links = array_map('trim', explode(',', $file['fileLink']));
                    $linksArray = array_merge($linksArray, $links);
                }
            }

            $fileCount = count($attachmentsArray);
            $linkCount = count($linksArray);
        ?>
            <div class="row mb-0 mt-2">
                <div class="col">
                    <a href="lessons-info.php?lessonID=<?php echo $lessonID; ?>"
                        style="text-decoration: none; color: inherit; display: block;">
                        <div class="todo-card d-flex align-items-stretch px-2 py-3">
                            <div class="d-flex w-100 align-items-center justify-content-between">

                                <!-- Left side: File icon and Text -->
                                <div class="d-flex align-items-center flex-grow-1">
                                    <div class="mx-4 d-flex align-items-center">
                                        <span class="material-symbols-outlined" style="line-height: 1;">
                                            menu_book
                                        </span>
                                    </div>

                                    <div>
                                        <div class="text-sbold text-16" style="line-height: 1;">
                                            <?php echo htmlspecialchars($lessonTitle); ?>
                                        </div>
                                        <div class="text-reg text-12 mt-1" style="line-height: 1;">
                                            <?php echo $fileCount . " file" . ($fileCount != 1 ? "s" : "") . " · " . $linkCount . " link" . ($linkCount != 1 ? "s" : ""); ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Arrow icon -->
                                <div class="mx-4">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        <?php
        } // end while
        ?>
    </div>

<?php else: ?>
    <!-- No Lessons Placeholder -->
    <div class="empty-state text-center">
        <img src="shared/assets/img/empty/lessons.png" alt="No Lessons" class="empty-state-img">
        <div class="empty-state-text text-reg text-14">
            No lessons have been posted yet.
        </div>
    </div>
<?php endif; ?>