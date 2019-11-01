<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tbody>
	<tr>
	<td class="menuText" width="100%">
		  <img style="vertical-align:middle" src="/pics/menusep2.png" height="19" width="16" alt=":">
		  <a class="mLink" href="/"><? echo l('home') ?></a>
		  <img style="vertical-align:middle" src="/pics/menusep2.png" height="19" width="15" alt=":">
		  <a class="mLink" href="/users"><? echo l('userlist') ?></a>
		  <img style="vertical-align:middle" src="/pics/menusep2.png" height="19" width="15" alt=":">
		  <a class="mLink" href="/highscores"><? echo l('highscores') ?></a>
		  <img style="vertical-align:middle" src="/pics/menusep2.png" height="19" width="15" alt=":">
		  <a class="mLink" href="/forum"><? echo l('forum')." ".privmsgcount();?></a>
		  <img style="vertical-align:middle" src="/pics/menusep2.png" height="19" width="15" alt=":">
		  <a class="mLink" href="/usergroups"><? echo l('usergroups') ?></a>
		  <img style="vertical-align:middle" src="/pics/menusep2.png" height="19" width="15" alt=":">
		  <a class="mLink" href="/about"><? echo l('about') ?></a>
		  <img style="vertical-align:middle" src="/pics/menusep2.png" height="19" width="15" alt=":">
		  <? if ($_SESSION['uid']) { ?>
		  <a class="mLink" href="/logout"><?
				  echo l('logout')." (".$_SESSION['username']; ?>)</a>
		  <img style="vertical-align:middle" src="/pics/menusep2.png"  height="19" width="15" alt=":">
			<? } ?>
		  
	    </td>
	</tr>
</tbody></table>

<br>
