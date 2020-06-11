<?

function player ($text, $mode, $speed, $eff, $autostart, $nr, $layout, $focus) {

global $servers;

if ($mode != 1) {
    $text = $text . "    ^";				# XXX Hack to add space at the end...
}

$text_escaped = preg_replace('/"/', '\"', $text);
$text_encoded = rawurlencode($text);
$text_escaped = preg_replace('/\+/', '<AR>', $text_escaped);

$playpause = l('playpause');
$playpause = preg_replace('/\s+\/\s+/', '/', $playpause);

if (!$nr) {
	$mnr = rand(1,10000);
}
else {
	$mnr = $nr;
}

$cw_tone = $_SESSION['cw_tone'];


/* Flash 10 hack */
if ($_SESSION['player'] == 2) {
    $fp10 = f10spaces($_SESSION['cw_speed']);
}
else {
	$fp10 = "";
}

$cgi_mp3 = CGIURL()."cw.mp3";
$cgi_ogg = CGIURL()."cw.ogg";

$url_escaped = "?s=$speed"."&e=$eff&f=$cw_tone&"."t=$fp10"."$text_escaped";
$url_encoded = "?s=$speed"."&e=$eff&f=$cw_tone&"."t=$fp10"."$text_encoded";


/* HTML for the Button  (used by HTML5 and New Flash) */
$buttonhtml = "<a id=\"playbutton$mnr\"
style=\"white-space:nowrap;font-size:16px;border-style:outset;border-width:4px;
padding:2px;background-color:#dddddd;font-weight:bold;
text-decoration:none;\" onMouseup=\"if(tmp=getElementById('eform')){tmp.input.focus();}\" onkeyup=\"if(tmp=getElementById('eform')){tmp.input.focus();}\"
href=\"javascript:playpause($mnr);\">$playpause</a>";


if (($mode == '') || !isset($mode))  {
	$mode = 1;
}

switch ($mode) {
	case '1':	/* jscwlib */

        $prefix = $_SESSION['vvv'] ? true : false;
        $dly = $_SESSION['delay_start'];
        $ews = $_SESSION['cw_ews'];

        $text = str_replace('\\', '', $text);
        $text = str_replace('"', '\"', $text);
        $text = preg_replace('/[\n\r]/', ' ', $text);

        if ($eff >= $speed) {
            $eff = 0;
        }

        $player = <<<JS
<div id="pv"></div>

<script>

    // make an array which holds all player objects (if not already there)
    if (!pa) {
        var pa = Array();    
    }

    var pv = new jscw();
    pv.renderPlayer("pv", pv);
    pv.setText("$text");
    pv.setWpm($speed);
    pv.setEff($eff);
    pv.setEws($ews);
    pv.setFreq($cw_tone);
    pv.setPrefix("vvv = ");
    pv.enablePS($prefix);
    pv.setStartDelay($dly);
    pv.onPlay = function () { if(tmp=document.getElementById('eform')){tmp.input.focus();} };

    pa[$mnr] = pv;


    function playpause(p) {
        if (pa[p].getRemaining()) {
            pa[p].pause();
        }
        else {
            pa[p].play();
        }
    }

</script>
JS;

        $player = preg_replace("/pv/", "pv".$mnr, $player);

        # we abuse the unused $layout variable to allow returning instead of
        # printing the player
        if ($layout && $layout == "return") {
            return $player;
        }
        else {
            echo $player;
        }

    break;

	/* JW Player Flash stuff */
	case '0':
	case '2':

	if (!$autostart) {
	if ($layout == 1) {	/* Play/Pause next to Player in table, no Link */
		echo " <div style=\"width:380px;border-width:thin;border-style:dashed; text-align:center;padding:6px\">";
		echo "<table><tr><td>";
	}
	else {
	echo " <div style=\"width:220px;border-width:thin;border-style:dashed; text-align:center;padding:2px\"> <br>";
		echo "<p>";
	}
	echo "
	<a id=\"playbutton$mnr\"
	style=\"font-size:16px;border-style:outset;border-width:4px;
	padding:2px;background-color:#dddddd;font-weight:bold;text-decoration:
	none;\"
   	onMouseup=\"if(tmp=getElementById('eform')){tmp.input.focus();}\"
   	onkeyup=\"if(tmp=getElementById('eform')){tmp.input.focus();}\"
	href=\"javascript:sendEvent('js$mnr','playpause');\">".
	$playpause	
	."</a>";
	if ($layout == 1) { 
		echo "</td><td>&nbsp;&nbsp;</td><td>";
	}
	else {
		echo "</p><br>";
	}
	}
	else {
		$mnr = $nr;
	}
	echo '
	<div id="container'.$mnr.'"> 
	Flash player not supported! <a href="/cwsettings">Click here to select another player: CW settings</a>.</div>';
	if ($layout == 1) {
		echo "</td></tr></table>";
	}
	echo '
	<script type="text/javascript">
		var s'.$mnr.' = new SWFObject("mediaplayer.swf","js'.$mnr.'","190","20","8");
		s'.$mnr.'.addParam("allowfullscreen","true");
		s'.$mnr.'.addVariable("width","190");
		s'.$mnr.'.addVariable("height","20");
		s'.$mnr.'.addVariable("file", encodeURIComponent("'.$cgi_mp3.$url_escaped.'"));
		s'.$mnr.'.addVariable("type", "mp3");
		s'.$mnr.'.addVariable("enablejs","true");
		s'.$mnr.'.addVariable("javascriptid","js'.$mnr.'");';
		if ($autostart) {
			echo 's'.$mnr.'.addVariable("autostart","true");';
		} 
		echo 's'.$mnr.'.write("container'.$mnr.'");
	</script>';

	echo '<script type="text/javascript">
	function sendEvent(swf,typ,prm) { 
		  thisMovie(swf).sendEvent(typ,prm); 
	 };
	function getUpdate(typ,pr1,pr2,swf) {
	};
	function thisMovie(swf) {
		    return document[swf];
	};
	function loadFile(swf,obj) { 
			  thisMovie(swf).loadFile(obj); 
	  };
	 </script>';
	

 	if (!$autostart and $layout == 0) {	 
		echo "<br> <a href=\"".$cgi_mp3."?d=001&".substr($url_encoded, 1).'">'.l('linktomp3file').'</a>
		';
	}

	echo "</div>";	

	if ($focus) {
?>
		<script type="text/javascript">
			document.getElementById('playbutton<? echo $mnr ?>').focus();
		</script>
<?
	}

	break;

	case '3':	/* HTML 5 */
		if ($autostart) {
			$layout = 3;
		}
        $autostart = 0;
		
		$cgi_url = $cgi_mp3;
		
		/* Assemble HTML for the Player itself */
		$playerhtml = "<audio id=\"player$mnr\" controls style=\"width:200px;height:40px\" autobuffer ";
		if ($autoplay) {
			$playerhtml .= " autoplay ";
		}
		$playerhtml .= " src=\"".$cgi_url.$url_encoded."\">";
		$playerhtml .= "HTML5 Audio tag not supported :-( </audio>";


		/* Create Player depending on Layout */
		if ($layout == 1) {	/* small (e.g. char previews) */
			echo " <div style=\"width:370px;border-width:thin;border-style:dashed; text-align:center;padding:6px\">";
			echo "<table><tr><td>".$buttonhtml.
				"</td><td>&nbsp;$playerhtml</td></tr></table></div>\n";
		}
		else if ($layout == 0) {	/* full */
			echo " <div style=\"width:280px;border-width:thin;border-style:".
					"dashed; text-align:center;padding:2px\"><br>";
			echo $playerhtml;
			echo "<br><br><p>$buttonhtml</p>";
			echo "<br><a href=\"".$cgi_mp3."?d=001&".substr($url_encoded, 1).'">'.l('linktomp3file').'</a>
		';
		}
		else if ($layout == 3) {	/* no controls and stuff */
			echo $playerhtml;
		}

		/* JavaScript for controls */
		echo "<script>function playpause(i) {
				var p = document.getElementById('player'+i);
					if (p.paused) {
						p.play();
					}
					else {
						p.pause();
					}	
				}
				</script>\n";
		echo "</div>";
	break;
	
} /* switch */


}

?>
