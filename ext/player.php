<html>
<head>
<link href="/styleiframe.css" rel="stylesheet" type="text/css">
<script src="https://lcwo.net/js/jscwlib.js"></script>
</head>
<body>  <?# if ($_GET[z]) { echo "onload='playpause1();'";}?>
<?
include("../inc/definitions.php");
function l() {
}
function CGIURL () {}
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

include('../inc/playerfunctions.php');

if (!$t) { $t = "Hallo"; }
if (!$e) { $e = 20; }
if (!$s) { $s = 20; }
if (!is_int($_SESSION['cw_tone'])) { $_SESSION['cw_tone'] = 600; }

$_SESSION['vvv']= 0;
$_SESSION['delay_start']= 0;
player($t, 1, $s, $e, 0, 1,1,0);
?>
<div id="pv1"></div>
<a class="footer" target="_blank" href="http://lcwo.net/">LCWO.net</a> - <a class="footer" target="_blank"  href="http://lcwo.net/text2cw">Text to Morse Converter</a>
</body>
</html>
