<h2><a name="calls"><? echo l('callsigntraining') ?></a></h2>
<?

    if (in_array($_GET['call'], array("1", "0"))) {
        $_SESSION['highscores']['callsshowall'] = $_GET['call'];
    }

    if (!isset($_SESSION['highscores']['callsshowall'])) {
        $_SESSION['highscores']['callsshowall'] = 0;
    }

	if ($_SESSION['highscores']['callsshowall'] == 1) {
		$limit = "99999";
	}
	else {
		$limit = "20";
	}

	// Frickel, frickel.
	if ($whereingroup) {
	$whereingroup3 = preg_replace('/uid/', 'id', $whereingroup2);
	}
	else {
		$whereingroup3 = '1';
	}

$top = mysqli_query($db,"
		select 
			lcwo_users.username as username, 
			lcwo_callsignsresults.uid,
			max(lcwo_callsignsresults.score) as score,
			max(lcwo_callsignsresults.max) as max,
			count(*) as att
			from lcwo_users 
			INNER JOIN lcwo_callsignsresults 
			ON lcwo_users.id = lcwo_callsignsresults.uid 
			where lcwo_callsignsresults.valid = 1 
			and $whereingroup3
			group by uid order by score desc limit $limit;
	");

	if (!$top) {
		echo "ERROR! ".mysqli_error($db);
		return;
	}
						
	echo "<table><tr><th>&nbsp;#&nbsp;</th><th>".l('score')."</th><th>".l('username')."</th>"
			."<th>".l('wpm')."</th><th>".l('attempts')."</th></tr>\n";
	$poscount=0;
	$highlightme=1;
	while ($o = mysqli_fetch_object($top)) {
			$poscount++;
			if ($highlightme && ($o->username == $_SESSION['username'])) {
				$color = " bgcolor='#dddddd'";
			}
			else {
				$color = '';
			}
			echo
			"<tr $color><td>$poscount</td><td>$o->score</td><td><a href=\"/profile/$o->username\">$o->username</a></td><td>$o->max</td><td>$o->att</td></tr>\n";
	}
	echo "</table>\n";


	if ($_SESSION['highscores']['callsshowall'] == 1) {
		echo '<a class="sLink" href="/highscores'.$thisgroup.'/call/0#calls">'.l('hide').'</a>';
	}
	else {
		echo '<a class="sLink" href="/highscores'.$thisgroup.'/call/1#calls">'.l('showall2').'</a>';
	}
	
?>

