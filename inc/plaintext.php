<h1><? echo l('plaintexttraining') ?></h1>

<? 

if (!$_SESSION['uid']) {
		echo "Sorry, you must be logged in to use this function.";
		return 0;
}

/* Set session variables on first time */

if (!$_SESSION['plain']['set']) {
		$_SESSION['plain']['cw_speed'] = $_SESSION['cw_speed'];
		$_SESSION['plain']['cw_eff'] = $_SESSION['cw_eff'];
		if ($_SESSION['plain']['cw_eff'] == $_SESSION['plain']['cw_speed']) {
				$_SESSION['plain']['lockspeeds'] = 1;
		}
		else {
				$_SESSION['plain']['lockspeeds'] = 0;
		}
		$_SESSION['plain']['simplify'] = 0;
		unset($_SESSION['plain']['collection']);
		$_SESSION['plain']['set'] = TRUE;
}

if ($_POST['sent']) {
	attempt();
}
else if (isset($_POST['submitted'])) {
	checktext();
}
else {
	parameterdialog();
}


function attempt () {
global $db;	
	$_SESSION['plain']['cw_speed'] = intval($_POST['speed']);
	$_SESSION['plain']['cw_eff'] = intval($_POST['eff']);
	$_SESSION['plain']['lockspeed'] = $_POST['lock'];
	$_SESSION['plain']['simplify'] = $_POST['simplify'];
	$_SESSION['plain']['collid'] = intval($_POST['collection']);
		
	echo "<p>".l('plaintextenter')."</p>";

	// fetch random sentence from the database for selected coollection.

	$query = mysqli_query($db,"select count(*), lang from lcwo_plaintext where
			collid='".$_SESSION['plain']['collid']."' group by lang");
	
	$foo = mysqli_fetch_row($query);

	if ($foo[0] == 0) {
			echo "Error. No such collection exists.";
			return;
	}
	
	$foo2 = rand(1, $foo[0]);
	$coll_lang = $foo[1];

	$query = mysqli_query($db,"select text from lcwo_plaintext where
			collid='".$_SESSION['plain']['collid']."' order by nr limit $foo2,
			1");
	if (!$query) {
		echo "ERROR! ".mysqli_error();
	}
	$foo = mysqli_fetch_row($query);

	$slang = $coll_lang;
	if ($_SESSION['plain']['simplify']) {
		$foo[0] = simplify($slang, $foo[0]);
	}
	$foo[0] = stripslashes($foo[0]);
	
	$txtext = $foo[0];
	$txtext2 = rawurlencode($foo[0]);
	
	?>
    <button id="btn_playpause" onClick="playpause(1); if(tmp=getElementById('eform')){tmp.input.focus();} return false;"><?=l('playpause',1);?></button><br><br>
	<form action="/plaintext" method="POST" id="eform">
		<table><tr>
		<td>
			<textarea spellcheck="false" autocapitalize="off" autocorrect="off" autocomplete="off" type="text" cols="30" rows="10" name="input" id="textinput"></textarea>
		</td>
        </tr>
        <tr>
		<td>
			<input type="submit" name="submitted" value=" <? echo l('checkresult',1) ?>">
			<input type="submit" name="cancel" value=" <? echo l('cancel',1) ?>">
	     </td>
		</tr></table>
		<input type="hidden" name="txtext" value="<? echo base64_encode($txtext); ?>">
		<input type="hidden" name="slang" value="<? echo $slang; ?>">
	</form>

    <script>
        document.getElementById('btn_playpause').focus();
    </script>

	<?

    if ($_SESSION['player'] != PL_JSCWLIB) { $txtext = "|S".($_SESSION['delay_start']*1000)." ".$txtext; }

	player($txtext, $_SESSION['player'], $_SESSION['plain']['cw_speed'], $_SESSION['plain']['cw_eff'],0, 1, 0,1);

    


}


function normalizeKana($str)
{
	$ar = array (
		"あ" => "ア", "い" => "イ", "う" => "ウ", "え" => "エ", "お" => "オ",
		"か" => "カ", "き" => "キ", "く" => "ク", "け" => "ケ", "こ" => "コ",
		"さ" => "サ", "し" => "シ", "す" => "ス", "せ" => "セ", "そ" => "ソ",
		"た" => "タ", "ち" => "チ", "つ" => "ツ", "て" => "テ", "と" => "ト",
		"な" => "ナ", "に" => "ニ", "ぬ" => "ヌ", "ね" => "ネ", "の" => "ノ",
		"は" => "ハ", "ひ" => "ヒ", "ふ" => "フ", "へ" => "ヘ", "ほ" => "ホ",
		"ま" => "マ", "み" => "ミ", "む" => "ム", "め" => "メ", "も" => "モ",
		"や" => "ヤ", "ゆ" => "ユ", "よ" => "ヨ",
		"ら" => "ラ", "り" => "リ", "る" => "ル", "れ" => "レ", "ろ" => "ロ",
		"わ" => "ワ", "ゐ" => "ヰ", "ゑ" => "ヱ", "を" => "ヲ",
		"ん" => "ン", "　" => " ",
		"が" => "ガ", "ぎ" => "ギ", "ぐ" => "グ", "げ" => "ゲ", "ご" => "ゴ",
		"ざ" => "ザ", "じ" => "ジ", "ず" => "ズ", "ぜ" => "ゼ", "ぞ" => "ゾ",
		"だ" => "ダ", "ぢ" => "ヂ", "づ" => "ヅ", "で" => "デ", "ど" => "ド",
		"ば" => "バ", "び" => "ビ", "ぶ" => "ブ", "べ" => "ベ", "ぼ" => "ボ",
		"ぱ" => "パ", "ぴ" => "ピ", "ぷ" => "プ", "ぺ" => "ペ", "ぽ" => "ポ",
		"っ" => "ツ",
		"ぁ" => "ア", "ぃ" => "イ", "ぅ" => "ウ", "ぇ" => "エ", "ぉ" => "オ",
		"ゃ" => "ヤ", "ゅ" => "ユ", "ょ" => "ヨ",
		"ッ" => "ツ",
		"ァ" => "ア", "ィ" => "イ", "ゥ" => "ウ", "ェ" => "エ", "ォ" => "オ",
		"ャ" => "ヤ", "ュ" => "ユ", "ョ" => "ヨ",
	);
	return strtr($str, $ar);
}


function checktext () {
	global $db;

	$input = stripslashes($_POST['input']);
	$txtext = base64_decode(stripslashes($_POST['txtext']));
	$slang = $_POST['slang'];
	
	echo "<table><tr><th>".l('sent')."</th><td>".$txtext."</td></tr>";
	echo "<tr><th>".l('received')."</th><td>".$input."</td></tr></table>";

	if($slang == "ja") {
		$input = normalizeKana($input);
		$txtext = normalizeKana($txtext);
	}

	$txtext = simplify($slang, mb_strtolower($txtext));
	$input = simplify($slang, mb_strtolower($input));

	// special case for Greek: upper case letter Σ is σ within a string but
	// ς at the end. mb_strtolower does not make this distinction.
	// Therefore replace all ς with σ before the comparison.
	$txtext = str_replace("ς", "σ", $txtext);
	$input = str_replace("ς", "σ", $input);

	$input = preg_replace("/[ ]+/", ' ', $input); 
	$input = preg_replace("/^ /", '', $input); 
	$input = preg_replace("/ $/", '', $input); 

    $lserrors = levenshtein(mb_substr($txtext,0,255), mb_substr($input,0,255));
    $accuracy = (intval(1000-1000*$lserrors/mb_strlen($txtext))/10);

	if ($accuracy < 0) {
		$accuracy= 0;
	}

	echo "<p>".l('accuracy').": $lserrors ".l('errors')." / ".mb_strlen($_POST['input'])." ".l('characters')." = ".$accuracy."%</p>";

	if ($_SESSION['plain']['cw_eff'] > $_SESSION['plain']['cw_speed']) {
			$_SESSION['plain']['cw_eff'] = $_SESSION['plain']['cw_speed'];
	}

	$i = mysqli_query($db,"insert into lcwo_plaintextresults set
			`uid`='".$_SESSION['uid']."', `speed`='".$_SESSION['plain']['cw_speed']."',
			`eff`='".$_SESSION['plain']['cw_eff']."', `accuracy`='$accuracy', `time`=NULL");

	if (!$i) {
		echo mysqli_error();
	}
			
	echo '<p><a href="/plaintext" id="newattempt">'.l('newsentence').'</a></p>';
?>

<script type="text/javascript">
    document.getElementById('newattempt').focus();
</script>


<?
}

function parameterdialog () {
global $db;
?>
<script type="text/javascript">
function lockspeed(spd) {
	var eff = document.getElementById('eff');
	eff.value = spd;
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
			ico.src="/pics/unlock.png";
	}
	else {
			locked = true;
			hiddenlock.value = '1';
			eff.style.background='#cccccc';
			eff.value=spd.value;
			ico.src="/pics/lock.png";
	}
}

var locked = <? echo ($_SESSION['plain']['lockspeeds']==1 ? "true" : "false") ?>;
</script>

<p><?echo l('plaintextexplain') ?></p>

<form action="/plaintext" method="POST">
	<table>
	<tr>
	<td>
		<? echo l('charspeedlong')?> (<? echo l('wpm') ?>):
		&nbsp;&nbsp;&nbsp;
	</td>
	<td>
		<input id="speed" onChange="if(locked) {lockspeed(this.value);}" 
			name="speed" type="text" value="<? echo $_SESSION['plain']['cw_speed']; ?>"
		   	size="3">
	</td>
	<td rowspan="2"><a href="#" onClick="locktoggle();">
	<img align="left" border="0" id="lockico" src="/pics/unlock.png"></a>
	&nbsp; 
	</td>
	</tr>
	<tr>
	<td><?echo l('effspeedlong')?>  (<? echo l('wpm') ?>):</td>
	<td><input id="eff" disable="disabled" onFocus="if(locked){this.blur();}"
onClick="if(locked) { locktoggle(); }" style="background:#ffffff;" name="eff"
type="text" value="<? echo $_SESSION['plain']['cw_eff']; ?>" size=3></td>
	</tr>
	<tr>
		<td>
		<? echo l('simplify'); ?>
		</td>
		<td>
			<input type="checkbox" name="simplify" 
			<? if ($_SESSION['plain']['simplify']) {
					echo ' checked ';
			}?>>
		</td>
	</tr>
	<tr>
	<td>
	<? echo l('language').' / '.l('collection'); ?>:
	</td>
	<td colspan="2">
	<select name="collection" size="1">
	<?
	    $preflang = $_SESSION['lang'];

		$coll = getavailableplaintextcollections();
	
		foreach ($coll as $w) {
            $ctmp = explode(' ', $w);
            $collid = intval(array_pop($ctmp));
            array_pop($ctmp);
			$w = join(" ", $ctmp);
			
			// No preferred collection set yet? Use first with home lang
			if (!isset($_SESSION['plain']['collid'])) {
				if (mb_substr($w,0,2) == $preflang) {
					echo "<option value=\"$collid\" selected>$w</option>";
					$preflang ="void";
				}
				else {
					echo "<option value=\"$collid\">$w</option>";
				}
			}
			else {
				if ($collid == $_SESSION['plain']['collid']) {
					echo "<option value=\"$collid\" selected>$w</option>";
				}
				else {
					echo "<option value=\"$collid\">$w</option>";
				}
			}

			
		}
?>
</select>

	</td>
	</tr>
	</table>
	<input type="hidden" id="hiddenlock" name="lock" value="0">
	<input type="hidden" name="sent" value="1">
	<input type="submit" id="startattempt" value=" <? echo l('start',1); ?> ">
</form>

<script type="text/javascript">
    document.getElementById('startattempt').focus();

/* Make sure the form loads in the locked status if the session variable for 
* locking is set */
if (locked) {
	locked = false;
	locktoggle();
}
	
</script>


<br><br>


<? 
}
echo "<!-- ".time()." -->";






function getavailableplaintextcollections () {
	global $db;

	$query = mysqli_query($db,"select description, lang, collid, 
					count(description) from lcwo_plaintext
					group by description order by lang ");

	if (!$query) {
		echo "Error: ".mysqli_error();
		return;
	}

	$wordlangs = array();
	
	while ($tmp = mysqli_fetch_row($query)) {
		array_push($wordlangs, ($tmp[1]." - ".$tmp[0]." (".$tmp[3].") - $tmp[2]"));
	}

	return $wordlangs;
}

?>
