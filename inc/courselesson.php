<? 
# Warning: Major Spaghetti code ahead!

if (!$_SESSION['uid']) {
echo "Sorry, you must be logged in to use this function.";
return 0;
}

if (($_SESSION['uid'] == 161) ) {
		$kochchar = 
		array('K','M','R','S','U','A','P','T','L','O','W','I', 
	'.','N','J','E','F','0','Y',',','V','G','5','/','Q','9','Z', 
		'3','8','B','?','4','2','7','C','1','D','6','X');
}
?>

<script type="text/javascript">

function newc (nc) {

var cwtest2 = '40 99 41 32 70 97 98 105 97 110 32 75 117 114 122 32 68 74 49 89 70 75';
		
var newurl = '?s=<? echo
$_SESSION['cw_speed'];?>&e=<? echo $_SESSION['cw_eff']?>&f=<?echo
$_SESSION['cw_tone'];?>&t='+nc+nc+nc+nc+nc+nc+nc+nc+nc+nc+nc+nc+nc;

		
<? if ($_SESSION['player'] != 3) { ?>
loadFile('js1', {file:'<?=CGIURL();?>cw.mp3'+newurl, type:'mp3'})
sendEvent('js1', 'playpause');
<? } else {		/* HTML5 */ ?>
var p = document.getElementById('player1');
p.src = '<?=CGIURL();?>cw.mp3'+newurl;
p.load();
p.play();
<? } ?>
var newchar = document.getElementById('newc');
newchar.innerHTML = '';

var newchar2 = document.getElementById('nc');
newchar2.innerHTML = nc;


}
</script>
<?

if (isint($_POST['lesson']) && $_SESSION['uid']) {
	unset($_SESSION['koch_lesson']);
	$_SESSION['koch_lesson'] = $_POST['lesson'];
	$k = mysqli_query($db,"update lcwo_users set `koch_lesson`='".$_SESSION['koch_lesson']."' where `id`='".$_SESSION['uid']."'");
	if (!$k) {
		echo "Help! Failed to change lesson in database.<br>";
	}
}

if (isint($_POST['duration'])  && $_SESSION['uid']) {
	unset($_SESSION['koch_duration']);
	$_SESSION['koch_duration'] = $_POST['duration'];

    $upd = mysqli_query($db,"update lcwo_users set koch_duration='".$_SESSION['koch_duration']."' where id='".$_SESSION['uid']."'");

    if (!$upd) {
        echo "<p>Error: Updating duration failed. .".mysqli_error($db)."</p>";
    }
}




?>


<h1><? echo l('kochmethodcourse'); ?></h1>

