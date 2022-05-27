
<h1><? echo l('changecwsettings') ?></h1>


<? 
if (!$_SESSION['uid']) {
echo "Sorry, you must be logged in to use this function."; 
return 0; 
}

	if ($_POST['submitted']) {

		if (isint($_POST['speed']) && isint($_POST['eff'])
		&& isint($_POST['tone']) && 
		($_POST['speed'] > 1) && 
		($_POST['eff'] >= 1) && 
		is_numeric($_POST['ews']) && 
		($_POST['tone'] > 100) &&
		(in_array($_POST['tonetype'], array("1", "0")))
		) {

            if ($_POST['ply'] == PL_HTML5) {
                $player = PL_HTML5;
            }
            else {
                $player = PL_JSCWLIB;
            }

			if ($_POST['lock'] == 1) {
				$lock = 1;
			}
			else {
				$lock = 0;
			}

			if ($_POST['vvv'] == 1) {
				$vvv = $_POST['vvv'];
			}
			else {
				$vvv = 0;
			}
		
			if ($_POST['eff'] > $_POST['speed']) {
				$eff =  $_POST['speed'];
			}	
			else {
				$eff = $_POST['eff'];
			}
	
			if ($_POST['ews'] > 40 or $_POST['ews'] < 0) {
				$ews = 0;
			}	
			else {
				$ews = $_POST['ews'];
			}
	
			/* rand:
						0 -> read group length from 'randlength',
							fixed value. Save this value in
							koch_randomlength
						1 -> random 2-7
							save 0 in koch_randomlength
			*/
			if (in_array($_POST['rand'], array("0", "1"))) {
				if ($_POST['rand']) {
					$rand = 0;			/* 0 means randomized lengths */
				}
				else {
					if (isint($_POST['randlength']) and
					$_POST['randlength'] < 10) {
						$rand = $_POST['randlength'];
					}
					else {
						$rand = $_SESSION['koch_randomlength'];
					}
				}
			}
			else {
				$rand = $_SESSION['koch_randomlength'];
			}

			if (isint($_POST['delay_start'])) {
					$ds = $_POST['delay_start'];
					if (!($ds >= 0 && $ds <= 20)) {
							$ds = 0;
					}
			}
			else {
					$ds = 0;
			}
	
			# new customcharacters string

			$customchars = "";
			foreach ($_POST as $p => $x){
				if (mb_substr($p, 0, 4) == "char") {
					$char = mb_substr($p, 4, 1);
					if ($char == "q") {			# exception for "
						$customchars .= '"';
					}
					else if ($char == "_") {			# exception for .
						$customchars .= '.';
					}
					else if ($char == "\\") {			# exc for '
						$customchars .= "'";
					}
					else {
						$customchars .= $char;
					}
				}				
			}

			$esccustomchars = esc($customchars);
			
			$upd = mysqli_query($db,"update lcwo_users set
			`cw_speed`=$_POST[speed], `cw_eff`=$eff, 
			`cw_ews`=$ews,
			`cw_tone`=$_POST[tone], `player`=$player,
			`vvv`='$vvv', `lockspeeds`='$lock',
			`cw_tone_random`='$_POST[tonetype]',
			koch_randomlength='$rand',
			customcharacters='$esccustomchars',
			delay_start='$ds' where id=$_SESSION[uid]");

			if (!$upd) {
				echo "<p>Failed to update database: ".mysqli_error($db)."</p>";
			}
			else {
				echo "<p><strong>".l('valuesaccepted')."</strong></p>\n";
				unset($_SESSION['cw_speed']);
				unset($_SESSION['cw_eff']);
				unset($_SESSION['cw_ews']);
				unset($_SESSION['cw_tone']);
				unset($_SESSION['player']);
				unset($_SESSION['vvv']);
				$_SESSION['cw_speed'] = $_POST['speed'];
				$_SESSION['cw_eff'] = $eff;
				$_SESSION['cw_ews'] = $ews;
				$_SESSION['cw_tone'] = $_POST['tone'];
				$_SESSION['cw_tone_random'] = $_POST['tonetype'];
				$_SESSION['player'] = $player;
				$_SESSION['vvv'] = $vvv;
				$_SESSION['lockspeeds'] = $lock;
				$_SESSION['koch_randomlength'] = $rand;
				$_SESSION['customcharacters'] = $customchars;
				$_SESSION['delay_start'] = $ds;
			}
		}
		else {
			echo "<p><strong>Warning:</strong> Values invalid. Not numeric or out of range.</p>";
		}
	}

?>

<script type="text/javascript">

var changed = false;

function value_changed () {
    changed = true;
    document.getElementById('changed_hint').style.display = "block";
}

function lockspeed(spd) {
	var eff = document.getElementById('eff');
	//var speed = document.getElementById('speed').value;
	eff.value = spd;
    value_changed();
}

function locktoggle() {
	var eff = document.getElementById('eff');
	var spd = document.getElementById('speed');
	var ico = document.getElementById('lockico');
	var hiddenlock = document.getElementById('hiddenlock');
	if (locked) {
			locked = false;
			hiddenlock.value = '0';
			eff.style.background='white';
			ico.src="pics/unlock.png";
	}
	else {
			locked = true;
			hiddenlock.value = '1';
			eff.style.background='#cccccc';
			eff.value=spd.value;
			ico.src="pics/lock.png";
	}
}

var locked = <? echo ($_SESSION['lockspeeds']==1 ? "true" : "false") ?>;

</script>

<table>
<tr><td width=55% valign="top">

<form action="/cwsettings" method="POST">
	<table>
	
	<tr>
	<td>
		<? echo l('charspeedlong')?> (<? echo l('wpm') ?>):
	</td>
	<td width="10%">
		<input id="speed" onChange="if(locked) {lockspeed(this.value);}; value_changed();" 
			name="speed" type="text" value="<? echo $_SESSION['cw_speed']; ?>"
		   	size="3">
	</td>
	<td rowspan="2" valign="absmiddle"><a href="#" onClick="locktoggle();">
	<img border="0" id="lockico" src="/pics/unlock.png"></a>
	&nbsp; 
	</td>
	</tr>
	<tr class="hl">
	<td><?echo l('effspeedlong')?>  (<? echo l('wpm') ?>):</td>
	<td><input onChange="value_changed();" id="eff" disable="disabled" onFocus="if(locked){this.blur();}" onClick="if(locked) { locktoggle(); }" style="background:#ffffff;" name="eff" type="text" value="<? echo $_SESSION['cw_eff']; ?>" size=3></td>
	</tr>
	<tr>
	<td>
		<? echo l('ewslong')?>:
	</td>
	<td width="10%"> <input onChange="value_changed();" id="ews" name="ews" type="text" value="<? echo $_SESSION['cw_ews']; ?>" size="2">
	</td>
	</tr>
	<tr>
	<td valign="top"><? echo l('tone') ?> (Hz):</td>
	<td colspan="2">
	<input type="radio" onChange="value_changed();" name="tonetype" value="0" <? if
(!$_SESSION['cw_tone_random']) { echo 'checked'; }?>>
	<input name="tone" onChange="value_changed();" type="text" value="<? echo $_SESSION['cw_tone']; ?>" size=3> <br>
	<input type="radio" onChange="value_changed();"  name="tonetype" value="1"
	<? if ($_SESSION['cw_tone_random']) { echo 'checked'; }?> > <? echo l('random') ?> (500-900Hz)
	</td>
	</tr>
	<tr class="hl">
	<td valign="top"><? echo l('cwplayer') ?>:</td>
	<td colspan="2">
    <input type="radio" onChange="value_changed();" name="ply" value="1" <? if ($_SESSION['player']==PL_JSCWLIB) { echo 'checked'; }?>> <a style="font-weight:bold;" href="https://fkurz.net/ham/jscwlib.html"><?=l('jscwlib');?></a><br>
	<input type="radio" onChange="value_changed();" name="ply" value="3" <? if ($_SESSION['player']==PL_HTML5) { echo 'checked'; }?>> <? echo l('html5player') ?><br>
	</td>
	</tr>
	<tr>
	<td><? echo l('cwprefix') ?>:</td>
	<td colspan="2">
	<input type="checkbox" onChange="value_changed();" name="vvv" value="1" <? if
	($_SESSION['vvv']==1) { echo 'checked'; }?>> "VVV = / AR"
	</td>
	</tr>
	<tr class="hl">
	<td valign="top"><? echo l('startdelay') ?>:</td>
	<td colspan="2">
	<input type="text" onChange="value_changed();" value="<?=$_SESSION['delay_start']?>" size=2 name="delay_start"> <?=l('seconds')?>
	</td>
	</tr>
	<tr>
	<td valign="top"><? echo l('grouplength') ?>:</td>
	<td colspan="2">
	<input type="radio" onChange="value_changed();" name="rand" value="0" <? if
	($_SESSION['koch_randomlength'] > 0) { echo 'checked'; }?>>
	<input type="text" value="<? echo
($_SESSION['koch_randomlength'] > 0) ? 
$_SESSION['koch_randomlength'] : 5 ?>" size=3 name="randlength">
	(<? echo l('fixed') ?>)<br>
	<input type="radio" onChange="value_changed();" name="rand" value="1" 
	<? if ($_SESSION['koch_randomlength']==0) { echo 'checked';
}?>> 2-7 (<? echo l('random') ?>)
	</tr>
	</table>
	<input type="hidden" name="submitted" value="1">
	<input type="hidden" id="hiddenlock" name="lock" value="0">

	<br>
	<br>
	<br>

    <table>
    <tr>
      <td>    
        <input type="submit" value=" <? echo l('submit',1) ?> ">	
      </td>
      <td>
      &nbsp; <span style="display:none;color:red;" id="changed_hint">&vltri; <?=l('changedhint');?></span>
      </td>
    </tr>
    </table>

</td>
<td width="45%" valign="top">

<p><?=l('customcharexplanation')?></p>
<script type="text/javascript">
var Aletters = new Array(
<? 
$tmparray = array();
foreach ($kochchar as $k) {
	if (ctype_alpha($k)) {
		array_push($tmparray, "\"$k\"");
	}
}
echo join(",", $tmparray);
?>);

var Akoch = new Array(
<? 
$tmparray = array();
foreach ($kochchar as $k) {
	array_push($tmparray, "\"$k\"");
}
echo join(",", $tmparray);
?>);

var Aextra = new Array(
<? 
$tmparray = array();
foreach ($extrachar as $k) {
	array_push($tmparray, "\"$k\"");
}
echo join(",", $tmparray);
?>);

var Acyrillic = new Array(
<? 
$tmparray = array();
foreach ($cyrillicchar as $k) {
	$k = mb_strtoupper($k);
	array_push($tmparray, "\"$k\"");
}
echo join(",", $tmparray);
?>);
var Aarabic = new Array(
<? 
$tmparray = array();
foreach ($arabicchar as $k) {
	array_push($tmparray, "\"$k\"");
}
echo join(",", $tmparray);
?>);



var Anumbers = new Array(0,1,2,3,4,5,6,7,8,9);


function togglechecks (larray) {
	var i;
	var newvalue = true;
	var tmp;

    value_changed();

	/* check if members of the array are checked */
	
	for	(i=0; i < larray.length; i++) {
		tmp = document.getElementById('char'+larray[i]);
		if (tmp.checked == true) {
			newvalue = false;
			break;
		}
	}

	/* if something was checked before, set all letters to
	* "checked", otherwise set all to unchecked. */
	
	for	(i=0; i < larray.length; i++) {
		tmp = document.getElementById('char'+larray[i]);
		tmp.checked = newvalue;	
	}
}

</script>


<table width="100%" cellpadding="0">
<?
$nr=0;
foreach ($kochchar as $k) {
	if ($nr == 0) {
		echo "<tr>";
	}
	echo '<td class="hl"><input onChange="value_changed();" id="char'.$k.'" type="checkbox" name="char'.$k.'" value="1"';
	if (!(strpos($_SESSION['customcharacters'], $k) === FALSE)) {
		echo " checked ";
	}
	echo '>'.mb_strtoupper($k)."</td>\n";
	if ($nr == 8) {
		echo "</tr>\n";
		$nr=-1;
	}
	$nr++;
}
?>
</tr>
<?
$nr=0;
foreach ($extrachar as $k) {
	if ($k == "quot") { $k2 = '"';} 
	else {$k2 = $k; }
	
	if ($nr == 0) {
		echo "<tr>";
	}
	echo '<td class="hl"><input onChange="value_changed();" id="char'.$k.'" type="checkbox" name="char'.$k.'" value="1"';
	
	if (!(strpos($_SESSION['customcharacters'], $k2) === FALSE)) {
		echo " checked ";
	}
	
	echo '> '.strtoupper($k2).'</td>'."\n";
	if ($nr == 8) {
		echo "</tr>";
		$nr=-1;
	}
	$nr++;
}
?>
</tr>
<?
$nr=0;
foreach ($cyrillicchar as $k) {
	$k = mb_strtoupper($k);
	if ($nr == 0) {
		echo "<tr>";
	}
	echo '<td class="hl"><input onChange="value_changed();" id="char'.$k.'" type="checkbox" name="char'.$k.'" value="1"';
	
	if (!(strpos($_SESSION['customcharacters'], $k) === FALSE)) {
		echo " checked ";
	}
	
	echo '> '.$k.'</td>'."\n";
	if ($nr == 8) {
		echo "</tr>";
		$nr=-1;
	}
	$nr++;
}
?>
</tr>
<?
$nr=0;
foreach ($arabicchar as $k) {
	if ($nr == 0) {
		echo "<tr>";
	}
	echo '<td class="hl"><input onChange="value_changed();" id="char'.$k.'" type="checkbox" name="char'.$k.'" value="1"';
	
	if (!(strpos($_SESSION['customcharacters'], $k) === FALSE)) {
		echo " checked ";
	}
	echo '> '.$k.'</td>'."\n";
	if ($nr == 8) {
		echo "</tr>";
		$nr=-1;
	}
	$nr++;
}
?>
</tr>



</table>

<input type="button" name="letters" value="<?=l('letters',1)?>" onClick="togglechecks(Aletters);">
<input type="button" name="numbers" value="<?=l('figures',1)?>" onClick="togglechecks(Anumbers);">
<input type="button" name="kochchars" value="<?=l('kochcharacters',1)?>" onClick="togglechecks(Akoch);">
<input type="button" name="extrachars" value="<?=l('extracharacters',1)?>" onClick="togglechecks(Aextra);">
<input type="button" name="cyrillicchars" value="<?=l('cyrillic',1)?>" onClick="togglechecks(Acyrillic);">
<input type="button" name="arabicchars" value="<?=l('arabic',1)?>" onClick="togglechecks(Aarabic);">


</td></tr>
</table>

</form>

<h2><? echo l('cwsample'); ?></h2>

<? player("sound sample vvv", $_SESSION['player'],
$_SESSION['cw_speed'], $_SESSION['cw_eff'],0, 0, 1,0); ?>

<br><br>

<script type="text/javascript">
/* Make sure the form loads in the locked status if the session variable for 
* locking is set */
if (locked) {
	locked = false;
	locktoggle();
}
</script>
