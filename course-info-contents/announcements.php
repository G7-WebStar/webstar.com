<?php

// $courseID = isset($_GET['courseID']) ? $_GET['courseID'] : '';

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
        a.announcementTitle,
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

while ($row = mysqli_fetch_assoc($announcementResult)) {
    $profilePicture = !empty($row['profilePicture']) ? $row['profilePicture'] : "shared/assets/img/courseInfo/prof.png";
    $fullName = $row['firstName'] . " " . $row['lastName'];
    $announcementDate = date("F j, Y g:iA", strtotime($row['announcementDate'] . " " . $row['announcementTime']));
    $announcementContent = $row['announcementContent'];
    $announcementID = $row['announcementID'];
    $totalNoted = $row['totalNoted'];
    $totalStudents = $row['totalStudents'];
    $isChecked = ($row['isUserNoted']) ? 'checked' : '';
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

            <p class="d-none d-md-block mb-0 mt-3 text-reg text-14" style="color: var(--black); line-height: 140%;">
                <?php echo $announcementContent; ?>
            </p>

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
            <p class="text-reg d-md-none mb-0 mt-3 text-reg text-12" style="color: var(--black); line-height: 140%;">
                <?php echo $announcementContent; ?>
            </p>
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