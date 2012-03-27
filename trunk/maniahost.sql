/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;


-- Dumping structure for table ManiaHost.Analytics
CREATE TABLE IF NOT EXISTS `Analytics` (
  `serverLogin` varchar(25) NOT NULL,
  `insertDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `avgPlayers` float NOT NULL,
  `maxPlayer` int(11) NOT NULL,
  PRIMARY KEY (`serverLogin`,`insertDate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table ManiaHost.Incomes
CREATE TABLE IF NOT EXISTS `Incomes` (
  `transactionDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `revenue` int(11) NOT NULL,
  `transactionCount` int(10) NOT NULL,
  PRIMARY KEY (`transactionDate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table ManiaHost.Maps
CREATE TABLE IF NOT EXISTS `Maps` (
  `path` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `name` varchar(60) NOT NULL,
  `author` varchar(25) NOT NULL,
  `authorTime` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `nbLaps` int(11) NOT NULL,
  `environment` varchar(50) NOT NULL,
  `fileSize` int(11) NOT NULL,
  PRIMARY KEY (`path`,`filename`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


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

ALTER TABLE `Rents`	DROP COLUMN `cost`;
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
  UNIQUE KEY `idRent` (`idRent`),
  CONSTRAINT `FK_Servers_Rents` FOREIGN KEY (`idRent`) REFERENCES `Rents` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
