<?php $activePage = 'inbox';
include('../shared/assets/database/connect.php');
include("../shared/assets/processes/prof-session-process.php");

$selectEnrolledQuery = "SELECT DISTINCT courses.courseCode FROM courses WHERE courses.userID = '$userID' ORDER BY courses.courseCode ASC ";
$selectEnrolledResult = executeQuery($selectEnrolledQuery);

$selectInboxQuery = "
SELECT
    inboxprof.createdAt AS inboxCreatedAt,
    inboxprof.messageText,
    courses.courseCode,
    userinfo.profilePicture AS profPFP
FROM inboxprof
INNER JOIN courses
    ON inboxprof.courseID = courses.courseID
INNER JOIN userinfo
    ON courses.userID = userinfo.userID
WHERE courses.userID = '$userID'
ORDER BY inboxprof.inboxProfID DESC
";
$selectInboxResult = executeQuery($selectInboxQuery);
$inboxCount = mysqli_num_rows($selectInboxResult);

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

            <div class="col main-container m-0 p-0 mx-0 mx-md-2 p-md-4">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">
                    <?php include '../shared/components/prof-navbar-for-mobile.php'; ?>

                    <div class="container-fluid py-3 row-padding-top  h-100  overflow-y-auto">
                        <div class="row">
                            <div class="col-12">

                                <!-- Header Section -->
                                <div class="row align-items-center mb-3 text-center text-md-start">
                                    <div class="col-12 col-md-auto text-center text-md-start position-relative">
                                        <h1 class="text-sbold text-25 mb-0 mt-2" style="color: var(--black);">My Inbox
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

                                    <!-- Dropdowns-->
                                    <div class="col-12 col-md-auto d-flex flex-wrap justify-content-center justify-content-md-start gap-3 mt-2 mt-md-0 d-none d-md-flex"
                                        style="row-gap: 0!important;" id="mobileFilters">
                                        <!-- Sort By -->
                                        <div class="col-auto mobile-dropdown">
                                            <div class="d-flex align-items-center flex-nowrap mt-2">
                                                <span class="dropdown-label me-2 text-reg">Sort by</span>
                                                <div class="custom-dropdown" data-dropdown="sort">
                                                    <button class="dropdown-btn text-reg text-14"
                                                        data-selected-sort="Newest">Newest</button>
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
                                                    <button class="dropdown-btn text-reg text-14"
                                                        data-selected-course="All">All</button>
                                                    <ul class="dropdown-list text-reg text-14">
                                                        <li data-value="All" data-course="All">All</li>
                                                        <?php
                                                        if ($selectEnrolledResult && mysqli_num_rows($selectEnrolledResult) > 0) {
                                                            mysqli_data_seek($selectEnrolledResult, 0);
                                                            while ($course = mysqli_fetch_assoc($selectEnrolledResult)) {
                                                                $courseCode = $course['courseCode'];
                                                                ?>
                                                                <li data-value="<?php echo $courseCode; ?>"
                                                                    data-course="<?php echo $courseCode; ?>">
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
                                    </div>

                                    <!-- Message Content -->
                                    <?php if ($inboxCount > 0): ?>
                                        <div class="message-container mt-4 mt-lg-4 pb-4">
                                            <?php
                                            while ($inbox = mysqli_fetch_assoc($selectInboxResult)) {
                                                $timestamp = strtotime($inbox['inboxCreatedAt']);
                                                $displayDate = $timestamp ? date("F j, Y g:ia", $timestamp) : '';
                                                $courseCode = $inbox['courseCode'];
                                                $messageText = trim($inbox['messageText']);
                                                ?>
                                                <div class="card mb-1 w-100 mt-2 inbox-card"
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
                                            ?>
                                            <div id="js-empty-state"
                                                class="d-none d-flex flex-column justify-content-center align-items-center inbox-empty-state inbox-filter-empty">
                                                <img src="../shared/assets/img/empty/inbox.png" width="100" class="mb-1">
                                                <div class="text-center text-14 text-reg mt-1">Your inbox is empty!</div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="d-flex flex-column justify-content-center align-items-center inbox-empty-state"
                                            style="min-height: 60vh;">
                                            <img src="../shared/assets/img/empty/inbox.png" width="100" class="mb-1">
                                            <div class="text-center text-14 text-reg mt-1">Your inbox is empty!</div>
                                        </div>
                                    <?php endif; ?>
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

    <!-- Toggle JS -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const filterToggle = document.getElementById("filterToggle");
            const mobileFilters = document.getElementById("mobileFilters");
            const icon = filterToggle.querySelector(".material-symbols-rounded");

            const storageKey = "filtersVisible_" + "<?php echo $activePage; ?>";

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
        document.addEventListener('DOMContentLoaded', () => {
            const messageContainer = document.querySelector('.message-container');
            const sortDropdown = document.querySelector('.custom-dropdown[data-dropdown="sort"]');
            const courseDropdown = document.querySelector('.custom-dropdown[data-dropdown="course"]');
            const messageCards = messageContainer ? Array.from(messageContainer.querySelectorAll('.inbox-card')) : [];

            if (!messageContainer) {
                return;
            }

            const baseEmptyState = messageContainer.querySelector('.inbox-empty-state:not(.inbox-filter-empty)');
            const hasCards = messageCards.length > 0;
            if (baseEmptyState) {
                baseEmptyState.style.display = hasCards ? 'none' : 'flex';
            }

            if (!hasCards) {
                return;
            }

            let currentSortDirection = 'desc';
            let currentCourseFilter = 'All';

            let filterEmptyState = document.getElementById('js-empty-state');
            if (!filterEmptyState) {
                filterEmptyState = document.createElement('div');
                filterEmptyState.id = 'js-empty-state';
                filterEmptyState.className = 'd-none d-flex flex-column justify-content-center align-items-center inbox-empty-state inbox-filter-empty';
                filterEmptyState.innerHTML = `
                    <img src="../shared/assets/img/empty/inbox.png" width="100" class="mb-1" alt="Empty inbox">
                    <div class="text-center text-14 text-reg mt-1">Your inbox is empty!</div>
                `;
                messageContainer.appendChild(filterEmptyState);
            }

            const applyFilters = () => {
                const cards = messageCards;

                cards.sort((a, b) => {
                    const aTime = parseInt(a.dataset.timestamp || '0', 10);
                    const bTime = parseInt(b.dataset.timestamp || '0', 10);
                    return currentSortDirection === 'asc' ? aTime - bTime : bTime - aTime;
                }).forEach(card => messageContainer.appendChild(card));

                let visibleCount = 0;
                cards.forEach(card => {
                    const matchesCourse = currentCourseFilter === 'All' || card.dataset.course === currentCourseFilter;
                    if (matchesCourse) {
                        card.style.display = '';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                // Always hide base empty state when we have cards (even if filtered out)
                if (baseEmptyState) {
                    baseEmptyState.style.display = 'none';
                }

                // Show filter empty state when no cards are visible
                const shouldShowEmpty = visibleCount === 0;

                // Ensure filter empty state exists
                if (!filterEmptyState.parentNode) {
                    messageContainer.appendChild(filterEmptyState);
                }

                // Toggle visibility using Bootstrap classes
                if (shouldShowEmpty) {
                    filterEmptyState.classList.remove('d-none');
                    filterEmptyState.classList.add('d-flex');
                } else {
                    filterEmptyState.classList.add('d-none');
                    filterEmptyState.classList.remove('d-flex');
                }

                // Adjust container layout based on whether we're showing empty state
                if (shouldShowEmpty) {
                    messageContainer.style.minHeight = '60vh';
                    messageContainer.style.display = 'flex';
                    messageContainer.style.alignItems = 'center';
                    messageContainer.style.justifyContent = 'center';
                    messageContainer.style.flexDirection = 'column';
                } else {
                    messageContainer.style.minHeight = 'auto';
                    messageContainer.style.display = '';
                    messageContainer.style.alignItems = '';
                    messageContainer.style.justifyContent = '';
                    messageContainer.style.flexDirection = '';
                }
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