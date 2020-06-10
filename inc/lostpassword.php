<?

# get a new password
if ($_POST['user'] && $_POST['hash']) {
	set_new_password();
}
else if ($_POST['username']) {
	request_mail();
}
else if ($_GET['h'] && $_GET['u']) {
	ask_new_password();
}
else {
	default_lostpassword();
}

function set_new_password() {
global $db;

	if (preg_match("/^[a-zA-Z0-9]{2,24}$/", $_POST['user']) &&
			preg_match("/^[a-zA-Z0-9]{32,32}$/", $_POST['hash']) &&
			($_POST['pw1'] == $_POST['pw2']) &&
			strlen($_POST['pw1'])) {

		# Again verify this identity....
		$getpw = mysqli_query($db,"SELECT `password`, `id` from `lcwo_users` where
							`username` = '".$_POST['user']."'");
		$p = mysqli_fetch_object($getpw);

		if (md5($p->password) == $_POST['hash']) {
				# OK!

				$pw = md5($p->id.SALT.md5(esc($_POST['pw1'])));
				
				$np = mysqli_query($db,"UPDATE `lcwo_users` set
				`password`='$pw' where
				`username`='$_POST[user]'");

				if (!$np) {
					echo "<p>Error: Update failed. Contact
					administrator.</p>";
				}
				else {
					echo "<p>".l('passwordchanged')."</P>";
				}

		}
		else {
			echo "<p>Error: Invalid password hash.</p>";
		}

			
					
	}
	else {
		echo "<p>Error: Illegal user name, password hash, or empty password!</p>";
	}
return 0;
}


###############################################################################
# got an username -> send email to him
function request_mail () {
global $db;

if (!preg_match("/^[a-zA-Z0-9]{1,24}$/", $_POST['username'])) {
	echo "<p>Error: The username (".$_POST['username'].") contains invalid characters.</p>";
	return 0;
}

	# Check abuse
	$ip = getenv("REMOTE_ADDR");

	$query =mysqli_query($db,"delete from lcwo_pwrequests where `date`!=CURDATE()");
	$query =mysqli_query($db,"insert into lcwo_pwrequests (`ip`, `date`, `username`) values ('$ip', CURDATE(), '".$_POST['username']."');");
	$query =mysqli_query($db,"select count(*) from lcwo_pwrequests where ip='$ip';");
	$a = mysqli_fetch_row($query);
	if ($a[0] > 4) {
		echo "Too many requests (limited to 5 per day)!<br>Contact ".ADMINMAIL;
		return 0;
	}


$getemail = mysqli_query($db,"SELECT `email`, `password` from `lcwo_users` where `username` = '".$_POST['username']."'");

$e = mysqli_fetch_object($getemail);

if (preg_match("/@/", $e->email)) {
	$hash = md5($e->password);		# hash of the password hash	

	$ip = getenv("REMOTE_ADDR");
	$ip = preg_replace('/\d+$/', '?', $ip);

	$subject = "Lost password at lcwo.net";
	$mailtext = "Hello,\n
someone, probably you (IP: $ip) requested a new
password for https://lcwo.net/.

To set a new password, please visit the following link:

".BASEURL."/lostpassword/$hash/".$_POST['username']."

If you didn't request a new password yourself, please
disregard this message.

Best Regards,
LCWO.net Password Robot

-- 
Responsible for this mail:\n".MAILSIGNATURE." ";

$msgid = "Message-Id: <".time()."-".md5($_POST['username'])."@msgid.lcwo.net>";

mail($e->email, $subject, $mailtext, "From: LCWO Robot <".ADMINMAIL.">\r\nBcc: ".ADMINMAIL."\r\n$msgid", "-f".ADMINMAIL);

echo "<p>".l('lostpassemailsent')."</p>";
echo "<p>Note: Some providers, like AT&T and Verizon are known to reject
		mails from lcwo.net - if you do not receive a mail within a few
		minutes, please check your spam/junk mail folder. If you don't
        find a message there, please contact the administrator by mail.</p>";
echo "<p>Please specify your user name in your email. Thanks!</p>";
echo "<p>Admin: <a href='mailto:".ADMINMAIL."'>".ADMINMAIL."</a></p>";
		
}
else {
	echo "<p>Error: ".l('noemailindb')."</p>
	<p>Send an email to <a href=\"mailto:".ADMINMAIL."\">the
admin (".ADMINMAIL.")</a> and ask for a new password.</p>";
echo "<p>Please specify your user name in your email. Thanks!</p>";
}
		
return 0;
}



###############################################################################
# user clicked link in mail, can enter new PW if hash/username right
function ask_new_password() {
global $db;

if (!preg_match("/^[a-zA-Z0-9]{1,24}$/", $_GET['u'])) {
	echo "<p>Error: The username (".$_GET['u'].") contains
	invalid characters.</p>";
	return 0;
}

if (!preg_match("/^[a-zA-Z0-9]{32,32}$/", $_GET['h'])) {
	echo "<p>Error: The password hash is invalid!</p>";
	return 0;
}

$getpw = mysqli_query($db,"SELECT `password` from `lcwo_users` where `username` = '".$_GET['u']."'");

$p = mysqli_fetch_object($getpw);

if (md5($p->password) == $_GET['h']) {
?>
<h1><? echo l('newpassword') ?></h1>
<p>Hello <? echo $_GET['u']; ?>!<br> 
<? echo l('enternewpassword'); ?>
</p>

<form action="/lostpassword" method="POST">
<table>
<tr>
<td><? echo l('password') ?>:</td>
<td><input type="password" size="20" name="pw1"></td>
</tr>
<tr>
<td><? echo l('cfmpassword') ?>:</td>
<td><input type="password" size="20" name="pw2"></td>
</tr>
</table>
<input type="submit" value=" Submit new password ">
<input type="hidden" name="hash" value="<? echo $_GET['h']; ?>">
<input type="hidden" name="user" value="<? echo $_GET['u']; ?>">
</form>
<?
}
else {
	echo "<p>Error: The password hash is invalid.</p>";
}

return 0;
}

function default_lostpassword () {
global $db;
?>
<h1><? echo l('forgotpassword') ?></h1>
<p>
<? echo l('forgotpassword2') ?>
</p>
<form action="/lostpassword" method="POST">
<? echo l('username') ?>: 
<input type="text" name="username" size="12">
<input type="submit" value="<?=l('submit',1)?>">
</form>
<p>
<? echo l('forgotpassword3') ?>
<a href="mailto:<?=ADMINMAIL;?>"><?=ADMINMAIL;?></a></p>
<?
}
?>
