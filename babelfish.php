<?
header("Content-Type: text/html; charset=utf-8");
session_start();

if (!$_SESSION['uid']) {
	echo "Please log in first.";
	exit(1);
}

include("inc/connectdb.php");
require("inc/functions.php");
require("inc/definitions.php");

/* permissions */

$perm = array(
#		'ar' => array('admin', 'yi3sra', 'YI3SRA'),
#		'cs' => array('admin', 'ok2ien', 'JerryA'),
#		'da' => array('admin', 'VK2KJJ', 'oz8agb', 'OZ8AGB'),
		'de' => array('admin'),
		'en' => array('admin'),
		'es' => array('admin', 'EA1QL'),
#		'fr' => array('admin', 'gagadget'),
#		'hr' => array('admin', '9A2JK', '9a2jk'),
#		'it' => array('admin', 'iz1crr'),
		'ja' => array('admin', 'Lucifuge'),
		'nl' => array('admin', 'ON7GZ'),
#		'pl' => array('admin', 'vk2oe', 'sq6jnx', 'SQ6JNX'),
#		'pt' => array('admin', 'CT1ILT', 'ct1drb'),
#		'ro' => array('admin', 'yo4px', 'Grunberg'),
#		'ru' => array('admin', 'yl3bu', 'YL3BU', 'RD1A', 'rd1a'),
#		'sl' => array('admin', 's55o', 'S55O'),
#		'sv' => array('admin', 'julle', 'Giuliano', 'sm2wmv', 'SM2WMV', 'cevinchurch'),
#		'bs' => array('DL4CC', 'dl4cc', 'admin'),
#		'fi' => array('DM1TT', 'dm1tt', 'OH5GEV', 'oh5gev','admin'),
#		'br' => array('py3it', 'PY3IT', 'PY3MAY', 'py3may', 'admin'),
#		'gr' => array('admin', 'vicky', 'Vicky', 'sv2kbs', 'SV2KBS'),
#		'ca' => array('admin', 'eb3cml', 'EB3CML', 'EB3MA', 'eb3ma'),
#		'hu' => array('admin', 'ha4yf', 'ha5ot', 'ha5bww', 'HA4YF', 'HA5OT', 'HA5BWW'),
#		'gl' => array('admin', 'ec3dr', 'EC3DR'),
#		'ms' => array('admin', '9m2rie', '9M2RIE'),
#		'th' => array('admin', 'hs8jyx', 'HS8JYX'),
#		'tr' => array('admin', 'TA2RX', 'ta2rx'),
		'ko' => array('admin',  'HL5KY'),
#		'zh' => array('admin', 'fdl'),
#		'cn' => array('admin', 'fdl'),
#		'sk' => array('admin', 'ok8ok', 'OK8OK'),
#		'sr' => array('admin', 'YU1QRP', 'yu1qrp', 'YU0W', 'yu0w'),
#		'bg' => array('admin', 'LZ3AI', 'lz3ai'),
#		'eo' => array('admin', 'esperanto14'),
#		'uk' => array('admin', 'UT4UQN'),
#		'vi' => array('admin', 'W6TN', 'w6tn'),
#		'no' => array('admin', 'LA6VQ', 'la6vq', 'la6ala', 'LA6ALA'),
#		'id' => array('admin', 'mahally'),
#		'si' => array('admin', 'TekCroach'),
		'he' => array('admin', 'ashuber')
);


if ($_POST['ajax']) {
	ajaxedit();
}
else {
	babelfishnormal();
}



function ajaxedit () {
	global $perm;
	global $langs;
    global $db;

	$name = escc($_POST['k']);
	$text = escc($_POST['v']);
	$lang = $_POST['l'];

	if ($lang == "0") {
		$lang = $_SESSION['lang'];
	}

	if ($_SESSION['username'] == "admin" or in_array($_SESSION['username'], $perm[$lang])) {
			
			/* check if translated item exists */
			$x = mysqli_query($db, "select count(*) from lcwo_texts where name='$name' and lang='".$lang."'");	
			if (!$x) {
				echo "die".mysqli_error();
				exit(1);
			}
			$k = mysqli_fetch_row($x);
			if ($k[0]) {
				$x = mysqli_query($db, "UPDATE `lcwo_texts` set text='$text' where `name`='$name' and lang='".$lang."'");
				/* if english (master language) mark all others as
				* possibly old */
				if ($_SESSION['lang'] == 'en') { 
					$x = mysqli_query($db, "UPDATE `lcwo_texts` set old='1' where `name`='$name' and lang != 'en';");
				}
				/* otherwise, mark this one as not OLD anymore */
				else {
					$x = mysqli_query($db, "UPDATE `lcwo_texts` set old='0' where `name`='$name' and lang='$lang';");
				}
			}
			else {	/* new snippet */
				$x = mysqli_query($db, "INSERT INTO `lcwo_texts` (text, name, lang) values ('$text', '$name', '".$lang."');");
			}
			bumpversion();

	}
	else {
			echo "Not allowed.";
	}
}



