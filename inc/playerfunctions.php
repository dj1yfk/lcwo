<?

function player ($text, $mode, $speed, $eff, $autostart, $nr, $layout, $focus) {

global $servers;

if ($mode != PL_JSCWLIB) {
    $mode = PL_HTML5;
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

$cw_tone = $_SESSION['cw_tone'] ? $_SESSION['cw_tone'] : 600;

$cgi_mp3 = CGIURL()."cw.mp3";
$cgi_ogg = CGIURL()."cw.ogg";

$url_escaped = "?s=$speed"."&e=$eff&f=$cw_tone&"."t="."$text_escaped";
$url_encoded = "?s=$speed"."&e=$eff&f=$cw_tone&"."t="."$text_encoded";


/* HTML for the Button  (used by HTML5) */
$buttonhtml = "<a id=\"playbutton$mnr\" class=\"playbutton\"
onMouseup=\"if(tmp=getElementById('eform')){tmp.input.focus();}\" onkeyup=\"if(tmp=getElementById('eform')){tmp.input.focus();}\"
href=\"javascript:playpause($mnr);\">$playpause</a>";


switch ($mode) {
    case PL_JSCWLIB:
        $prefix = $_SESSION['vvv'] ? 'true' : 'false';
        $dly = $_SESSION['delay_start'] ? $_SESSION['delay_start'] : 0.1;
        $ews = $_SESSION['cw_ews'] ? $_SESSION['cw_ews'] : 0;

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

	case PL_HTML5:
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
