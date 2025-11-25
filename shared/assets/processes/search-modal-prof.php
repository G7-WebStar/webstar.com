<?php
include_once(__DIR__ . '/../database/connect.php');

ini_set('display_errors', 1);
error_reporting(E_ALL);

$search = isset($_POST['searchTerm']) ? trim($_POST['searchTerm']) : '';
$escaped = mysqli_real_escape_string($conn, $search);

$query = "
    SELECT 
        u.userID,
        u.userName,
        i.firstName,
        i.lastName,
        i.profilePicture,
        u.role
    FROM users AS u
    INNER JOIN userinfo AS i ON u.userID = i.userID
    WHERE (u.role = 'student' OR u.role = 'professor' OR u.role = 'developer')
      AND (
          u.userName LIKE '%$escaped%' OR
          i.firstName LIKE '%$escaped%' OR
          i.lastName LIKE '%$escaped%'
      )
    AND u.userName IS NOT NULL 
    AND u.userName <> ''
    AND i.firstName IS NOT NULL
    AND i.firstName <> ''
    LIMIT 20
";

$result = mysqli_query($conn, $query);

if (!$result) {
    echo '<div class="text-center text-danger p-3">Query failed: ' . mysqli_error($conn) . '</div>';
    exit;
}

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $roleBadge = '';
        if ($row['role'] === 'professor') {
            $roleBadge = '<span style="background-color: #b3d4ff; color: #0a3d91; font-size: 0.75rem; 
    padding: 2px 8px; border-radius: 12px; margin-left: 8px;">Instructor</span>';
        } elseif ($row['role'] === 'student') {
            $roleBadge = '<span style="background-color: #d3f9d8; color: #196c2c; font-size: 0.75rem; 
    padding: 2px 8px; border-radius: 12px; margin-left: 8px;">Student</span>';
        } elseif ($row['role'] === 'developer') {
            $roleBadge = '<span style="background-color: #e0ccff; color: #4b0082; font-size: 0.75rem; 
    padding: 2px 8px; border-radius: 12px; margin-left: 8px;">Developer</span>';
        }

        echo '
    <a href="profile.php?user=' . urlencode($row['userName']) . '" class="d-flex align-items-center text-decoration-none"
        style="padding: 14px 18px; transition: background 0.2s;">
        <div class="rounded-circle me-3 flex-shrink-0"
            style="width: 40px; height: 40px; background-color: #5ba9ff;
                   background-image: url(\'../shared/assets/pfp-uploads/' . htmlspecialchars($row['profilePicture']) . '\');
                   background-size: cover; background-position: center;">
        </div>
        <div class="d-flex flex-column justify-content-center">
            <span class="text-sbold">' . htmlspecialchars($row['firstName'] . ' ' . $row['lastName']) . $roleBadge . '</span>
            <small class="text-reg">@' . htmlspecialchars($row['userName']) . '</small>
        </div>
    </a>';
    }
} else {
    echo '
        <div class="text-center d-flex flex-column align-items-center justify-content-center h-100">
            <div>
                <img src="../shared/assets/img/empty/search.png" width="80" class="mb-1">
            </div>
            <div class="text-med text-14 mt-2">No user found</div>
        </div>';
}

?>