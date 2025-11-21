<?php
$activePage = 'course';
include('shared/assets/database/connect.php');
include("shared/assets/processes/session-process.php");
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

$noResult = false;
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
LEFT JOIN courseschedule
    ON courses.courseID = courseschedule.courseID
WHERE enrollments.userID = '$userID' AND isActive = '$isActive'
GROUP BY courses.courseID
";




$baseCourseQuery = $selectCourseQuery;

if ((isset($_GET['search'])) && ($_GET['search'] !== '')) {
    $search = $_GET['search'];

    $searchCourseQuery = $selectCourseQuery .= " AND (courses.courseCode LIKE '%$search%' OR courses.courseTitle LIKE '%$search%')";
    $selectCourseResult = executeQuery($searchCourseQuery);

    if (mysqli_num_rows($selectCourseResult) == 0) {
        $selectCourseResult = executeQuery($baseCourseQuery);
        $noResult = true;
    }
} else {
    $search = '';
    $selectCourseResult = executeQuery($baseCourseQuery);
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | My Courses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="shared/assets/css/inbox.css">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="shared/assets/img/webstar-icon.png">
    <link rel="stylesheet" href="shared/assets/css/course.css">

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
                        <div class="row header-section align-items-center ">
                            <div class="col-12 col-md-auto text-center text-md-start position-relative">
                                <h1 class="text-sbold text-25 my-2 me-0 me-md-3" style="color: var(--black);">My Courses
                                </h1>
                                <!-- Filter Icon (mobile only) -->
                                <span id="filterToggle"
                                    class="position-absolute end-0 top-50 translate-middle-y d-md-none px-2"
                                    role="button" tabindex="0" aria-label="Show filters"
                                    style="cursor: pointer; user-select: none; ">
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
                                        <input type="text" placeholder="Search" name="search"
                                            value="<?php echo $search ?>"
                                            class="form-control py-1 text-reg text-14">
                                        <button type="button" class="btn-outline-secondary">
                                            <i class="bi bi-search me-2"></i>
                                        </button>
                                    </div>
                                </div>
                                <div
                                    class="col-12 col-sm-3 mt-2 mt-md-0 ms-0 ms-md-3 justify-content-center justify-content-xl-start align-items-center px-0 px-xl-auto d-flex d-lg-flex">
                                    <div class="d-flex align-items-center flex-nowrap my-1">
                                        <span class="dropdown-label me-2 text-reg">Status</span>
                                        <div class="custom-dropdown">
                                            <button class="dropdown-btn text-reg text-14">Active</button>
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
                            <div
                                class="col-12 col-md-auto text-center d-flex d-md-block justify-content-center justify-content-md-end mt-1 mt-md-0">
                                <button type="button"
                                    class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14 my-1 d-flex align-items-center gap-2"
                                    style="background-color: var(--primaryColor); border: 1px solid var(--black); color: var(--black);"
                                    data-bs-toggle="modal" data-bs-target="#enrollCourseModal">
                                    <span>+ Join Course</span>
                                </button>
                            </div>
                        </div>

                        <!-- Start of Cards Section -->
                        <div class="row my-2 mx-3 mx-md-0 mt-3" id="courseContainer">
                            <!-- Card -->
                            <?php
                            if (mysqli_num_rows($selectCourseResult) > 0 && !$noResult) {
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
                                <div class="text-reg h1 text-center mt-5">No Result.</div>
                            <?php
                            }
                            ?>
                        </div>
                        <!-- End of Cards Section -->
                    </div>
                </div>
            </div>

            <!-- Enroll Course Modal -->
            <div class="modal fade" id="enrollCourseModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered py-4" style="max-width: 500px;">
                    <div class="modal-content">
                        <div class="modal-header border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="modal-title enroll-modal-title mb-0 text-sbold">Enter access code</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <form id="enrollForm">
                            <div class="modal-body p-4">
                                <div class="mx-auto" style="width: 100%; max-width: 420px;">
                                    <p class="text-med enroll-modal-description text-start mt-3 mb-2">
                                        Enter the access code provided by your professor.
                                    </p>
                                    <div class="d-flex justify-content-center mb-3">
                                        <input type="text" name="access_code"
                                            class="input-style form-control rounded-4 border-blue"
                                            id="accessCodeInput" placeholder="0 0 0 0 0 0" required
                                            inputmode="text" pattern="^[A-Z0-9]{6}$" maxlength="6"
                                            oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0,6);">
                                    </div>
                                </div>

                                <div id="alertContainer" style="display: none;" class="mb-3">
                                    <div class="alert alert-danger d-flex align-items-center mb-0" role="alert" id="alertStyle">
                                        <i class="bi bi-exclamation-triangle-fill me-2" id="exclamation"></i>
                                        <div class="text-reg text-14" id="alertMessage"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer border-top">
                                <button type="submit"
                                    class="btn btn-sm px-3 py-1 rounded-pill text-sbold text-md-14 d-inline-flex align-items-center justify-content-center criteria-add-btn"
                                    style="background-color: var(--primaryColor); border: 1px solid var(--black);">
                                    Enroll
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

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

            <script>
                document.addEventListener("DOMContentLoaded", function() {
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

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const input = document.getElementById('accessCodeInput');
                    const form = document.getElementById('enrollForm');
                    const alertContainer = document.getElementById('alertContainer');
                    const alertStyle = document.getElementById('alertStyle');
                    const exclamation = document.getElementById('exclamation');
                    const alertMessage = document.getElementById('alertMessage');
                    const modal = document.getElementById('enrollCourseModal');

                    // Hide alert when typing
                    input.addEventListener('input', function() {
                        alertContainer.style.display = 'none';
                    });

                    // Handle form submission via AJAX
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();

                        const code = input.value.trim().toUpperCase().replace(/[^A-Z0-9]/g, '');

                        if (code.length !== 6) {
                            alertMessage.textContent = 'Please enter a complete 6-character access code.';
                            alertContainer.style.display = 'block';
                            input.focus();
                            return;
                        }

                        // Send AJAX request to PHP handler
                        fetch('shared/assets/processes/enroll-course.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                },
                                body: 'access_code=' + encodeURIComponent(code)
                            })
                            .then(response => response.json())
                            .then(data => {
                                alertMessage.textContent = data.message;
                                alertContainer.style.display = 'block';

                                if (data.success) {
                                    alertStyle.classList.remove('alert-danger');
                                    alertStyle.classList.add('alert-success');
                                    exclamation.remove();
                                    // Reload page after short delay to show updated courses
                                    setTimeout(() => location.reload(), 1000);
                                } else {
                                    alertContainer.classList.remove('alert-success');
                                    alertContainer.classList.add('alert-danger');
                                }
                            })
                            .catch(err => {
                                alertMessage.textContent = 'An error occurred. Please try again.';
                                alertContainer.style.display = 'block';
                                alertContainer.classList.remove('alert-success');
                                alertContainer.classList.add('alert-danger');
                            });
                    });

                    // Reset modal when closed
                    modal.addEventListener('hidden.bs.modal', function() {
                        input.value = '';
                        alertContainer.style.display = 'none';
                        alertContainer.classList.remove('alert-success', 'alert-danger');
                    });

                    // Focus input when modal opens
                    modal.addEventListener('shown.bs.modal', function() {
                        input.focus();
                    });
                });
            </script>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const searchInput = document.querySelector('input[name="search"]');
                    const courseContainer = document.getElementById('courseContainer');

                    if (searchInput) {
                        searchInput.addEventListener('input', function() {
                            const searchTerm = searchInput.value.trim();

                            fetch('shared/assets/processes/search-course.php?search=' + encodeURIComponent(searchTerm) + "<?php echo (isset($_GET['status'])) ? "&status=" . $filter : ''; ?>")
                                .then(response => response.text())
                                .then(html => {
                                    courseContainer.innerHTML = html;
                                })
                                .catch(err => console.error('Fetch error:', err));
                        });
                    }
                });
            </script>

</body>

</html>