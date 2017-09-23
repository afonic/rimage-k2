/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


DROP TABLE IF EXISTS `#__rimage_gallery_hashes`;

CREATE TABLE `#__rimage_gallery_hashes` (
  `itemid` int(11) unsigned NOT NULL,
  `hash` varchar(255) NOT NULL DEFAULT '',
  `path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`itemid`),
  KEY `hash` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table #__rimage_gallery_images
# ------------------------------------------------------------

DROP TABLE IF EXISTS `#__rimage_gallery_images`;

CREATE TABLE `#__rimage_gallery_images` (
  `itemid` int(11) unsigned NOT NULL,
  `galleryset` varchar(255) NOT NULL DEFAULT '',
  `path` varchar(255) NOT NULL DEFAULT '',
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `orig_path` varchar(255) NOT NULL DEFAULT '',
  `timestamp` char(11) DEFAULT NULL,
  KEY `itemid` (`itemid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table #__rimage_item_images
# ------------------------------------------------------------

DROP TABLE IF EXISTS `#__rimage_item_images`;

CREATE TABLE `#__rimage_item_images` (
  `itemid` int(11) unsigned NOT NULL,
  `galleryset` varchar(255) NOT NULL DEFAULT '',
  `path` varchar(255) NOT NULL DEFAULT '',
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `orig_path` varchar(255) NOT NULL DEFAULT '',
  `timestamp` char(11) DEFAULT NULL,
  KEY `itemid` (`itemid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
