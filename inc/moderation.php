<?
if (!in_array($_SESSION['uid'], $g_moderators)) {
	echo ":-)";
	return;
}
?>
<h1>Moderation</h1>

<?

if ($_POST['action'] == "OK" and isint($_POST['post']+0)) {
	approvemsg($_POST['post']+0);
}
elseif ($_POST['action'] == "WL" and isint($_POST['post']+0)) {
	approvemsg($_POST['post']+0);
	whitelist($_POST['post']+0);
}
elseif ($_POST['action'] == "DEL" and isint($_POST['post']+0)) {
	deletemsg($_POST['post']+0);
}
elseif ($_POST['action'] == "BAN" and isint($_POST['post']+0)) {
    banuser($_POST['post']+0);
	deletemsg($_POST['post']+0);
}

show_queue();
?>

<?
function show_queue () {
global $db;
?>
<h2>Queue</h2>



<table class="tborder">
<?
$q = mysqli_query($db,"select * from lcwo_posts where approved = 0;");

$count = 0;
while ($o = mysqli_fetch_object($q)) {
		$count++;

$info = uid2info($o->uid);
$mtext = $o->text;
$mtext = preg_replace("/\n/", "<br>", $mtext);
$mtext = bb2html($mtext);

echo "<tr><td class=\"tborder\" valign=\"top\" width=\"20%\">".$info->username." - ".$info->name."<br>".da($o->time)."<br>Post #".$o->id."</td><td class=\"tborder\" valign=\"top\" width=\"70%\">$mtext</td><td class=\"tborder\">
		<form action=\"/moderation\" method=\"POST\">
		<input type='submit' name='action' value='OK'> 
		<input type='submit' name='action' value='DEL'> 
		<input type='submit' name='action' value='WL'> 
		<input type='submit' name='action' value='BAN'> 
		<input type='hidden' name='post' value='$o->id'>
		</form>
		<br><a href='/forum/".$o->tid."'>Thread</a></td></tr>\n";
}

if (!$count) {
		echo "<tr><td colspan=3>No posts in queue</td></tr>\n";
}

?>
</table>


<script>
	window.setTimeout("window.location = window.location;", 10*60*1000);
	document.title = '<?=$count;?> posts in queue';
</script>


<?
} // function show_queue


function approvemsg($m) {
global $db;
	$q = mysqli_query($db,"update lcwo_posts set approved = 1 where id='$m';");
	if (!$q) {
			echo "<p>Error: </p>".mysqli_error($db);
	}
	else {
			echo "<p>Post $m approved.</p>";
	}
}

function deletemsg($m) {
global $db;
	$q = mysqli_query($db,"delete from lcwo_posts where id='$m' and approved=0 limit 1;");
	if (!$q) {
			echo "<p>Error: </p>".mysqli_error($db);
	}
	else {
			echo "<p>Post $m deleted.</p>";
	}
}

function whitelist ($m) {
global $db;
		$q = mysqli_query($db,"select uid from lcwo_posts where id='$m'");
		$o = mysqli_fetch_object($q);
		print "Whitelisted UID ".$o->uid."<br>";

		$q = mysqli_query($db,"update lcwo_users set forum_whitelist = 1 where ".
				" id=".$o->uid);
}

function banuser ($m) {
    global $db;
    $q = mysqli_query($db,"select uid from lcwo_posts where id='$m'");
    $o = mysqli_fetch_object($q);
    print "Banning user ".$o->uid." for 24 hours<br>";;

    $ts = time() + 24*60*60;

    $q = mysqli_query($db,"update lcwo_users set forum_whitelist = $ts where id=".$o->uid);
}


