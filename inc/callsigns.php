<?
if (!$_SESSION['uid']) {
		echo "Sorry, you must be logged in to use this
		function.";
		return 0;
}

/* Set defaults */
if (!$_SESSION['callsigns']['set']) {
	$_SESSION['callsigns']['tone'] = intval((5+$_SESSION['cw_tone'])/10)*10;
	$_SESSION['callsigns']['tone_random'] = $_SESSION['cw_tone_random'];
	$_SESSION['callsigns']['mincharspeed'] = 10;
	$_SESSION['callsigns']['maxspeed'] = 125;
	$_SESSION['callsigns']['fixspeed'] = 0;
	$_SESSION['callsigns']['stoponerror'] = 0;
	$_SESSION['callsigns']['blind'] = 0;
	$_SESSION['callsigns']['filter'] = 0;
	$_SESSION['callsigns']['set'] = 1;
}

?>

<h1 id="header1"><? echo l('callsigntraining'); ?></h1>
<?
if (is_numeric($_POST['csspeed'])) { 
	callsignattempt();
}
else {
	callsigndialog();
}



function callsignattempt () {

?>
<script type="text/javascript">
var calls = new Array();
var cwspeed = <? if (is_numeric($_POST['csspeed'])) { echo $_POST['csspeed']; } else { echo "25"; }; ?>;
var mincharspeed = <? if (is_numeric($_POST['csmincharspeed'])) { echo $_POST['csmincharspeed']; } else { echo "0"; }; ?>;
var limitspeed = <? if (is_numeric($_POST['csmaxspeed'])) { echo $_POST['csmaxspeed']; } else { echo "150"; }; ?>;
var fixedspeed = <? if($_POST['csfixspeed']) { echo 1; } else {echo 0;} ?>;
var maxspeed = 0;
var score = 0;
var freq = <? echo intval($_POST['tone']) ? $_POST['tone'] : $_SESSION['cw_tone']; ?>;
var rand_tone = <? echo intval($_POST['tone1']); ?>;
var stop_on_error =  <? if($_POST['stoponerror']) { echo 1; } else {echo 0;} ?>;
var blind =  <? if($_POST['blind']) { echo 1; } else {echo 0;} ?>;
var stop_active = false;


nr = -1;		/* 0..24 */

/* 
 "For people who are honorable, the temptation to cheat is easily overcome." 
  --Randy K5ZD
  http://lists.contesting.com/archives//html/CQ-Contest/2008-04/msg00259.html
 */

<?
getcalls(); 
?>


// show or hide call table, score, etc.?
function hide_elements(x) {
    document.getElementById("calltable").style.display = x ? 'none' : '';
    document.getElementById("score").style.display = x ? 'none' : '';
    document.getElementById("curspeed").style.display = x ? 'none' : '';
    document.getElementById("maxspeed").style.display = x ? 'none' : '';
    document.getElementById("blindmode").style.display = x ? '' : 'none';
}

function check (call) {
	var error;

	if (nr >= 0) {
		
	call = call.toUpperCase();
	call = call.replace(/\s+/, "");

	
	var t = document.getElementById('calltable').rows[nr+1].cells;
	t[2].innerHTML = cwspeed;

    var last_speed = cwspeed;

	if (call != calls[nr]) {
			error = true;
			call = "<span style=\"color:#ff0000\">" + call +
			"&nbsp;</span>";
			if ((cwspeed > 5) && !fixedspeed ) {
					cwspeed--;
			}
	}
	else {
			error = false;
			if (cwspeed > maxspeed) {
				maxspeed = cwspeed;
			}
			if ((cwspeed < limitspeed) && !fixedspeed) {
				cwspeed++;
			}
			score += (cwspeed * calls[nr].length);
	}
	
	t[0].innerHTML = '<a href="javascript:playcall('+nr+', true, '+last_speed+');document.getElementById(' + "'" + 'callentry' + "'" + ').focus();">' + calls[nr] + '</a>';
	t[1].innerHTML = call;
	
	var s = document.getElementById('curspeed');
	s.innerHTML = cwspeed;
	
	var max = document.getElementById('maxspeed');
	max.innerHTML = maxspeed;
	
	var sc = document.getElementById('score');
	sc.innerHTML = score;
	
	var ce = document.getElementById('callentry');
	ce.value = "";
	
	}
	
	nr++;	

	if (nr < 25) {

		if (rand_tone) {
			freq = 500 + Math.ceil(400*Math.random());
		}

		if (!stop_on_error) {
			playcall(nr, true, 0);
		}
		else {
			if (!error) {
				playcall(nr, true, 0);
			}
			else {
				stop_active = true;
				playcall(nr, false, 0);	// only load the new call, do not play yet
			}
		}
		
	}
    else {
            hide_elements(false);
			var ef = document.getElementById('entryform');
			var h1 = document.getElementById('header1');
			var newattemptdivvar = document.getElementById('newattemptdiv');
			var valid = (fixedspeed == 1) ? 0 : 1;
			
			/* Submit the score via "AJAX" now... */
			submitscore("callsigns", maxspeed, score, valid, ef);
		
			h1.innerHTML = "<? echo l('callsigntraining',1)." - ".l('attemptfinished',1); ?>";
			newattemptdivvar.innerHTML = '<p><a href="/callsigns" id="newattempt"><? echo l('newattempt',1); ?></a></P>';
			document.getElementById('newattempt').focus();
			
	}
	
}

<? include "submitscore.js"; ?>


function playcall (cnr, auto_start, opt_speed) {

    if (opt_speed) {
        var cwspeedtmp = opt_speed;
    }
    else {
    	var cwspeedtmp = cwspeed;
    }

    var cwspeedeff = cwspeedtmp;

    if (cwspeedtmp < mincharspeed) {
        cwspeedtmp = mincharspeed;
    }

    var delay = <? if ($_SESSION['player'] != PL_JSCWLIB) { echo $_SESSION['delay_start']; } else { echo "0.05"; } ?>;

    if (delay) {
        var text = '|S' + (delay*1000) + ' ' + calls[cnr];
    }
    else 
    {
        var text = calls[cnr];
    }

    try {
    	var cs = document.getElementById('clicktostart');
        cs.innerHTML = '&nbsp; &nbsp; &nbsp; <input type="button" value="<?=l('pressdottoreplay',1);?>" onclick="playcall(nr, true, 0);return false;">';
    }
    catch (e) {
    }
	
    <? 
    if ($_SESSION['player'] == PL_HTML5) {              /* HTML5 player */
    ?>
    var p = document.getElementById('player1');
    p.src = '<?=CGIURL();?>cw.mp3?s='+cwspeedtmp+'&e='+cwspeedeff+'&f='+freq+'&t='+text;
    p.load();
	if (auto_start) {
		p.play();
	}
    <?
    }
    else if ($_SESSION['player'] == PL_JSCWLIB) {
    ?>
        pa[1].setText(text);
        pa[1].setWpm(cwspeedtmp);
        pa[1].setEff(cwspeedeff);
        pa[1].setFreq(freq);
        pa[1].enablePS(false);
        pa[1].play();
    <?
	}
    ?>
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
				var ce = document.getElementById('callentry');
				if (stop_on_error && stop_active) {
					playcall(nr, true, 0);
					stop_active = false;
				}
				else {
					check(ce.value);
                }
            ret = false;
            break;
		case 46:	/* dot */
		case 32:	/* space */
				if (nr >= 0) {	/* attempt already started */
					playcall(nr, true, 0);		/* repeat call */
					stop_active = false;
				}
				else {	/* start */
					var ce = document.getElementById('callentry');
					check(ce.value);
				}
            ret = false;
		    break;
	}
    document.getElementById('callentry').focus();
    return ret;
}
</script>

