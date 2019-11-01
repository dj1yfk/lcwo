<?
include($_SERVER['DOCUMENT_ROOT']."/inc/connectdb.php");
include($_SERVER['DOCUMENT_ROOT']."/inc/functions.php");

$uid = $_GET['u'];
$hash= $_GET['h'];

if (!preg_match("/^[0-9]{1,6}$/", $uid)) {
		echo "Bad user.";
		return;
}

if (!preg_match("/^[a-zA-Z0-9]{32,32}$/", $hash)) {
		echo "Bad hash.";
		return;
}

$query = mysqli_query($db,"select password from lcwo_users where id='$uid'");

$o = mysqli_fetch_object($query);

if (md5($o->password) == $hash) {
		delete_user($uid);
		return;
}	
else {
		echo "Wrong hash!";
		return;
}
?>
