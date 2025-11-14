<?php
include('../database/connect.php');
include("session-process.php");

$noResult = false;
$search = isset($_GET['search']) ? $_GET['search'] : '';

$selectCourseQuery = "
SELECT 
    courses.*, 
    profInfo.firstName AS profFirstName,
    profInfo.middleName AS profMiddleName,
    profInfo.lastName AS profLastName,
    profInfo.profilePicture AS profPFP,
    GROUP_CONCAT(
        CONCAT(
            courseSchedule.day, ' ', 
            DATE_FORMAT(courseSchedule.startTime, '%h:%i %p'), '-', 
            DATE_FORMAT(courseSchedule.endTime, '%h:%i %p')
        ) 
        ORDER BY FIELD(courseSchedule.day, 'Mon','Tue','Wed','Thu','Fri','Sat','Sun'), courseSchedule.startTime
        SEPARATOR '\n'
    ) AS courseSchedule
FROM courses
INNER JOIN userinfo AS profInfo
    ON courses.userID = profInfo.userID
INNER JOIN enrollments
    ON courses.courseID = enrollments.courseID
LEFT JOIN courseSchedule
    ON courses.courseID = courseSchedule.courseID
WHERE enrollments.userID = '$userID'
";

if ($search !== '') {
    $selectCourseQuery .= " AND (courses.courseCode LIKE '%$search%' OR courses.courseTitle LIKE '%$search%')";
}

$selectCourseQuery .= " GROUP BY courses.courseID";

$selectCourseResult = executeQuery($selectCourseQuery);

if (mysqli_num_rows($selectCourseResult) == 0) {
    $noResult = true;
}

// Return HTML for the cards
if (!$noResult) {
    while ($courses = mysqli_fetch_assoc($selectCourseResult)) {
?>
        <div class="col-12 col-md-6 col-lg-4 col-xl-3 mt-2 m-0 p-0 pe-0 pe-md-2">
            <div class="card border border-black rounded-4 d-flex flex-column h-100">
                <a href="course-info.php?courseID=<?php echo $courses['courseID']; ?>">
                    <img src="shared/assets/img/course-images/<?php echo $courses['courseImage']; ?>"
                        class="card-img-top object-fit-cover rounded-top-4 course-image" alt="..."
                        style="background-color: #FDDF94; height: 150px;">
                </a>
                <div class="card-body border-top border-black">
                    <div class="row lh-1 mb-2">
                        <a href="course-info.php?courseID=<?php echo $courses['courseID']; ?>"
                            class="text-decoration-none text-black">
                            <p class="card-text text-sbold text-18 m-0">
                                <?php echo $courses['courseCode']; ?>
                            </p>
                        </a>
                        <p class="card-text text-med text-14 mb-2">
                            <?php echo $courses['courseTitle']; ?>
                        </p>
                    </div>
                    <div class="row mb-2">
                        <div class="d-flex align-items-center">
                            <div class="me-2">
                                <img src="shared/assets/pfp-uploads/<?php echo $courses['profPFP']; ?>"
                                    alt="" width="24" height="24" class="rounded-circle">
                            </div>
                            <div class="lh-sm">
                                <p class="card-text text-med text-12 m-0">
                                    <?php echo $courses['profFirstName'] . " " . $courses['profMiddleName'] . " " . $courses['profLastName']; ?>
                                </p>
                                <p class="card-text text-med text-12 mb-0">Professor</p>
                            </div>
                        </div>
                    </div>
                    <div class="row ps-2 pe-3 mb-2 align-items-center">
                        <div class="col-auto d-flex justify-content-center align-items-start p-0 ms-1">
                            <span class="material-symbols-rounded" style="font-size:24px; color:#2c2c2c;">
                                calendar_today
                            </span>
                        </div>
                        <div class="col p-0 ms-2">
                            <p class="card-text text-reg text-12 mb-0">
                                <?php echo isset($courses['courseSchedule']) ? nl2br($courses['courseSchedule']) : 'No schedule yet'; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php
    }
} else {
    echo '<div class="text-reg h1 text-center mt-5">No Result.</div>';
}
?>