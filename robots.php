<?
header("Content-type: text/plain");
include("inc/definitions.php");
?>
User-agent: *
<?
if (DEV) {
?>
Disallow: /
<?
}
else {
?>
Disallow: /cgi-bin/
Disallow: /misc/
Disallow: /register
<?
}
?>
