<!DOCTYPE html>
<html>
<head>
<title><? 
$tmptitle = l($titles[$p],1); 
if ($tmptitle) {
	if ($p == 'profile') {
		$tmptitle .= " ".$_GET['u'];
	}
	echo "$tmptitle - ";
}
if (!$tmptitle) {
	echo "LCWO.net - ";
}
?>Learn CW Online</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="description" lang="en" content="Learn CW Online - At LCWO you can learn Morse Code (CW) online, in your web browser!">
<?
if ($_SESSION['lang'] != "en") {
$query = mysqli_query($db, "select lang, text from lcwo_texts where
name='welcometolcwo1' and lang='".$_SESSION['lang']."';");
while ($w = mysqli_fetch_row($query)) {
	echo '<meta name="description" lang="'.$w[0].'" content="'.$w[1]."\">\n";
}
}
?>
<meta name="keywords" content="learn cw, cw, morse code practice, telegraphy, learn morse code, morse code, koch method, morsen lernen, impare il cw, high speed cw, hst, qrq, ham radio, dj1yfk, rufzxp, fabian kurz">
<meta name="author" content="Fabian Kurz">
<meta property="og:title" content="Learn CW Online" />
<meta property="og:type" content="website" />
<meta property="og:url" content="https://lcwo.net/" />
<meta property="og:image" content="https://lcwo.net/pics/lcwo.png" />
<meta property="og:description" content="Learn CW Online - a free web app for learning Morse code!" />
<link rel="icon" href="/favicon.ico">
<link rel="shortcut icon" href="/favicon.ico">
<?
include('inc/canonical.php');
?>
<link href="/atom.xml" type="application/atom+xml" rel="alternate" title="LCWO News Feed">
<link href="/forumatom.xml" type="application/atom+xml" rel="alternate" title="LCWO Forum Feed">
<?
$forward = array('logout' => 'bye', 'delete'=> 'main'); 

if ($forward[$_GET['p']]) {
	    echo "<meta http-equiv=\"refresh\" content=\"1;url=/".$forward[$_GET['p']]."\"/>";
	}


$theme = $_SESSION['theme'] ? $_SESSION['theme'] : 'style';

?>
<link id="lcwocss" href="/<?=$theme;?>.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="/js/leaflet.css">
<script type="text/javascript" src="/js/jscwlib.js?cachebreak=<? echo filemtime("js/jscwlib.js"); ?>"></script>

<!-- User activity heatmap dependencies
curl -L 'https://d3js.org/d3.v7.min.js' > js/d3.js
curl -L 'https://unpkg.com/cal-heatmap/dist/cal-heatmap.min.js' > js/cal-heatmap.js
curl -L 'https://unpkg.com/cal-heatmap/dist/cal-heatmap.css' > cal-heatmap.css
curl -L 'https://unpkg.com/@popperjs/core@2' > js/popperjs-core.js
curl -L 'https://unpkg.com/cal-heatmap/dist/plugins/Tooltip.min.js' > js/cal-heatmap-Tooltip.js
-->

<script src="/js/d3.js"></script>
<script src="/js/cal-heatmap.js"></script>
<link rel="stylesheet" href="/cal-heatmap.css">
<script src="/js/popperjs-core.js"></script>
<script src="/js/cal-heatmap-Tooltip.js"></script>

</head>
<body bgcolor="#ffffff"> 
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tbody>
		<tr class="headerline">
			<td style="background-image:url(/pics/lcwo-bg2.png);" width="41%">
			<a href="/"><img style="border-style:none;" src="/pics/lcwo.png" height="75" width="214" title="LCWO - Home" alt="[LCWO LOGO]"></a> </td>
			<td style="background-image:url(/pics/lcwo-bg2.png);" width="59%"> 
<?
	if (DEV) {
?>			
&nbsp;<strong>Development Version</strong> (<a href="https://lcwo.net/">Click here to go to LCWO.net</a>) - <a href="/babelfish.php?limit=0">Babelfish</a> - 
<?
	if ($_SESSION['debug'] == 1) {
		echo '<a href="/?debug=2">Switch debug off</a> - ';
	}
	else {
		echo '<a href="/?debug=1">Switch debug on</a> -';
	}

	if ($_SESSION['debug'] == 3) {
		echo '<a href="/?debug=2">Switch inpage translate off</a><br>';
	}
	else {
		echo '<a href="/?debug=3">Switch inpage translate on</a><br>';
	}
} /* if DEV */
if ($_SESSION['uid']) {
?>
<noscript>
<p><strong>Please note:</strong> Your browser does not support JavaScript, or it
is currently disabled. Some parts of this site may not work, or will have very
limited functionality without JavaScript.</p>
</noscript>
<?
}
if (!$_SERVER['HTTPS']) { ?> <a rel="nofollow" href="https://lcwo.net/">Click to switch to a secure connection (https).</a> <? }

if ((!array_key_exists('consent', $_SESSION)) || $_SESSION['consent'] == "0") {
?>
Please be aware of LCWO's <a href="/privacy">privacy policy</a> to comply with the <a href="https://en.wikipedia.org/wiki/General_Data_Protection_Regulation">GDPR</a>.

<span id="consent" style="white-space:nowrap;font-size:12px;border-style:outset;border-width:2px;padding:2px;background-color:#dddddd;">
<a style="text-decoration:none;" href="javascript:agree_policy();">&nbsp;OK</a>
</span>
<?
}
?>

&nbsp;
</td> </tr> </tbody></table>
<script>
function agree_policy () {
	var i = document.getElementById("consent");
	i.innerHTML = "Thanks!";
	var request =  new XMLHttpRequest();
	request.open("GET", "/api/consent.php", true);
	request.send();
}
</script>
