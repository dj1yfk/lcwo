<div class="menu-icon" onclick="toggleMenu('.menuText')">
		<div class="placeholder-menu">Navigation</div>
		<div class="menu-lines">☰</div>
	</div>
<nav class="menuText" width="100%">
		  <div>
			<img style="vertical-align:middle" src="/pics/menusep2.png" height="19" width="16" alt=":">
			<a class="mLink" href="/"><? echo l('home') ?></a>
		  </div>
		  <div>
			<img style="vertical-align:middle" src="/pics/menusep2.png" height="19" width="15" alt=":">
			<a class="mLink" href="/users"><? echo l('userlist') ?></a>
		  </div>
		  <div>
			<img style="vertical-align:middle" src="/pics/menusep2.png" height="19" width="15" alt=":">
			<a class="mLink" href="/highscores"><? echo l('highscores') ?></a>
		  </div>
		  <div>
			<img style="vertical-align:middle" src="/pics/menusep2.png" height="19" width="15" alt=":">
			<a class="mLink" href="/forum"><? echo l('forum')." ".privmsgcount();?></a>
		  </div>
		  <div>
			<img style="vertical-align:middle" src="/pics/menusep2.png" height="19" width="15" alt=":">
			<a class="mLink" href="/usergroups"><? echo l('usergroups') ?></a>
		  </div>
		  <div>
			<img style="vertical-align:middle" src="/pics/menusep2.png" height="19" width="15" alt=":">
			<a class="mLink" href="/about"><? echo l('about') ?></a>
		  </div>
		  <div>
			<img style="vertical-align:middle" src="/pics/menusep2.png" height="19" width="15" alt=":">
			<? if ($_SESSION['uid']) { ?>
			<a id="logoutlink" class="mLink" href="/logout"><?
					echo l('logout')." (".$_SESSION['username']; ?>)</a>
		  </div>
		  <div>
			<img style="vertical-align:middle" src="/pics/menusep2.png"  height="19" width="15" alt=":">
				<? } ?>
		  </div>
</nav>