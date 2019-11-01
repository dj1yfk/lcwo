<?
echo "<h2>Bestenliste f&uuml;r Benutzerdefinierte 5er-Gruppen</h2>\n";
echo "<p>(Spezielle Funktion f&uuml;r diese Benutzergruppe.)</p>\n";


?>

<table>
<tr><th>&oslash; <? echo l(accuracy) ?></th><th><? echo l(username) ?></th>
<th>&oslash; <? echo l(effspeed); ?></th><th><? echo l(attempts); ?></th></tr>

<?
	$query = mysqli_query($db, "select member from lcwo_groupmembers where gid=19");

	while ($m = mysqli_fetch_row($query)) {
		$validuids .= ($m[0].", ");
	}

	$validuids .= "0";


	$gr = mysqli_query($db, "select lcwo_users.username, 
			round(avg(lcwo_groupsresults.eff),1) as avgspeed, 
			round(avg(lcwo_groupsresults.accuracy),1) as avgacc, 
			count(*) as cnt 
			from lcwo_users 
			INNER JOIN lcwo_groupsresults 
			ON lcwo_users.id = lcwo_groupsresults.uid 
			where (lcwo_groupsresults.uid in ($validuids) 
			AND lcwo_groupsresults.mode = 'custom')
			group by uid 
			order by avgacc desc 
			$limit;");

	if (!$gr) { echo mysqli_error($db); }

	while ($f = mysqli_fetch_row($gr)) {
			echo "<tr><td>$f[2]</td><td><a href=\"?p=profile&u=$f[0]\">$f[0]</a></td><td>$f[1]</td><td>$f[3]</td></tr>";
	}

?>
</table>





