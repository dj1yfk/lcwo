<?

# Update user's last activity
if ($_SESSION['uid']) {
    mysqli_query($db, "INSERT INTO lcwo_online (UID, LASTACTIVE) VALUES
			(".$_SESSION['uid'].", NULL)
			ON DUPLICATE KEY UPDATE lastactive=NULL;");
}

mysqli_query($db, "delete from lcwo_online where lastactive < (NOW() - INTERVAL ".TIMEOUT." MINUTE);");

$getonlineusers = mysqli_query($db, "select distinct lcwo_users.username, 
lcwo_users.name,
lcwo_online.uid
FROM lcwo_users 
INNER JOIN lcwo_online 
WHERE 
lcwo_users.id = lcwo_online.uid
AND
lcwo_users.hide = 0
AND
lcwo_online.lastactive > 
(NOW() - interval ".
TIMEOUT." MINUTE)
ORDER BY username asc;
");

$onlineusers=0;
$meloggedin = 0;

$output = "";
while ($usid = mysqli_fetch_object($getonlineusers)) {
		$onlineusers++;
		$cleanname = preg_replace('/"/', '&quot;', $usid->name);
		$output .= "<a href=\"/profile/$usid->username\" title=\"".$cleanname."\">".$usid->username."</a> ";
}

if (!$onlineusers) {
	$output = l('nobodyloggedin');
}


?>



<table width="90%">
<tr><th class="tborder"><? echo l('whoisonline') ?> (<?=$onlineusers;?>)</th></tr>
<tr><td class="tborder">
<br>
<div align="center">
<?=$output;?>
</div>
<br>
</td></tr>
</table>
