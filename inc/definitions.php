<?

# LCWO definitions
#
# This file contains defaults suited for the Docker version
# of LCWO. 

# For real deployments, you will want to create your own file,
# called definitions.custom.php, and put it into the `inc` folder.

if (file_exists($_SERVER['DOCUMENT_ROOT']."/inc/definitions.custom.php")) {
	include($_SERVER['DOCUMENT_ROOT']."/inc/definitions.custom.php");
	return;
}

# Otherwise, we use the defaults:

define("ADMINNAME", "Mr. or Ms. LCWO");
define("ADMINMAIL", "help@lcwo.net.changeme.invalid");
define("ADMINURL", "https://invalid.invalid/");

define("MAILSIGNATURE", 
"Name Surname, Callsign
Street 42
12345 Town
Phone: +1234
Mail: mail@invalid.invalid");

# User IDs of special users
define("TESTUSER",  "1");	# test user - public login. restricted in some ways.
define("ADMIN",     "2");	# admin user - can enter the admin console /admin
define("HOSTNAME",  "localhost:8000");
define("BASEURL",  "http://".HOSTNAME);
define("DEV",       "1");	# development flag (enables translation stuff)

define("FORUM_RO",   false); # Make forum read-only?
define("USERG_RO",   false); # Disable creating new user groups?
define("PMSG_RO",    false); # Disable sending new private messages

define("CAPTCHA_IMG",    false); # false = use text captcha for signup, true = image captcha

define("PL_JSCWLIB", 1);
define("PL_FLASH", 2);
define("PL_HTML5", 3);

# default player for new users
define("PL_DEFAULT", PL_JSCWLIB);

# User IDs of the forum moderators.
$g_moderators = array("1", "2");

# User IDs of word training editors
$g_wordeditors = array("1", "2");

define("SALT", "salt_for_passwords");
define("SALT_IMG_URL", "salt_for_image_urls");

define("TILES_URL", "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png");
define("TILES_ATTRIB", 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors');

# These languages are available for the user.
$langs = array('de', 'da', 'en', 'fr', 'cs', 'pt', 'it', 'es', 'pl', 'ro', 'ja',
   'sl', 'sv', 'nl', 'ru', 'bs', 'fi', 'gr', 'ca', 'hu', 'hr', 'ms', 'th',
   'tr', 'zh', 'cn', 'bg', 'uk', 'br', 'no', 'sr', 'si');
sort($langs);

# Themes. .css is appended to the selected theme.
$themes = array("style", "styledark");

# how long will users be shown as "online" after last activity
define("TIMEOUT",  "15");

# CGI server for HTML5 player
$cgiserver = "localhost:8000/";


# Ranges of untrusted IP addresses - when signing in, users from these
# IP ranges need to solve a captcha.
#       
$untrustedips = array("^134\.0\.30\.35", "^any_reg_ex$");

# Forum bad words
$badwords = array("levitra", "viagra", "impotence", "my spam list",
"stopspam", "spamtoday", "spam.today", "porn", "sexy", "bondage",
"hentai", "masturbat", "torrent", "nip slip", "carrie underwood",
"audiolive.org", "monstrance", "url=", "recordsmusic.org", "pharmarcy",
"directorytelephone", "xyz.net.tw");

?>
