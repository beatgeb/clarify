ALTER TABLE `activity` ADD `target` INT(11)  NULL  DEFAULT NULL  AFTER `object_type`;
ALTER TABLE `activity` ADD `target_type` ENUM('PROJECT','SCREEN','USER','MEASURE','COLOR','FONT','COMMENT','MODULE')  NULL  DEFAULT NULL  AFTER `target`;
ALTER TABLE `activity` ADD `title` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `target_type`;
ALTER TABLE `activity` ADD `actor_title` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `title`;
ALTER TABLE `activity` ADD `object_title` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `actor_title`;
ALTER TABLE `activity` ADD `target_title` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `object_title`;