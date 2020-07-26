<h2><? echo l('news') ?> 
<a href="https://twitter.com/learncwonline"><img height=14 src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+CjxzdmcgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAzMDAuMDAwMDYgMjQ0LjE4NzAzIiBoZWlnaHQ9IjI0NC4xOSIgd2lkdGg9IjMwMCIgdmVyc2lvbj0iMS4xIiB4bWxuczpjYz0iaHR0cDovL2NyZWF0aXZlY29tbW9ucy5vcmcvbnMjIiB4bWxuczpkYz0iaHR0cDovL3B1cmwub3JnL2RjL2VsZW1lbnRzLzEuMS8iPgogPGcgc3R5bGU9IiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTUzOS4xOCAtNTY4Ljg2KSI+CiAgPHBhdGggZD0ibTYzMy45IDgxMi4wNGMxMTIuNDYgMCAxNzMuOTYtOTMuMTY4IDE3My45Ni0xNzMuOTYgMC0yLjY0NjMtMC4wNTM5LTUuMjgwNi0wLjE3MjYtNy45MDMgMTEuOTM4LTguNjMwMiAyMi4zMTQtMTkuNCAzMC40OTgtMzEuNjYtMTAuOTU1IDQuODY5NC0yMi43NDQgOC4xNDc0LTM1LjExMSA5LjYyNTUgMTIuNjIzLTcuNTY5MyAyMi4zMTQtMTkuNTQzIDI2Ljg4Ni0zMy44MTctMTEuODEzIDcuMDAzMS0yNC44OTUgMTIuMDkzLTM4LjgyNCAxNC44NDEtMTEuMTU3LTExLjg4NC0yNy4wNDEtMTkuMzE3LTQ0LjYyOS0xOS4zMTctMzMuNzY0IDAtNjEuMTQ0IDI3LjM4MS02MS4xNDQgNjEuMTMyIDAgNC43OTc4IDAuNTM2NCA5LjQ2NDYgMS41ODU0IDEzLjk0MS01MC44MTUtMi41NTY5LTk1Ljg3NC0yNi44ODYtMTI2LjAzLTYzLjg4LTUuMjUwOCA5LjAzNTQtOC4yNzg1IDE5LjUzMS04LjI3ODUgMzAuNzMgMCAyMS4yMTIgMTAuNzk0IDM5LjkzOCAyNy4yMDggNTAuODkzLTEwLjAzMS0wLjMwOTkyLTE5LjQ1NC0zLjA2MzUtMjcuNjktNy42NDY4LTAuMDA5IDAuMjU2NTItMC4wMDkgMC41MDY2MS0wLjAwOSAwLjc4MDc3IDAgMjkuNjEgMjEuMDc1IDU0LjMzMiA0OS4wNTEgNTkuOTM0LTUuMTM3NiAxLjQwMDYtMTAuNTQzIDIuMTUxNi0xNi4xMjIgMi4xNTE2LTMuOTMzNiAwLTcuNzY2LTAuMzg3MTYtMTEuNDkxLTEuMTAyNiA3Ljc4MzggMjQuMjkzIDMwLjM1NSA0MS45NzEgNTcuMTE1IDQyLjQ2NS0yMC45MjYgMTYuNDAyLTQ3LjI4NyAyNi4xNzEtNzUuOTM3IDI2LjE3MS00LjkyOSAwLTkuNzk4My0wLjI4MDM2LTE0LjU4NC0wLjg0NjM0IDI3LjA1OSAxNy4zNDQgNTkuMTg5IDI3LjQ2NCA5My43MjIgMjcuNDY0IiBmaWxsPSIjMWRhMWYyIi8+CiA8L2c+Cjwvc3ZnPgo="></a>
<a href="/atom.xml"><img src="/pics/feed.png" border="0"
alt="[Atom LCWO News Feed]" title="LCWO Atom News Feed"></a>
<a href="/forumatom.xml"><img src="/pics/feed.png" border="0"
alt="[Atom LCWO Forum Feed]" title="LCWO Atom Forums Feed"></a>
</h2>

<?
$changelog = "<p><a href='/changelog'>ChangeLog</a> &mdash; ".l('feedback').": <a href='mailto:".ADMINMAIL."'>".ADMINNAME."</a>.</p>";

if ($p == 'news') {
	echo $changelog;
}


if ($_GET['all'] == 1) {
	$limit = "";
}
else {
	$limit = " limit 10 ";
}

?>


<?
/* enter news; for ADMIN only*/

if ($_SESSION['uid'] == ADMIN) {
?>
<form action="/news" method="POST">
<input name="newstext" size="35">
<input type="submit">
</form>
<?

if ($_POST['newstext']) {
    $text = esc($_POST['newstext']);
	$a = mysqli_query($db, "insert into lcwo_news (date, news) values (CURRENT_DATE, '$text');");
	if (!$a) {
		echo "wat: ".mysqli_error($db);
	}
}

if (isint($_GET['delete'])) {
	$nr = $_GET['delete'];
	$a = mysqli_query($db, "delete from lcwo_news where ID='$nr' limit 1;");
	echo mysqli_error();
}


} /* if ADMIN */


/* fetch and display news */
$q = mysqli_query($db, "select `date`, `news`, `ID` from lcwo_news order by id desc $limit;");
if(!$q) {
	echo "News failed. WTF?";
	return;
}
$count = 0;
while ($row = mysqli_fetch_row($q)) {
	$count++;
	if ($count == 1) {
		$date = "<span style=\"color:#ff0000\">".$row[0]."</span>";
	}
	else {
			$date = $row[0];
	}

	if ($_SESSION['uid'] == ADMIN) {
			$delete = "- <a href='/news/delete/".$row[2]."'>Delete</a>";
	}

	echo "<p><strong>$date</strong> - ".$row[1].$delete."</p>\n";
	
}

if (!$_GET['all']) {
	echo "<div><a href='/news/all'>".l('showall2').'</a></div>';
}
else {
	echo "<div><a href='/news'>".l('hide').'</a></div>';
}


if ($p != 'news') {
	echo $changelog;
}




?>




