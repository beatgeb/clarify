CREATE TABLE `project_permission` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `creator` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modifier` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `user` int(11) NOT NULL,
  `project` int(11) NOT NULL,
  `permission` enum('EDIT','COMMENT','VIEW','ADMIN') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;