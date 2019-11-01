<h2><a name="qtc"><? echo l('qtctraining') ?></a></h2>

<?
	/* view settings */

	if (is_numeric($_GET['qa'])) {
		$_SESSION['highscores']['qtcattempts'] = $_GET['qa'];
	}

	if (!isset($_SESSION['highscores']['qtcattempts'])) {
		$_SESSION['highscores']['qtcattempts'] = 50;
	}

    if (in_array($_GET['qall'], array("1", "0"))) {
        $_SESSION['highscores']['qtcshowall'] = $_GET['qall'];
    }

    if (!isset($_SESSION['highscores']['qtcshowall'])) {
        $_SESSION['highscores']['qtcshowall'] = 0;
    }

?>

<? selectnrbox(0, 1000, 25,
$_SESSION['highscores']['qtcattempts'], 'qa', 'highscores',
'GET', $_GET['group']); ?>

<table>
<tr><th>&oslash; <? echo l('accuracy') ?></th><th><? echo l('username') ?></th>
<th>&oslash; <? echo l('wpm'); ?></th><th><? echo l('attempts'); ?></th></tr>

<?

	unset($gr, $r, $acc, $spd, $att, $entry);

	if ($_SESSION['highscores']['qtcshowall'] == 1) {
		$limit = "";
	}
	else {
		$limit = "limit 10";
	}

	$gr = mysqli_query($db,"
			select lcwo_users.username,
			round(avg(lcwo_qtcresults.speed),1) as avgspeed,
			round(avg(lcwo_qtcresults.qtcs),1) as avgacc,
			count(*) as cnt
			from lcwo_users
			INNER JOIN lcwo_qtcresults
			ON lcwo_users.id = lcwo_qtcresults.uid
			where $whereingroup2
			group by uid
			having cnt > ".$_SESSION['highscores']['qtcattempts']."
			order by avgacc desc
			$limit;");


    if (!$gr) { echo mysqli_error(); }

    while ($f = mysqli_fetch_row($gr)) {
			echo "<tr><td>".(10*$f[2])."</td><td><a href=\"/profile/$f[0]\">$f[0]</a></td><td>$f[1]</td><td>$f[3]</td></tr>";
    }


?>
</table>

<?

if ($_SESSION['highscores']['qtcshowall'] == 1) {
	echo '<a class="sLink" href="/highscores'.$thisgroup.'/qall/0#qtc">'.l('hide').'</a>';
}
else {
	echo '<a class="sLink" href="/highscores'.$thisgroup.'/qall/1#qtc">'.l('showall2').'</a>';
}
?>
				




<p><strong> <? echo l('fastest90acc'); ?> </strong></p>

<?
    $gr = mysqli_query($db,"SELECT lcwo_users.username, 
						max(lcwo_qtcresults.speed) as max 
						FROM lcwo_users 
						INNER JOIN lcwo_qtcresults 
						ON lcwo_users.id = lcwo_qtcresults.uid 
						WHERE (`qtcs` > 8 AND
						$whereingroup2)
						GROUP BY lcwo_users.username
						ORDER BY max desc 
						$limit;");

    if (!$gr) {
        echo "$validuids3 Error!".mysqli_error($db);
		return;
    }

    echo "<table><tr><th>".l('wpm')."</th><th>".l('username')."</th></tr>\n";

    while ($f = mysqli_fetch_array($gr)) {
        echo "<tr><td>$f[1]</td><td><a href=\"/profile/$f[0]\">$f[0]</a></td></tr>\n";
    }
    echo "</table>\n"

?>


