<?php $activePage = 'inbox';
include('shared/assets/database/connect.php');
include("shared/assets/processes/session-process.php");

$selectEnrolledQuery = "SELECT DISTINCT
courses.courseCode
FROM courses
INNER JOIN enrollments
    ON courses.courseID = enrollments.courseID
WHERE enrollments.userID = '$userID'
ORDER BY courses.courseCode ASC
";
$selectEnrolledResult = executeQuery($selectEnrolledQuery);

$selectInboxQuery = "SELECT DISTINCT
inbox.inboxID,
inbox.createdAt AS inboxCreatedAt,
inbox.messageText,
inbox.notifType,
courses.courseCode,
courses.courseID,
userinfo.profilePicture AS profPFP,
(SELECT assignments.assignmentID 
 FROM assessments 
 INNER JOIN assignments ON assessments.assessmentID = assignments.assessmentID
 WHERE assessments.courseID = courses.courseID 
 AND (inbox.messageText LIKE CONCAT('%', assessments.assessmentTitle, '%') 
      OR inbox.messageText LIKE CONCAT('%\"', assessments.assessmentTitle, '\"%'))
 LIMIT 1) AS assignmentID,
(SELECT tests.testID 
 FROM assessments 
 INNER JOIN tests ON assessments.assessmentID = tests.assessmentID
 WHERE assessments.courseID = courses.courseID 
 AND (inbox.messageText LIKE CONCAT('%', assessments.assessmentTitle, '%') 
      OR inbox.messageText LIKE CONCAT('%\"', assessments.assessmentTitle, '\"%'))
 LIMIT 1) AS testID,
(SELECT lessons.lessonID 
 FROM lessons 
 WHERE lessons.courseID = courses.courseID 
 AND inbox.messageText LIKE CONCAT('%', lessons.lessonTitle, '%')
 LIMIT 1) AS lessonID
FROM inbox
INNER JOIN enrollments
    ON inbox.enrollmentID = enrollments.enrollmentID
INNER JOIN courses
    ON enrollments.courseID = courses.courseID
INNER JOIN userinfo
    ON courses.userID = userinfo.userID
