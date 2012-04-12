ALTER TABLE `screen` ADD `code` CHAR(8) NULL DEFAULT NULL AFTER `project`;
ALTER TABLE `screen` ADD UNIQUE INDEX (`code`);
ALTER TABLE `screen` ADD `count_font` INT(11) NOT NULL AFTER `count_color`;
ALTER TABLE `project` ADD `slug` VARCHAR(40) NULL DEFAULT NULL AFTER `screen_count`;
ALTER TABLE `project` DROP `short`;
ALTER TABLE `project` ADD UNIQUE INDEX (`creator`, `slug`);

DROP TABLE `font`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;