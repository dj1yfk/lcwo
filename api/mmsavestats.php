<?
	session_start();
	header('Content-Type: text/xml; charset=ISO-8859-1');
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Pragma: no-cache");

	
	$uid = $_SESSION[uid];
	if (!$uid) {
		echo "<b>You need to be logged in!</b>";
		exit();
	}

	/* All variables must be integers > 0 */
	foreach ($_POST as $p) {
			$p = (int) $p;
			if ($p < 0) {
				echo "Value >$p< invalid!";
			exit();
		}
	}

	include("../inc/connectdb.php");
	
	/* assemble update query */

	$update = "update lcwo_mmresults set ";
	foreach ($_POST as $k => $v) {
			$update .= "$k = $v, ";
	}
	$update .= "$k = $v where uid=$uid;";
	
	$query = mysqli_query($db,"$update");

	if ($query) {
		echo "<b>Stats saved (".$_POST[count].").</b>";
	}	


	
?>
