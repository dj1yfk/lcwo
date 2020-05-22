<h1>&Uuml;ber - Learn CW Online</h1>
<p><em>Learn CW Online</em> (LCWO) ist eine Plattform zum Lernen und Trainieren von Morsetelegraphie (CW). Sie wurde konzipiert und programmiert 
von <a href="http://fkurz.net/">Fabian Kurz, DJ1YFK</a> 
(<a href="/impressum">Impressum</a>, 
<a href="http://lcwo.net/profile/dj1yfk">Profil</a>) und ging im Mai 2008
ans Netz. </p>

<p>LCWO wird ständig weiterentwickelt und jegliche Hinweise und
Vorschläge sind willkommen. Zur Kontaktaufnahme kann das <a
href="/impressum#form">Kontaktformular</a> verwendet werden, oder Sie schicken
eine E-Mail an <a href="mailto:help&#64;lcwo.net">help&#64;lcwo.net</a>. 
Für allgemeine Fragen und Diskussionen rund im LCWO und CW gibt es ein <a
href="/forum">Forum</a>.  An dieser Stelle ein herzliches Dankeschön an die zahlreichen Benutzer, die sich durch konstruktive Kritik und gute Vorschläge an diesem Projekt beteiligt haben!</p> 

<p><strong>Diese Seite ist nichtkommerziell und die Benutzung ist kostenlos.</strong> Es besteht kein Interesse an Geldspenden.</p>

<h2>Übersetzer</h2>
<p><em>Vielen herzlichen Dank</em> an die folgenden Personen für die Übersetzung dieser Seite in eine der <?=count($langs);?> Sprachen, die LCWO anbietet:
</p>

<? include("inc/about-translators.php"); ?>


<p>Falls Sie LCWO in eine neue Sprache übersetzen möchten, wenden Sie sich
bitte per <a href="mailto:help&#64;lcwo.net">E-Mail</a> an uns. Vielen Dank!</p>

<h2>Weitersagen!</h2>
<p>Um von Ihrer Webseite oder Ihrem Blog auf LCWO zu verweisen, 
können Sie einen der folgenden Banner oder Buttons verwenden. Ebenso sind hochaufgelöste Grafiken für Druckmedien oder QSL-Karten in verschiedenen Formaten verfügbar.</p>
<div>
Banner, 468x60px: <br><img src="/pics/lcwo-banner.png" alt="[LCWO Banner]"><br><br>
Buttons, 80x15px: <br>
<img src="/pics/lcwo-button1.png" alt="[LCWO Button 1]">&nbsp; <img
src="/pics/lcwo-button2.png" alt="[LCWO Button 2]"><br><br>
Logos für QSO-Karten (ZIP-Archiv mit PDF-, EPS- und PNG-Dateien):<br>
<a href="/pics/qsl-logos.zip"><img
src="/pics/qsl-logo-small.png" alt="[LCWO print logo]"></a><br><br>
</div>

<h2 id="rate">Bewertungen</h2>

<table width="80%">
<tr>
<td valign="top" width="45%">
<a href="http://www.eham.net"><img style="border:none;" src="/pics/ehamlogo.gif" alt="[eham.net logo]"></a><br>
<p>Diese Seite wurde auf eHam.net bewertet (<?=$eham1?> bei <?=$eham2?> Bewertungen). <br><a
href="http://www.eham.net/reviews/detail/8401">Vielen Dank! Hier können
auch Sie eine Bewertung abgeben!</a></p>
<p>LCWO wird auch in der <a
href="http://www.eham.net/links/rating/10889">Link-Liste</a> von eHam geführt.</p>
</td>
<td width="10%">
&nbsp;
</td>
<td width="45%">
<? include("dxzone.html"); ?>
</td>
</tr>
</table>

<h2>Pressemitteilung</h2>
<p>Der folgende Text kann gerne in beliebigen Medien (Blogs, Foren, Newsletter)
übernommen werden:
</p>

<pre style="border:1px solid #aaaaaa; margin:3px">
<? include("lcwo.txt"); ?>
</pre>


<h2>Technisches, Kompatibilität</h2>

<p>Alle modernen Browser, insbesondere die letzten beiden Generationen von
of <a href="http://www.firefox.com/">Mozilla Firefox</a> und des Microsoft
Internet Explorer werden unterstützt. Ebenso funktioniert LCWO mit <a
href="http://www.opera.com/">Opera</a> and Apple <a
href="http://www.apple.com/safari/">Safari</a> in aktuellen Versionen (auch auf iPhone/iPad).</p>

<p>Um alle Funktionen voll nutzen zu können, muss JavaScript aktiviert sein
und entweder ein Browser mit <a href="http://en.wikipedia.org/wiki/HTML5">HTML5</a>-Unterstützung verwendet werden, oder der 
<a href="http://www.adobe.com/de/products/flashplayer/">Flash
Player</a> muss installiert sein.</p>

<p>Die Entwicklungsumgebung von LCWO ist <a
href="http://www.debian.org/">Debian GNU/Linux</a> und <a
href="http://www.ubuntu.com">Ubuntu</a>, mit dem
genialen Editor <a href="http://www.vim.org/">vim</a>. Die Seite
läuft auf einem <a href="http://www.apache.org/">Apache</a>
Webserver und ist weitestgehend in <a href="http://www.php.net/">PHP</a> 
programmiert (ohne Frameworks, alles selbstgebaut); im Hintergrund
arbeitet eine <a href="http://www.mysql.com/">MySQL</a>-Datenbank. Die Morse
MP3- und OGG-Dateien werden von speziell angepassten Versionen von <a
href="http://fkurz.net/ham/ebook2cw.html">ebook2cw</a> erzeugt, die
als kompilierte CGIs (geschrieben in C) laufen.</p>

