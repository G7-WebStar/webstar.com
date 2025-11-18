<?php
include_once '../shared/assets/database/connect.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('sidebar_resolve_user_id')) {
    function sidebar_resolve_user_id()
    {
        $conn = $GLOBALS['conn'];
        if (isset($_SESSION['userID']))
            return (int) $_SESSION['userID'];
        if (isset($_SESSION['email'])) {
            $emailEsc = mysqli_real_escape_string($conn, $_SESSION['email']);
            $res = executeQuery("SELECT userID, userName FROM users WHERE email = '$emailEsc' LIMIT 1");
            if ($res && ($u = mysqli_fetch_assoc($res))) {
                $_SESSION['userID'] = (int) $u['userID'];
                if (!isset($_SESSION['userName']) && isset($u['userName'])) {
                    $_SESSION['userName'] = $u['userName'];
                }
                return (int) $u['userID'];
            }
        }
        if (isset($_SESSION['userName'])) {
            $userNameEsc = mysqli_real_escape_string($conn, $_SESSION['userName']);
            $res = executeQuery("SELECT userID FROM users WHERE userName = '$userNameEsc' LIMIT 1");
            if ($res && ($u = mysqli_fetch_assoc($res))) {
                $_SESSION['userID'] = (int) $u['userID'];
                return (int) $u['userID'];
            }
        }
        return null;
    }
}

if (!function_exists('sidebar_fetch_count')) {
    function sidebar_fetch_count($sql)
    {
        $result = executeQuery($sql);
        if ($result && ($row = mysqli_fetch_assoc($result)) && isset($row['c'])) {
            return (int) $row['c'];
        }
        return 0;
    }
}

$isInboxPage = isset($activePage) && $activePage === 'inbox';

$userId = sidebar_resolve_user_id();
$unreadInboxCount = 0;

// Get enrollmentIDs for the user
$enrollmentIds = [];
if ($userId !== null) {
    $enrollmentQuery = "SELECT enrollmentID FROM enrollments WHERE userID = $userId";
    $enrollmentResult = executeQuery($enrollmentQuery);
    if ($enrollmentResult) {
        while ($row = mysqli_fetch_assoc($enrollmentResult)) {
            $enrollmentIds[] = $row['enrollmentID'];
        }
    }
}

// Inbox: clear and count using enrollmentID only
if (!empty($enrollmentIds)) {
    $enrollmentIdsStr = implode(',', $enrollmentIds);
    if ($isInboxPage)
        executeQuery("UPDATE inbox SET isRead = 1 WHERE enrollmentID IN ($enrollmentIdsStr) AND isRead = 0");
    $unreadInboxCount = sidebar_fetch_count("SELECT COUNT(*) AS c FROM inbox WHERE enrollmentID IN ($enrollmentIdsStr) AND isRead = 0");
} else {
    if ($isInboxPage)
        executeQuery("UPDATE inbox SET isRead = 1 WHERE isRead = 0");
    $unreadInboxCount = sidebar_fetch_count("SELECT COUNT(*) AS c FROM inbox WHERE isRead = 0");
}

// Share to session for view fallback
$_SESSION['InboxCount'] = $unreadInboxCount;
?>

<!-- Styles -->
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0"
    rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">


<!-- Header Navbar -->
<nav class="navbar navbar-light p-2 px-4 d-md-none border-bottom border-secondary-subtle"
    style="position: absolute; top: 0; left: 0; right: 0; width: 100%; padding: 0; margin: 0; background-color: #fff; z-index: 1000;">
    <div class="container-fluid p-0 m-0 d-flex justify-content-between align-items-center">

        <!-- Logo (linked to home) -->
        <a class="navbar-brand mb-2" href="index.php" style="margin-left: 0; text-decoration: none;">
            <img src="../shared/assets/img/webstar-logo-black.png" alt="Webstar" style="height: 25px;">
        </a>

        <div class="dropdown d-flex justify-content-center">
            <button class="btn btn-custom text-sbold rounded-3 px-2 d-flex justify-content-center align-items-center" type="button"
                id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" style="background: var(--primaryColor) !important;
                             border: 1px solid var(--black);">
                <span class="material-symbols-rounded">add</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow text-med" aria-labelledby="dropdownMenuButton"
                style="width: auto; min-width: 0;">
                <li>
                    <a class="dropdown-item d-flex align-items-center mt-1" href="create-course.php">
                        <span class="material-symbols-rounded me-2"
                            style="color:#2c2c2c;font-size:16px">folder</span>
                        Course
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center" href="post-announcement.php">
                        <span class="material-symbols-rounded me-2"
                            style="color:#2c2c2c;font-size:16px">campaign</span>
                        Announce
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center" href="add-lesson.php">
                        <span class="material-symbols-rounded me-2"
                            style="color:#2c2c2c;font-size:16px">notes</span>
                        Lesson
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center" href="create-test.php">
                        <span class="material-symbols-rounded me-2"
                            style="color:#2c2c2c;font-size:16px">quiz</span>
                        Test
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center" href="assign-task.php">
                        <span class="material-symbols-rounded me-2"
                            style="color:#2c2c2c;font-size:16px">add_task</span>
                        Task
                    </a>
                </li>
            </ul>

        </div>

    </div>
</nav>


