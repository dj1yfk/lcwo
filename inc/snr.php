<?
if (!$_SESSION['uid']) {
echo "Sorry, you must be logged in to use this function.";
return 0;
}

echo "<h1>".l('snrtest')."</h1>";

if (in_array($_GET['attempt'], array("new", "continue"))) {
	attempt($_GET['attempt']);
}
elseif ($_GET['attempt'] == "finish") {
	finishattempt();
}
else {
	intro();
}

function intro () {

?>
<img src="pics/noise.jpg" align="right" alt="[NOISE]" title="Noise">
<?

echo "<p>".l('snrtest1')."</p>";
echo "<p>".l('snrtest2')."</p>";
echo "<p>".l('snrtest3')."</p>";
echo "<p>".l('snrtest4')."</p>";


# Check for unfinished attempts...

$uid = $_SESSION[uid];
$query = mysql_query("SELECT speed, time from lcwo_snrtests where
						uid='$uid' and finished=0;");
if (!$query) {
	echo "Error: ".mysql_error();
	return;
}

$att = mysql_fetch_object($query);

if ($att) {
	echo "<p><a href=\"/snr/continue\">".l(snroldattempt).
		" ($att->speed ".l(wpm).", ".da($att->time).")</a></p>";

}

?>

<h2><? echo l(newattempt); ?></h2>

<form action="/snr/new" method="POST">
<table>
<tr>
<td><? echo l(speed) ?>:</td>
<td>
<select name="speed" size="1">
<option value="5">5 <? echo l(wpm);?></option>
<option value="10">10 <? echo l(wpm);?></option>
<option value="15" selected>15 <? echo l(wpm);?></option>
<option value="20">20 <? echo l(wpm);?></option>
</select>
</td><td>
<input type="submit" value="<? echo l(start); ?>">
</td></tr>
</table>
</form>
<?

} // Intro




function attempt ($attempt) {
	global $letterschar;

	switch ($attempt) {
		case "new":
			$query = mysql_query("DELETE FROM lcwo_snrtests where
									uid='$_SESSION[uid]' and finished=0;");

			if (in_array($_POST[speed], array(5, 10, 15, 20))) {
				$speed = $_POST[speed];
			}
			else {
				$speed = 15;
			}
			$query = mysql_query("INSERT INTO lcwo_snrtests (uid,
					speed, time) VALUES ($_SESSION[uid], $speed, NULL) ");
			/* FALLTHROUGH */
		case "continue":
			/* get the ID and speed of the current attempt */
			$query = mysql_query("SELECT nr, speed from lcwo_snrtests where
			uid='$_SESSION[uid]' order by time desc limit 1");
			if (!$query) {
				echo "Error: ".mysql_error();
				return;
			}
			$tmp = mysql_fetch_row($query);
			$_SESSION[snr][id] = $tmp[0];
			$speed = $tmp[1];
			if (!$_SESSION[snr][id]) {
				echo "Error: Cannot find attempt! Please contact the admin.";
				return;
			}
			
			/* Find out which of the 15 texts still have to be
			* done. Start from a1 and find the first which has a
			* accuracy of -1 = not tested yet. */
	
			$row = getattemptrow(); 
			
			for ($tmp = 0; $tmp < 15; $tmp++) {
				if ($row[$tmp] < 0) {
					break;
				}
			}	
			$_SESSION[snr][test] = $tmp+1;

			if ($_SESSION[snr][test] > 15) {
				echo "ERROR: Something went wrong. Please contact
				the administrator if the problem persists.";
				return;
			}

			/* Did we get a text to grade? If so, insert it into
			* the database */
			
			if ($_POST[text]) {
				$row = gradeandinsertintodatabase($row);
			}
		
			break;
	}

	/* Show a table with SNRs vs. achieved accuracies as far as
	* possible */

	showaccuracytable($row);

	
?>



<?

$snrdb = 10-$_SESSION[snr][test];

$text = strtoupper(getgroups($speed, 25, $letterschar, 1, 5));
$text = substr($text, 0, strlen($text)-1);

$textplay = "^ ^ |f800 ^ |N".$snrdb." ".$text."   ^"; 

echo "<p><strong>".l(currentsnr)." ".(10-$_SESSION[snr][test])."dB</strong>. "
.l(snrpseplay)."</p>";

if ($_SESSION[snr][test] == 15) {
	$action = "finish";
}
else {
	$action = "continue";
}


?>

	
<form action="/snr/<? echo $action ?>" method="POST" name="eform">
<table>
	<tr>
	<td><textarea name="input" cols="40" rows="10"></textarea></td>
	<td>
	&nbsp;
	<? player($textplay, $_SESSION[player], $speed, $speed, 0, 0, 0, 1); ?>
	</td>
	</tr>
	<tr>
	<td>
		<input type="hidden" name="text" value="<? echo $text; ?>">
 <input type="submit" value=" <? echo l("continue"); ?> "> (<? echo l(notcasesensitive); ?>)
	</td>
</table>
</form>

<?

} /* attempt */

function finishattempt () {

	gradeandinsertintodatabase ($bla);
	$row = getattemptrow();
	showaccuracytable($row);

	if ($_SESSION[snr][id]) {
		$query = mysql_query("update lcwo_snrtests set finished=1
							where nr = '".$_SESSION[snr][id]."';");
		$_SESSION[snr][id] = 0;
	}
	else {
		echo "Error: Looks like you finished this attempt?";
	}

	echo "<p>".l(snrtestfinished)."</p>";

	$g = new snrgraph();
	$g->user = $_SESSION[username];
	$g->create();
	$g->add_dataset($row, "This attempt.");
	$g->write_image("img/snr".$_SESSION[uid].".gif");

?>
	<img src="img/snr<? echo $_SESSION[uid]; ?>.gif">

<?
	
	
}

function gradeandinsertintodatabase ($row) {

	if (!$_POST[text]) {
		return "";
	}
		
	$text = strtoupper($_POST[text]); 
	$input = strtoupper($_POST[input]);
	$input = preg_replace('/[^A-Z]/', ' ', $input);
	$input = preg_replace('/\s+/', ' ', $input);
	$diff = levenshtein($input, $text);
	$accuracy = round(100*(1 - ($diff/strlen($text))),2);
	$row[$_SESSION[snr][test]-1] = $accuracy;
				
	$aa = "a".$_SESSION[snr][test];
				
	$query = mysql_query("update lcwo_snrtests set
	$aa = $accuracy where nr = '".$_SESSION[snr][id]."';");
	$_SESSION[snr][test] += 1;
	return $row;
}





function getattemptrow () {
			$query = mysql_query("select a1, a2, a3, a4, a5, a6,
			a7, a8, a9, a10, a11, a12, a13, a14, a15 from
			lcwo_snrtests where nr='".$_SESSION[snr][id]."'");
			if (!$query) {
				echo "Error: ".mysql_error();
				return;
			}
			return mysql_fetch_array($query);
}


function showaccuracytable ($row) {
?>
	<table class="tborder" width="90%">
	<tr>
	<th><? echo l(SNR); ?></th>
	<td width="6.66%">9dB</td>
	<td width="6.66%">8dB</td>
	<td width="6.66%">7dB</td>
	<td width="6.66%">6dB</td>
	<td width="6.66%">5dB</td>
	<td width="6.66%">4dB</td>
	<td width="6.66%">3dB</td>
	<td width="6.66%">2dB</td>
	<td width="6.66%">1dB</td>
	<td width="6.66%">0dB</td>
	<td width="6.66%">-1dB</td>
	<td width="6.66%">-2dB</td>
	<td width="6.66%">-3dB</td>
	<td width="6.66%">-4dB</td>
	<td width="6.66%">-5dB</td>
	</tr>
	<tr>
	<th>%</th>
<?
	for ($tmp = 0; $tmp < 15; $tmp++) {
		if (($tmp+1) == $_SESSION[snr][test]) {
			echo '<td class="tborderred">';
		}
		else {
			echo '<td>';
		}
		if ($row[$tmp] < 0) {

			echo "-";
		}
		else {
				echo $row[$tmp];
		}
		echo "</td>";
	}

?>
	</tr>
	</table>
<?

} // showaccuracytable

?>
<div class="vcsid">$Id: snr.php 35 2010-09-02 21:10:27Z dj1yfk $</div>

