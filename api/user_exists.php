<?
session_start();
header('Content-Type: text/json; charset=utf-8');
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Pragma: no-cache");

$un = $_POST['username'];

if (preg_match('/^[a-zA-Z0-9]{2,24}$/', $un)) {
	include("../inc/connectdb.php");

	$gcheckuser =  mysqli_query($db,"SELECT `id` from lcwo_users WHERE `username` = '".$un."';");
	
	if (!$gcheckuser) {
		echo "Error: ".mysqli_error($db);
		return;
	}

	$checkuser = mysqli_fetch_row($gcheckuser);

	if ($checkuser[0] > 0) {
		echo '{ "user_exists": 1}';
	}
	else {
		echo '{ "user_exists": 0}';
	}
}
else {
		echo '{ "user_exists": -1}';
}

?>
