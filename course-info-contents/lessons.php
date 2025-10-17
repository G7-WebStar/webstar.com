<?php
$lessonQuery = "SELECT * FROM lessons WHERE courseID = '$courseID'";
$lessonResult = executeQuery($lessonQuery);

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
    <!-- Sort By Dropdown (Front-End Only, above announcements) -->
    <div class="d-flex align-items-center flex-nowrap mb-3" id="header">
        <span class="dropdown-label me-2">Sort by:</span>
        <button class="btn dropdown-toggle dropdown-custom" type="button"
            data-bs-toggle="dropdown" aria-expanded="false">
            <span>Newest</span>
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item text-reg" href="#">Newest</a></li>
            <li><a class="dropdown-item text-reg" href="#">Oldest</a></li>
            <li><a class="dropdown-item text-reg" href="#">Unread first</a></li>
        </ul>
    </div>

    <div class="d-flex flex-column flex-nowrap overflow-y-auto overflow-x-hidden"
        style="max-height: 70vh;">
        <div class="row mb-0 mt-3">
            <div class="col">
                <a href="lessons-info.php?lessonID=<?php echo $lessonID; ?>"
                    style="text-decoration: none; color: inherit; display: block;">
                    <div class="todo-card d-flex align-items-stretch p-2">
                        <div class="d-flex w-100 align-items-center justify-content-between">

                            <!-- Left side: File icon and Text -->
                            <div class="d-flex align-items-center flex-grow-1">
                                <!-- File icon -->
                                <div class="mx-4">
                                    <img src="shared/assets/img/lessons.png" alt="File Icon" style="width: 20px; height: 25px;">
                                </div>

                                <!-- Content -->
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
    </div>
<?php
}
?>