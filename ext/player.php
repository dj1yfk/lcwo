<html>
<head>
<link href="/styleiframe.css" rel="stylesheet" type="text/css">
</head>
<body>  <?# if ($_GET[z]) { echo "onload='playpause1();'";}?>
<?
function l($x) {
	return "Play/Pause";
}
include("../inc/definitions.php");
if ($_GET['z']) {
	$a = explode('~~', base64_decode($_GET['z']));
	$s = $a[0];
	$e = $a[1];
	$f = $a[2];
	$t = $a[3];
	$autoplay = 1;
}
else { # Normal GET variables
	$t = $_GET['t'];
	$e = $_GET['e'];
	$f = $_GET['f'];
	$s = $_GET['s'];
	$autoplay = 0;
}

$_SESSION['cw_tone'] = $f;

function cgiurl() {
	global $servers;
	return "http://".$servers["eu"]["1"]."/cgi-bin/";
}

include('../inc/playerfunctions.php');

if (!$t) { $t = "Hallo"; }
if (!$e) { $e = 20; }
if (!$s) { $s = 20; }
if (!is_int($_SESSION[cw_tone])) { $_SESSION[cw_tone] = 600; }

player($t, 4, $s, $e, 0, 1,1,0);
?>
<a class="footer" target="_blank" href="http://lcwo.net/">Learn CW Online
- LCWO.net</a> - <a class="footer" target="_blank"  href="http://lcwo.net/text2cw">Text to Morse Converter</a>
</body>
</html>
