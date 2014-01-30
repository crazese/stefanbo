-- MySQL dump 10.10
--
-- Host: localhost    Database: oncecode
-- ------------------------------------------------------
-- Server version	5.1.11-beta-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES latin1 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin_userinfo`
--

DROP TABLE IF EXISTS `admin_userinfo`;
CREATE TABLE `admin_userinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userName` varchar(30) DEFAULT NULL,
  `userPswd` varchar(50) DEFAULT NULL,
  `userType` int(11) DEFAULT '0',
  `truth_name` varchar(30) DEFAULT NULL,
  `workId` varchar(30) DEFAULT NULL,
  `dptMent` varchar(50) DEFAULT NULL,
  `ifOnline` enum('0','1') DEFAULT '0',
  `remark` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uni_userName` (`userName`),
  KEY `uni_userType` (`userType`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin_userinfo`
--


/*!40000 ALTER TABLE `admin_userinfo` DISABLE KEYS */;
LOCK TABLES `admin_userinfo` WRITE;
INSERT INTO `admin_userinfo` VALUES (1,'admin','e10adc3949ba59abbe56e057f20f883e',0,'test','1008','oncecode','0','administrator'),(9,'test','e10adc3949ba59abbe56e057f20f883e',2,'test','1001','oncecode','0',NULL),(12,'super','e10adc3949ba59abbe56e057f20f883e',1,'test','1002','oncecode','0',NULL);
UNLOCK TABLES;
/*!40000 ALTER TABLE `admin_userinfo` ENABLE KEYS */;

--
-- Table structure for table `sys_menu`
--

DROP TABLE IF EXISTS `sys_menu`;
CREATE TABLE `sys_menu` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `higherMenu` varchar(20) DEFAULT NULL,
  `menuName` varchar(20) DEFAULT NULL,
  `linkPage` varchar(100) DEFAULT NULL,
  `picPath` varchar(100) DEFAULT NULL,
  `masterQx` int(2) DEFAULT '0',
  `menuLevel` int(2) DEFAULT '0',
  `orderNum` int(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sys_menu`
--


/*!40000 ALTER TABLE `sys_menu` DISABLE KEYS */;
LOCK TABLES `sys_menu` WRITE;
INSERT INTO `sys_menu` VALUES (1,'#','信息管理',NULL,NULL,0,0,0),(2,'#','基本管理',NULL,NULL,0,0,1),(3,'#','内部管理',NULL,NULL,0,0,2),(4,'#','系统管理',NULL,NULL,0,0,3),(5,'系统管理','系统设置','','',0,1,100),(8,'系统管理','帐号管理','','',1,1,1),(9,'帐号管理','所有帐号','users/showuser.php','images/upPics/00.gif',1,2,6),(10,'系统设置','系统菜单','sysConfig/showMenu.php','images/upPics/05.gif',0,2,6),(11,'帐号管理','添加帐号','users/alterUser.php','images/upPics/09.gif',1,2,1),(12,'系统设置','添加菜单','sysconfig/alterMenu.php','images/upPics/09.gif',1,2,1),(13,'基本管理','产品管理','','',2,1,1),(14,'信息管理','信息回复','#','',2,1,1),(15,'内部管理','员工管理','#','',2,1,1);
UNLOCK TABLES;
/*!40000 ALTER TABLE `sys_menu` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

