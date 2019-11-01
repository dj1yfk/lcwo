<?
header("Content-Type: text/html; charset=iso-8859-1");
ini_set("session.gc_maxlifetime",43200);
session_start();

include("inc/definitions.php");

/* Avoid Session-Hijacking by Search Engines and subsequently
* other users. Skip for test user. */

if ($_SESSION['uid'] != 16) {
	if (isset($_SESSION['HTTP_USER_AGENT'])) {
		if ($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT'])) {
				session_unset();
				session_destroy();
				$destroyed = 1;
		}
	}
	else {
		session_regenerate_id();
		$_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
	}
}

include("inc/connectdb.php");
include("inc/killspam.php");

#if ($_SESSION["uid"]) {
#	mysql_query("update lcwo_online set `LASTACTIVE`=NULL
#			where `UID`=".$_SESSION["uid"]);
#}

header("Cache-Control: no-cache, must-revalidate"); 
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 
header("Pragma: no-cache");

include("inc/functions.php");
rand_init();

if ($_SESSION['uid']) {

is_deleted_user();

# logging for debugging purposes
#$userip = getenv('REMOTE_ADDR');
#$fh = fopen('log.txt','a');
#if ($fh) {  
#                fputs($fh, $_GET[p]." ".$_GET[u]." ".$_SESSION['username']." $userip\n");  fclose($fh);
#}

if ($_SESSION['loadedlocale'] != 1 or in_array($_GET['hl'], $langs)) {
	if (!$_SESSION['lang']) {
			if (in_array($_GET['hl'], $langs)) {
				loadlocale($_GET['hl']);
			}
			else {
				loadlocale('en');
			}
	}
	else {
		loadlocale($_SESSION['lang']);
	}
}

/* it can still be an old locale version, check, load new if
* needed */
updatelocale();

} /* no session uid */
else if (in_array($_GET['hl'], $langs)) {
    loadlocale($_GET['hl']);
    unset($_SESSION['lang']);
    $_SESSION['lang'] = $_GET['hl'];
}
else if ($_SESSION['lang']) {
    loadlocale($_SESSION['lang']);
}
else if (in_array(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2), $langs)) {
	$browserlang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    loadlocale($browserlang);
    unset($_SESSION['lang']);
    $_SESSION['lang'] = $browserlang;
}
else {
    loadlocale('en');
}

if ($_GET['debug']) {
    switch ($_GET['debug']) {
        case "1":
            $_SESSION['debug'] = 1;
            break;
        case "2":
            $_SESSION['debug'] = 0;
            break;
        case "3":
            $_SESSION['debug'] = 3;
            break;
    }
}

include("inc/sites-titles.php");

if (isset($_GET['p']) && (in_array($_GET['p'], $sites))) {
	
	$p = $_GET['p'];
}
else {
	if ($_SESSION['uid']) {
		$p = 'main';
	}
	else {
		$p = 'welcome';
	}
}


include("inc/header.php");
include("inc/menu.php");

?>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="220" valign="top">
			<? 
				if (!$_SESSION['uid']) {
					include("inc/login.php"); 
					include("inc/language.php"); 
				}
				else {
					include("inc/personalmenu.php"); 
				}
			?>
			<br>
			<? include("inc/online.php"); ?>
		</td>
		<td valign="top">
			<? include("inc/$p.php"); ?>
		</td>
		<td width="20">
		&nbsp;
		</td>
	</tr>
</table>

<?
include("inc/footer.php");

if ($destroyed) {
echo "Destroyed.";
}

if (DEV && ($_SESSION['debug'] == 3)) {
    include("babel.html");
    include("babel.js");
}

?>

</body>
</html>

<?
# save customized settings for
# plain, wordtraining, callsigns, text2cw, download
# from session variable to re-load them at the next
# login

if ($_SESSION['uid']) {
    $tmp = $_SESSION;
    $tmp['l'] = "";

    foreach (array("plain", "wordtraining", "callsigns", "text2cw", "download") as $item) {
        if ($_SESSION[$item]) {
            $save[$item] = $_SESSION[$item];
        }
    }

    if ($save) {
        $save_ser = mysqli_real_escape_string($db, serialize($save));

        mysqli_query($db, "INSERT INTO lcwo_userprefs (uid, prefs) VALUES
            (".$_SESSION['uid'].", '$save_ser')
            ON DUPLICATE KEY UPDATE prefs='$save_ser';");
    }

}

mysqli_close($db);

?>
