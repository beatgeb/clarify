# ************************************************************
# Sequel Pro SQL dump
# Version 3408
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 127.0.0.1 (MySQL 5.5.20)
# Database: clarify
# Generation Time: 2012-03-14 11:34:07 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table behaviour
# ------------------------------------------------------------

DROP TABLE IF EXISTS `behaviour`;

CREATE TABLE `behaviour` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `creator` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modifier` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `screen` int(11) DEFAULT NULL,
  `behaviour` int(11) DEFAULT NULL,
  `x` int(11) DEFAULT NULL,
  `y` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table behaviour_event
# ------------------------------------------------------------

DROP TABLE IF EXISTS `behaviour_event`;

CREATE TABLE `behaviour_event` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `creator` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modifier` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `behaviour` int(11) NOT NULL,
  `event` int(11) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table behaviour_option
# ------------------------------------------------------------

DROP TABLE IF EXISTS `behaviour_option`;

CREATE TABLE `behaviour_option` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `creator` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modifier` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `behaviour` int(11) NOT NULL,
  `option` int(11) NOT NULL,
  `value` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table changelog
# ------------------------------------------------------------

DROP TABLE IF EXISTS `changelog`;

CREATE TABLE `changelog` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table color
# ------------------------------------------------------------

DROP TABLE IF EXISTS `color`;

CREATE TABLE `color` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `creator` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modifier` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `screen` int(11) DEFAULT NULL,
  `color` int(11) DEFAULT NULL,
  `x` int(11) DEFAULT NULL,
  `y` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `screen` (`screen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table comment
# ------------------------------------------------------------

DROP TABLE IF EXISTS `comment`;

CREATE TABLE `comment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `creator` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modifier` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `screen` int(11) NOT NULL,
  `layer` int(11) NOT NULL,
  `nr` int(11) DEFAULT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `w` int(11) DEFAULT NULL,
  `h` int(11) DEFAULT NULL,
  `content` text,
  PRIMARY KEY (`id`),
  KEY `screen` (`screen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table font
# ------------------------------------------------------------

DROP TABLE IF EXISTS `font`;

CREATE TABLE `font` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table library_behaviour
# ------------------------------------------------------------

DROP TABLE IF EXISTS `library_behaviour`;

CREATE TABLE `library_behaviour` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `creator` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modifier` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `vendor` varchar(40) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table library_behaviour_event
# ------------------------------------------------------------

DROP TABLE IF EXISTS `library_behaviour_event`;

CREATE TABLE `library_behaviour_event` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `creator` int(1) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modifier` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `behaviour` int(11) DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table library_behaviour_option
# ------------------------------------------------------------

DROP TABLE IF EXISTS `library_behaviour_option`;

CREATE TABLE `library_behaviour_option` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `creator` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modifier` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `behaviour` int(11) NOT NULL,
  `name` varchar(40) NOT NULL DEFAULT '',
  `value_type` varchar(40) NOT NULL DEFAULT '',
  `value_default` varchar(40) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table library_component
# ------------------------------------------------------------

DROP TABLE IF EXISTS `library_component`;

CREATE TABLE `library_component` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `creator` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modifier` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `vendor` varchar(100) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table library_component_behaviour
# ------------------------------------------------------------

DROP TABLE IF EXISTS `library_component_behaviour`;

CREATE TABLE `library_component_behaviour` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `creator` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modifier` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `component` int(11) DEFAULT NULL,
  `behaviour` int(11) DEFAULT NULL,
  `state` enum('REQUESTED','DEVELOPMENT','READY') DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table measure
# ------------------------------------------------------------

DROP TABLE IF EXISTS `measure`;

CREATE TABLE `measure` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `creator` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modifier` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `screen` int(11) DEFAULT NULL,
  `type` enum('SINGLE','SIZE','ELEMENT') DEFAULT 'SINGLE',
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `screen` (`screen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table project
# ------------------------------------------------------------

DROP TABLE IF EXISTS `project`;

CREATE TABLE `project` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `creator` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modifier` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `short` varchar(40) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table project_color
# ------------------------------------------------------------

DROP TABLE IF EXISTS `project_color`;

CREATE TABLE `project_color` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `creator` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modifier` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `project` int(11) NOT NULL,
  `reference_color` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `r` int(4) DEFAULT '0',
  `g` int(4) DEFAULT '0',
  `b` int(4) DEFAULT '0',
  `hex` varchar(6) DEFAULT NULL,
  `alpha` int(4) DEFAULT '0',
  `hue` int(4) DEFAULT NULL,
  `saturation` int(4) DEFAULT NULL,
  `brightness` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project` (`project`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table screen
# ------------------------------------------------------------

DROP TABLE IF EXISTS `screen`;

CREATE TABLE `screen` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `creator` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modifier` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `project` int(11) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text,
  `width` int(11) DEFAULT '1024',
  `height` int(11) DEFAULT NULL,
  `align_horizontal` enum('LEFT','CENTER','RIGHT') DEFAULT 'CENTER',
  `align_vertical` enum('TOP','CENTER','BOTTOM') DEFAULT 'TOP',
  `type` varchar(40) DEFAULT NULL,
  `ext` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project` (`project`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `creator` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `username` varchar(40) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `twitter_user_id` int(11) DEFAULT NULL,
  `twitter_screen_name` varchar(40) DEFAULT NULL,
  `twitter_oauth_token` varchar(100) DEFAULT NULL,
  `twitter_oauth_secret` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `twitter_user_id` (`twitter_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
