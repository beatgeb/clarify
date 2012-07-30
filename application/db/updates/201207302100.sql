CREATE TABLE `activity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `creator` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `actor` int(11) NOT NULL,
  `actor_type` enum('PROJECT','SCREEN','USER','MEASURE','COLOR','FONT','COMMENT') NOT NULL DEFAULT 'PROJECT',
  `verb` enum('NONE','COMMENT','MEASURE','PICK','DEFINE','JOIN') NOT NULL DEFAULT 'NONE',
  `object` int(11) NOT NULL,
  `object_type` enum('PROJECT','SCREEN','USER','MEASURE','COLOR','FONT','COMMENT') NOT NULL DEFAULT 'PROJECT',
  PRIMARY KEY (`id`),
  KEY `actor` (`actor`,`actor_type`),
  KEY `object` (`object`,`object_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;