<!-- Bottom Navbar -->
<nav class="navbar fixed-bottom border-top d-block d-md-none" style="background-color:white; height: 80px;">
    <div class="container d-flex justify-content-around pb-1">

        <!-- Home -->
        <a href="index.php"
            class="btn d-flex nav-btn-navbar flex-column align-items-center <?php echo ($activePage == 'home') ? 'selected-nav-item' : ''; ?>"
            style="border-color:transparent; text-decoration:none;">
            <span class="material-symbols-rounded" style="font-size:20px">
                dashboard
            </span>
            <span class="text-med text-12">Home</span>
        </a>

        <!-- Courses -->
        <a href="course.php"
            class="btn d-flex nav-btn-navbar flex-column align-items-center <?php echo ($activePage == 'course') ? 'selected-nav-item' : ''; ?>"
            style="border-color:transparent; text-decoration:none;">
            <span class="material-symbols-rounded" style="font-size:20px">
                folder
            </span>
            <small class="text-med text-12">Courses</small>
        </a>

        <!-- Quests -->
        <a href="assess.php"
            class="btn d-flex nav-btn-navbar flex-column align-items-center <?php echo ($activePage == 'todo') ? 'selected-nav-item' : ''; ?>"
            style="border-color:transparent; text-decoration:none;">
            <span class="material-symbols-rounded" style="font-size:21px">
                assignment
            </span>
            <small class="text-med text-12">Assess</small>
        </a>

        <!-- Inbox -->
        <a href="inbox.php"
            class="btn d-flex nav-btn-navbar flex-column align-items-center position-relative <?php echo ($activePage == 'inbox') ? 'selected-nav-item' : ''; ?>"
            style="border-color:transparent; text-decoration:none;">
            <i class="bi bi-inbox-fill" style="font-size:25px; color:var(--black); margin-top:-10px;"></i>
            <small class="text-med text-12" style="margin-top:-7px;">Inbox</small>
            <?php
            $displayInbox = isset($unreadInboxCount) && is_numeric($unreadInboxCount)
                ? (int) $unreadInboxCount
                : (isset($_SESSION['InboxCount']) ? (int) $_SESSION['InboxCount'] : 0);
            if ($displayInbox > 0) { ?>
                <span
                    class="mt-1 position-absolute top-0 start-100 z-3 translate-middle badge rounded-pill bg-danger text-reg text-white"
                    style="color:white!important;">
                    <?php echo $displayInbox; ?>
                </span>
            <?php } ?>
        </a>


        <!-- More -->
        <div class="dropup">
            <button class="btn nav-btn-navbar d-flex flex-column align-items-center" data-bs-toggle="dropdown"
                aria-expanded="false" style="border-color:transparent;">
                <span class="material-symbols-rounded dehaze-icon" style="font-size:20px;">
                    dehaze
                </span>
                <small class="text-med text-12">More</small>
            </button>

            <ul class="dropdown-menu dropdown-menu-end text-small shadow"
                style="bottom:100%; margin-bottom:8px; transform:none !important;">

                <!-- Settings -->
                <li style="margin-bottom:6px;">
                    <a class="dropdown-item d-flex align-items-center text-med text-14" href="settings.php">
                        <span class="material-symbols-rounded me-2" style="font-size:18px;">settings</span>
                        Settings
                    </a>
                </li>

                <!-- Support -->
                <li style="margin-bottom:6px;">
                    <a class="dropdown-item d-flex align-items-center text-med text-14" href="support.php">
                        <span class="material-symbols-rounded me-2" style="font-size:18px;">contact_support</span>
                        Support
                    </a>
                </li>

                <!-- Search -->
                <li style="margin-bottom:6px;">
                    <a class="dropdown-item d-flex align-items-center text-med text-14" href="search.php"
                        data-bs-toggle="modal" data-bs-target="#searchModalMobile">
                        <span class="material-symbols-rounded me-2" style="font-size:18px;">search</span>
                        Search
                    </a>
                </li>

                <!-- Calendar -->
                <li style="margin-bottom:6px;">
                    <a class="dropdown-item d-flex align-items-center text-med text-14" href="calendar.php">
                        <span class="material-symbols-rounded me-2" style="font-size:18px;">calendar_month</span>
                        Calendar
                    </a>
                </li>

                <!-- Profile -->
                <li style="margin-bottom:6px;">
                    <a class="dropdown-item d-flex align-items-center text-med text-14" href="profile.php">
                        <span class="material-symbols-rounded me-2" style="font-size:18px;">person</span>
                        My Profile
                    </a>
                </li>

                <!-- Sign Out -->
                <li>
                    <a class="dropdown-item d-flex align-items-center text-med text-14" href="#" onclick="logout();"
                        style="color:var(--highlight);">
                        <span class="material-symbols-rounded me-2" style="font-size:18px;">logout</span>
                        Sign out
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const dropup = document.querySelector('.dropup');
        const dropupBtn = dropup.querySelector('button');
        const icon = dropupBtn.querySelector('.dehaze-icon');

        // When dropdown is shown
        dropup.addEventListener('shown.bs.dropdown', () => {
            icon.textContent = 'close';
            icon.classList.add('spin');
        });

        // When dropdown is hidden (click outside or menu item)
        dropup.addEventListener('hidden.bs.dropdown', () => {
            icon.textContent = 'dehaze';
            icon.classList.remove('spin');
        });

        // When "Search" item clicked, also reset icon
        const searchBtn = dropup.querySelector('a[data-bs-target="#searchModalMobile"]');
        if (searchBtn) {
            searchBtn.addEventListener('click', () => {
                icon.textContent = 'dehaze';
                icon.classList.remove('spin');
            });
        }
    });
</script>