<h2><? echo l('news') ?> 
<a href="https://twitter.com/learncwonline">@learncwonline</a>
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




