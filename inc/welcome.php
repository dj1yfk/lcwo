<h1>
<? echo l('welcometolcwo'); ?>
</h1>
<p> <? echo l('welcometolcwo1'); ?> </P>


<p><a href="/signup"><? echo l('signup') ?></a> 
<? echo l('signup2') ?>
</p>

<h2><? echo l('features') ?></h2>
<p>- <? echo l('kochmethodcourse') ?><br>
- <a href="/highscores"><? echo l('highscores') ?></a> &mdash; <? echo l('compareresults');?>
<br>
- <? echo l('speedpractice') ?> (<?echo l('codegroups').", ".
l('plaintexttraining').", ".l('callsigntraining').",
".l('wordtraining') ?>)<br>
- <a href="/download"><? echo l('downloadpracticefiles')."</a> (".l('download').")"; ?><br>
- <a href="/text2cw"><? echo l('text2cw') ?></a> (<? echo l('doesntrequirelogin'); ?>)<br>
- <a href="/forum"><? echo l('forum'); ?></a> <? echo l('fordiscussion'); ?><br>
- <a href="/usergroups"><? echo l('usergroups') ?></a><br>
- <a href="https://www.waedc.de/" title="Worked All Europe DX Contest">WAE</a> <? echo l('qtctraining'); ?><br>
- <? echo l('moretocome') ?>
</p>


<?

include("inc/news.php");

?>
