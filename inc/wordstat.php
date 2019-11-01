<?

if (!$_SESSION['uid']) {
		echo "Sorry, you must be logged in to use this
		function.";
		return 0;
}

?>

<?

	$gc = mysqli_query($db,"select `max`, `score`
   	from lcwo_wordsresults where
	`uid`='".$_SESSION['uid']."' order by nr");

	$maxspeed = 0;	
	while ($c = mysqli_fetch_object($gc)) {
			$count++;
			$score[$count] = $c->score;
			$maxpts = max($maxpts, $c->score);
			$max[$count] = $c->max;
			$maxspeed = max($maxspeed, $c->max);
	}

?>


<h1><? echo l('wordstats'); ?></h1>

<?

$width = 600;
$height = 250;

graph($width, $height, $score, array(0,max($score)), 
						$max, array(0,max($max)),
						array(), "words", array("Score",
						"Speed", '', l(wpm)));


?>

<img src="/img/words<? echo imgurl($_SESSION[uid]); ?>.gif">

<p><? echo l('bestscore').": ".$maxpts." ".l('points')." /
".$maxspeed."
".l('wpm'); ?></p>

<p><a href="/main"><? echo l('home') ?></a></p>
<div class="vcsid">$Id: wordstat.php 143 2012-01-10 21:34:26Z dj1yfk $</div>

