
<h1><? echo l('downloadpracticefiles') ?></h1>

<?  

$dlmodes = array("letters", "figures", "mixed", "custom",
"kochmethodcourse", "callsigntraining", "wordtraining");
$wlangs = getavailablewordcollections();

/* Handle session variables */

if (!$_SESSION['download']['set']) {
	initsessiondownloads();
}
else {
	updatesessiondownloads();
}

?>

<script type="text/javascript">

function lessoncheck() {
	var lesson = document.getElementById('lesson');
	var mode = document.getElementById('mode');
	
	if (mode.value == "kochmethodcourse" || mode.value == "wordtraining") {
		lesson.style.background='white';
	}
	else {
		lesson.style.background='#cccccc';
	}
}

function wordtrainingcheck() {
	var lang = document.getElementById('wlanguage');
	var simplify = document.getElementById('wsimplify');
	var maxlen = document.getElementById('wmaxlen');
	var mode = document.getElementById('mode');
	
	if (mode.value == "wordtraining") {
		lang.style.background='white';
		simplify.style.color ='black';
		maxlen.style.color ='black';
	}
	else {
		lang.style.background='#cccccc';
		simplify.style.color ='#aaaaaa';
		maxlen.style.color ='#aaaaaa';
	}
}
	
</script>



<p><? echo l('downloadpracticefiles1') ?></p>

<div style="border-width:thin;border-style:dashed;padding:5px;width:75%">

<form action="/download" method="POST">

<table>
<tr>
<td>
<? echo l('changemode'); ?>: 
</td>
<td>
<select id="mode" name="mode" size="1"
onChange="lessoncheck();wordtrainingcheck();">
<?
	foreach ($dlmodes as $mode) {
		if ($_SESSION['download']['mode'] == $mode) {
			echo "<option value=\"$mode\" selected>".l($mode)."</option>";
		}
		else {
			echo "<option value=\"$mode\">".l($mode)."</option>";
		}

	} # foreach

?>
</select>

</td>
</tr>

<tr>
<td>
<? echo l('lesson') ?>:
</td>
<td>
<select id="lesson" name="lesson" size="1">
<? 
for ($i=1; $i <= 40; $i++) {
	if ($_SESSION['download']['lesson'] == $i) {
		echo "<option selected value=\"$i\">$i</option>\n";
	}
	else {
		echo "<option value=\"$i\">$i</option>\n";
	}
}
?>
</select>

</td>
</tr>

<tr>
<td>
<? echo l('language')." (".l('wordtraining').")"; ?>:
</td>
<td>
<? 
$langnames["cw"] = l("cwabbreviations");
?>
<select id="wlanguage" name="wlanguage[]" size="5" multiple>
<?
    $preflangs = $_SESSION['download']['wlanguage'];
	$wordlangs = getavailablewordcollections();

	# if none of the $preflangs is in $wordlangs, make
	# sure that "en0" (English) is selected by default 

	$wordlangs_short = array();
	foreach ($wordlangs as $wl) {
			$arr = explode('~', $wl);
			$wordlangs_short[] = $arr[0].$arr[1];
	}

	$ok = 0;
	foreach ($preflangs as $pl) {
			if (in_array($pl, $wordlangs_short)) {
					$ok = 1;
			}
	}

	if (!$ok) {
			$preflangs[] = "en0"; 
	}

	foreach ($wordlangs as $w) {
		$arr = explode('~', $w);
		# arr = lang, collection id, collection name, language name
		$langcollid = $arr[0].$arr[1];
		echo "<option value=\"$arr[0]$arr[1]\" ";
				
		if (in_array($langcollid, $preflangs)) {
				echo " selected ";
				$selected++;
		}
		
		echo ">$arr[0] - ".$langnames[$arr[0]];
		if ($arr[2]) {
			echo " ($arr[2])";
		}
		echo "</option>";
	}

?>
</select>
<br>
<? echo l('clickshiftmultiple'); ?>
<br>
<span id="wsimplify"><? echo l('simplify'); ?>:</span>
<input type="checkbox" value="1" name="simplify"
<? if ($_SESSION['download']['wsimplify']) echo " checked ";
?>>
<br>
<span id="wmaxlen"><? echo l('maxlength'); ?>:</span>
<input type="text" value="<?=$_SESSION['download']['wmaxlen'];?>" size="2" name="maxlen">
</td>
</tr>


