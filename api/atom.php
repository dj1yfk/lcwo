<?
header('Content-type: application/atom+xml');
echo '<?xml version="1.0" encoding="utf-8"?>';
include('../inc/connectdb.php');
include('../inc/definitions.php');
$myurl= BASEURL;
?>
 
<feed xmlns="http://www.w3.org/2005/Atom"> 
<?
$q = mysqli_query($db,"select `date`, `news`, `id` from lcwo_news order by id desc
limit 10");
while ($r = mysqli_fetch_row($q)) {
$x++;

/* Header */
if ($x == 1) {
?>
	<title>Learn CW Online News</title>
	<link rel="self" href="<?=$myurl;?>/atom.xml" />
	<link href="<?=$myurl;?>/" />
	<updated><?=$r[0];?>T12:00:00Z</updated>
	<author>
		<name>Fabian Kurz</name>
		<email>fabian@fkurz.net</email>
		<uri>http://fkurz.net/</uri>
	</author>
	<id><?=$myurl;?>/</id>
<?
}

$text = $r[1];
$text = strip_tags($text);
$text = mb_convert_encoding($text, 'UTF-8', "ISO-8859-1");
$text = preg_replace_callback('/&#([0-9]+);/', create_function ( '$matches', 'return mb_convert_encoding(pack(\'n\', $matches[1]), \'UTF-8\', \'UTF-16BE\');') , $text);
$text = preg_replace("/&/", "&amp;", $text);

?>	
    <entry>
	<title type="html"><? echo $r[0].": ".$text;?></title>
	  <link href="<?=$myurl;?>/news#<?=$r[2]?>" />
	  <id><?=$myurl;?>/news#<?=$r[2];?></id>
	  <updated><?=$r[0];?>T12:00:00Z</updated>
	  <summary type="html"><? echo $text;?></summary>
	</entry>
<?
}

?>
 
</feed>
