<?php $activePage = 'profCourses'; ?>
<?php
include('../shared/assets/database/connect.php');
include("../shared/assets/processes/prof-session-process.php");

if (isset($_POST['markUnarchived'])) {
    $courseID = $_POST['courseID'];
    $update = "UPDATE courses SET isActive = 'Yes' WHERE courseID = '$courseID'";
    executeQuery($update);
}

if (isset($_POST['markArchived'])) {
    $courseID = $_POST['courseID'];
    $update = "UPDATE courses SET isActive = 'No' WHERE courseID = '$courseID'";
    executeQuery($update);
}

$filter = isset($_GET['status']) ? $_GET['status'] : 'active';
$isActive = ($filter == 'archived') ? 'No' : 'Yes';

$course = "SELECT 
              userinfo.userInfoID,
              userinfo.userID,
              userinfo.profilePicture,
              userinfo.firstName,
              userinfo.lastName,
              courses.courseID,
              courses.courseCode,
              courses.courseTitle,
              courses.courseImage,
              SUBSTRING_INDEX(courses.schedule, ' ', 1)  AS courseDays,
              SUBSTRING_INDEX(courses.schedule, ' ', -1) AS courseTime
            FROM userinfo
            INNER JOIN courses ON userinfo.userID = courses.userID
            WHERE courses.userID = '$userID'
              AND courses.isActive = '$isActive'";

$courses = executeQuery($course);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Assign Task</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/course.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../shared/assets/img/webstar-icon.png">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />

