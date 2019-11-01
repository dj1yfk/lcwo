<?
# For a real deployment, you will most likely want to use a custom file here, which is not under version control.
if (file_exists($_SERVER['DOCUMENT_ROOT']."/inc/connectdb.custom.php")) {
	include($_SERVER['DOCUMENT_ROOT']."/inc/connectdb.custom.php");
}
else {
	# default values; this works in the docker container
	$db = mysqli_connect("localhost","lcwo","lcwo", "LCWO") or die ("<h1>Sorry: Could not connect to database.</h1>");
}
?>
