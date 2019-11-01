<?

$eham1 = "5/5";
$eham2 = "15";

if (file_exists("inc/about-".$_SESSION['lang'].".php")) {
	include ("inc/about-".$_SESSION['lang'].".php");
}
else {
	include ("inc/about-en.php");
}



?>


<div class="vcsid">$Id: about.php 238 2014-06-12 06:02:26Z dj1yfk $</div>
