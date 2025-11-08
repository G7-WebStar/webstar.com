<?php $activePage = 'inbox';
include('shared/assets/database/connect.php');
include("shared/assets/processes/session-process.php");

$selectEnrolledQuery = "SELECT
courses.courseCode
FROM courses
INNER JOIN enrollments
    ON courses.courseID = enrollments.courseID
WHERE enrollments.userID = '$userID'
";
$selectEnrolledResult = executeQuery($selectEnrolledQuery);

$selectInboxQuery = "SELECT 
inbox.createdAt AS inboxCreatedAt,
inbox.messageText,
courses.courseCode,
assessments.assessmentTitle AS assessmentTitle,
userinfo.profilePicture AS profPFP
FROM inbox
INNER JOIN enrollments
	ON inbox.enrollmentID = enrollments.enrollmentID
INNER JOIN todo
	ON enrollments.userID = todo.userID
    AND enrollments.courseID = (SELECT courseID 
                               FROM assessments 
                               WHERE assessments.assessmentID = todo.assessmentID)
INNER JOIN assessments
	ON todo.assessmentID = assessments.assessmentID
INNER JOIN courses
	ON assessments.courseID = courses.courseID
INNER JOIN userinfo
	ON courses.userID = userinfo.userID
WHERE enrollments.userID = '$userID' AND todo.status = 'Pending'
ORDER BY inbox.inboxID DESC
";
$selectInboxResult = executeQuery($selectInboxQuery);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Webstar | My Inbox</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css" />
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css" />
    <link rel="stylesheet" href="shared/assets/css/inbox.css" />
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link rel="icon" type="image/png" href="shared/assets/img/webstar-icon.png" />

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />

</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-0 p-md-3"
        style="background-color: var(--black);">
        <div class="row w-100">
            <?php include 'shared/components/sidebar-for-mobile.php'; ?>
            <?php include 'shared/components/sidebar-for-desktop.php'; ?>

            <div class="col main-container m-0 p-0 mx-0 mx-md-2 p-md-4 overflow-y-auto">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">
                    <?php include 'shared/components/navbar-for-mobile.php'; ?>

                    <div class="container-fluid py-3 row-padding-top">
                        <div class="row">
                            <div class="col-12">
                                <!-- Header Section -->
                                <div class="row align-items-center mb-3 text-center text-md-start">
                                    <div class="col-12 col-md-auto text-center text-md-start position-relative">
                                        <h1 class="text-sbold text-25 my-2" style="color: var(--black);">My Inbox
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

                                        <!-- Sort By -->
                                        <div class="col-auto mobile-dropdown p-0">
                                            <div class="d-flex align-items-center flex-nowrap my-2">
                                                <span class="dropdown-label me-2 text-reg">Sort by</span>
                                                <div class="custom-dropdown">
                                                    <button class="dropdown-btn text-reg text-14">Newest</button>
                                                    <ul class="dropdown-list text-reg text-14">
                                                        <li data-value="Newest">Newest</li>
                                                        <li data-value="Oldest">Oldest</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Course -->
                                        <div class="col-auto mobile-dropdown p-0">
                                            <div class="d-flex align-items-center flex-nowrap my-2">
                                                <span class="dropdown-label me-2 text-reg">Courses</span>
                                                <div class="custom-dropdown">
                                                    <button class="dropdown-btn text-reg text-14">All</button>
                                                    <ul class="dropdown-list text-reg text-14">
                                                        <li data-value="All">All</li>
                                                        <li data-value="Courses">Courses</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Type -->
                                        <div class="col-auto mobile-dropdown p-0">
                                            <div class="d-flex align-items-center flex-nowrap my-2">
                                                <span class="dropdown-label me-2 text-reg">Type</span>
                                                <div class="custom-dropdown">
                                                    <button class="dropdown-btn text-reg text-14">All</button>
                                                    <ul class="dropdown-list text-reg text-14">
                                                        <li data-value="All">All</li>
                                                        <li data-value="Course Updates">Course Updates</li>
                                                        <li data-value="Badge Updates">Badge Updates</li>
                                                        <li data-value="Leaderboard Updates">Leaderboard Updates
                                                        </li>
                                                        <li data-value="Level Updates">Level Updates</li>
                                                        <li data-value="Submission Updates">Submission Updates</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                    </div>



                                    <!-- Message Content -->
                                    <div class="message-container mt-3 pb-4">
                                        <?php
                                        if (mysqli_num_rows($selectInboxResult) > 0) {
                                            while ($inbox = mysqli_fetch_assoc($selectInboxResult)) {
                                                ?>
                                                <div class="card mb-1 me-3 w-100 mt-2"
                                                    style="max-width: 1101px; border: 1px solid var(--black); border-radius: 15px; background-color: var(--pureWhite); opacity: 1;">
                                                    <div class="card-body py-2 px-4 px-md-3">
                                                        <div class="row align-items-center">
                                                            <!-- Message Text -->
                                                            <div class="col d-flex flex-column text-start mt-2 mb-2">
                                                                <p class="mb-2 text-sbold text-17 message-text"
                                                                    style="color: var(--black); line-height: 100%;">
                                                                    <?php echo $inbox['messageText'] . " " . $inbox['assessmentTitle']; ?>
                                                                </p>
                                                                <small class="text-reg text-12"
                                                                    style="color: var(--black); line-height: 100%;">January
                                                                    12, 2024
                                                                    8:00AM</small>

                                                                <!-- Course tag on small screen below message text -->
                                                                <div class="d-block d-lg-none mt-2">
                                                                    <span
                                                                        class="text-reg text-12 badge rounded-pill course-badge"
                                                                        style="width: 99px; height: 19px; border-radius: 50px; padding: 4px 10px;">
                                                                        <?php echo $inbox['courseCode']; ?>
                                                                    </span>
                                                                </div>
                                                            </div>

                                                            <!-- Course tag on large screen right side, vertically centered -->
                                                            <div class="col-auto d-none d-lg-flex align-items-center"
                                                                style="display:flex;align-items:center;">
                                                                <span class="text-reg text-12 badge rounded-pill course-badge"
                                                                    style="width: 99px; height: 19px; border-radius: 50px; padding: 4px 10px;">
                                                                    <?php echo $inbox['courseCode']; ?>
                                                                </span>
                                                            </div>
                                                        </div>

                                                    </div>

                                                </div>
                                                <?php
                                            }
                                        } else {
                                            ?>
                                            <!-- Empty State -->
                                            <div class="d-flex flex-column justify-content-center align-items-center inbox-empty-state">
                                                <img src="shared/assets/img/empty/inbox.png" width="100" class="mb-1">
                                                <div class="text-center text-14 text-reg mt-1">Your inbox is empty!</div>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    </div>
    </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>


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
</body>

</html>