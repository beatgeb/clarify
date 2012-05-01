--
-- Add module count to table screen
ALTER TABLE screen ADD  `count_module` int(11) NOT NULL DEFAULT '0' ;

--
-- Table structure for table `module`
--

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=40 ;

-- --------------------------------------------------------

--
-- Table structure for table `project_module`
--

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;
