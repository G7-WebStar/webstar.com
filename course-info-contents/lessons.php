<?php
$lessonQuery = "SELECT * FROM lessons WHERE courseID = '$courseID'";
$lessonResult = executeQuery($lessonQuery);
?>

<?php if (mysqli_num_rows($lessonResult) > 0): ?>

    <!-- Sort By Dropdown (only shown if there are lessons) -->
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

    <!-- Lessons List -->
    <div class="d-flex flex-column flex-nowrap overflow-y-auto overflow-x-hidden"
        style="max-height: 70vh;">
        <?php
        while ($lesson = mysqli_fetch_assoc($lessonResult)) {
            $lessonID = $lesson['lessonID'];
            $lessonTitle = $lesson['lessonTitle'];

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
            <div class="row mb-0 mt-3">
                <div class="col">
                    <a href="lessons-info.php?lessonID=<?php echo $lessonID; ?>"
                        style="text-decoration: none; color: inherit; display: block;">
                        <div class="todo-card d-flex align-items-stretch p-2">
                            <div class="d-flex w-100 align-items-center justify-content-between">

                                <!-- Left side: File icon and Text -->
                                <div class="d-flex align-items-center flex-grow-1">
                                    <div class="mx-4 d-flex align-items-center">
                                        <span class="material-symbols-outlined" style="line-height: 1;">
                                            menu_book
                                        </span>
                                    </div>

                                    <div>
                                        <div class="text-sbold text-16 py-1" style="line-height: 1;">
                                            <?php echo $lessonTitle ?>
                                        </div>
                                        <div class="text-reg text-12" style="line-height: 1;">
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
        <img src="shared/assets/img/notebook.png" alt="No Lessons" class="empty-state-img">
        <div class="empty-state-text text-reg text-16">
            No lessons have been posted yet.
        </div>
    </div>
<?php endif; ?>