<? if (!isset($_POST['text'])) { ?>

<form action="/courselesson" method="POST">
<table>
<tr>
<td>
<? echo l('youareinlesson') ?> <strong><? echo $_SESSION['koch_lesson'];
?></strong> <? echo l('of40lessons'); ?>
</td>
<td width="2%">&nbsp;</td>
<td><? echo l('changetolesson') ?>:</td>
<td>
<select onChange="this.form.submit();" name="lesson" size="1">
<?
	for ($i = 1; $i < 41; $i++) {
		if ($i == $_SESSION['koch_lesson']) {
			echo "<option selected>$i</option>";
		}
		else {
			echo "<option>$i</option>";
		}
	}
?>
</select>
</td>
<td>&nbsp;<? echo l('changeduration') ?>:</td>
<td>
<select onchange="this.form.submit();" name="duration" size="1">
<?
	for ($i = 1; $i < 6; $i++) {
		if ($i == $_SESSION['koch_duration']) {
			echo "<option selected>$i</option>";
		}
		else {
			echo "<option>$i</option>";
		}
	}
?>
</select>
</td>
</tr>
</table>
</p>
</form>

<? if ($_SESSION['koch_lesson'] > 1) { ?>

<p><? echo l('lettersinthislesson'); ?>: <strong>
<?
for ($k = 0; $k <= $_SESSION['koch_lesson']; $k++) {
	if ($_SESSION['player'] != 1) {		# Flash/HTML player -> Links to chars
		echo "<a href=\"javascript:newc('".$kochchar[$k]."')\">".$kochchar[$k]."</a> ";
	}
	else {
		echo $kochchar[$k]." ";
	}
}
$nc = $kochchar[$k-1];
?>
</strong>
<?
} /* if lesson > 1 */

if ($_SESSION['player'] == 0) {
	echo "&nbsp;&nbsp; (".l('clickoncharactertohear').")";
}

?>
</p>

<h2><? echo l('listento') ?><span id="newc"><? # l(thenew) ?></span> <? echo ($_SESSION['koch_lesson'] == 1) ?  l('charactersdual') : l('character') ?>: <? echo ($_SESSION['koch_lesson'] == 1) ? "K, M" : "<span id=\"nc\">$nc</span>"; ?></h2>

<?

if ($_SESSION['koch_lesson'] == 1) {
echo "<table><tr><td>";
player("KKKKKKKKKKKKKKKKKK", $_SESSION['player'],
$_SESSION['cw_speed'], $_SESSION['cw_eff'],0, 0, 1,0); 
echo "</td><td>&nbsp;&nbsp;</td><td>";
player("MMMMMMMMMMMMMMMMMM", $_SESSION['player'],
$_SESSION['cw_speed'], $_SESSION['cw_eff'],0, 0, 1,0); 
echo "</td></tr></table>\n";
}
else {
player("$nc$nc$nc$nc$nc$nc$nc$nc$nc$nc$nc$nc$nc",
$_SESSION['player'], $_SESSION['cw_speed'], $_SESSION['cw_eff'], 0, 1,
1,0); 
}


}  /* if !isset($_POST[text}) */

?>


<h2><? echo l('practicetext'); ?> (<? echo $_SESSION['koch_duration'] ?> <?=l("minute")?>)</h2>