WHERE enrollments.userID = '$userID'
ORDER BY inbox.inboxID DESC
";
$selectInboxResult = executeQuery($selectInboxQuery);
$inboxCount = mysqli_num_rows($selectInboxResult);
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
                                        <div class="col-auto mobile-dropdown p-0">
                                            <div class="d-flex align-items-center flex-nowrap my-2">
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

                                        <!-- Type -->
                                        <div class="col-auto mobile-dropdown p-0">
                                            <div class="d-flex align-items-center flex-nowrap my-2">
                                                <span class="dropdown-label me-2 text-reg">Type</span>
                                                <div class="custom-dropdown" data-dropdown="type">
                                                    <button class="dropdown-btn text-reg text-14"
                                                        data-selected-type="All">All</button>
                                                    <ul class="dropdown-list text-reg text-14">
                                                        <li data-value="All" data-type="All">All</li>
                                                        <li data-value="Course Update" data-type="Course Update">Course
                                                            Update</li>
                                                        <li data-value="Submissions Update"
                                                            data-type="Submissions Update">Submissions Update</li>
                                                        <li data-value="Badge Updates" data-type="Badge Updates">Badge
                                                            Updates</li>
                                                        <li data-value="Leaderboard Updates"
                                                            data-type="Leaderboard Updates">Leaderboard Updates</li>
                                                        <li data-value="Level Updates" data-type="Level Updates">Level
                                                            Updates</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <!-- Message Content -->
                                    <div class="message-container mt-3 pb-4" <?php echo ($inboxCount == 0) ? 'style="min-height: 60vh; display: flex; align-items: center; justify-content: center; flex-direction: column;"' : 'style="min-height: auto;"'; ?>>
                                        <?php
                                        if ($inboxCount > 0) {
                                            while ($inbox = mysqli_fetch_assoc($selectInboxResult)) {

                                                $timestamp = strtotime($inbox['inboxCreatedAt']);
                                                $displayDate = $timestamp ? date('F j, Y g:iA', $timestamp) : '';
                                                $courseCode = $inbox['courseCode'] ?? '';
                                                $courseID = $inbox['courseID'] ?? '';
                                                $notificationType = trim($inbox['notifType']);
                                                $messageText = trim($inbox['messageText']);
                                                $assignmentID = $inbox['assignmentID'] ?? null;
                                                $testID = $inbox['testID'] ?? null;
                                                $lessonID = $inbox['lessonID'] ?? null;

                                                // Determine page redirect based on message patterns
                                                $dataAction = '';

                                                // Check message patterns to determine the source
                                                if (strpos($messageText, "A new test has been posted") !== false && $testID) {
                                                    // From create-test
                                                    $dataAction = "exam-info.php?testID=" . $testID;
                                                } elseif (strpos($messageText, "A new task has been assigned") !== false && $assignmentID) {
                                                    // From assign-task
                                                    $dataAction = "assignment.php?assignmentID=" . $assignmentID;
                                                } elseif (strpos($messageText, "A new lesson has been added") !== false && $lessonID) {
                                                    // From add-lesson
                                                    $dataAction = "lessons-info.php?lessonID=" . $lessonID;
                                                } elseif (strpos($messageText, "A new announcement has been posted") !== false && $courseID) {
                                                    // From post-announcement
                                                    $dataAction = "course-info.php?courseID=" . $courseID . "&activeTab=announcements";
                                                } elseif ($notificationType == "Submissions Update") {
                                                    // Check if it's assignment or test grading
                                                    if (strpos($messageText, "has been graded") !== false && $assignmentID) {
                                                        // From assignment grading
                                                        $dataAction = "assignment.php?assignmentID=" . $assignmentID;
                                                    } elseif (strpos($messageText, "was returned by your instructor") !== false && $testID) {
                                                        // From test grading
                                                        $dataAction = "exam-info.php?testID=" . $testID;
                                                    } elseif ($assignmentID) {
                                                        // Fallback: try assignment if available
                                                        $dataAction = "assignment.php?assignmentID=" . $assignmentID;
                                                    } elseif ($testID) {
                                                        // Fallback: try test if available
                                                        $dataAction = "exam-info.php?testID=" . $testID;
                                                    } elseif ($courseID) {
                                                        // Fallback: go to course page
                                                        $dataAction = "course-info.php?courseID=" . $courseID;
                                                    }
                                                } elseif ($courseID) {
                                                    // Default: go to course page
                                                    $dataAction = "course-info.php?courseID=" . $courseID;
                                                }
                                                ?>
                                                <div class="card mb-1 me-3 w-100 mt-2 inbox-card"
                                                    data-timestamp="<?php echo $timestamp ?: 0; ?>"
                                                    data-course="<?php echo $courseCode; ?>"
                                                    data-type="<?php echo $notificationType; ?>"
                                                    data-action="<?php echo $dataAction; ?>"
                                                    style="max-width: 1101px; border: 1px solid var(--black); border-radius: 15px; background-color: var(--pureWhite); opacity: 1; cursor:pointer;">

                                                    <div class="card-body py-2 px-4 px-md-3">
                                                        <div class="row align-items-center">

                                                            <div class="col d-flex flex-column text-start mt-2 mb-2">
                                                                <p class="mb-2 text-sbold text-17 message-text"
                                                                    style="color: var(--black); line-height: 100%;">
                                                                    <?php echo $messageText; ?>
                                                                </p>
                                                                <small class="text-reg text-12" style="color: var(--black);">
                                                                    <?php echo $displayDate; ?>
                                                                </small>

                                                                <div class="d-block d-lg-none mt-2">
                                                                    <span
                                                                        class="text-reg text-12 badge rounded-pill course-badge"
                                                                        style="width: 99px; height: 19px; padding: 4px 10px;">
                                                                        <?php echo $courseCode; ?>
                                                                    </span>
                                                                </div>
                                                            </div>

                                                            <div class="col-auto d-none d-lg-flex align-items-center">
                                                                <span class="text-reg text-12 badge rounded-pill course-badge"
                                                                    style="width: 99px; height: 19px; padding: 4px 10px;">
                                                                    <?php echo $courseCode; ?>
                                                                </span>
                                                            </div>

                                                        </div>
                                                    </div>

                                                </div>
                                                <?php
                                            }

                                            // JS empty-state
                                            ?>
                                            <div id="js-empty-state"
                                                class="d-none d-flex flex-column justify-content-center align-items-center inbox-empty-state">
                                                <img src="shared/assets/img/empty/inbox.png" width="100" class="mb-1">
                                                <div class="text-center text-14 text-reg mt-1">No notifications found.</div>
                                            </div>

                                        <?php } else { ?>
                                            <div
                                                class="d-flex flex-column justify-content-center align-items-center inbox-empty-state">
                                                <img src="shared/assets/img/empty/inbox.png" width="100" class="mb-1">
                                                <div class="text-center text-14 text-reg mt-1">Your inbox is empty!</div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="feedbackModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered py-4" style="max-width: 700px;  height: 25px;">
                <div class="modal-content">

                    <!-- HEADER -->
                    <div class="modal-header border-bottom">
                        <div class="modal-title text-sbold text-20 ms-3">
                            Congratulations!
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form id="feedbackForm" action="" method="POST">
                        <div class="modal-body pb-2">
                            <div class="container">
                                <div class="row justify-content-center">
                                    <div class="col-12 d-flex justify-content-center flex-column text-center">
                                        <img class="img-fluid object-fit-contain mx-auto mb-3" width="200px"
                                            src="shared/assets/img/badge/perfect_scorer.png" alt="image">
                                        <p class="text-sbold mb-0">You’ve unlocked a new badge:</p>
                                        <p class="text-bold">Ahead of the Curve</p>
                                    </div>
                                    <div class="row">
                                        <div class="mx-auto col-8 text-center">
                                            <p class="text-reg text-14">You’re leading the pack! This badge is awarded
                                                for staying on top of lessons and completing tasks before the deadlines.
                                                Keep blazing the trail!</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mx-auto col-8 text-center">
                                            <img class="img-fluid object-fit-contain mx-auto" width="20px"
                                                src="shared/assets/img/xp.png" alt="XP">
                                            <span class="text-sbold">+150 XPs</span>
                                        </div>
                                        <div class="mx-auto col-8 text-center">
                                            <img class="img-fluid object-fit-contain mx-auto" width="20px"
                                                src="shared/assets/img/webstar.png" alt="XP">
                                            <span class="text-sbold">+50 Webstars</span>
                                        </div>
                                        <div class="mx-auto col-8 text-center my-3">
                                            <span class="text-reg text-12 badge rounded-pill course-badge"
                                                style="width: 99px; height: 19px; border-radius: 50px; padding: 4px 10px;">
                                                COMP-006
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FOOTER -->
                        <div class="modal-footer border-top">

                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>


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

        <!-- Dropdown js -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const messageContainer = document.querySelector('.message-container');
                const sortDropdown = document.querySelector('.custom-dropdown[data-dropdown="sort"]');
                const courseDropdown = document.querySelector('.custom-dropdown[data-dropdown="course"]');
                const typeDropdown = document.querySelector('.custom-dropdown[data-dropdown="type"]');

                let currentSortDirection = 'desc';
                let currentCourseFilter = 'All';
                let currentTypeFilter = 'All';

                const applyFilters = () => {
                    if (!messageContainer) return;

                    const cards = Array.from(messageContainer.querySelectorAll('.inbox-card'));
                    const jsEmptyState = document.getElementById('js-empty-state');

                    if (cards.length === 0) {
                        if (jsEmptyState) {
                            jsEmptyState.classList.add('d-none');
                        }
                        return;
                    }

                    // Sort cards
                    cards.sort((a, b) => {
                        const aTime = parseInt(a.dataset.timestamp || '0', 10);
                        const bTime = parseInt(b.dataset.timestamp || '0', 10);
                        return currentSortDirection === 'asc' ? aTime - bTime : bTime - aTime;
                    }).forEach(card => messageContainer.appendChild(card));

                    let visibleCount = 0;
                    // Filter cards
                    cards.forEach(card => {
                        // Trim and normalize course codes for comparison
                        const cardCourse = (card.dataset.course || '').trim();
                        const filterCourse = (currentCourseFilter || '').trim();
                        const cardType = (card.dataset.type || '').trim();
                        const filterType = (currentTypeFilter || '').trim();

                        const matchesCourse = currentCourseFilter === 'All' || cardCourse === filterCourse;
                        const matchesType = currentTypeFilter === 'All' || cardType === filterType;
                        const shouldShow = matchesCourse && matchesType;
                        card.style.display = shouldShow ? '' : 'none';
                        if (shouldShow) {
                            visibleCount++;
                        }
                    });

                    if (jsEmptyState) {
                        const shouldShowEmpty = visibleCount === 0;
                        jsEmptyState.classList.toggle('d-none', !shouldShowEmpty);

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
                    }
                };

                document.querySelectorAll('.custom-dropdown').forEach(dropdown => {
                    const btn = dropdown.querySelector('.dropdown-btn');
                    const list = dropdown.querySelector('.dropdown-list');

                    if (!btn || !list) return;

                    btn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const isOpen = list.style.display === 'block';
                        document.querySelectorAll('.custom-dropdown .dropdown-list').forEach(otherList => {
                            if (otherList !== list) {
                                otherList.style.display = 'none';
                            }
                        });
                        // Toggle current
                        list.style.display = isOpen ? 'none' : 'block';
                    });

                    list.querySelectorAll('li').forEach(item => {
                        item.addEventListener('click', (e) => {
                            e.stopPropagation();
                            const selectedValue = item.dataset.value || item.textContent.trim();
                            btn.textContent = selectedValue;
                            list.style.display = 'none';

                            // Handle sort dropdown
                            if (dropdown === sortDropdown) {
                                currentSortDirection = item.dataset.sort || 'desc';
                                applyFilters();
                            }

                            // Handle course dropdown
                            if (dropdown === courseDropdown) {
                                currentCourseFilter = item.dataset.course || 'All';
                                applyFilters();
                            }

                            // Handle type dropdown
                            if (dropdown === typeDropdown) {
                                currentTypeFilter = item.dataset.type || 'All';
                                applyFilters();
                            }
                        });
                    });

                    document.addEventListener('click', (e) => {
                        if (!dropdown.contains(e.target)) {
                            list.style.display = 'none';
                        }
                    });
                });

                if (messageContainer) {
                    const cards = messageContainer.querySelectorAll('.inbox-card');
                    if (cards.length > 0) {
                        applyFilters();
                    }
                }
            });
        </script>

        <script>
            document.addEventListener("DOMContentLoaded", () => {
                document.querySelectorAll(".inbox-card").forEach(card => {
                    card.addEventListener("click", (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        const url = card.dataset.action;
                        console.log("Card clicked, data-action:", url);
                        if (url && url !== "") {
                            window.location.href = url;
                        } else {
                            console.warn("No URL found in data-action attribute");
                        }
                    });
                });
            });
        </script>
</body>

</html>