<tr>
<td>
<? echo l('changeduration') ?>:
</td>
<td>
<select id="duration" name="duration" size="1">
<? 
for ($i=1; $i <= 5; $i++) {
	if ($_SESSION['download']['duration'] == $i) {
		echo "<option value=\"$i\" selected>$i</option>\n";
	}
	else {
		echo "<option value=\"$i\">$i</option>\n";
	}
}
?>
</select>
</td>
</tr>

<tr>
<td>
<? echo l('charspeedlong')." (".l('wpm')."):"; ?>
</td>
<td>
<input type="text" name="speed" size="3" value="<? echo $_SESSION['download']['speed']; ?>">
</td>
</tr>

<tr>
<td>
<? echo l('effspeedlong')." (".l('wpm')."):"; ?>
</td>
<td>
<input type="text" name="eff" size="3" value="<? echo
$_SESSION['download']['eff']; ?>">
</td>
</tr>

<tr>
<td><? echo l('tone') ?> (Hz):</td>
<td><input name="tone" type="text" value="<? echo $_SESSION['download']['tone']; ?>" size=3></td>
</tr>

<tr>
<td>
	<? echo l('numberoffiles'); ?>:
</td>
<td>
	<input type="text" name="number" size="3" value="<? echo $_SESSION['download']['number'];?>">
</td>
</tr>

</table>

<input type="submit" value="<? echo l('submit',1)?>">
</form>

</div>

<?
	if (is_numeric($_POST['number']) && is_numeric($_POST['lesson'])
		&& is_numeric($_POST['duration']) &&
		is_numeric($_POST['speed']) && is_numeric($_POST['eff']) &&
		is_numeric($_POST['tone']) && is_numeric($_POST['number']) &&
		is_numeric($_POST['maxlen']) && in_array($_POST['mode'], $dlmodes)
	) {

?>
<h2><? echo l('download'); ?></h2>

<p>
<?=l('downloadhint');?> 
<a href="/downloadhint"><?=l('downloadhint2');?></a> 
</p>

<table>
<tr><th><? echo l('mp3file') ?></th><th><? echo l('text') ?></th></tr>

<?

for ($i = 1; $i <= $_SESSION['download']['number']; $i++) {
	$i = sprintf("%03d", $i);

	switch ($_POST['mode']) {
		case 'letters':
			$char = $letterschar;
			break;
		case 'figures':
			$char = $figureschar;
			break;
		case 'mixed':
			$char = $mixedchar;
			break;
		case 'custom':
			$char = getcustomcharacters();
			break;
		case 'kochmethodcourse':
			$char = $kochchar;
			break;
		default:
			$char = $letterschar;
	}

	if ($_POST['mode'] == "kochmethodcourse") {
		$nr = $_POST['lesson'];
	}
	else {
		$nr = count($char)-1;
	}

	if (isset($_SESSION['koch_randomlength'])) {
		$grouplength = $_SESSION['koch_randomlength'];
	}
	else {
		$grouplength = 5;
	}

	// HACK: cw-download.mp3 does not support variable / random
	// frequencies... so temporarily disable it here!
	$tonerandom = $_SESSION['cw_tone_random'];
    $_SESSION['cw_tone_random'] = false;

    // HACK: Enforce "VVV" prefix to be generated in getgroups() - only when we
    // use HTML5 player

    $pl = $_SESSION['player'];
    $_SESSION['player'] = PL_HTML5;
	$text = my_strtoupper(getgroups($_SESSION['download']['speed'], $_SESSION['download']['eff'], $nr, $char, $_SESSION['download']['duration'], $grouplength, false));
    
    $_SESSION['cw_tone_random'] = $tonerandom;

	if ($_POST['mode'] == 'callsigntraining') {
		$text = "";
		include_once("inc/calldb.php");
	
		# How many calls? 1 call = abt 5 letters -> duration * eff
		
		for ($j=0; $j < ($_SESSION['download']['duration']*$_SESSION['download']['eff']); $j++) {
			$text .= $calldb[rand(0,22000)]." ";
		}
	}

	if ($_POST['mode'] == 'wordtraining') {
		$tmplesson = $_SESSION['download']['lesson'] < 9 ? 9 : $_SESSION['download']['lesson'];

		# XXX XXX make language dependent xxx
		$words = gettextsbylanguage("words", $_SESSION['download']['wlanguage'],
				$_SESSION['download']['wmaxlen'],
				intval($_SESSION['download']['duration']*$_SESSION['download']['eff']),
				$_SESSION['download']['wsimplify'], $tmplesson);
        if (!$words) {
            echo "<p>Sorry, no words found for the selected lesson!</p>";
            return;
        }
		$text = implode(" ", $words);
	}
	
	$_SESSION['downloadtexts'][$i] = stripcommands($text);

	$text = rawurlencode($text);	

	$idnr++;	
	echo "<tr><td><a id='downloadmp3-$idnr'
	href=\"".CGIURL()."cw-download.mp3?d=$i&s=".$_POST['speed']."&e=".$_POST['eff']."&f=".$_POST['tone']."&t=$text\">lcwo-$i.mp3</a>".
	"</td><td><a id='downloadtxt-$idnr' href=\"/api/gettext.php?nr=$i\">lcwo-$i.txt</a></td></tr>\n";

    $_SESSION['player'] = $pl;  # restore player
} # for

?>
</table>

<script>
	function downloadall () {
		for (i=1; i <= <?=$idnr?> ; i++) {
			window.open(document.getElementById('downloadmp3-'+i).href);
			window.open(document.getElementById('downloadtxt-'+i).href);
		}
		return false;
	}
</script>

		<p><a href="#" onclick="return downloadall();"><strong><?=l('downloadallfiles')?></strong></a> (<?=l('warningpopupblocker')?>)</p>



<?
	}
