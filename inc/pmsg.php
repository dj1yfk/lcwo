<?
if (!$_SESSION['uid']) {
		echo "Please log in.<br>";
		return 0;
}

if ($_SESSION['uid'] == TESTUSER) {
		echo "Feature disabled for the test user.<br>";
		return 0;
}



switch ($_GET['action']) {
	case 'send':
	case 'reply':
			sendpmsg();
			break;
	case 'read':
			readpmsg();
			break;
	case 'del':
			delpmsg();
			break;
	case 'spam':
			spam();
			break;
	default:
		showinbox(0);
}
?>
<br><br>

<?
return 0;

function showinbox ($limit) {
global $db;
?>
<h1><? echo l('privatemessages'); ?></h1>


<table width="75%"
<tr><th width="20%"><?=l('pmsgfrom');?></th><th width="20%"><?=l('datetime')?></th><th width="50%"><?=l('pmsgsubject')?></th><th><?=l('action')?></th></tr>
<?

	if ($limit) {
		$limit = "limit $limit";
	}
	else {
		$limit = "";
	}

	$query = mysqli_query($db,"select * from lcwo_pmsg where touid=".$_SESSION['uid']." or fromuid=".$_SESSION['uid']." order by time desc ".$limit);

	if (!$query) {
			echo "Error: ".mysqli_error();
			return 0;
	}

	$i=0;
	while ($o = mysqli_fetch_object($query)) {
			$i++;
			$fname = uid2uname($o->fromuid);
			$tname = uid2uname($o->touid);

            # if it's a sent message, hide the read/unread status for the sender
            if ($_SESSION['uid'] == $o->fromuid) {
                $o->read = 1;
            }

			echo "<tr><td><a href='/profile/$fname'>$fname</a> &rarr; <a href='/profile/$tname'>$tname</a></td>";
			echo "<td>".da($o->time)."</td>";
			echo "<td>";
			if (!$o->read) { echo "<strong>"; }
			echo "<a href='/pmsg/read/$o->id'>$o->subject</a>";
			if (!$o->read) { echo "</strong>"; }
            echo "</td>";
            if ($o->touid == $_SESSION['uid']) {
                echo "<td align='center'>".deletereply($o->id, $o->spam)."</td></tr>\n";
            }
	}

	if (!$i) {
		echo "<tr><td colspan='3' align='center'>".l('pmsginboxempty')."</td></tr>\n";
	}
?>
</table>
<p><a href="/forum"><?=l('back')?></a></p>
<?
} # showinbox


function readpmsg () {
	global $db;
	$msgid = intval($_GET['id']);
	if ($msgid <= 0) {
		echo "Invalid Message.";
		return;
	}

	$query = mysqli_query($db,"select * from lcwo_pmsg where `id`= '$msgid' and (touid='".$_SESSION['uid']."' or fromuid='".$_SESSION['uid']."') limit 1;");

	if (!$query) { echo "Error: ".mysqli_error($db); return 0; }

	if ($msg = mysqli_fetch_object($query)) {

        # only mark as 'read' if the recipient reads the mail.
        if ($msg->touid == $_SESSION['uid']) {
		    $query = mysqli_query($db,"update lcwo_pmsg set `read`=1,time=time where `id`= '$msgid' and touid='".$_SESSION['uid']."' limit 1;");
        }
        else {
            $action = false;
        }

		$username = uid2uname($msg->fromuid);
	?>
    <h1><?=$msg->subject;?></h1>
		<p><?  echo "<strong>".l('pmsgfrom').": </strong> <a href='/profile/$username'>$username</a>; <strong>".l('datetime').": </strong> ".da($msg->time)." &nbsp;&nbsp;&nbsp;&nbsp; ".deletereply($msg->id, $msg->spam); ?></p>

	<div class="pmsgbox">
<?
		$text = preg_replace("/\n/", "<br>", $msg->text);
		$text = bb2html($text);
		echo $text;
?>
	</div>

	<p><a href="/pmsg"><?=l('back')?></a></p>

	<?
	}
	else {
		echo "<p>No such message.</p>";
	}

}


