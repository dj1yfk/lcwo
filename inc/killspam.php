<?
# XXX does this still work? 2018-10-07
# Logged in user or user = OK
if ($_SESSION['uid']) {
	return;
}

$likely_spam = 0;

# suspicious user who came in via http://lcwo.net/?p=welcome&hl=pt
# within the last 120 seconds.

if (is_suspicious_ip($_SERVER['REMOTE_ADDR'])) {
		if (in_array($_SERVER['HTTP_REFERER'], array(
				"http://lcwo.net/signup"
		)
		)) {
				$likely_spam = 1;
		}

}
else if ($_SERVER['HTTP_REFERER'] == "http://lcwo.net/?p=welcome&hl=pt") {
		add_suspicious_ip($_SERVER['REMOTE_ADDR']);
}

function is_suspicious_ip($ip) {
		global $db;
		$q = mysqli_query($db, "select count(*) as c from lcwo_spamips where ip='$ip' and lastactive > (NOW() - interval 120 second)"); 
		$r = mysqli_fetch_object($q);
		return $r->c;
}

function add_suspicious_ip($ip) {
		global $db;
		$q = mysqli_query($db, "insert into lcwo_spamips (ip, lastactive) values ('$ip', NULL) ON DUPLICATE KEY UPDATE lastactive=NULL;;");
		if (!$q) {
				echo mysqli_error();
		}	
}



?>
