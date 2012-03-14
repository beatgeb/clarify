ALTER TABLE `color` ADD INDEX (`screen`);
ALTER TABLE `project_color` ADD INDEX (`project`);
ALTER TABLE `measure` ADD INDEX (`screen`);
ALTER TABLE `screen` ADD INDEX (`project`);
ALTER TABLE `comment` ADD INDEX (`screen`);