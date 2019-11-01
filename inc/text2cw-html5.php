<?
$html5code = '<div style="width:350px;border-width:thin;border-style:dashed;text-align:center;padding:6px">
<table><tr><td><a id="playbutton" style="font-size:16px;border-style:outset;border-width:4px;
padding:2px;background-color:#dddddd;font-family:Verdana,Arial;font-weight:bold;text-decoration:none;color:black;" 
href="javascript:playpause();">Play / Pause</a></td> <td>&nbsp;<audio id="lcwoplayer" controls 
style="width:200px;height:28px" autobuffer>
<source type="audio/mp3" src="https://cgi2.lcwo.net/cgi-bin/cw.mp3?s='.$s.'&e='.$e.'&f='.$f.'&t='.$t.' ">
<source type="audio/ogg" src="https://cgi2.lcwo.net/cgi-bin/cw.ogg?s='.$s.'&e='.$e.'&f='.$f.'&t='.$t.' ">
HTML5 Audio tag not supported :-( </audio></td></tr></table>
<a style="font-size:10px;color:#000000" href="http://lcwo.net/">Learn CW Online - LCWO.net</a> - 
<a style="font-size:10px;color:#000000" href="http://lcwo.net/text2cw">Text to Morse Converter</a></div>
<script>
function playpause() { var p = document.getElementById(\'lcwoplayer\'); if (p.paused) { p.play(); } else { p.pause(); }	}
</script>';
?>

