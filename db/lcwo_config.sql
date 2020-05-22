--
-- Table structure for table `lcwo_config`
--

DROP TABLE IF EXISTS `lcwo_config`;
CREATE TABLE `lcwo_config` (
  `ID` bigint(5) NOT NULL AUTO_INCREMENT,
  `key` varchar(64) NOT NULL DEFAULT '',
  `val` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  KEY `key` (`key`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `lcwo_config`
--

LOCK TABLES `lcwo_config` WRITE;
INSERT INTO `lcwo_config` VALUES (1,'localeversion','1004');
UNLOCK TABLES;
