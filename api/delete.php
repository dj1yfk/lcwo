<?
session_start();
header('Content-Type: text/xml; charset=utf-8');
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Pragma: no-cache");

# This is more or less a copy of inc/delete except that we use POST (AJAX)
if (!$_SESSION['uid']) {
		echo "Sorry, you must be logged in to use this function.";
		return 0;
}

include("../inc/functions.php");


$types = array('groups', 'callsigns', 'lesson', 'plaintext', 'words', 'qtc');

if (in_array($_POST['type'], $types) && is_numeric($_POST['nr'])) {
	include("../inc/connectdb.php");

	/* check if this attempt exists and was made by the current user */
	$type = $_POST['type'];
	$nr = $_POST['nr'];

	$gcheckuser =  mysqli_query($db,"SELECT `uid` from lcwo_".$type."results
			WHERE `nr` = '".$nr."';");
	
	if (!$gcheckuser) {
		echo "Error: ".mysqli_error();
	}

	$checkuser = mysqli_fetch_row($gcheckuser);

	if ($checkuser[0] == $_SESSION['uid']) {
		if (mysqli_query($db,"DELETE from lcwo_".$type."results
				WHERE `nr` = '".$nr."';")) {
			echo l('attemptdeleted');
				}
		else {
			echo mysqli_error();
		}
	}
	else {
		echo"<p>Error: Attempt doesn't exist or was made by another user!</p>";
	}

}
else {
	echo "<p>Errorneous data.</p>";
}

?>
