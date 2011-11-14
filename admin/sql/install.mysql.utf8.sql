
CREATE TABLE IF NOT EXISTS `#__techjoomlaAPI_users` (
  `id` int(11) NOT NULL auto_increment,
  `api` varchar(200) NOT NULL,
  `token` TEXT NOT NULL,
  `user_id` int(11) NOT NULL,
  `client` varchar(200) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

