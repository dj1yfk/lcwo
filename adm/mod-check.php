<?

include($_SERVER['DOCUMENT_ROOT']."/inc/definitions.php");
include($_SERVER['DOCUMENT_ROOT']."/inc/connectdb.php");

$hrs = 1;

# Finding new posts between $hrs and $hrs-1 
$query = mysqli_query($db,"select * from lcwo_posts where approved = 0 and 
		(time < (NOW() - INTERVAL ".(1+($hrs-1)*60)." MINUTE))
		and
		(time > (NOW() - INTERVAL ".($hrs*60)." MINUTE))");

$mailtext = "User \tTopic\n===========================================\n";
while ($d = mysqli_fetch_object($query)) {
		$count++;
	$mailtext .= uid2name($d->uid)."\t".$d->topic."\n";
}

if ($count > 0) {
	mail (ADMINMAIL, "New posts at LCWO for moderation", BASEURL."/moderation\n\n$mailtext",
			"From: LCWO Robot <".ADMINMAIL.">");
}

function uid2name ($uid) {
    global $db;
    $uid = intval($uid);
    $getuname = mysqli_query($db,"SELECT `username`  from lcwo_users where `ID`='$uid';");
    if (!$getuname) {
        return "unknown";
    }
    $xx = mysqli_fetch_object($getuname);
    return $xx->username;
}

function uid2email ($uid) {
    global $db;
    $uid = intval($uid);
    $getuname = mysqli_query($db,"SELECT `email`  from lcwo_users where `ID`='$uid';");
    if (!$getuname) {
        return false;
    }
    $xx = mysqli_fetch_object($getuname);
    return $xx->email;
}

function forumid2name ($id) {
    global $db;
    $uid = intval($id);
    $getname = mysqli_query($db,"SELECT `groupname`  from lcwo_usergroups where `GID`='$id';");
    if (!$getname) {
        return "unknown";
    }
    $xx = mysqli_fetch_row($getname);
    return $xx[0];
}

?>
