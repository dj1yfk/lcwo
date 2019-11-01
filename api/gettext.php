<?php
	session_start();

	if (is_numeric($_GET[nr])) {
		$nr = $_GET[nr];
	}
	
	header('Content-type: text/plain');
	header("Content-Disposition: attachment; filename=\"lcwo-$nr.txt\"");
	echo "LCWO practice text ($nr) for $_SESSION[username].\r\n\r\n";
	echo wordwrap($_SESSION[downloadtexts][$nr], 60, "\r\n");
?>

