<table width="100%">
<tr><th class="tborder"><? echo l('login') ?></th></tr>
<tr><td class="tborder">

<br>

<div align="center">
<form action="/dologin" method="POST">
<table class="login-table">
	<tr>
		<td style="width:20%"><? echo l('username'); ?>: </td>
		<td>
			<input type="text" size="8" name="username">
		</td>
	</tr>
	<tr>
		<td style="width:20%"><? echo l('password'); ?>: </td>
		<td>
			<input type="password" size="8" name="password">
		</td>
	</tr>

</table>

<input type="submit" value="<? echo l('loginsubmit',1) ?>">
</form>
</div>
</td></tr>
<tr>
<td class="tborder">
	<br>
	<div align="center">
	<a href="/lostpassword"><? echo l('forgotpassword') ?></a> - 
		<strong><a href="/signup"><? echo l('register'); ?></a></strong>
	</div>
	<br>
</td>
</tr>
</table>


