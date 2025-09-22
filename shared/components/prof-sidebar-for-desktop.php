<?php
include_once '../shared/assets/database/connect.php';
if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
	session_start();
}

if (!function_exists('prof_sidebar_resolve_user_id')) {
	function prof_sidebar_resolve_user_id() {
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

if (!function_exists('prof_sidebar_fetch_count')) {
	function prof_sidebar_fetch_count($sql) {
		$result = executeQuery($sql);
		if ($result && ($row = mysqli_fetch_assoc($result)) && isset($row['c'])) {
			return (int) $row['c'];
		}
		return 0;
	}
}

$profIsInboxPage = isset($activePage) && $activePage === 'inbox';
$profUserId = prof_sidebar_resolve_user_id();
$profInboxCount = 0;

if ($profUserId !== null) {
	if ($profIsInboxPage) executeQuery("UPDATE inbox SET isRead = 1 WHERE userID = $profUserId AND isRead = 0");
	$profInboxCount = prof_sidebar_fetch_count("SELECT COUNT(*) AS c FROM inbox WHERE userID = $profUserId AND isRead = 0");
} else {
	if ($profIsInboxPage) executeQuery("UPDATE inbox SET isRead = 1 WHERE isRead = 0");
	$profInboxCount = prof_sidebar_fetch_count("SELECT COUNT(*) AS c FROM inbox WHERE isRead = 0");
}

$_SESSION['profInboxCount'] = $profInboxCount;
?>

<div class="col-auto d-none d-md-block">
    <div class="col-auto d-none d-md-block">
        <div class="row">
            <!-- Sidebar -->
            <div class="card border-0 sidebar mx-2 p-2 overflow-y-auto" style="width: 220px;">
                <!-- Logo -->
                <div class="d-flex justify-content-center">
                    <img src="../shared/assets/img/webstar-logo-black.png" class="img-fluid pt-5 pb-5 px-3"
                        width="180px;">
                </div>

                <!-- Navigation -->
                <ul class="nav flex-column">

                    <li class="nav-item my-1 d-flex align-items-center gap-2 m-3 p-2 rounded-3 <?php echo ($activePage == 'profIndex') ? 'selected-box' : ''; ?>"
                        data-page="home">
                        <img src="../shared/assets/img/dashboard.png" class="img-fluid"
                            style="width: 30px; height: 30px;">
                        <a class="nav-link text-dark p-0 text-med text-18 ps-2 <?php echo ($activePage == 'profIndex') ? 'selected' : ''; ?>"
                            href="profIndex.php"><strong>Home</strong></a>
                    </li>

                    <li class="nav-item my-1 d-flex align-items-center gap-2 m-3 p-2 rounded-3 <?php echo ($activePage == 'course') ? 'selected-box' : ''; ?>"
                        data-page="">
                        <img src="../shared/assets/img/courses.png" class="img-fluid"
                            style="width: 30px; height: 30px;">
                        <a class="nav-link text-dark p-0 text-med text-18 ps-2 <?php echo ($activePage == 'course') ? 'selected' : ''; ?>"
                            href="#"><strong>Courses</strong></a>
                    </li>

                    <li class="nav-item my-1 d-flex align-items-center gap-2 m-3 p-2 rounded-3 <?php echo ($activePage == 'inbox') ? 'selected-box' : ''; ?>"
                        data-page="">
                        <img src="../shared/assets/img/inbox.png" class="img-fluid" style="width: 30px; height: 30px;">
                        <a class="nav-link text-dark p-0 text-med text-18 ps-2 <?php echo ($activePage == 'inbox') ? 'inbox' : ''; ?>"
                            href="profInbox.php"><strong>Inbox</strong></a>
                        <?php
                        $displayProfInbox = isset($profInboxCount) && is_numeric($profInboxCount)
                            ? (int) $profInboxCount
                            : (isset($_SESSION['profInboxCount']) ? (int) $_SESSION['profInboxCount'] : 0);
                        ?>
                        <?php if ($displayProfInbox > 0) { ?>
                            <span class="badge-container ms-auto me-1">
                                <span class="inbox-badge"><?php echo $displayProfInbox; ?></span>
                            </span>
                        <?php } ?>
                    </li>

                    <li class="nav-item my-1 d-flex align-items-center gap-2 m-3 p-2 rounded-3 <?php echo ($activePage == 'search') ? 'selected-box' : ''; ?>"
                        data-page="">
                        <img src="../shared/assets/img/profIndex/search.png" class="img-fluid"
                            style="width: 30px; height: 30px;">
                        <a class="nav-link text-dark p-0 text-med text-18 ps-2 <?php echo ($activePage == 'search') ? 'selected' : ''; ?>"
                            href="#"><strong>Search</strong></a>
                    </li>

                    <li class="nav-item my-1 d-flex align-items-center gap-2 m-3 p-2 rounded-3 <?php echo ($activePage == 'assess') ? 'selected-box' : ''; ?>"
                        data-page="">
                        <img src="../shared/assets/img/profIndex/assess.png" class="img-fluid"
                            style="width: 30px; height: 30px;">
                        <a class="nav-link text-dark p-0 text-med text-18 ps-2 <?php echo ($activePage == 'assess') ? 'selected' : ''; ?>"
                            href="#"><strong>Assess</strong></a>
                    </li>

                </ul>




                <div class="dropdown mt-auto p-4" style="letter-spacing: -1px;">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://avatars.githubusercontent.com/u/181800261?s=96&v=4" alt="" width="32"
                            height="32" class="rounded-circle me-2">
                        <strong class="text-dark text-med text-16 px-1">jamesdoe</strong>
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