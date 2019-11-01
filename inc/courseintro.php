
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

<? player("KKKKKKKK ", $_SESSION['player'], $_SESSION['cw_speed'], $_SESSION['cw_eff'], 0, 0, 1, 0); ?>

<h2><? echo l('character') ?> M</h2>
<p>
<? echo l('clicktohearletter'); ?> <strong>M</strong>.
</p>

<? player("MMMMMMMM ", $_SESSION['player'], $_SESSION['cw_speed'], $_SESSION['cw_eff'], 0, 0, 1, 0); ?>

<?
echo "<p>".l('courseintro5')."</p>\n";
echo "<p>".l('courseintro6')."</p>\n";
echo "<p>".l('courseintro7')."</p>\n";
?>
<p><a href="/courselesson">
<? echo l('courseintro8'); ?>
</a></p>

<div class="vcsid">$Id: courseintro.php 34 2010-09-02 19:59:52Z dj1yfk $</div>