<?
if (isset($_POST['text']))  {
	$sent = explode(' ', $_POST['text']);	

	$rxtext = strtoupper($_POST['input']);
	/* remove superfluous \n and \s and accept ; for ?, etc. */
	$rxtext = preg_replace('/[\s]+/', ' ', $rxtext);
	$rxtext = preg_replace('/[\n]+/', ' ', $rxtext);
	$rxtext = preg_replace('/;/', '?', $rxtext);
	$rxtext = stripcommands($rxtext);
		    
	$rcvd = preg_split('/[\s\n]+/', $rxtext);


	echo '<table><tr><td valign="top">';
	
	echo "<p><strong>".l('result')." (".$_SESSION['cw_speed']."/".$_SESSION['cw_eff']." ".l('wpm')."):</strong></p>\n";



	# Groups	
	echo "<table><tr><th>".l('sentgroup')."</th><th>".l('receivedgroup')."</th><th "
			."colspan=\"2\">".l('errors')."</th>";
	echo "</tr>\n";
	$i=0;
	foreach ($sent as $group) {
		if ($group == '') { break; }
		$error = check ($group, $rcvd[$i]);
		$totalerrors += $error[1];
		$groups++;
		echo
		"<tr><td style=\"font-family:monospace\">$group</td><td
		style=\"font-family:monospace\">".$rcvd[$i]."</td><td>".$error[0]."</td><td>".$error[1]."</td></tr>";
		$i++;
	}
	echo "</table>\n";
	
	echo '</td><td>&nbsp;&nbsp;</td><td valign="top">';

    $real = realspeed($_POST['text'], $_SESSION['cw_speed'], $_SESSION['cw_eff']);


#	return array(round($realspeed_cpm,1), round($realspeed_wpm,1),
#			    round($length,1), $characters);

	// $grouplen = $_SESSION['koch_randomlength'] ? $_SESSION['koch_randomlength'] : 5;
	
	$errpct = (intval(1000*$totalerrors/($real[3]))/10);

	if ($errpct > 100) {
		$errpct = 100;
	}

	echo "<p>".l('groups').": $groups (".($real[3])." ".l('characters')."), ".l('errors')." ".$totalerrors." = $errpct%</p>";

    echo '<p>'.l('realspeed').': '.$real[3].' '.l('characters').' / '. $real[2].
    ' '.l('seconds').' = '.$real[1].' '.l('wpm').' / '.$real[0].' '.l('cpm').'</p>';

	
	$lserrors = levenshtein(strtoupper(substr($_POST['text'],0,255)), strtoupper(substr($rxtext,0,255)));
	$lserrpct = (intval(1000*$lserrors/($real[3]))/10);
	
	if ($lserrpct > 100) {
		$lserrpct = 100;
	}

	echo "<p><a href='http://en.wikipedia.org/wiki/Levenshtein_distance'>Levenshtein</a>-".l('errors').": ".$lserrors." = $lserrpct %</p>";

	if (($errpct < 10) || ($lserrpct < 10)) {
		echo "<p><strong>".l('good')."</strong> ".l('goodaccuracy')."</p>";
	}
	echo '<p><a href="/courselesson" id="newattempt">'.l('continuetraining').'</a></p>';

	echo '</td></tr></table>';

?>
<script type="text/javascript">
    document.getElementById('newattempt').focus();
</script>
<?

	
	$accuracy = 100-min($errpct, $lserrpct);

	$in = mysqli_query($db,"insert into lcwo_lessonresults set
	`uid`='$_SESSION[uid]', `lesson`='$_SESSION[koch_lesson]',
	`speed`='$_SESSION[cw_speed]', `eff`='$_SESSION[cw_eff]',
	`accuracy`='$accuracy', `time`=NULL;");

	if (!$in) {
		echo "<p>Error: Storing result in database
		failed.".mysqli_error($db)."</p>";
	}
			
}
else {
?>

<?
	currentparameters();
?>

<div id="formatwarning" style="width:65%;background-color:#cef010;display:none">
<?=l('formatwarning');?>
</div>


<? $text = getgroups($_SESSION['cw_speed'], $_SESSION['cw_eff'], $_SESSION['koch_lesson'], $kochchar, $_SESSION['koch_duration'], $_SESSION['koch_randomlength']); ?>

<form action="/courselesson" method="POST" id="eform">
<table>
	<tr>
	<td><textarea  spellcheck="false" autocapitalize="off" autocorrect="off" autocomplete="off"  name="input" cols="40" rows="10"></textarea></td>
	<td>
	&nbsp;

<? 
$playertext = "|W".$_SESSION['cw_ews']." ".$text;
if ($_SESSION['delay_start'] > 0) {
	$playertext = '|S'.($_SESSION['delay_start']*1000).' '.$playertext;
}

// Add some delay at the end to make Safari users happy
$playertext .= " |S20000 ";

player("$playertext", $_SESSION['player'], $_SESSION['cw_speed'],
		$_SESSION['cw_eff'], 0, 0, 0, 1); 
?>
	
	</td>
	</tr>
	<tr>
	<td>
	<?
	
	    $text2 = $text;
	    
	    if ($_SESSION['vvv'] == 1) {
		$text2 = substr($text2, 6);
		$text2 = substr($text2, 0, strlen($text2)-5);
	    }
		$text2 = stripcommands($text2);
	?>
		<input type="hidden" name="text" value="<? echo $text2; ?>">
		<input type="submit" value=" <? echo l('checkresult',1); ?> " onClick="return checkspaces();"> (<? echo l('notcasesensitive'); ?>)
	</td>
</table>

</form>
<?

}

?>
<? echo "<!-- ".time()." -->"; ?>

<?
include("inc/formatwarning.php");
?>

