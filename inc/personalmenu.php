
<table width="100%">
<tr><th class="personalmenu tborder"><? echo l('menufor')." ".$_SESSION['username']; ?></th></tr>
<tr><td class="tborder">
<a href="/"><strong><? echo l('home') ?></strong></a> - 
<a href="/news"><strong><? echo l('news') ?></strong></a>
<br><br>

<? echo l('kochmethodcourse'); ?>
<ul>
<li><a href="/courseintro"><? echo l('introduction'); ?></a></li>
<li><a href="/courselesson"><? echo l('lessons')." (".$_SESSION['koch_lesson']; ?>/40)</a></li>
<li><a href="/morsemachine">MorseMachine</a></li>
</ul>
<? echo l('speedpractice'); ?>
<ul>
<li><a href="/groups"><? echo l('codegroups'); ?></a></li>
<li><a href="/plaintext"><? echo l('plaintexttraining'); ?></a></li>
<li><a href="/wordtraining"><? echo l('wordtraining'); ?></a></li>
<li><a href="/callsigns"><? echo l('callsigntraining'); ?></a></li>
<li><a href="/qtc"><? echo l('qtctraining'); ?></a></li>
</ul>

<? echo l('misc'); ?>
<ul>
<li><a href="/text2cw"><? echo l('text2cw'); ?></a></li>
<li><a href="/download"><? echo l('downloadpracticefiles'); ?></a></li>
<li><a href="/transmit">TX training</a></li>
</ul>


</td></tr>
<tr><th class="tborder"><? echo l('account'); ?></th></tr>
<tr>
<td class="tborder">
	<ul>
	<li><a href="/cwsettings"><? echo l('changecwsettings'); ?></a></li>
	<li><a href="/account"><? echo l('editaccount'); ?></a></li>  
	<li><a href="/profile/<? echo $_SESSION['username'] ?>"><?  echo l('myprofile'); ?></a></li>  
	</ul>
</td>
</tr>

<?
	$query = mysqli_query($db,"SELECT count(*) from lcwo_groupmembers where member='".$_SESSION['uid']."'");
	$tmp = mysqli_fetch_row($query);
	if ($tmp[0] != 0) {
	?>
<tr><th class="tborder"><? echo l('usergroups'); ?></th></tr>
<tr>
<td class="tborder">
	<ul>
<?
		$query = mysqli_query($db, "SELECT lcwo_usergroups.groupname,
			lcwo_usergroups.gid from lcwo_usergroups
			INNER JOIN lcwo_groupmembers 
			ON
			lcwo_groupmembers.gid = lcwo_usergroups.gid
			where lcwo_groupmembers.member = '".$_SESSION['uid']."' 
			order by lcwo_usergroups.gid asc
			");

			if (!$query) {
				echo "ERROR: ".mysqli_error($db);
				exit(1);
			}

			
			while ($line = mysqli_fetch_row($query)) {
					echo "<li><a href=\"/usergroups/".$line[1]."\">".$line[0]."</a></li>\n";
			}
?>

	</ul>
</td>
</tr>
<?

} // if member of usergroups
?>

</table>


