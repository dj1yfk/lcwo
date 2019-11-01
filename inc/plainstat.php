<?

if (!$_SESSION['uid']) {
		echo "Sorry, you must be logged in to use this
		function.";
		return 0;
}

?>

<?

	$gc = mysqli_query($db,"select `accuracy`, `eff`
   	from lcwo_plaintextresults where `uid`='".$_SESSION['uid']."' order by nr");

	$maxspeed = 0;	
	while ($c = mysqli_fetch_object($gc)) {
			$count++;
			$accuracy[$count] = $c->accuracy;
			$max[$count] = $c->eff;
			$maxspeed = max($maxspeed, $c->eff);
	}

?>


<h1><? echo l('plainstats'); ?></h1>

<?

$width = 600;
$height = 250;

graph($width, $height, $accuracy, array(0,100), 
						$max, array(0,max($max)),
						array(), "plain", array("Accuracy",
						"Speed", '%', l('wpm')));


?>

<img src="/img/plain<? echo imgurl($_SESSION['uid']); ?>.gif">

<p><? echo l('fastest90acc').": ".$maxspeed." ".l('wpm'); ?></p>

<p><a href="/"><? echo l('home') ?></a></p>
<div class="vcsid">$Id: plainstat.php 143 2012-01-10 21:34:26Z dj1yfk $</div>

