
<?php
include_once 'shared/assets/database/connect.php';
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

if (!function_exists('sidebar_resolve_user_id')) {
	function sidebar_resolve_user_id() {
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
	function sidebar_fetch_count($sql) {
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
	if ($isInboxPage) executeQuery("UPDATE inbox SET isRead = 1 WHERE isRead = 0");
	$unreadInboxCount = sidebar_fetch_count("SELECT COUNT(*) AS c FROM inbox WHERE isRead = 0");
}

// To-do: clear and count
if ($userId !== null) {
	if ($isTodoPage) executeQuery("UPDATE todo SET isRead = 1 WHERE userID = $userId AND isRead = 0");
	$newTodoCount = sidebar_fetch_count("SELECT COUNT(*) AS c FROM todo WHERE userID = $userId AND isRead = 0");
} else {
	if ($isTodoPage) executeQuery("UPDATE todo SET isRead = 1 WHERE isRead = 0");
	$newTodoCount = sidebar_fetch_count("SELECT COUNT(*) AS c FROM todo WHERE isRead = 0");
}

// Share to session for view fallback
$_SESSION['InboxCount'] = $unreadInboxCount;
$_SESSION['TodoNewCount'] = $newTodoCount;
?>

<div class="col-auto d-none d-md-block">
    <div class="col-auto d-none d-md-block">
        <div class="row">
            <!-- Sidebar -->
            <div class="card border-0 sidebar mx-2 p-2 overflow-y-auto" style="width: 220px;">
                <!-- Logo -->
                <div class="d-flex justify-content-center">
                    <img src="shared/assets/img/webstar-logo-black.png" class="img-fluid pt-5 pb-5 px-3" width="180px;">
                </div>

                <!-- Navigation -->
                <ul class="nav flex-column">

                    <li class="nav-item my-1 d-flex align-items-center gap-2 m-3 p-2 rounded-3 <?php echo ($activePage == 'home') ? 'selected-box' : ''; ?>"
                        data-page="home">
                        <img src="shared/assets/img/dashboard.png" class="img-fluid" style="width: 30px; height: 30px;">
                        <a class="nav-link text-dark p-0 text-med text-18 ps-2 <?php echo ($activePage == 'home') ? 'selected' : ''; ?>"
                            href="index.php"><strong>Home</strong></a>
                    </li>

                    <li class="nav-item my-1 d-flex align-items-center gap-2 m-3 p-2 rounded-3 <?php echo ($activePage == 'course') ? 'selected-box' : ''; ?>"
                        data-page="course">
                        <img src="shared/assets/img/courses.png" class="img-fluid" style="width: 30px; height: 30px;">
                        <a class="nav-link text-dark p-0 text-med text-18 ps-2 <?php echo ($activePage == 'course') ? 'selected' : ''; ?>"
                            href="course.php"><strong>Courses</strong></a>
                    </li>

                    <li class="nav-item my-1 d-flex align-items-center gap-2 m-3 p-2 rounded-3 <?php echo ($activePage == 'inbox') ? 'selected-box' : ''; ?>"
                        data-page="explore">
                        <div style="position: relative;">
                            <img src="shared/assets/img/inbox.png" class="img-fluid" style="width: 30px; height: 30px;">
                        </div>
                        <a class="nav-link text-dark p-0 text-med text-18 ps-2 <?php echo ($activePage == 'inbox') ? 'inbox' : ''; ?>"
                            href="inbox.php"><strong>Inbox</strong></a>
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
                        <a class="nav-link text-dark p-0 text-med text-18 ps-2 <?php echo ($activePage == 'todo') ? 'selected' : ''; ?>"
                            href="todo.php"><strong>To-do</strong></a>
                        <?php $displayTodo = isset($newTodoCount) ? (int)$newTodoCount : (isset($_SESSION['TodoNewCount']) ? (int)$_SESSION['TodoNewCount'] : 0);
                        if ($displayTodo > 0) { ?>
                            <span class="badge-container ms-auto me-1">
                                <span class="todo-badge"><?php echo $displayTodo; ?></span>
                            </span>
                        <?php } ?>
                    </li>

                   <li class="nav-item my-1 d-flex align-items-center gap-2 m-3 p-2 rounded-3 <?php echo ($activePage == 'explore') ? 'selected-box' : ''; ?>"
                        data-page="explore" data-bs-toggle="modal" data-bs-target="#searchModal">
                        <img src="shared/assets/img/explore.png" class="img-fluid" style="width: 30px; height: 30px;">
                        <a href="#" class="nav-link text-dark p-0 text-med text-18 ps-2 <?php echo ($activePage == 'explore') ? 'selected' : ''; ?>">
                            <strong>Explore</strong></a>
                    </li>


                    <li class="nav-item my-1 d-flex align-items-center gap-2 m-3 p-2 rounded-3 <?php echo ($activePage == 'shop') ? 'selected-box' : ''; ?>"
                        data-page="shop">
                        <img src="shared/assets/img/shop.png" class="img-fluid" style="width: 30px; height: 30px;">
                        <a class="nav-link text-dark p-0 text-med text-18 ps-2 <?php echo ($activePage == 'shop') ? 'selected' : ''; ?>"
                            href="shop.php"><strong>Shop</strong></a>
                    </li>

                </ul>




                <div class="dropdown mt-auto p-4" style="letter-spacing: -1px;">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://avatars.githubusercontent.com/u/181800261?s=96&v=4" alt="" width="32"
                            height="32" class="rounded-circle me-2">
                        <strong class="text-dark text-med text-16 px-1"><?php echo isset($_SESSION['userName']) ? htmlspecialchars($_SESSION['userName']) : 'jamesdoe'; ?></strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end text-small shadow">
                        <li class="ms-3 my-1" style="font-family: var(--Bold);"><i
                                class="fa-solid fa-gear me-2"></i>Settings</li>
                        <li><a class="dropdown-item" style="font-family: var(--Regular);"
                                href="termsAndConditions.php">Terms &
                                Conditions</a></li>
                        <li><a class="dropdown-item" style="font-family: var(--Regular);"
                                href="changepassword.php">Change
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
</div>

<!-- Search Modal -->
<div class="modal fade text-reg" id="searchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="background: transparent !important; box-shadow: none !important;">

            <!-- Search Bar (separated, clean, no background) -->
            <div class="p-3 position-relative">
                <input type="text" class="form-control rounded-pill pe-5 border-black" placeholder="Search students & professors">
                <i class="bi bi-search position-absolute top-50 end-0 translate-middle-y me-5 text-muted z-3"></i>
            </div>

            <!-- Search Results -->
            <div class="p-3">
                <div class="list-group rounded-3 shadow-sm border border-black">
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center border-0">
                        <div class="rounded-circle bg-primary me-3" style="width:40px; height:40px;"></div>
                        <div>
                            <div class="fw-bold">Christian James D. Torrillo</div>
                            <small class="text-muted">@jamesdoe</small>
                        </div>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center border-0">
                        <div class="rounded-circle bg-primary me-3" style="width:40px; height:40px;"></div>
                        <div>
                            <div class="fw-bold">Christian James D. Torrillo</div>
                            <small class="text-muted">@jamesdoe</small>
                        </div>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center border-0">
                        <div class="rounded-circle bg-primary me-3" style="width:40px; height:40px;"></div>
                        <div>
                            <div class="fw-bold">Christian James D. Torrillo</div>
                            <small class="text-muted">@jamesdoe</small>
                        </div>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>