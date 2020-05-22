<?

if ($_SESSION['uid'] == TESTUSER) {
	unset($_GET['newimage']);
	unset($_GET['newaboutme']);
}

if ($_GET['newimage']) {
	uploadimage("profile");
}
else if ($_GET['newaboutme']) {
	newaboutmetext();
}
else if ($_GET['u']) {
	if ($_POST['showstat1']) {
		handleshowstat();
	}
	showprofile();
}
else if (!$_GET['u']) {
	echo "<meta http-equiv=\"refresh\" content=\"0;url=/\"/>";
	return;
}


function showprofile () {
global $db;

global $langnames;
global $enlangnames;
		
if (preg_match('/^[a-zA-Z0-9_-]+$/', $_GET['u'])) {
	$username = $_GET['u'];
}
else {
	echo "Invalid / malformed username.";
	return;
}

$uq = mysqli_query($db,"SELECT id, name, location, signupdate, koch_lesson, lang, profileaboutme, show_ministat from lcwo_users where username = '$username'");

if (!$uq) {
	echo "error: ".mysqli_error($db);
	return;
}

$user = mysqli_fetch_object($uq);

if (!$user->id) {
	echo "Invalid username.";
	return;
}


/* find out which groups the user is a member of */
$getgroups = mysqli_query($db,"
	SELECT lcwo_usergroups.groupname,
			lcwo_groupmembers.gid 
			FROM lcwo_usergroups 
			INNER JOIN lcwo_groupmembers 
			ON lcwo_groupmembers.gid = lcwo_usergroups.gid 
			WHERE lcwo_groupmembers.member = '$user->id';");

if (!$getgroups) {
	echo "error: ".mysqli_error($db);
	return;
}
	
while ($g = mysqli_fetch_object($getgroups)) {
		$groups .= '<a href="/usergroups/'.$g->gid.'">'.$g->groupname."</a>, ";
}

$groups = mb_substr($groups, 0, mb_strlen($groups)-2);


?>


<h1>LCWO <? echo l('myprofile').": ".$username ?> </h1>

<table width="85%">
<tr>
<td rowspan="15" width=140 valign="top">
<?
if (file_exists("img/userimage".$user->id.".jpg")) {
?>
<img src="/img/userimage<? echo $user->id ?>.jpg" alt="[<? echo $username ?>]">
<?
}
else {
?>
<img src="/img/userimage0.png" alt="[no user image]">
<?
}
?>
</td>
<td width=15%><strong><? echo l('name') ?>:</strong></td><td><? echo $user->name ?> </td></tr>
<tr><td><strong><? echo l('location') ?>:</strong></td><td><? echo
$user->location ?> </td></tr>
<tr><td><strong><? echo l('language') ?>:</strong></td><td><? echo
$langnames[$user->lang]; if ($user->lang != "en") { echo
" (".$enlangnames[$user->lang].")";} ?> </td></tr>
<tr><td><strong><? echo l('lesson') ?>:</strong></td><td><? echo
$user->koch_lesson ?> </td></tr>
<tr><td><strong><? echo l('signedup') ?>:</strong></td><td><?
echo $user->signupdate ?> </td></tr>
<tr><td><strong><? echo l('usergroups') ?>:</strong></td><td><?
echo $groups; ?></td></tr>
<tr><td valign="top"><strong><? echo l('aboutme') ?>:</strong></td><td>
<? 
$text_bb = preg_replace('/\n/', '<br>', $user->profileaboutme);
echo bb2html($text_bb);
?></td></tr>
</table>

<?
if ($_SESSION["uid"] && $_SESSION["uid"] != TESTUSER) {
	echo '<br><div><img style="vertical-align:middle" src="/pics/email.png" valign="center">&nbsp;&nbsp;<a rel="nofollow" href="/pmsg/send/'.$username.'">'.l('sendpersonalmessage').'</a> ('.$_SESSION['username'].' &#8594; '.$username.')</div><br>';
}
?>

<?
if ($user->show_ministat or ($user->id == $_SESSION['uid'])) {
	echo "<h2>".l('statsoverview')."</h2>";


	foreach (array("letters", "figures", "mixed") as $mode) {

	# Maximum speed of user at 90%+ accuracy
	$query = mysqli_query($db,"SELECT max(eff) from lcwo_groupsresults
	where uid='$user->id' and accuracy >= 90 and mode='$mode' and valid=1");
	if (!$query) { echo "Error1!".mysqli_error($db); return; }
	$tmp = mysqli_fetch_row($query);
	$cg[$mode][1] = $tmp[0];

	# Placement	
	$query = mysqli_query($db,"SELECT count(distinct uid) from lcwo_groupsresults
							where eff > '".$cg[$mode][1]."' and accuracy>=90
							and mode='$mode' and valid=1;");
	if (!$query) { echo "Error2!".mysqli_error($db); return; }
	$tmp = mysqli_fetch_row($query);
	$cg[$mode][0] = $tmp[0]+1;
	
	# Number of total places
	$query = mysqli_query($db,"SELECT count(distinct uid) from lcwo_groupsresults
							where mode='$mode' and valid=1");
	if (!$query) { echo "Error3!".mysqli_error($db); return; }
	$tmp = mysqli_fetch_row($query);
	$cg[$mode][3] = $tmp[0];
	
	# Number of total attempts 
	$query = mysqli_query($db,"SELECT count(eff) from
								lcwo_groupsresults where uid='$user->id'
								and mode='$mode';");
	if (!$query) { echo "Error4!".mysqli_error($db); return; }
	$tmp = mysqli_fetch_row($query);
	$cg[$mode][2] = $tmp[0];


	if ($tmp[0] == 0) {
		$cg[$mode][0] = "-";
	}
	
	} # foreach  Code Groups Stats

	# Callsign Training and word training

	foreach (array("callsigns", "words") as $what) {
			
	# Maximum speed, score and nr of attempts
	$query = mysqli_query($db,"SELECT max(max), max(score), count(*)
	from lcwo_".$what."results where valid=1 and uid='$user->id';");
	if (!$query) { echo "Error1!".mysqli_error($db); return; }
	$tmp = mysqli_fetch_row($query);
	$foo[$what][1] = $tmp[0];
	$foo[$what][2] = $tmp[1];
	$foo[$what][3] = $tmp[2];

	if ($tmp[2] == 0) {
			$foo[$what][2] = "-";
	}
	
	# Place
	$query = mysqli_query($db,"SELECT count(distinct uid) from lcwo_".$what."results
	where valid = 1 and score > '".$foo[$what][2]."'");
	if (!$query) { echo "Error1!".mysqli_error($db); return; }
	$tmp = mysqli_fetch_row($query);
	$foo[$what][0] = $tmp[0]+1;

	if ($foo[$what][2] == "-") {
		$foo[$what][0] = "-";
	}
	
	
	# Total count
	$query = mysqli_query($db,"SELECT count(distinct uid) from lcwo_".$what."results");
	if (!$query) { echo "Error1!".mysqli_error($db); return; }
	$tmp = mysqli_fetch_row($query);
	$foo[$what][4] = $tmp[0];

	
	} # foreach

	# Plain Text training
	
	# Maximum speed of user at 90%+ accuracy
	$query = mysqli_query($db,"SELECT max(eff) from lcwo_plaintextresults
	where uid='$user->id' and accuracy >= 90");
	if (!$query) { echo "Error1!".mysqli_error($db); return; }
	$tmp = mysqli_fetch_row($query);
	$pt[1] = $tmp[0];

	# find out place
	$query = mysqli_query($db,"SELECT uid, avg(accuracy) as a from
	lcwo_plaintextresults group by uid order by a desc");
	if (!$query) { echo "Error1!".mysqli_error($db); return; }
	$place = 0;
	while ($tmp = mysqli_fetch_row($query)) {
		$place++;
		if ($tmp[0] == $user->id) {		# user itself
			$pt[2] = round($tmp[1],1);
			break;
		}
	}
	if ($place == 0) {
		$pt[0] = "-";
		$pt[2] = "-";
	}
	else {
		$pt[0] = $place;
	}
	
	# Number of total places
	$query = mysqli_query($db,"SELECT count(distinct uid) from lcwo_plaintextresults");
	if (!$query) { echo "Error3!".mysqli_error($db); return; }
	$tmp = mysqli_fetch_row($query);
	$pt[3] = $tmp[0];
	
	# Number of total attempts 
	$query = mysqli_query($db,"SELECT count(eff) from lcwo_plaintextresults where uid='$user->id' ;");
	if (!$query) { echo "Error4!".mysqli_error($db); return; }
	$tmp = mysqli_fetch_row($query);
	$pt[4] = $tmp[0];

	# no attempts?	
	if ($pt[4] == 0) {
		$pt[0] = $pt[2] = "-";
	}

	# QTC training
	# Maximum speed of user at 90%+ accuracy
	$query = mysqli_query($db,"SELECT max(speed) from lcwo_qtcresults
	where uid='$user->id' and qtcs > 8");
	if (!$query) { echo "Error1!".mysqli_error($db); return; }
	$tmp = mysqli_fetch_row($query);
	$qtc[1] = $tmp[0];

	# find out place
	$query = mysqli_query($db,"SELECT uid, avg(qtcs) as a from
	lcwo_qtcresults group by uid order by a desc");
	if (!$query) { echo "Error1!".mysqli_error($db); return; }
	$place = 0;
	while ($tmp = mysqli_fetch_row($query)) {
		$place++;
		if ($tmp[0] == $user->id) {		# user itself
			$qtc[2] = round(10*$tmp[1],1);
			break;
		}
	}
	$qtc[0] = $place;
	
	# Number of total places
	$query = mysqli_query($db,"SELECT count(distinct uid) from lcwo_qtcresults");
	if (!$query) { echo "Error3!".mysqli_error($db); return; }
	$tmp = mysqli_fetch_row($query);
	$qtc[3] = $tmp[0];
	
	# Number of total attempts 
	$query = mysqli_query($db,"SELECT count(qtcs) from lcwo_qtcresults where uid='$user->id' ;");
	if (!$query) { echo "Error4!".mysqli_error($db); return; }
	$tmp = mysqli_fetch_row($query);
	$qtc[4] = $tmp[0];

	if ($qtc[4] == 0) {
		$qtc[0] = $qtc[2] = "-";
	}

	?>
	<table width="45%">
		<tr><th><? echo l('speedpractice'); ?>&nbsp;</th>
			<th colspan="3"><? echo l('place'); ?>&nbsp;</th>
			<th><? echo l('wpm'); ?>&nbsp;</th>
			<th><? echo l('score'); ?>&nbsp;</th>
			<th><? echo l('attempts'); ?>&nbsp;</th>
		</tr>
		<tr><td><? echo l('codegroups'); ?></td>
		</tr>
		<? foreach (array("letters", "figures", "mixed") as $mode) { ?>
		<tr><td>&nbsp;- <? echo l("$mode"); ?>&nbsp;&nbsp;</td>
			<td><? echo $cg[$mode][0]."</td><td>/</td><td>".$cg[$mode][3]; ?> </td>
			<td><? echo $cg[$mode][1]; ?></td><td>-</td>
			<td><? echo $cg[$mode][2]; ?></td>
		</tr>
		<? } ?>
		<tr><td><? echo l('callsigntraining'); ?></td>
			<td><? echo $foo['callsigns']['0']."</td><td>/</td><td>".$foo['callsigns']['4']; ?></td>
			<td><? echo $foo['callsigns']['1']; ?></td>
			<td><? echo $foo['callsigns']['2']; ?></td>
			<td><? echo $foo['callsigns']['3']; ?></td>
		</tr>
		<tr><td><? echo l('wordtraining'); ?></td>
			<td><? echo $foo['words']['0']."</td><td>/</td><td>".$foo['words']['4']; ?></td>
			<td><? echo $foo['words']['1']; ?></td>
			<td><? echo $foo['words']['2']; ?></td>
			<td><? echo $foo['words']['3']; ?></td>
		</tr>
		<tr><th><? echo l('speedpractice'); ?>&nbsp;</th>
			<th colspan="3"><? echo l('place'); ?>&nbsp;</th>
			<th><? echo l("max")." ".l('wpm'); ?>&nbsp;</th>
			<th>&oslash; <? echo l('accuracy'); ?>&nbsp;</th>
			<th><? echo l('attempts'); ?>&nbsp;</th>
		</tr>
		<tr><td><? echo l('plaintexttraining'); ?></td>
			<td><? echo $pt[0]."</td><td>/</td><td>".$pt[3]; ?></td>
			<td><? echo $pt[1]; ?></td>
			<td><? echo $pt[2]; ?></td>
			<td><? echo $pt[4]; ?></td>
		</tr>
		<tr><td><? echo l('qtctraining'); ?></td>
			<td><? echo $qtc[0]."</td><td>/</td><td>".$qtc[3]; ?></td>
			<td><? echo $qtc[1]; ?></td>
			<td><? echo $qtc[2]; ?></td>
			<td><? echo $qtc[4]; ?></td>
		</tr>
		</tr>
	</table>

	<?

	
	# Public or not?
	if ($user->id == $_SESSION['uid']) {
		echo "<br><form method=\"POST\" action=\"/profile/$username\">";
		echo "<input type=\"hidden\" name=\"showstat1\" value=1>";
		echo "<input type=\"checkbox\" name=\"showstat2\" 
		value=\"1\" ";
		echo ($user->show_ministat ? " checked " : "");
		echo "> ".l('visiblepublic')." &nbsp; &nbsp; &nbsp; <input
		type=\"submit\" value=\"".l('change')."\"></form><br>";
	}

	
	
	
	
}
	if ($_SESSION['uid'] == $user->id && $_SESSION['uid'] != TESTUSER) {
?>
		<h2><?=l('editprofiledescription');?></h2>
		<form action="/profile/newaboutme" method="POST">
		<table>
		<tr>
		<td>
		<textarea cols=50 rows=5 name="text"><? echo $user->profileaboutme?></textarea><br>
		</td>
		<td valign="top">
		<? echo l('allowedtags') ?><br>
    	<b>[b]<? echo l('boldtext') ?>[/b]</b><br>
	    <i>[i]<? echo l('italictext') ?>[/i]</i>
		<br><br><br><br>
		
		<input type="submit" value="<? echo l('submit',1);?>">
		
		</td>
		</tr>
		</table>
		</form>

<h2><? echo l('uploadnewpicture'); ?></h2>
<form action="/profile/newimage" method="POST" enctype="multipart/form-data">
	<p>JPG-file: <input name="file" type="file" size="50" maxlength="100000"
	  accept="image/*">
	</p>
	<input type="submit" value="<? echo l('submit'); ?>">
</form>
<?
	}


	
} // showprofile


function newaboutmetext () {
	global $db;

	$text = esc($_POST['text']);
	$text = strip_tags($text);
	if (mb_strlen($text) > 62000) {
		$text = mb_substr($text, 0, 62000);
	}
	
	// Possible Spam 
	if (strpos($text, "url") != FALSE) {
		$mailtext = delete_user_url();
		$mailtext .= "\n".BASEURL."/profile/".$_SESSION['username']."\n";

		mail (ADMINMAIL, "LCWO: Possible Spam ", $mailtext , "From: LCWO Robot <".ADMINMAIL.">");
	}

	if (!mysqli_query($db,"UPDATE lcwo_users set profileaboutme='$text' where
			id='$_SESSION[uid]'")) {
		echo "ERROR: ".mysqli_error($db);;
	}
	else {
?>
<a href="/profile/<? echo $_SESSION['username']; ?>">OK</a>
<script type="text/javascript">
window.location.href =
'/profile/<? echo $_SESSION['username']; ?>';
</script>
<?
	}
}



function handleshowstat () {
	global $db;	
	# Show stat to public
	if ($_POST['showstat2'] == 1) {
		mysqli_query($db,"UPDATE lcwo_users set show_ministat=1 where id='".$_SESSION['uid']."'");
		$_SESSION['show_ministat'] = 1;
	}
	else {
		mysqli_query($db,"UPDATE lcwo_users set show_ministat=0 where id='".$_SESSION['uid']."'");
		$_SESSION['show_ministat'] = 0;
		
	}
	
}

?>
<div class="vcsid">$Id: profile.php 168 2012-09-06 17:42:01Z dj1yfk $</div>


