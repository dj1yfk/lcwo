<?


function player ($text, $mode, $speed, $eff, $autostart, $nr, $layout, $focus) {

global $servers;

$text = $text . "    ^";				# XXX Hack to add space at the end...
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
	$mode = 0;
}

switch ($mode) {
	case '1':	/* Embedded OLD */
	if ($autostart == 1) {
		$autostart = "true";
	}
	else {
		$autostart = "false";
	}
	
echo "<embed src=\"$cgi_mp3$url_encoded\" autostart=\"$autostart\" hidden=\"false\" height=\"100\"
width=\"250\"><br> ";
	echo "<br> <a onMouseover=\"top.status='';return true;\"
	href=\"".$cgi_mp3.$url_encoded.'">'.l('linktomp3file').'</a>';
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
	<div id="container'.$mnr.'"> <a
	href="http://www.macromedia.com/go/getflashplayer">Get the
	Flash Player</a> and activate JavaScript to see this
	player. If you are using Android or an Apple device, 
	please chose the HTML5 audio option in your <a href="/cwsettings">CW settings</a>.</div>';
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
//		  if(navigator.appName.indexOf("Miicrosoft") != -1) {
//		    return window[swf];
//		  } else {
		    return document[swf];
//		  }
	};
	function loadFile(swf,obj) { 
			  thisMovie(swf).loadFile(obj); 
	  };
	 </script>';
	

 	if (!$autostart and $layout == 0) {	 
		echo "<br> <a onMouseover=\"top.status='';return true;\"
		href=\"".$cgi_mp3.$url_encoded.'">'.l('linktomp3file').'</a>
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
			echo "<br><a href=\"".$cgi_mp3.$url_encoded.'">'.l('linktomp3file').'</a>
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
	
	case '4':		/* New Flash */

		$id = $mnr;	
		if ($autostart) { $layout = 3; }

			?>
		<script type="text/javascript">
			var myListener<?=$id;?> = new Object();
			myListener<?=$id;?>.onInit = function() {
				this.position = 0;
				this.paused = 0;
			};

			myListener<?=$id;?>.onUpdate = function() {
				var pos_seconds = parseInt(this.position/1000);
				var pos_minutes = 0, dur_minutes = 0;
				while (pos_seconds >= 60) { pos_minutes++; pos_seconds -= 60; }
				if (pos_seconds < 10) { pos_seconds = '0' + pos_seconds; }	
				document.getElementById("info_position<?=$id;?>").innerHTML = 
					pos_minutes + ':' + pos_seconds;

				var timelineWidth = 160;
				var sliderWidth = 40;
				var sliderPositionMin = 0;
				var sliderPositionMax = sliderPositionMin + timelineWidth - sliderWidth;
				var sliderPosition = sliderPositionMin + Math.round((timelineWidth - sliderWidth) * this.position / this.duration);

				if (sliderPosition < sliderPositionMin) {
					sliderPosition = sliderPositionMin;
				}
				if (sliderPosition > sliderPositionMax) {
					sliderPosition = sliderPositionMax;
				}
				document.getElementById("playerslider<?=$id;?>").style.left = sliderPosition+"px";
			};
                
			function getFlashObject<?=$id;?>() {
				return document.getElementById("myFlash<?=$id;?>");
			}

			function playpause<?=$id;?>() {
				if (myListener<?=$id;?>.isPlaying && !this.paused) {
					getFlashObject<?=$id;?>().SetVariable("method:pause", "");
					this.paused = 1;
				}
				else {
					if (myListener<?=$id;?>.position == 0) {
					getFlashObject<?=$id;?>().SetVariable("method:setUrl", "<?=$cgi_mp3.$url_encoded;?>");
					}
					getFlashObject<?=$id;?>().SetVariable("method:play", "");
					getFlashObject<?=$id;?>().SetVariable("enabled", "true");
					this.paused = 0;
				}
			}
		</script>

		<!--[if IE]>
		<script type="text/javascript" event="FSCommand(command,args)" for="myFlash<?=$id;?>">
		eval(args);
		</script>
		<![endif]-->

		<object id="myFlash<?=$id;?>" type="application/x-shockwave-flash" data="/player_mp3_js.swf" width="1" height="1">
			<param name="movie" value="/player_mp3_js.swf" />
			<param name="AllowScriptAccess" value="always" />
			<param name="FlashVars" value="listener=myListener<?=$id;?>&amp;interval=500" />
		</object>
<?
		if ($layout == 1) {	/* small for previews */
?>
		<table><tr><td>
		<?=$buttonhtml;?></td><td>
		<div id="playercontroller">
			<span class="timeline"><a id="playerslider<?=$id;?>" href="#slider">SLIDER</a></span>
			<span class="position" id="info_position<?=$id;?>">0:00</span>
		</div>
		</td></tr></table>
<?
		}
		else if ($layout == 0) {	/* full */
			echo " <div style=\"width:220px;border-width:thin;border-style:".
					"dashed; padding:10px\">";
			?>			
		<div id="playercontroller">
			<span class="timeline"><a id="playerslider<?=$id;?>" href="#slider">SLIDER</a></span>
			<span class="position" id="info_position<?=$id;?>">0:00</span>
		</div>
		<?
			echo "<br><p style='text-align:center;'>$buttonhtml</p>";
			echo "<p style='text-align:center;'><a href=\"".$cgi_mp3.$url_encoded.'">'.l('linktomp3file').'</a></p></div>';
		}
		else if ($layout == 3) {
			/* no controls */
	?>
		<div id="playercontroller">
			<span class="timeline"><a id="playerslider<?=$id;?>" href="#slider">SLIDER</a></span>
			<span class="position" id="info_position<?=$id;?>">0:00</span>
		</div>
	<?
		}
		
?>
		<script>
			function playpause (player) {
				eval('playpause' + player + '();');
			}
		</script>

<?
	break;	
	
} /* switch */


}

?>
