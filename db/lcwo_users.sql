-- note: the password hashes are only correct for the default salt values set in definitions.php. If you change the salt, create a new password hash!
LOCK TABLES `lcwo_users` WRITE;
INSERT INTO `lcwo_users` VALUES (1,'test','db533528be4b79c06f55666d6d4fba28','','Test user','','2008-05-23',20,20,600,1,3,'de',0,1,1,0,5,0,'','',0,1,0,'letters',0,0,0,0,'eu',1,0);
INSERT INTO `lcwo_users` VALUES (2,'admin', '9b336436b1e0167d228051f9d876ae35','','Admin user','','2008-05-23',20,20,600,1,3,'de',0,1,1,0,5,0,'','',0,1,0,'letters',0,0,0,0,'eu',1,0);
UNLOCK TABLES;

