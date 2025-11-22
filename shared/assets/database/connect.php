<?php
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$db = "webstart";

// $dbhost = "localhost";
// $dbuser = "u977784600_webstar";
// $dbpass = "Webstaru977784600_";
// $db = "u977784600_webstar";


$conn = new mysqli($dbhost, $dbuser, $dbpass, $db) or die("Connect failed: %s\n" . $conn->error);

if (!$conn) {
	die("Connection Failed. " . mysqli_connect_error());
	echo "can't connect to database";
}

function executeQuery($query)
{
	$conn = $GLOBALS['conn'];
	return mysqli_query($conn, $query);
}
?>