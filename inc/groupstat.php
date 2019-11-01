<h1><? echo l('groupstats'); ?></h1>
<?

if (!$_SESSION['uid']) {
		echo "Sorry, you must be logged in to use this
		function.";
		return 0;
}

?>

<?

foreach (array('letters', 'figures', 'mixed', 'custom') as $what) {

	$gc = mysqli_query($db,"select `accuracy`, `eff`
   	from lcwo_groupsresults where
	`uid`='$_SESSION[uid]' and mode='$what' order by nr");

	unset($accuracy, $eff);
	$count = 0;
	$maxeff = 0;
	$maxeff90 = 0;
	
	while ($c = mysqli_fetch_object($gc)) {
			$count++;
			$accuracy[$count] = $c->accuracy;
			$eff[$count] = $c->eff;
			$maxeff = max($maxeff, $c->eff);
			if ($c->accuracy >= 90) {
				$maxeff90 = max($maxeff90, $c->eff);
			}
	}

	$width = 600;
	$height = 250;

	graph($width, $height, $accuracy, array(0,100), 
						$eff, array(0,$maxeff),
						array(), "groups$what",
						array('Accuracy','Speed','%','WpM'));


?>
<h2><? echo l($what) ?></h2>
<img src="/img/groups<? echo $what.imgurl($_SESSION['uid']); ?>.gif">
<p><? echo l('fastest90acc').": ".$maxeff90." ".l('wpm'); ?></p>

<?

} # foreach

?>


<p>
<? echo l('zeroaccuracynote') ?>
</p>

<p><a href="/main"><? echo l('home') ?></a></p>

<div class="vcsid">$Id: groupstat.php 143 2012-01-10 21:34:26Z dj1yfk $</div>


