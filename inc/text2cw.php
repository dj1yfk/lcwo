<h1><? echo l('text2cw') ?></h1>

<?
if (!$_SESSION['text2cw']['init']) {
	init_text2cw();
}

if ($_POST['sent']) {
	text2cw();
}
else {
	entryform();
}



function text2cw () {

	update_text2cw();

	$text = stripslashes($_POST['text']);
    $text = str_replace('\\', '', $text);
    $hide = $_POST['cbhide'] ? true : false;

	if ($text == "") {
		$text = "LCWO";
	}
?>
<table width="100%">
<tr width="100%">
<td width="46%" valign="top">
<?	
	echo "<h2>".l('yourtext')."</h2>";

	echo "<p id='text' class=\"tborder\">".htmlspecialchars($text)."</p>";

    if ($hide) {
?>
    <a id="reveal" href="#" onclick="document.getElementById('text').style.display = 'block';document.getElementById('reveal').style.display='none';"><?=l('revealtext');?></a>
<?
    }

	echo "<h2>".l('cwplayeranddownload')."</h2>";

	$s = intval($_POST['speed']);
	$e = intval($_POST['eff']);
	$f = intval($_POST['freq']);
    $text = preg_replace('/[\n\r]/', ' ', $text);
    if ($_SESSION['player'] == PL_JSCWLIB) {
        $text = preg_replace('/"/', '\"', $text);
    }
	$t = $text;

	/* shitty global */
	$_SESSION['cw_tone'] = intval($_POST['freq']);

	// default: JSCWLIB 1
	player($t, isset($_SESSION['player']) ?  $_SESSION['player'] : 1, $s, $e, 0, 1, 0,1); 
?>

    <script>
    if (typeof(pa) != 'undefined') {
        pa[1].enablePS(false);
        pa[1].setStartDelay(0.1);
    }

<?
    if ($hide) {
?>
    document.getElementById('text').style.display = "none";
<?
    }
?>

    </script>

<?
    # separate download link for JSCWLIB player because not everyone
    # understands the download symbol
    if (!isset($_SESSION['player']) or ($_SESSION['player'] == 1)) {
?>
    <div id="downloadlink"></div>
    <script>
        var dl = document.getElementById("downloadlink");
        dl.innerHTML = "<a href='" + pa[1].btn_down.href + "'>Download MP3</a>";
    </script>
<?
    }

?>

</td>
<td width="8%" valign="top">
&nbsp;
</td>
<td width="46%" valign="top">
<h2><?=l('includecwplayeronyoursite')?></h2>

<p><?=l('includeplayertext');?></p>

<textarea cols=70 rows=10>
<script src="https://lcwo.net/js/jscwlib.js"></script>
<div id="player"></div>  
<script>
    var m = new jscw();
    m.setWpm(<?=$s;?>);
    m.setEff(<?=$e;?>);
    m.setFreq(<?=$f;?>);
<?
    if ($hide) {
?>
    m.setTextB64("<?=base64_encode($t);?>");
<?
    }
    else {
?>
    m.setText("<?=$t;?>");
<?
    }
?>
    m.renderPlayer('player', m);
</script>
</textarea>
<br>
<?
    $url = BASEURL."/ext/player?z=";
    $code = urlencode(base64_encode($s."~~".$e."~~".$f."~~".$t));
    echo "<a href='".$url.$code."'>".l('playerdirectlink')."</a>";
?>
</td>
</tr>
</table>
<?
	echo '<br><p><a href="/text2cw">'.l('backtoentryform').'</a></p>';
return 0;
}

