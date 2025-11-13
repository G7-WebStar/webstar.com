<?php $activePage = 'profCourses'; ?>
<?php
include('../shared/assets/database/connect.php');
include("../shared/assets/processes/prof-session-process.php");

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

$course = "SELECT 
              userinfo.userInfoID,
              userinfo.userID,
              userinfo.profilePicture,
              userinfo.firstName,
              userinfo.lastName,
              courses.courseID,
              courses.courseCode,
              courses.courseTitle,
              courses.courseImage
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

            <!-- Sidebar (only shows on mobile) -->
            <?php include 'shared/components/sidebar-for-mobile.php'; ?>

            <!-- Sidebar Column (fixed on desktop) -->
            <?php include 'shared/components/sidebar-for-desktop.php'; ?>

            <!-- Main Container Column-->
            <div class="col main-container m-0 p-0 mx-0 mx-md-2 p-0 p-md-4 overflow-y-auto">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">

                    <!-- Navbar for mobile -->
                    <?php include 'shared/components/navbar-for-mobile.php'; ?>

                    <div class="container-fluid py-3 overflow-y-auto row-padding-top"
                        style="padding-bottom: 100px !important;">
                        <div class="row m-4 justify-content-center">
                            <div class="row header-section align-items-center ">
                                <div class="col-12 col-md-auto text-center text-md-start position-relative">
                                    <h1 class="text-sbold text-25 my-2 me-0 me-md-3" style="color: var(--black);">My
                                        Courses
                                    </h1>
                                    <!-- Filter Icon (mobile only) -->
                                    <span id="filterToggle"
                                        class="position-absolute end-0 top-50 translate-middle-y d-md-none px-2"
                                        role="button" tabindex="0" aria-label="Show filters"
                                        style="cursor: pointer; user-select: nones; ">
                                        <span class="material-symbols-rounded"
                                            style="font-size: 30px; color: var(--black);">
                                            tune
                                        </span>
                                    </span>
                                </div>

                                <!-- Dropdowns -->
                                <div class="col-12 col-md-auto d-flex flex-wrap justify-content-center justify-content-md-start gap-3 mt-2 mt-md-0 d-none d-md-flex"
                                    style="row-gap: 0!important;" id="mobileFilters">
                                    <div
                                        class="col-12 col-lg-6 px-0 px-xl-auto me-2 my-1 d-flex justify-content-center justify-content-md-start">
                                        <div class="search-container d-flex me-0">
                                            <form method="GET" class="form-control bg-transparent border-0 p-0">
                                                <input type="text" placeholder="Search" name="search"
                                                    value="<?php echo $search ?>"
                                                    class="form-control py-1 text-reg text-14">
                                                <button type="submit" class="btn-outline-secondary">
                                                    <i class="bi bi-search me-2"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div
                                        class="col-12 col-sm-3 mt-2  mt-md-0 ms-0 ms-md-3 justify-content-center justify-content-lg-end justify-content-xl-start align-items-center px-0 px-xl-auto d-flex d-lg-flex">
                                        <div class="d-flex align-items-center flex-nowrap my-1">
                                            <span class="dropdown-label me-2 text-reg">Status</span>
                                            <button class="btn dropdown-toggle dropdown-custom" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <span class="text-reg text-14">Active</span>
                                            </button>
                                            <ul class="dropdown-menu text-reg text-14">
                                                <li><a class="dropdown-item text-reg text-14" href="#">Active</a>
                                                </li>
                                                <li><a class="dropdown-item text-reg text-14" href="#">Archived</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- Start of Cards Section -->
                            <div class="row my-2 mx-3 mx-md-0 mt-3">
                                <!-- Card -->
                                <?php
                                if (mysqli_num_rows($selectCourseResult) > 0 && !$noResult) {
                                    while ($courses = mysqli_fetch_assoc($selectCourseResult)) {
                                        ?>
                                        <div class="col-12 col-md-6 col-lg-4 col-xl-3 mt-2 m-0 p-0 pe-0 pe-md-2">
                                            <div class="card border border-black rounded-4 d-flex flex-column h-100">
                                                <a href="course-info.php?courseID=<?php echo $courses['courseID']; ?>">
                                                    <img src="shared/assets/img/course-images/<?php echo $courses['courseImage']; ?>"
                                                        class="card-img-top object-fit-cover rounded-top-4 course-image"
                                                        alt="..." style="background-color: #FDDF94; height: 150px;">
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
                                                                <?php echo isset($courses['courseSchedule']) ? nl2br($courses['courseSchedule']) : 'No schedule yet'; ?>
                                                            </p>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                } else if ($noResult) { ?>
                                        <div class="text-sbold h1 text-center mt-5">No Result.</div>
                                    <?php
                                } else {
                                    ?>
                                        <script>
                                            window.location.href = "course-join.php";
                                        </script>
                                    <?php
                                }
                                ?>
                            </div>
                            <!-- End of Cards Section -->
                        </div>

                    </div>
                </div>
            </div>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    const filterToggle = document.getElementById("filterToggle");
                    const mobileFilters = document.getElementById("mobileFilters");
                    const icon = filterToggle.querySelector(".material-symbols-rounded");

                    // Unique key per page
                    const storageKey = "filtersVisible_" + "<?php echo $activePage; ?>";

                    // Restore previous state
                    if (localStorage.getItem(storageKey) === "true") {
                        mobileFilters.classList.remove("d-none");
                        filterToggle.classList.add("active");
                        icon.textContent = "close";
                    }

                    filterToggle.addEventListener("click", () => {
                        const isVisible = !mobileFilters.classList.contains("d-none");

                        // Toggle panel
                        mobileFilters.classList.toggle("d-none");

                        // Toggle icon
                        filterToggle.classList.toggle("active");
                        icon.textContent = filterToggle.classList.contains("active") ? "close" : "tune";

                        // Save state
                        localStorage.setItem(storageKey, !isVisible);
                    });
                });
            </script>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>