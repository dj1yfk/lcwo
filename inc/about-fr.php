<h1>A propos de LCWO - Learn CW Online</h1>
<p>Ce site, <em>Learn CW Online</em> (LCWO), a été créé en mai 2008 par
<a href="http://fkurz.net/">Fabian Kurz, DJ5CW</a> 
(<a href="/impressum">Impressum</a>, 
<a href="http://lcwo.net/profile/dj5cw">profile</a>),
dans le but de faire apprendre et pratiquer la CW (code Morse) le plus facilement et le moins contraignant possible.
</p>

<p>LCWO est en développement constant, ainsi les commentaires et suggestions
sont les bienvenus. Pour prendre contact avec l&rsquo;auteur, utiliser le
<a href="/impressum#form">formulaire de contact</a> ou bien envoyez un mèl à <a href="mailto:help&#64;lcwo.net">help&#64;lcwo.net</a>. Pour des questions ou
discussions d&rsquo;ordre général, n&rsquo;hésitez pas à vous servir du <a href="/forum">forum</a>.
Merci à tous les utilisateurs qui ont contribués au projet jusqu&rsquo;à
présent. Sans tous ces retours d&rsquo;information le site ne serait pas
ce qu&rsquo;il est aujourd&rsquo;hui !</p>

<p><strong>L&rsquo;usage de LCWO est et restera toujours gratuit.</strong>
Les dons ne nous intéressent pas, cela dit si vous êtes en mesure de fournir
une bande passante fiable pour les CGI (génération des MP3/OGG), merci
de <a href="mailto:help&#64;lcwo.net">nous contacter</a>.</p>


<h2>Traducteurs</h2>

<p><em>Un grand merci</em> aux personnes suivantes pour la traduction de
l&rsquo;interface utilisateur:</p>

<? include("inc/about-translators.php"); ?>

<p>Si vous souhaitez aider à traduire LCWO dans d&rsquo;autres langues, merci de prendre contact avec
<a href="mailto:help@lcwo.net">Fabian, DJ5CW par mèl</a>. Merci&nbsp;!</p>

<h2>Spread the word!</h2>
<p>Si ça vous dit de placer un lien depuis votre site web ou votre blog,
merci d&rsquo;utiliser une des bannières et boutons ci-après. Des logos
(en N&amp;B) sont également disponibles pour vos cartes QSL.</p>

<div>
Bannière, 468x60 pixels: <br><img src="/pics/lcwo-banner.png" alt="[LCWO Banner]"><br><br>
Boutons, 80x15 pixels: <br>
<img src="/pics/lcwo-button1.png" alt="[LCWO Button 1]">&nbsp; <img
src="/pics/lcwo-button2.png" alt="[LCWO Button 2]"><br><br>
Logos pour cartes QSL (fichier ZIP en différents formats, PDF, EPS, PNG):
<br>
<a href="/pics/qsl-logos.zip"><img
src="/pics/qsl-logo-small.png" alt="[LCWO print logo]"></a><br><br>
</div>

<h2 id="rate">Opinions, Notes</h2>

<table width="80%">
<tr>
<td valign="top" width="45%">
<a href="http://www.eham.net"><img style="border:none;" src="/pics/ehamlogo.gif" alt="[eham.net logo]"></a><br>
<p>Le site a été évalué (<?=$eham1?> sur <?=$eham2?> opinions) sur eHam.net.<br><a
href="http://www.eham.net/reviews/detail/8401">Merci et n&rsquo;hésitez pas à ajouter votre propre opinion également&nbsp;!</a></p>
<p>Vous pouvez aussi noter LCWO sur la 
<a
href="http://www.eham.net/links/rating/10889">section Liens de eHam.
</a>.</p>
</td>
<td width="10%">
&nbsp;
</td>
<td width="45%">
<? include("dxzone.html"); ?>
</td>
</tr>
</table>

<h2>Annonce de presse</h2>
<p>
N&rsquo;hésitez pas à utiliser le texte suivant de promotion de LCWO pour votre bulletin de radio-club, site web, blog, etc.
</p>

<pre style="border:1px solid #aaaaaa; margin:3px">
<? include("lcwo-fr.txt"); ?>
</pre>


<h2>Compatibilité des navigateurs, aspects techniques</h2>

<p>LCWO supporte la plupart des navigateurs modernes, en particulier les
deux dernières générations de
<a href="http://www.firefox.com/">Mozilla Firefox</a>
et de Google Chrome. Il a été signalé comme étant compatible
avec 
<a href="http://www.opera.com/">Opera</a> et <a
href="http://www.apple.com/safari/">Safari</a> d&rsquo;Apple.</p>

<p>Pour profiter de toutes les fonctionnalités de LCWO, JavaScript doit
être installé et un navigateur utilisant soit
<a href="http://en.wikipedia.org/wiki/HTML5">HTML5</a> ou bien le
<a href="http://www.adobe.com/de/products/flashplayer/">Flash
Player</a> d&rsquo;Adobe pour la lecture CW est fortement recommandé.
</p>

<p>Le développement de LCWO est fait sous
<a href="http://www.debian.org/">Debian GNU/Linux</a> et <a
href="http://www.ubuntu.com">Ubuntu</a>, 
grâce à l&rsquo;éditeur fantastique
<a href="http://www.vim.org/">vim</a>. 
Le site tourne sur un serveur
<a href="http://www.apache.org/">Apache</a>, il est programmé quasiment entièrement en
<a href="http://www.php.net/">PHP</a> (sans aucun framework, tout fait maison) 
et emploie une base de données
<a href="http://www.mysql.com/">MySQL</a>.
Les fichiers CW en MP3 et les fichiers OGG sont générés à partir de versions customisées de
<a href="http://fkurz.net/ham/ebook2cw.html">ebook2cw</a>,
qui tournent comme des CGI compilées, écrites en C.
</p>

<p>Sources: <a href="https://git.fkurz.net/dj1yfk/lcwo">https://git.fkurz.net/dj1yfk/lcwo</a>.</p>
