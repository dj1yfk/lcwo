<h1>LCWO <? echo l('userpageof')." ".$_SESSION['username']; ?></h1>

<script>
function del(type, nr) {
		var tr = document.getElementById(type + nr);
		var td = document.getElementById('td' + type + nr);

		td.innerHTML = '...'; // immediately remove link to avoid clicking agn

		var url = window.location.href;
		var arr = url.split("/");
		var posturl = "//" + arr[2] + "/api/delete.php";

		var request =  new XMLHttpRequest();
		request.open("POST", posturl, true);
		request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		request.onreadystatechange = function() {
			var done = 4, ok = 200;
				if (request.readyState == done && request.status == ok) {
				if (request.responseText) {
					tr.style = 'color: gray;'
					td.innerHTML =  request.responseText;
				}
				else {
					td.innerHTML =  "<b>Delete failed!</b>";
				}
			}
		};
		request.send('type='+type+'&nr='+nr);
}
</script>

<p>
<? echo l('overviewofpractice') ?>
</p>

<?
$qatt = mysqli_query($db,"select count(*) as c from lcwo_lessonresults where uid=".$_SESSION['uid'].";"); $at = mysqli_fetch_object($qatt);
?>

<h2>
<? echo l('kochmethodcourse'); 
if ($at->c > 0) {
	echo " &mdash; ".$at->c." ".l('attempts',0,$at->c);
}
?>
</h2>


