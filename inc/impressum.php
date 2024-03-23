<h1>Impressum <? if ($_SESSION['lang'] != 'de') { echo "/ ".l("legalnotice");  }?></h1>

<?
if (file_exists($_SERVER['DOCUMENT_ROOT']."/inc/impressum.custom.php")) {
        include($_SERVER['DOCUMENT_ROOT']."/inc/impressum.custom.php");
}
else {
?>
<strong>This instance of LCWO has no legal info set. This needs to be fixed by the site administrator by creating a file impressum.custom.php which will replace this section.</strong>
<?
}
?>
<p>LCWO is copyright &copy; <a href="https://fkurz.net/">Fabian Kurz</a>, 2008-<? $d = getdate(); echo $d['year']; ?>. Source code: <a href="https://git.fkurz.net/dj1yfk/lcwo">https://git.fkurz.net/dj1yfk/lcwo</a>.</p>

<?
if (CONTACTFORM) {
    if ($_POST['name']) {
        sendmail();
    }
    else {
        showform($_SESSION['name'],$_SESSION['email'],"");
    }
}

function showform ($name, $email, $text) {

		if ($_SESSION['uid'] == TESTUSER) {
			$name = $email = "";
		}

?>
<h2><?=l('contactform');?></h2>
<form action="/impressum" method="POST">
<table>
<tr>
<td><?=l('name')?>:</td>
<td><input type="text" size="25" name="name" value="<? echo $name ?>"></td>
</tr>
<tr>
<td><?=l('email');?>:</td>
<td><input type="text" size="25" name="email" value="<? echo $email ?>"></td>
</tr>
<tr>
<td colspan="2"><textarea name="text" cols="40" rows="10"><? 
echo $text;
?></textarea></td>
</tr>
<tr><td colspan="2"><input type="checkbox" onClick="document.getElementById('sub').disabled=!this.checked;"> I have read, understood and accepted the <a href="/privacy">privacy policy</a>.</td></tr>
<tr>
<td>
<input id="sub" disabled="true" type="submit" value="<? echo l('submit'); ?>">
</td>
</tr>
</table>
</form>
<?

} # showform

function sendmail () {
	if (!$_POST['email']) {
		echo "<b>Contact form Error</b>: No email address entered.";
		showform($_POST['name'], "", $_POST['text']);
		return;
	}


	if (!$_POST[text]) {
		echo "<b>Contact form Error</b>: No text entered.";
		showform($_POST['name'], $_POST['email'], "");
		return;
	}

	if (
			preg_match('/a href/i', $_POST['text']) or
			preg_match('/bit.ly/i', $_POST['text']) 
	) {
		echo "<b>Looks like spam. Not delivered.</b>";
		return;
		$spam = "true";
	}
	else {
		echo "<b>".l('thanksforyourmessage')."</b>";
	}

	mail (ADMINMAIL, "Message on LCWO contact form",
			"From: ".$_POST['name']." - ".$_POST['email']."\n\n".
			"Username: ".$_SESSION['username']."\n\n"
			.$_POST['text']."\n\nSpam: ".$spam.
			"\n\nFrom:".getenv("REMOTE_ADDR")."\n\n",
					"From: LCWO Robot <".ADMINMAIL.">\r\nReply-To: ".$_POST['email']."\r\n".
					"Content-Type: text/plain; charset=utf8\r\n".
					"Content-Transfer-Encoding: 8bit");
}

?>

