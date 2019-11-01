<?
# Give a meaningful canonical URL for SEO

switch ($p) {
	case 'profile':
			c("/profile/".$_GET['u']);
			break;
	case 'text2cw':
			c("/text2cw");
			break;
	case 'forum':
			if (isint($_GET['t'])) {
				c("/forum/".$_GET['t']);
			}
			else {
				c("/forum");
			}
			break;
	case 'about':
			c("/about");
}




function c ($path) {
	echo "<link rel='canonical' href='".BASEURL.$path."'>\n";
}




?>
