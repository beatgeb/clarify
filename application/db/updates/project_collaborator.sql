DROP TABLE IF EXISTS `project_collaborator`;
CREATE TABLE `project_collaborator` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `creator` int(11) unsigned DEFAULT NULL,
  `created` datetime NOT NULL,
  `email` varchar(255) NOT NULL,
  `project` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;