function sendpmsg () {
	global $db;
	$action = $_GET['action'];

    if (PMSG_RO) {
        echo "Sending personal messages disabled by admin.\n";
        return;
    }

	if ($action == "send") {
		$recipient = $_GET['id'];
		$recipient_uid = uname2uid($_GET['id']);
		if (!$recipient_uid) {
			echo "Error!";
			return 0;
		}
	}
	else {	# reply => find out whom to send message to
		$toid = intval($_GET['id']);
		$query = mysqli_query($db,"select fromuid, subject, text from lcwo_pmsg where".
				" touid='$_SESSION[uid]' and id='$toid' ");
		if (sqlerror($query)) { return 0; }

		if ($msg = mysqli_fetch_object($query)) {
			$recipient = uid2uname($msg->fromuid);
			$recipient_uid = $msg->fromuid;
			$quotesubject = $msg->subject;
			$quotesubject = preg_replace('/Re: /', '', $quotesubject);
			$quotesubject = "Re: $quotesubject";
			$quotetext = "$recipient wrote:\n".$msg->text;
			$quotetext = wordwrap($quotetext, 70);
			$quotetext = preg_replace('/\n/', "\n> ", $quotetext);
			$quotetext = preg_replace('/> >/', ">>", $quotetext);
			$quotetext .= "\n\n";
		}
		else {
			echo "Invalid message, cannot reply.";
			return 0;
		}
	}

?>

<h1><? echo l('sendpersonalmessage')." (".$recipient.")"; ?></h1>

<?
	# Send or compose message?
	#
	# Send
	if ($_POST['send'] == 1) {

			$text = strip_tags(esc($_POST['text']));
			if (mb_strlen($text) > 50000) {
				$text = mb_substr($text, 0, 50000);
				$text = preg_replace("/'$/", "", $text);
			}
			$subject = strip_tags(esc($_POST['subject']));
			if (mb_strlen($subject) > 255) {
				$subject = mb_substr($subject, 0, 255);
				$subject = preg_replace("/'$/", "", $subject);
			}

			if (!$text) {
					echo "<p>Error: No text!</p>";
					return 0;
			}
			if (!$subject) {
				$subject = "[empty subject]";
			}

			$query = mysqli_query($db,"INSERT into lcwo_pmsg 
				(fromuid, touid, subject, text, ip, time)
				VALUES ('$_SESSION[uid]', $recipient_uid, '$subject', '$text',
						'".getenv('REMOTE_ADDR')."', NULL)");

			if ($query) {
				echo "<p>".l('pmsgsent')."</p>";
			}
			else {
				echo "Error! ".mysqli_error();
			}
	}
	else {	# POST[send] = 0 -> compose
?>
<form method="POST">
<?=l('pmsgsubject')?>: <input type="text" size="80" name="subject" id="subject" value="<?=$quotesubject;?>"><br>
<textarea cols="80" rows="25" name="text" id="textbox"><?=$quotetext;?></textarea><br>
<input type="submit">
<input type="hidden" name="send" value="1">
</form>

<script type="text/javascript">
<? if ($action == "send") { ?>
			document.getElementById('subject').focus();
<? } else { ?>
			document.getElementById('textbox').focus();
<? } ?>
</script>
<?
	} # else ...

?>
<p><a href="/pmsg"><?=l('back')?></a></p>
<?

}


function delpmsg () {
	global $db;
	$msg = intval($_GET['id']);
	if ($msg <= 0) {
		echo "Invalid Message.";
		return;
	}

	$query = mysqli_query($db,"delete from lcwo_pmsg where `id`= '$msg' and touid='".$_SESSION['uid']."' limit 1;");

	if (!$query) {
		echo "Error: ".mysqli_error($db);
	}
?>
<script type="text/javascript">
	window.location.href = '/pmsg';
</script>

<?
}

function spam () {
	global $db;
	$msg = intval($_GET['id']);
	if ($msg <= 0) {
		echo "Invalid Message.";
		return;
	}

	$query = mysqli_query($db,"select * from lcwo_pmsg where `id`= '$msg' and touid='".$_SESSION['uid']."' limit 1;");

	if (!$query) {
		echo "Error: ".mysqli_error($db);
    }
    else {
        $pmsg = mysqli_fetch_object($query);
        if ($pmsg->spam == 1) {
            echo "<p>Removed the spam flag from message.</p>";
	        mysqli_query($db,"update lcwo_pmsg set spam = 0 where `id`= '$msg' and touid='".$_SESSION['uid']."' limit 1;");
        }
        else {
            echo "<p>Flagged this message as SPAM. An administrator will look into the issue. If you flagged this message errorneously, just click <a href='/pmsg/spam/$msg'>here</a>.</p>";
	        mysqli_query($db,"update lcwo_pmsg set spam = 1 where `id`= '$msg' and touid='".$_SESSION['uid']."' limit 1;");
            echo mysqli_error($db);
        }
    }
?>
   <p><a href="/pmsg"><?=l('back');?></a></p>
<?
}



function deletereply($id, $isspam) {
    if ($isspam) {
        $img = "nospam.png";
    }
    else {
        $img = "spam.png";
    }
    $ret = "<a href='/pmsg/del/$id'><img style='border:none;vertical-align:middle' src='/pics/del.png' alt='[del]' title='".l('delete')."'></a>";
    $ret .= "&nbsp;&nbsp;<a href='/pmsg/reply/$id'><img style='border:none;vertical-align:middle' src='/pics/reply.png' alt='[reply]' title='".l('reply')."'></a>";
    $ret .= "&nbsp;&nbsp;<a href='/pmsg/spam/$id'><img style='border:none;vertical-align:middle' src='/pics/$img' alt='[spam]' title='".l('spam')."'></a>";
    return $ret;

}





?>



