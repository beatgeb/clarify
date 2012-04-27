-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 27, 2012 at 10:09 PM
-- Server version: 5.5.9
-- PHP Version: 5.3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `clarify`
--

-- --------------------------------------------------------

--
-- Table structure for table `behaviour`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `behaviour_event`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `behaviour_option`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `changelog`
--

DROP TABLE IF EXISTS `changelog`;
CREATE TABLE `changelog` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `color`
--

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=62 ;

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Table structure for table `font`
--

DROP TABLE IF EXISTS `font`;
CREATE TABLE `font` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `creator` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modifier` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `screen` int(11) NOT NULL,
  `font` int(11) NOT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `screen` (`screen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `library_behaviour`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `library_behaviour_event`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `library_behaviour_option`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `library_component`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `library_component_behaviour`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `measure`
--

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `module`
--

DROP TABLE IF EXISTS `module`;
CREATE TABLE `module` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `creator` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modifier` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `screen` int(11) DEFAULT NULL,
  `module` int(11) DEFAULT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `screen` (`screen`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=328 ;

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

DROP TABLE IF EXISTS `project`;
CREATE TABLE `project` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `creator` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modifier` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `screen_count` int(11) NOT NULL DEFAULT '0',
  `slug` varchar(40) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `creator` (`creator`,`slug`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `project_color`
--

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
  `name_css` varchar(255) DEFAULT NULL,
  `r` int(4) DEFAULT '0',
  `g` int(4) DEFAULT '0',
  `b` int(4) DEFAULT '0',
  `hex` varchar(6) DEFAULT NULL,
  `alpha` int(4) DEFAULT '0',
  `hue` int(4) NOT NULL DEFAULT '0',
  `saturation` int(4) NOT NULL DEFAULT '0',
  `lightness` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project` (`project`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=39 ;

-- --------------------------------------------------------

--
-- Table structure for table `project_font`
--

DROP TABLE IF EXISTS `project_font`;
CREATE TABLE `project_font` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `creator` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modifier` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `project` int(11) DEFAULT NULL,
  `reference_font` int(11) DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `name_css` varchar(40) DEFAULT NULL,
  `family` varchar(255) DEFAULT '',
  `size` int(11) DEFAULT NULL,
  `size_em` float DEFAULT NULL,
  `line_height` float DEFAULT NULL,
  `transform` enum('UPPERCASE','LOWERCASE','CAPITALIZE') DEFAULT NULL,
  `color` int(11) DEFAULT NULL,
  `color_background` int(11) DEFAULT NULL,
  `color_hover` int(11) DEFAULT NULL,
  `color_active` int(11) DEFAULT NULL,
  `color_visited` int(11) DEFAULT NULL,
  `style` enum('NORMAL','ITALIC','OBLIQUE') DEFAULT NULL,
  `style_hover` enum('NORMAL','ITALIC','OBLIQUE') DEFAULT NULL,
  `style_active` enum('NORMAL','ITALIC','OBLIQUE') DEFAULT NULL,
  `style_visited` enum('NORMAL','ITALIC','OBLIQUE') DEFAULT NULL,
  `weight` varchar(10) DEFAULT NULL,
  `weight_hover` varchar(10) DEFAULT NULL,
  `weight_active` varchar(10) DEFAULT NULL,
  `weight_visited` varchar(10) DEFAULT NULL,
  `decoration` varchar(20) DEFAULT NULL,
  `decoration_hover` varchar(20) DEFAULT NULL,
  `decoration_active` varchar(20) DEFAULT NULL,
  `decoration_visited` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project` (`project`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `project_module`
--

DROP TABLE IF EXISTS `project_module`;
CREATE TABLE `project_module` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `creator` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modifier` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `project` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `skin` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project` (`project`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=248 ;

-- --------------------------------------------------------

--
-- Table structure for table `screen`
--

DROP TABLE IF EXISTS `screen`;
CREATE TABLE `screen` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `creator` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modifier` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `project` int(11) NOT NULL,
  `code` char(8) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text,
  `width` int(11) DEFAULT '1024',
  `height` int(11) DEFAULT NULL,
  `align_horizontal` enum('LEFT','CENTER','RIGHT') DEFAULT 'CENTER',
  `align_vertical` enum('TOP','CENTER','BOTTOM') DEFAULT 'TOP',
  `type` varchar(40) DEFAULT NULL,
  `ext` varchar(40) DEFAULT NULL,
  `embeddable` enum('TRUE','FALSE') DEFAULT NULL,
  `count_comment` int(11) NOT NULL DEFAULT '0',
  `count_measure` int(11) NOT NULL DEFAULT '0',
  `count_color` int(11) NOT NULL DEFAULT '0',
  `count_font` int(11) NOT NULL DEFAULT '0',
  `count_module` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `project` (`project`),
  KEY `project_2` (`project`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `creator` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `username` varchar(40) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `twitter_user_id` int(11) DEFAULT NULL,
  `twitter_screen_name` varchar(40) DEFAULT NULL,
  `twitter_oauth_token` varchar(100) DEFAULT NULL,
  `twitter_oauth_secret` varchar(100) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `invitation_code` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `twitter_user_id` (`twitter_user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;
