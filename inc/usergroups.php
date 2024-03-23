
<?
if ($_GET["join"]) {
	joingroup();
}
else if ($_GET['uploadpic']) {
	if ($_GET['uploadpic'] == 1) {
		uploaddialog();
	}
	else if ($_GET['uploadpic'] == 2) {
		uploadimage("group");
	}
}
else if ($_GET["new"]) {
	if ($_GET["new"] == 1) {
		newgroup();
	}
}
else if ($_GET["approve"]) {
	approvemember();
}
else if ($_GET["edit"] == 1) {
	editgroup();
}
else if ($_GET["edit"] == 2) {
	savegroupchanges();
}
else if ($_GET["group"]) {
	grouppage();
}
else if ($_GET["leave"]) {
	leavegroup();
}
else if ($_GET["subscribe"]) {
	subscribe();
}
else if ($_GET['map']) {
    showmap();
}
else {
	groupoverview();
}

?>



<?

/* 
 * showmap. shows all user groups on the map as markers.
 * if GET gid is set, this also allows setting a new position
 * for this group.
 */

function showmap () {

if ($_GET['gid']) {
    echo "<h2>".l('usergroups').": ".get_group_name($_GET['gid'])."</h2>";
    if (is_admin_of($_SESSION['uid'], $_GET['gid'])) {
        echo "<p>Click on the map to set the location for this group. <a href='/usergroups/".$_GET['gid']."'>Back to group</a></p>";
    }
}
else {
    echo "<h2>".l('usergroups')."</h2>";
}

?>
<script src="/js/leaflet.js"></script>
<style>
#lcwomap { height: 550px; }
</style>
<div id="lcwomap"></div>

<script>
    var mymap = L.map('lcwomap').setView([30, 0], 2);
    var osmUrl='<? echo TILES_URL ?>';
    var osmAttrib='<? echo TILES_ATTRIB ?>';
    var osm = new L.TileLayer(osmUrl, {minZoom: 1, maxZoom: 12, attribution: osmAttrib});

    mymap.addLayer(osm);

<?
if (is_admin_of($_SESSION['uid'], $_GET['gid'])) {
?>
    mymap.on('click', onMapClick);
<?
}
?>


/* Query all user group locations and draw them on the map */

var request =  new XMLHttpRequest();
request.open("GET", '/api/index.php?action=get_usergroups', true);
request.onreadystatechange = function() {
	var done = 4, ok = 200;
	if (request.readyState == done && request.status == ok) {
		if (request.responseText) {
			var p = JSON.parse(request.responseText);
            console.log(p);
            for (var i = 0; i < p.length; i++) {
                if (p[i].lat != 0 && p[i].lon != 0) {
                    var marker = L.marker([p[i].lat, p[i].lon]).addTo(mymap);
                    marker.bindPopup("<a href='/usergroups/" + p[i].gid + "'><b>" + p[i].groupname + "</b><br>" + p[i].groupdescription + "</a>");
                }
            }
		}
	};
}
request.send();

var popup = L.popup();
function  onMapClick(e) {
    var ll = e.latlng;

    var gid = <? echo ($_GET['gid']) ? $_GET['gid'] : 0;?>;
    var lat = ll.lat;
    var lon = ll.lng;

    popup.setLatLng(ll).setContent("You moved the marker for this user group here.").openOn(mymap);

    var request =  new XMLHttpRequest();
    request.open("GET", '/api/index.php?action=set_usergroup_location&gid=' + gid + "&lat=" + lat + "&lon=" + lon, true);
    request.onreadystatechange = function() {
        var done = 4, ok = 200;
        if (request.readyState == done && request.status == ok) {
            if (request.responseText) {
                var p = JSON.parse(request.responseText);
                if (p.msg != "ok") {
                    alert(p.msg);
                }
            }
        };
    }
    request.send();
}

</script>







<?
}

/*
	Groupoverview. Shows all available groups in a list.
*/