</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-0 p-md-3"
        style="background-color: var(--black);">

        <div class="row w-100">
            <!-- Sidebar (mobile) -->
            <?php include '../shared/components/prof-sidebar-for-mobile.php'; ?>

            <!-- Sidebar (desktop) -->
            <?php include '../shared/components/prof-sidebar-for-desktop.php'; ?>

            <!-- Main Container -->
            <div class="col main-container m-0 p-0 mx-0 mx-md-2 p-md-4 overflow-y-auto">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">

                    <!-- Navbar (mobile) -->
                    <?php include '../shared/components/prof-navbar-for-mobile.php'; ?>

                    <div class="container-fluid py-3 overflow-y-auto">
                        <div class="row m-4 justify-content-center">

                            <div class="row header-section align-items-center justify-content-center">
                                <!-- Header Section -->
                                <div class="col-12 col-xl-8 mb-3 mb-xl-0">
                                    <div class="row g-3">
                                        <div class="col-12 col-lg-3 px-0">
                                            <p
                                                class="text-sbold mb-0 lh-md dynamic-text text-start text-sm-center text-md-start">
                                                My Courses</p>
                                        </div>
                                        <div class="col-12 col-lg-6 px-0 px-xl-auto">
                                            <div class="search-container d-flex mx-sm-auto">
                                                <form method="GET" class="form-control bg-transparent border-0">
                                                    <input type="text" placeholder="Search" name="search"
                                                        class="form-control py-1 text-reg text-14" id="searchInput">
                                                    <button type="submit" class="btn-outline-secondary">
                                                        <i class="bi bi-search me-2"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        <div
                                            class="col-12 col-sm-3 justify-content-lg-end justify-content-xl-start align-items-center px-0 px-xl-auto d-flex d-sm-none d-lg-flex">
                                            <div class="d-flex align-items-center flex-nowrap">
                                                <span class="dropdown-label me-2 text-reg">Status:</span>
                                                <button class="btn dropdown-toggle dropdown-custom" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <span class="text-reg text-14">
                                                        <?php echo ($filter == 'archived') ? 'Archived' : 'Active'; ?>
                                                    </span>
                                                </button>
                                                <ul class="dropdown-menu text-reg text-14">
                                                    <li><a class="dropdown-item text-reg text-14"
                                                            href="?status=active">Active</a></li>
                                                    <li><a class="dropdown-item text-reg text-14"
                                                            href="?status=archived">Archived</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-xl-4">
                                    <div class="row g-3">
                                        <div
                                            class="col-12 d-flex justify-content-end align-items-center px-0 px-xl-auto">
                                            <div
                                                class="col-6 d-none d-sm-flex d-lg-none justify-content-center justify-content-md-start align-items-center flex-nowrap">
                                                <span class="dropdown-label me-2 text-reg">Status:</span>
                                                <button class="btn dropdown-toggle dropdown-custom" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <span class="text-reg text-14">
                                                        <?php echo ($filter == 'archived') ? 'Archived' : 'Active'; ?>
                                                    </span>
                                                </button>
                                                <ul class="dropdown-menu text-reg text-14">
                                                    <li><a class="dropdown-item text-reg text-14"
                                                            href="?status=active">Active</a></li>
                                                    <li><a class="dropdown-item text-reg text-14"
                                                            href="?status=archived">Archived</a></li>
                                                </ul>
                                            </div>
                                            <!-- Create Course Button -->
                                            <div
                                                class="col-12 col-sm-6 col-lg-12 d-flex justify-content-end justify-content-sm-center justify-content-md-end align-items-center px-0">
                                                <button
                                                    class="add-course-btn btn btn-primary px-3 py-1 rounded-pill text-reg text-md-14">
                                                    <a href="create-course.php"
                                                        style="text-decoration: none; color: black;">+ Create Course</a>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- End Header Section -->

                                <!-- Courses Section -->
                                <div class="row px-0">
                                    <?php if ($courses && mysqli_num_rows($courses) > 0) { ?>
                                        <?php while ($row = mysqli_fetch_assoc($courses)) { ?>
                                            <div class="col-12 col-lg-6 col-xl-4 mt-4 course-card">
                                                <div class="card border border-black rounded-4">
                                                    <a href="prof-courses-info.php?courseID=<?php echo $row['courseID']; ?>">
                                                        <img src="../shared/assets/img/home/<?php echo $row['courseImage']; ?>"
                                                            class="card-img-top object-fit-cover rounded-top-4" alt="..."
                                                            style="background-color: var(--primaryColor); height: 190px;">
                                                    </a>
                                                    <div class="card-body border-top border-black">
                                                        <div class="row lh-1 mb-2">
                                                            <a href="prof-courses-info.php?courseID=<?php echo $row['courseID']; ?>"
                                                                class="text-decoration-none text-black">
                                                                <p class="card-text text-bold text-18 m-0 course-code">
                                                                    <?php echo $row['courseCode']; ?>
                                                                </p>
                                                            </a>
                                                            <p class="card-text text-reg text-14 mb-1 course-title">
                                                                <?php echo $row['courseTitle']; ?>
                                                            </p>
                                                        </div>

                                                        <div class="row px-3 mb-1">
                                                            <div
                                                                class="col-1 d-flex justify-content-center align-items-center m-0 p-0">
                                                                <img src="../shared/assets/pfp-uploads/<?php echo $row['profilePicture']; ?>"
                                                                    alt="" width="32" height="32" class="rounded-circle">
                                                            </div>
                                                            <div class="col-11 my-0 lh-sm">
                                                                <p class="card-text text-bold text-14 mb-0 mt-2">
                                                                    <?php echo $row['firstName'] . ' ' . $row['lastName']; ?>
                                                                </p>
                                                                <p class="card-text text-med text-12 mb-2">Professor</p>
                                                            </div>
                                                        </div>
                                                        <div class="row px-3 mb-2">
                                                            <div
                                                                class="col-1 d-flex justify-content-center align-items-center m-0 p-0">
                                                                <span
                                                                    class="material-symbols-outlined black">calendar_today</span>
                                                            </div>
                                                            <div class="col-11 d-flex align-items-center mt-1 lh-sm">
                                                                <p class="card-text text-reg text-14 m-0">
                                                                    <span
                                                                        class="text-med"><?php echo $row['courseDays']; ?></span>
                                                                    <?php echo $row['courseTime']; ?>
                                                                </p>
                                                            </div>
                                                        </div>

                                                        <div class="dropdown position-absolute bottom-0 end-0 m-2">
                                                            <button class="btn btn-light btn-sm" type="button"
                                                                id="dropdownMenuButton<?php echo $row['courseID']; ?>"
                                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="bi bi-three-dots-vertical"></i>
                                                            </button>
                                                            <ul class="dropdown-menu"
                                                                aria-labelledby="dropdownMenuButton<?php echo $row['courseID']; ?>">
                                                                <?php if ($isActive == 'Yes') { ?>
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
                                        <p class="text-center text-white mt-4">No courses found.</p>
                                    <?php } ?>
                                </div>
                                <!-- End Courses Section -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const searchInput = document.getElementById("searchInput");
            const cards = document.querySelectorAll(".course-card");

            searchInput.addEventListener("keyup", () => {
                const searchValue = searchInput.value.toLowerCase();

                cards.forEach(card => {
                    const code = card.querySelector(".course-code").textContent.toLowerCase();
                    const title = card.querySelector(".course-title").textContent.toLowerCase();

                    if (code.includes(searchValue) || title.includes(searchValue)) {
                        card.style.display = "block";
                    } else {
                        card.style.display = "none";
                    }
                });
            });
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>