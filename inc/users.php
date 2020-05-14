<?

# users per page
$upp = 1000;

if ($_SESSION['user_order'] && $_SESSION['user_order_dir']) {
	$order = $_SESSION['user_order'];
	$orderdir = $_SESSION['user_order_dir'];
}
else {
	$order = 'id';
	$_SESSION['user_order'] = 'id';
	$_SESSION['user_order_dir'] = 'asc';
}


if (is_numeric($_GET['l']) && ($_GET['l'] >= 0)) {
	$limit = $_GET['l'];
}
else {
	$limit = 0;
}

if (in_array($_GET['o'], array('id', 'username', 'name', 'location'))) {
	$order = $_GET['o'];
	$_SESSION['user_order'] = $_GET['o'];
}

if (in_array($_GET['d'], array('asc', 'desc'))) {
	$orderdir = $_GET['d'];
	$_SESSION['user_order_dir'] = $_GET['d'];
}

/* Only show members of a group? */
if (isint($_GET['member'])) {
	$m1 = mysqli_query($db,"SELECT groupname from lcwo_usergroups where gid='".$_GET['member']."'");
	if (!($m2 = mysqli_fetch_object($m1))) {
		echo "Error: Invalid user group!";
		return;
	}
	$groupname = $m2->groupname;
	/* get member IDs */
	$m3 = mysqli_query($db,"SELECT member from lcwo_groupmembers where gid='".$_GET['member']."';");
	$validmembers = "where id in (";
	while ($k = mysqli_fetch_row($m3)) {
		$validmembers .= "$k[0], ";
	}
	$validmembers .= "0)";
}
else {
	$validmembers = "";
}




$ct = mysqli_query($db,"select count(*) as c from lcwo_users
$validmembers;");
if (!$ct) {
		echo "Error!".mysqli_error();
		return; 
}

$count = mysqli_fetch_object($ct);
$count = $count->c;

$us = mysqli_query($db,"select `id`, `username`, `name`, `location`, `signupdate`
				from lcwo_users $validmembers order by `$order` $orderdir limit $limit, $upp");

if (!$us) {
	echo "Failed to get user list.".mysqli_error($db);
	return;
}

if ($_GET['member']) {
	echo "<h1>".l('membersof').": ".$groupname."</h1>\n";
	echo "<p><a href=\"/usergroups/".$_GET['member']."\">".l('grouppage').": $groupname</a></p>";
}
else {
	echo "<h1>LCWO ".l('userlist')."</h1>\n";	
}


	echo '<div align="center">';
	echo '<table width="85%"><tr><th width="10%">ID'.sortico('id', $orderdir).'</th><th
	width="25%">'. l('username').sortico('username', $orderdir).'</th><th
	width="25%">'.l('name').sortico('name', $orderdir).'</th><th
	width="25%">'.l('location').sortico('location', $orderdir).'</th><th width="15%">'.l('signedup')."</th></tr>\n";
	while ($u = mysqli_fetch_object($us)) {
			if ($u->username != "[deleted]") {
				echo "<tr><td>$u->id</td><td><a href=\"/profile/$u->username\">$u->username</a></td><td>$u->name</td><td>$u->location</td> <td>$u->signupdate</td></tr>\n";
			}
			else {
				echo "<tr><td>$u->id</td><td>$u->username</td> <td>$u->name</td><td>$u->location</td><td></td></tr>\n";
			}
	}
	echo "</table>\n";
	echo "</div>\n";

	/* Show Links to all pages of the list */
	echo "<hr noshade width=75%>\n";
	echo "<div align=\"center\">";
	for ($i=0; $i < $count; $i+=$upp) {
		if ($i == $limit) {
			echo "<strong><a href='/users/$i'>".($i+1)."+</a></strong> ";
		}
		else {
			echo "<a href='/users/$i'>".($i+1)."+</a> ";
		}
	}
	echo "</div>\n";
	
		


function sortico($i, $d) {
		
	if (isint($_GET['l'])) {
		$li = $_GET['l'];
	}
	else {
		$li = 0;
	}
	if ($_SESSION['user_order'] == $i) {
		if ($_SESSION['user_order_dir'] == 'desc') {
			return 
			'&nbsp;<a rel="nofollow" href="/users/'.$li.'/'.$i.'/asc"><img border=0 src="/pics/sortg.png" ALT="[N]"></a>'.
			'<img src="/pics/sortu.png" ALT="[S]">';
		}
		else {
			return 
			'&nbsp;<img src="/pics/sort.png" ALT="[S]">'.
			'<a rel="nofollow" href="/users/'.$li.'/'.$i.'/desc"><img border=0 src="/pics/sortug.png" ALT="[N]"></a>';
		}
	}
	else {
		return 
				"&nbsp; <a rel='nofollow' href=\"/users/$li/$i/asc\">".
				'<img border=0 src="/pics/sortg.png" ALT="[N]"></a>'.
				"<a rel='nofollow' href=\"/users/$li/$i/desc\">".
				'<img border=0 src="/pics/sortug.png" ALT="[S]"></a>';
	}
}


?>
<div class="vcsid">$Id: users.php 209 2013-01-02 11:36:20Z dj1yfk $</div>

