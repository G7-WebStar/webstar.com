<?php
include('shared/assets/database/connect.php');
include("shared/assets/processes/session-process.php");

// Get the username from the URL (e.g., profile.php?user=jamesdoe)
$username = $_GET['user'] ?? null;

// If not provided, show the logged-in user's profile
if (!$username && isset($_SESSION['userID'])) {
    $userID = $_SESSION['userID'];
    $query = "
        SELECT 
            users.userID,
            users.userName,
            userinfo.firstName,
            userinfo.lastName,
            userinfo.profilePicture,
            userinfo.schoolEmail,
            userinfo.facebookLink,
            userinfo.linkedInLink,
            userinfo.githubLink,
            userinfo.yearLevel,
            userinfo.yearSection,
            userinfo.programID,
            program.programInitial,
            profile.bio,
            profile.webstars,
            (
                SELECT COUNT(*) 
                FROM enrollments AS e
                WHERE e.userID = users.userID
            ) AS totalEnrollments,
            (
                SELECT COUNT(*)
                FROM studentBadges AS sb
                WHERE sb.userID = users.userID
            ) AS totalBadges
        FROM users
        JOIN userinfo ON users.userID = userinfo.userID
        JOIN program ON userinfo.programID = program.programID
        LEFT JOIN profile ON users.userID = profile.userID
        LEFT JOIN studentBadges AS sb ON users.userID = sb.userID
        LEFT JOIN badges AS b ON sb.badgeID = b.badgeID
        WHERE users.userID = '$userID'
    ";
    $badgeQuery = "
        SELECT b.badgeName, b.badgeIcon, COUNT(sb.badgeID) AS timesEarned
        FROM studentBadges AS sb
        JOIN badges AS b ON sb.badgeID = b.badgeID
        WHERE sb.userID = '$userID'
        GROUP BY sb.badgeID
    ";

    $selectLeaderboardQuery = "SELECT 
    courses.courseID,
    courses.courseCode,
    courses.courseTitle,
    SUM(leaderboard.xpPoints) AS totalPoints
    FROM leaderboard
    INNER JOIN enrollments
        ON leaderboard.enrollmentID = enrollments.enrollmentID
    INNER JOIN courses
	    ON enrollments.courseID = courses.courseID
    WHERE enrollments.userID = '$userID'
    GROUP BY courses.courseID, courses.courseCode, courses.courseTitle;
";
    $selectLeaderboardResult = executeQuery($selectLeaderboardQuery);

    $activitiesQuery = "
    SELECT 
        a.description,
        a.createdAt
    FROM activities AS a
    WHERE a.userID = '$userID'
    ORDER BY a.createdAt DESC
    LIMIT 10
";
    $activitiesResult = executeQuery($activitiesQuery);

} elseif ($username) {
    // Otherwise, get user by username
    $escaped = mysqli_real_escape_string($conn, $username);
    $query = "
        SELECT 
            users.userID,
            users.userName,
            userinfo.firstName,
            userinfo.lastName,
            userinfo.profilePicture,
            userinfo.schoolEmail,
            userinfo.facebookLink,
            userinfo.linkedInLink,
            userinfo.githubLink,
            userinfo.yearLevel,
            userinfo.yearSection,
            userinfo.programID,
            program.programInitial,
            profile.bio,
            profile.webstars,
            (
                SELECT COUNT(*) 
                FROM enrollments AS e
                WHERE e.userID = users.userID
            ) AS totalEnrollments,
            (
                SELECT COUNT(*)
                FROM studentBadges AS sb
                WHERE sb.userID = users.userID
            ) AS totalBadges
        FROM users
        JOIN userinfo ON users.userID = userinfo.userID
        JOIN program ON userinfo.programID = program.programID
        LEFT JOIN profile ON users.userID = profile.userID
        LEFT JOIN studentBadges AS sb ON users.userID = sb.userID
        LEFT JOIN badges AS b ON sb.badgeID = b.badgeID
        WHERE users.userName = '$escaped'
    ";
    $badgeQuery = "
    SELECT b.badgeName, b.badgeIcon, COUNT(sb.badgeID) AS timesEarned
    FROM studentBadges AS sb
    JOIN badges AS b ON sb.badgeID = b.badgeID
    JOIN users AS u ON sb.userID = u.userID
    WHERE u.userName = '$escaped'
    GROUP BY sb.badgeID
";

    $selectLeaderboardQuery = "
    SELECT 
        c.courseID,
        c.courseCode,
        c.courseTitle,
        SUM(l.xpPoints) AS totalPoints
    FROM leaderboard AS l
    INNER JOIN enrollments AS e
        ON l.enrollmentID = e.enrollmentID
    INNER JOIN courses AS c
        ON e.courseID = c.courseID
    INNER JOIN users AS u
        ON e.userID = u.userID
    WHERE u.userName = '$escaped'
    GROUP BY c.courseID, c.courseCode, c.courseTitle
";
    $selectLeaderboardResult = executeQuery($selectLeaderboardQuery);

    $activitiesQuery = "
    SELECT 
        a.description,
        a.createdAt
    FROM activities AS a
    INNER JOIN users AS u
        ON a.userID = u.userID
    WHERE u.userName = '$escaped'
    ORDER BY a.createdAt DESC
    LIMIT 10
";
    $activitiesResult = executeQuery($activitiesQuery);

} else {
    // No username and no session → redirect to login
    header("Location: login.php");
    exit;
}

