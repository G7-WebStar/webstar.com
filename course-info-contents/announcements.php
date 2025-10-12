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
    ORDER BY a.announcementDate DESC, a.announcementTime DESC
";

$announcementResult = executeQuery($announcementQuery);

// Looping
while ($row = mysqli_fetch_assoc($announcementResult)) {
    $profilePicture = !empty($row['profilePicture']) ? $row['profilePicture'] : "shared/assets/img/courseInfo/prof.png";
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
    <div class="announcement-card d-flex align-items-start mb-3">
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
            <p class="text-reg d-md-none mb-0 mt-3 text-reg text-12" style="color: var(--black); line-height: 140%;">
                <?php echo $announcementContent; ?>
            </p>

            <?php if (!empty($attachmentsArray) || !empty($linksArray)): ?>
                <!-- Desktop -->
                <div class="d-none d-md-block mt-3">
                    <div class="d-flex flex-column flex-nowrap overflow-y-auto"
                        style="gap: 6px; max-height: 20vh; max-width: 100%; padding-bottom: 6px;">
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
                            <a href="<?php echo $filePath; ?>"
                                class="text-decoration-none" style="color: var(--black);"
                                <?php if (!preg_match('/^https?:\/\//', $filePath)) : ?>
                                download="<?php echo htmlspecialchars($file); ?>"
                                <?php endif; ?>>
                                <div class="cardFile d-flex align-items-start w-100" style="cursor:pointer;">
                                    <i class="px-4 py-3 fa-solid fa-file"></i>
                                    <div class="ms-2">
                                        <div class="text-sbold text-16 mt-1 pe-4"><?php echo $fileNameOnly ?></div>
                                        <div class="due text-reg text-14 mb-1"><?php echo $fileExt ?> · <?php echo $fileSizeMB ?></div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>

                        <?php foreach ($linksArray as $link): ?>
                            <div class="cardFile d-flex align-items-start w-100" style="padding:0.2rem;">
                                <img src="https://www.google.com/s2/favicons?domain=<?php echo urlencode(parse_url($link, PHP_URL_HOST)); ?>"
                                    alt="icon" style="width:20px; height:20px; flex-shrink:0; margin:1rem;">
                                <div class="ms-2">
                                    <div class="text-sbold text-16 mt-1 pe-4">
                                        <?php echo $fileTitle ?>
                                    </div>
                                    <div class="text-reg link text-14 mt-0">
                                        <a href="<?php echo $link; ?>" target="_blank" rel="noopener noreferrer"
                                            style="text-decoration: none; color: var(--black);">
                                            <?php echo $link; ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                    </div>
                </div>

                <!-- Mobile -->
                <div class="d-flex d-md-none flex-column mt-3 overflow-y-auto" style="gap: 4px; max-height: 22vh;">
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
                        <a href="<?php echo $filePath; ?>"
                            class="text-decoration-none" style="color: var(--black);"
                            <?php if (!preg_match('/^https?:\/\//', $filePath)) : ?>
                            download="<?php echo htmlspecialchars($file); ?>"
                            <?php endif; ?>>
                            <div class="cardFile d-flex align-items-start" style="cursor:pointer;">
                                <i class="p-3 fa-solid fa-file"></i>
                                <div class="d-flex flex-column">
                                    <div class="text-sbold text-14 mt-1 file-name"><?php echo $fileNameOnly ?></div>
                                    <div class="due text-reg text-12 mb-1"><?php echo $fileExt ?> · <?php echo $fileSizeMB ?></div>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>

                    <?php foreach ($linksArray as $link): ?>
                        <div class="cardFile d-flex align-items-start w-100" style="cursor:pointer; box-sizing:border-box;">
                            <img src="https://www.google.com/s2/favicons?domain=<?php echo urlencode(parse_url($link, PHP_URL_HOST)); ?>"
                                alt="icon" style="width:20px; height:20px; flex-shrink:0; margin:1rem;">
                            <div class="d-flex flex-column" style="flex:1; min-width:0; word-break:break-word; overflow-wrap:break-word;">
                                <div class="text-sbold text-14 mt-1 file-name">
                                    <?php echo $fileTitle ?>
                                </div>
                                <div class="due text-reg text-12 mb-1">
                                    <a href="<?php echo $link; ?>" target="_blank" rel="noopener noreferrer"
                                        style="text-decoration: none; color: var(--black);">
                                        <?php echo $link; ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Checker (desktop) -->
            <div class="form-check d-none d-md-flex align-items-center mt-4" style="gap: 20px;">
                <form method="POST">
                    <input type="hidden" name="announcementID" value="<?php echo $announcementID; ?>">
                    <input class="form-check-input" type="checkbox"
                        name="noted"
                        onchange="this.form.submit()"
                        style="margin-top:0;"
                        <?php echo $isChecked; ?>>
                    <label class="form-check-label text-med text-12 mb-0"
                        style="color: var(--black); position: relative; top: -5px;">
                        Noted
                    </label>
                </form>

                <div class="text-med text-12 ms-3" style="color: var(--black); position: relative; top: -2px;">
                    <?php echo $totalNoted . " of " . $totalStudents . " students noted"; ?>
                </div>
            </div>

            <!-- Checker (mobile) -->
            <div class="form-check d-flex d-md-none align-items-center mt-4" style="gap: 6px;">
                <form method="POST">
                    <input type="hidden" name="announcementID" value="<?php echo $announcementID; ?>">
                    <input class="form-check-input" type="checkbox"
                        name="noted"
                        onchange="this.form.submit()"
                        style="margin-top:0;"
                        <?php echo $isChecked; ?>>
                    <label class="form-check-label text-med text-12 mb-0"
                        style="color: var(--black); position: relative; top: -5px;">
                        Noted
                    </label>
                </form>

                <div class="text-med text-12 ms-2" style="color: var(--black); position: relative; top: -3px;">
                    <?php echo $totalNoted . " of " . $totalStudents . " students noted"; ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>