<table width="100%">
<tr>
<td width="25%">

<table id="calltable" border="1" width="80%">
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
<?

	# XXX Move this to start of function and use sanitized variables in the 
	# script
	$_SESSION['callsigns']['speed'] = intval($_POST['csspeed']);
	$_SESSION['callsigns']['mincharspeed'] = intval($_POST['csmincharspeed']);
	$_SESSION['callsigns']['maxspeed'] = intval($_POST['csmaxspeed']);
	if (isint($_POST['tone'])) {
	   $tone = $_POST['tone'];
	} else {
		$tone = $_SESSION['cw_tone'];		
	}; 
	$_SESSION['callsigns']['tone'] = $tone;
	$_SESSION['callsigns']['tone_random'] = $_POST['tone1'];
	$_SESSION['callsigns']['fixspeed'] = $_POST['csfixspeed'];
	$_SESSION['callsigns']['stoponerror'] = $_POST['stoponerror'];
	$_SESSION['callsigns']['blind'] = $_POST['blind'];
	$_SESSION['callsigns']['filter'] = intval($_POST['csfilter']);


?>
<p><? echo l('curspeed'); ?>: <span id="curspeed"><? echo $_POST['csspeed'];
?></span><? echo l('wpm'); ?> - <? echo l('maxspeed') ?>: <span id="maxspeed">0</span><? echo l('wpm') ?></p>
    <p><? echo l('score') ?>: <span id="score">0</span> <span id="blindmode"> (<?=l('blindmode');?>)</span></p>

<div id="entryform">

<form id="rform" action="" method="" onkeypress="return disableEnterKey(event)">
<input value="" spellcheck="false" autocapitalize="off" autocorrect="off" autocomplete="off" id="callentry" name="call" size="12">
<input id="okbutton" type="button" value="OK" onclick="javascript:if(stop_on_error && stop_active) { playcall(nr, true, 0); stop_active = false; } else { check(this.form.call.value); }; if (nr != -1) { document.getElementById('callentry').focus(); console.log('focus'); } "> <span id="clicktostart">
&lt;- <? echo l('clicktostart') ?></span> 
</form>

