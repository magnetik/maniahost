-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.5.13-log - MySQL Community Server (GPL)
-- Server OS:                    Win32
-- HeidiSQL version:             6.0.0.4024
-- Date/time:                    2012-01-25 16:24:41
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;

-- Dumping structure for table ManiaHost.Rents
CREATE TABLE IF NOT EXISTS `Rents` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `playerLogin` varchar(25) NOT NULL,
  `rentDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `duration` int(10) unsigned NOT NULL,
  `cost` int(10) unsigned NOT NULL,
  `serverOptions` text NOT NULL,
  `gameInfos` text NOT NULL,
  `maps` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table ManiaHost.Servers
CREATE TABLE IF NOT EXISTS `Servers` (
  `hostname` varchar(15) NOT NULL,
  `port` int(10) NOT NULL,
  `login` varchar(25) NOT NULL,
  `superAdminPassword` varchar(50) NOT NULL DEFAULT 'SuperAdmin',
  `status` varchar(50) NOT NULL,
  `idRent` int(11) DEFAULT NULL,
  PRIMARY KEY (`hostname`,`port`),
  UNIQUE KEY `login` (`login`),
  KEY `FK_Servers_Rents` (`idRent`),
  CONSTRAINT `FK_Servers_Rents` FOREIGN KEY (`idRent`) REFERENCES `Rents` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