$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// For My Items
if (!$username && isset($_SESSION['userID'])) {
    // Use userID from session
    $myItemsQuery = "
        SELECT 
        prof.bio,
        prof.webstars,
        e.emblemName AS emblemTitle,
        e.emblemPath AS emblemImg,
        c.title AS coverTitle,
        c.imagePath AS coverImg,
        t.themeName AS colorTitle,
        t.hexCode AS colorHex
    FROM profile prof
    LEFT JOIN emblem e ON prof.emblemID = e.emblemID
    LEFT JOIN coverImage c ON prof.coverImageID = c.coverImageID
    LEFT JOIN colorTheme t ON prof.colorThemeID = t.colorThemeID
        WHERE prof.userID = '$userID'
    ";
} elseif ($username) {
    $escaped = mysqli_real_escape_string($conn, $username);
    // Get userID by username first
    $userRow = $conn->query("SELECT userID FROM users WHERE userName = '$escaped' LIMIT 1")->fetch_assoc();
    if ($userRow) {
        $userID = (int) $userRow['userID'];
        $myItemsQuery = "
            SELECT 
        prof.bio,
        prof.webstars,
        e.emblemName AS emblemTitle,
        e.emblemPath AS emblemImg,
        c.title AS coverTitle,
        c.imagePath AS coverImg,
        t.themeName AS colorTitle,
        t.hexCode AS colorHex
    FROM profile prof
    LEFT JOIN emblem e ON prof.emblemID = e.emblemID
    LEFT JOIN coverImage c ON prof.coverImageID = c.coverImageID
    LEFT JOIN colorTheme t ON prof.colorThemeID = t.colorThemeID
            WHERE prof.userID = '$escaped'
        ";
    } else {
        // Username not found
        header("Location: 404.php");
        exit;
    }
}

// Execute the query
$myItemsResult = mysqli_query($conn, $myItemsQuery);
$profile = mysqli_fetch_assoc($myItemsResult);

if (!$user) {
    // Redirect to 404 page if user is not found
    header("Location: 404.php");
    exit;
}

