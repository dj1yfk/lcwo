<?
include("../inc/connectdb.php");

$verbose = false;

# Finding new posts in user groups of last 6 hours
$query = mysqli_query($db,"select * from lcwo_posts where forumid > 0 and 
		time > (NOW() - INTERVAL 360 MINUTE)");

while ($d = mysqli_fetch_array($query)) {
	if (!$msg[$d[forumid]]) {
		$msg[$d[forumid]] = "\n".forumid2name($d[forumid])."\n";
	}
		$msg[$d[forumid]] .= uid2name($d[uid])." (".$d["time"]."): ".$d[topic]." - http://lcwo.net/forum/".$d[tid]."\n";
}


$query = mysqli_query($db,"select gid, member from lcwo_groupsubscribe;");
while ($d = mysqli_fetch_array($query)) {
	if (!$mem[$d[member]]) {
		$mem[$d[member]] = array();
	}
	array_push($mem[$d[member]], $d[gid]);
}

?><pre><?

foreach ($mem as $mid => $groups) {
	# mail to $mid
	$mailtext = "Hello,\n\nnew messages were posted in your subscribed groups at http://lcwo.net/\n";
	$x = 0;
	foreach ($groups as $gr) {
			if ($msg[$gr]) {
				$x++;
				$mailtext .= $msg[$gr];
			}
	}
	$mailtext .= "\nIn order to change your subscription settings, please\n".
		"visit http://lcwo.net/usergroups/subscribe\n\n-- \nhttp://lcwo.net/impressum\n";

	if ($x > 0) {
			$ad = uid2email($mid);
			if ($ad != false) {
					if ($verbose == true) {
						echo "$ad\n";
						echo $mailtext; 
					}
				mail ($ad, "New posts at LCWO!", $mailtext, 
						"From: LCWO Robot <help@lcwo.net>", "-fhelp@lcwo.net");

			}
	}


}


?></pre><?

function uid2name ($uid) {
    global $db;
    $uid = intval($uid);
    $getuname = mysqli_query($db,"SELECT `username`  from
    lcwo_users where `ID`='$uid';");
    if (!$getuname) {
        return "unknown";
    }
    $xx = mysqli_fetch_object($getuname);
    return $xx->username;
}

function uid2email ($uid) {
    global $db;
    $uid = intval($uid);
    $getuname = mysqli_query($db,"SELECT `email`  from
    lcwo_users where `ID`='$uid';");
    if (!$getuname) {
        return false;
    }
    $xx = mysqli_fetch_object($getuname);
    return $xx->email;
}

function forumid2name ($id) {
    global $db;
    $uid = intval($id);
    $getname = mysqli_query($db,"SELECT `groupname`  from
    lcwo_usergroups where `GID`='$id';");
    if (!$getname) {
        return "unknown";
    }
    $xx = mysqli_fetch_row($getname);
    return $xx[0];
}








?>
