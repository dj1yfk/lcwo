<?

$start = microtime(true);

$groupmembers = 0;

if (isint($_GET['group'])) {

	if (is_private_group($_GET['group']) &&
	!is_member_of($_SESSION['uid'], $_GET['group'])) {
		echo "<p>".l('privategroup')."</p>";
		return;
	}
		
	$getgroupmembers = mysqli_query($db,"SELECT  member from
	lcwo_groupmembers where gid='$_GET[group]';");
	$groupmembersarray = array();
	while ($gm = mysqli_fetch_row($getgroupmembers)) {
		array_push($groupmembersarray, $gm[0]);
	}
	$groupmembers = join(",", $groupmembersarray);

	$getgroupname = mysqli_query($db,"SELECT groupname from
	lcwo_usergroups where gid='$_GET[group]';");
	if (!($groupnamearray = mysqli_fetch_row($getgroupname))) {
		echo "Error. Group invalid!";
		return;
	}
	else {
		$groupname = $groupnamearray[0];
	}
	

	$thisgroupnr = (int) ($_GET['group']);
	$thisgroup = "/group/".$thisgroupnr;


	
}
else {
	$thisgroup = "";
}

# Stuff for SQL queries...
	if ($groupmembers) {
		$whereingroup = "WHERE uid in ($groupmembers)";
	}
	else {
		$whereingroup = "";
	}

	if ($whereingroup) {
		$whereingroup2 = preg_replace('/WHERE/', '', $whereingroup);
	}
	else {
		$whereingroup2 = 1;
	}




if (!$thisgroup) {
	echo "<h1>LCWO ".l('highscores')."</h1>";
}
else {
	echo "<h1>".l('usergrouphighscores')." ".$groupname."</h1>";
	echo '<p><a href="/usergroups/'.$thisgroupnr.'">'.l('backtogroupsite').'</a></p>';
}


$rett = "before table: " . ($start - microtime(true)) ."\n";

?>


<table width="100%" >
<tr>
<td width="45%" valign="top">
<? include("highscores-groups.php");?>
<? $rett .= "after groups: " . (microtime(true) - $start) ."\n"; ?>
</td>

<td>
</td>

<td width="45%" valign="top">
<? include("highscores-plain.php");?>
<? $rett .= "after plain: " . (microtime(true)-$start) ."\n"; ?>
</td>
</tr>

<tr>
<td width="45%" valign="top">
<br>
<? include("highscores-rufz.php");?>
<? $rett .= "after rufz: " . (microtime(true) - $start) ."\n"; ?>
</td>

<td width="10%">
</td>

<td width="45%" valign="top">
<br>
<?  include("highscores-words.php");?>
<? $rett .= "after words: " . (microtime(true) - $start) ."\n"; ?>
</td>
</tr>

<tr>
<td width="45%" valign="top">
<br>
<? include("highscores-qtc.php");?>
<? $rett .= "after qtc: " . (microtime(true) - $start) ."\n"; ?>
</td>

<td width="10%">
</td>

<td width="45%" valign="top">
<br>
<? include("highscores-koch.php");?>
<? $rett .= "after koch: " . (microtime(true) - $start) ."\n"; ?>
</td>
</tr>


</table>
<!--
<?=$rett; ?>
-->
<div class="vcsid">$Id: highscores.php 251 2014-11-12 18:35:55Z dj1yfk $</div>

