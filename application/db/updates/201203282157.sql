ALTER TABLE `screen` ADD `count_comment` INT(11)  NOT NULL  DEFAULT '0'  AFTER `embeddable`;
ALTER TABLE `screen` ADD `count_measure` INT(11)  NOT NULL  DEFAULT '0'  AFTER `count_comment`;
ALTER TABLE `screen` ADD `count_color` INT(11)  NOT NULL  DEFAULT '0'  AFTER `count_measure`;
