<?php
include_once 'shared/assets/database/connect.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('sidebar_resolve_user_id')) {
    function sidebar_resolve_user_id()
    {
        $conn = $GLOBALS['conn'];
        if (isset($_SESSION['userID'])) return (int) $_SESSION['userID'];
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
    if ($isInboxPage) executeQuery("UPDATE inbox SET isRead = 1 WHERE enrollmentID IN ($enrollmentIdsStr) AND isRead = 0");
    $unreadInboxCount = sidebar_fetch_count("SELECT COUNT(*) AS c FROM inbox WHERE enrollmentID IN ($enrollmentIdsStr) AND isRead = 0");
} else {
    // If student has no enrollments, no notification count should be shown and no database updates should occur
    $unreadInboxCount = 0;
}

// To-do: clear and count (only if student has enrollments)
if ($userId !== null && !empty($enrollmentIds)) {
    if ($isTodoPage) executeQuery("UPDATE todo SET isRead = 1 WHERE userID = $userId AND isRead = 0");
    $newTodoCount = sidebar_fetch_count("SELECT COUNT(*) AS c FROM todo WHERE userID = $userId AND isRead = 0");
} else {
    // If student has no enrollments, no todo count should be shown and no database updates should occur
    $newTodoCount = 0;
}

// Share to session for view fallback
$_SESSION['InboxCount'] = $unreadInboxCount;
$_SESSION['TodoNewCount'] = $newTodoCount;
?>

<div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="sidebarOffcanvas"
    aria-labelledby="sidebarOffcanvasLabel" style="background-color:var(--dirtyWhite); width: 250px;">
    <div class="offcanvas-header">
        <button type="button" class="mt-2 btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column p-0">
        <div class="d-flex flex-column flex-shrink-0 p-3" style="width: 100%;">
            <a class="d-flex align-items-center  text-decoration-none">
                <img src="shared/assets/img/webstar-logo-black.png" class="img-fluid w-100 py-3 px-2" />
            </a>

            <hr>
            <ul class="nav nav-pills flex-column mb-auto">

                <li class="nav-item d-flex align-items-center gap-2 my-1 p-2 rounded-3 <?php echo ($activePage == 'home') ? 'selected-box' : ''; ?>"
                    data-page="home">
                    <img src="shared/assets/img/dashboard.png" style="width: 30px; height: 30px;">
                    <a class="nav-link text-dark p-0 text-med text-18 ps-2 <?php echo ($activePage == 'home') ? 'selected' : ''; ?>"
                        href="index.php"><strong>Home</strong></a>
                </li>

                <li class="nav-item d-flex align-items-center gap-2 my-1 p-2 rounded-3 <?php echo ($activePage == 'course') ? 'selected-box' : ''; ?>"
                    data-page="course">
                    <img src="shared/assets/img/courses.png" style="width: 30px; height: 30px;">
                    <a class="nav-link text-dark p-0 text-med text-18 ps-2 <?php echo ($activePage == 'course') ? 'selected' : ''; ?>"
                        href="course.php"><strong>Courses</strong></a>
                </li>

                <li class="nav-item d-flex align-items-center gap-2 my-1 p-2 rounded-3 <?php echo ($activePage == 'inbox') ? 'selected-box' : ''; ?>"
                    data-page="inbox">
                    <div style="position: relative;">
                        <img src="shared/assets/img/inbox.png" style="width: 30px; height: 30px;">
                    </div>
                    <a class="nav-link text-dark p-0 text-med text-18 ps-2 <?php echo ($activePage == 'inbox') ? 'selected' : ''; ?>"
                        href="inbox.php"><strong>Inbox</strong></a>
                    <?php $displayInbox = isset($unreadInboxCount) ? (int)$unreadInboxCount : (isset($_SESSION['InboxCount']) ? (int)$_SESSION['InboxCount'] : 0);
                    if ($displayInbox > 0) { ?>
                        <span class="badge-container ms-auto me-1">
                            <span class="inbox-badge"><?php echo $displayInbox; ?></span>
                        </span>
                    <?php } ?>
                </li>

                <li class="nav-item d-flex align-items-center gap-2 my-1 p-2 rounded-3 <?php echo ($activePage == 'todo') ? 'selected-box' : ''; ?>"
                    data-page="todo">
                    <img src="shared/assets/img/todo.png" style="width: 30px; height: 30px;">
                    <a class="nav-link text-dark p-0 text-med text-18 ps-2 <?php echo ($activePage == 'todo') ? 'selected' : ''; ?>"
                        href="todo.php"><strong>To-do</strong></a>
                    <?php $displayTodo = isset($newTodoCount) ? (int)$newTodoCount : (isset($_SESSION['TodoNewCount']) ? (int)$_SESSION['TodoNewCount'] : 0);
                    if ($displayTodo > 0) { ?>
                        <span class="badge-container ms-auto me-1">
                            <span class="todo-badge"><?php echo $displayTodo; ?></span>
                        </span>
                    <?php } ?>
                </li>

                <li class="nav-item d-flex align-items-center gap-2 my-1 p-2 rounded-3 <?php echo ($activePage == 'explore') ? 'selected-box' : ''; ?>"
                    data-page="explore" data-bs-toggle="modal" data-bs-target="#searchModalMobile">
                    <img src="shared/assets/img/explore.png" class="img-fluid" style="width: 30px; height: 30px;">
                    <a href="#" class="nav-link text-dark p-0 text-med text-18 ps-2 <?php echo ($activePage == 'explore') ? 'selected' : ''; ?>">
                        <strong>Explore</strong></a>
                </li>


                <li class="nav-item d-flex align-items-center gap-2 my-1 p-2 rounded-3 <?php echo ($activePage == 'shop') ? 'selected-box' : ''; ?>"
                    data-page="shop">
                    <img src="shared/assets/img/shop.png" style="width: 30px; height: 30px;">
                    <a class="nav-link text-dark p-0 text-med text-18 ps-2 <?php echo ($activePage == 'shop') ? 'selected' : ''; ?>"
                        href="shop.php"><strong>Shop</strong></a>
                </li>

            </ul>

            <!-- Profile -->
            <hr>
            <div class="dropdown mt-auto py-4 px-2">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://avatars.githubusercontent.com/u/181800261?s=96&v=4" alt="" width="32" height="32"
                        class="rounded-circle me-2">
                    <strong class="text-dark text-med text-16 px-1"><?php echo isset($_SESSION['userName']) ? htmlspecialchars($_SESSION['userName']) : 'jamesdoe'; ?></strong>
                </a>
                <ul class="dropdown-menu dropdown-menu-end text-small shadow">
                    <li class="ms-3 my-1" style="font-family: var(--Bold);"><i
                            class="fa-solid fa-gear me-2"></i>Settings</li>
                    <li><a class="dropdown-item" style="font-family: var(--Regular);"
                            href="termsAndConditions.php">Terms &
                            Conditions</a></li>
                    <li><a class="dropdown-item" style="font-family: var(--Regular);" href="changepassword.php">Change
                            Password</a></li>
                    <li><a class="dropdown-item" style="font-family: var(--Regular);" href="faqs.php">FAQs</a>
                    </li>
                    <li><a class="dropdown-item" style="font-family: var(--Regular);" href="feedback.php">Send
                            Feedback</a></li>
                    <hr class="dropdown-divider">
                    <li><a class="dropdown-item" style="font-family: var(--Bold);" href="profile.php"><i
                                class="fa-solid fa-user me-2"></i>My Profile</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" style="font-family: var(--Bold); color:var(--highlight)"
                            href="login.php"><i class="fa-solid fa-right-from-bracket me-2"></i>Sign
                            out</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Search Modal -->
