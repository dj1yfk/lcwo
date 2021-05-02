<?
	$uid = $_SESSION['uid'];
	if (!$uid) {
		echo "<b>Please log in.</b>";
		return;
	}

    if (!$_SESSION['mm']['charset']) {
        $_SESSION['mm']['charset'] = 0;
    }

    $charset = Array();
    $charsetname = Array("Koch", l('customchars'), "CWops", "ABC", "Swedish", "Danish", "Hiragana", "Katakana");
    
    $cnr = 0;
    $charset[$cnr++] = $kochchar;

    $charset[$cnr++] = Array();
    # FIXME limited to 50 character
    # FIXME allow "
    for ($i = 0; $i < mb_strlen($_SESSION['customcharacters']); $i++) {
        if ($i == 49) {
            break;
        }
        if (mb_substr($_SESSION['customcharacters'], $i, 1) == '"') {
            continue;
        }
        array_push($charset[1], mb_substr($_SESSION['customcharacters'], $i, 1));
    }

    # CWops CW Academy
    $charset[$cnr++] = Array("T", "E", "A", "N", "O", "I", "S", "1", "4", "R", "H", "D", "L", "2", "5", "U",
        "C", "M", "W", "3", "6", "?", "F", "Y", "P", "G", "7", "9", "/", "B", "V", "K",
        "J", "8", "0", "=", "X", "Q", "Z", /* "&lt;BK&gt;", */ "-", /* "&lt;SK&gt;" */);
    
    $charset[$cnr++] = Array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");

    $charset[$cnr++] = Array("=", "+", "N", "L", "O", "E", "I", "X", "V", "T", "/", "?", "A", "Z", "H", "Ö", ",", "R", "D", "F", "Y", "-", "Ä", "B", "P", "S", "U", "Q", "W", "K", "Å", "M", "7", "4", "9", "5", "C", "G", "J", "8", "1", "3", "6","2", "0", "@"); 

    $charset[$cnr++] = Array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "Æ","Ø","Å");

    if ($_SESSION['player'] == PL_JSCWLIB) {
        $charset[$cnr++] = Array("い","ろ","は","に","ほ","へ","と","ち","り","ぬ","る","を","わ","か","よ","た","れ","そ","つ","ね","な","ら","む","う","ゐ","の","お","く","や","ま","け","ふ","こ","え","て","あ","さ","き","ゆ","め","み","し","ゑ","ひ","も","せ","す","ん");

        $charset[$cnr++] = Array("イ","ロ","ハ","ニ","ホ","ヘ","ト","チ","リ","ヌ","ル","ヲ","ワ","カ","ヨ","タ","レ","ソ","ツ","ネ","ナ","ラ","ム","ウ","ヰ","ノ","オ","ク","ヤ","マ","ケ","フ","コ","エ","テ","ア","サ","キ","ユ","メ","ミ","シ","ヱ","ヒ","モ","セ","ス","ン");
    }

    if (array_key_exists('charset', $_POST) and isint($_POST['charset']+0) and $_POST['charset'] <= count($charset)) {
        $_SESSION['mm']['charset'] = $_POST['charset']+0;
    }
?>


