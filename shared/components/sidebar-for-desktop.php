<?php
include_once 'shared/assets/database/connect.php';
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
$isTodoPage = isset($activePage) && $activePage === 'todo';

$userId = sidebar_resolve_user_id();
$unreadInboxCount = 0;
$newTodoCount = 0;

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

// To-do: clear and count
if ($userId !== null) {
    if ($isTodoPage)
        executeQuery("UPDATE todo SET isRead = 1 WHERE userID = $userId AND isRead = 0");
    $newTodoCount = sidebar_fetch_count("SELECT COUNT(*) AS c FROM todo WHERE userID = $userId AND isRead = 0");
} else {
    if ($isTodoPage)
        executeQuery("UPDATE todo SET isRead = 1 WHERE isRead = 0");
    $newTodoCount = sidebar_fetch_count("SELECT COUNT(*) AS c FROM todo WHERE isRead = 0");
}

// Share to session for view fallback
$_SESSION['InboxCount'] = $unreadInboxCount;
$_SESSION['TodoNewCount'] = $newTodoCount;

// Get username and pfp

$userID = $_SESSION['userID'];

$usernameAndProfilePictureQuery = "
SELECT 
    u.userName, 
    ui.profilePicture,
    p.webstars
FROM users u
JOIN userinfo ui ON u.userID = ui.userID
JOIN profile p ON u.userID = p.userID
WHERE u.userID = $userID
";

$usernameAndProfilePictureResult = executeQuery($usernameAndProfilePictureQuery);
$userInformation = mysqli_fetch_assoc($usernameAndProfilePictureResult);

?>

<div class="col-auto d-none d-md-block">
    <div class="col-auto d-none d-md-block">
        <div class="row">
            <!-- Sidebar -->
            <div class="card border-0 sidebar mx-2 p-2 overflow-y-auto" style="width: 220px;">
                <!-- Logo -->
                <div class="d-flex justify-content-center">
                    <img src="shared/assets/img/webstar-logo-black.png" class="img-fluid px-3"
                        style="padding-top:2rem; padding-bottom:2rem" width="180px;">
                </div>

                <!-- Navigation -->
                <ul class="nav flex-column">

                    <li class="nav-item my-1 d-flex align-items-center gap-2 m-3 p-2 rounded-3 <?php echo ($activePage == 'home') ? 'selected-box' : ''; ?>"
                        data-page="home">
                        <img src="shared/assets/img/dashboard.png" class="img-fluid" style="width: 30px; height: 30px;">
                        <a class="nav-link p-0 text-med text-18 ps-2 <?php echo ($activePage == 'home') ? 'selected' : ''; ?>"
                            href="index.php" style="text-decoration: none; color:var(--black)"><strong>Home</strong></a>
                    </li>

                    <li class="nav-item my-1 d-flex align-items-center gap-2 m-3 p-2 rounded-3 <?php echo ($activePage == 'course') ? 'selected-box' : ''; ?>"
                        data-page="course">
                        <img src="shared/assets/img/courses.png" class="img-fluid" style="width: 30px; height: 30px;">
                        <a class="nav-link p-0 text-med text-18 ps-2 <?php echo ($activePage == 'course') ? 'selected' : ''; ?>"
                            href="course.php"
                            style="text-decoration: none; color:var(--black)"><strong>Courses</strong></a>
                    </li>

                    <li class="nav-item my-1 d-flex align-items-center gap-2 m-3 p-2 rounded-3 <?php echo ($activePage == 'inbox') ? 'selected-box' : ''; ?>"
                        data-page="explore">
                        <div style="position: relative;">
                            <img src="shared/assets/img/inbox.png" class="img-fluid" style="width: 30px; height: 30px;">
                        </div>
                        <a class="nav-link p-0 text-med text-18 ps-2 <?php echo ($activePage == 'inbox') ? 'inbox' : ''; ?>"
                            href="inbox.php"
                            style="text-decoration: none; color:var(--black)"><strong>Inbox</strong></a>
                        <?php
                        $displayInbox = isset($unreadInboxCount) && is_numeric($unreadInboxCount)
                            ? (int) $unreadInboxCount
                            : (isset($_SESSION['InboxCount']) ? (int) $_SESSION['InboxCount'] : 0);
                        if ($displayInbox > 0) { ?>
                            <span class="badge-container ms-auto me-1">
                                <span class="inbox-badge"><?php echo $displayInbox; ?></span>
                            </span>
                        <?php } ?>
                    </li>

                    <li class="nav-item my-1 d-flex align-items-center gap-2 m-3 p-2 rounded-3 <?php echo ($activePage == 'todo') ? 'selected-box' : ''; ?>"
                        data-page="shop">
                        <img src="shared/assets/img/todo.png" class="img-fluid" style="width: 30px; height: 30px;">
                        <a class="nav-link p-0 text-med text-18 ps-2 <?php echo ($activePage == 'todo') ? 'selected' : ''; ?>"
                            href="todo.php"
                            style="text-decoration: none; color:var(--black)"><strong>Quests</strong></a>
                        <?php $displayTodo = isset($newTodoCount) ? (int) $newTodoCount : (isset($_SESSION['TodoNewCount']) ? (int) $_SESSION['TodoNewCount'] : 0);
                        if ($displayTodo > 0) { ?>
                            <span class="badge-container ms-auto me-1">
                                <span class="todo-badge"><?php echo $displayTodo; ?></span>
                            </span>
                        <?php } ?>
                    </li>

                    <li class="nav-item my-1 d-flex align-items-center gap-2 m-3 p-2 rounded-3 <?php echo ($activePage == 'explore') ? 'selected-box' : ''; ?>"
                        data-page="explore" data-bs-toggle="modal" data-bs-target="#searchModal">
                        <img src="shared/assets/img/explore.png" class="img-fluid" style="width: 30px; height: 30px;">
                        <a href="#" style="text-decoration: none; color:var(--black)"
                            class="nav-link p-0 text-med text-18 ps-2 <?php echo ($activePage == 'explore') ? 'selected' : ''; ?>">
                            <strong>Explore</strong></a>
                    </li>


                    <li class="nav-item my-1 d-flex align-items-center gap-2 m-3 p-2 rounded-3 <?php echo ($activePage == 'shop') ? 'selected-box' : ''; ?>"
                        data-page="shop">
                        <img src="shared/assets/img/shop.png" class="img-fluid" style="width: 30px; height: 30px;">
                        <a class="nav-link p-0 text-med text-18 ps-2 <?php echo ($activePage == 'shop') ? 'selected' : ''; ?>"
                            href="shop.php" style="text-decoration: none; color:var(--black)"><strong>Shop</strong></a>
                    </li>

                    <div class="mt-1 end-0 mt-2 mx-3 mb-2 p-2 d-flex align-items-center">
                        <img class="me-2" src="shared/assets/img/webstar.png" alt="Description of Image" width="30">
                        <div class="d-flex flex-column align-items-start ps-2" style="line-height: 1.2;">
                            <a class="text-med text-18" style="text-decoration: none;">
                                <strong><?php echo $userInformation['webstars'] ?></strong>
                            </a>
                            <a class="text-med text-14" style="text-decoration: none;">
                                Webstars
                            </a>
                        </div>
                    </div>


                </ul>

                <!-- User Dropdown -->
                <div class="dropdown mt-auto p-4">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="shared/assets/pfp-uploads/<?php echo $userInformation['profilePicture'] ?>" alt=""
                            width="32" height="32" class="rounded-circle me-2">
                        <strong class="text-dark text-med text-16 px-1">
                            <?php echo $userInformation['userName'] ?>
                        </strong>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end text-small shadow"
                        style="margin-top: 8px; transform: none !important; --bs-dropdown-link-active-bg: transparent; --bs-dropdown-link-active-color: inherit;">

                        <!-- Settings -->
                        <li style="margin-bottom:6px;">
                            <a class="dropdown-item d-flex align-items-center text-med text-14" href="settings.php">
                                <span class="material-symbols-rounded" style="font-size:18px; display: inline-flex; width: 1.5em; ">settings</span>
                                Settings
                            </a>
                        </li>

                        <!-- Support -->
                        <li style="margin-bottom:6px;">
                            <a class="dropdown-item d-flex align-items-center text-med text-14" href="support.php">
                                <span class="material-symbols-rounded" style="font-size:18px; display: inline-flex; width: 1.5em; ">contact_support</span>
                                Support
                            </a>
                        </li>

                        <!-- Settings -->
                        <li style="margin-bottom:6px;">
                            <a class="dropdown-item d-flex align-items-center text-med text-14" href="calendar.php">
                                <span class="material-symbols-rounded"
                                    style="font-size:18px; display: inline-flex; width: 1.5em; ">calendar_month</span>
                                Calendar
                            </a>
                        </li>

                        <!-- Profile -->
                        <li style="margin-bottom:6px;">
                            <a class="dropdown-item d-flex align-items-center text-med text-14" href="profile.php">
                                <span class="material-symbols-rounded" style="font-size:18px; display: inline-flex; width: 1.5em; ">person</span>
                                My Profile
                            </a>
                        </li>

                        <!-- Sign Out -->
                        <li>
                            <a class="dropdown-item d-flex align-items-center text-med text-14" href="login.php"
                                style="color:var(--highlight);">
                                <span class="material-symbols-rounded" style="font-size:18px; display: inline-flex; width: 1.5em; ">logout</span>
                                Sign Out
                            </a>
                        </li>
                    </ul>
                </div>


            </div>
        </div>
    </div>
