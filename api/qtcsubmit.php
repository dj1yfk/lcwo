<?

session_start();
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Pragma: no-cache");

if (!$_SESSION[uid]) {
	echo "Sorry, this requires a login.";
	return 0;
}

include("../inc/functions.php");

$speed = $_POST[speed];
$good = $_POST[good];


if (is_numeric($speed) and is_numeric($good)) {
	include("../inc/connectdb.php");

	$query = mysqli_query($db,"INSERT into lcwo_qtcresults (`uid`,
		`speed`, `qtcs`, `time`) VALUES
		('$_SESSION[uid]','$speed','$good', NOW())");

	if (!$query) {
		echo "Database failure: ".mysqli_error();
		return;
	}
	else {
		echo "<strong>".l(addedscore)."</strong>";
	}


	
}
else {
	echo "<strong>Error. Invalid speed or number of QTCs.</strong>";
}

?>
