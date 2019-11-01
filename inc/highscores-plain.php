<h2><a name="plain"><? echo l('plaintexttraining') ?></a></h2>

<?
	/* view settings */

	if (is_numeric($_GET['pa'])) {
		$_SESSION['highscores']['plaintextattempts'] = $_GET['pa'];
	}

	if (!isset($_SESSION['highscores']['plaintextattempts'])) {
		$_SESSION['highscores']['plaintextattempts'] = 200;
	}

    if (in_array($_GET['pall'], array("1", "0"))) {
        $_SESSION['highscores']['plainshowall'] = $_GET['pall'];
    }

    if (!isset($_SESSION['highscores']['plainshowall'])) {
        $_SESSION['highscores']['plainshowall'] = 0;
    }

?>

<? selectnrbox(0, 1000, 10,
$_SESSION['highscores']['plaintextattempts'], 'pa', 'highscores',
'GET', $_GET['group']); ?>

<table>
<tr><th>&oslash; <? echo l('accuracy') ?></th><th><? echo l('username') ?></th>
<th>&oslash; <? echo l('effspeed'); ?></th><th><? echo l('attempts'); ?></th></tr>

<?
	/* count attempts per user, only allow users with more than $_SESSION['highscpres'][plaintextattempts] */
	unset($gr, $r, $acc, $spd, $att, $entry);

	if ($_SESSION['highscores']['plainshowall'] == 1) {
		$limit = "";
	}
	else {
		$limit = "limit 10";
	}

	$gr = mysqli_query($db,"
			select lcwo_users.username,
			round(avg(lcwo_plaintextresults.eff),1) as avgspeed,
			round(avg(lcwo_plaintextresults.accuracy),1) as avgacc,
			count(*) as cnt
			from lcwo_users
			INNER JOIN lcwo_plaintextresults
			ON lcwo_users.id = lcwo_plaintextresults.uid
			where $whereingroup2
			group by uid
			having cnt > ".$_SESSION['highscores']['plaintextattempts']."
			order by avgacc desc $limit;");

    if (!$gr) { echo mysqli_error(); }

    while ($f = mysqli_fetch_row($gr)) {
			echo "<tr><td>$f[2]</td><td><a href=\"/profile/$f[0]\">$f[0]</a></td><td>$f[1]</td><td>$f[3]</td></tr>";
    }


?>
</table>

<?

if ($_SESSION['highscores']['plainshowall'] == 1) {
	echo '<a class="sLink" href="/highscores'.$thisgroup.'/pall/0#plain">'.l('hide').'</a>';
}
else {
	echo '<a class="sLink" href="/highscores'.$thisgroup.'/pall/1#plain">'.l('showall2').'</a>';
}
?>
				




<p><strong> <? echo l('fastest90acc'); ?> </strong></p>


<?
    $gr = mysqli_query($db,"SELECT lcwo_users.username, 
						max(lcwo_plaintextresults.eff) as max 
						FROM lcwo_users 
						INNER JOIN lcwo_plaintextresults 
						ON lcwo_users.id = lcwo_plaintextresults.uid 
						WHERE (`accuracy` > 90 AND
						$whereingroup2)
						GROUP BY lcwo_users.username
						ORDER BY max desc 
						$limit;");

    if (!$gr) {
        die("Error!");
    }

    echo "<table><tr><th>".l('wpm')."</th><th>".l('username')."</th></tr>\n";

    while ($f = mysqli_fetch_array($gr)) {
        echo "<tr><td>$f[1]</td><td><a href=\"/profile/$f[0]\">$f[0]</a></td></tr>\n";
    }
    echo "</table>\n"

?>


