<?
header('Content-type: application/atom+xml');
echo '<?xml version="1.0" encoding="utf-8"?>';
include('connectdb.php');
include('inc/definitions.php');
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
		<name><?=ADMINNAME;?></name>
		<email><?=ADMINMAIL;?></email>
		<uri><?=ADMINURL;?></uri>
	</author>
	<id><?=$myurl;?>/forum</id>

<?
}
		$text = strip_tags($r->text); 
#		$text = htmlentities($text, ENT_COMPAT, "ISO8859-1");
		$text = preg_replace("/&/", "&amp;", $text);

$text = preg_replace_callback('/&#([0-9]+);/', create_function ( '$matches', 'return mb_convert_encoding(pack(\'n\', $matches[1]), \'UTF-8\', \'UTF-16BE\');') , $text);

		$text = preg_replace("/\[quote=[a-zA-Z0-9]+\].+\[\/quote\]/", "", $text);
		$text = preg_replace("/\[(\/)?[a-zA-Z0-9=]+\]/", "", $text);
		$text = preg_replace('/(\r\n)/', " &lt;br&gt;", $text);
#		$topic = htmlentities($r->topic, ENT_COMPAT, "ISO8859-1");
#		$topic = preg_replace("/&/", "&amp;", $topic);
		$topic = $r->topic;

$topic = preg_replace_callback('/&#([0-9]+);/', create_function ( '$matches', 'return mb_convert_encoding(pack(\'n\', $matches[1]), \'UTF-8\', \'UTF-16BE\');') , $topic);

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
	<title type="html"><?=$topic;?></title>
	  <link href="<?=$myurl;?>/forum/<?=$r->tid?><?=$anchor;?>" />
	  <id><?=$myurl;?>/forum/<?=$r->tid?><?=$anchor;?></id>
	  <updated><?=$date;?></updated>
	  <content type="html"><?=$text;?></content>
	</entry>
<?
}
?>
 
</feed>




<?
	function da ($in) {
    if (mb_strlen($in) == 14) {
        $nd = mb_substr($in, 0, 4).'-';
        $nd .= mb_substr($in, 4, 2).'-';
        $nd .= mb_substr($in, 6, 2).' ';
        $nd .= mb_substr($in, 8, 2).':';
        $nd .= mb_substr($in, 10, 2).':';
        $nd .= mb_substr($in, 12, 2);
		$nd = da($nd);
		return $nd;
    } 
		$in = preg_replace("/\s+/", "T", $in);
		$in = $in."Z";
		return $in;
	}
?>
