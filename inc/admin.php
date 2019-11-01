<?
if ($_SESSION['uid'] != ADMIN) {
	echo "No :-)<br>";
	return;
}

switch($_POST['type']) {
	case 'newpass':
		newpassword();
		break;
	case 'deleteuser':
		deleteuser();
		break;
	case 'passlink':
		passlink();
		break;
}

?>
<h1>Admin Interface</h1>

<h2>New Password</h2>
<form action="/admin" method="POST">
User: <input size="12" name="user" value=""> &mdash;
New PW: <input size="12" name="pw" value=""> &mdash;
<input type="hidden" name="type" value="newpass">
<input type="submit">
</form>

<h2>Link for new Password</h2>
<form action="/admin" method="POST">
User: <input size="12" name="user" value="">
<input type="hidden" name="type" value="passlink">
<input type="submit">
</form>

<h2>Delete User</h2>
<form action="/admin" method="POST">
User ID: <input size="5" name="user" value="">
<input type="hidden" name="type" value="deleteuser">
<input type="submit">
</form>

<?


function newpassword () {
	global $db;
	$pass = esc($_POST['pw']);
	$user = esc($_POST['user']);
	
	$query = mysqli_query($db,"select id from lcwo_users where username=\"$user\"");

	if (!$k = mysqli_fetch_object($query)) {
			echo "User not found!<br>";
			return;
	}

	$pass = md5($k->id . SALT . md5($pass));

	$query = mysqli_query($db,"update lcwo_users set password=\"$pass\" where username=\"$user\";");

	if ($query) {
		echo "PW $user / $pass => OK<br>";
	}
	else {
		echo "Error: ".mysqli_error($db);
	}
}


function deleteuser () {
	global $db;
	$id = $_POST['user']+0;
	if (!is_int($id) or $id < 2) {
		echo "No! (>$id< == not integer)";
		return;
	}

	echo "User: $id<br><br>";

	delete_user($id);
}

function passlink () {
	global $db;
	$user = esc($_POST['user']);
	
	$query = mysqli_query($db,"select md5(password) as pp from lcwo_users where username=\"$user\"");

	if (!$k = mysqli_fetch_object($query)) {
			echo "User not found!<br>";
			return;
	}

	if ($query) {
		echo "Here's a link to set a new password: https://lcwo.net/lostpassword/".$k->pp."/".$user."\n";
	}
	else {
		echo "Error: ".mysqli_error($db);
	}
}
?>


<div class="vcsid">$Id: admin.php 188 2012-09-30 20:48:22Z dj1yfk $</div>

