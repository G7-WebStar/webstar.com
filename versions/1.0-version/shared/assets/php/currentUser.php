<?php
$userID = 1;
$_SESSION['userID'] = $userID;

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

$proflieQuery = "SELECT users.userID, userinfo.profilePicture, users.userName, users.email, userinfo.userBio, userinfo.totalPoints, follows.followingID, follows.followedID 
                 FROM users INNER JOIN userinfo ON users.userID = userinfo.userID 
                 INNER JOIN follows ON users.userID = follows.userID WHERE users.userID = $userID;";
$profileResult = executeQuery($proflieQuery);

$followerCountQuery = "SELECT COUNT(*) AS followerCount FROM follows WHERE followingID = $userID";
$followerCountResult = executeQuery($followerCountQuery);

$followingCountQuery = "SELECT COUNT(*) AS followingCount FROM follows WHERE followedID = $userID";
$followingCountResult = executeQuery($followingCountQuery);
