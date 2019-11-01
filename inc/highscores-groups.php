<h2><a name="groups"><? echo l('codegroups') ?></a></h2>

<?

    if (in_array($_GET['gall'], array("1", "0"))) {
        $_SESSION['highscores']['groupsshowall'] = $_GET['gall'];
    }

    if (!isset($_SESSION['highscores']['groupsshowall'])) {
        $_SESSION['highscores']['groupsshowall'] = 0;
    }

?>



<p><strong> <? echo l('fastest90acc'); ?> </strong></p>

<table width="100%">
<tr>
<?
	$arr = array('letters', 'figures', 'mixed');

	if ($_SESSION['highscores']['groupsshowall'] == 1) {
		$limit = "";
	}
	else {
		$limit = "LIMIT 20";
	}

	foreach ($arr as $what) {

    $gr = mysqli_query($db,"SELECT lcwo_users.username, 
					max(lcwo_groupsresults.eff) as max 
					FROM lcwo_users 
					INNER JOIN lcwo_groupsresults 
					ON lcwo_users.id = lcwo_groupsresults.uid 
					WHERE `accuracy` > 90 and mode='$what'
					and valid='1'
					and $whereingroup2
					GROUP BY lcwo_users.username
					ORDER BY max desc 
					$limit;");


		if (!$gr) {
			die("Error");
		}

		echo "<td valign=\"top\" width=\"33%\">
		<table width=\"100%\">
		<tr><th colspan=\"2\">".l($what)."</th></tr>
		<tr><th>".l('wpm')."</th><th>User</th></tr>\n";

		while ($f = mysqli_fetch_row($gr)) {
				echo "<tr><td>$f[1]</td><td><a
				href=\"/profile/$f[0]\">$f[0]</a></td></tr>\n";
		}
		
		echo "</table></td>\n"	;

	} /* foreach */

?>
</tr>
</table>

<?

if ($_SESSION['highscores']['groupsshowall'] == 1) {
    echo '<a class="sLink" href="/highscores'.$thisgroup.'/gall/0#groups">'.l('hide').'</a>';
}
else {
    echo '<a class="sLink" href="/highscores'.$thisgroup.'/gall/1#groups">'.l('showall2').'</a>';
}
?>

