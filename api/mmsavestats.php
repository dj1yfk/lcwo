<?
	session_start();
	header('Content-Type: text/xml; charset=utf-8');
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Pragma: no-cache");

	
	$uid = $_SESSION['uid'];
	if (!$uid) {
		echo "<b>You need to be logged in!</b>";
		exit();
	}

	foreach ($_POST as $k => $v) {
		if (!preg_match('/^[0-9]{1,10}$/', $v)) {
			echo "Value invalid!";
			exit();
		}
		if (!preg_match('/^(k\d+)|(count)$/', $k)) {
			echo "Key invalid!";
			exit();
		}
	}

	include("../inc/connectdb.php");
	
	/* assemble update query for lcwo_mmstatus table which holds the
     * error bars and total number of characters */

	$update = "update lcwo_mmstatus set ";
	foreach ($_POST as $k => $v) {
			$update .= "$k = $v, ";
	}
	$update .= "$k = $v where uid=$uid;";
	
	$query = mysqli_query($db,"$update");


	if ($query) {
		echo "<b>Stats saved (".intval($_POST['count']).").</b>";
	}	


	
?>
