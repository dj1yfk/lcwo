<h1>LCWO ChangeLog</h1>
<pre>

This is a detailed list of changes made to LCWO. It's mostly for the
developer's own reference, but it's public so everyone can see what is going
on behind the scenes at LCWO. Some things may not make much sense to anyone but
the author. 

Even more fine grained changes can be observed in the git repository:
<a href="https://git.fkurz.net/dj1yfk/lcwo">https://git.fkurz.net/dj1yfk/lcwo</a>

Started on 2009-02-08.

2020-06-11:
    * MorseMachine: Practice with custom character set, and Hiragana/Katakana (jscwlib only)

2020-06-10:
    * jscwlib is now the default CW player for new users

2020-05-23:
    * The site now internally uses the utf8 character encoding.
    * Added French proverbs to plain text training (tnx SolarMax)
    * Link to MP3 file under player: Results in download, doesn't play
      file in browser

2020-05-10:
    * Include experimental sound generation with jscwlib.
    * Allow selecting REAL characters for code groups (instead of PARIS)

2020-04-29:
    * Every user can download their results as JSON or CSV files now.

2020-04-24:
    * Allow play/pause control by double-tapping Ctrl on Koch course

2020-04-17:
    * Add usage statistics page (https://lcwo.net/stats)

2020-04-14:
    * MorseMachine allows to show the current character when pressing button.
      This allows the user to *know* which letter was sent and to re-hear it
      before skipping it (tnx 4Z5SL)
    * Transmit training: Add more character (Umlauts, etc.) (tnx SA6CBZ)
    * MorseMachine: Add alternative character order (tnx SA6CBZ)
    * Fix problems with Umlauts in wordtraining.
    * Word-Editor: Allow filtering words by left/right match (tnx DK1ET)

2020-02-12:
    * MorseMachine now supports different character sets / orders (tnx NN7M)

2020-02-10:
    * Enable Extra Word Space also for custom code groups (but not for
      normal code groups because we want comparable results for the
      highscore lists)

2020-01-15:
    * Allow opening a local text file in the text2cw converter (tnx WA2NFN)

2019-12-17:
    * Embeddable CW in forum messages: Allow changing CW parameters.

2019-12-09:
    * Allow embedding morse code snippets into forum posts with [cw]text[/cw]
      syntax. Additionally speed, eff(ective speed), ews (extra word space)
      and freq can be specified e.g. [cw speed=60 ews=5 freq=500]Hello[/cw].

2019-11-18:
    * Word training: Add auto skip function to move to the next word without
      pressing return (5 seconds delay)
    * Extra word space: Allow larger spaces (previous limit 5, now 40)

2019-11-06:
    * Add extra word spacing option (currently only active in Koch lessons)
    * Remove autocomplete for word and callsign training

2019-11-01:
    * Reduce buzzer volume in Morse Machine

2019-10-31:
    * Allow space instead of dot for replay in callsign and word training.

2019-09-07:
    * Firefox does not allow auto-playing sound any more (without a click
      or tap by the user). In word training, callsign training and Morse
      Machine, this resulted in errors because the keyboard focus started
      in an entry field and pressing enter here did not work. Moving the
      focus to the "OK" or "Start" button by default seems to fix this
      (still no click/tap needed, only pressing enter while the button is
      in focus works).
    * Save session variables in the database, so the settings made for
      plain text training, word training, callsign training, text2cw and
      download will be restored the next login. 

2019-08-20:
    * User group locations can be placed as markers on a map

2019-08-19:
    * Forums and user groups can be set to read only by appropriate flags.
    * Forum users can be banned for 24h by the moderator.

2019-02-26:
    * Word/Callsign training: Show repeat button after first callsign.
    * Fix broken repeat function for callsign training.
    * Honor the "delayed start" setting also for callsign and word training.
 
2019-01-16:
    * Word/Callsign training: Instead of pressing "." to repeat,
      you can now also press a button to the same effect.
      Using the dot doesn't work on Chrome mobile.

2018-11-28:
    * Show warning when no texts found that match the selected
      criteria in word training. 
    * Accept &aring; for &agrave;

2018-10-18:
    * Show number of users online

2018-10-07:
    * Major code cleanup. Move hardcoded values into config files.
    * First working version that runs in a Docker container,
      for testing and development mostly.
    * Updated documentation. Prepare for open source release.
    * Users can now opt to hide from the "online" display

2018-09-03:
    * MorseMachine produces "buzzer" sound on errors [tnx 4Z5SL]

2018-08-07:
    * Switch from mysql to mysqli interface.

2018-01-25:
    * Callsign training now allows to replay all calls (click on the
      call) and a mode where it pauses after each error [tnx 4Z5SL]

2015-02-22:
    * Wordtraining: When less than 25 words are available for the
      selected parameters, allow duplicates. [tnx AA7F]

2015-01-12:
    * Add <s>strikethrough</s> text to forum
    * Add MP3 as alternative source to HTML5 player snippet (text2cw)

2014-12-18:
    * Cleanup in Callsign/Wordtraining
    * Remove display of "0 Attempts" from main overview
    * Added pluralized versions to various strings (members, attempts)

2014-12-17:
    * Make it possible to have different plural forms in translations
      (as needed e.g. in Slavic languages) (tnx Jerry A.)
    * Added Serbian to word training (tnx YU0W).

2014-12-16:
    * Ignore spaces in user input for callsign / word training (tnx Jerry A.)
    
2014-12-13:
    * Fix x axis scaling of graphs for 13 .. 17 entries (tnx DO1PSY).
    * Fix y axis label positions / spacing (no more overlap with axis)
    * Deleting results on the main page is now done in the
      background by AJAX, which makes it a lot easier/faster
      to delete many entries.
    * Show number of attempts done on overview / main page for each
      practice mode
    * Added Italian proverbs to Plain Text Training (tnx I5SKK)

2014-12-08:
    * https enabled for lcwo.net with self-signed certificate

2014-12-06:
    * Add "cancel" button in plain text training attempts (tnx K7CPY)
    * Remove obsolete functions (f10spaces) and clean up
      a little
    
2014-12-05:
    * Changed text in the input area to fixed width/monospace (tnx K6WRU).
    
2014-12-03:
    * Fixed error reporting for groups with variable lengths (tnx DL6ER)
    * Moved everything into a new directory
    * New paths for api calls etc.
    * Major code cleanup (remove millions of warnings by
      using proper strings instead of constants, e.g. $_SESSION['good']
          instead of $_SESSION[bad], etc.
    * Fixed issue in Plain Text training when text contained
      quotation marks (tnx Jerry A.).

2014-11-27:
    * Czech dictionary added for word training (tnx OK1DNA)

2014-11-22:
    * Download for Word training with CW abbreviations fixed.
    
2014-11-17:
    * Signup requires solving a captcha now, for certain IP address ranges.

2014-11-12:
    * Performance of highscore calculations improved.

2014-10-29:
    * New interface language Srpski (tnx YU0W)
    
2014-09-11:
    * New interface language Norsk (tnx LA6ALA)
    
2014-06-15:
    * Account settings now include a field where the user can chose the
      closest server (by continent - currently servers in Europe and
      North America available). This will decrease loading times and
      latencies, especially for the Morse Machine.

2014-06-14:
    * Disable autocomplete, spellcheck, autocapitalize and autocorrect
      in Morse text entry forms.

2014-06-12:    
    * Update MP3 download page (multiple languages/sets for word training)
    * Fix errornous output of MP3 downloads if random tone frequency was
      selected. (closes bug #7)
    * Make changes in lesson/length/code group mode, etc. "auto submit"
      (closes bug #6)

2014-06-11:
    * Word training now allows to select multiple collections
      for one attempt, i.e. you can mix languages etc.

2014-06-10:
    * Added possibility to have more than one word collection per
      language for word training.
    * Updated German word lists (tnx DL1AKP)
    * Added Brazilian Portuguese translation (tnx PY3MAY)

2014-05-15:
    * Minor enhancement to TX training: Second button for touch events.

2013-06-01:
    * Security enhancements.

2013-01-27:
    * Start of lessons or code groups can now be delayed by up to
      10 seconds ("Delayed start" parameter in the CW Settings)

2013-01-25:
    * Uploaded the JavaScript Morse decoder for TX practice (/transmit)

2013-01-04:
    * Atom feeds (atom.xml/forumatom.xml) now serve proper
      UTF-8-formatted files.

2013-01-03:
    * Fixed generation of variable length groups (tnx K6DMT)

2013-01-02:
    * OGG/Vorbis headers fixed; HTML5 should work reliably now.

2012-12-31:
    * Callsign and Word training: Pressing "." before attempt starts
      doesn't produce "undefined" anymore, instead starts the attempt.
    * Word Training: JavaScript errors removed.
    * MorseMachine: Allow to start session with spacebar

2012-12-30:
    * Improved accuracy of lesson / group duration (setting vs.
      actual length) significantly

2012-12-29:
    * Moved CW generation to a different server, with improved
      CGI version of ebook2cw (new release 0.8.2 to be out soon)
    * Fixed little bug which sometimes causes "UNDEFINED" to be
      sent instead of callsigns or words.
    * Fixed error reporting; character '0' is no longer equivalent
      to an empty string.
    * Fix calculation of real speed for Farnsworth

2012-12-28:
    * Wordtraining: 2 or less characters work now, even if there
      are less than 25 different words in the database.
    * Draw gray line at 90% in stats graphs for Koch/code groups

2012-10-04:
    * Allow anyone to post in forums of _public_ user groups.

2012-09-30:
    * Fixed garbled special characters in JavaScript alert box
      in MorseMachine (tnx TA2RX)
    * Made User group subscription texts translatable.
    * Private groups are no longer included in moderation
    * Ukrainian translation now "official", tnx UT4UQN!

2012-09-23:
    * Number of forum posts now shows only approved posts.

2012-09-15:
    * Added Ukrainian language (preliminary, thanks Mikolaj, UT4UQN!)

2012-09-10:
    * Fixed HTML output in "Who is online?" box (" =&gt; &amp;quot;)
    * Some measures against spam-bots

2012-08-22:
    * Changed QTC trainer repeat function to F7 / F8 key
    * Small updates in language files (various languages)
    * Editing of Forum posts re-enabled for whitelisted users

2012-08-19:
    * Added variable CW frequency and repetition functions to QTC trainer.

2012-08-18:
    * Some security enhancements.

2012-07-10:
    * Changing user names re-enabled.
    * Added new language: Bulgarian, thanks Tony, LZ3AI
    
2012-01-12:
    * Changing user names temporarily disabled.
    * New Forum posts need to be approved by a moderator.

2012-01-10:
    * Privacy enhancements.

2011-08-21:
    * Disabled Koch course highscores due to scaling problems.

2011-06-15:
    * Fixed one-off-bug ("spurious character" in lesson 9); tnx KB3TZK
    * Subscription to usergroup-forums by email added.

2011-06-14:
    * Initialization of download-settings for users who are not logged in
    * Added "Download all"-button on download-page (experimental)
    * Improved grading algorithm for groups

2011-06-13:
    * Add warning message for malformed text entry (e.g. missing spaces).
    * Randomness of variable group lengths now weighted, 2 / 7 less likely
      than 3 / 6 less likely than 4 / 5
    * Randomness of letter probabilities weighted in Koch course. Letters
      of later lessons are more likely to appear more often in the texts now.
    * Improved Koch course stats (graphs for single lessons available)

2011-04-30:
    * Fix broken CGI address in MorseMachine / HTML5 mode
    
2011-04-04:
    * Added Chinese translations (traditional = zh, simplified = cn)

2011-01-26:
    * tex2cw now saves the settings, if changed from default
    * Word training now also can be limited to words for a particular lesson
    * Decreased delay before MP3 playback starts

2011-01-16:
    * Abbreviated numbers in code groups no longer affect mixed text
    * Add rel=canonical links
    * Fix single quotes / apostrophes in custom characters

2011-01-15:
    * Fixed error in URL for new character functions in Koch course

2011-01-14:
    * Added a constant CGIURL in an emergency operation to redirect the broken
      CGIs of lcwo.net to another host.
    * Fixed escaping of apostrophes in Plain text training
    * Fixed HTML for the Alternative Flash player (missing colsing div)

2011-01-13:
    * Moved to new server with MySQL 5 and PHP 5
    * Improved some SQL to reflect new MySQL5 functions

2011-01-12:
    * Limit number of lost password requests based on IP, username and date
    * Add wordwrap for reply-text in personal messages

2011-01-11:
    * "Who is online?" stats are no longer reset on the top of each hour
    * Changed PHP version to 5.2
    * Redirecting www.lcwo.net to lcwo.net
    * Updated eham ratings / cosmetic changes

2011-01-10:
    * Minor bugfixes (HTML, Atom feed validity)
    * Added Abbreviated numbers to the Code Groups training (req. DF9TS)
    * Improved readibility of the Forum feed

2011-01-05:
    * Quoting bug fixed
    * Forum Atom feed date and URL format fixed
    * Added some new translating items
    * Added French (tnx F5IHN) and German "About" page 
    * Changed some relative URLs to absolute
    * Added "direct player link" to text2cw results (e.g. to send
      a link in a mail, with encoded text)
    * Ignore VVV = or AR if entered (groups, koch)
    * Improved readibilty of CW Settings page
    * Changed Error 404 page

2010-12-20:
    * Added Personal Message features to the forum

2010-12-08:
    * Added Spanish "About" page (tnx EA1GBX)
    * Improved callsign database handling for callsign training

2010-12-07:
    * Uploaded Turkish translation (TNX TA2RX!)
    * Started localizing the "About" page
    * Added some more missing translation items
    * Added rel="nofollow" to different sortings of user list to reduce
      redundant search results
    * Little bugfix in forum avatar display

2010-12-06:
    * Added some missing translation items (tnx TA2RX)

2010-11-18:
    * Proper removal of all data from users who delete their account.

2010-11-03:
    * Fixed word-wrapping of Play/Pause button

2010-09-29:
    * MorseMachine now shows effectice speed (tnx N2MCS for the suggestion)

2010-09-28:
    * Added Digg-Button
    * Improved translation system
    * Added "Delete Account"
    * Localized MorseMachine strings
    * Added MP3 support for HTML5 / Apple Safari

2010-09-17:
    * Added Atom / Twitter icons to News and Forums

2010-09-16:
    * Fixed group length for calculation of error rate if it's not default (5)
    * Added HTML snippets to the text2cw page to include in user's websites

2010-09-15:
    * Fixed usergroups joins/approvals etc.

2010-09-14:
    * Small bugfixes for Clean URLs
    * Small fixes in Forums
    * News database driven now (finally!)
    * Added Atom Feeds for News and Forums
    * Changed html title to include "Learn CW Online" instead of LCWO.net

2010-09-03:
    * Clean URLs implemented + speaking URLs for Forum topics
    * Updated some translations

2010-08-31:
    * Forum allows basic quoting. Only works one level deep, no nested quotes.

2010-08-24:
    * Cosmetic changes on about-page.
    * Made LCWO-logo a link to /
    * Added eham.net and dxzone.com rating links etc.

2010-08-23:
    * Code cleanup, removed JavaScript errors, etc.
    * Improved titles for better readability and search engine indexing
    * Fixed error in Plain Text training (simplify characters)

2010-08-18:
    * Cursor in QTC trainer always goes to the end of the field now. Only has
      an effect in IE; in Firefox it did that anyway by default.

2010-08-09:
    * Improved the MP3 Download section. It now remembers all settings within a
      session and a little tutorial was added, describing the usage of a
      download manager.

2010-08-02:
    * Experimentally supporting an alternative Flash MP3 player
      (http://flash-mp3-player.net/players/js/)

2010-08-01:
    * Allow user group admins (founders) to modify group details (name,
      description, etc.)
    * Change font for pre-formatted text

2010-07-31:
    * Modify page title according to Forum thread titles

2010-07-30:
    * MorseMachine: 
        - Added Reset button
        - Use tone frequency from user settings
        - Allow to change error bars arbitrarily by clicking on them
        - Repeat wrongly copied characters until they are copied correctly
          or return is pressed (= penalty on error bar)
        - Visual feedback of copied letters (correct = green, wrong = red)

2010-07-26:
    * Added a beta of the "Morse Machine", a la Ward Cunningham, K9OX.

2010-07-19:
    * Always loading Flash related JavaScript stuff, in order to make sure it
      is loaded when switching from HTML5 to Flash

2010-07-10:
    * HTML5 player vertical size fixed, so it doesn't momentarily "grow" while
      loading a new file.

2010-07-09:
    * Speed limit, fixed speed, and filters for long/unusual/slashed calls now
      available in callsign training.

2010-07-07:
    * Added meaningful titles to most pages

2010-06-15:
    * Repeats are now possible in callsign and word training by pressing "."
    * Small change of the contact form
    * QTC training supports abbreviated numbers

2010-06-14:
    * Added optional minimum character speed to Callsign and Word training.
      Below the minimum speed, Farnsworth is used (suggested by SQ6JNX, tnx).

2010-06-05:
    * Sort user groups by group-id on the groups overview

2010-05-25:
    * Cosmetic improvements of the signup-dialog

2010-05-20:
    * Added English descriptions of all languages at important points (e.g.
      user profiles, account settings, etc.)
    * Made sure that empty forum posts are not possible anymore

2010-05-19:
    * Limited access to forum threads in private groups to actual group members
    * Reset settings of the "test" user account regularly

2010-05-16:
    * Cosmetic changes to the HTML5 player (size, MP3 link)
    * Fixed letter preview in Koch lessons for HTML5 player
    * Improved Flash 10 player initial spaces

2010-05-15:
    * Added a CW-CGI that generates OGG/Vorbis instead of MP3
    * Added a native HTML5 audio player option
    * Minor changes to "about" and "impressum" pages

2010-05-05:
    * Added Italian words to Word training (tnx Stefano, IN3AEF)

2010-05-03:
    * Cosmetics in statistics overview on profile page

2010-05-01:
    * Fixed bug in graph average generation

2010-04-30:
    * Added Avatar to forums
    * Removed support for HTML tags in forums, replaced by BBcode
    * Added Dutch proverbs
    * Added "real" speed calculations
    * Fixed some small bugs which related to extra spaces in received texts
    * Added Plain text training and QTC training to statistics overview
    * Several minior optical improvements
    
2010-04-21:
    * Added Malay (tnx 9M2RIE).

2010-03-23:
    * Added Thai (tnx HS8JYX).

2009-12-17:
    * Added Dutch to Word Training (tnx PA0WV)

2009-10-06:
    * Improved parameter dialog for callsign training a little.

2009-10-05:
    * Added to plain text training: English proverbs (619), American proverbs
      (169), German proverbs (184)
    * Plain text training now remembers chosen database through a session.

2009-10-04:
    * Added Portuguese to the word training database.
    * Fixed bugs in word training and callsign training; max-speed now
      correct in all cases.
    * Rewrote plain text training. More languages and databases will be added
      soon.

2009-09-21:
    * Added quick access to usergroups in left hand side menu

2009-08-10:
    * Custom code groups included in personal statistics.

2009-08-05:
    * QTC highscores show topspeed regardless of # of attempts

2009-07-19:
    * Added Croatian translation, tnx 9A2JK!

2009-07-09:
    * Updated Suomi translation

2009-07-08:
    * Highscores for QTCs now also have a threshold value for attempts
    * QTC speed now shown on QTC page

2009-07-03:
    * Added a small stats overview to the profile pages (optional)

2009-07-02:
    * Code groups duration can be set up to 30 minutes now

2009-07-01:
    * Leaving user groups is now possible.
    * Fixed warnings on empty group-highscore lists

2009-06-29:
    * Callsign training remembers speed and uses the tone frequency from the CW
      settings. Automatic focus of the submit button in the first dialog.
    
2009-06-12:
    * Short URLs for user profiles, like http://lcwo.net/u/dj1yfk
      now available

2009-05-28:
    * Fixed bug in MP3 file download area
    * Callsign training and Word training now notifies you if you
      make a new personal highscore, and shows current standings.

2009-05-22:
    * Added Spanish wordlist to the word training.
    * Word- and Callsign training improved (submitting of score
      automated; callsign list doesn't disappear).

2009-04-30:
    * Added new language: Catala. Tnx Salva, EB3CML!

2009-04-08:
    * Added some Q-groups to the Word training (CW)

2009-04-07:
    * Code group mode (letters, figures, ...) is now saved in the
      database after once setting it.

2009-03-26:
    * Updated the "About" page
    * Added a contact form to the imprint

2009-03-18:
    * Added Word training to the practice file downloads

2009-03-17:
    * QTC highscores are now properly filtered for user groups.

2009-03-11:
    * Fixed a little bug in the QTC trainer (grp/nr was sometimes
      sent twice.)

2009-03-08:
    * Added WAE link to the front page

2009-03-07:
    * Some "New Attempt" links and "Play/Pause" buttons now
      automatically get the focus when the page loads, so they
      can be accessed just by hitting "Enter" (instead of using
      the mouse).
    * Added QTC-Training and Word-Training to the highscore lists
    * Added "CW abbreviations" as new "language" to the word
      trainer

2009-03-06:
    * Cleaned some translation items; finished German translation
    * Added Hungarian (magyar) as a new language.
    * Some optical enhancements (play button, user group display)

2009-03-05:
    * Fixed access for viewers who are not logged in to public
      user groups.

2009-03-04:
    * Koch course now accepts ";" instead of "?" (easier to type
      on some keyboard layouts).
    * Slightly rearranged lesson result display 

2009-02-09:
    * Added link to News to the Menu
    * Added this Changelog
    * Improved spam filtering for the Forum
    * QTC-trainer now fully translatable.
    * Speed in QTC trainer passed by GET instead of POST
    * Word training now allows setting the tone (fixed or random)
    * Added French word list for Wordtraining
    * Signup/Register form: Form data is saved if an invalid or
      taken username was chosen.
    
</pre>
