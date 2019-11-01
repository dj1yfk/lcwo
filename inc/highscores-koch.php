<h2><a name="koch"><? echo l('kochmethodcourse'); ?></a></h2>

<p><?=l("results30days");?></p>
<?

	/* view settings */

	if (isint($_GET['ka'])) {
		$_SESSION['highscores']['kochattempts'] = $_GET['ka'];
	}

	if (!isset($_SESSION['highscores']['kochattempts'])) {
		$_SESSION['highscores']['kochattempts'] = $groupmembers ? 0 : 50;
	}

	if (in_array($_GET['kall'], array("1", "0"))) {
		$_SESSION['highscores']['kochshowall'] = $_GET['kall'];
	}

	if (!isset($_SESSION['highscores']['kochshowall'])) {
		$_SESSION['highscores']['kochshowall'] = 0;
	}
	
?>


<? selectnrbox(0, 1000, 50,
$_SESSION['highscores']['kochattempts'], 'ka', 'highscores', 'GET',
$_GET['group']); ?>

<table>
<tr><th>&oslash; <? echo l('accuracy') ?></th><th><? echo l('username') ?></th>
<th>&oslash; <? echo l('effspeed'); ?></th><th><? echo l('attempts'); ?></th></tr>

<?

	if ($_SESSION['highscores']['kochshowall'] == 1) {
		$limit = "";
	}
	else {
		$limit = "limit 10";
	}
	
	$gr = mysqli_query($db,"
			select lcwo_users.username, 
			round(avg(lcwo_lessonresults.eff),1) as avgspeed, 
			round(avg(lcwo_lessonresults.accuracy),1) as avgacc, 
			count(*) as cnt 
			from lcwo_users 
			INNER JOIN lcwo_lessonresults 
			ON lcwo_users.id = lcwo_lessonresults.uid 
			where ($whereingroup2
			and time > (NOW() - interval 30 DAY))
			group by uid 
			having cnt > ".$_SESSION['highscores']['kochattempts']."
			order by avgacc desc 
			$limit;");

	if (!$gr) { echo mysqli_error(); }

	while ($f = mysqli_fetch_row($gr)) {
			echo "<tr><td>$f[2]</td><td><a href=\"/profile/$f[0]\">$f[0]</a></td><td>$f[1]</td><td>$f[3]</td></tr>";
	}

?>
</table>

<?

if ($_SESSION['highscores']['kochshowall'] == 1) {
	echo '<a class="sLink" rel="nofollow" href="/highscores'.$thisgroup.'/kall/0#koch">'.l('hide').'</a>';
}
else {
	echo '<a class="sLink" rel="nofollow" href="/highscores'.$thisgroup.'/kall/1#koch">'.l('showall2').'</a>';
}

?>



<p><strong> <? echo l('attempts'); ?> </strong></p>

<?

	$gr = mysqli_query($db,"SELECT lcwo_users.username, 
						count(lcwo_lessonresults.accuracy) as ct 
						FROM lcwo_users 
						INNER JOIN lcwo_lessonresults 
						ON lcwo_users.id = lcwo_lessonresults.uid 
						WHERE (`accuracy` > '0' AND 
						$whereingroup2 AND
						time > (NOW() - interval 30 DAy))
						GROUP BY lcwo_users.username
						ORDER BY ct desc 
						$limit;");

	if (!$gr) {
		echo "Error".mysqli_error();
		return;
	}

	echo "<table><tr><th>".l('attempts')."</th><th>".l('username')."</th></tr>\n";
	
	while ($f = mysqli_fetch_array($gr)) {
		echo "<tr><td>$f[1]</td><td><a
		href=\"/profile/$f[0]\">$f[0]</a></td></tr>\n";
	}
	echo "</table>\n"	
?>

