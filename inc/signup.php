<h1>
<? echo l('signupheader') ?>
</h1>

<script>
	function checkUsername () {
		var u = document.getElementById('formusername');
		if (u.value.length < 2) {
			document.getElementById('user_exists_warning').innerHTML = '(too short)';
			return;
		}

  		var posturl;
		posturl = "/api/user_exists.php";

		var request =  new XMLHttpRequest();
		request.open("POST", posturl, true);
		request.setRequestHeader("Content-Type",
				"application/x-www-form-urlencoded");

		request.onreadystatechange = function() {
				var done = 4, ok = 200;
				if (request.readyState == done && request.status == ok) {
						if (request.responseText) {
								var r = JSON.parse(request.responseText);
								if (r.user_exists == 0) {
										document.getElementById('user_exists_warning').innerHTML = '(available)';
								}
								else if (r.user_exists == 1) {
										document.getElementById('user_exists_warning').innerHTML = '<b style="color:red">Username already exists!</b>';
								}
								else if (r.user_exists == -1) {
										document.getElementById('user_exists_warning').innerHTML = '<b>Username with invalid characters. Only A-Z, 0-9 allowed.</b>'; 
								}
						}
				}
		};
		request.send('username='+u.value);
	}
</script>


<p> <? echo l('signupinstructions'); ?> </p>
<p> <? echo l('signupinstructions2'); ?> </p>


<div align="center">
<form action="/register" method="POST">
<table>
<tr style="background-color:#dfdfdf">
	<td width="20%"><? echo l('username') ?>: *</td>
	<td><input onKeyup="checkUsername();" type="text" size="20" id="formusername" name="username" value="<?  echo $_SESSION['signup']['username']; ?>">
	 (<? echo l('allowedchars') ?>: a-z, A-Z, 0-9) <span id="user_exists_warning"></span></td>
</tr>
<tr>
	<td><? echo l('password') ?>: *</td>
	<td><input type="password" size="20"
	name="pw1"></td> 
</tr>
<tr style="background-color:#dfdfdf">
	<td><? echo l('cfmpassword') ?>: *</td>
	<td><input type="password" size="20"
	name="pw2"></td>
</tr>
<tr>
	<td><?=l('email')?>:</td>
	<td><input type="text" size="20" name="email" placeholder="yourname@example.com" value="<?  echo $_SESSION['signup']['email']; ?>"></td>
</tr>
<tr style="background-color:#dfdfdf">
	<td><? echo l('name') ?>:</td>
	<td><input type="text" size="20" name="name" placeholder="Your Name" value="<? echo $_SESSION['signup']['name']; ?>"></td>
</tr>
<tr>
	<td><? echo l('location') ?>:</td>
	<td><input type="text" size="20" name="location" placeholder="Your Location" value = "<?  echo $_SESSION['signup']['location']; ?>"></td>
</tr>
<tr style="background-color:#dfdfdf">
	<td><? echo l('language') ?>:</td>
    <td>
<select name="lang" size="1">
<?
if (in_array($_SESSION['lang'], $langs)) {
	$preflang = $_SESSION['lang'];
}
else if (in_array($browserlang, $langs)) {
	$preflang = $browserlang;
}

foreach ($langs as $lang) {
	if ($lang == $preflang) {
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

<?

if (is_untrusted_ip($_SERVER['REMOTE_ADDR'])) {
?>
<tr style="background-color:#dfdfdf"><td>Solve captcha: *</td>
<td>
<?
		$nr1 = rand(1, 5);
		$nr2 = rand(1, 4);
		$solution = $nr1 + $nr2;
		$_SESSION['captcha'] = md5($solution."asaltydog");
		$im = imagecreate(60,18);
		$white = imagecolorallocate ($im, 0xff,0xff,0xff);
		$green = imagecolorallocate ($im, 0xa4,0xbf,0x12);
		imagefilledrectangle($im,0,0,60,20,$white);
		imagestring($im, 3, 3, 1, $nr1 . " + " . $nr2 . " =", $green);
		ob_start (); 
		imagejpeg ($im);
		$image_data = ob_get_contents (); 
		ob_end_clean (); 
		$image_data_base64 = base64_encode ($image_data);
?>
		<img src="data:image/jpeg;base64,<?= $image_data_base64; ?>" >
<input type="text" size="20" name="captcha" value=""> example: <img style="border:1px" src="/pics/solve.png"> 
</td>
</tr>
<?
}
else { // trusted IP
		$_SESSION['captcha'] = false;
}
?>
<tr><td colspan="2"><input type="checkbox" onClick="document.getElementById('sub').disabled=!this.checked;"> I have read, understood and accepted the <a href="/privacy">privacy policy</a>.</td></tr>
<tr>
<td>
<input type="hidden" name="l" value="1">
<input type="submit" id="sub" disabled="true" value="<? echo l('submit'); ?>">
</td>
</tr>
</table>
</form>
</div>

* = <? echo l('requiredfields') ?>

<p style="color:#666666">
<? echo l('disclaimer'); ?>
</p>