function entryform () {

?>
<p> <? echo l('text2cwdesc1'); ?> </p>
<p> <? echo l('text2cwdesc2'); ?> </p>
<p> <? echo l('text2cwdesc3'); ?> </p>



<form action="/text2cw" method="POST">

<table>
<tr>
<td><? echo l('charspeedlong') ?>:</td><td> 
<select id="speed" name="speed" size="1" onchange="change_spd();">
<?
	$preset1 = $_SESSION['text2cw']['cw_speed']; 
	$preset2 = $_SESSION['text2cw']['cw_eff'];
	$preset3 = $_SESSION['text2cw']['cw_tone'];
	
	for ($i = 5; $i < 151; $i++) {
		if ($i == $preset1) {
			echo "<option selected>$i</option>";
		}
		else {
			echo "<option>$i</option>";
		}
	}
?>
</select> <? echo l('wpm'); ?>
</td>
<td rowspan="2"><a href="#" onClick="locktoggle();"><img border="0" id="lockico" src="/pics/unlock.png"></a></td>
</tr>
<tr>
<td><? echo l('effspeedlong') ?>:</td><td> 
<select id="eff" name="eff" size="1">
<?
	for ($i = 5; $i < 151; $i++) {
		if ($i == $preset2) {
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
<td><? echo l('tone') ?>:</td><td> 
<select name="freq" size="1">
<?
	for ($i = 250; $i < 1000; $i+=10) {
		if ($i == $preset3) {
			echo "<option selected>$i</option>";
		}
		else {
			echo "<option>$i</option>";
		}
	}

?>
</select> Hz
</td>
</tr>
</table>

<textarea name="text" id="txt" cols="80" rows="10"></textarea>
<br>
<input type="hidden" name="sent" value="1">
<input type="submit" value=" <? echo l('convert',1) ?> ">
&nbsp;
Open text: <input type="file" id="t2c_file" onChange="javascript:load_text(this);return false">
&nbsp;
<input id="hide" type="checkbox" onChange="javascript:toggle_hide();" name="cbhide" value="1"<? if ($_SESSION['text2cw']['hide'] == true) { echo " checked "; } ?>> <?=l('hidetext');?>
</form>
<script>
  var hide = false;
  function toggle_hide() {
      hide = document.getElementById('hide').checked;
      if (hide) {
          document.getElementById('txt').style.display = "none";
      }
      else {
          document.getElementById('txt').style.display = "block";
      }
  }
  toggle_hide();


    function load_text(f) {
        if (!f.files[0]) {
            return;
        }

        var reader = new FileReader();
        reader.onload = function(f) {
            var t = f.target.result;

            if (t.length > 8000) {
                t = t.substring(0, 8000);
                alert('File truncated to 8000 characters (maximum)');
            }

            document.getElementById('txt').value = t;
        };
        reader.readAsText(f.files[0]);
    }
</script>

<script type="text/javascript">
function lockspeed(spd) {
	var eff = document.getElementById('eff');
	eff.value = spd;
}

function locktoggle() {
	var eff = document.getElementById('eff');
	var spd = document.getElementById('speed');
	var ico = document.getElementById('lockico');
	if (locked) {
			locked = false;
//			eff.style.background='white';
			eff.disabled = false;
			ico.src="pics/unlock.png";
	}
	else {
			locked = true;
			// eff.style.background = '#cccccc';
			eff.disabled = false;
            eff.selectedIndex = spd.selectedIndex;
			eff.disabled = true;
			ico.src="pics/lock.png";
	}
}
var locked = <? echo ($_SESSION['lockspeeds']==1 ? "true" : "false") ?>;


function change_spd () {
    if (locked) {
        var eff = document.getElementById('eff');
        var spd = document.getElementById('speed');
        eff.selectedIndex = spd.selectedIndex;
    }
}

/* Make sure the form loads in the locked status if the session variable for 
* locking is set */
if (locked) {
	locked = false;
	locktoggle();
}
</script>


<?
}	/* function entryform () */

/* set / initialize session vars */
function init_text2cw () {
		if ($_SESSION['uid']) {
			$_SESSION['text2cw']['cw_speed'] = $_SESSION['cw_speed']; 
			$_SESSION['text2cw']['cw_eff']  = $_SESSION['cw_eff'];
			$_SESSION['text2cw']['cw_tone'] = $_SESSION['cw_tone'];
		}
		else {
			$_SESSION['text2cw']['cw_speed'] = 20;
			$_SESSION['text2cw']['cw_eff']  = 20;
			$_SESSION['text2cw']['cw_tone'] = 600;
		}
		$_SESSION['text2cw']['init'] = 1;
}

function update_text2cw () {
		$_SESSION['text2cw']['cw_speed'] = intval($_POST['speed']);
		$_SESSION['text2cw']['cw_eff']  = intval($_POST['eff']);
		$_SESSION['text2cw']['cw_tone'] = intval($_POST['freq']);
		$_SESSION['text2cw']['hide'] = $_POST['cbhide'] ? true : false;
}


?>


