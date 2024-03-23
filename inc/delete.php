<?
if (!$_SESSION['uid']) {
		echo "Sorry, you must be logged in to use this function.";
		return 0;
}

$types = array('groups', 'callsigns', 'lesson', 'plaintext', 'words', 'qtc');

if (in_array($_GET['type'], $types) && is_numeric($_GET['nr'])) {
	/* check if this attempt exists and was made by the current user */
	$gcheckuser =  mysqli_query($db,"SELECT `uid` from lcwo_".$_GET['type']."results WHERE `nr` = '".$_GET['nr']."';");
	
	if (!$gcheckuser) {
		echo "Error: ".mysqli_error($db);
	}

	$checkuser = mysqli_fetch_row($gcheckuser);

	if ($checkuser[0] == $_SESSION['uid']) {
		if (mysqli_query($db,"DELETE from lcwo_".$_GET['type']."results WHERE `nr` = '".$_GET['nr']."';")) {
			echo "<p>".l('attemptdeleted')."</p>";
				}
		else {
			echo mysqli_error($db);
		}
	}
	else {
		echo"<p>Error: Attempt doesn't exist or was made by another user!</p>";
	}

}
else {
	echo "<p>Erroneous data.</p>";
}


?>
