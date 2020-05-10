
<?


if (!$_SESSION['uid']) {
echo "Sorry, you must be logged in to use this function.";
return 0;
}


?>
		<h1><? echo l('learncwatlcwo'); ?></h1>

<?

echo "<p>".l('courseintro1')."</p>\n";
echo "<p>".l('courseintro2')."</p>\n";
echo "<p>".l('courseintro3')."</p>\n";
echo "<p>".l('courseintro4')."</p>\n";

?>


<h2><? echo l('character') ?> K</h2>
<p>
<? echo l('clicktohearletter'); ?> <strong>K</strong>.
</p>

<? player("KKKKKKKK ", $_SESSION['player'], $_SESSION['cw_speed'], $_SESSION['cw_eff'], 0, 1, 1, 0); ?>

<h2><? echo l('character') ?> M</h2>
<p>
<? echo l('clicktohearletter'); ?> <strong>M</strong>.
</p>

<? player("MMMMMMMM ", $_SESSION['player'], $_SESSION['cw_speed'], $_SESSION['cw_eff'], 0, 2, 1, 0); ?>

<?
echo "<p>".l('courseintro5')."</p>\n";
echo "<p>".l('courseintro6')."</p>\n";
echo "<p>".l('courseintro7')."</p>\n";
?>
<p><a href="/courselesson">
<? echo l('courseintro8'); ?>
</a></p>

<script>
// disable vvv and start delay in preview players
if (pa[1]) {
    pa[1].enablePS(false);
    pa[1].setStartDelay(0.1);
}
if (pa[2]) {
    pa[2].enablePS(false);
    pa[2].setStartDelay(0.1);
}
</script>
