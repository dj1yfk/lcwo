<?
if (!$_SESSION['uid']) {
echo "Sorry, you must be logged in to use this function.";
return 0;
}

# Handle actions

if ($_POST['speed']) {
	qtctrainer();
}
else {
	qtcdialog();
}

function qtctrainer () {

$speed = intval($_POST['speed']);
$abbrev = intval($_POST['abbrev']);

$_SESSION['qtc']['cw_speed'] = $speed;
$_SESSION['qtc']['abbrev'] = $abbrev;

if (!$speed) {
	$speed = 30;
}
		
?>

<script type="text/javascript" src="/js/keys.js"></script>
<script type="text/javascript">

// Global Variables
var currentqtc = 1;
var goodqtcs;
var started = 0;
var sentgrpnr = 0;
var currentfocus;
var cwspeed = <? echo $speed; ?>;
var cwfreq = <? echo $_SESSION['cw_tone']; ?>;
var abbreviatemode = <? echo $abbrev; ?>;
var i=0;
var validated = 0;
var shiftpressed = 0;


// MP3 for everyone - dj1yfk 2017-10-06
var h5c = "cw.mp3";

qtcs = new Array();

/*
 "For people who are honorable, the temptation to cheat is easily overcome."
   --Randy K5ZD
   http://lists.contesting.com/archives//html/CQ-Contest/2008-04/msg00259.html
*/
<?
	include('eucalls2.php');

	echo "qtcs[0] = '".(intval(rand(1,99)))."/10'\n";
	
	for ($i=1; $i < 11; $i++) {
		$call = $calldb[intval(rand(0,5760))];
		$time = intval(rand(10,23)).intval(rand(10,59));
		echo "qtcs[$i]='$time;$call;".(intval(rand(10,800)))."'\n";
	}
?>

function startQTCs () {

sentgrpnr = 1;
playerload(cwspeed, cwspeed, cwfreq, " QTC " + qtcs[0] + " = QRV?  ^");

document.getElementById('grpnr').focus();
		
}

function keyaction(e) {
	var key;
	if(window.event) {
		key = window.event.keyCode;     // IE
	}
	else {
		key = e.which;     // firefox
	}

	if(key == 13)  {

		// Very beginning, nothing has been sent yet. Send Grp/Nr
		if (sentgrpnr == 0) {
			startQTCs();
			return false;
		}		
		
		// Confirm Grp/Nr and start 1st QTC
		if (started == 0) {
			started = 1;
			sendQTC(0);
			document.getElementById('time1').focus();
			return false;
		}
			
		// Check QTC 
		if (currentqtc < 10) {
			var tmp = document.getElementById('call'+currentqtc);
		    tmp.value = tmp.value.toUpperCase();
			currentqtc++;
			sendQTC(0);
			document.getElementById('time'+currentqtc).focus();
		}
		else {
			finishQTCs();
		}
		return false;
	}
	else if (key == 32) {	// Space -> Focus to next field in QTC
		// If we have not started, go to the first QTC field and
		// start the fun
		if (started == 0) {
			started = 1;
			sendQTC(0);
			document.getElementById('time'+currentqtc).focus();
			return false;	
		}
		if (currentfocus == "time"+currentqtc) {
			document.getElementById('call'+currentqtc).focus();
		}
		else if (currentfocus == "call"+currentqtc) {
			document.getElementById('nr'+currentqtc).focus();
		}
		else if (currentfocus == "nr"+currentqtc) {
			document.getElementById('time'+currentqtc).focus();
		}
		return false;
	}
	/* repeat QTC: F7 or F8 */
	else if (key == 118 || key == 119) {	
		if (started == 0) {
				startQTCs();
		}
		else {
			sendQTC(1);
		}
		return false;
	}
    else if (key == 120) {  /* F9 = new attempt */
        newattempt();
    }
	else {
		return true;
	}
}

function hasfocus (x) {
	x.value = x.value; 
	currentfocus = x.id;
}

function makeallcallsuppercase () {
	var i, tmp;
	for (i = 1; i<=10; i++) {
		tmp = document.getElementById('call'+i);
	    tmp.value = tmp.value.toUpperCase();
	}	
}

function playerload (wpm, eff, freq, text) {
    var file = '?s='+wpm+'&e='+eff+'&f='+freq+'&t= ' + text;
	<?
	if($_SESSION['player'] == PL_HTML5) {	/* HTML 5 */
	?>
		var p = document.getElementById('player1');
		p.src = '<?=CGIURL();?>'+h5c+file;
		p.load();
		p.play();	
	<?
	}
    else if ($_SESSION['player'] == PL_JSCWLIB) {
    ?>
        pa[1].setText(text);
        pa[1].setWpm(wpm);
        pa[1].setEff(eff);
        pa[1].setFreq(freq);
        pa[1].setStartDelay(0.1);
        pa[1].enablePS(false);
        pa[1].play();
    <?
    }
	?>
}

function validateQTC() {
	var i, j, qtcgood;
	var t = document.getElementById('qtctable')
	var rcvdqtc = new Array();
	var grpnr = document.getElementById('grpnr').value;
	
	makeallcallsuppercase();

	if (grpnr != qtcs[0]) {
			t.rows[1].cells[0].innerHTML = 
			"<span style=\"text-decoration:line-through\">"+
			grpnr+"</span> <span style=\"color:#ff0000\">("+qtcs[0]
			+")</span>";
	}
	else {
			t.rows[1].cells[0].innerHTML = 
			"<span style=\"color:#348017;font-weight: bold;\">"+
			grpnr+"</span>";
	}

	goodqtcs=0;	
	for (i=1; i <= 10; i++) {
		thisqtc = qtcs[i].split(";");
		qtcgood = 1;
		rcvdqtc[0] = document.getElementById('time'+i).value;
		rcvdqtc[1] = document.getElementById('call'+i).value;
		rcvdqtc[2] = document.getElementById('nr'+i).value;

		for (j=0; j < 3; j++) {
			if (rcvdqtc[j] != thisqtc[j]) {
				qtcgood = 0;
				t.rows[i].cells[j+1].innerHTML = 
				"<span style=\"text-decoration:line-through\">"+
				rcvdqtc[j]+"</span> <span style=\"color:#ff0000\">("+thisqtc[j]
				+")</span>";
			}
			else {
				t.rows[i].cells[j+1].innerHTML = 
				"<span style=\"color:#348017;font-weight: bold;\">"+
				rcvdqtc[j]+"</span>";
			}
		}
		if (qtcgood) {
			goodqtcs++;
		}
		
	}

	document.getElementById('err').innerHTML = '<strong><? echo l('attemptfinished',1); ?>. '+goodqtcs+' / 10 QTCs @ '+cwspeed+'WpM.'
		+'</strong> ';

	validated = 1;

	submitscore();

    document.getElementById('newattempt').focus();
	
	return false;
}


function sendQTC (repeat) {
	var QTC = qtcs[currentqtc].split(";");
	var r;

	if (repeat) {
		 r = '|f500 agn |f<?=$_SESSION['cw_tone'];?> ';
	}
    else {
		 r = '|f500 R |f<?=$_SESSION['cw_tone'];?> ';
    }

	QTC[0] = abbreviatenumbers(QTC[0]);	
	QTC[2] = abbreviatenumbers(QTC[2]);	

    playerload(cwspeed, cwspeed, <?=$_SESSION['cw_tone'];?>, r+' '+QTC[0]+' '+QTC[1]+' '+QTC[2]+'         ^');

}


function abbreviatenumbers (x)  {
		var num = new Array();
		var rep = new Array();

		switch (abbreviatemode) {
			case 0:
					return x;
					break;
			case 1:			/* 1, 9 and 0 */
				num = ['1', '9', '0'];
				rep = ['A', 'N', 'T'];
				break;
			case 2:
				num = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0'];
				rep = ['A', 'U', 'V', '4', 'E', '6', 'B', 'D', 'N', 'T'];
				break;
		}

		var q, y;
		for (q = 0; q < num.length; q++) {
			for (y = 0; y < x.length; y++) {
				x = x.replace(num[q], rep[q]);
			}
		}
		return x;
}

var finished = false;
function finishQTCs () {
    if (!finished) {    /* press enter first time after last QTC */
    	var grpnr = document.getElementById('grpnr').value;
        playerload(cwspeed, cwspeed, 500, ' R QSL QTC '+grpnr+'  ^');
        finished = true;
    }
    else {              /* press enter second time after last QTC - Check */
        validateQTC();
    }

}



// Just cancel this attempt by clearing all fields and reloading the site.
function newattempt () {
	var i, tmp;

	if (!validated) {
		for (i = 1; i<=10; i++) {
			document.getElementById('call'+i).value = '';
			document.getElementById('time'+i).value = '';
			document.getElementById('nr'+i).value = '';
		}	
		document.getElementById('grpnr').value = '';
	}

	location.reload()
}


function submitscore () {

// Provide the XMLHttpRequest class for IE 5.x-6.x:
if( typeof XMLHttpRequest == "undefined" ) XMLHttpRequest = function() {
  try { return new ActiveXObject("Msxml2.XMLHTTP.6.0") } catch(e) {}
  try { return new ActiveXObject("Msxml2.XMLHTTP.3.0") } catch(e) {}
  try { return new ActiveXObject("Msxml2.XMLHTTP") } catch(e) {}
  try { return new ActiveXObject("Microsoft.XMLHTTP") } catch(e) {}
  throw new Error( "This browser does not support XMLHttpRequest." )
};

  // generate submit URL depending on current host
  var url = window.location.href;
  var arr = url.split("/");
  var posturl = "//" + arr[2] + "/api/qtcsubmit.php";



  var request =  new XMLHttpRequest();
  request.open("POST", posturl, true);
  request.setRequestHeader("Content-Type",
                           "application/x-www-form-urlencoded");
 
  request.onreadystatechange = function() {
    var done = 4, ok = 200;
    if (request.readyState == done && request.status == ok) {
      if (request.responseText) {
		document.getElementById('msg2').innerHTML = request.responseText;
      }
	  else {
		document.getElementById('msg2').innerHTML="<b>Adding score failed.</b>";
	  }
    }
  };
  request.send('speed='+cwspeed+'&good='+goodqtcs);
}


</script>


<h2><? echo l('qtctraining')." - ".$speed." ".l('wpm'); ?></h2>
<span id="err"><? echo l('message') ?>: </span><br>
<span id="msg2"></span>

<br>



<form onkeydown="return keyaction(event);" onSubmit="return false;">
<table width="85%">

<tr><td width="66%">
<table class="tborder" width="100%" id="qtctable">
<tr><th class="tborder">Gr/Num</th><th class="tborder"><? echo
l("time") ?></th><th class="tborder"><? echo l('callsign')
?></th><th class="tborder"><? echo l('serial') ?></th></tr>
<tr>
<td><input type="text" size="6" id="grpnr"></td>
<td><input type="text" size="4" id="time1" onFocus="hasfocus(this)"></td>
<td><input type="text" size="12" id="call1" onFocus="hasfocus(this)"></td>
<td><input type="text" size="4" id="nr1" onFocus="hasfocus(this)"></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type="text" size="4" id="time2" onFocus="hasfocus(this)"></td>
<td><input type="text" size="12" id="call2" onFocus="hasfocus(this)"></td>
<td><input type="text" size="4" id="nr2" onFocus="hasfocus(this)"></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type="text" size="4" id="time3" onFocus="hasfocus(this)"></td>
<td><input type="text" size="12" id="call3" onFocus="hasfocus(this)"></td>
<td><input type="text" size="4" id="nr3" onFocus="hasfocus(this)"></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type="text" size="4" id="time4" onFocus="hasfocus(this)"></td>
<td><input type="text" size="12" id="call4" onFocus="hasfocus(this)"></td>
<td><input type="text" size="4" id="nr4" onFocus="hasfocus(this)"></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type="text" size="4" id="time5" onFocus="hasfocus(this)"></td>
<td><input type="text" size="12" id="call5" onFocus="hasfocus(this)"></td>
<td><input type="text" size="4" id="nr5" onFocus="hasfocus(this)"></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type="text" size="4" id="time6" onFocus="hasfocus(this)"></td>
<td><input type="text" size="12" id="call6" onFocus="hasfocus(this)"></td>
<td><input type="text" size="4" id="nr6" onFocus="hasfocus(this)"></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type="text" size="4" id="time7" onFocus="hasfocus(this)"></td>
<td><input type="text" size="12" id="call7" onFocus="hasfocus(this)"></td>
<td><input type="text" size="4" id="nr7" onFocus="hasfocus(this)"></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type="text" size="4" id="time8" onFocus="hasfocus(this)"></td>
<td><input type="text" size="12" id="call8" onFocus="hasfocus(this)"></td>
<td><input type="text" size="4" id="nr8" onFocus="hasfocus(this)"></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type="text" size="4" id="time9" onFocus="hasfocus(this)"></td>
<td><input type="text" size="12" id="call9" onFocus="hasfocus(this)"></td>
<td><input type="text" size="4" id="nr9" onFocus="hasfocus(this)"></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type="text" size="4" id="time10" onFocus="hasfocus(this)"></td>
<td><input type="text" size="12" id="call10" onFocus="hasfocus(this)"></td>
<td><input type="text" size="4" id="nr10" onFocus="hasfocus(this)"></td>
</tr>
</table>
</td>
<td valign="top" width="33%">
<table width="90%" class="tborder">
	<tr><th class="tborder" colspan="2"><? echo l('keys') ?></th></tr>
	<tr> <td> <strong><? echo l('spacebar') ?></strong> </td> <td>
	<? echo l('nextfield') ?> </td></tr>
	<tr> <td> <strong><? echo l('returnkey') ?></strong> </td> <td> <? echo l('nextqtc'); ?> </td></tr>
	<tr> <td> <strong>F7/F8</strong> </td> <td> <? echo l('repeatqtc'); ?> </td></tr>
	<tr> <td> <strong>F9</strong> </td> <td> <? echo l('newattempt'); ?> </td></tr>
	<tr> <td> &nbsp; </td></tr>
	<tr><th class="tborder" colspan=2><? echo l('functions') ?></th></tr>
	<tr> <td colspan=2> <center> <input type="submit"
	style="width:80%;font-weight:bold" id="startqtc" value="<? echo l('start',1) ?>" onClick="startQTCs();" > </center> </td> </tr>
	<tr> <td colspan=2> <center> <input type="submit" style="width:80%" 
	value="<? echo l('validate',1) ?>" onClick="validateQTC();"> </center> </td> </tr>
 	<tr> <td colspan=2> <center> <input type="submit" style="width:80%" 
	value="<? echo l('cancel',1) ?>" onClick="newattempt();";> </center> </td> </tr>
 	<tr> <td colspan=2> <center> <input type="submit" id="newattempt"
	style="width:80%" value="<? echo l('newattempt',1) ?>"
	onClick="window.location.href='/qtc'";> </center> </td> </tr>
	</table>
</td>
</tr>
</table>
</form>


<?
$mode = $_SESSION['player'];
player ("", $mode, 99, 99, 1, 1,0,0);
?>

<script type="text/javascript">
var times = new Array();
var calls = new Array();
var nrs = new Array();
var i,j;

for (i=1; i<=10; i++) {

times[i] = new LiveValidation('time'+i, { insertAfterWhatNode: 'err' });
times[i].add( Validate.Numericality, { minimum: 1000, maximum:
2359, notANumberMessage: '', wrongNumberMessage: '<? echo
l('timeinvalid',1); ?>', tooLowMessage: '<? echo l('timeinvalid',1) ?>', 
tooHighMessage:'<? echo l('timeinvalid',1) ?>' } );

calls[i] = new LiveValidation('call'+i, { insertAfterWhatNode: 'err' });
calls[i].add( Validate.Format, { pattern: /^[a-zA-Z0-9\/]+$/i, 
failureMessage: "<? echo l('callinvalid',1)?>" });

nrs[i] = new LiveValidation('nr'+i, { insertAfterWhatNode: 'err' });
nrs[i].add( Validate.Numericality, { minimum: 0, maximum: 9999, notANumberMessage: '', wrongNumberMessage: '', tooLowMessage: '', tooHighMessage:'' } );

}

document.getElementById('startqtc').focus();

</script>

<?
} /* function qtctrainer */

