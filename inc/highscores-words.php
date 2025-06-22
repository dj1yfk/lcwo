<h2><a name="words"><? echo l('wordtraining') ?></a></h2>
<?

    if (in_array($_GET['word'], array("1", "0"))) {
        $_SESSION['highscores']['wordsshowall'] = $_GET['word'];
    }

    if (!isset($_SESSION['highscores']['wordsshowall'])) {
        $_SESSION['highscores']['wordsshowall'] = 0;
    }

	if ($_SESSION['highscores']['wordsshowall'] == 1) {
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
			lcwo_wordsresults.uid,
			max(lcwo_wordsresults.score) as score,
			max(lcwo_wordsresults.max) as max,
			count(*) as att
			from lcwo_users 
			INNER JOIN lcwo_wordsresults 
			ON lcwo_users.id = lcwo_wordsresults.uid 
			where lcwo_wordsresults.valid = 1 
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
	while ($o = mysqli_fetch_object($top)) {
			$poscount++;
			if ($highlightme && ($o->username == $_SESSION['username'])) {
				$color = " class='highscorehighlight' ";
			}
			else {
				$color = '';
			}
			echo
			"<tr $color><td>$poscount</td><td>$o->score</td><td><a href=\"/profile/$o->username\">$o->username</a></td><td>$o->max</td><td>$o->att</td></tr>\n";
	}
	echo "</table>\n";


	if ($_SESSION['highscores']['wordsshowall'] == 1) {
		echo '<a class="sLink" href="/highscores'.$thisgroup.'/word/0#calls">'.l('hide').'</a>';
	}
	else {
		echo '<a class="sLink" href="/highscores'.$thisgroup.'/word/1#calls">'.l('showall2').'</a>';
	}
	
?>