<table border="0" width="100%">
<tr>
<td width="85%">
<?
	if ($_GET['k'] == 1) {
		$klimit = '';
	}
	else {
		$klimit = 'limit 3';
	}

	$gc = mysqli_query($db,"select `nr`, `lesson`, `speed`, `eff`,
	`accuracy`, `time` from	lcwo_lessonresults where
	`uid`='".$_SESSION['uid']."' order by `time` desc $klimit");

	echo "<table><tr><th>".l('lesson')."</th><th>".l('charspeed')."</th><th>".
			l('effspeed')."</th><th>".l('accuracy')." (%)</th><th>".
			l('datetime')."</th><th>".l('delete')."</th></tr>\n";
	
	$knr=0;
	while ($c = mysqli_fetch_object($gc)) {
		$knr++;
		echo "<tr id='lesson$c->nr'><td>$c->lesson</td><td>$c->speed</td><td>$c->eff</td>";
		echo "<td>$c->accuracy</td><td>";
		echo da($c->time);
		echo "</td><td id='tdlesson$c->nr' align='center'><a onClick='del(\"lesson\",$c->nr);return false;' href='/delete/lesson/$c->nr'><img src='/pics/del.png' border=0></a></td></tr>\n";
	}
	if (!$knr) {
		echo "<tr><td colspan=6 align=center>".l('noattempts')."</td></tr>";
	}
	echo "</table>";
	if ($knr == 3) {
		echo '<p><a href="/main/k">'.l('showall2').'</a></p>';
	}
	else if ($knr > 3) {
		echo '<p><a href="/main">'.l('hide').'</a></p>';
	}
?>
</td>
<td style="vertical-align:top;"> 
<center>
<? if ($knr >= 3) { ?>
<a href="/kochstat">
<img src="/pics/graph.jpg"><br>
<? echo l('showstatistics') ?>
</a>
<br>
<a href="/api/index.php?action=export_results&type=koch&fmt=json">JSON</a> / 
<a href="/api/index.php?action=export_results&type=koch&fmt=csv">CSV</a> 
<? } 
else { ?>
<img src="/pics/graph.jpg"><br>
<?
echo l('statsafter3attempts');
}
?>
</center>
</td>
</tr>
</table>

<?
$qatt = mysqli_query($db,"select count(*) as c from lcwo_groupsresults where uid=".$_SESSION['uid'].";"); $at = mysqli_fetch_object($qatt);
?>

<h2><?echo l('codegroups');
if ($at->c > 0) {
		echo " &mdash; ".$at->c." ".l('attempts',0,$at->c); 
}
?></h2>

<table border="0" width="100%">
<tr>
<td width="85%">

<?
	if ($_GET['c'] == 1) {
		$climit = '';
	}
	else {
		$climit = 'limit 3';
	}

	$gc = mysqli_query($db,"select `nr`, `mode`, `speed`, `eff`,
	`accuracy`, `time` from	lcwo_groupsresults where
	`uid`='".$_SESSION['uid']."' order by `time` desc $climit");

	echo "<table><tr><th>".l('groupmode')."</th><th>".l('charspeed')."</th><th>".
			l('effspeed')."</th><th>".l('accuracy')." (%)</th><th>".
			l('datetime')."</th><th>".l('delete')."</th></tr>\n";
	
	$cnr=0;
	while ($c = mysqli_fetch_object($gc)) {
		echo "<tr id='groups$c->nr'><td>".l($c->mode)."</td><td>$c->speed</td><td>$c->eff</td>";
		echo "<td>$c->accuracy</td><td>";
		echo da($c->time);
		echo "</td><td id='tdgroups$c->nr' align='center'><a onClick='del(\"groups\",$c->nr);return false;' href='/delete/groups/$c->nr'><img src='/pics/del.png' border=0></a></td></tr>\n";
		$cnr++;
	}

	if (!$cnr) {
		echo "<tr><td colspan=6 align=center>".l('noattempts')."</td></tr>";
	}
	
	echo "</table>";
	if ($cnr == 3) {
		echo '<p><a href="/main/c">'.l('showall2').'</a></p>';
	}
	else if ($cnr > 3) {
		echo '<p><a href="/main">'.l('hide').'</a></p>';
	}
	
?>
</td>
<td style="vertical-align:top;"> 
<center>
<? if ($cnr >= 3) { ?>
<a href="/groupstat">
<img src="/pics/graph.jpg"><br>
<? echo l('showstatistics') ?>
</a>
<br>
<a href="/api/index.php?action=export_results&type=groups&fmt=json">JSON</a> / 
<a href="/api/index.php?action=export_results&type=groups&fmt=csv">CSV</a> 
<? } 
else { ?>
<img src="/pics/graph.jpg"><br>
<?
echo l('statsafter3attempts');
}
?>
</center>
</td>
</tr>
</table>

<?
$qatt = mysqli_query($db,"select count(*) as c from lcwo_plaintextresults where uid=".$_SESSION['uid'].";"); $at = mysqli_fetch_object($qatt);
?>

<h2><?echo l('plaintexttraining');
if ($at->c > 0) {
		echo " &mdash; ".$at->c." ".l('attempts',0,$at->c); 
}
?></h2>

<table border="0" width="100%">
<tr>
<td width="85%">

<?

	if ($_GET['p'] == 1) {
		$plimit = '';
	}
	else {
		$plimit = 'limit 3';
	}

	$gc = mysqli_query($db,"select `nr`, `speed`, `eff`,
	`accuracy`, `time` from	lcwo_plaintextresults where
	`uid`='".$_SESSION['uid']."' order by `time` desc $plimit");

	echo "<table><tr><th>".l('charspeed')."</th><th>".
			l('effspeed')."</th><th>".l('accuracy')." (%)</th><th>".
			l('datetime')."</th><th>".l('delete')."</th></tr>\n";
	
	$pnr=0;
	while ($c = mysqli_fetch_object($gc)) {
		echo "<tr id='plaintext$c->nr'><td>$c->speed</td><td>$c->eff</td>";
		echo "<td>$c->accuracy</td><td>";
		echo da($c->time);
		echo "</td><td id='tdplaintext$c->nr' align='center'><a onClick='del(\"plaintext\",$c->nr);return false;' href='/delete/plaintext/$c->nr'><img src='/pics/del.png' border=0></a></td></tr>\n";
		$pnr++;
	}
	if (!$pnr) {
		echo "<tr><td colspan=6 align=center>".l('noattempts')."</td></tr>";
	}
	echo "</table>";


	if ($pnr == 3) {
		echo '<p><a href="/main/p">'.l('showall2').'</a></p>';
	}
	else if ($pnr > 3) {
		echo '<p><a href="/main">'.l('hide').'</a></p>';
	}

?>
</td>
<td style="vertical-align:top;"> 
<center>
<? if ($pnr >= 3) { ?>
<a href="/plainstat">
<img src="/pics/graph.jpg"><br>
<? echo l('showstatistics') ?>
</a>
<br>
<a href="/api/index.php?action=export_results&type=plaintext&fmt=json">JSON</a> / 
<a href="/api/index.php?action=export_results&type=plaintext&fmt=csv">CSV</a> 
<? } 
else { ?>
<img src="/pics/graph.jpg"><br>
<?
echo l('statsafter3attempts');
}
?>
</center>
</td>
</tr>
</table>

<?
$qatt = mysqli_query($db,"select count(*) as c from lcwo_callsignsresults where uid=".$_SESSION['uid'].";"); $at = mysqli_fetch_object($qatt);
?>

<h2><?echo l('callsigntraining');
if ($at->c > 0) {
		echo " &mdash; ".$at->c." ".l('attempts',0,$at->c); 
}
?></h2>

<table border="0" width="100%">
<tr>
<td width="85%">

<?

	if ($_GET['l'] == 1) {
		$llimit = '';
	}
	else {
		$llimit = 'limit 3';
	}

	$gc = mysqli_query($db,"select `nr`, `score`, `max`,
	`time` from	lcwo_callsignsresults where
	`uid`='".$_SESSION['uid']."' order by `time` desc $llimit");

	echo "<table><tr><th>".l('score')."</th><th>".
			l('maxspeed')."</th><th>".
			l('datetime')."</th><th>".l('delete')."</th></tr>\n";
	
	$lnr=0;
	while ($c = mysqli_fetch_object($gc)) {
		echo "<tr id='callsigns$c->nr'><td>$c->score</td><td>$c->max</td>";
		echo "<td>";
		echo da($c->time);
		echo "</td><td id='tdcallsigns$c->nr' align='center'><a onClick='del(\"callsigns\",$c->nr);return false;' href='/delete/callsigns/$c->nr'><img src='/pics/del.png' border=0></a></td></tr>\n";
		$lnr++;
	}
	if (!$lnr) {
		echo "<tr><td colspan=6 align=center>".l('noattempts')."</td></tr>";
	}
	echo "</table>";


	if ($lnr == 3) {
		echo '<p><a href="/main/l">'.l('showall2').'</a></p>';
	}
	else if ($lnr > 3) {
		echo '<p><a href="/main">'.l('hide').'</a></p>';
	}

	
?>
</td>
<td style="vertical-align:top;"> 
<center>
<? if ($lnr >= 3) { ?>
<a href="/callstat">
<img src="/pics/graph.jpg"><br>
<? echo l('showstatistics') ?>
</a>
<br>
<a href="/api/index.php?action=export_results&type=callsigns&fmt=json">JSON</a> / 
<a href="/api/index.php?action=export_results&type=callsigns&fmt=csv">CSV</a> 
<? } 
else { ?>
<img src="/pics/graph.jpg"><br>
<?
echo l('statsafter3attempts');
}
?>
</center>
</td>
</tr>
</table>

<?
$qatt = mysqli_query($db,"select count(*) as c from lcwo_wordsresults where uid=".$_SESSION['uid'].";"); $at = mysqli_fetch_object($qatt);
?>

<h2><?echo l('wordtraining');
if ($at->c > 0) {
		echo " &mdash; ".$at->c." ".l('attempts',0,$at->c); 
}
?></h2>

<table border="0" width="100%">
<tr>
<td width="85%">

<?

	if ($_GET['w'] == 1) {
		$wlimit = '';
	}
	else {
		$wlimit = 'limit 3';
	}

	$gc = mysqli_query($db,"select `nr`, `score`, `max`,
	`time` from	lcwo_wordsresults where
	`uid`='".$_SESSION['uid']."' order by `time` desc $wlimit");

	echo "<table><tr><th>".l('score')."</th><th>".
			l('maxspeed')."</th><th>".
			l('datetime')."</th><th>".l('delete')."</th></tr>\n";
	
	$wnr=0;
	while ($c = mysqli_fetch_object($gc)) {
		echo "<tr id='words$c->nr'><td>$c->score</td><td>$c->max</td>";
		echo "<td>";
		echo da($c->time);
		echo "</td><td id='tdwords$c->nr' align='center'><a onClick='del(\"words\",$c->nr);return false;' href='/delete/words/$c->nr'><img src='/pics/del.png' border=0></a></td></tr>\n";
		$wnr++;
	}
	if (!$wnr) {
		echo "<tr><td colspan=6 align=center>".l('noattempts')."</td></tr>";
	}
	echo "</table>";


	if ($wnr == 3) {
		echo '<p><a href="/main/w">'.l('showall2').'</a></p>';
	}
	else if ($wnr > 3) {
		echo '<p><a href="/main">'.l('hide').'</a></p>';
	}

	
?>
</td>
<td style="vertical-align:top;">
<center>
<? if ($wnr >= 3) { ?>
<a href="/wordstat">
<img src="/pics/graph.jpg"><br>
<? echo l('showstatistics') ?>
</a>
<br>
<a href="/api/index.php?action=export_results&type=words&fmt=json">JSON</a> / 
<a href="/api/index.php?action=export_results&type=words&fmt=csv">CSV</a> 
<? } 
else { ?>
<img src="/pics/graph.jpg"><br>
<?
echo l('statsafter3attempts');
}
?>
</center>
</td>
</tr>
</table>


<?
$qatt = mysqli_query($db,"select count(*) as c from lcwo_qtcresults where uid=".$_SESSION['uid'].";"); $at = mysqli_fetch_object($qatt);
?>

<h2><?echo l('qtctraining');
if ($at->c > 0) {
		echo " &mdash; ".$at->c." ".l('attempts',0,$at->c); 
}
?></h2>

<table border="0" width="100%">
<tr>
<td width="85%">

<?

	if ($_GET['q'] == 1) {
		$qlimit = '';
	}
	else {
		$qlimit = 'limit 3';
	}

	$gc = mysqli_query($db,"select `nr`, `qtcs`, `speed`,
	`time` from	lcwo_qtcresults where
	`uid`='".$_SESSION['uid']."' order by `time` desc $qlimit");

	echo "<table><tr><th>QTCs</th><th>".l('wpm')."</th><th>".
			l('datetime')."</th><th>".l('delete')."</th></tr>\n";
	
	$qnr=0;
	while ($c = mysqli_fetch_object($gc)) {
		echo "<tr id='qtc$c->nr'><td>$c->qtcs</td><td>$c->speed</td>";
		echo "<td>";
		echo da($c->time);
		echo "</td><td id='tdqtc$c->nr' align='center'><a onClick='del(\"qtc\",$c->nr);return false;' href='/delete/qtc/$c->nr'><img src='/pics/del.png' border=0></a></td></tr>\n";
		$qnr++;
	}
	if (!$qnr) {
		echo "<tr><td colspan=6 align=center>".l('noattempts')."</td></tr>";
	}
	echo "</table>";


	if ($qnr == 3) {
		echo '<p><a href="/main/q">'.l('showall2').'</a></p>';
	}
	else if ($wnr > 3) {
		echo '<p><a href="/main">'.l('hide').'</a></p>';
	}

	
?>
</td>
<td style="vertical-align:top;">
<center>
<? if ($qnr >= 3) { ?>
<a href="/qtcstat">
<img src="/pics/graph.jpg"><br>
<? echo l('showstatistics') ?>
</a>
<br>
<a href="/api/index.php?action=export_results&type=qtc&fmt=json">JSON</a> / 
<a href="/api/index.php?action=export_results&type=qtc&fmt=csv">CSV</a> 
<? } 
else { ?>
<img src="/pics/graph.jpg"><br>
<?
echo l('statsafter3attempts');
}
?>
</center>
</td>
</tr>
</table>

<div class="vcsid">$Id: main.php 31 2014-12-18 13:22:37Z fabian $</div>
