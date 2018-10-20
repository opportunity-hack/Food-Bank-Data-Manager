-- CXA Auth LW Database configuration

SET NAMES utf8;
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE DATABASE `cxa` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;
USE `cxa`;

DROP TABLE IF EXISTS `auth_tokens`;
CREATE TABLE `auth_tokens` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `selector` char(12) DEFAULT NULL,
  `token` char(64) DEFAULT NULL,
  `userid` int(11) unsigned NOT NULL,
  `expires` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;


DROP TABLE IF EXISTS `password_reset`;
CREATE TABLE `password_reset` (
  `userid` int(11) unsigned NOT NULL,
  `token` char(64) NOT NULL,
  `expires` int(11) NOT NULL,
  PRIMARY KEY (`userid`),
  UNIQUE KEY `userid_UNIQUE` (`userid`)
) ENGINE=InnoDB;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `userid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(45) NOT NULL,
  `password` char(60) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `email` longtext,
  `authorization` int(11) unsigned NOT NULL DEFAULT '0',
  `otpsecret` char(16) DEFAULT NULL,
  PRIMARY KEY (`userid`),
  UNIQUE KEY `userid_UNIQUE` (`userid`)
) ENGINE=InnoDB;

INSERT INTO `users` (`userid`, `username`, `password`, `name`, `email`, `authorization`, `otpsecret`) VALUES
(1,	'admin',	'$2y$10$BQQf70BbggdS3bP.22seVeAvzrxEjWs.0c/ufP6gzXbq/cxs6DW6K',	'Administrator',	'example@example.com',	4,	''),
(777,	'guest',	'heaven',	'guest',	NULL,	0,	'');

DROP TABLE IF EXISTS `user_limbo`;
CREATE TABLE `user_limbo` (
  `userid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(45) NOT NULL,
  `password` char(60) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `email` longtext,
  `otpsecret` char(16) DEFAULT NULL,
  PRIMARY KEY (`userid`),
  UNIQUE KEY `userid_UNIQUE` (`userid`)
) ENGINE=InnoDB;
