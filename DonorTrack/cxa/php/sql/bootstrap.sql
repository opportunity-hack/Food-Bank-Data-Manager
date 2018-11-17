-- CXA Auth LW Database configuration v2.02

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;

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
(777,	'guest',	'guest',	'guest',	NULL,	0,	'');


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


DROP TABLE IF EXISTS `dashboard_data`;
CREATE TABLE `dashboard_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `frame_id` int(10) unsigned NOT NULL,
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `url` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB;


CREATE TABLE `grocery_list` (
  `name` varchar(500) DEFAULT NULL
) ENGINE=InnoDB;

INSERT INTO `grocery_list` (`name`) VALUES
('AJ\'s Fine Foods'),
('Albertsons'),
('Bashas'),
('CVS'),
('El Super'),
('Fry\'s'),
('Frys'),
('Los Altos Ranch Market'),
('Safeway'),
('Starbucks'),
('Target Store'),
('Walmart'),
('Winco');


DROP TABLE IF EXISTS `report_emails`;
CREATE TABLE `report_emails` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC)
) ENGINE = InnoDB;


DROP TABLE IF EXISTS `schema_version`;
CREATE TABLE `schema_version` (
  `version` DECIMAL(10, 5) NOT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB;

LOCK TABLES `schema_version` WRITE;
/*!40000 ALTER TABLE `schema_version` DISABLE KEYS */;
INSERT INTO `schema_version` (`version`) VALUES ('2.01');
/*!40000 ALTER TABLE `schema_version` ENABLE KEYS */;
UNLOCK TABLES;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
