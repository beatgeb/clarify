ALTER TABLE `user` ADD UNIQUE INDEX `email` (`email`);
ALTER TABLE `user` CHANGE `email` `email` VARCHAR(255)  NOT NULL  DEFAULT '';
