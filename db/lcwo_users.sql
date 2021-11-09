-- note: the password hashes are only correct for the default salt values set in definitions.php. If you change the salt, create a new password hash!
LOCK TABLES `lcwo_users` WRITE;
INSERT INTO `lcwo_users` VALUES (1,'test','7ed9c8067c5ad3470f8a248d040bf97c','','Test user','','2008-05-23',20,20,0,600,1,1,'en',0,1,1,0,5,0,'','',0,1,0,'letters',0,0,0,0,'style',1,0);
INSERT INTO `lcwo_users` VALUES (2,'admin', '9b336436b1e0167d228051f9d876ae35','','Admin user','','2008-05-23',20,20,0,600,1,1,'en',0,1,1,0,5,0,'','',0,1,0,'letters',0,0,0,0,'style',1,0);
UNLOCK TABLES;

