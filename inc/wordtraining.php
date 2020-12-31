<?
if (!$_SESSION['uid']) {
		echo "Sorry, you must be logged in to use this
		function.";
		return 0;
}

/* Set defaults only once */
if (!($_SESSION['wordtraining']['set'] == TRUE)) {
	$_SESSION['wordtraining']['speed'] = $_SESSION['cw_speed'];
	$_SESSION['wordtraining']['mincharspeed'] = 10;
	$_SESSION['wordtraining']['maxlength'] = 15;
	$_SESSION['wordtraining']['lesson'] = 40;
	$_SESSION['wordtraining']['fixspeed'] = 0;
	$_SESSION['wordtraining']['autoskip'] = 0;
	$_SESSION['wordtraining']['lang'] = array($_SESSION['lang']."0");
	$_SESSION['wordtraining']['tone'] = $_SESSION['cw_tone'];
	$_SESSION['wordtraining']['tone_random'] = $_SESSION['cw_tone_random'];
}


?>

<h1 id="header1"><? echo l('wordtraining'); ?></h1>

<?
if ($_POST['speed']) {
	wordtraining();
}
else {
	parameterdialog();
}



function parameterdialog () {

global $langnames;
		
echo "<p>".l('wordtraining1')."</p>";
echo "<p>".l('wordtraining2')."</p>";
echo "<p>".l('wordtraining3')."</p>";

?>
<form action="/wordtraining" method="POST">
<table>
<tr>
<td><? echo l('speed')?>: </td>
<td>
<select name="speed" size="1">
<?
	for ($i = 5; $i < 101; $i++) {
		if ($i == $_SESSION['wordtraining']['speed']) {
			echo "<option selected>$i</option>";
		}
		else {
			echo "<option>$i</option>";
		}
	}
?>
</select> <? echo l('wpm'); ?>
</td>
</tr>
<tr>
<td> <? echo l('minimum')." ".l('charspeedlong')?>: </td>
<td>
<select name="mincharspeed" size="1">
<?
	for ($i = 5; $i < 101; $i++) {
		if ($i == $_SESSION['wordtraining']['mincharspeed']) {
			echo "<option selected>$i</option>";
		}
		else {
			echo "<option>$i</option>";
		}
	}
?>
</select> <? echo l('wpm'); ?>
</td>
</tr>
<tr>
<td> <? echo l('charsfromlesson')?>: </td>
<td>
<select name="lesson" size="1">
<?
	for ($i = 9; $i <= 40; $i++) {
		if ($i == $_SESSION['wordtraining']['lesson']) {
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

<tr>
<td><? echo l('tone')?>: </td>
<td>
<input type="radio" name="tone1" value="0" <? 
if (!$_SESSION['wordtraining']['tone_random']) {
	echo " checked ";
}
?>>
<select name="tone" size="1">
<?
	for ($i = 400; $i <= 1000; $i += 10) {
		if ($i == $_SESSION['wordtraining']['tone']) {
			echo "<option selected>$i</option>";
		}
		else {
			echo "<option>$i</option>";
		}
	}
?>
</select> Hz <br>
<input type="radio" name="tone1" value="1"
<?
if ($_SESSION['wordtraining']['tone_random']) {
	echo " checked ";
}
?>
> 
<? echo l('random') ?>
 (500-900Hz)
</td>
</tr>

<tr>
<td valign="top">
<? echo l('language')." / ".l('collection'); ?>:
</td>
<td valign="top">
<select name="lang[]" size="5" multiple>
<?
    $preflangs = $_SESSION['wordtraining']['lang'];
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
</td>
</tr>
<tr>
<td>
<? echo l('maxlength'); ?>:
</td>
<td>
<input type="text" name="maxlength" value="<? echo
$_SESSION['wordtraining']['maxlength']; ?>" size="2">
</td>
</tr>
<tr>
<tr>
<td>
<? echo l('simplify'); ?>:
</td>
<td>
<input type="checkbox" name="simplify" 
<? if ($_SESSION['wordtraining']['simplify']) {
	echo ' checked ';
}?>>
</td>
</tr>
<tr>
<td>
<? echo l('fixedspeed'); ?>:
</td>
<td>
<input type="checkbox" name="fixspeed"
<? if ($_SESSION['wordtraining']['fixspeed']) {
	echo ' checked ';
}
?>
>
</td>
</tr>
<tr>
<td>
<? echo l('autoskip'); ?>:
</td>
<td>
<input type="checkbox" name="autoskip"
<? if ($_SESSION['wordtraining']['autoskip']) {
	echo ' checked ';
}
?>
>
</td>
</tr>
<tr>
<td>
<input type="hidden" name="sent" value="1">
<input type="submit" id="startbutton" value=" <? echo l('start',1) ?> ">
</td>
</tr>
</table>
</form>

<script type="text/javascript">
    document.getElementById('startbutton').focus();
</script>


<br>
<br>
<br>
<?
echo "<p>".l('wordtraining4')."</p>";
}/* parameterdialog */



function wordtraining () {

	global $langs;

	array_push($langs, "cw");	

	if (empty($_POST['lang'])) {
			echo "Error: No language selected!\n";
			return;
	}

	# $_POST['lang'] is an array of the languages + collections
	# which the user selected, e.g.
	# de0  en0  en1  pt0   ...
	#
	# Check if all languages are in a suitable format

	# XX duplicate code - put into function
	foreach ($_POST['lang'] as $ltmp) {
		# Language must be formed   LLx   e.g. de0  de1 ... it2	
		if ((!in_array(mb_substr($ltmp, 0, 2), $langs))
				or  !isint(mb_substr($ltmp,2,1))) {
				echo "Error. Invalid language!";
				return;
		}
	}
	
	$langarray = $_POST['lang'];


	if (isint($_POST['speed'])) {
	   $speed = $_POST['speed'];
	} else {
		$speed = 25;		
	}; 
	
	if (isint($_POST['mincharspeed'])) {
	   $mincharspeed = $_POST['mincharspeed'];
	} else {
		$mincharspeed = 0;	
	}; 

	if (isint($_POST['tone'])) {
	   $tone = $_POST['tone'];
	} else {
		$tone = $_SESSION['cw_tone'];		
	}; 

	// Random
	if ($_POST['tone1']) {
		$tone = 0;
	}

	
	if (isint($_POST['maxlength'])) {
		$maxlen = $_POST['maxlength'];
	}
	else {
		$maxlen = 99;
	}

	if (isint($_POST['lesson']) and $_POST['lesson'] <= 40 and $_POST['lesson'] >= 9){
		$lesson = $_POST['lesson'];
	}
	else {
		$lesson = 40;
	}

	if ($_POST['fixspeed']) {
		$fixspeed = 1;
	}
	else {
		$fixspeed = 0;
	}

	if ($_POST['simplify']) {
		$simplify = 1;
	}
	else {
		$simplify = 0;
	}

	if ($_POST['autoskip']) {
		$autoskip = 1;
	}
	else {
		$autoskip = 0;
	}

	$_SESSION['wordtraining']['speed'] = $speed;
	$_SESSION['wordtraining']['mincharspeed'] = $mincharspeed;
	$_SESSION['wordtraining']['tone'] = $tone;
	$_SESSION['wordtraining']['tone_random'] = $_POST['tone1'];
	$_SESSION['wordtraining']['lang'] = $langarray;
	$_SESSION['wordtraining']['lesson'] = $lesson;
	$_SESSION['wordtraining']['maxlength'] = $maxlen;
	$_SESSION['wordtraining']['fixspeed'] = $fixspeed;
	$_SESSION['wordtraining']['autoskip'] = $autoskip;
	$_SESSION['wordtraining']['simplify'] = $simplify;
	$_SESSION['wordtraining']['set'] = TRUE;
		
?>

<script type="text/javascript">
var skiptimer;
var words = new Array();
var cwspeed =<? echo $speed; ?> ;
var mincharspeed = <? echo $mincharspeed; ?>;
var maxspeed = 0;
var score = 0;
var tone  = <? echo $tone; ?>;
var thistone = tone;

var h5c = "cw-u.mp3";   // cw-unicode

var nr = -1;		/* 0..24 */

/* 
 "For people who are honorable, the temptation to cheat is easily overcome." 
  --Randy K5ZD
  http://lists.contesting.com/archives//html/CQ-Contest/2008-04/msg00259.html
 */
<?

	$words = gettextsbylanguage("words", $langarray, $maxlen, 25, $simplify, $lesson);

	for($i = 0; $i < 25; $i++) {
		 echo " words[$i] = \"".$words[$i]."\"; ";
	}

?>


function check (word) {

	if (nr >= 0) {
		
	word = word.toLowerCase();
	word = word.replace(/\s+/, "");

	var t = document.getElementById('wordtable').rows[nr+1].cells;

	t[2].innerHTML = cwspeed;
	
	if (word != words[nr]) {
			word = "<span style=\"color:#ff0000\">" + word +
			"&nbsp;</span>";
			if (cwspeed > 5) {
					<?
						if (!$fixspeed) {
					?>
					cwspeed--;
					<?
					}
					?>
			}
	}
	else {
			
			if (cwspeed > maxspeed) {
				maxspeed = cwspeed;
			}

			<?
			if (!$fixspeed) {
			?>
			cwspeed++;
			<?
			}
			?>
			score += (cwspeed * words[nr].length);
	
	}

	t[0].innerHTML = words[nr];
	t[1].innerHTML = word;
	
	var s = document.getElementById('curspeed');
	s.innerHTML = cwspeed;
	
	var max = document.getElementById('maxspeed');
	max.innerHTML = maxspeed;
	
	var sc = document.getElementById('score');
	sc.innerHTML = score;
	
	var ce = document.getElementById('wordentry');
	ce.value = "";
	
	}
	
	nr++;	

	// Random tone?
	if (tone == 0) {
		thistone = (500 + parseInt(Math.random() * (900-500+1)));
   	}
	else {
		thistone = tone;
	}
	
	
	if (nr < 25) {
		playcall();	
	}
	else {
			var ef = document.getElementById('entryform');
			var h1 = document.getElementById('header1');
			var newattemptdivvar = document.getElementById('newattemptdiv');

			submitscore("words", maxspeed, score, 1, ef);

			h1.innerHTML = '<? echo l('wordtraining',1)." - ".l('attemptfinished',1); ?>';


			newattemptdivvar.innerHTML = '<p><a href="/wordtraining" id="newattempt"><? echo l('newattempt',1); ?></a></P>';
			document.getElementById('newattempt').focus();
			
	}
	
}

<? include "submitscore.js"; ?>


function playcall () {
	var cwspeedtmp = cwspeed;
		if (cwspeed < mincharspeed) {
		cwspeedtmp = mincharspeed;  
	}
    var delay = <? if ($_SESSION['player'] != PL_JSCWLIB) { echo $_SESSION['delay_start']; } else { echo "0.05"; } ?>;
    var autoskip = <?=$_SESSION['wordtraining']['autoskip']?>;

    if (delay) {
        var text = '|S' + (delay*1000)+ ' ' + words[nr];
    }
    else {
        var text = words[nr];
    }
			
	var cs = document.getElementById('clicktostart');
	cs.innerHTML = '&nbsp; &nbsp; &nbsp; <input type="button" value="<?=l('pressdottoreplay',1);?>" onclick="playcall();return false;">';

    <?    
	if ($_SESSION['player'] == PL_HTML5) {				/* HTML5 player */
	?>
	var p = document.getElementById('player1');
	p.src = '<?=CGIURL();?>'+h5c+'?s='+cwspeedtmp+'&e='+cwspeed+'&f='+thistone+'&t='+text;	
	p.load();
	p.play();
	<?
	}
	else if ($_SESSION['player'] == PL_JSCWLIB) {
    ?>
        pa[1].setText(text);
        pa[1].setWpm(cwspeedtmp);
        pa[1].setEff(cwspeed);
        pa[1].setFreq(thistone);
        pa[1].enablePS(false);
        pa[1].play();
    <?
	}
	?>

    if (autoskip) {
<?
    if ($_SESSION['player'] == PL_HTML5) {
?>
        p.onended = function () {
            console.log("onended => skip timer started");
            skiptimer = window.setInterval(skip, 5000);
        }
<?
    }
    else if ($_SESSION['player'] == PL_JSCWLIB) {
?>
        skiptimer = window.setInterval(skip, 5000 + pa[1].getLength()*1000);
<?
    }
?>
    } /* if autoskip */
}

function skip () {
    console.log("skip");
    window.clearInterval(skiptimer);
    check("");
}


// http://www.arraystudio.com/as-workshop/disable-form-submit-on-enter-keypress.html
function disableEnterKey(e)
{
     var key;

     if(window.event)
          key = window.event.keyCode;     //IE
     else
          key = e.which;     //firefox

    var ret = true;
	switch (key) {
			case 13:
				var ce = document.getElementById('wordentry');
				check(ce.value);
                ret = false;
                break;
			case 46:
			case 32:
				if (nr >= 0) {
					playcall();
				}
				else {
					var ce = document.getElementById('wordentry');
					check(ce.value);
				}
                ret = false;
                break;
	}

    document.getElementById('wordentry').focus();
    return ret;

}





</script>


<table width="100%">
<tr>
<td width="25%">


<table id="wordtable" border="1" width="80%">
<tr>
<th width="40%"><? echo l('sent') ?></th><th width="40%"><? echo l('received'); ?></th><th><? echo l('wpm'); ?></th>
</tr>
<? for ($i=0; $i < 25; $i++) {
echo "<tr>
<td class=\"smalltd\">-</td>
<td class=\"smalltd\">-</td>
<td class=\"smalltd\">-</td>
</tr>\n";
}
?>
</table>

</td>
<td valign="top">

<? if ($_POST['speed']) { ?>

<p><? echo l('curspeed'); ?>: <span id="curspeed"><? echo $_POST['speed'];
?></span><? echo l('wpm'); ?> - <? echo l('maxspeed') ?>: <span id="maxspeed">0</span><? echo l('wpm') ?></p>
	<p><? echo l('score') ?>: <span id="score">0</span></p>

<div id="entryform">

<form id="rform" action="" method="" onkeypress="return disableEnterKey(event)">
<input spellcheck="false" autocapitalize="off" autocorrect="off" autocomplete="off" value="" id="wordentry" name="word" size="12">
<input type="button" id="okbutton" value="OK" onclick="check(this.form.word.value);document.getElementById('wordentry').focus();"> <span id="clicktostart">
&lt;- <? echo l('clicktostart') ?></span> 
</form>

</div>

<div id="newattemptdiv"></div>

<br><br>

<script>
    // modern firefox and chrome browsers do not allow autoplay any more.
    // we can only play sound if the user "clicked or tapped". pressing 
    // enter in the callsign field is not sufficient, but moving the focus
    // to the "OK" button and having the user press enter works. so we force
    // this behaviour at the start.
    function force_focus () {
        if (nr == -1) {
            document.getElementById('okbutton').focus(); 
        }
        else {
            window.clearInterval(interval);
            console.log('cleared');
        }
    }
    var interval = window.setInterval(force_focus, 500);
</script>

<?
	$mode = $_SESSION['player'];
    player("", $mode, $speed, $speed, 1, 1,0,0);

}
else {
}
?>
</td>
</tr>
</table>
<? echo "<!-- ".time()." -->"; ?>

<?
} /* wordtraining */


function finishattempt () {

echo "<h2>".l('attemptfinished')."</h2>\n";

$score = $_GET['score'];
$max = $_GET['max'];

if (!is_numeric($score) or !is_numeric($max)) {
	echo "<p>Sorry. The score and/or max values are not
	numeric.</p>";
	return 0;
}

if (($score*7+$max*34)/12 != $_GET['checksum']) {
	echo "<p>Error: Checksum wrong!</p>";
	return 0;
}

$in = mysqli_query($db,"insert into lcwo_wordsresults (`uid`,
`max`, `score`, `time`) VALUES ('".$_SESSION['uid']."', '$max',
'$score', NULL)");

?>
<p>
<? echo l('scoreadded')." ($score / $max ".l('wpm').")"; ?>
</p>
		<p><a href="/"><? echo l('home') ?></a></P>
		<p><a href="/wordtraining" id="newattempt"><? echo l('newattempt'); ?></a></P>

<script type="text/javascript">	
	document.getElementById('newattempt').focus();	
</script>
<?

} /* finishattempt */


?>
<div class="vcsid">$Id: wordtraining.php 26 2014-12-18 09:43:20Z fabian $</div>

