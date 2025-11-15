<?php
include('../database/connect.php');
include("prof-session-process.php");

if (isset($_POST['markUnarchived'])) {
    $courseID = $_POST['courseID'];
    $update = "UPDATE courses SET isActive = '1' WHERE courseID = '$courseID'";
    executeQuery($update);
}

if (isset($_POST['markArchived'])) {
    $courseID = $_POST['courseID'];
    $update = "UPDATE courses SET isActive = '0' WHERE courseID = '$courseID'";
    executeQuery($update);
}

$filter = isset($_GET['status']) ? $_GET['status'] : 'active';
$isActive = ($filter == 'archived') ? '0' : '1';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$course = "
    SELECT 
        userinfo.userInfoID,
        userinfo.userID,
        userinfo.profilePicture,
        userinfo.firstName,
        userinfo.lastName,
        courses.courseID,
        courses.courseCode,
        courses.courseTitle,
        courses.courseImage,
        GROUP_CONCAT(
            CONCAT(
                courseSchedule.day, ' ', 
                DATE_FORMAT(courseSchedule.startTime, '%h:%i %p'), '-', 
                DATE_FORMAT(courseSchedule.endTime, '%h:%i %p')
            ) 
            ORDER BY FIELD(courseSchedule.day, 'Mon','Tue','Wed','Thu','Fri','Sat','Sun'), courseSchedule.startTime
            SEPARATOR '\n'
        ) AS courseSchedule
    FROM userinfo
    INNER JOIN courses ON userinfo.userID = courses.userID
    LEFT JOIN courseSchedule ON courses.courseID = courseSchedule.courseID
    WHERE courses.userID = '$userID'
      AND courses.isActive = '$isActive'
";

if (!empty($search)) {
    $course .= " AND (courses.courseCode LIKE '%$search%' OR courses.courseTitle LIKE '%$search%')";
}

$course .= " GROUP BY courses.courseID";

$courses = executeQuery($course);
?>

<?php if ($courses && mysqli_num_rows($courses) > 0) { ?>
    <?php while ($row = mysqli_fetch_assoc($courses)) { ?>
        <div class="col-12 col-md-6 col-lg-4 col-xl-3 mt-2 m-0 p-0 pe-0 pe-md-2">
            <div class="card course-card border border-black rounded-4 d-flex flex-column h-100">
                <a href="course-info.php?courseID=<?php echo $row['courseID']; ?>">
                    <img src="../shared/assets/img/course-images/<?php echo $row['courseImage']; ?>"
                        class="card-img-top object-fit-cover rounded-top-4 course-image" alt="..."
                        style="background-color: var(--primaryColor); height: 150px;">
                </a>
                <div class="card-body border-top border-black">
                    <div class="row lh-1 mb-2">
                        <a href="courses-info.php?courseID=<?php echo $row['courseID']; ?>"
                            class="text-decoration-none text-black">
                            <p class="card-text text-sbold text-18 m-0 course-code">
                                <?php echo $row['courseCode']; ?>
                            </p>
                        </a>
                        <p class="card-text text-med text-14 mb-2 course-title">
                            <?php echo $row['courseTitle']; ?>
                        </p>
                    </div>
                    <div class="row mb-2">
                        <div class="d-flex align-items-center">
                            <div class="me-2">
                                <img src="../shared/assets/pfp-uploads/<?php echo $row['profilePicture']; ?>"
                                    alt="" width="24" height="24" class="rounded-circle">
                            </div>
                            <div class="lh-sm">
                                <p class="card-text text-med text-12 m-0">
                                    <?php echo $row['firstName'] . ' ' . $row['lastName']; ?>
                                </p>
                                <p class="card-text text-med text-12 mb-0">Professor</p>
                            </div>
                        </div>
                    </div>
                    <div class="row ps-2 pe-3 mb-3 align-items-center">
                        <!-- Calendar Icon -->
                        <div
                            class="col-auto d-flex justify-content-center align-items-start p-0 ms-1">
                            <span class="material-symbols-rounded"
                                style="font-size:24px; color:#2c2c2c;">
                                calendar_today
                            </span>
                        </div>

                        <!-- Schedule Text -->
                        <div class="col p-0 ms-2">
                            <p class="card-text text-reg text-12 mb-0">
                                <?php echo isset($row['courseSchedule']) ? nl2br($row['courseSchedule']) : 'No schedule yet'; ?>
                            </p>
                        </div>
                    </div>
                    <div class="dropdown position-absolute bottom-0 end-0 m-2">
                        <button class="btn btn-sm" type="button"
                            style="background-color:transparent!important; border:0px;transform: none !important; box-shadow: none !important"
                            id="dropdownMenuButton<?php echo $row['courseID']; ?>"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu"
                            aria-labelledby="dropdownMenuButton<?php echo $row['courseID']; ?>">
                            <?php if ($isActive == '1') { ?>
                                <li>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="courseID"
                                            value="<?php echo $row['courseID']; ?>">
                                        <button type="submit" name="markArchived"
                                            class="dropdown-item text-reg text-14">
                                            Mark as Archived
                                        </button>
                                    </form>
                                </li>
                            <?php } else { ?>
                                <li>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="courseID"
                                            value="<?php echo $row['courseID']; ?>">
                                        <button type="submit" name="markUnarchived"
                                            class="dropdown-item text-reg text-14">
                                            Unarchive
                                        </button>
                                    </form>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
<?php } else { ?>
    <p class="text-center text-reg mt-4">No courses found.</p>
<?php } ?>