<?
error_reporting(0);
header("Content-Type: text/html; charset=utf-8");
ini_set("session.gc_maxlifetime",43200);
session_set_cookie_params([
    'path' => '/',
    'secure' => false,
    'httponly' => false,
    'samesite' => 'Lax'
]);
session_start();

include("inc/definitions.php");
include("inc/connectdb.php");
include("inc/killspam.php");

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
?>
<script>

function getCookieVal (offset) {
    var endstr = document.cookie.indexOf (";", offset);
    if (endstr == -1) { endstr = document.cookie.length; }
    return unescape(document.cookie.substring(offset, endstr));
}

function getCookie (name) {
    var arg = name + "=";
    var alen = arg.length;
    var clen = document.cookie.length;
    var i = 0;
    while (i < clen) {
            var j = i + alen;
            if (document.cookie.substring(i, j) == arg) {
                return getCookieVal (j);
            }
            i = document.cookie.indexOf(" ", i) + 1;
            if (i == 0) break;
        }
    return null;
}

    // attempt cookie login once. when it failed for some reason, we are at
    // /#loginbycookie and we do not try again
	if ((window.location.href.indexOf("loginbycookie") == -1) && getCookie('lcwo_username') && getCookie('lcwo_hash')) {
        console.log("found cookies, attempting to log in!");
        var u = getCookie('lcwo_username');
        var h = getCookie('lcwo_hash');
	
		var request =  new XMLHttpRequest();
		request.open("POST", '/dologin', true);
        request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		request.onreadystatechange = function() {
			var done = 4, ok = 200;
			if (request.readyState == done && request.status == ok) {
				if (request.responseText) {
                    if (request.responseText.indexOf("LOGIN_SUCCESS") != -1) {
                        // login succeeded... forwarding
                        window.setTimeout( function () {
                            window.location.href = '<?=BASEURL;?>/#loginbycookie';
                        }, 1000);
                    }
				}
			}
		}
		request.send("username="+u);
    }
    else {
        console.log("not attempting to log in via cookie");
    }

</script>
<?                
                    include("inc/login.php"); 
                    include("inc/language.php"); 
                }
				else {
					include("inc/personalmenu.php"); 
				}
			?>
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

    foreach (array("plain", "wordtraining", "callsigns", "text2cw", "download", "groups", "qtc", "mm") as $item) {
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
