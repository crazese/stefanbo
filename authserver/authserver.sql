/*
Navicat MySQL Data Transfer

Source Server         : 192.168.1.5
Source Server Version : 50161
Source Host           : 192.168.1.5:3306
Source Database       : authserver

Target Server Type    : MYSQL
Target Server Version : 50161
File Encoding         : 65001

Date: 2012-03-16 14:21:04
*/

SET FOREIGN_KEY_CHECKS=0;

CREATE DATABASE `authserver` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;

use authserver;

-- ----------------------------
-- Table structure for `ol_serverlist`
-- ----------------------------
DROP TABLE IF EXISTS `ol_serverlist`;
CREATE TABLE `ol_serverlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `serverName` varchar(200) NOT NULL,
  `serverIp` varchar(200) NOT NULL,
  `status` varchar(200) NOT NULL,
  `recommend` int(2) NOT NULL,
  `openTime` varchar(100) NOT NULL,
  `serverOnlineNum` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ol_serverlist
-- ----------------------------

-- ----------------------------
-- Table structure for `ol_users`
-- ----------------------------
DROP TABLE IF EXISTS `ol_users`;
CREATE TABLE `ol_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(2000) NOT NULL,
  `userName` varchar(200) NOT NULL,
  `pwd` varchar(200) NOT NULL,
  `email` varchar(200) DEFAULT NULL,
  `lastLoging` varchar(200) NOT NULL COMMENT '最后一次登入时间(时间戳)',
  `enterServers` text COMMENT '用户登入过的服务器(serverList中的id)',
  `verifyCallBack` varchar(2000) DEFAULT NULL COMMENT '游戏服务器回调验证值',
  PRIMARY KEY (`id`),
  UNIQUE KEY `userName` (`userName`) USING HASH
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ol_users
-- ----------------------------

ALTER TABLE `ol_users` ADD COLUMN `origin`  varchar(2000) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 0 AFTER `verifyCallBack`;

ALTER TABLE `ol_users` ADD COLUMN `verify`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `origin`;

ALTER TABLE `ol_users` ADD COLUMN `verify_date`  varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `verify`;

ALTER TABLE `ol_serverlist` ADD COLUMN `origin`  varchar(2000) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 0 AFTER `serverOnlineNum`;

ALTER TABLE `ol_serverlist` ADD COLUMN `version`  varchar(10) NULL DEFAULT '0' AFTER `origin`;
UPDATE ol_serverlist SET version = 0;
ALTER TABLE `ol_users` ADD COLUMN `userName2`  varchar(200) NOT NULL DEFAULT 0 AFTER `verify_date`;
ALTER TABLE `ol_users` ADD COLUMN `sex`  tinyint(1) NOT NULL DEFAULT 0 AFTER `userName2`;