function groupoverview() {
global $db;
global $langnames;
global $langs;
		
?>
<h1><? echo l('usergroups'); ?></h1>

<p><? echo l('usergroupsintro'); ?></p>

<p>See all groups (which have a location set) on the <a href="/usergroups/map">LCWO user groups world map</a>.</p>

<table width="95%">
<tr>
<th><? echo l('usergroupname'); ?></th><th><? echo
l('usergroupdescription'); ?></th><th><? echo l('usergroupmembers');
?></th><th><? echo l('language') ?></th><th><? echo
l('usergrouptype') ?></th>
</tr>
<?
	$getgroups = mysqli_query($db,"SELECT gid, groupname, groupdescription,
	lang, private from lcwo_usergroups order by gid;");

	while ($g = mysqli_fetch_row($getgroups)) {

			$getnr = mysqli_query($db,"SELECT count(*) from
			lcwo_groupmembers where gid=".$g[0]."");
			$mct = mysqli_fetch_row($getnr);

			if ($g[4]) { $g[4] = l("private"); }
			else { $g[4] = l("public"); }
			
			echo "<tr><td><a href=\"/usergroups/$g[0]\">$g[1]</a>".
			"</td><td>$g[2]</td><td>$mct[0]</td><td>".$langnames[$g[3]]."</td>".
			"<td>$g[4]</td></tr>\n";	
	}

	echo "</table>\n";

// No new groups for the test user or lids who are not logged in.
if (!$_SESSION['uid'] or ($_SESSION['uid'] == TESTUSER)) {
	return;
}
	
?>

<h2><? echo l('newusergroup'); ?></h2>
<p><? echo l('newusergroupintro'); ?></p>

<form action="/usergroups/new" method="POST">
	<table>
		<tr>
		<td><? echo l('usergroupname'); ?>: </td> <td><input type="text" size="48" name="groupname"></td>
		</tr>
		<tr>
		<td><? echo l('usergroupdescription'); ?>: </td> <td><input type="text" size="48" name="groupdescription"></td>
		</tr>
		<tr>
		<td><? echo l('language'); ?>: </td> <td>
		<select name="lang" size="1">
   	<?  foreach ($langs as $lang) {
			if ($lang == $_SESSION['lang']) {
				echo "<option value=\"$lang\" selected>$lang - ".$langnames[$lang]."</option>";
			}
			else {
				echo "<option value=\"$lang\">$lang - ".$langnames[$lang]."</option>";
			}
		}
?>
	</select>
		</td>
		</tr>

		<tr>
		<td><? echo l('usergrouptype'); ?>: </td> <td>
		<select name="private" size="1">
			<option value="0" selected><? echo l("public"); ?></option>
			<option value="1"><? echo l("private"); ?></option>
		</select>
		</td>
		</tr>
		<tr>
		<td colspan=2>
			<input type="submit" value="<? echo l('submit',1); ?>">
		</td>
	</table>
	
</form>




<?
} // function groupoverview

function grouppage () {
	global $db;
	global $langnames;
		
	if (!isint($_GET['group'])) {
		echo "Error: Invalid group.";
		return;
	}

	$gg = mysqli_query($db,"SELECT gid, groupname, groupdescription,
	founder, lang, private from lcwo_usergroups where
	gid='".$_GET['group']."';");
	
	if (!$gg) {
		echo "Error: Group not found!";
		return;
	}
	
	$group = mysqli_fetch_object($gg);
	if (!$group) {
		echo "Error: Group not found!";
		return;
	}
	
	$founder = uid2uname($group->founder);
	$getnr = mysqli_query($db,"SELECT count(*) from lcwo_groupmembers where gid='".$_GET['group']."'");
	$mct = mysqli_fetch_row($getnr);


	/* if this group is private, check if the user is allowed to
	* see this group page. */

	if ($group->private) {
		$query = mysqli_query($db,"SELECT member from lcwo_groupmembers where gid='$group->gid' and member='".$_SESSION['uid']."'");

		if (!$query) {
			echo "Error: ".mysqli_error();
			return;
		}

		if (!mysqli_fetch_row($query)) {	/* No member! */
			$notmember = 1;
		}
		if ($_SESSION['uid'] == ADMIN) {
			$notmember = 0;
		}
	}
	
	
	echo "<h1>".l('usergroups').": $group->groupname</h1>";

	if ($notmember) {
		echo "<p>".l('privategroup')."</p>";

		if ($_SESSION['uid']) {
			echo "<p><a href=\"/usergroups/join/$group->gid\">".l('joinprivategroup')."</a></p>";
		}
		return;
	}

	
?>	
	<table width="80%">
	<tr>
		<td valign="top" width="140">
			<?
			if (file_exists("img/groupimage".$group->gid.".jpg")) {
			?>
			<img src="/img/groupimage<? echo $group->gid ?>.jpg" alt="Group Logo">
			<?
			}
			else {
			?>
			<img src="/img/groupimage0.png" alt="group image">
			<?
			}
			if (is_admin_of($_SESSION['uid'], $group->gid)) {
			?>		
				<br>
				<a href="/usergroups/<? echo $group->gid; ?>/uploadpic"><? echo l('uploadnewpicture'); ?></a>
			<?
			}
			?>
	
	
		</td>
		<td valign="top">
			<p><em><? echo $group->groupdescription?></em> -- 
			<? echo l('foundedby') ?>
	<a href="/profile/<?=$founder ?>"><?=$founder ?></a>, <a
	href="/users/member/<?=$group->gid ?>"><?= $mct[0]." ".l('members',0,$mct[0]); ?></a></p>
		<p><?=l('language');?>: <strong><?=$langnames[$group->lang] ?></strong>,
		<?=l('type')?>: <strong>
		<?
			if ($group->private) { echo l("private"); }
			else { echo l("public"); }
		?>
		</strong></p>

		<ul>
<?
	$ismember = mysqli_query($db,"SELECT member from lcwo_groupmembers
	where gid='$group->gid' and member='".$_SESSION['uid']."'");
	
	if (mysqli_fetch_row($ismember)) { 
		echo "<li>".l('youareamember')."</li>\n";

		if (is_subscribed_to($_SESSION['uid'], $group->gid)) {
			echo "<li><a rel='nofollow' href='/usergroups/subscribe/-$group->gid'>".l('unsubscribeemailalerts')."</a></li>\n";
		}
		else {
			echo "<li><a rel='nofollow' href='/usergroups/subscribe/$group->gid'>".l('subscribeemailalerts')."</a></li>\n";
		}

		/* do not allow admin to leave his own group */
		if (!is_admin_of($_SESSION['uid'], $group->gid)) {
		?>
			<script type="text/javascript">
				function reallyleave () {
					var answer = confirm("Really leave this group?");
					if (answer) {
						window.location.href = "<? echo
						"/usergroups/leave/$group->gid" ?>";
					}
				}
			</script>
		<?
		echo "<li><a onClick='reallyleave();return;' href=\"#\">".l('leavethisgroup')."</a></li>\n";
		}
		else {
			/* admin -> change group details */
			echo "<li><a href=\"/usergroups/$group->gid/edit\">".l('editgroupdetails')."</a></li>";

		}
	}
	else if ($_SESSION['uid']) {
		echo "<li><a href=\"/usergroups/join/$group->gid\">".l('jointhisgroup')."</a></li>";
	}

?>

	<li><a href="/highscores/group/<? echo $group->gid; ?>"><?
echo l('showgrouphighscore'); ?></a></li>	
	</ul>

<?
	/* Check for people who want to join this group */
	if ($group->private && is_admin_of($_SESSION['uid'], $group->gid)) {
		$query = mysqli_query($db,"
		SELECT distinct lcwo_users.username, lcwo_users.id
		FROM lcwo_users 
		INNER JOIN lcwo_grouprequests
		ON lcwo_grouprequests.member = lcwo_users.id
		where gid='$group->gid'");

		$apps = array();
		while ($application = mysqli_fetch_object($query)) {
				array_push($apps,
				$application->username."~".$application->id);
		}

		if (count($apps)) {
			echo "<hr noshade>";
			echo "<p>".l('membershipapplications')."</p>";
			echo "<table><th>".l('username')."</th><th>".l('approve')."</th></tr>";
			foreach ($apps as $a) {
				list($uname, $uid) = explode('~', $a);
				echo "<tr><td>$uname</td><td>
				<a href=\"/usergroups/$group->gid/approve/$uid/1\">"
				.l('yes').
				"</a> /
				<a href=\"/usergroups/$group->gid/approve/$uid/0\">"
				.l('no').
				"</a>
				</td></tr>\n";
			}
			echo "</table>";
		}
			
	}
?>







		
		</td>
	</tr>
	</table>
<?	


include("inc/forum.php");

// Custom group stuff.
if ($group->gid == 19) {
	include("inc/custom/uska-stgallen.php");
}


}



function joingroup () {
global $db;

	if (!$_SESSION['uid']) {
		return;
	}
		
	$group = $_GET['join'];

	if (!isint($group)) {
		echo "ERROR: Invalid group!";
		return 0;
	}

	/* check if group exists and is public */

	$cg = mysqli_query($db,"SELECT groupname, private from
				lcwo_usergroups where gid='$group'");
	$g = mysqli_fetch_object($cg);

	if (!$g) {
		echo "ERROR: Group does not exist!";
		return 0;
	}

	if (($g->private == 1) && ($_SESSION['uid'])) {
		/* Apply for membership in group */
		$query = mysqli_query($db,"INSERT INTO lcwo_grouprequests
		(member, gid) VALUES ('".$_SESSION['uid']."', '$group');");	
		if (!$query) {
			echo "Error: ".mysqli_error();
			return;
		}
		echo "<p>".l('appliedformembership')."</p>";
		echo '<p><a href="/usergroups">'.l('back')."</a></p>";
		return 0;
	}
	
	/* OK, join! Check if already member, you never know */
	
	$cim = mysqli_query($db,"SELECT member from lcwo_groupmembers
	where gid='$group' and member='".$_SESSION['uid']."'");

	if (mysqli_fetch_row($cim)) {
		echo "ERROR: You are already a member of this group!";
		return 0;
	}

	$join = mysqli_query($db,"INSERT INTO lcwo_groupmembers (gid,
					member) VALUES ('$group', '".$_SESSION['uid']."')");	

	if (!$join) {
		echo "ERROR: Unable to join group. Contact
		admin: ".mysqli_error();
		return 0;
	}
	else {
		echo "<a href=\"/usergroups/$group\">".l('joinedgroup')."</a>";
	}
}


function uploaddialog () {
	global $db;
	if (!isint($_GET['group'])) {
		return;
	}
	
	if (!is_admin_of($_SESSION['uid'], $_GET['group'])) {
		echo "You are not authorized to change the picture for this group!";
		return;
	}

	echo "<h1>".l('uploadnewpicture')."</h1>";

	?>
	<form action="/usergroups/<?echo $_GET['group']?>/uploadpic/ok" method="POST"
	enctype="multipart/form-data">
		<p><? echo l('chosejpegfile'); ?> 
		<input name="file" type="file" size="50" maxlength="100000"
	   												accept="image/*">
		</p>
		<input type="submit" value="<? echo l('submit'); ?>">
	</form>

<?
	


} /* uploaddialog */


function newgroup() {
	global $db;	
	global $langs;

    if (USERG_RO) {
		echo "Sorry. User groups are in read-only mode. Cannot create a new group. Contact admin."; 
		return;
    }
		
	if (!$_SESSION['uid'] or ($_SESSION['uid'] == TESTUSER)) {
		echo "Sorry. The test user is not allowed to start groups.";
		return;
	}

	if (!($_POST['groupname'] && $_POST['groupdescription'] &&
		in_array($_POST['lang'], $langs))) {
		echo "<p><b>Error:</b> Please fill in all fields.</p>\n";
		echo '<p><a href="/usergroups">Back</a></p>';
		return;
	}

	/* Check if group already exists */

	$groupname = esc(strip_tags($_POST['groupname']));
	
	$query = mysqli_query($db,"SELECT groupname from lcwo_usergroups
					where groupname = '$groupname'");

	if (!$query) {
		echo "Error! ".mysqli_error();
		return;
	}

	if (mysqli_fetch_row($query)) {
		echo "<b>Error:</b> Group already exists!\n";
		echo '<p><a href="/usergroups">Back</a></p>';
		return;
	}

	/* OK, new group! */
	
	$groupdescription = esc(strip_tags($_POST['groupdescription']));
	$lang = esc($_POST['lang']);
	$private = $_POST["private"] ? 1 : 0;

	$query = mysqli_query($db,"INSERT INTO lcwo_usergroups
	(`groupname`, `groupdescription`, `founder`, `private`,
	`lang`) 
	VALUES
	('$groupname', '$groupdescription', '".$_SESSION['uid']."',
	'$private', '$lang');");

	if (!$query) {
		echo "<b>Error:</b> ".mysqli_error();
		return;
	}

	/* get our group ID */
	$query = mysqli_query($db,"SELECT gid from lcwo_usergroups where
	groupname = '$groupname'");
	
	if (!$query) {
		echo "<b>Error:</b> ".mysqli_error();
		return;
	}
	
	$groupid = array_pop(mysqli_fetch_row($query));	# PHP sucks.

	/* add the founder to the group */

	$query = mysqli_query($db,"INSERT INTO lcwo_groupmembers (gid,
	member) VALUES ('$groupid', '".$_SESSION['uid']."')");


	if (!$query) {
		echo "<b>Error:</b> ".mysqli_error();
		return;
	}
	
	
	echo "<p><a
	href=\"/usergroups/$groupid\">".l('groupcreated')."</a></p>";

	?>	
	<script type="text/javascript">
	window.location.href = '/usergroups/<? echo $groupid ?>';
	</script>
	<?
} /* newgroup */






function approvemember () {
	global $db;
	if (!isint($_GET['group']) || !isint($_GET['approve']) ||
	!in_array($_GET['ok'], array(0,1))) {
		echo "Error. Invalid values.";
		return;
	}

	if (!is_admin_of($_SESSION['uid'], $_GET['group'])) {
		echo "Error. You are not admin of this group!";
		return;
	}

	$query = mysqli_query($db,"delete from lcwo_grouprequests where
	member='".$_GET['approve']."' and gid='".$_GET['group']."'");
	
	if ($_GET['ok']) {
		$query = mysqli_query($db,"insert into lcwo_groupmembers (gid,
		member) VALUES ('".$_GET['group']."', '".$_GET['approve']."');");
		if (!$query) {
			echo "Error! ".mysqli_error();
		}
	}

	echo "OK";
?>

<script type="text/javascript">
window.location.href = "<? echo "/usergroups/".$_GET['group']."" ?>";
</script>



<?


	
}




function leavegroup () {
	global $db;

	if (isint($_GET['leave'])) {

			$group = (int) $_GET['leave'];

			$member = (int) $_SESSION['uid'];
			
			if (is_member_of($member, $group)) {

				$query = mysqli_query($db,"DELETE from lcwo_groupmembers where 
				`gid`='$group' and `member`='$member' LIMIT 1");
					
				echo "OK. -&gt; <a href=\"/usergroups\">".l('usergroups')."</a>";
				return;
			}
			else {
					echo "You are not even a member of this group :-)";
					return;
			}
	}
	else {
			echo "Error: Group not numeric. <a href=\"/usergroups\">Back</a>.";
	}

}



function editgroup () {
	global $db;
	global $langnames;
	global $langs;
		
	if (!isint($_GET['group'])) {
		echo "Error: Invalid group.";
		return;
	}

	$gg = mysqli_query($db,"SELECT gid, groupname, groupdescription,
	founder, lang, private from lcwo_usergroups where
	gid='".$_GET['group']."';");
	
	if (!$gg) { echo "Error: Group not found!"; return; }
	
	$group = mysqli_fetch_object($gg);
	if (!$group) { echo "Error: Group not found!"; return; }
	
	if (!is_admin_of($_SESSION['uid'], $group->gid)) {
		echo "<p>You are <b>not</b> an admin of this group.</p><p><a
		href=\"/usergroups\">Back to user groups</a></p>"; 
		return;
	}
	
	echo "<h1>".l('editgroupdetails').": $group->groupname</h1>";

?>	
	<table width="80%">
	<tr>
		<td valign="top" width="140">
			<?
			if (file_exists("img/groupimage".$group->gid.".jpg")) {
			?>
			<img src="/img/groupimage<? echo $group->gid ?>.jpg" alt="Group Logo">
			<?
			}
			else {
			?>
			<img src="/img/groupimage0.png" alt="group image">
			<?
			}
			?>		
				<br>
		<a href="/usergroups/<? echo $group->gid; ?>/uploadpic"><? echo l('uploadnewpicture'); ?></a>
	
		</td>
		<td valign="top">

<form action="/usergroups/<?=$group->gid;?>/edit/ok" method="POST">
	<table>
		<tr>
		<td><? echo l('usergroupname'); ?>: </td> <td><input type="text"
size="48" name="groupname" value="<?=$group->groupname;?>"></td>
		</tr>
		<tr>
		<td><? echo l('usergroupdescription'); ?>: </td> <td><input type="text"
size="48" name="groupdescription" value="<?=$group->groupdescription;?>"></td>
		</tr>
		<tr>
<td><? echo l('language');?>: </td> <td>
		<select name="lang" size="1">
   	<?  foreach ($langs as $lang) {
			if ($lang == $group->lang) {
				echo "<option value=\"$lang\" selected>$lang - ".$langnames[$lang]."</option>";
			}
			else {
				echo "<option value=\"$lang\">$lang - ".$langnames[$lang]."</option>";
			}
		}
?>
	</select>
		</td>
		</tr>

		<tr>
		<td><? echo l('usergrouptype'); ?>: </td> <td>
		<select name="private" size="1">
<option value="0" <? if(!$group->private){echo "selected";} ?>><? echo l("public"); ?></option>
			<option value="1" <? if($group->private){echo "selected";} ?>><? echo l("private"); ?></option>
		</select>
		</td>
		</tr>
		<tr>
		<td colspan=2>
			<input type="submit" value="<? echo l('submit'); ?>">
		</td>
	</table>
	
</form>
		
		</td>
		</tr>
		</table>

        <p><a href="/usergroups/map/<?=$group->gid;?>">Click here to set the group location on the world map.</a></p>


<a href="/usergroups/<?=$group->gid;?>"><?=l("back");?></a>

		
	<?	
}


function savegroupchanges () {
	global $db;
	global $langs;
	

	if (!isint($_GET['group'])) { echo "Invalid group!"; return; }

		
	if (!is_admin_of($_SESSION['uid'], $_GET['group'])) {
		echo "Error. You are not admin of this group!";
		return;
	}

	if (!($_POST['groupname'] && $_POST['groupdescription'] &&
		in_array($_POST['lang'], $langs))) {
		echo "<p><b>Error:</b> Please fill in all fields.</p>\n";
		echo '<p><a href="javascript:history.back();">Back</a></p>';
		return;
	}

	/* Check if group already exists */

	$gid = $_GET['group'];
	$groupname = esc(strip_tags($_POST['groupname']));
	
	$query = mysqli_query($db,"SELECT groupname from lcwo_usergroups
					where groupname = '$groupname' and gid != '$gid'");

	if (!$query) { echo "Error! ".mysqli_error(); return; }

	if (mysqli_fetch_row($query)) {
		echo "<b>Error:</b> Different group with same name already exists!\n";
		echo '<p><a href="javascript:history.back();">Back</a></p>';
		return;
	}

	/* OK! */
	
	$groupdescription = esc(strip_tags($_POST['groupdescription']));
	$lang = esc($_POST['lang']);
	$private = $_POST["private"] ? 1 : 0;

	$query = mysqli_query($db,"update lcwo_usergroups set 
	`groupname` = '$groupname', 
	`groupdescription` = '$groupdescription',
   	`private` = $private,
	`lang` = '$lang'
	where gid=$gid;
	");

	if (!$query) {
		echo "<b>Error:</b> ".mysqli_error();
		return;
	}

	?>	
	OK....
	<script type="text/javascript">
	window.location.href = '/usergroups/<? echo $gid ?>/edit';
	</script>
	<?

}



function subscribe () {
	global $db;
		echo "<h1>".l('usergroups').": ".l('subscriptions')."</h1>";

		if ($_GET["subscribe"] == "show" or !is_int($_GET["subscribe"]+0)) {
				$show = 1;
				$group = 0;
		}
		else {
				$show = 0;
				$group = $_GET["subscribe"];
				if ($group > 0) {
						$subscribe = true;
				}
				else {
						$group *= -1;
						$unsubscribe = true;
				}
		}

		if (!$_SESSION['uid'] or $_SESSION['uid'] == TESTUSER) {
				echo "<p>Only available for regular registered users.</p>\n";
				return;
		}

		if ($_SESSION['email']) {
			echo "<p>".l('subscriptioninfo')."</p>\n";
		}
		else {
			echo "<p><a href='/account'>".l('emailaddressneeded')."</a></p>";
			return;
		}

		if (is_private_group($group)) {
				if (!is_member_of($_SESSION['uid'], $group)) {
						echo "<p>Error: ".l('youarenotamember')."</p>\n";
						return;
				}
		}


if ($subscribe) {
		if (is_subscribed_to($_SESSION['uid'], $group)) {
			echo "<p>".l('alreadysubscribed')."</p>\n";
			return;
		}

		$query = mysqli_query($db,"insert into lcwo_groupsubscribe 
				(gid, member) values ($group, ".$_SESSION['uid'].")");

		if (!$query) {
				echo "Error! ".mysqli_error();
				return;
		}
		
		echo "<p>".l('subscriptionsuccessful')." ".get_group_name($group)."</p>";
}
else if ($unsubscribe) {
		$query = mysqli_query($db,"delete from lcwo_groupsubscribe 
				where gid=$group and member=".$_SESSION['uid'].";");

		if (!$query) {
				echo "Error! ".mysqli_error();;
				return;
		}

		echo "<p>".l('unsubscriptionsuccessful')." ".get_group_name($group)."</p>";
}

?>
		<h3><?=l('subscriptions');?></h3>
<?


		$query = mysqli_query($db,"SELECT lcwo_usergroups.groupname,
			lcwo_usergroups.gid from lcwo_usergroups
			INNER JOIN lcwo_groupsubscribe
			ON
			lcwo_groupsubscribe.gid = lcwo_usergroups.gid
			where lcwo_groupsubscribe.member = '".$_SESSION['uid']."' 
			order by lcwo_usergroups.gid asc
			");

			if (!$query) {
				echo "ERROR: ".mysqli_error();
				exit(1);
			}
			echo "<ul>\n";	
			while ($line = mysqli_fetch_row($query)) {
					echo "<li><a href=\"/usergroups/$line[1]\">$line[0]</a> - <a href=\"/usergroups/subscribe/-$line[1]\">".l('unsubscribe')."</a></li>\n";
					$subcount++;
			}

			if ($subcount == 0) {
					echo "<li>---</li>";
			}
			echo "</ul>\n";	







?>
		<a href="/usergroups/<?=$group?>"><?=l('back');?></a>
<?
}
?>
