<?
session_start();

include($_SERVER['DOCUMENT_ROOT']."/inc/functions.php");
include($_SERVER['DOCUMENT_ROOT']."/inc/definitions.php");
include($_SERVER['DOCUMENT_ROOT']."/inc/connectdb.php");

if ($_SESSION['uid'] != ADMIN) {
    echo "Only for admin...";
    return;
}

$q = mysqli_query($db,"select sum(count) from lcwo_mmresults where 1");
$w = mysqli_fetch_row($q);
echo $w[0];

?>
<table>
<tr><td>
<?
echo "<table border=1>";
echo "<tr><th>nr</th><th>user</th><th>lesson</th><th>wpm</th><th>eff</th><th>%</th><th>time</th></tr>";

$q = mysqli_query($db,"select * from lcwo_lessonresults order by nr desc limit 20");

while ($l = mysqli_fetch_row($q)) {
	$kk=0;
	echo "<tr>";
	foreach ($l as $u) {
		echo "<td>";
		if ($kk == 1) {
		    echo uid2uname($u)."\t";
		}
		else if ($kk == 6) {
		    echo substr(da($u), strlen(da($u))-6, 6)."\t";
		}
		else {
		echo "$u\t";
		}
		$kk++;
		echo "</td>";
	}
	echo "</tr>\n";
}

echo "</table>";
?>
</td><td>
<?
echo "<table border=1>";
echo "<tr><th>nr</th><th>user</th><th>lesson</th><th>wpm</th><th>eff</th><th>%</th><th>time</th></tr>";

$q = mysqli_query($db,"select * from lcwo_groupsresults order by nr desc limit 20");

while ($l = mysqli_fetch_row($q)) {
	$kk=0;
	echo "<tr>";
	foreach ($l as $u) {
		echo "<td>";
		if ($kk == 1) {
		    echo uid2uname($u)."\t";
		}
		else if ($kk == 6) {
		    echo substr(da($u), strlen(da($u))-6, 6)."\t";
		}
		else {
		echo "$u\t";
		}
		$kk++;
		echo "</td>";
	}
	echo "</tr>\n";
}

echo "</table>";
?>
</td>
<td>
<?
echo "<table border=1>";
echo "<tr><th>nr</th><th>user</th><th>w</th><th>pts</th></tr>";

$q = mysqli_query($db,"select * from lcwo_callsignsresults order by nr desc limit 20");

while ($l = mysqli_fetch_row($q)) {
	$kk=0;
	echo "<tr>";
	foreach ($l as $u) {
		echo "<td>";
		if ($kk == 1) {
		    echo uid2uname($u)."\t";
		}
		else if ($kk == 4) {
		    echo substr(da($u), strlen(da($u))-6, 6)."\t";
		}
		else {
		echo "$u\t";
		}
		$kk++;
		echo "</td>";
	}
	echo "</tr>\n";
}

echo "</table>";
?>

</td>
<td>
<?
echo "<table border=1>";
echo "<tr><th>nr</th><th>user</th><th>w</th><th>e</th><th>pct</th></tr>";

$q = mysqli_query($db,"select * from lcwo_plaintextresults order by nr desc limit 20");

while ($l = mysqli_fetch_row($q)) {
	$kk=0;
	echo "<tr>";
	foreach ($l as $u) {
		echo "<td>";
		if ($kk == 1) {
		    echo uid2uname($u)."\t";
		}
		else if ($kk == 5) {
		    echo substr(da($u), strlen(da($u))-6, 6)."\t";
		}
		else {
		echo "$u\t";
		}
		$kk++;
		echo "</td>";
	}
	echo "</tr>\n";
}

echo "</table>";
?>

</td>

<td>
<?
echo "<table border=1>";
echo "<tr><th>nr</th><th>user</th><th>w</th><th>pts</th></tr>";

$q = mysqli_query($db,"select * from lcwo_wordsresults order by nr desc limit 20");

while ($l = mysqli_fetch_row($q)) {
	$kk=0;
	echo "<tr>";
	foreach ($l as $u) {
		echo "<td>";
		if ($kk == 1) {
		    echo uid2uname($u)."\t";
		}
		else if ($kk == 4) {
		    echo substr(da($u), strlen(da($u))-6, 6)."\t";
		}
		else {
		echo "$u\t";
		}
		$kk++;
		echo "</td>";
	}
	echo "</tr>\n";
}

echo "</table>";
?>

</td>

<td>
<?
echo "<table border=1>";
echo "<tr><th>nr</th><th>user</th><th>w</th><th>qtc</th></tr>";

$q = mysqli_query($db,"select * from lcwo_qtcresults order by nr desc limit 20");

while ($l = mysqli_fetch_row($q)) {
	$kk=0;
	echo "<tr>";
	foreach ($l as $u) {
		echo "<td>";
		if ($kk == 1) {
		    echo uid2uname($u)."\t";
		}
		else if ($kk == 4) {
		    echo substr(da($u), strlen(da($u))-6, 6)."\t";
		}
		else {
		echo "$u\t";
		}
		$kk++;
		echo "</td>";
	}
	echo "</tr>\n";
}

echo "</table>";
?>

</td>


</tr>
</table>

<?
$q = mysqli_query($db,"select * from lcwo_pwrequests;");
	while ($x = mysqli_fetch_row($q)) {
		echo implode(' ', $x);
		echo '<br>';
	}
?>