</div>

<div id="newattemptdiv"></div>

<br><br>

<?
    $mode = $_SESSION['player'];
    player("", $mode, $_SESSION['callsigns']['speed'], $_SESSION['callsigns']['speed'], 1, 1,0,0);
    
?>

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

    hide_elements(blind);

    var interval = window.setInterval(force_focus, 500);
</script>
<?



}



function callsigndialog () {

echo "<p>".l('callsigntraining1')."</p>";
echo "<p>".l('callsigntraining2')."</p>";

if (!isset($_SESSION['callsigns']['speed'])) {
	$_SESSION['callsigns']['speed'] = $_SESSION['cw_speed'];
}


?>

<form action="/callsigns" method="POST">

<table>
<tr>
<td><? echo l('speed')?>: </td>
<td>
<select name="csspeed" size="1">
<?

	for ($i = 5; $i < 101; $i++) {
		if ($i == $_SESSION['callsigns']['speed']) {
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
<td><? echo l('minimum')." ".l('charspeedlong')?>: </td>
<td>
<select name="csmincharspeed" size="1">
<?

	for ($i = 5; $i < 101; $i++) {
		if ($i == $_SESSION['callsigns']['mincharspeed']) {
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
<td><? echo l('max')." ".l('speed')?>: </td>
<td>
<select name="csmaxspeed" size="1">
<?

	for ($i = 5; $i < 131; $i++) {
		if ($i == $_SESSION['callsigns']['maxspeed']) {
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
<td>
<? echo l('fixedspeed'); ?>*:
</td>
<td>
<input type="checkbox" name="csfixspeed"
<? if ($_SESSION['callsigns']['fixspeed']) {
	echo ' checked ';
}
?>
>
</td>
</tr>

<tr>
<td><? echo l('tone')?>: </td>
<td>
<input type="radio" name="tone1" value="0" <? 
if (!$_SESSION['callsigns']['tone_random']) {
	echo " checked ";
}
?>>
<select name="tone" size="1">
<?
	for ($i = 400; $i <= 1000; $i += 10) {
		if ($i == $_SESSION['callsigns']['tone']) {
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
if ($_SESSION['callsigns']['tone_random']) {
	echo " checked ";
}
?>
> 
<? echo l('random') ?>
 (500-900Hz)
</td>
</tr>

<tr>
<td><? echo l('filtercalls');?>: </td>
<td>
<select name="csfilter" size="1">
<?
$filters = array(l('nofilter',1), l('filterlongcalls',1), l('filterslashedcalls',1));

	for ($i = 0; $i < 3; $i++) {
		if ($i == $_SESSION['callsigns']['filter']) {
			echo "<option value=\"$i\" selected>".$filters[$i]."</option>";
		}
		else {
			echo "<option value=\"$i\">".$filters[$i]."</option>";
		}
	}
?>
</select> 
</td>
</tr>

<tr>
<td>
Stop on error:
</td>
<td>
<input type="checkbox" name="stoponerror" <? if ($_SESSION['callsigns']['stoponerror']) { echo ' checked '; } ?> >
</td>
</tr>

<tr>
<td>
<?=l('blindmode');?>:
</td>
<td>
<input type="checkbox" name="blind" <? if ($_SESSION['callsigns']['blind']) { echo ' checked '; } ?> >
</td>
</tr>



<tr>
<td>
&nbsp;
<input type="hidden" name="sent" value="1">
		<input type="submit" id="startbutton" value=" <? echo l('start',1) ?> ">
</td>
</tr>
</table>
</form>

<p>
&nbsp; &nbsp; * <? echo l('ineligiblescore'); ?>
</p>
<script type="text/javascript">
	document.getElementById('startbutton').focus();
</script>


<?
}
?>
</td>
</tr>
</table>
<? echo "<!-- ".time()." -->"; ?>

<?

# XXX move calls to database!
function getcalls () {
include("inc/calldb.php");
$i = 25;
do {
	$next = 0;
	$k = rand(0, count($calldb)-1);

	switch ($_POST['csfilter']) {
		case 2:		/* remove slashed and long calls */
			if (strstr($calldb[$k], "/")) {
				$next = 1;
			}
			/* fallthrough! */
		case 1:		/* remove long calls */
			if (preg_match('/\d{2,6}/', $calldb[$k])) {
				$next = 1;
				break;
			}
			/* long suffixes */
			if (preg_match('/[A-Z]{4,12}/', $calldb[$k])) {
				$next = 1;
				break;
			}
			break;
		case 0:	/* all calls are beautiful */
			break;
	}
	
	if (!$next) {
		echo " calls[".($i-1)."] = \"".$calldb[$k]."\"; ";
		$i--;
	}
} while ($i);


}

?>
