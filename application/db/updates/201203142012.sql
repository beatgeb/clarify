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