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

	if (!$text) {
		$text = "LCWO";
	}
?>
<table width="100%">
<tr width="100%">
<td width="46%" valign="top">
<?	
	echo "<h2>".l('yourtext')."</h2>";
	echo "<p class=\"tborder\">".htmlspecialchars($text)."</p>";
    echo "<!-- $text -->";

	echo "<h2>".l('cwplayeranddownload')."</h2>";

	$s = intval($_POST['speed']);
	$e = intval($_POST['eff']);
	$f = intval($_POST['freq']);
    $text = preg_replace('/[\n\r]/', ' ', $text);
    $text = preg_replace('/"/', '\"', $text);
	$t = $text;

	/* shitty global */
	$_SESSION['cw_tone'] = intval($_POST['freq']);

	// default: HTML5
	player($t, isset($_SESSION['player']) ?  $_SESSION['player'] : 3, $s, $e, 0, 1, 0,1); 
?>

    <script>
    if (pa[1]) {
        pa[1].enablePS(false);
        pa[1].setStartDelay(0.1);
    }
    </script>

</td>
<td width="8%" valign="top">
&nbsp;
</td>
<td width="46%" valign="top">
<h2><?=l('includecwplayeronyoursite')?></h2>

<p><?=l('includeplayertext');?></p>

<form>
	<?=l('flashplayer');?>:<br>
	<textarea cols="40" rows="5"><?
		include('inc/text2cw-flash.php');
		echo $flashcode;
	?></textarea><br><br>
	<?=l('html5player');?>:<br>
	<textarea cols="40" rows="5"><?
		include('inc/text2cw-html5.php');
		echo $html5code;
		?></textarea><br><br><?
		$url = BASEURL."/ext/player?z=";
		$code = urlencode(base64_encode($s."~~".$e."~~".$f."~~".$t));
		echo l('playerdirectlink')."<br><a href='".$url.$code."'>".$url.$code."</a>";
?>
</form>

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

<table width="90%">
<tr>
<td><? echo l('charspeedlong') ?>: 
<select name="speed" size="1">
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
<td><? echo l('effspeedlong') ?>: 
<select name="eff" size="1">
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
<td><? echo l('tone') ?>: 
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
<tr>
<td width="100%" colspan="3">
<textarea name="text" id="txt" cols="80" rows="10"></textarea>
</td>
</tr>
</table>
<input type="hidden" name="sent" value="1">
<input type="submit" value=" <? echo l('convert',1) ?> ">
&nbsp;
Open text: <input type="file" id="t2c_file" onChange="javascript:load_text(this);return false">
</form>
<script>
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
}


?>



<div class="vcsid">$Id: text2cw.php 119 2011-01-26 17:19:39Z dj1yfk $</div>

