<?
header('Content-type: application/atom+xml');
echo '<?xml version="1.0" encoding="utf-8"?>';
include('../inc/connectdb.php');
include('../inc/definitions.php');
$myurl = BASEURL;
?>
 


	<?
$q = mysqli_query($db,"select lcwo_users.username, lcwo_posts.time,
lcwo_posts.topic, lcwo_posts.uid, lcwo_posts.tid, lcwo_posts.id,
lcwo_posts.text, lcwo_posts.isreply from lcwo_users INNER JOIN lcwo_posts
ON lcwo_users.id = lcwo_posts.uid where lcwo_posts.forumid = 0 and lcwo_posts.approved = 1 order by id desc
limit 10");

while ($r = mysqli_fetch_object($q)) {
$x++;

if ($x == 1) {
?>
<feed xmlns="http://www.w3.org/2005/Atom"> 
	<title>Learn CW Online Forum</title>
	<link rel="self" href="<?=$myurl;?>/forumatom.xml" />
	<link href="<?=$myurl;?>/forum" />
	<updated><? echo da($r->time);?></updated>
	<author>
		<name>Fabian Kurz</name>
		<email>fabian@fkurz.net</email>
		<uri>http://fkurz.net/</uri>
	</author>
	<id><?=$myurl;?>/forum</id>

<?
}
		$text = strip_tags($r->text); 
		$text = mb_convert_encoding($text, 'UTF-8', "ISO-8859-1");
$text = preg_replace_callback('/&#([0-9]+);/', create_function ( '$matches', 'return mb_convert_encoding(pack(\'n\', $matches[1]), \'UTF-8\', \'UTF-16BE\');') , $text);
		$text = preg_replace("/&/", "&amp;", $text);
		$text = preg_replace("/</", "&lt;", $text);
		$text = preg_replace("/>/", "&gt;", $text);
		$text = preg_replace("/\[quote=[a-zA-Z0-9]+\].+\[\/quote\]/", "", $text);
		$text = preg_replace("/\[(\/)?[a-zA-Z0-9=]+\]/", "", $text);

		$topic = $r->topic;
		$topic = mb_convert_encoding($topic, 'UTF-8', "ISO-8859-1");
$topic = preg_replace_callback('/&#([0-9]+);/', create_function ( '$matches', 'return mb_convert_encoding(pack(\'n\', $matches[1]), \'UTF-8\', \'UTF-16BE\');') , $topic);

		$topic = preg_replace("/&/", "&amp;", $topic);
		$topic = $r->username.": ".$topic;
		$date = da($r->time);
		if ($r->isreply) {
			$anchor = "#post$r->id";
		}
		else {
			$anchor = "";
		}
?>	
    <entry>
	<title><?=$topic;?></title>
	  <link href="<?=$myurl;?>/forum/<?=$r->tid?><?=$anchor;?>" />
	  <id><?=$myurl;?>/forum/<?=$r->tid?><?=$anchor;?></id>
	  <updated><?=$date;?></updated>
	  <content><?=$text;?></content>
	</entry>
<?
}
?>
 
</feed>




<?
	function da ($in) {
    if (strlen($in) == 14) {
        $nd = substr($in, 0, 4).'-';
        $nd .= substr($in, 4, 2).'-';
        $nd .= substr($in, 6, 2).' ';
        $nd .= substr($in, 8, 2).':';
        $nd .= substr($in, 10, 2).':';
        $nd .= substr($in, 12, 2);
		$nd = da($nd);
		return $nd;
    } 
		$in = preg_replace("/\s+/", "T", $in);
		$in = $in."Z";
		return $in;
	}
?>
