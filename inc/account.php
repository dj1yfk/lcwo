<?

	if (!$_SESSION['uid']) {
		echo "Sorry, you must be logged in to use this.";
		return 0;
	}

	if ($_SESSION['uid'] == TESTUSER) {
	    echo "<p>The test user's account cannot be changed. Information is read-only.</p>";
	}
	elseif (isset($_SESSION['uid']) && $_POST['deleteaccount']) {
		echo "<p style='font-size:16px;color:red'>".l('deleteaccount3')."</p>";
		$mailtext = delete_user_url();
		mail (ADMINMAIL, "LCWO Request for deletion", $mailtext , "From: LCWO Robot <".ADMINMAIL.">");
	}
	elseif (isset($_SESSION['uid']) && $_POST['submitted']) {
	$valid = 1;
	$error = "";
			
			if ($_POST['pw1'] != '') {
					if ($_POST['pw1'] != $_POST['pw2']) {
						$valid = 0;
						$error .= l('passwordsnotmatch')."<br>";
					}
					else {
						$newpass = md5($_SESSION['uid'].SALT.md5($_POST['pw1']));
						$np = mysqli_query($db,"update lcwo_users set `password`='$newpass' where `id`=".$_SESSION['uid'].";");
						if (!$np) {
							echo "<p>Error: ".l('changepwfail')."</p>";
							$valid = 0;
						}
						else {
							echo "<p>".l('pwchanged')."</p>";
						}
					}
			}

			$username = mysqli_real_escape_string($db,stripslashes($_POST["username"])); 
			$name = mysqli_real_escape_string($db,stripslashes(strip_tags($_POST["name"])));
			$email = mysqli_real_escape_string($db,stripslashes(strip_tags($_POST["email"])));
			$location = mysqli_real_escape_string($db,stripslashes(strip_tags($_POST["location"])));

			# if new language is faulty, ignore it!
			if (in_array($_POST['lang'], $langs)) {
				$newlang = $_POST['lang'];
			}
			else {
				$newlang = $_SESSION['lang'];
			}

            # if new theme does not exist, ignore it
			if (in_array($_POST['theme'], $themes)) {
				$newtheme = $_POST['theme'];
			}
			else {
				$newtheme = $_SESSION['theme'];
			}

            $hide = 0;
            if (isset($_POST['hide'])) {
                $hide = 1;
            }

			if (preg_match("/^[a-zA-Z0-9]{2,24}$/", $username)) {
			    $gu = mysqli_query($db,"select `id` from lcwo_users where `username`='$username' and `id` != '".$_SESSION['uid']."';");

			    if (!$gu) {
				    die("Unable to check username.\n".mysqli_error($db));
			    }

			    if (mysqli_fetch_row($gu)) {
				    $valid = 0;
				    $error .= l('usernametaken');
			    }
			}
			else {
				$valid = 0;
				$error .= l('userinvalidchar')."<br>";
			}
		
			if ($valid) {
					$update = mysqli_query($db,"update lcwo_users set
					`username` = '$username',
					`name`='$name',
					`email`='$email', `location`='$location',
                    `lang` = '$newlang', `hide` = $hide,
                    `theme` = '$newtheme'
					where `id`=".$_SESSION['uid']);

					if (!$update) {
						echo "<p>".l('updatefail')."</p>";
					}
					else {
						echo "<p>".l('updatesuccess')."</p>";

						unset($_SESSION['username']);
						unset($_SESSION['name']);
						unset($_SESSION['email']);
						unset($_SESSION['location']);
						unset($_SESSION['lang']);
						unset($_SESSION['theme']);
						unset($_SESSION['loadedlocale']);
						$_SESSION['username'] = $username;
						$_SESSION['name'] = stripslashes($name);
						$_SESSION['email'] = $email;
						$_SESSION['location'] = stripslashes($location);
						$_SESSION['lang'] = $newlang;
						$_SESSION['theme'] = $newtheme;
						$_SESSION['hide'] = $hide;
    					# forces reload of locale
						loadlocale($newlang);
						$_SESSION['loadedlocale'] = 0;
					}

			}
			else {
				echo "<p>Sorry: <br>$error</p>";
			}	

			


	}
	elseif ($_POST['submitted']) {
		echo "Error. Not logged in.";
	}



?>



<h1>
<? echo l('editaccount'); ?>
</h1>

<p>
<? echo l('accountinstructions'); ?>
</p>

<div align="center">
<form action="/account" method="POST">
<table width="75%">
<tr class='hl'>
	<td width="20%"><?echo l('username')?>:</td>
	<td><input type="text" size="20" name="username" value="<? echo $_SESSION['username']; ?>">
	</td>
	<td>
	* (<? echo l('allowedchars'); ?>: a-z, A-Z, 0-9)</td>
</tr>
<tr>
<td><?echo l('password')?>:</td>
	<td><input type="password" size="20" name="pw1"></td>
	<td>* (<? echo l('leaveemptyifnochange') ?>)</td> 
</tr>
<tr class="hl">
<td><?echo l('cfmpassword')?>:</td>
	<td><input type="password" size="20" name="pw2"></td>
	<td>*</td>
</tr>
<tr>
	<td><?=l('email')?>:</td>
	<td><input type="text" size="20" name="email" value="<? echo $_SESSION['email']; ?>">
	</td>
</tr>
<tr class="hl">
	<td><?echo l('name')?>:</td>
	<td><input type="text" size="20" name="name" value="<? echo htmlspecialchars($_SESSION['name']); ?>">
	</td>
</tr>
<tr>
	<td><?echo l('location')?>:</td>
	<td><input type="text" size="20" name="location" value="<? echo htmlspecialchars($_SESSION['location']); ?>">
	</td>
</tr>
<tr class="hl">
	<td><?echo l('language') ?>:</td>
	<td>
<select name="lang" size="1">
<?
foreach ($langs as $lang) {
	if ($lang == $_SESSION['lang']) {
		echo "<option value=\"$lang\" selected>$lang - ".$langnames[$lang]." (".$enlangnames[$lang].")</option>";
	}
	else {
		echo "<option value=\"$lang\">$lang - ".$langnames[$lang]." (".$enlangnames[$lang].")</option>";
	}
}
?>
</select>
</td>
</tr>
<tr>
	<td><?echo l('theme') ?>:</td>
	<td>
<select name="theme" size="1" onchange="loadCSS(this.value);">
<?
foreach ($themes as $theme) {
	if ($theme == $_SESSION['theme']) {
		echo "<option value=\"$theme\" selected>".l($theme)."</option>";
	}
	else {
		echo "<option value=\"$theme\">".l($theme)."</option>";
	}
}
?>
</select>
</td>
</tr>
<tr class='hl'>
    <td>Privacy:</td>
    <td><input type="checkbox" name="hide" <? echo $_SESSION['hide'] == 1 ? 'checked' : ''; ?>> Don't show me in <?=l('whoisonline');?></td>
</tr>
<tr>
<td>
<input type="hidden" name="submitted" value="1">
<input type="submit" value="<?echo l('submit',1)?>">
</td>
</tr>
</table>
</form>
</div>

<script>
function loadCSS(a) {
    document.getElementById('lcwocss').href = "/" + a + ".css";
}

loadCSS('<?=$_SESSION['theme'];?>');

</script>


<h2><?=l('deleteaccount');?></h2>
<p><?=l('deleteaccount1');?></p>
<p><?=l('deleteaccount2');?></p>
<form action="/account" method="POST">
<input type="hidden" name="deleteaccount" value="1">
<input type="submit" onclick="return confirm('Really delete this account?');" value="<?=l('delete',1)?>">
</form>
