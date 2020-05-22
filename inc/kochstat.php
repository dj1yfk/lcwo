<?


if (!$_SESSION['uid']) {
		echo "Sorry, you must be logged in to use this
		function.";
		return 0;
}

if (!$_SESSION['kochstat']['set']) {
	$_SESSION['kochstat']['lesson'] = "All";
	$_SESSION['kochstat']['set'] = true;
}

if ($_POST['lesson'] == "All" or (isint($_POST['lesson']) && $_POST['lesson'] < 41)) {
		$_SESSION['kochstat']['lesson'] = $_POST['lesson'];
}

?>





<?
	for ($o = 0; $o < 41; $o++) {
		$min[$o] = 999;
	}

	$gc = mysqli_query($db,"select `lesson`,
	`accuracy`, `time` from	lcwo_lessonresults where
	`uid`='".$_SESSION['uid']."' order by `time`");
	
	while ($c = mysqli_fetch_object($gc)) {
			$count++;
			#for the graph
			if ($_SESSION['kochstat']['lesson'] == "All") {
				$accuracy[$count] = $c->accuracy;
				$lesson[$count] = $c->lesson;
			}
			elseif ($c->lesson == $_SESSION['kochstat']['lesson']) {
				$count2++;
				$accuracy[$count2] = $c->accuracy;
				$lesson[$count2] = $c->lesson;
			}
			# for the table
			if (!$first[$c->lesson]) {
				$first[$c->lesson] = $c->time;
			}
			$last[$c->lesson] = $c->time;
			$att[$c->lesson]++;
			if ($min[$c->lesson] > $c->accuracy) {
				$min[$c->lesson] = $c->accuracy;
			}
			if ($max[$c->lesson] < $c->accuracy) {
				$max[$c->lesson] = $c->accuracy;
			}
			$avg[$c->lesson] += $c->accuracy;
	}

?>


<h1><? echo l('kochstats'); ?></h1>

<?

$width = 600;
$height = 250;

graph($width, $height, $accuracy, array(0,100), false,
array(9,19), $lesson, "koch", array('Accuracy', 'Lesson', '%',
''));


?>

<img src="/img/koch<? echo imgurl($_SESSION['uid']); ?>.gif">
<table>
<tr>
<td>
<? echo l('zeroaccuracynote') ?>
</td>
<td>
<? echo l('resultsfromlesson') ?>
</td>
<td>
<form action="/kochstat" method="POST">
<select name="lesson" size="1" onchange="this.form.submit();">
<option <? if ($_SESSION['kochstat']['lesson'] == "All") { echo "selected";}  ?>>All</option>
<?
	for ($i = 1; $i < 41; $i++) {
			if ($i == $_SESSION['kochstat']['lesson']) {
				$x = " selected";
			}		
			else {
				$x = "";
			}
			echo "<option$x>$i</option>";
	}
?>
</select>
</form>
</td>
</tr>
</table>



<h2><? echo l('lessons').", ".l('accuracy'); ?></h2>
<table width="50%">
<tr><th><? echo l('lesson') ?></th><th><? echo l('attempts') ?></th><th colspan="3">
<? echo l('accuracy') ?></th><th><? echo l('timespent'); ?></th></tr>
<tr><th>&nbsp;</th><th></th><th><?=l("min")?></th><th><?=l("max");?></th><th><?=l("avg")?></th><th>(<? echo l('days'); ?>)</th></tr>
<?
	for ($i=0; $i < 41; $i++) {
		if (!$first[$i]) { continue; };
		
		$days = days(da($first[$i]), da($last[$i]));

		echo "<tr><td>$i</td>
		<td>".$att[$i]."</td><td>".$min[$i]."</td><td>".$max[$i]."</td><td>".
		round($avg[$i]/$att[$i], 1)."</td><td>$days</td></tr>";
		
	}
	
?>

</table>



<?


function days ($from, $to) {	# from - to
	$f['year'] = mb_substr($from, 0, 4);
	$f['month'] = mb_substr($from, 5, 2);
	$f['day'] = mb_substr($from, 8, 2);
	$f['hr'] = mb_substr($from, 11, 2);
	$f['min'] = mb_substr($from, 14, 2);

	$t['year'] = mb_substr($to, 0, 4);
	$t['month'] = mb_substr($to, 5, 2);
	$t['day'] = mb_substr($to, 8, 2);
	$t['hr'] = mb_substr($to, 11, 2);
	$t['min'] = mb_substr($to, 14, 2);

	$from = mktime(0,$f['min'],$f['hour'], $f['month'], $f['day'], $f['year']);
	$to = mktime(0,$t['min'],$t['hour'], $t['month'], $t['day'], $t['year']);

	$days = intval(($to - $from)/86400);

	if ($days ==0) {
		$days = 1;
	}

	return $days;	

}




?>
<p><a href="/main"><? echo l('home') ?></a></p>