</div>

<!-- Search Modal -->
<div class="modal fade text-reg" id="searchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow"
            style="background: transparent !important; box-shadow: none !important;">

            <!-- Search Bar -->
            <form class="p-3 position-relative">
                <input type="text" id="searchInput" class="form-control rounded-pill pt-3 pb-3"
                    placeholder="Search students & professors"
                    style="border: 1.5px solid #2c2c2c; padding-right: 5rem; padding-left: 27px;">
                <span class="material-symbols-rounded pe-3" style="position: absolute; right: 30px; top: 50%; transform: translateY(-50%);
          color: #2c2c2c; font-size: 24px;">search</span>
            </form>

            <!-- Search Results -->
            <div class="p-3">
                <div class="rounded-4 shadow-sm scroll-box" style="border: 1.5px solid #2c2c2c;
          border-radius: 16px; height: 350px; background-color: #fff; padding:10px;">
                    <div id="searchResults" class="scroll-content"
                        style="height: 100%; overflow-y: auto; border-radius: 12px;">
                        <div class="text-center text-muted p-3">Type a name or username to search.</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Search Modal JS -->
<script>
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');

    searchInput.addEventListener('input', () => {
        const query = searchInput.value.trim();

        if (query === '') {
            searchResults.innerHTML = '<div class="text-center text-muted p-3">Type a name or username to search.</div>';
            return;
        }

        fetch('shared/assets/processes/search-modal.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'searchTerm=' + encodeURIComponent(query)
        })
            .then(res => res.text())
            .then(html => searchResults.innerHTML = html)
            .catch(() => searchResults.innerHTML = '<div class="text-center text-muted p-3">Error loading results.</div>');
    });

</script>