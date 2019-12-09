<?

if ($_GET['p'] == "forum") {
	if (isint($_GET['e'])) {
		if ($_SESSION['forum_whitelist'] == "1") {
			editpost($_GET['e']);
		}
		else {
				return;
		}
	}
	else {
		show_forum(0);		/* Main Forum */
	}
}
else if (($_GET['p'] == "usergroups") && isint($_GET['group'])) {
	show_forum($_GET['group']);
}	


?>

<?

function show_forum ($forumid) {
global $db;

/*
	Show Thread
*/

if (isint($_GET['t'])) {

	$tid = intval($_GET['t']);

	$gm = mysqli_query($db,"SELECT `topic`, `forumid` from
	`lcwo_posts` where `tid`='$tid' limit 1");

	$m = mysqli_fetch_object($gm);

	if (!$m) {
		echo "<p>Thread does not exist!</p>\n";
		return;
	}	

	if ($m->forumid) {
		$gotourl = "/usergroups/".$m->forumid;
	}
	else {
		$gotourl = "/forum";
	}

	$forumid = $m->forumid;
	if ($forumid == 0) {
		?>
		<h1> <? echo l('discussionforum'); ?> <a href="http://lcwo.net/forumatom.xml"><img src="/pics/feed.png" border="0" alt="[Atom LCWO Forum Feed]" title="LCWO Atom Forums Feed"></a>
		</h1>
		<p><? echo l('forumdescription'); ?> </p>
<?
	}
	else {
		$querygroupname = mysqli_query($db,"SELECT groupname from
		lcwo_usergroups where gid='$forumid'");

		if ($_SESSION['uid'] != ADMIN) {
			if (is_private_group($m->forumid) & !is_member_of($_SESSION['uid'], $m->forumid)) {
				echo "<p>You are not a member of this private group.</p>";
				return; // XXX translate
			}
		}
		
		$groupname = mysqli_fetch_row($querygroupname);
		
		?>
		<h1> <? echo l('groupdiscussionforum')." ".$groupname[0]; ?>  </h1>
		<?
	}

    if (FORUM_RO) {
?>
    <p><b>Note: The forum is in read-only mode. If you have questions about LCWO, don't hesitate to contact us via <a href="mailto:<?=ADMINMAIL?>"><?=ADMINMAIL?></a> or via the <a href="/impressum">contact form</a>.</b></p>
<?
    }


?>
<script  type="text/javascript">
	document.title = document.title.replace(/Forum/, 'Forum - <?=esc($m->topic);?>');

	function quote (postid) {
			var post = document.getElementById('post' + postid).innerHTML;
			var user = document.getElementById('user' + postid).innerHTML;
			var answer = document.getElementById('answer');

			user = user.replace(/\s-.+$/, '');
			
			post = post.replace(/<div(.|\r|\n)+<\/div>/g, '');
			post = post.replace(/<br>/g, '');
			post = post.replace(/<(\/)?([a-z])>/g, '[$1$2]');
			post = post.replace(/(\s|\r|\n)$/g, '');
			post = post.replace(/^(\s|\r|\n)/g, ''); 
			post = post.replace(/^(\s|\r|\n)/g, ''); 

			answer.value = '[quote='+user+']'+post+'[/quote]\n\n'+answer.value;

	}
</script>

<?	
	echo "<h2>".l('thread').": $m->topic </h2>";
?>	
<p><a href="<?=$gotourl?>"><? echo l('backtoforum') ?></a></p>
<table width="80%" cellpadding="5">
<tr><th width="30%"><? echo l('author') ?></th><th width="70%"><?=l('forumtext')?></th></tr>
<?
	
	$gm = mysqli_query($db,"SELECT `id`, `uid`, `time`, `text`, `approved` from
	`lcwo_posts` where `tid`='$tid' order by `id`, `time` asc");

	$count = 0;
	while ($m = mysqli_fetch_object($gm)) {
		$count++;

		$awaitingapproval = "";
		if ($m->approved == 0) {
				if ($m->uid == $_SESSION['uid']) {
						$awaitingapproval = "<br><br><i>".l('postawaitingapproval')."</i>";
				}
				else {
					continue;
				}
		}

		$info = uid2info($m->uid);
		$mtext = $m->text;
		$mtext = preg_replace("/\n/", "<br>", $mtext);
		$mtext = bb2html($mtext);

		echo "<tr> <td class=\"tborder\" valign=\"top\">";
		if ($info->username != "[deleted]") {
			echo "<div><a id='user$m->id' href=\"/profile/$info->username\">$info->username - $info->name</a>";
			if ($_SESSION['uid'] && $_SESSION['uid'] != TESTUSER) {
				echo "&nbsp;&nbsp;<a href='/pmsg/send/$info->username'><img style='vertical-align:middle' src='/pics/email.png' alt='Send PM' border='0'></a>";
			}
			echo "</div>\n";	
			$deleted = 0;
		}
		else {
			$deleted = 1;
			echo "[deleted]";
		}

		if ($m->uid == 1) {
				echo "<strong>".l('administrator')."</strong><br>\n";
		}
if (!$deleted && file_exists("img/userimage".$m->uid.".jpg")) {
?>
<img src="/img/userimage<? echo $m->uid ?>.jpg" width="75"> 
<?
}
		echo "<br><br>".l('posted').": ".(da($m->time));
		if (($m->uid == $_SESSION['uid']) && ($_SESSION['uid'] != TESTUSER) && ($_SESSION['forum_whitelist'] == 1)) {
			echo " - <a href=\"/forum/edit/$m->id\">".l(edit)."</a>";
		}
		elseif ($_SESSION['uid']) {
			echo " - <a onClick='quote($m->id);return false;' href=\"#\">".l('quote')."</a>";
		}
		echo "</td><td class=\"tborder\" id=\"fp$count\" valign=\"top\">";
		echo "<div id=\"post$m->id\">$mtext $awaitingapproval</div>\n</td>";
		echo "</tr>\n";
	}

	# Reply
    if ($_SESSION['forum_whitelist'] > time()) { # banned
?>
        <tr><td colspan="2">You are currently not allowed to post on the forum.</td></tr>
<?
    }
    elseif ($_SESSION['uid'] && !FORUM_RO) {
?>

	<tr>
	<td class="tborder" valign="top"><? echo l('postreply') ?>:<br><br>
	<? echo l('allowedtags') ?><br> 
	<b>[b]<? echo l('boldtext') ?>[/b]</b><br>
	<i>[i]<? echo l('italictext') ?>[/i]</i><br>
    [s]<s><? echo l('striketext') ?></s>[/s]<br>
    [cw speed=50 eff=20 ews=2 freq=600]Text[/cw]<br>
	</td>
	<td  class="tborder">
	<form action="/forumpost" method="POST">
	<textarea name="text" cols="50" rows="6" id="answer"></textarea><br>
	<input type="hidden" name="tid" value="<? echo $tid; ?>">
	<input type="hidden" name="forumid" value="<? echo $forumid; ?>">
	<input type="submit" value="<? echo l('submit',1)?>">
	</form>
	</td>
	</tr>

<?
	}
?>

</table>
<p><a href="<? echo $gotourl; ?>"><? echo l('backtoforum') ?></a></p>

<?
}

/* Show overview */

else { 
		
if ($_GET['showall']) {
	$showall = 1;
}

if ($forumid == 0) {
	?>
	<h1> <? echo l('discussionforum'); ?>  <a href="http://lcwo.net/forumatom.xml"><img src="/pics/feed.png" border="0" alt="[Atom LCWO Forum Feed]" title="LCWO Atom Forums Feed"></a> </h1>

<table>
<tr>
<td width="50%" valign="top">
	<p><? echo l('forumdescription'); ?> </p>
</td>
<?
    if (FORUM_RO) {
?>
    <p><b>Note: The forum is in read-only mode. If you have questions about LCWO, don't hesitate to contact us via <a href="mailto:<?=ADMINMAIL?>"><?=ADMINMAIL?></a> or via the <a href="/impressum">contact form</a>.</b></p>
<?
    }
?>
<td width="15%">
&nbsp;
</td>
<td width="35%" valign="top">
	<?
		if ($_SESSION['uid'] && $_SESSION['uid'] != TESTUSER) {
	?>
		<div>&nbsp;&nbsp;<img src="/pics/email.png" style="vertical-align:middle">
		&nbsp; <a href="/pmsg"><?=l('privatemessages');?></a> <?=privmsgcount();?></div>
	<?
		}
	?>
</td>
</tr>
</table>
<?
}


$gt = mysqli_query($db,"SELECT `id`, `tid` from `lcwo_posts` where
forumid = '$forumid' and approved=1 order by
`id` desc");

# Get the last maximum 20 threads (or all)
$threads = 0;
while ($t = mysqli_fetch_object($gt)) {
	if (!$th[$t->tid]) {
		$th[$t->tid] = 1;
		$threads++;
	}
	if (!$showall && $threads == 20) {
		break;
	}
}


	
?>

<h2><? echo l('threads') ?></h2>

<table width="80%">
<tr><th width="50%">
<? echo l('topic') ?>
</th><th width="10%">
<? echo l('posts') ?>
</th><th width="40%">
<? echo l('lastpost') ?>
</th></tr>
<?

if ($threads) {

foreach (array_keys($th) as $tid) {
	# Last post from this thread, and topic of the thread.
	$gt = mysqli_query($db,"select `topic`, `time`, `uid` from `lcwo_posts`
	where `tid`='$tid' and approved = 1 order by `time` desc");

	$g = mysqli_fetch_object($gt);

	$topic = $g->topic;	
	$topicurl = toURLpath($topic);
	$time = da($g->time);
	$user = uid2uname($g->uid);

	$gc = mysqli_query($db,"select count(*) from `lcwo_posts` where
	`tid` = '$tid' and approved=1");

	$g = mysqli_fetch_row($gc);
	$count = $g[0];
	
echo "
<tr>
<td><a href=\"/forum/$tid$topicurl\">$topic</a></td>
<td align=\"center\">$count</td>
<td align=\"center\">$time by ";

if ($user == "[deleted]") {
	echo $user;
}
else {
	echo "<a href=\"/profile/$user\">$user</a>";
}
echo "</td></tr>\n\n";
}	# foreach


}
/* No threads! */
else {
	echo '<tr><td colspan="3">'.l('nopostsyet').'</td></tr>';
}

?>
</table>

<?
# check for more than 10 and offer to display all


if ($forumid == 0) {
	$newlocation = "/forum";	
}
else {
	$newlocation = "/usergroups/$forumid";
}


if (!$showall) {
	$nt = mysqli_query($db,"SELECT DISTINCT `tid` from `lcwo_posts`
	where forumid='$forumid' and approved=1");
	$threadcount = 0;
	while ($t = mysqli_fetch_row($nt)) {
		$threadcount++;
	}
	
	if ($threadcount > 10) {
	?>
		<p><a href="<?=$newlocation;?>/showall"><? echo l('showall'); ?></a></p>
	<?
	}
}
else {
	?>
		<p><a href="<?=$newlocation;?>"><? echo l('hide'); ?></a></p>
	<?

}


} # else

?>



<? if ($_GET['t'] || !$_SESSION['uid'] || FORUM_RO || $_SESSION['forum_whitelist'] > time()) {
return 0;
}
?>

<h2> <? echo l('newthread') ?> </h2>

<table><tr><td>

<form action="/forumpost" method="POST">
	<? echo l('topic') ?>: <input type="text" size="32" name="title"><br>
	<textarea name="text" cols="50" rows="6"></textarea><br>
	<input type="hidden" name="new" value="1">
	<input type="hidden" name="forumid" value="<?=$forumid;?>">
	<input type="submit" value="<? echo l('submit',1)?>">
</form>
</td>
<td>
&nbsp;
</td>
<td valign="top">
	<? echo l('allowedtags') ?><br><br>
	<b>[b]<? echo l('boldtext') ?>[/b]</b><br>
	<i>[i]<? echo l('italictext') ?>[/i]</i><br>
	[s]<s><? echo l('striketext') ?></s>[/s]
</td>
</table>


<?

} // Function show_forum



function editpost ($id) {
global $db;
	if (!isint($id)) {
		echo "Not a valid post ID.\n";
		return;
	}
	
	$query = mysqli_query($db,"SELECT `uid`, `tid`, `time`, `text` from `lcwo_posts` where `id`='$id'");

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
	

	echo "<h2>".l('forum')." - ".l('edit')."</h2>";
	
?>

<table width="75%">
	<tr>
	<td class="tborder" valign="top"><? echo l('edit') ?>:<br><br>
	<? echo l('allowedtags') ?><br> 
	<b>[b]<? echo l('boldtext') ?>[/b]</b><br>
	<i>[i]<? echo l('italictext') ?>[/i]</i><br>
	[s]<s><? echo l('striketext') ?></s>[/s]
	</td>
	<td  class="tborder">
	<form action="/forumpost" method="POST">
	<textarea name="text" cols="50" rows="10"><?
		echo $post->text;
	?></textarea><br>
	<input type="hidden" name="pid" value="<? echo $id; ?>">
	<input type="hidden" name="tid" value="<? echo $post->tid; ?>">
	<input type="submit" value="<? echo l('submit',1)?>">
	</form>
	</td>
	</tr>
</table>



<?
}


if (!$_SESSION['uid']) {
	echo "<p>".l(mustbeloggedin)."</p>";
}


?>
<div class="vcsid">$Id: forum.php 62 2015-01-12 17:34:44Z fabian $</div>

