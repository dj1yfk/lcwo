<?

if ($_SESSION['uid']) {
		session_destroy();
}

$valid = 1;
$error = "";

$username = $_POST['username'];
$password = $_POST['password'];

if (!preg_match("/^[a-zA-Z0-9]{1,24}$/", $username)) {
	$valid = 0;
	$error .= "User name contains invalid characters. Only
	letters and numerals allowed.<br>\n";
}
else {
	$getuser = mysqli_query($db,"SELECT id, username, location, name, email,
			password, cw_speed, cw_eff, cw_tone, koch_lesson,
			player, lang, vvv, lockspeeds, groups_duration,
			groups_abbrev, continent,
			koch_duration, koch_randomlength,
			groups_randomlength, customcharacters,
			cw_tone_random, groups_mode, show_ministat,
			forum_whitelist, delay_start, consent, hide
		   	from lcwo_users where username='$username'");

	if ($user = mysqli_fetch_object($getuser)) {
		if ($user->password == md5($user->id.SALT.md5($password))) {
			$valid = 1;
		}
		else {
			$valid = 0;
			$error .= "Password doesn't match.<br>\n";
		}

	}
	else {
		$valid = 0;
		$error .= "User $user not found in database.<br>\n";
	}
}


if ($valid) {
		echo '<a href="/">'.l('loginsuccessful').'</a>';
	$_SESSION['username'] = $user->username;
	$_SESSION['location'] = $user->location;
	$_SESSION['uid'] = $user->id;
	$_SESSION['email'] = $user->email;
	$_SESSION['name'] = $user->name;
	$_SESSION['location'] = $user->location;
	$_SESSION['cw_speed'] = $user->cw_speed;
	$_SESSION['cw_eff'] = $user->cw_eff;
	$_SESSION['cw_tone'] = $user->cw_tone;
	$_SESSION['cw_tone_random'] = $user->cw_tone_random;
	$_SESSION['koch_lesson'] = $user->koch_lesson;
	$_SESSION['koch_duration'] = $user->koch_duration;
	$_SESSION['koch_randomlength'] = $user->koch_randomlength;
	$_SESSION['groups_mode'] = $user->groups_mode;
	$_SESSION['groups_abbrev'] = $user->groups_abbrev;
	$_SESSION['groups_duration'] = $user->groups_duration;
	$_SESSION['groups_randomlength'] = $user->groups_randomlength;
	$_SESSION['player'] = $user->player;
	unset($_SESSION['lang']);
	$_SESSION['lang'] = $user->lang;
	$_SESSION['continent'] = $user->continent;
	$_SESSION['vvv'] = $user->vvv;
	$_SESSION['lockspeeds'] = $user->lockspeeds;
	$_SESSION['customcharacters'] = $user->customcharacters;
	$_SESSION['show_ministat'] = $user->show_ministat;
	$_SESSION['forum_whitelist'] = $user->forum_whitelist;
	$_SESSION['delay_start'] = $user->delay_start;
	$_SESSION['debug'] = 0;
	$_SESSION['consent'] = $user->consent;
	$_SESSION['hide'] = $user->hide;

	loadlocale($_SESSION['lang']);
	
	mysqli_query($db,"delete from lcwo_online where `UID`='".$_SESSION['uid']."'");
        mysqli_query($db,"INSERT INTO lcwo_online (UID, LASTACTIVE) VALUES
			(".$_SESSION['uid'].", NULL)");

#	$userip = getenv('REMOTE_ADDR');
#	$fh = fopen('loginz.txt','a');
#	if ($fh) {
#		fputs($fh, "$_SESSION[username] ".date('l jS \of F Y h:i:s A')." - $userip\n");
#		fclose($fh);
#	}


    # load saved SESSION data which is in lcwo_userprefs table

    $q = mysqli_query($db, "select prefs from lcwo_userprefs where `uid`='".$_SESSION['uid']."'");
    if ($a = mysqli_fetch_row($q)) {
        $us = unserialize($a[0]);
        if ($us != FALSE) {
            $_SESSION = array_merge($_SESSION, $us);
            echo "<!-- merged arrays -->";
        }
    }


}
else {
	echo "Sorry, login failed: <br> $error\n";
}
?>

<div class="vcsid">$Id: dologin.php 248 2014-06-15 20:47:20Z dj1yfk $</div>