function qtcdialog () {

echo '<h2>'.l('qtctraining').'</h2>';		
echo '<p>'.l('qtctraining1').'</p>';		
echo '<p>'.l('qtctraining2').'</p>';		
echo '<p>'.l('qtctraining3').'</p>';		

if (!$_SESSION['qtc']['set']) {
    $_SESSION['callsigns']['cw_speed'] = $_SESSION['cw_speed'];
    $_SESSION['callsigns']['abbrev'] = 0;
    $_SESSION['callsigns']['set'] = true;
}


?>

<form action="/qtc" method="POST">
<table>
<input type="hidden" name="p" value="qtc">
<tr>
<td><? echo l('speed')?>: </td>
<td>
<select name="speed" size="1">
<?
	for ($i = 5; $i < 101; $i++) {
		if ($i == $_SESSION['qtc']['cw_speed']) {
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
<td><? echo l('abbreviatednumbers')?>: </td>
<td>
<select name="abbrev" size="1">
    <option value="0" <? if ($_SESSION['qtc']['abbrev'] == 0) { echo "selected"; } ?>>-</option>
    <option value="1" <? if ($_SESSION['qtc']['abbrev'] == 1) { echo "selected"; } ?>>1, 9, 0</option>
    <option value="2" <? if ($_SESSION['qtc']['abbrev'] == 2) { echo "selected"; } ?>>0 - 9</option>
</select> 
</td>
</tr>
<?
if (0) {
?>
<tr>
<td><? echo l('abbreviatedtimes')?>: </td>
<td>
<select name="abbrev" size="1">
	<option value="0">-</option>
	<option value="1"><?=l('all');?></option>
	<option value="2"><?=l('partly');?></option>
</select> 
</td>
</tr>
<?
}
?>
<tr>
<td colspan="3">
<input type="submit" id="startbutton" value="<? echo l('start',1); ?>">
</td>
</tr>
</table>	
</form>

<script type="text/javascript">
	document.getElementById('startbutton').focus();
</script>

<?
}
?>
