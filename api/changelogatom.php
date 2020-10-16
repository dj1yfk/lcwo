<?
header('Content-type: application/atom+xml');
echo '<?xml version="1.0" encoding="utf-8"?>';
include('../inc/definitions.php');
$myurl= BASEURL;
?>
 
<feed xmlns="http://www.w3.org/2005/Atom"> 
<?

$cl = file("../inc/changelog.php");
$lastdate = "0";
$text = "";
$cla = Array();
foreach ($cl as $c) {

    if (preg_match("/^(20[0-9]{2}-[0-9]{2}-[0-9]{2}):/", $c, $ma)) {
        $currdate = $ma[1];
    }
    else {
        $text .= $c;
    }

    if (preg_match("/^\s*$/", $c)) {
        if ($currdate) {
            $cla[$currdate] = $text;
        }
        $text = "";
    }
}

foreach ($cla as $date => $text) {
$x++;

/* Header */
if ($x == 1) {
?>
	<title>Learn CW Online Changelog</title>
	<link rel="self" href="<?=$myurl;?>/changelogatom.xml" />
	<link href="<?=$myurl;?>/" />
	<updated><?=$date;?>T12:00:00Z</updated>
	<author>
		<name>Fabian Kurz</name>
		<email>fabian@fkurz.net</email>
		<uri>http://fkurz.net/</uri>
	</author>
	<id><?=$myurl;?>/</id>
<?
}

$text = strip_tags($text);
$text = mb_convert_encoding($text, 'UTF-8', "ISO-8859-1");
$text = preg_replace_callback('/&#([0-9]+);/', create_function ( '$matches', 'return mb_convert_encoding(pack(\'n\', $matches[1]), \'UTF-8\', \'UTF-16BE\');') , $text);
$text = preg_replace("/&/", "&amp;", $text);

?>	
    <entry>
	<title type="html"><?=$text;?></title>
	  <link href="<?=$myurl;?>/changelog#<?=$date?>" />
	  <id><?=$myurl;?>/changelog#<?=$date;?></id>
	  <updated><?=$date;?>T12:00:00Z</updated>
	  <content><? echo "$date: $text"; ?></content>
	</entry>
<?
}

?>
 
</feed>
