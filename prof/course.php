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
    <link rel="stylesheet" href="../shared/assets/css/inbox.css">
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

                    <div class="container-fluid py-3 overflow-y-auto row-padding-top">

                        <div class="row header-section align-items-center">
                            <!-- Title -->
                            <div class="col-12 col-md-auto text-center text-md-start position-relative mb-2 mb-md-0">
                                <h1 class="text-sbold text-25 my-2 me-0 me-md-3" style="color: var(--black);">My Courses
                                </h1>
                                <!-- Filter Icon (mobile only) -->
                                <span id="filterToggle"
                                    class="position-absolute end-0 top-50 translate-middle-y d-md-none px-2"
                                    role="button" tabindex="0" aria-label="Show filters"
                                    style="cursor: pointer; user-select: none;">
                                    <span class="material-symbols-rounded"
                                        style="font-size: 30px; color: var(--black);">
                                        tune
                                    </span>
                                </span>
                            </div>

                            <!-- Header Tools: Search + Dropdown + Create Course -->
                            <div class="col-12 col-md-auto justify-content-start align-items-center gap-3 mt-2 mt-md-0 d-none d-md-flex"
                                id="mobileFilters">

                                <!-- Search -->
                                <div class="d-flex justify-content-center justify-content-md-start">
                                    <div class="search-container d-flex search-container-prof">
                                        <form method="GET" class="form-control bg-transparent border-0 p-0">
                                            <input type="text" placeholder="Search" name="search"
                                                value="<?php echo htmlspecialchars($search); ?>"
                                                class="form-control py-1 text-reg text-14" id="searchInput">
                                            <button type="submit" class="btn-outline-secondary">
                                                <i class="bi bi-search me-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Status Dropdown -->
                                <div class="col-auto mobile-dropdown p-0 mt-2 mt-md-0">
                                    <div class="d-flex align-items-center flex-nowrap my-2">
                                        <span class="dropdown-label me-2 text-reg">Status</span>
                                        <div class="custom-dropdown">
                                            <button class="dropdown-btn text-reg text-14">
                                                <?php echo ($filter == 'archived') ? 'Archived' : 'Active'; ?>
                                            </button>
                                            <ul class="dropdown-list text-reg text-14">
                                                <li data-value="Active" onclick="window.location.href='?status=active'">
                                                    Active</li>
                                                <li data-value="Archived"
                                                    onclick="window.location.href='?status=archived'">Archived</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row my-2 mx-3 mx-md-0 mt-3">
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
                                <p class="text-center text-white mt-4">No courses found.</p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const searchInput = document.getElementById("searchInput");
            // Get the outer column that holds each course
            const courseCols = document.querySelectorAll(".col-12.col-md-6.col-lg-4.col-xl-3");

            searchInput.addEventListener("keyup", () => {
                const searchValue = searchInput.value.toLowerCase();

                courseCols.forEach(col => {
                    const code = col.querySelector(".course-code")?.textContent.toLowerCase() || "";
                    const title = col.querySelector(".course-title")?.textContent.toLowerCase() || "";

                    if (code.includes(searchValue) || title.includes(searchValue)) {
                        col.style.display = "";
                    } else {
                        col.style.display = "none";
                    }
                });
            });
        });

    </script>

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

    <!-- Dropdown js -->
    <script>
        document.querySelectorAll('.custom-dropdown').forEach(dropdown => {
            const btn = dropdown.querySelector('.dropdown-btn');
            const list = dropdown.querySelector('.dropdown-list');

            btn.addEventListener('click', () => {
                list.style.display = list.style.display === 'block' ? 'none' : 'block';
            });

            list.querySelectorAll('li').forEach(item => {
                item.addEventListener('click', () => {
                    btn.textContent = item.dataset.value;
                    list.style.display = 'none';
                });
            });

            // Close dropdown if clicked outside
            document.addEventListener('click', (e) => {
                if (!dropdown.contains(e.target)) {
                    list.style.display = 'none';
                }
            });
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>