function getRelativeTime($datetime, $fullDateFallback = true)
{
    $now = new DateTime("now", new DateTimeZone('Asia/Manila'));
    $past = new DateTime($datetime, new DateTimeZone('Asia/Manila'));
    $diff = $now->getTimestamp() - $past->getTimestamp();

    if ($diff < 0) {
        $diff = 0;
    }

    if ($diff < 3600) { // less than 1 hour → minutes
        $minutes = max(1, floor($diff / 60));
        return $minutes . 'm ago';
    } elseif ($diff < 86400) { // less than 1 day → hours
        $hours = floor($diff / 3600);
        return $hours . 'h ago';
    } elseif ($diff < 604800) { // less than 1 week → days
        $days = floor($diff / 86400);
        return $days . 'd ago';
    } else { // older → show full date
        return $fullDateFallback ? date("F j, Y", strtotime($datetime)) : floor($diff / 604800) . 'w ago';
    }
}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@<?= htmlspecialchars($user['userName']) ?>'s Webstar Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="shared/assets/css/profile.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="shared/assets/img/webstar-icon.png">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0"
        rel="stylesheet" />

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

                    <div class="container-fluid py-3 overflow-y-auto row-padding-top" style="position: relative;">
                        <div class="row g-0 w-100">

                            <!-- Sticky Header -->
                            <div class="d-flex align-items-center text-decoration-none sticky-header py-4"
                                id="stickyHeader" style="padding: 14px 18px;">
                                <div class="rounded-circle me-3 flex-shrink-0 ms-3" style="width: 40px; height: 40px; background-color: #5ba9ff;
                                background: url('shared/assets/pfp-uploads/<?= htmlspecialchars($user['profilePicture']) ?>') no-repeat center center;
                                background-size: cover;">
                                </div>
                                <div class="d-flex flex-column justify-content-center">
                                    <span class="text-sbold">
                                        <?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) ?>
                                    </span>
                                    <small class="text-reg">
                                        @<?= htmlspecialchars($user['userName']) ?>
                                    </small>
                                </div>
                            </div>

                            <!-- First Column -->
                            <div class="col-12 col-md-4 first-column d-flex flex-column"
                                style="position: sticky; top: 0px; z-index: 5; align-self: flex-start; height: fit-content;">

                                <!-- Profile -->
                                <div class="row m-0 w-100" id="firstColumn">
                                    <div class="col m-0 p-0 ">
                                        <div class="card profile rounded-4 me-md-2"
                                            style="border: 1px solid var(--black);background: linear-gradient(to bottom, <?= htmlspecialchars($profile['colorHex']) ?>, #FFFFFF);">
                                            <!-- General Info -->
                                            <div class="row m-0 pb-md-3 d-flex align-items-center">
                                                <div class="cover-photo" style="background: url('shared/assets/img/shop/cover-images/<?= htmlspecialchars($profile['coverImg']) ?>') center/cover no-repeat;"></div>

                                                <!-- Profile Block -->
                                                <div class="profile-block px-4">
                                                    <div class="profile-pic"
                                                        style="background: url('shared/assets/pfp-uploads/<?= htmlspecialchars($user['profilePicture']) ?>') center/cover no-repeat white;">
                                                    </div>
                                                    <div class="profile-text mt-3">
                                                        <!-- Name and Username -->
                                                        <div class="div">
                                                            <div class="user-name text-bold">
                                                                <?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) ?>
                                                            </div>
                                                            <div class="user-username text-med text-muted">
                                                                @<?= htmlspecialchars($user['userName']) ?>
                                                            </div>
                                                            <div class="user-username text-med text-muted">
                                                                <?= htmlspecialchars($user['programInitial'] . ' ' . $user['yearLevel'] . '-' . $user['yearSection']) ?>
                                                            </div>

                                                            <!-- Bio -->
                                                            <div class="bio mt-3">
                                                                <div class="text-med text-14">
                                                                    <?= htmlspecialchars($user['bio']) ?>
                                                                </div>
                                                            </div>
                                                            <!-- Stats -->
                                                            <div class="stats mt-4">
                                                                <div
                                                                    class="d-flex justify-content-between align-items-center text-center">
                                                                    <div class="flex-fill text-center mx-1">
                                                                        <span class="text-16 text-bold">
                                                                            <?= htmlspecialchars($user['totalBadges']) ?>
                                                                        </span>
                                                                        <small
                                                                            class="text-med text-muted">badges</small>
                                                                    </div>

                                                                    <div class="flex-fill text-center mx-1">
                                                                        <span class="text-bold">
                                                                            <?= htmlspecialchars($user['totalEnrollments']) ?>
                                                                        </span>
                                                                        <small class="text-med text-muted">
                                                                            courses</small>
                                                                    </div>

                                                                    <div class="flex-fill text-center mx-1">
                                                                        <span class="text-bold">
                                                                            <?= htmlspecialchars($user['webstars']) ?>
                                                                        </span>
                                                                        <small
                                                                            class="text-med text-muted">webstars</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- Send an Email -->
                                                            <div
                                                                class="d-flex justify-content-center align-items-center mt-4">
                                                                <a href="https://mail.google.com/mail/?view=cm&fs=1&to=<?= htmlspecialchars($user['schoolEmail']) ?>"
                                                                    target="_blank" rel="noopener noreferrer"
                                                                    class="btn d-flex align-items-center justify-content-center gap-2 rounded-3 text-sbold text-14 m-0"
                                                                    style="background-color: <?= htmlspecialchars($profile['colorHex']) ?>; border: 1px solid var(--black); width: 100%;">

                                                                    <span class="material-symbols-rounded">mail</span>
                                                                    <span>Email</span>

                                                                </a>
                                                            </div>
                                                            <!-- Socials -->
                                                            <div class="d-flex justify-content-center align-items-center gap-4 mt-3 text-20 mb-3"
                                                                style="color: var(--black);">
                                                                <?php if (!empty($user['facebookLink'])):
                                                                    $facebook = $user['facebookLink'];
                                                                    if (!str_starts_with($facebook, 'http://') && !str_starts_with($facebook, 'https://')) {
                                                                        $facebook = 'https://' . $facebook;
                                                                    }
                                                                    ?>
                                                                    <a href="<?= htmlspecialchars($facebook) ?>"
                                                                        target="_blank" rel="noopener noreferrer"
                                                                        style="color: inherit;">
                                                                        <i class="fab fa-facebook"></i>
                                                                    </a>
                                                                <?php endif; ?>

                                                                <?php if (!empty($user['githubLink'])):
                                                                    $github = $user['githubLink'];
                                                                    if (!str_starts_with($github, 'http://') && !str_starts_with($github, 'https://')) {
                                                                        $github = 'https://' . $github;
                                                                    }
                                                                    ?>
                                                                    <a href="<?= htmlspecialchars($github) ?>"
                                                                        target="_blank" rel="noopener noreferrer"
                                                                        style="color: inherit;">
                                                                        <i class="fab fa-github"></i>
                                                                    </a>
                                                                <?php endif; ?>

                                                                <?php if (!empty($user['linkedInLink'])):
                                                                    $linkedin = $user['linkedInLink'];
                                                                    if (!str_starts_with($linkedin, 'http://') && !str_starts_with($linkedin, 'https://')) {
                                                                        $linkedin = 'https://' . $linkedin;
                                                                    }
                                                                    ?>
                                                                    <a href="<?= htmlspecialchars($linkedin) ?>"
                                                                        target="_blank" rel="noopener noreferrer"
                                                                        style="color: inherit;">
                                                                        <i class="fab fa-linkedin"></i>
                                                                    </a>
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

                            <!-- Second Column -->
                            <div class="col-12 col-md-4 m-0 second-column">
                                <!-- My Emblem Row-->
                                <div class="row m-0 w-100 mb-2">
                                    <!-- My Emblem Col-->
                                    <div class="col m-0 p-0 ">
                                        <!-- My Emblem Card-->
                                        <div class="card second rounded-4 p-4 me-md-2"
                                            style="border: 1px solid var(--black);">
                                            <!-- My Emblem Header-->
                                            <div class="d-flex align-items-center">
                                                <span class="material-symbols-rounded me-2">
                                                    favorite
                                                </span>
                                                <span class="text-sbold">My Emblem</span>
                                            </div>
                                            <div class="h-100 d-flex justify-content-center align-items-center">
                                                <img src="shared/assets/img/shop/emblems/<?= htmlspecialchars($profile['emblemImg']) ?>" class="img-fluid"
                                                    style="max-height: 250px; width: 100%; height: auto; object-fit: contain;">
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <!-- My Badges Row -->
                                <div class="row m-0 w-100 mb-2">
                                    <!-- My Badges Col-->
                                    <div class="col m-0 p-0 ">
                                        <!-- My Badges Card-->
                                        <div class="card second rounded-4 pt-4 ps-4 pe-4 pb-3 me-md-2"
                                            style="border: 1px solid var(--black);">
                                            <!-- My Badges Header-->
                                            <div class="d-flex justify-content-between align-items-start m-0 p-0 mb-2">
                                                <div class="d-flex align-items-center">
                                                    <span class="material-symbols-rounded me-2">
                                                        trophy
                                                    </span>
                                                    <span class="text-sbold">My Badges</span>
                                                </div>
                                                <div class="text-bold text-med">
                                                    <?= htmlspecialchars($user['totalBadges']) ?>
                                                </div>
                                            </div>
                                            <!-- My Badges Content -->
                                            <div class="w-100 d-flex justify-content-center">
                                                <div class="w-100 m-0 p-0 mb-1"
                                                    style="max-height:1500px; overflow-y: auto; margin-right: -10px;">
                                                    <!-- Badges Card -->
                                                    <?php
                                                    $badgeResult = mysqli_query($conn, $badgeQuery);

                                                    // Check if there are any badges
                                                    if (mysqli_num_rows($badgeResult) > 0) {
                                                        while ($row = mysqli_fetch_assoc($badgeResult)) {
                                                            ?>
                                                            <div class="w-100 badge-option rounded-3 d-flex align-items-center p-2 mt-2"
                                                                style="cursor: pointer; border: 1px solid var(--black);">
                                                                <img src="shared/assets/img/badge/<?php echo $row['badgeIcon']; ?>"
                                                                    alt="Badge" style="width: 55px; height: 55px;"
                                                                    class="mx-1 ms-1 me-2">
                                                                <div>
                                                                    <div style="line-height: 1.1;">
                                                                        <div class="text-sbold text-14">
                                                                            <?php echo $row['badgeName']; ?>
                                                                        </div>
                                                                        <div class="text-med text-12">
                                                                            Received
                                                                            <strong><?php echo $row['timesEarned']; ?></strong>
                                                                            <?php echo $row['timesEarned'] > 1 ? 'times' : 'time'; ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?php
                                                        }
                                                    } else {
                                                        // Display this if no badges
                                                        echo '<div class="text-center text-med text-14 mt-2">This user has no badges yet.</div>';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- My Courses Row -->
                                <div class="row m-0 w-100">
                                    <!-- My Courses Col-->
                                    <div class="col m-0 p-0 ">
                                        <!-- My Courses Card-->
                                        <div class="card second rounded-4 pt-4 ps-4 pe-4 pb-2 me-md-2"
                                            style="border: 1px solid var(--black);">
                                            <!-- My Courses Header-->
                                            <div class="d-flex justify-content-between align-items-start m-0 p-0 mb-3">
                                                <div class="d-flex align-items-center">
                                                    <span class="material-symbols-rounded me-2">
                                                        folder
                                                    </span>
                                                    <span class="text-sbold">My Courses</span>
                                                </div>
                                                <div class="text-bold text-med">
                                                    <?= htmlspecialchars($user['totalEnrollments']) ?>
                                                </div>
                                            </div>
                                            <!-- Course Card -->
                                            <div class="w-100 m-0 p-0 mb-1 "
                                                style="max-height:1500px; overflow-y: auto; margin-right: -10px;">
                                                <?php
                                                if (mysqli_num_rows($selectLeaderboardResult) > 0) {
                                                    while ($leaderboards = mysqli_fetch_assoc($selectLeaderboardResult)) {
                                                        ?>
                                                        <div class="card rounded-3 mb-2"
                                                            style="border: 1px solid var(--black);">
                                                            <div class="card-body p-4">
                                                                <!-- Rank Info -->
                                                                <div style="display: inline-flex; align-items: center;">
                                                                    <span class="rank-number text-bold text-18">11</span>
                                                                    <span
                                                                        class="text-reg text-12 badge rounded-pill ms-2 learderboard-badge"
                                                                        style="display: inline-flex; align-items: center; gap: 4px;">
                                                                        <i class="fa-solid fa-caret-up"></i>
                                                                        2
                                                                    </span>
                                                                </div>

                                                                <!-- Course Info -->
                                                                <div class="info-block">
                                                                    <div class="comp-code text-sbold text-16">
                                                                        <?php echo $leaderboards['courseCode']; ?>
                                                                    </div>
                                                                    <div class="subj-code text-reg text-12 mb-0 text-truncate">
                                                                        <?php echo $leaderboards['courseTitle']; ?>
                                                                    </div>

                                                                    <div class="xp-container">
                                                                        <div class="xp-block text-reg text-12 mb-0">
                                                                            <?php echo $leaderboards['totalPoints']; ?> · LV 1
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    }
                                                } else
                                                    echo '<div class="text-center text-med text-14 mb-2">This user has no badges yet.</div>';
                                                ?>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Third Column -->
                            <div class="col-12 col-md-4 m-0 third-column">
                                <!-- My Star Card Row-->
                                <div class="row m-0 w-100 mb-2 star-card-row">
                                    <!-- My Star Card Col-->
                                    <div class="col m-0 p-0 ">
                                        <!-- My Star Card Card-->
                                        <div class="card second rounded-4 p-4 me-md-2"
                                            style="border: 1px solid var(--black);">
                                            <!-- My Star Card Header-->
                                            <div class="d-flex justify-content-between align-items-start m-0 p-0">
                                                <div class="d-flex align-items-center">
                                                    <span class="material-symbols-rounded me-2">
                                                        kid_star
                                                    </span>
                                                    <span class="text-sbold">My Star Card</span>
                                                </div>
                                                <?php if (isset($_SESSION['userID']) && $_SESSION['userID'] == $user['userID']): ?>
                                                    <div>
                                                        <button type="button"
                                                            class="btn btn-sm px-3 rounded-pill text-med text-14"
                                                            style="background-color: <?= htmlspecialchars($profile['colorHex']) ?>; border: 1px solid var(--black);"
                                                            onclick="exportCardAsJPG()"
                                                            title="Download your Star Card and share it with your friends!"
                                                            data-bs-toggle="tooltip" data-bs-placement="left">
                                                            <div style="display: flex; align-items: center; gap: 5px;">
                                                                <i class="fa-solid fa-share"></i>
                                                                <span>Share</span>
                                                            </div>
                                                        </button>

                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <!-- My Star Card Content -->
                                            <div class="w-100 d-flex justify-content-center m-0 p-0 mb-1">
                                                <div class="mt-3 rounded-4 p-0"
                                                    style="border: 1px solid var(--black); width: 250px;  aspect-ratio: 1 / 1  !important;">
                                                    <div class="px-4 rounded-4 star-card"
                                                        style="background: linear-gradient(to bottom,<?= htmlspecialchars($profile['colorHex']) ?>, #FFFFFF); max-width: 350px; ">
                                                        <div class="text-center text-12 text-sbold mb-4"
                                                            style="margin-top: 30px;">
                                                            <span class="me-1">My Week on </span>
                                                            <img src="shared/assets/img/webstar-logo-black.png"
                                                                style="width: 80px; height: 100%; object-fit: cover; margin-top:-5px"
                                                                alt="Profile Picture">
                                                        </div>
                                                        <div
                                                            class="d-flex justify-content-center text-decoration-none pb-2">
                                                            <div class="rounded-circle flex-shrink-0 me-2 overflow-hidden"
                                                                style="width: 40px; height: 40px; border: 1px solid var(--black); box-shadow: inset 0 0 0 2px rgba(0, 0, 0, 0.8);">
                                                                <img src="shared/assets/pfp-uploads/<?= htmlspecialchars($user['profilePicture']) ?>"
                                                                    style="width: 100%; height: 100%; object-fit: cover;"
                                                                    alt="Profile Picture">
                                                            </div>

                                                            <div
                                                                class="d-flex flex-column justify-content-center text-12">
                                                                <span class="text-sbold">
                                                                    <?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) ?>
                                                                </span>
                                                                <small class="text-reg">
                                                                    @<?= htmlspecialchars($user['userName']) ?>
                                                                </small>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="d-flex flex-column justify-content-center text-14 mt-1">
                                                            <span class="text-bold text-center">COMP-006</span>
                                                            <small class="text-reg text-center">Web
                                                                Development</small>
                                                        </div>
                                                        <div class="stats mt-3 mb-1">
                                                            <div
                                                                class="d-flex justify-content-between align-items-center text-center">
                                                                <div class="flex-fill text-center mx-1 text-14">
                                                                    <div class="text-bold">2</div>
                                                                    <small
                                                                        class="text-med text-muted text-12">level</small>
                                                                </div>

                                                                <div class="flex-fill text-center mx-1 text-14">
                                                                    <div class="text-bold">3</div>
                                                                    <small class="text-med text-muted text-12">
                                                                        rank</small>
                                                                </div>

                                                                <div class="flex-fill text-center mx-1 text-14">
                                                                    <div class="text-bold">340</div>
                                                                    <small
                                                                        class="text-med text-muted text-12">XPs</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="emblem">
                                                            <div
                                                                class="h-100 d-flex justify-content-center align-items-center">
                                                                <img src="shared/assets/img/shop/emblems/<?= htmlspecialchars($profile['emblemImg']) ?>" class="img-fluid"
                                                                    style="max-height: 250px; width: 100%; height: auto; object-fit: contain;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Recent Activity Row -->
                                <div class="row m-0 w-100 mb-2">
                                    <!-- Recent Activity Col-->
                                    <div class="col m-0 p-0 ">
                                        <!-- Recent Activity Card-->
                                        <div class="card second rounded-4 pt-4 ps-4 pe-4 pb-2 me-md-2"
                                            style="border: 1px solid var(--black);">
                                            <!-- Recent Activity Header-->
                                            <div class="d-flex align-items-center mb-3">
                                                <span class="material-symbols-rounded me-2">
                                                    bolt
                                                </span>
                                                <span class="text-sbold">Recent Activity</span>
                                            </div>

                                            <div class="w-100 m-0 p-0 mb-1"
                                                style="max-height:1500px; overflow-y: auto; margin-right: -10px;">

                                                <?php
                                                if (mysqli_num_rows($activitiesResult) > 0) {
                                                    while ($activity = mysqli_fetch_assoc($activitiesResult)) {
                                                        ?>
                                                        <div class="mb-3">
                                                            <div class="text-sbold text-14">
                                                                <?= htmlspecialchars($activity['description']) ?>
                                                            </div>
                                                            <div class="text-reg text-12 mb-0 text-truncate">
                                                                <?= getRelativeTime($activity['createdAt'], true) ?>
                                                            </div>

                                                        </div>
                                                        <?php
                                                    }
                                                } else {
                                                    echo '<div class="text-center text-med text-14 mb-2">No recent activity yet.</div>';
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
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script>
        function exportCardAsJPG() {
            const card = document.querySelector('.star-card');

            html2canvas(card, {
                scale: window.devicePixelRatio * 4,
                useCORS: true,
                backgroundColor: null,
                logging: false,
                onclone: (clonedDoc) => {
                    // Find the cloned version of the card (not the real one)
                    const clonedCard = clonedDoc.querySelector('.star-card');

                    // Remove Bootstrap's rounded-4 only in the cloned copy
                    clonedCard.classList.remove('rounded-4');
                    clonedCard.querySelectorAll('.rounded-4').forEach(el => el.classList.remove('rounded-4'));
                }
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'star-card-highres.png';
                link.href = canvas.toDataURL('image/png', 1.0);
                link.click();
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const profileCard = document.getElementById('firstColumn');
        const stickyHeader = document.getElementById('stickyHeader');
        if (window.innerWidth < 768) { // only for mobile
            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach(entry => {
                        if (!entry.isIntersecting) {
                            stickyHeader.classList.add('show');
                        } else {
                            stickyHeader.classList.remove('show');
                        }
                    });
                },
                { threshold: 0 }
            );
            observer.observe(profileCard);
        }
    </script>

    <script>
        // Initialize Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>


</body>


</html>