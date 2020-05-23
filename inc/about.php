<?

$eham1 = "4.7/5";
$eham2 = "21";

if (file_exists("inc/about-".$_SESSION['lang'].".php")) {
	include ("inc/about-".$_SESSION['lang'].".php");
}
else {
	include ("inc/about-en.php");
}

?>
