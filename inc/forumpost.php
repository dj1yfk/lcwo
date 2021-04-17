<?
if (!$_SESSION['uid']) {
echo "Sorry, you must be logged in to use this function.";
return 0;
}

if (FORUM_RO) {
    echo "Forum is read-only.";
    return 0;
}

/* Check if we were whitelisted or banned in the meantime... */
$q = mysqli_query($db,"select forum_whitelist from lcwo_users where id=".$_SESSION['uid']);
$o = mysqli_fetch_object($q);
$_SESSION['forum_whitelist'] = $o->forum_whitelist;

if ($_SESSION['forum_whitelist'] > time()) {    # banned => return
    echo "User is banned until ".strftime("%Y-%b-%d %H:%M", $_SESSION['forum_whitelist']);
    return;
}

if ($_POST['pid']) {		/* POST ID? If so, edit the post... */
		if ($_SESSION['forum_whitelist'] == "1") {
			editpost();
		}
		return;
}

/*
	XXX this should also be in functions...

*/

/* Check if $forumid is set (i.e. user group forum) and if
* so, if the user is allowed to post there */

if (!isint($_POST['forumid'])) {
	echo "Error: Invalid forum ID.\n";
	return;
}

/* Restrict posting to private groups to members */
	
if (($_POST['forumid'] > 0) and (is_private_group($_POST['forumid']))) {
	$checkquery = mysqli_query($db,"select member from
	lcwo_groupmembers where gid='".$_POST['forumid']."' and
	member='".$_SESSION['uid']."'");

	if (!mysqli_fetch_row($checkquery)) {
		echo "Error: Trying to post in a forum of an user
		group you are not a member of.\n";
		return;
	}
}
	

if ($_POST['forumid']) {
	$gotourl = "/usergroups/$_POST[forumid]";
}
else {
	$gotourl = "/forum";
}


# badwords are defined in inc/definitions.php
foreach ($badwords as $bw) {
	if (preg_match("/$bw/", $_POST['text'])) {
		mail (ADMINMAIL, "Spam detected at ".HOSTNAME, 
		$_POST['text']."\n\nFrom:".getenv("REMOTE_ADDR")."\n\n"); 
		return 0;
	}
}

# New Thread

if (($_POST["new"] == 1) && $_POST['text'] && $_POST['title']) {
	
	/* Find last Thread ID */
		
	$gt = mysqli_query($db,"SELECT DISTINCT `tid` from `lcwo_posts` order by `tid` desc");
	$g = mysqli_fetch_object($gt);

	$newtid = ($g->tid + 1);

	$text = esc($_POST['text']);
	$text = strip_tags($text);	
	$topic = strip_tags(esc($_POST['title']));	

	/* too long? */
	if (strlen($topic) > 1000) {
		$topic = substr($topic, 0, 999);
		$topic = preg_replace("/'$/", "", $topic);
	}
	if (strlen($text) > 62000) {
		$text = substr($text, 0, 62000);
		$text = preg_replace("/'$/", "", $text);
	}

	/* too short? */

	if (!strlen($topic) or !strlen($text)) {
		echo "<p>Error: empty post?</p>";
		return;
	}

    $approved = ($_SESSION['forum_whitelist'] ==  "1") ? 1 : 0;

	$ins = mysqli_query ($db, "INSERT INTO `lcwo_posts` (`tid`,
		`isreply`, `uid`, `topic`, `time`, `text`, `forumid`, `ip`, `approved`) VALUES (
		'$newtid', '0', '".$_SESSION['uid']."', '$topic', NULL, 
		'$text', '".$_POST['forumid']."', '".getenv('REMOTE_ADDR')."', $approved);");

		if (!$ins) {
			echo "<p>Error. Sorry, something went wrong here.</p>".mysqli_error($db);
		}
		else {
			if ($_SESSION['forum_whitelist'] == 1) {
					echo "<p>OK</p>";
			}
			else {
					echo "<p>OK. ".l('postawaitingapproval')." </p>";
			}
			echo "<p><a href=\"/forum/$newtid\">".l('backtothread')
			."</a></p><p><a href=\"$gotourl\">".l('backtoforum')."</a></p>\n";
		}

	
	
		
return 0;
}

# INSERT POST
if (is_numeric($_POST['tid']) && $_POST['text']) {
	$tid = intval($_POST['tid']);
	
	$gt = mysqli_query($db,"SELECT `topic` from `lcwo_posts` where `tid`='$tid'");
	$t = mysqli_fetch_array($gt);

	if (!$t[0]) {
?>
	<h1>Error. Thread does not exist.</h1>
	<p><a href="/forum"><? echo l('backtoforum') ?></a></p>
<?
	}
	else {

		$text = esc($_POST[text]);	
		$text = strip_tags($text);	

		if (strlen($text) > 62000) {
			$text = substr($text, 0, 62000);
			$text = preg_replace("/'$/", "", $text);
		}

		if (!strlen($text)) {
			echo "<p>Error: No text.</p>";
			return;
		}
	
		$top = mysqli_real_escape_string($db,$t[0]);

        $approved = ($_SESSION['forum_whitelist'] ==  "1") ? 1 : 0;

		$ins = mysqli_query ($db, "INSERT INTO `lcwo_posts` (`tid`,
		`isreply`, `uid`, `topic`, `time`, `text`,`forumid`, `ip`, `approved`)
			   	VALUES (
		'$tid', '1', '".$_SESSION['uid']."', '$top', NULL, '$text',
		'".$_POST['forumid']."', '".getenv('REMOTE_ADDR')."', $approved);");

		if (!$ins) {
			echo "<p>Error. Sorry, something went wrong here.</p>".mysqli_error($db);
		}
		else {
			if ($appr || ($_SESSION['forum_whitelist'] == 1)) {
					echo "<p>OK</p>";
			}
			else {
					echo "<p>OK.  ".l('postawaitingapproval')." </p>";
			}
			echo "<p><a href=\"/forum/$tid\">".l('backtothread')."</a></p><p><a href=\"$gotourl\">".l('backtoforum')."</a></p>\n";
		}
	}
}
else {
?>
<h1>Error</h1>
<p>Looks like you didn't enter a text/title or try to post in a thread
that doesn't exist.</p>
<p><a href="/forum"><? echo l('backtoforum'); ?></a></p>
<?
}


function editpost () {
	global $db;
	if (!isint($_POST['pid'])) {
		echo "Error. Post ID not valid.";
		return;
	}

	$id = $_POST['pid'];

    $query = mysqli_query($db,"SELECT `uid` from `lcwo_posts` where `id`='$id'");

    if (!$query) {
        echo "Error! ".mysqli_error($db);
        return;
    }

    $post = mysqli_fetch_object($query);

    if (!$post) {
        echo "Invalid post ID.";
        return;
    }

    if ($post->uid != $_SESSION['uid']) {
        echo "Error: This is not your post.\n";
        return;

    }

	/* if we are here, all is OK! */

	$text = esc($_POST['text']);
	if ($_SESSION['uid'] != 1) {
		$text = strip_tags($text);
	}

	if (!strlen($text)) {
		echo "<p>Error: Empty post?</p>";
		return;
	}

	
	$query = mysqli_query($db,"UPDATE lcwo_posts set text='$text',
	time=time, `ip`='".getenv('REMOTE_ADDR')."' where id='$id';");

	if (!$query) {
		echo "Error. Edit failed!\n".mysqli_error($db);
		return;
	}
	
	echo "<p>OK</p>\n<p><a
	href=\"/forum/$_POST[tid]\">".l('backtothread')."</a></p>";
	
}
?>