<h1>Morse Machine (<?=l('lesson')." <span id=\"lessondisplay\">".$_SESSION['koch_lesson']."</span>)";?> &mdash; Beta Version</h1>
<p><?=l('idea');?>: Ward, K9OX (<a href="http://c2.com/morse/">A Fully Automatic Morse
Code Teaching Machine</a>).</p>

<form action="/morsemachine" method="POST">
<table>
<tr>
<td>
Character set: &nbsp;
</td>
<td>
<select onChange="this.form.submit();" name="charset" size="1">
<?
    $i = 0;
    foreach ($charset as $c) {
        $selected = ($_SESSION['mm']['charset'] == $i) ? " selected " : "";
        # if there are more than 30 characters, abbreviate them with "..." in
        # the middle to avoid a very wide box which can cause layout problems
        # on screens with limited horizontal size
        if (count($c) > 32) {
            echo "<option value='$i' $selected>".join(', ', array_slice($c, 0, 15))." ... ".join(', ', array_slice($c, count($c) - 15, 15))." (".$charsetname[$i].")</option>\n";
        }
        else {
            echo "<option value='$i' $selected>".join(', ', $c)." (".$charsetname[$i].")</option>\n";
        }
        $i++;
    }

    # save typing work
    $cs = $charset[$_SESSION['mm']['charset']];

?>
</select>
</td></tr>
</table>
</form>





<form onsubmit="return false;">
<?=l('lesson');?>: <input type="submit" onclick="lessonchange(-1);" value="-">
<input type="submit" onclick="lessonchange(1);" value="+">
&nbsp;&nbsp;
<?=l('speed');?>: <input type="submit" onclick="speedchange(-1);" value="-">
<input type="submit" onclick="speedchange(1);" value="+">
&nbsp;&nbsp;
<input type="submit" onclick="reset_errors();" value="<?=l("reset",1)?>">
&nbsp;&nbsp;
<input onclick="buzzer_active = this.checked;" id="buzz" type="checkbox" value="1" checked> Buzzer on errors</input>
</form>
<? echo l('curspeed')." (".l('wpm').")";?>: <span id="speed"></span> &mdash; 
<? echo l('effspeedlong')." (".l('wpm').")";?>: <span id="effspeed"></span> &mdash; 
<?=l('totalchars');?>: <span id="charcount">0</span> &mdash; <span id="response"></span>


<table>
<tr>
<?
	$count = -2;
	foreach ($cs as $k) {
		$count++;
		if ($count < $_SESSION['koch_lesson']) {
				$height1 = 0;
		}
		else {
				$height1 = 101;
		}

		$height2 = 101 - $height1;
		
		echo "<td><img style='display:block;' src=\"/pics/mm-0.png\" id=\"$k-0\" onClick=\"changebar(this, event)\" width=\"10px\"".
		" height=\"$height1"."px\">";
		echo "<img style='display:block;' src=\"/pics/mm-1.png\" id=\"$k-1\" onClick=\"changebar(this, event)\" width=\"10px\"".
		" height=\"".$height2."px\"></td>\n";
		
	}
?>
</tr>
<tr>
<?
	$count = 0;
	foreach ($cs as $k) {
			echo "<td><span id=\"label-$k\">".$k."</span></td>";
			$count++;
	}
?>
</tr>
</table>

<form onsubmit="enteraction();return false;">
<input value="" name="entrybox" id="entrybox" size="1"> &nbsp;&mdash;&nbsp; <?=l('mminstructions');?>
<script>
    document.getElementById('entrybox').addEventListener('keyup', keypressed2); 

    function keypressed2(e) {
        if (e.key == "Control" || e.key.substr(0,5) == "Arrow") {
            showsolution();
        }
        else if (!multi_input && e.key.length == 1) {
            keypressed(e.key);
        }
    }
</script>


<br><br>

<input type="submit" id="startbutton" onclick="" value="<?=l("start",1)?>">
&nbsp;
<span id="char"></span>

</form>

<?
	/* Initialize Database for the user, and retrieve old values */
	/* row exists for user? */
	$query =mysqli_query($db,"select count(*) from lcwo_mmresults where uid='$uid'");

	if (!$query) { echo "db failure1"; return; }

	$tmp = mysqli_fetch_row($query);
	if ($tmp[0] == 0) {         /* first entry, create dummy for user */
		$query =mysqli_query($db,"insert into lcwo_mmresults (`uid`) values ($uid)");
	}

	/* fetch values */
	$query = mysqli_query($db,"select * from lcwo_mmresults where uid=$uid");
	if (!$query) { echo "db failure2"; return; }
	$values = mysqli_fetch_row($query);

	/*
		$values = ('nr', 'uid', 'count', 'k0', ..., 'k40');
	*/

?>

<script>
	var buzzer_active = 1;
	var lesson = <? if ($_SESSION['mm']['charset'] == 0) { echo $_SESSION['koch_lesson']; } else { echo count($cs)-1; } ?>;
	var player = <?=$_SESSION['player']; ?>;
	var speed = <?=$_SESSION['cw_speed']; ?>;
	var freq = <?=$_SESSION['cw_tone']; ?>;
	var effspeed = speed;
	var firstkeypress = 0;
	var lastkeypress = 0;
	var currchar = "";
	var currentfailed = 0;	/* failed current character => repeat */
	var charcount = <?=$values[2];?>;
	var sessioncharcount = 0;
	var mmchar = new Array("<? echo join('","', $cs); ?>");
    var started = false;

     /* for languages where we can enter one character with one key,
       * enter can be used to skip a letter. for languages where several
       * keystrokes are needed (e.g. Japanese), enter will be used to
       * evaluate the input.
      */
    var multi_input = <? if ($_SESSION['mm']['charset'] == 6 or $_SESSION['mm']['charset'] == 7) { echo "true"; } else { echo "false"; } ?>;

	var h5c = "cw.mp3";
	
	var badness = new Array();
<?
		array_shift($values);	/* nr */
		array_shift($values);	/* uid */
		array_shift($values);	/* count */
		for ($i=0; $i < count($cs); $i++) {
			echo 'badness["'.$cs[$i].'"] = '.$values[$i].';';
		}
?>
	update();
	
	/* evaluate correctness of entry, and send next */	
	function keypressed (s) {
        s = document.getElementById('entrybox').value;
        // console.log("pressed >" +  s + "<");
        document.getElementById('entrybox').value = "";

		if (sessioncharcount && (s == ' ')) {	/* Space -> Send again */
			// replay w/o buzzer
			playletter(currchar, 0);
			return;
		}
		
		if (currchar) {		/* otherwise it's the first letter */
			s = s.toUpperCase();
			if (!s) {
				return;
			}
			
			sessioncharcount++;
			
			if (s == currchar) {
					if (!currentfailed && badness[currchar] > 5) {
						badness[currchar] *= 0.9;
						badness[currchar] = parseInt(badness[currchar]);
					}
					highlight(currchar, '#cdf010');
					currentfailed = 0;
					charcount++;
			}
			else {
					currentfailed = 1;
					highlight(s, '#ff6666');
			}
			update();
		}

		/* Send stats to server, store in database */ 
		if (!(charcount % 20)) {
			savestats();
		}
	
		if (!currentfailed) {	
			currchar = nextchar();
			playletter(currchar, 0);
		}
		else {
			// replay with buzzer
			playletter(currchar, 1);
		}

	}

    function enteraction () {
        if (!started) {
            skipletter();
            started = true;
            return;
        }

        if (multi_input) {
            keypressed(document.getElementById('entrybox').value);
        }
        else {
            skipletter();
        }
    }

    function skipletter () {
			/* show which letter is really was and increase error bar */
			highlight(currchar, '#ff6666');
			if (badness[currchar] < 95) {
				badness[currchar] += 5;
			}
			update();
			currchar = nextchar();
			playletter(currchar, 0);
            document.getElementById('char').innerHTML = "";
	}

    function showsolution () {
	        highlight(currchar, '#dababe');
            document.getElementById('char').innerHTML = "Character is: <b>" + currchar + "</b>"; 
            f();
    }

	
	function update () {
        document.getElementById('char').innerHTML = "";
		document.getElementById('charcount').innerHTML = charcount;
		document.getElementById('lessondisplay').innerHTML = lesson;
		document.getElementById('speed').innerHTML = speed;
		document.getElementById('effspeed').innerHTML = effspeed;

		if (!firstkeypress) {
			firstkeypress = new Date().getTime();
		}
		else {
			lastkeypress = new Date().getTime();

			/* eff WpM: characters / minute */
	
			effspeed = Math.round(10*sessioncharcount/(5*(lastkeypress - firstkeypress)/(60 * 1000)))/10;
		}


		/* update badness bars */
		for (i=0; i < mmchar.length; i++) {
			x = document.getElementById(mmchar[i]+'-0');
			y = document.getElementById(mmchar[i]+'-1');
			z = document.getElementById('label-'+mmchar[i]);
	
			if (i <= lesson) {
                x.src = "/pics/mm-0.png";
				x.height = 101-badness[mmchar[i]];			
				y.height = badness[mmchar[i]];			
                z.className = 'mmactive';
			}
			else {
                x.height = 100;
                x.src = "/pics/mm-2.png";
				y.height = 1;			
                z.className = 'mminactive';
			}
		}
		
		f();
		return false;
	}

	function highlight (ch, color) {
			var tmp;
			for (i=0; i < mmchar.length; i++) {
				if ((tmp = document.getElementById('label-'+mmchar[i])) != null) {
						tmp.style.backgroundColor = document.body.style.backgroundColor;
				}
			}
			if ((tmp = document.getElementById('label-'+ch)) != null) {
					tmp.style.backgroundColor = color;
			}
	}

	
	function reset_errors () {
		var question_html = "<?=l('reseterrorbars',1);?>";
		question_utf8 = question_html.replace(/&#([0-9]+);/g, html2utf);

		if (confirm(question_utf8) == true) {
			for (i=0; i < mmchar.length; i++) {
					badness[mmchar[i]] = 101;
			}
		}
		update();
	}

	function html2utf (match, p1, o, s) {
		return String.fromCharCode(parseInt(p1));
	}

	function lessonchange (v) {
		lesson += v;
		if (lesson < 1) lesson = 1;
		if (lesson >= mmchar.length) lesson = mmchar.length - 1;
		update();
	}

	function speedchange (v) {
		speed += v;
		if (speed < 5) speed = 5;
		if (speed > 999) speed = 999;
		update();
	}

	function changebar (obj, e) {
		var id = obj.id;
		if (!e) var e = id.event;
		var ypos = e.clientY + document.body.scrollTop;
		var fromtop = 0;

		/* find absolute position  from top, through all parents */
		if (obj.offsetParent) {
			do {
				fromtop += obj.offsetTop;
			} while ((obj = obj.offsetParent) != null);
		}
	
		ypos = ypos - fromtop;	
		
		if (id.substr(2,1) == "0") {	/* upper part */
			badness[id.substr(0,1)] = 101-ypos;
		}
		else {							/* lower part */
			badness[id.substr(0,1)] -= ypos;
		}

		update();
		
	}


	
	function in_array(needle, a) {
		for (i=0;i < a.length;i++) {
			if (a[i] == needle) {
				return true;
			}
		}
		return false;
	}

	/* Players need unified JavaScript API!! */
	function playletter (l, buzz) {
		console.log('Playletter ' + l + ' Buzz: ' + buzz + " player = " + player);
		if (buzz && buzzer_active) {
			l = '|T2 |f200 |v55 T |v100 |T0 |f' + freq + ' ' + l;
		}
		var flashurl =  '<?=CGIURL();?>cw.mp3?s='+speed+'&e='+speed+'&f='+freq+'&t='+l;
        if (player == <?=PL_HTML5;?>) {	
			/* espeed hack to make quite sure a different URL is called;
			otherwise sometimes the HTML5 player of Firefox get stuck on single
			letters */
			var espeed = parseInt(1+Math.random()*speed);
			var p = document.getElementById('player1');
			p.src = '<?=CGIURL();?>'+h5c+'?s='+speed+'&e='+espeed+'&f='+freq+'&t='+l;
			p.load();
			p.play(); 
		}
        else if (player == <?=PL_JSCWLIB;?>) {
            l = l.replace(/\|v/gi, "|x");
            pa[1].setText(l);
            pa[1].setWpm(speed);
            pa[1].setEff(speed);
            pa[1].setFreq(freq);
            pa[1].enablePS(false);
            pa[1].setStartDelay(0.1);
            pa[1].play();
        }
		else {
			loadFile('js1', {file:flashurl, type:'mp3', autostart:'true'})
		}
	}

	function replayletter () {
        if (player == <?=PL_HTML5;?>) {	
			document.getElementById('player1').play();
		}
        else if (player == <?=PL_JSCWLIB;?>) {
            pa[1].play();
        }
		else {
			sendEvent('js1','stop');
			sendEvent('js1','playpause');
		}
	}


    function nextchar () {

        /* select next, weighted distribution; characters with less accuracy
           are most likely. find random number between 0 and accumulated badness
           of all letters up to current lesson. then check in which letter's
           badness-area it lies...
         */

        total = 0;

        var lessonmax = lesson + 1;

        // limit to the number of characters in current character set
        if (lessonmax > mmchar.length) {
            lessonmax = mmchar.length;
        }

        for (i = 0; i < lessonmax; i++) {
            total += Math.pow(badness[mmchar[i]],2);
        }
        console.log("total = " + total);

        rand = parseInt(Math.random()*total);

        console.log("rand = " + rand);
        i = 0;
        while (rand > Math.pow(badness[mmchar[i]],2)) {
            rand -= Math.pow(badness[mmchar[i]],2);
            i++;
        }

        currentfailed = 0;
        console.log('nextchar(lesson=' + lesson + ') => ' + i + ' => ' + mmchar[i]);
        return mmchar[i];
    }


function savestats () {

var response_location = document.getElementById('response');
		
// Provide the XMLHttpRequest class for those complete idiots who use
// the shitty IE 5.x-6.x browser.
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
  var posturl = "//" + arr[2] + "/api/mmsavestats.php";

  var request =  new XMLHttpRequest();
  request.open("POST", posturl, true);
  request.setRequestHeader("Content-Type",
                           "application/x-www-form-urlencoded");
 
  request.onreadystatechange = function() {
    var done = 4, ok = 200;
    if (request.readyState == done && request.status == ok) {
      if (request.responseText) {
			response_location.innerHTML =  request.responseText;
      }
	  else {
		  response_location.innerHTML =  "<b>ERROR: Saving stats failed.  Contact administrator (<?=ADMINMAIL;?>) with details if the problem persists.</b>";
	  }
    }
  };
 
 var statstring = ""; 
  for (j=0;j<mmchar.length;j++) {
		statstring = statstring + '&k'+j+'='+badness[mmchar[j]];
  }
  request.send('count=' + charcount + statstring);
}

function f () {
	document.getElementById('entrybox').focus();
}

document.getElementById('startbutton').focus();
	
	
</script>


<br>
<br>
<br>
<br>
<?
	$mode = $_SESSION['player'];
	player("", $mode, 99, 99, 1, 1,0,0);
?>
