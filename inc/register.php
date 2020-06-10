<?

$valid = 0;
$error = "";
$spam = false;

if (preg_match('/g8tmv/i', $_POST['username'])) {
	exit;
}
if (preg_match('/tuckley/i', $_POST['name'])) {
	exit;
}



// Before anything, check for captcha if both username
// and name match (typical for spam)
if (
$_POST['username'] == $_POST['name'] &&
  is_untrusted_ip($_SERVER['REMOTE_ADDR'])) {
		if ($_SESSION['captcha'] != md5($_POST['captcha'].'asaltydog')) {
				echo "Error: Wrong captcha value or cookies disabled! Go <a href='/signup'>back and try again</a>.";
				
				if ($_POST['l']) {
				mail(ADMINMAIL, "LCWO: Signup-Spam (Captcha Fail)!", getenv('REMOTE_ADDR')."\n".print_r($_POST, true));
				}
				return;
		}
}

if (isset($_POST['l']) && isset($_POST['username']) && preg_match("/^[a-zA-Z0-9]{2,24}$/",
$_POST['username']) && isset($_POST['pw1']) && 	($_POST['pw1'] ==
$_POST['pw2']) && strlen($_POST['pw1']) > 0) {

	# Check for Spam users
	if (
		(($likely_spam == 1) && ($_POST['username'] == $_POST['name']))
		||
		(preg_match('/\r\n/', $_POST['email']))
	) {
			mail(ADMINMAIL, "LCWO: Signup-Spam!", getenv('REMOTE_ADDR')."\n".print_r($_POST, true));
			$spam = true;
	}
	
	if (in_array($_POST['lang'], $langs)) {
		$newlang = $_POST['lang'];
	}
	else {
		$newlang = 'en';
	}

	/* check if user name already exists */

	$gu = mysqli_query($db,"select username from lcwo_users where username='".$_POST['username']."';");

	$u = mysqli_fetch_object($gu);

	if ($u) {
		$error .= "Another user with this user name already exists.<br>";
		$valid = 0;
	}
	else {
		$valid = 1;
	}

	if (strlen($_POST['username']) < 2) {
		$error .= "Username too short. Minimum 2 characters.<br>";
		$valid = 0;
	}

	if (in_array($_POST['continent'], array_keys($serverlocations))) {
	    $continent = $_POST['continent'];
	}
	else {
		$error .= "Invalid continent <br>";
		$valid = 0;
	}

			
}
else {
	$valid = 0;
	if ($_POST['pw1'] != $_POST['pw2']) {
		$error .= "Passwords do not match.<br>";
	}

	if (!preg_match("/^[a-zA-Z0-9]{2,24}$/", $_POST['username'])) {
		$error .= "Illegal user name (too long or invalid characters. Only letters and numerals allowed).<br>";
	}

	if ($_POST['pw1'] == '') {
		$error .= "Password must not be empty.<br>";
	}
}


if ($valid) {

	$email = isset($_POST['email']) ?  mysqli_real_escape_string($db,stripslashes($_POST['email'])) : '';
	$name = isset($_POST['name']) ?  mysqli_real_escape_string($db,stripslashes($_POST['name'])) : '';
	$location= isset($_POST['location']) ?  mysqli_real_escape_string($db,stripslashes($_POST['location'])) : '';

	if (!$spam) {
	$add = mysqli_query($db,"INSERT INTO lcwo_users (`username`,
	`password`, `email`, `player`, `cw_tone`, `name`, `location`, `lang`, `continent`, `signupdate`, `consent`, `profileaboutme`) VALUES 
	('".$_POST['username']."', 'notyet', '$email', ".PL_DEFAULT.", 600, 
	'$name', '$location', '$newlang', '$continent', CURDATE(), 1, '');");
	}	

	loadlocale($newlang);

# logging for debugging purposes
#    $userip = getenv('REMOTE_ADDR');
#    $fh = fopen('register-log.txt','a');
#    if ($fh) {
#	        fputs($fh, $_POST[username]." $name $email $location ".date('l jS \of F Y h:i:s A')." - $userip\n");  fclose($fh);
#    }

	echo "<p>".$_SESSION['l']['thanksforsigningup']." (".$_POST['username'].")</p>";
	if ($add) {
		# Now make salted passord with id...
		$query = mysqli_query($db,"select id from lcwo_users where username='".$_POST['username']."'");
		$row = mysqli_fetch_row($query);
		$password = md5($row[0] . SALT . md5($_POST['pw1']));
		mysqli_query($db,"update lcwo_users set password='$password' ".
				" where id='".$row[0]."';");
	}
	else {
		echo "ERROR! ".mysqli_error($db);
	}

		
}
else {
	echo $_SESSION['l']['regfailed']." <br><br> $error";

	$_SESSION['signup']['username'] = $_POST['username'];
	$_SESSION['signup']['name'] = $_POST['name'];
	$_SESSION['signup']['email'] = $_POST['email'];
	$_SESSION['signup']['location'] = $_POST['location'];
	
	echo '<br> Please <a href="/signup">go back to the sign up
	page</a> and try again. Thanks. ';
}

?>

<div class="vcsid">$Id: register.php 248 2014-06-15 20:47:20Z dj1yfk $</div>

