<?

session_start();
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Pragma: no-cache");

if (!$_SESSION['uid']) {
	echo "Sorry, this requires a login.";
	return 0;
}

include("../inc/functions.php");

include("../inc/connectdb.php");

$query = mysqli_query($db,"update lcwo_users set consent=1 where id=".$_SESSION['uid']);

$_SESSION['consent'] = 1;
?>
