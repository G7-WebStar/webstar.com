<?php

$lessonQuery = "SELECT * FROM lessons WHERE courseID = '$courseID'";
$lessonResult = executeQuery($lessonQuery);

while ($lesson = mysqli_fetch_assoc($lessonResult)) {

    // Count attachments and links
    $fileCount = 0;
    $linkCount = 0;

    if (!empty($lesson['attachment'])) {
        $attachmentsArray = explode(',', $lesson['attachment']);
        $fileCount = count($attachmentsArray);
    }

    if (!empty($lesson['link'])) {
        $linksArray = explode(',', $lesson['link']);
        $linkCount = count($linksArray);
    }

    $lessonTitle = $lesson['lessonTitle'];
    $lessonID = $lesson['lessonID'];
?>

    <div class="row mb-0 mt-3">
        <div class="col">
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
                        <a href="lessons-info.php?lessonID=<?php echo $lessonID; ?>" style="color: inherit; text-decoration: none;">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>

<?php
}
?>