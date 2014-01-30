-- MySQL dump 10.11
--
-- Host: localhost    Database: authuser
-- ------------------------------------------------------
-- Server version	5.0.45

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

--
-- Table structure for table `Product`
--

DROP TABLE IF EXISTS `Product`;
CREATE TABLE `Product` (
  `intID` int(11) NOT NULL auto_increment,
  `strProductName` char(255) default NULL,
  `intUnlockType` int(11) NOT NULL default '0',
  `strFilePath` char(255) default NULL,
  `intCodeVersion` int(11) default NULL,
  `strCodeSeed` char(64) default NULL,
  PRIMARY KEY  (`intID`)
) ENGINE=MyISAM AUTO_INCREMENT=58 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Product`
--

LOCK TABLES `Product` WRITE;
/*!40000 ALTER TABLE `Product` DISABLE KEYS */;
INSERT INTO `Product` VALUES (1,'Sothink DHTMLMenu',1,'/var/www/reguser/packages/sdmenustd.zip',1,''),(3,'Sothink Glanda 2001a',1,'/var/www/reguser/packages/glandastd.zip',1,NULL),(4,'Sothink CoolMenu 3.0',2,'/var/www/reguser/packages/coolnav.zip',1,'AppletMenuWizard100'),(5,'Sothink HTML Editor 2.5',1,'/var/www/reguser/packages/she25std.zip',1,NULL),(6,'Sothink SWF Decompiler MX 2002 Pro',1,'/var/www/reguser/packages/swfdecpro.zip',1,''),(7,'Sothink Glanda 2001a (Chinese Simplified)',1,'/var/www/reguser/packages/glandachsstd.zip',1,NULL),(8,'&#30805;&#24605;&#38378;&#23458;&#31934;&#28789; MX 2002 &#19987;&#19994;&#29256; &#65288;&#31616;&#20307;&#20013;&#25991;&#29256;&#65289;',1,'/var/www/reguser/packages/swfdecchspro.zip',1,NULL),(9,'&#30805;&#24605;&#39764;&#27861;&#33756;&#21333; 3.7&#65288;&#31616;&#20307;&#20013;&#25991;&#29256;&#65289; 	',1,'/var/www/reguser/packages/sdmenuchsstd.zip',1,NULL),(10,'&#30805;&#24605;&#39764;&#27861;&#33756;&#21333; 3.7&#65288;&#32321;&#20307;&#20013;&#25991;&#29256;&#65289;',1,'/var/www/reguser/packages/sdmenuchtstd.zip',1,NULL),(11,'&#30805;&#24605;&#20027;&#39029;&#32534;&#36753;&#22120; 2.0 &#65288;&#31616;&#20307;&#20013;&#25991;&#29256;&#65289;',1,'/var/www/reguser/packages/ctpg2procn.zip',1,NULL),(12,'Sothink CoolMenu 3.0 (Chinese Simplified)',2,'/var/www/reguser/packages/coolnav.zip',1,'AppletMenuWizard100'),(13,'&#30805;&#24605;&#21363;&#26102;&#36890; 20&#20154;&#29256;',1,'/var/www/reguser/packages/sm11cn20.zip',1,NULL),(14,'&#30805;&#24605;&#21363;&#26102;&#36890; 50&#20154;&#29256;',1,'/var/www/reguser/packages/sm11cn50.zip',1,NULL),(15,'&#30805;&#24605;&#21363;&#26102;&#36890; 80&#20154;&#29256;',1,'/var/www/reguser/packages/sm11cn80.zip',1,NULL),(16,'Sothink SlidingMenu 2.0',2,'/var/www/reguser/packages/slidclass.zip',1,'AppletSlidMenuWizard100'),(17,'SourceTec Messenger 1.1',1,'/var/www/reguser/packages/sm11en.zip',1,NULL),(18,'CoolText',2,'/var/www/reguser/packages/srcctx.zip',1,'AppletCoolTextWizard100'),(19,'CoolButton',2,'/var/www/reguser/packages/srcbtn.zip',1,'CutePageCoolButton100'),(20,'CutePage Professional (English version)',1,'/var/www/reguser/packages/ctpg2proe.zip',1,NULL),(21,'Sothink DHTMLMenu 4',1,'/var/www/reguser/packages/sdmenustd4.zip',1,NULL),(22,'Sothink SWF Quicker',3,'/var/www/reguser/packages/swfquickerstd.zip',2,''),(23,'Sothink DHTMLMenu 5',3,'',2,''),(24,'Sothink SWF Decompiler MX 2005',3,'',2,''),(25,'Sothink Glanda 2005 (abounded)',3,'',2,''),(26,'Sothink FlashVideo Encoder',3,'',2,''),(27,'Sothink DVD EzWorkshop',3,'',2,''),(28,'Sothink Glanda 2005',3,'',2,''),(29,'Sothink MenuTree',3,'',2,''),(30,'SWF Decompiler JPN',3,'',2,''),(31,'SWF Decompiler CHT',3,'',2,''),(32,'DVD2AVI',3,'',2,''),(33,'iPod Video Convertor',3,'',2,''),(34,'SWF Decompiler DEU',3,'',2,''),(35,'Jet DVD Ripper',3,'',2,''),(36,'Flash to Video',3,'',2,''),(37,'DVD to iPod Converter',3,'',2,''),(38,'Video to PSP',3,'',2,''),(39,'DVD to PSP',3,'',2,''),(40,'Video to 3GP',3,'',2,''),(41,'DVD to 3GP',3,'',2,''),(42,'Movie DVD Maker',3,'',2,''),(43,'Sothink Video Encoder for Adobe Flash (Command-Line Version)',3,'',2,''),(44,'Sothink FLV Player',3,'',1,''),(45,'Sothink Web Video Downloader for Firefox',3,'',2,''),(46,'Sothink DHTMLMenu Lite',3,'',2,''),(47,'Sothink Web Video Downloader',3,'',2,''),(48,'Sothink JavaScript Web Scroller',3,'',1,''),(56,'Quicker for Silverlight',3,'',1,''),(51,'Sothink FLV to Video Converte',3,'',1,''),(52,'Sothink Video Converter',3,'',1,''),(53,'Sothink Video Encoder Engine for Adobe Flash (Linux Version)',3,'',1,''),(57,'Sothink HD Movie Maker',3,'',1,'');
/*!40000 ALTER TABLE `Product` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2009-06-24  6:46:13