function babelfishnormal () {
		global $perm;
		global $langs;
        global $db;
?>
<html>
<head>
<title>babelfish</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>

<? include('babel.js'); ?>

<h1>
<a href="babelfish.php">lcwo babelfish (click to reload)</a> --
<a href=".">back to LCWO</a> 
</h1>
<div style="color:#ff0000">New: Texts are submitted in the
&quot;background&quot; now, no
more full page-reloads (which were usually slow). Please let me know if you
face any problems. Tnx! <pre>-- Fabian DJ5CW</pre></div>


<?
if ($_SESSION['showlangs']['init'] == 0) {
	foreach ($langs as $l) {
		$_SESSION['hidelangs'][$l] = 1;
	}
	$_SESSION['showlangs']['en'] = 1;
	$_SESSION['hidelangs']['en'] = 0;
	$_SESSION['showlangs'][$_SESSION['lang']] = 1;
	$_SESSION['hidelangs'][$_SESSION['lang']] = 0;
	$_SESSION['showlangs']['init'] = 1;
}

if (in_array($_GET['hide'], $langs)) {
	$_SESSION['hidelangs'][$_GET['hide']] = 1;
	$_SESSION['showlangs'][$_GET['hide']] = 0;
}

if (in_array($_GET['show'], $langs)) {
	$_SESSION['showlangs'][$_GET['show']] = 1;
	$_SESSION['hidelangs'][$_GET['show']] = 0;
}

if (!isset($_SESSION['babelorder'])) {
	$_SESSION['babelorder'] = 'ORDER BY name asc';
}

if ($_GET['babelorder'] == 'n') {
	$_SESSION['babelorder'] = 'ORDER BY name asc ';
}
else if ($_GET['babelorder'] == 't') {
	$_SESSION['babelorder'] = 'ORDER BY id asc';
}

?>


<p><b>Hidden languages: </b>
<?
foreach ($langs as $hl) {
	if ($_SESSION['hidelangs'][$hl] == 1) {
		echo "<a href='?show=$hl'>$hl</a>  ";
	}
}
?>
(click to display)<br>
<b>Displayed languages:</b>
<?
foreach ($langs as $hl) {
	if ($_SESSION['showlangs'][$hl] == 1) {
		echo "<a href='?hide=$hl'>$hl</a>  ";
	}
}
?>
(click to hide)
</p>
<p><strong>Order items by:</strong> 
<a href="?babelorder=n">Name</a> -
<a href="?babelorder=t">Time added</a> (approximate)
</p>

<?

	if ($_POST['new']) {
		$name = $_POST['name'];
		if (!preg_match("/^[a-zA-Z0-9]{1,32}$/", $name)) {
			echo "Invalid name $name!<br>";
			exit(1);
		}
        $get = 	mysqli_query($db, "SELECT `name` from lcwo_texts where `name`='".$_POST['name']."'");
		$gn = mysqli_fetch_object($get);
		if ($gn->name == $name) {
			echo "Error: $name already exists.<br>";
			exit(1);
		}
		$text = escc($_POST['en']);
		$i = mysqli_query($db, "INSERT INTO `lcwo_texts` (`name`, `lang`, `text`, `old`) VALUES ('$name', 'en', '$text', 0)");
		if (!$i) {
			echo "Error: ".mysqli_error();
		}
		else {
			echo "added $name -> $text";
			bumpversion();
		}

	
	}	# POST[new]

	else if ($_POST['namelang']) {
		list($name, $lang)= explode('-', $_POST['namelang']);
		$text = escc($_POST['text']);
	
		/* check permissions */

		$allowed = 1;
		if (!in_array($_SESSION['username'], $perm[$lang])) {
			echo "<strong>Sorry, your user name ($_SESSION[username]) is
			not allowed to edit the language $lang. If this is an
			error, please ask Fabian to change your
			permissions.</strong>";
			$allowed = 0;
		}	

		if ($allowed) {
		
		$x = mysqli_query($db, "SELECT `id` from `lcwo_texts` where `lang` = '$lang' and `name`='$name'");
		$g = mysqli_fetch_object($x);
		
		if ($g->id) {				# exists -> update
			$x = mysqli_query($db, "UPDATE `lcwo_texts` set text='$text' where `id`='$g->id'");
			/* if english (master language) mark all others as
			* possibly old */
			if ($lang == 'en') { 
				$x = mysqli_query($db, "UPDATE `lcwo_texts` set old='1' where `name`='$name' and lang != 'en';");
			}
			/* otherwise, mark this one as not OLD anymore */
			else {
				$x = mysqli_query($db, "UPDATE `lcwo_texts` set old='0' where `id`='$g->id';");
			}
			
		}
		else {
			$x = mysqli_query($db, "INSERT INTO `lcwo_texts` (`name`, `lang`, `text`, `old`) VALUES ('$name', '$lang', '$text', 0)");
		}
		if (!$x) {
			echo "Error: ".mysqli_error();
		}
		else {
			echo "updated $name / $lang -> $text";
			bumpversion();
		}

		} # if $allowed
		
	}

	
?>

<?

if (is_numeric($_GET['limit']) && ($_GET['limit'] >= 0)) {
	$_SESSION['limit'] = $_GET['limit'];	
	$limit = $_SESSION['limit'];
	$number = 20;
}
else if ($_GET['limit'] == 'u' || $_SESSION['limit'] == 'u') {
	$_SESSION['limit'] = 'u';
	$limit = 0;
	$number = 10000;
}
else if (isset($_SESSION['limit']) && $_SESSION['limit'] != 'u') {
	$limit = $_SESSION['limit'];
	$number = 20;
}
else {
	$_SESSION['limit'] = 0;
	$limit = 0;
	$number = 20;
}

# $get = mysql_query("SELECT count(distinct name) as k from lcwo_texts");
# $g = mysql_fetch_object($get);

#if ($g->k > 20) {
#	echo "<p>Show 20 entries starting from: ";
#	for ($i = 0; $i < $g->k; $i += 20) {
#			if ($_SESSION[limit] == $i) {
#				echo " <b>";
#			}
#			echo " <a href='babelfish.php?limit=$i'>$i</a> ";
#			if ($_SESSION[limit] == $i) {
#				echo "</b> ";
#			}
#	}
#}

echo "<p><strong>Filter:</strong> <a href='babelfish.php?limit=0'>Show all.</a> - <a href='babelfish.php?limit=u'>Show only untranslated or changed items.</a></p>";	


$names = array();
$langs = array();

$get = mysqli_query($db, "SELECT distinct name from lcwo_texts ".$_SESSION['babelorder']);
while ($g = mysqli_fetch_object($get)) {
		array_push($names, $g->name);
}

$get = mysqli_query($db, "SELECT distinct lang from lcwo_texts;");
while ($g = mysqli_fetch_object($get)) {
		if ($_SESSION['showlangs'][$g->lang] == 1 ) {
			array_push($langs, $g->lang);
		}
}

# array_multisort($names);

?>

<table border=1>
<tr>
<th>name</th>
<?
for ($i=0; $i < count($langs); $i++) {
	echo "<th>".$langs[$i]."</th>";
	$langsindex[$langs[$i]] = $i;
}
?>
</tr>

<?

$cnt = 0;

foreach ($names as $n) {
	$get = mysqli_query($db, "SELECT `lang`, `text`, `old` from lcwo_texts where `name`='$n'");

	$get2 = mysqli_query($db, "SELECT `val` from lcwo_textindex where `key`='$n'");

	if ((!$get) || (!$get2)) {
		echo mysqli_error();
	}

	unset($wherethis);

	$wherethis = mysqli_fetch_object($get2);

	$ttext = array();
	while ($g = mysqli_fetch_object($get)) {
		$ttext[$langsindex[$g->lang]] = $g->text;
		$old[$langsindex[$g->lang]] = $g->old;
	}

	# Only show this row IF any of the selected languages is
	# actually OLD or EMPTY

	$showline = 0;
	
	if ($_SESSION['limit'] == 'u') {
		for ($i=0; $i < count($langs); $i++) {
			if ($ttext[$i] == '' or $old[$i]) {
				$showline = 1;
			}
		}
	}
	else {
		$showline = 1;
	}

	if ($showline) {
		$cnt++;

		echo "\n\n<tr><td width='15%'>$n<br>$cnt<br>(used: $wherethis->val)</td>";	
	
		for ($i=0; $i < count($langs); $i++) {
				echo "<td><form action=\"babelfish.php\" method=POST>\n";
	
				echo "<textarea cols=25 rows=5 name=text>".$ttext[$i]."</textarea><br>\n";
				echo "<input type='hidden' name='namelang' value='$n-".$langs[$i]."'>\n";
				echo "<input type='submit' onclick='babeltx(this.form.namelang.value, this.form.text.value); return false;' value=\"Submit\">\n";
				if ($old[$i]) { echo "<b><font color='#ff0000'>MAYBE OLD?</font></b>"; 
				}
				echo "<span id='babelresponse-$n-".$langs[$i]."'></span></form></td>\n";
		}
		echo "</tr>\n";
		}

	}
?>
</table>

<hr>

<h2>New item</h2>
<form action="babelfish.php" method="POST">
name: <input type="text" name="name"> &nbsp; &nbsp;
en: <input type="text" name="en"><br>
<input type="hidden" name="new" value="1">
<input type="submit">
</form>


<?






?>



</body>
</html>


<?
} // normal


function escc ($str) {
global $db;
return mysqli_real_escape_string($db, stripslashes($str));
}

function bumpversion() {
        global $db;
		if ($bumped == 1) {
			return 0;
		}
		$get = 	mysqli_query($db, "SELECT `val` from lcwo_config where `key`='localeversion'");
		$gn = mysqli_fetch_object($get);
		$verr = $gn->val;
		$verr += 1;
		$x = mysqli_query($db, "UPDATE `lcwo_config` set `val`='$verr' where `key`='localeversion';");
	
		if ($x) {
			echo "OK! Language revision: $verr";
		}
		else {
			echo "Error: Couldn't update locale!<br>";
		}
		$bumped = 1;
}











?>
