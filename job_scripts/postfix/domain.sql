/*
Navicat MySQL Data Transfer

Source Server         : 59.175.238.6_mysql
Source Server Version : 40120
Source Host           : 59.175.238.6:3306
Source Database       : extmail

Target Server Type    : MYSQL
Target Server Version : 40120
File Encoding         : 65001

Date: 2014-01-26 11:26:24
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for domain
-- ----------------------------
DROP TABLE IF EXISTS `domain`;
CREATE TABLE `domain` (
  `domain` varchar(255) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `hashdirpath` varchar(255) NOT NULL default '',
  `maxalias` int(10) NOT NULL default '0',
  `maxusers` int(10) NOT NULL default '0',
  `maxquota` varchar(16) NOT NULL default '0',
  `maxnetdiskquota` varchar(16) NOT NULL default '0',
  `transport` varchar(255) default NULL,
  `can_signup` tinyint(1) NOT NULL default '0',
  `default_quota` varchar(255) default NULL,
  `default_netdiskquota` varchar(255) default NULL,
  `default_expire` varchar(12) default NULL,
  `disablesmtpd` smallint(1) default NULL,
  `disablesmtp` smallint(1) default NULL,
  `disablewebmail` smallint(1) default NULL,
  `disablenetdisk` smallint(1) default NULL,
  `disableimap` smallint(1) default NULL,
  `disablepop3` smallint(1) default NULL,
  `createdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `expiredate` date NOT NULL default '0000-00-00',
  `active` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`domain`),
  KEY `domain` USING BTREE (`domain`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='ExtMail - Virtual Domains';

-- ----------------------------
-- Records of domain
-- ----------------------------
INSERT INTO `domain` VALUES ('sothink.com', 'virtualDomain for sothink.com', 'A0/B0', '100', '100', '214748364800S', '107374182400S', '', '0', '1048576000S', '1048576000S', '3y', '0', '0', '0', '0', '1', '0', '2008-10-04 15:10:04', '2015-11-08', '1');
INSERT INTO `domain` VALUES ('dhtml-menu-builder.com', 'virtualDomain for dhtml-menu-builder.com', 'A0/B0', '100', '100', '214748364800S', '107374182400S', '', '0', '1048576000S', '1048576000S', '3y', '0', '0', '0', '0', '1', '0', '2008-10-04 15:10:04', '2015-11-08', '1');
INSERT INTO `domain` VALUES ('flash-animation-maker.com', 'virtualDomain for flash-animation-maker.com', 'A0/B0', '100', '100', '214748364800S', '107374182400S', '', '0', '1048576000S', '1048576000S', '3y', '0', '0', '0', '0', '1', '0', '2008-10-04 15:10:04', '2015-11-08', '1');
INSERT INTO `domain` VALUES ('flashpioneer.com', 'virtualDomain for flashpioneer.com', 'A0/B0', '100', '100', '214748364800S', '107374182400S', '', '0', '1048576000S', '1048576000S', '3y', '0', '0', '0', '0', '1', '0', '2008-10-04 15:10:04', '2015-11-08', '1');
INSERT INTO `domain` VALUES ('flash-to-html5.net', 'virtualDomain for flash-to-html5.net', 'A0/B0', '100', '100', '214748364800S', '107374182400S', '', '0', '1048576000S', '1048576000S', '3y', '0', '0', '0', '0', '1', '0', '2008-10-04 15:10:04', '2015-11-08', '1');
INSERT INTO `domain` VALUES ('flash-video-converters.com', 'virtualDomain for flash-video-converters.com', 'A0/B0', '100', '100', '214748364800S', '107374182400S', '', '0', '1048576000S', '1048576000S', '3y', '0', '0', '0', '0', '1', '0', '2008-10-04 15:10:04', '2015-11-08', '1');
INSERT INTO `domain` VALUES ('freetodown.com', 'virtualDomain for freetodown.com', 'A0/B0', '100', '100', '214748364800S', '107374182400S', '', '0', '1048576000S', '1048576000S', '3y', '0', '0', '0', '0', '1', '0', '2008-10-04 15:10:04', '2015-11-08', '1');
INSERT INTO `domain` VALUES ('jofgame.com', 'virtualDomain for jofgame.com', 'A0/B0', '100', '100', '214748364800S', '107374182400S', '', '0', '1048576000S', '1048576000S', '3y', '0', '0', '0', '0', '1', '0', '2008-10-04 15:10:04', '2015-11-08', '1');
INSERT INTO `domain` VALUES ('logo-creator.net', 'virtualDomain for logo-creator.net', 'A0/B0', '100', '100', '214748364800S', '107374182400S', '', '0', '1048576000S', '1048576000S', '3y', '0', '0', '0', '0', '1', '0', '2008-10-04 15:10:04', '2015-11-08', '1');
INSERT INTO `domain` VALUES ('logo-maker.net', 'virtualDomain for logo-maker.net', 'A0/B0', '100', '100', '214748364800S', '107374182400S', '', '0', '1048576000S', '1048576000S', '3y', '0', '0', '0', '0', '1', '0', '2008-10-04 15:10:04', '2015-11-08', '1');
INSERT INTO `domain` VALUES ('movie-burners.com', 'virtualDomain for movie-burners.com', 'A0/B0', '100', '100', '214748364800S', '107374182400S', '', '0', '1048576000S', '1048576000S', '3y', '0', '0', '0', '0', '1', '0', '2008-10-04 15:10:04', '2015-11-08', '1');
INSERT INTO `domain` VALUES ('myconverters.com', 'virtualDomain for myconverters.com', 'A0/B0', '100', '100', '214748364800S', '107374182400S', '', '0', '1048576000S', '1048576000S', '3y', '0', '0', '0', '0', '1', '0', '2008-10-04 15:10:04', '2015-11-08', '1');
INSERT INTO `domain` VALUES ('mylogomaker.de', 'virtualDomain for mylogomaker.de', 'A0/B0', '100', '100', '214748364800S', '107374182400S', '', '0', '1048576000S', '1048576000S', '3y', '0', '0', '0', '0', '1', '0', '2008-10-04 15:10:04', '2015-11-08', '1');
INSERT INTO `domain` VALUES ('photo-album-maker.com', 'virtualDomain for photo-album-maker.com', 'A0/B0', '100', '100', '214748364800S', '107374182400S', '', '0', '1048576000S', '1048576000S', '3y', '0', '0', '0', '0', '1', '0', '2008-10-04 15:10:04', '2015-11-08', '1');
INSERT INTO `domain` VALUES ('sothink.com.cn', 'virtualDomain for sothink.com.cn', 'A0/B0', '100', '100', '214748364800S', '107374182400S', '', '0', '1048576000S', '1048576000S', '3y', '0', '0', '0', '0', '1', '0', '2008-10-04 15:10:04', '2015-11-08', '1');
INSERT INTO `domain` VALUES ('sothinkmedia.com', 'virtualDomain for sothinkmedia.com', 'A0/B0', '100', '100', '214748364800S', '107374182400S', '', '0', '1048576000S', '1048576000S', '3y', '0', '0', '0', '0', '1', '0', '2008-10-04 15:10:04', '2015-11-08', '1');
INSERT INTO `domain` VALUES ('sothinkmedia.de', 'virtualDomain for sothinkmedia.de', 'A0/B0', '100', '100', '214748364800S', '107374182400S', '', '0', '1048576000S', '1048576000S', '3y', '0', '0', '0', '0', '1', '0', '2008-10-04 15:10:04', '2015-11-08', '1');
INSERT INTO `domain` VALUES ('srctec.com', 'virtualDomain for srctec.com', 'A0/B0', '100', '100', '214748364800S', '107374182400S', '', '0', '1048576000S', '1048576000S', '3y', '0', '0', '0', '0', '1', '0', '2008-10-04 15:10:04', '2015-11-08', '1');
INSERT INTO `domain` VALUES ('swf-decompiler.com', 'virtualDomain for swf-decompiler.com', 'A0/B0', '100', '100', '214748364800S', '107374182400S', '', '0', '1048576000S', '1048576000S', '3y', '0', '0', '0', '0', '1', '0', '2008-10-04 15:10:04', '2015-11-08', '1');
INSERT INTO `domain` VALUES ('swf-to-fla.com', 'virtualDomain for swf-to-fla.com', 'A0/B0', '100', '100', '214748364800S', '107374182400S', '', '0', '1048576000S', '1048576000S', '3y', '0', '0', '0', '0', '1', '0', '2008-10-04 15:10:04', '2015-11-08', '1');
INSERT INTO `domain` VALUES ('tutu-mobile.com', 'virtualDomain for tutu-mobile.com', 'A0/B0', '100', '100', '214748364800S', '107374182400S', '', '0', '1048576000S', '1048576000S', '3y', '0', '0', '0', '0', '1', '0', '2008-10-04 15:10:04', '2015-11-08', '1');