<div class="modal fade text-reg" id="searchModalMobile" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow"
            style="background: transparent !important; box-shadow: none !important;">

            <!-- Search Bar -->
            <form class="p-3 position-relative">
                <input type="text" id="searchInputMobile" class="form-control rounded-pill pt-3 pb-3"
                    placeholder="Search"
                    style="border: 1.5px solid #2c2c2c; padding-right: 5rem; padding-left: 27px;">
                <span class="material-symbols-rounded pe-3" style="position: absolute; right: 30px; top: 50%; transform: translateY(-50%);
          color: #2c2c2c; font-size: 24px;">search</span>
            </form>

            <!-- Search Results -->
            <div class="p-3">
                <div class="rounded-4 shadow-sm scroll-box" style="border: 1.5px solid #2c2c2c;
          border-radius: 16px; height: 350px; background-color: #fff; padding:10px;">
                    <div id="searchResultsMobile" class="scroll-content"
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
    const searchInputMobile = document.getElementById('searchInputMobile');
    const searchResultsMobile = document.getElementById('searchResultsMobile');

    searchInputMobile.addEventListener('input', () => {
        const query = searchInputMobile.value.trim();

        if (query === '') {
            searchResultsMobile.innerHTML = '<div class="text-center text-muted p-3">Type a name or username to search.</div>';
            return;
        }

        fetch('shared/assets/processes/search-modal.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'searchTerm=' + encodeURIComponent(query)
            })
            .then(res => res.text())
            .then(html => searchResultsMobile.innerHTML = html)
            .catch(() => searchResultsMobile.innerHTML = '<div class="text-center text-muted p-3">Error loading results.</div>');
    });

</script>