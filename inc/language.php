<table width="90%">
<tr><th class="tborder">Language</th></tr>
<tr>
<td class="tborder">
	<table width="100%">
<?

$i = 0;

foreach ($langs as $l) {
	if ($l == "cw") { continue; }
	if ($l == $_SESSION['lang']) {
		$b1 = "<strong>";
		$b2 = "</strong>";
	}
	else {
		$b1 = ''; $b2 = '';
	}
	$i++;
	if ($i == 1) { echo "<tr>"; }
	echo "<td><a href=\"/$l/".$p."\" title=\"".$enlangnames[$l]."\">$b1 ".$langnames[$l]." $b2</a></td>";
	if ($i == 2) { echo "</tr>"; $i =  0;}
}
?>

	</table>
</td>
</tr>
</table>


