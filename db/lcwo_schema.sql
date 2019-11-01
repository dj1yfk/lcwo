--
-- Table structure for table `lcwo_callsignsresults`
--

-- DROP TABLE IF EXISTS `lcwo_callsignsresults`;
CREATE TABLE `lcwo_callsignsresults` (
  `NR` bigint(5) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `max` int(11) NOT NULL DEFAULT '0',
  `score` int(11) NOT NULL DEFAULT '0',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `valid` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`NR`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

--
-- Table structure for table `lcwo_config`
--

-- DROP TABLE IF EXISTS `lcwo_config`;
CREATE TABLE `lcwo_config` (
  `ID` bigint(5) NOT NULL AUTO_INCREMENT,
  `key` varchar(64) NOT NULL DEFAULT '',
  `val` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  KEY `key` (`key`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

--
-- Table structure for table `lcwo_groupmembers`
--

-- DROP TABLE IF EXISTS `lcwo_groupmembers`;
CREATE TABLE `lcwo_groupmembers` (
  `gid` bigint(5) NOT NULL DEFAULT '0',
  `member` bigint(5) NOT NULL DEFAULT '0',
  KEY `gid` (`gid`),
  KEY `member` (`member`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `lcwo_grouprequests`
--

-- DROP TABLE IF EXISTS `lcwo_grouprequests`;
CREATE TABLE `lcwo_grouprequests` (
  `gid` bigint(5) NOT NULL DEFAULT '0',
  `member` bigint(5) NOT NULL DEFAULT '0',
  KEY `gid` (`gid`),
  KEY `member` (`member`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `lcwo_groupsresults`
--

-- DROP TABLE IF EXISTS `lcwo_groupsresults`;
CREATE TABLE `lcwo_groupsresults` (
  `NR` bigint(5) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `mode` varchar(24) NOT NULL DEFAULT 'letters',
  `speed` int(11) NOT NULL DEFAULT '0',
  `eff` int(11) NOT NULL DEFAULT '0',
  `accuracy` float NOT NULL DEFAULT '0',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `valid` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`NR`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

--
-- Table structure for table `lcwo_groupsubscribe`
--

-- DROP TABLE IF EXISTS `lcwo_groupsubscribe`;
CREATE TABLE `lcwo_groupsubscribe` (
  `gid` bigint(5) NOT NULL DEFAULT '0',
  `member` bigint(5) NOT NULL DEFAULT '0',
  KEY `gid` (`gid`),
  KEY `member` (`member`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `lcwo_lessonresults`
--

-- DROP TABLE IF EXISTS `lcwo_lessonresults`;
CREATE TABLE `lcwo_lessonresults` (
  `NR` bigint(5) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `lesson` int(11) NOT NULL DEFAULT '0',
  `speed` int(11) NOT NULL DEFAULT '0',
  `eff` int(11) NOT NULL DEFAULT '0',
  `accuracy` float NOT NULL DEFAULT '0',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`NR`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

--
-- Table structure for table `lcwo_mmresults`
--

-- DROP TABLE IF EXISTS `lcwo_mmresults`;
CREATE TABLE `lcwo_mmresults` (
  `id` bigint(5) NOT NULL AUTO_INCREMENT,
  `uid` bigint(3) NOT NULL DEFAULT '0',
  `count` int(11) NOT NULL DEFAULT '0',
  `k0` int(11) NOT NULL DEFAULT '101',
  `k1` int(11) NOT NULL DEFAULT '101',
  `k2` int(11) NOT NULL DEFAULT '101',
  `k3` int(11) NOT NULL DEFAULT '101',
  `k4` int(11) NOT NULL DEFAULT '101',
  `k5` int(11) NOT NULL DEFAULT '101',
  `k6` int(11) NOT NULL DEFAULT '101',
  `k7` int(11) NOT NULL DEFAULT '101',
  `k8` int(11) NOT NULL DEFAULT '101',
  `k9` int(11) NOT NULL DEFAULT '101',
  `k10` int(11) NOT NULL DEFAULT '101',
  `k11` int(11) NOT NULL DEFAULT '101',
  `k12` int(11) NOT NULL DEFAULT '101',
  `k13` int(11) NOT NULL DEFAULT '101',
  `k14` int(11) NOT NULL DEFAULT '101',
  `k15` int(11) NOT NULL DEFAULT '101',
  `k16` int(11) NOT NULL DEFAULT '101',
  `k17` int(11) NOT NULL DEFAULT '101',
  `k18` int(11) NOT NULL DEFAULT '101',
  `k19` int(11) NOT NULL DEFAULT '101',
  `k20` int(11) NOT NULL DEFAULT '101',
  `k21` int(11) NOT NULL DEFAULT '101',
  `k22` int(11) NOT NULL DEFAULT '101',
  `k23` int(11) NOT NULL DEFAULT '101',
  `k24` int(11) NOT NULL DEFAULT '101',
  `k25` int(11) NOT NULL DEFAULT '101',
  `k26` int(11) NOT NULL DEFAULT '101',
  `k27` int(11) NOT NULL DEFAULT '101',
  `k28` int(11) NOT NULL DEFAULT '101',
  `k29` int(11) NOT NULL DEFAULT '101',
  `k30` int(11) NOT NULL DEFAULT '101',
  `k31` int(11) NOT NULL DEFAULT '101',
  `k32` int(11) NOT NULL DEFAULT '101',
  `k33` int(11) NOT NULL DEFAULT '101',
  `k34` int(11) NOT NULL DEFAULT '101',
  `k35` int(11) NOT NULL DEFAULT '101',
  `k36` int(11) NOT NULL DEFAULT '101',
  `k37` int(11) NOT NULL DEFAULT '101',
  `k38` int(11) NOT NULL DEFAULT '101',
  `k39` int(11) NOT NULL DEFAULT '101',
  `k40` int(11) NOT NULL DEFAULT '101',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

--
-- Table structure for table `lcwo_news`
--

-- DROP TABLE IF EXISTS `lcwo_news`;
CREATE TABLE `lcwo_news` (
  `ID` bigint(5) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '1970-01-01',
  `news` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

--
-- Table structure for table `lcwo_online`
--

-- DROP TABLE IF EXISTS `lcwo_online`;
CREATE TABLE `lcwo_online` (
  `UID` bigint(5) NOT NULL DEFAULT '0',
  `LASTACTIVE` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`UID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `lcwo_userprefs`
--

-- DROP TABLE IF EXISTS `lcwo_userprefs`;
CREATE TABLE `lcwo_userprefs` (
  `uid` bigint(5) NOT NULL DEFAULT '0',
  `prefs` text NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


--
-- Table structure for table `lcwo_plaintext`
--

-- DROP TABLE IF EXISTS `lcwo_plaintext`;
CREATE TABLE `lcwo_plaintext` (
  `nr` bigint(5) NOT NULL AUTO_INCREMENT,
  `lang` char(2) NOT NULL DEFAULT 'en',
  `description` varchar(32) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  `collid` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`nr`),
  KEY `lang` (`lang`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

--
-- Table structure for table `lcwo_plaintextresults`
--

-- DROP TABLE IF EXISTS `lcwo_plaintextresults`;
CREATE TABLE `lcwo_plaintextresults` (
  `NR` bigint(5) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `speed` int(11) NOT NULL DEFAULT '0',
  `eff` int(11) NOT NULL DEFAULT '0',
  `accuracy` float NOT NULL DEFAULT '0',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`NR`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

--
-- Table structure for table `lcwo_pmsg`
--

-- DROP TABLE IF EXISTS `lcwo_pmsg`;
CREATE TABLE `lcwo_pmsg` (
  `id` bigint(5) NOT NULL AUTO_INCREMENT,
  `fromuid` bigint(3) NOT NULL DEFAULT '0',
  `touid` bigint(3) NOT NULL DEFAULT '0',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `text` text NOT NULL,
  `ip` varchar(64) NOT NULL DEFAULT '127.0.0.1',
  `read` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `touid` (`touid`),
  KEY `fromuid` (`fromuid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

--
-- Table structure for table `lcwo_posts`
--

-- DROP TABLE IF EXISTS `lcwo_posts`;
CREATE TABLE `lcwo_posts` (
  `id` bigint(5) NOT NULL AUTO_INCREMENT,
  `tid` bigint(3) NOT NULL DEFAULT '0',
  `isreply` tinyint(4) NOT NULL DEFAULT '0',
  `uid` bigint(3) NOT NULL DEFAULT '0',
  `topic` varchar(16000) NOT NULL DEFAULT '',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `text` text NOT NULL,
  `forumid` int(5) NOT NULL DEFAULT '0',
  `ip` varchar(16) NOT NULL DEFAULT '',
  `approved` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

--
-- Table structure for table `lcwo_pwrequests`
--

-- DROP TABLE IF EXISTS `lcwo_pwrequests`;
CREATE TABLE `lcwo_pwrequests` (
  `ip` varchar(64) NOT NULL DEFAULT '0.0.0.0',
  `date` date NOT NULL DEFAULT '1970-01-01',
  `username` varchar(64) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `lcwo_qtcresults`
--

-- DROP TABLE IF EXISTS `lcwo_qtcresults`;
CREATE TABLE `lcwo_qtcresults` (
  `NR` bigint(5) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `speed` int(11) NOT NULL DEFAULT '0',
  `qtcs` int(11) NOT NULL DEFAULT '0',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`NR`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

--
-- Table structure for table `lcwo_spamips`
--

-- DROP TABLE IF EXISTS `lcwo_spamips`;
CREATE TABLE `lcwo_spamips` (
  `IP` varchar(16) NOT NULL DEFAULT '127.0.0.1',
  `LASTACTIVE` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`IP`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `lcwo_texts`
--

-- DROP TABLE IF EXISTS `lcwo_texts`;
CREATE TABLE `lcwo_texts` (
  `id` bigint(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '',
  `lang` char(2) NOT NULL DEFAULT 'en',
  `text` text NOT NULL,
  `old` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

--
-- Table structure for table `lcwo_usergroups`
--

-- DROP TABLE IF EXISTS `lcwo_usergroups`;
CREATE TABLE `lcwo_usergroups` (
  `gid` bigint(5) NOT NULL AUTO_INCREMENT,
  `groupname` varchar(128) NOT NULL DEFAULT '',
  `groupdescription` text NOT NULL,
  `founder` bigint(5) NOT NULL DEFAULT '0',
  `lang` char(2) NOT NULL DEFAULT 'en',
  `private` int(1) NOT NULL DEFAULT '0',
  `lat` float NOT NULL DEFAULT '0',
  `lon` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`gid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

--
-- Table structure for table `lcwo_users`
--

-- DROP TABLE IF EXISTS `lcwo_users`;
CREATE TABLE `lcwo_users` (
  `ID` bigint(5) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(64) NOT NULL DEFAULT '',
  `name` varchar(64) NOT NULL DEFAULT '',
  `location` varchar(255) DEFAULT NULL,
  `signupdate` date NOT NULL DEFAULT '1970-01-01',
  `cw_speed` int(11) NOT NULL DEFAULT '20',
  `cw_eff` int(11) NOT NULL DEFAULT '10',
  `cw_tone` int(11) NOT NULL DEFAULT '800',
  `koch_lesson` int(11) NOT NULL DEFAULT '1',
  `player` int(11) NOT NULL DEFAULT '2',
  `lang` char(2) NOT NULL DEFAULT 'en',
  `vvv` int(8) NOT NULL DEFAULT '0',
  `koch_duration` int(1) NOT NULL DEFAULT '1',
  `groups_duration` int(1) NOT NULL DEFAULT '1',
  `lockspeeds` int(8) NOT NULL DEFAULT '0',
  `koch_randomlength` int(8) NOT NULL DEFAULT '5',
  `groups_randomlength` int(8) NOT NULL DEFAULT '0',
  `customcharacters` varchar(127) NOT NULL DEFAULT '',
  `profileaboutme` text NOT NULL,
  `cw_tone_random` int(8) NOT NULL DEFAULT '0',
  `course_duration` int(1) NOT NULL DEFAULT '1',
  `randomlength` int(8) NOT NULL DEFAULT '0',
  `groups_mode` varchar(32) NOT NULL DEFAULT 'letters',
  `show_ministat` int(11) NOT NULL DEFAULT '0',
  `groups_abbrev` int(1) NOT NULL DEFAULT '0',
  `forum_whitelist` int(1) NOT NULL DEFAULT '0',
  `delay_start` int(11) NOT NULL DEFAULT '0',
  `continent` varchar(2) NOT NULL DEFAULT 'eu',
  `consent` int(11) NOT NULL DEFAULT '0',
  `hide` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

--
-- Table structure for table `lcwo_words`
--

-- DROP TABLE IF EXISTS `lcwo_words`;
CREATE TABLE `lcwo_words` (
  `ID` bigint(5) NOT NULL AUTO_INCREMENT,
  `lang` varchar(5) NOT NULL DEFAULT '',
  `word` varchar(64) NOT NULL DEFAULT '',
  `lesson` int(11) NOT NULL DEFAULT '40',
  `collid` int(11) NOT NULL DEFAULT '0',
  `collection` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  KEY `lang` (`lang`),
  KEY `lesson` (`lesson`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

--
-- Table structure for table `lcwo_wordsresults`
--

DROP TABLE IF EXISTS `lcwo_wordsresults`;
CREATE TABLE `lcwo_wordsresults` (
  `NR` bigint(5) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `max` int(11) NOT NULL DEFAULT '0',
  `score` int(11) NOT NULL DEFAULT '0',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `valid` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`NR`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
