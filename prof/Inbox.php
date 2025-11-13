<?php $activePage = 'inbox';
include('../shared/assets/database/connect.php');
include("../shared/assets/processes/prof-session-process.php");

$selectEnrolledQuery = "SELECT DISTINCT
courses.courseCode
FROM courses
WHERE courses.userID = '$userID'
ORDER BY courses.courseCode ASC
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
    <title>Webstar | Prof Inbox</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css" />
    <link rel="stylesheet" href="../shared/assets/css/sidebar-and-container-styles.css" />
    <link rel="stylesheet" href="../shared/assets/css/inbox.css" />
    <link rel="stylesheet" href="../shared/assets/css/sidebar-and-container-styles.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link rel="icon" type="image/png" href="../shared/assets/img/webstar-icon.png" />

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />

    <style>
        @media (min-width: 992px) {
            .responsive-circle {
                width: 45.52px !important;
                height: 45.52px !important;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-0 p-md-3"
        style="background-color: var(--black);">
        <div class="row w-100">
            <?php include '../shared/components/prof-sidebar-for-mobile.php'; ?>
            <?php include '../shared/components/prof-sidebar-for-desktop.php'; ?>

            <div class="col main-container m-0 p-0 mx-0 mx-md-2 p-md-4 overflow-y-auto">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">
                    <?php include '../shared/components/prof-navbar-for-mobile.php'; ?>

                    <div class="container-fluid py-3 row-padding-top">
                        <div class="row">
                            <div class="col-12">

                                <!-- Header Section -->
                                <div class="row align-items-center mb-3 text-center text-lg-start">
                                    <!-- Title -->
                                    <div class="col-12 col-lg-auto mb-3 mb-lg-0">
                                        <h1 class="text-sbold text-25 mb-0 mt-2" style="color: var(--black);">My Inbox
                                        </h1>
                                    </div>

                                    <!-- Dropdowns-->
                                     
                                <!-- Sort By -->
                                <div class="col-auto mobile-dropdown">
                                    <div class="d-flex align-items-center flex-nowrap mt-2">
                                        <span class="dropdown-label me-2 text-reg">Sort by</span>
                                        <div class="custom-dropdown" data-dropdown="sort">
                                            <button class="dropdown-btn text-reg text-14" data-selected-sort="Newest">Newest</button>
                                            <ul class="dropdown-list text-reg text-14">
                                                <li data-value="Newest" data-sort="desc">Newest</li>
                                                <li data-value="Oldest" data-sort="asc">Oldest</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Course -->
                                <div class="col-auto mobile-dropdown">
                                    <div class="d-flex align-items-center flex-nowrap mt-2">
                                        <span class="dropdown-label me-2 text-reg">Courses</span>
                                        <div class="custom-dropdown" data-dropdown="course">
                                            <button class="dropdown-btn text-reg text-14" data-selected-course="All">All</button>
                                            <ul class="dropdown-list text-reg text-14">
                                                <li data-value="All" data-course="All">All</li>
                                                <?php
                                                if ($selectEnrolledResult && mysqli_num_rows($selectEnrolledResult) > 0) {
                                                    mysqli_data_seek($selectEnrolledResult, 0);
                                                    while ($course = mysqli_fetch_assoc($selectEnrolledResult)) {
                                                        $courseCode = $course['courseCode'];
                                                        ?>
                                                        <li data-value="<?php echo $courseCode; ?>" data-course="<?php echo $courseCode; ?>">
                                                            <?php echo $courseCode; ?>
                                                        </li>
                                                        <?php
                                                    }
                                                    mysqli_data_seek($selectEnrolledResult, 0);
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                    <!-- Message Content -->
                                    <div class="message-container mt-4 mt-lg-4 pb-4">
                                        <?php
                                        if (mysqli_num_rows($selectInboxResult) > 0) {
                                            while ($inbox = mysqli_fetch_assoc($selectInboxResult)) {
                                                $timestamp = strtotime($inbox['inboxCreatedAt']);
                                                $displayDate = $timestamp ? date("F j, Y g:ia", $timestamp) : '';
                                                $courseCode = $inbox['courseCode'];
                                                $messageText = trim($inbox['messageText'] . ' ' . $inbox['assessmentTitle']);
                                                ?>
                                                <div class="card mb-1 me-3 w-100 mt-2 inbox-card"
                                                    data-timestamp="<?php echo $timestamp ?: 0; ?>"
                                                    data-course="<?php echo $courseCode; ?>"
                                                    style="max-width: 1101px; border: 1px solid var(--black); border-radius: 15px; background-color: var(--pureWhite); opacity: 1;">
                                                    <div class="card-body py-2 px-4 px-md-3">
                                                        <div class="row align-items-center">
                                                            <!-- Message Text -->
                                                            <div class="col d-flex flex-column text-start mt-2 mb-2">
                                                                <p class="mb-2 text-sbold text-17 message-text"
                                                                    style="color: var(--black); line-height: 100%;">
                                                                    <?php echo $messageText; ?>
                                                                </p>
                                                                <small class="text-reg text-12"
                                                                    style="color: var(--black); line-height: 100%;">
                                                                    <?php echo $displayDate; ?>
                                                                </small>

                                                                <!-- Course tag on small screen below message text -->
                                                                <div class="d-block d-lg-none mt-2">
                                                                    <span
                                                                        class="text-reg text-12 badge rounded-pill course-badge"
                                                                        style="width: 99px; height: 19px; border-radius: 50px; padding: 4px 10px;">
                                                                        <?php echo $courseCode; ?>
                                                                    </span>
                                                                </div>
                                                            </div>

                                                            <!-- Course tag on large screen right side, vertically centered -->
                                                            <div class="col-auto d-none d-lg-flex align-items-center"
                                                                style="display:flex;align-items:center;">
                                                                <span class="text-reg text-12 badge rounded-pill course-badge"
                                                                    style="width: 99px; height: 19px; border-radius: 50px; padding: 4px 10px;">
                                                                    <?php echo $courseCode; ?>
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
                                                <img src="../shared/assets/img/empty/inbox.png" width="100" class="mb-1">
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
        document.addEventListener('DOMContentLoaded', () => {
            const messageContainer = document.querySelector('.message-container');
            const sortDropdown = document.querySelector('.custom-dropdown[data-dropdown="sort"]');
            const courseDropdown = document.querySelector('.custom-dropdown[data-dropdown="course"]');
            const messageCards = messageContainer ? Array.from(messageContainer.querySelectorAll('.inbox-card')) : [];

            if (!messageContainer || messageCards.length === 0) {
                return;
            }

            let currentSortDirection = 'desc';
            let currentCourseFilter = 'All';

            let filterEmptyState = messageContainer.querySelector('.inbox-filter-empty');
            if (!filterEmptyState) {
                filterEmptyState = document.createElement('div');
                filterEmptyState.className = 'd-flex flex-column justify-content-center align-items-center inbox-empty-state inbox-filter-empty';
                filterEmptyState.style.display = 'none';
                filterEmptyState.innerHTML = `
                    <img src="../shared/assets/img/empty/inbox.png" width="100" class="mb-1" alt="Empty inbox">
                    <div class="text-center text-14 text-reg mt-1">No messages match your filters.</div>
                `;
                messageContainer.appendChild(filterEmptyState);
            }

            const applyFilters = () => {
                const cards = Array.from(messageContainer.querySelectorAll('.inbox-card'));

                cards.sort((a, b) => {
                    const aTime = parseInt(a.dataset.timestamp || '0', 10);
                    const bTime = parseInt(b.dataset.timestamp || '0', 10);
                    return currentSortDirection === 'asc' ? aTime - bTime : bTime - aTime;
                }).forEach(card => messageContainer.appendChild(card));

                let visibleCount = 0;
                cards.forEach(card => {
                    const matchesCourse = currentCourseFilter === 'All' || card.dataset.course === currentCourseFilter;
                    card.style.display = matchesCourse ? '' : 'none';
                    if (matchesCourse) {
                        visibleCount++;
                    }
                });

                messageContainer.appendChild(filterEmptyState);
                filterEmptyState.style.display = visibleCount === 0 ? 'flex' : 'none';
            };

            if (sortDropdown) {
                const sortButton = sortDropdown.querySelector('.dropdown-btn');
                sortDropdown.querySelectorAll('li').forEach(item => {
                    item.addEventListener('click', () => {
                        currentSortDirection = item.dataset.sort || 'desc';
                        if (sortButton) {
                            sortButton.dataset.selectedSort = item.dataset.value || 'Newest';
                        }
                        applyFilters();
                    });
                });
            }

            if (courseDropdown) {
                const courseButton = courseDropdown.querySelector('.dropdown-btn');
                courseDropdown.querySelectorAll('li').forEach(item => {
                    item.addEventListener('click', () => {
                        currentCourseFilter = item.dataset.course || 'All';
                        if (courseButton) {
                            courseButton.dataset.selectedCourse = currentCourseFilter;
                        }
                        applyFilters();
                    });
                });
            }

            applyFilters();
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