//	else { # no POST, only check if lesson must be greyed
		/*
		<script type="text/javascript">
		lessoncheck();
		wordtrainingcheck();
		</script>
		*/
//		return;
//	}

?>

<script type="text/javascript">
lessoncheck();
wordtrainingcheck();
</script>



<?
function initsessiondownloads() {

		if ($_SESSION['uid']) {
			$_SESSION['download']['lesson'] = $_SESSION['koch_lesson'];
			$_SESSION['download']['wlanguage'] = array($_SESSION['lang']."0");
			$_SESSION['download']['duration'] = $_SESSION['koch_duration'];
			$_SESSION['download']['speed'] = $_SESSION['cw_speed'];
			$_SESSION['download']['eff'] = $_SESSION['cw_eff'];
			$_SESSION['download']['tone'] = $_SESSION['cw_tone'];
		}
		else {
			$_SESSION['download']['lesson'] = 40;
			$_SESSION['download']['wlanguage'] = array('en0');
			$_SESSION['download']['duration'] = 1;
			$_SESSION['download']['speed'] = 20;
			$_SESSION['download']['eff'] = 10;
			$_SESSION['download']['tone'] = 600;
		}

		$_SESSION['download']['mode'] = "kochmethodcourse";
		$_SESSION['download']['wsimplify'] = false;
		$_SESSION['download']['wmaxlen'] = 15;
		$_SESSION['download']['number'] = 5;
		$_SESSION['download']['set'] = 1;
}

function updatesessiondownloads() {

		global $wlangs, $dlmodes;
		global $langs;

		array_push($langs, "cw");

		if (in_array($_POST['mode'], $dlmodes)) 
			$_SESSION['download']['mode'] = $_POST['mode'];
		// Check language array -- XXX duplicate code from wordtraining.
		// put into function
		$langarray = $_POST['wlanguage'];
		if (count($langarray)) {
		    foreach ($langarray as $ltmp) {
    		    # Language must be formed   LLx   e.g. de0  de1 ... it2 
       		 	if ((!in_array(substr($ltmp, 0, 2), $langs))
       	     	    or  !isint(substr($ltmp,2,1))) {
						// malformed! Just be safe and use en0
						$langarray=array("en0");
       			 }
   			 }
			$_SESSION['download']['wlanguage'] = $langarray;
		}

		if (inrange($_POST['maxlen'], 4, 99)) 
			$_SESSION['download']['wmaxlen'] = $_POST['maxlen'];
		if (inrange($_POST['duration'], 1, 5)) 
			$_SESSION['download']['duration'] = $_POST['duration'];
		if (inrange($_POST['speed'], 5, 200)) 
			$_SESSION['download']['speed'] = $_POST['speed'];
		if (inrange($_POST['eff'],1,200)) 
			$_SESSION['download']['eff'] = $_POST['eff'];
		if (inrange($_POST['tone'],250,3000)) 
			$_SESSION['download']['tone'] = $_POST['tone'];
		if (inrange($_POST['number'],1,50)) 
			$_SESSION['download']['number'] = $_POST['number'];
		if (inrange($_POST['lesson'],1,40)) 
			$_SESSION['download']['lesson'] = $_POST['lesson'];
	
		$_SESSION['download']['wsimplify'] = $_POST['simplify'];
}

?>
