<?


session_start();
header('Content-Type: text/xml; charset=utf-8');
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Pragma: no-cache");


include("../inc/functions.php");

if (!$_SESSION['uid']) {
	echo "Sorry, this requires a login.";
	return 0;
}

$type = $_POST['type'];
$val1 = $_POST['val1'];
$val2 = $_POST['val2'];
$val3 = $_POST['val3'];


if (is_numeric($val1) and is_numeric($val2) and is_numeric($val3) and 
		in_array($type, array('callsigns', 'words'))) {
	include("../inc/connectdb.php");


	switch ($type) {
		case 'callsigns':
			$table = "lcwo_callsignsresults";
			$val1f = "max";
			$val2f = "score";
			$val3f = "valid";
			break;
		case 'words':
			$table = "lcwo_wordsresults";
			$val1f = "max";
			$val2f = "score";
			$val3f = "valid";
			break;
		default:
			exit(1);
	}


	# Check for new Highscore
	$query = mysqli_query($db,"SELECT max($val2f) from $table where
			`uid` = '".$_SESSION['uid']."'");
	if (!$query) {
		echo "Database failure: ".mysqli_error();
		return;
	}
	$highscore = mysqli_fetch_row($query);
	if ($highscore[0] < $val2) {
		echo "<strong>".l('newpersonalhighscore')."</strong><br><br>";
	}
	
	# Add score...
	$query = mysqli_query($db,"INSERT into $table (`uid`,
		`$val1f`, `$val2f`, `$val3f`, `time`) VALUES
		('$_SESSION[uid]','$val1','$val2', '$val3', NOW())");

	if (!$query) {
		echo "Database failure: ".mysqli_error($db);
		return;
	}
	else {
		echo "<strong>".l('addedscore')."</strong><br><br>";
	}


	# Tell position on highscore list...
	$query = mysqli_query($db,"select count(distinct uid) from $table where `$val2f` > '$val2';");
	$thisposition = mysqli_fetch_row($query);
	$thisposition[0]++;
	
	$query = mysqli_query($db,"select count(distinct uid) from $table where `$val2f` > '$highscore[0]';");
	if (!$query) {
		echo "Database failure: ".mysqli_error($db);
		return;
	}
	$bestposition = mysqli_fetch_row($query);
	$bestposition[0]++;

	echo l('highscoreposthis')." ".$thisposition[0]."<br>";
	echo l('highscoreposbest')." ".$bestposition[0]."<br><br>";
	

	
}
else {
	echo "<strong>Error. Invalid values.</strong>";
}

?>
