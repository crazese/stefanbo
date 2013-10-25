/*
Navicat MySQL Data Transfer

Source Server         : 192.168.1.5
Source Server Version : 50167
Source Host           : 192.168.1.5:3306
Source Database       : hero_xsyd

Target Server Type    : MYSQL
Target Server Version : 50167
File Encoding         : 65001

Date: 2013-07-02 15:07:22
*/

SET FOREIGN_KEY_CHECKS=0;

CREATE TABLE IF NOT EXISTS `ol_easoupay_ord` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`playersid`  int(11) NOT NULL ,
`ord_no`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '订单号对应回调的out_trade_no' ,
`ord_amt`  decimal(11,3) NOT NULL COMMENT '支付金额 元(用户选择的金额)' ,
`cashier_code`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '支付方式 ' ,
`trade_no`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '支付宝内部交易号' ,
`buyer_email`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '买家帐号' ,
`gmt_create`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '交易创建时间' ,
`notify_time`  int(11) NULL DEFAULT NULL COMMENT '回调时间(时间戳)' ,
`seller_id`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '卖家ID' ,
`total_fee`  decimal(11,1) NULL DEFAULT NULL COMMENT '支付金额 元' ,
`gmt_payment`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '付款时间，如未付款无此属性' ,
`notify_id`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '支付宝异步通知ID号,同一笔订单不会变' ,
`use_coupon`  varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`verify`  tinyint(2) NOT NULL DEFAULT 0 COMMENT '单订验证 0创建订单 1支付成功 2为支付失败 3等待支付' ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
ROW_FORMAT=Compact
;


-- ----------------------------
-- Table structure for `ol_sg_payinfo`
-- ----------------------------
CREATE TABLE  IF NOT EXISTS  `ol_sg_payinfo` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `orderId` varchar(64) NOT NULL,
  `payname` varchar(64) NOT NULL,
  `paydesc` varchar(128) NOT NULL,
  `itemCode` varchar(32) NOT NULL,
  `money` varchar(12) NOT NULL,
  `currency` varchar(6) NOT NULL,
  `isStage` tinyint(1) NOT NULL,
  `payStatus` varchar(6) NOT NULL,
  `userid` int(11) NOT NULL,
  `gameid` varchar(20) NOT NULL,
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ol_sg_payinfo
-- ----------------------------

-- ----------------------------
-- Table structure for `ol_sg_payinfo_error`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_sg_payinfo_error` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `orderId` varchar(64) NOT NULL,
  `payname` varchar(64) NOT NULL,
  `paydesc` varchar(128) NOT NULL,
  `itemCode` varchar(32) NOT NULL,
  `money` varchar(12) NOT NULL,
  `currency` varchar(6) NOT NULL,
  `isStage` tinyint(1) NOT NULL,
  `payStatus` varchar(6) NOT NULL,
  `userid` int(11) NOT NULL,
  `gameid` varchar(20) NOT NULL,
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS  `ol_weibo_payinfo` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `return_url` varchar(64) NOT NULL,
  `order_id` varchar(32) NOT NULL,
  `order_uid` varchar(128) NOT NULL,
  `wbdesc` varchar(32) NOT NULL,
  `appkey` varchar(128) NOT NULL,
  `amount` int(4) NOT NULL,
  `version` char(4) NOT NULL,
  `token` varchar(255) NOT NULL,
  `playersid` bigint(12) NOT NULL,
  `pay_status` tinyint(1) NOT NULL DEFAULT '0',
  `errorCode` int(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`intID`),
  UNIQUE KEY `order_id` (`order_id`),
  KEY `playersid` (`playersid`),
  KEY `order_uid` (`order_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 此表的自增号需要从正式服取
CREATE TABLE IF NOT EXISTS  `ol_wborderid` (
  `intID` bigint(12) NOT NULL AUTO_INCREMENT,
  `ortime` int(11) NOT NULL,
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB AUTO_INCREMENT=993990856 DEFAULT CHARSET=utf8;
ALTER TABLE `ol_wborderid` ADD COLUMN `playersid`  int(11) NOT NULL AFTER `ortime`;
ALTER TABLE `ol_wborderid` ADD COLUMN `sinauid`  varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `playersid`;
CREATE INDEX `index_wborder_playersid` USING BTREE ON `ol_wborderid`(`playersid`) ;

-- ----------------------------
-- Table structure for `ol_91dj_payinfo`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_91dj_payinfo` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `App_Id` char(32) NOT NULL,
  `Uin` char(32) NOT NULL,
  `Urecharge_Id` char(64) NOT NULL,
  `Extra` char(64) NOT NULL,
  `Recharge_Money` decimal(8,3) NOT NULL,
  `Recharge_Gold_Count` int(4) NOT NULL,
  `Pay_Status` tinyint(1) NOT NULL,
  `Create_Time` int(11) NOT NULL,
  `Sign` char(64) NOT NULL,
  PRIMARY KEY (`intID`),
  KEY `Urecharge_Id` (`Urecharge_Id`),
  KEY `Pay_Status` (`Pay_Status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `ol_91game_payinfo`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_91game_payinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `AppId` int(11) NOT NULL,
  `Act` tinyint(1) NOT NULL,
  `ProductName` varchar(100) NOT NULL,
  `ConsumeStreamId` varchar(50) NOT NULL,
  `CooOrderSerial` varchar(50) NOT NULL,
  `Uin` varchar(50) NOT NULL,
  `GoodsId` varchar(50) NOT NULL,
  `GoodsInfo` varchar(50) NOT NULL,
  `GoodsCount` int(6) NOT NULL,
  `OriginalMoney` decimal(8,2) NOT NULL,
  `OrderMoney` decimal(8,2) NOT NULL,
  `Note` varchar(255) NOT NULL,
  `PayStatus` tinyint(1) NOT NULL,
  `CreateTime` datetime NOT NULL,
  `Sign` varchar(64) NOT NULL,
  `sysTime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `ol_91game_payinfo_error`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_91game_payinfo_error` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `AppId` int(11) NOT NULL,
  `Act` tinyint(1) NOT NULL,
  `ProductName` varchar(100) NOT NULL,
  `ConsumeStreamId` varchar(50) NOT NULL,
  `CooOrderSerial` varchar(50) NOT NULL,
  `Uin` varchar(50) NOT NULL,
  `GoodsId` varchar(50) NOT NULL,
  `GoodsInfo` varchar(50) NOT NULL,
  `GoodsCount` int(6) NOT NULL,
  `OriginalMoney` decimal(8,2) NOT NULL,
  `OrderMoney` decimal(8,2) NOT NULL,
  `Note` varchar(255) NOT NULL,
  `PayStatus` tinyint(1) NOT NULL,
  `CreateTime` datetime NOT NULL,
  `Sign` varchar(64) NOT NULL,
  `sysTime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `ol_91h5_payinfo`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_91h5_payinfo` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `AccountID` char(64) NOT NULL,
  `GameServerID` int(10) NOT NULL,
  `Timestamp` char(16) NOT NULL,
  `OrderSerial` char(64) NOT NULL,
  `Amount` decimal(8,3) NOT NULL,
  `Point` int(5) NOT NULL,
  `Sign` char(64) NOT NULL,
  PRIMARY KEY (`intID`),
  UNIQUE KEY `OrderSerial` (`OrderSerial`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `ol_az_payinfo`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_az_payinfo` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `appKey` varchar(200) NOT NULL,
  `amount` decimal(8,3) NOT NULL,
  `orderId` varchar(64) NOT NULL,
  `payResult` char(5) NOT NULL,
  `ext` varchar(255) NOT NULL,
  `payType` char(10) NOT NULL,
  `signStr` varchar(255) NOT NULL,
  `msg` varchar(100) NOT NULL,
  `payTime` int(11) NOT NULL,
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `ol_az_payinfo_error`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_az_payinfo_error` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `appKey` varchar(200) NOT NULL,
  `amount` decimal(8,3) NOT NULL,
  `orderId` varchar(64) NOT NULL,
  `payResult` char(5) NOT NULL,
  `ext` varchar(255) NOT NULL,
  `payType` char(10) NOT NULL,
  `signStr` varchar(255) NOT NULL,
  `msg` varchar(100) NOT NULL,
  `payTime` int(11) NOT NULL,
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `ol_br_payinfo`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_br_payinfo` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `result` tinyint(1) NOT NULL COMMENT '充值是否成功',
  `order_id` char(50) NOT NULL COMMENT '订单号',
  `amount` float NOT NULL COMMENT '应付款金额',
  `pay_amount` float NOT NULL COMMENT '实际付款金额',
  `game_id` char(32) NOT NULL COMMENT '游戏ID',
  `msg` char(64) NOT NULL COMMENT '卡状态',
  `card_code1` varchar(128) NOT NULL COMMENT '订单失败信息',
  `verifystr` text NOT NULL COMMENT '加密串',
  `userid` bigint(18) NOT NULL,
  `systime` int(11) NOT NULL,
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `ol_br_payinfo_error`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_br_payinfo_error` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `result` tinyint(1) NOT NULL COMMENT '充值是否成功',
  `order_id` char(50) NOT NULL COMMENT '订单号',
  `amount` float NOT NULL COMMENT '应付款金额',
  `pay_amount` float NOT NULL COMMENT '实际付款金额',
  `game_id` char(32) NOT NULL COMMENT '游戏ID',
  `msg` char(64) NOT NULL COMMENT '卡状态',
  `card_code1` varchar(128) NOT NULL COMMENT '订单失败信息',
  `verifystr` text NOT NULL COMMENT '加密串',
  `userid` bigint(18) NOT NULL,
  `systime` int(11) NOT NULL,
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `ol_br1_payinfo`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_br1_payinfo` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `cid` varchar(64) NOT NULL,
  `uid` varchar(64) NOT NULL,
  `order_id` varchar(64) NOT NULL,
  `amount` float(6,0) NOT NULL,
  `verifystring` varchar(64) NOT NULL,
  `payTime` int(11) NOT NULL,
  `userid` bigint(12) NOT NULL,
  `serverid` varchar(50) NOT NULL,
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `ol_br1_payinfo_error`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_br1_payinfo_error` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `cid` varchar(64) NOT NULL,
  `uid` varchar(64) NOT NULL,
  `order_id` varchar(64) NOT NULL,
  `amount` float(6,0) NOT NULL,
  `verifystring` varchar(64) NOT NULL,
  `payTime` int(11) NOT NULL,
  `userid` bigint(12) NOT NULL,
  `serverid` varchar(50) NOT NULL,
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `ol_dena_payinfo`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_dena_payinfo` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `orderId` varchar(128) NOT NULL,
  `orderTime` int(11) NOT NULL,
  `userid` bigint(11) NOT NULL,
  `amount` int(4) NOT NULL,
  PRIMARY KEY (`intID`),
  KEY `orderId` (`orderId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `ol_dkpayinfo`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_dkpayinfo` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `amount` decimal(8,3) NOT NULL,
  `cardtype` char(5) NOT NULL,
  `orderid` char(64) NOT NULL,
  `result` tinyint(1) NOT NULL,
  `timetamp` int(11) NOT NULL,
  `aid` char(50) NOT NULL,
  `client_secret` char(64) NOT NULL,
  PRIMARY KEY (`intID`),
  UNIQUE KEY `orderid` (`orderid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `ol_dkpayinfo_error`
-- ----------------------------;
CREATE TABLE IF NOT EXISTS  `ol_dkpayinfo_error` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `amount` decimal(8,3) NOT NULL,
  `cardtype` char(5) NOT NULL,
  `orderid` char(64) NOT NULL,
  `result` tinyint(1) NOT NULL,
  `timetamp` int(11) NOT NULL,
  `aid` char(50) NOT NULL,
  `client_secret` char(64) NOT NULL,
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `ol_dl_payinfo`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_dl_payinfo` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `result` tinyint(1) NOT NULL,
  `money` decimal(8,3) NOT NULL,
  `order` char(64) NOT NULL,
  `mid` char(32) NOT NULL,
  `time` char(18) NOT NULL,
  `signature` char(64) NOT NULL,
  `ext` char(60) NOT NULL,
  PRIMARY KEY (`intID`),
  KEY `order` (`order`),
  KEY `result` (`result`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `ol_dx_payinfo`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_dx_payinfo` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `sysID` int(11) NOT NULL,
  `LinkID` char(128) NOT NULL,
  `mobile` char(64) NOT NULL,
  `cmd` char(20) NOT NULL,
  `status` char(20) NOT NULL,
  `orderTime` int(11) NOT NULL,
  `spno` char(128) NOT NULL,
  `playersid` bigint(11) NOT NULL,
  PRIMARY KEY (`intID`),
  UNIQUE KEY `LinkID` (`LinkID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `ol_dx_visitinfo`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_dx_visitinfo` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `orderId` char(20) NOT NULL,
  `msg` varchar(255) NOT NULL,
  `ms` char(32) NOT NULL,
  `playersid` bigint(11) NOT NULL,
  `fwqdm` varchar(20) NOT NULL,
  `orderTime` int(11) NOT NULL,
  PRIMARY KEY (`intID`),
  KEY `orderId` (`orderId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `ol_ifengpayinfo`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_ifengpayinfo` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `game_id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `server_id` int(5) NOT NULL,
  `bill_no` char(100) NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `user_id` bigint(12) NOT NULL,
  `trade_status` char(50) NOT NULL,
  `partner_bill_no` char(64) NOT NULL,
  `extra` char(100) NOT NULL,
  `sign` char(255) NOT NULL,
  `payTime` int(11) NOT NULL,
  PRIMARY KEY (`intID`),
  UNIQUE KEY `bill_no` (`bill_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `ol_ifengpayinfo_error`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_ifengpayinfo_error` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `game_id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `server_id` int(5) NOT NULL,
  `bill_no` char(100) NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `user_id` bigint(12) NOT NULL,
  `trade_status` char(50) NOT NULL,
  `partner_bill_no` char(64) NOT NULL,
  `extra` char(100) NOT NULL,
  `sign` char(255) NOT NULL,
  `payTime` int(11) NOT NULL,
  PRIMARY KEY (`intID`),
  UNIQUE KEY `bill_no` (`bill_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `ol_jf_payinfo`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_jf_payinfo` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `sign` char(64) NOT NULL,
  `time` char(12) NOT NULL,
  `cost` int(11) NOT NULL,
  `appkey` char(64) NOT NULL,
  `create_time` char(12) NOT NULL,
  `order_id` char(64) NOT NULL,
  `userid` bigint(12) NOT NULL,
  `gameid` char(30) NOT NULL,
  `orderStatus` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`intID`),
  UNIQUE KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table structure for `ol_jf1_payinfo`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_jf1_payinfo` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `sign` char(64) NOT NULL,
  `time` char(12) NOT NULL,
  `cost` int(11) NOT NULL,
  `appkey` char(64) NOT NULL,
  `create_time` char(12) NOT NULL,
  `order_id` char(64) NOT NULL,
  `userid` bigint(12) NOT NULL,
  `gameid` char(30) NOT NULL,
  `orderStatus` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`intID`),
  UNIQUE KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `ol_korea_payinfo`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_korea_payinfo` (
  `txid` char(25) NOT NULL,
  `userid` bigint(12) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `detail` char(200) NOT NULL,
  `message` char(200) NOT NULL,
  `count` int(3) NOT NULL,
  `log_time` char(60) NOT NULL,
  `appid` char(60) NOT NULL,
  `product_id` char(32) NOT NULL,
  `charge_amount` decimal(8,3) NOT NULL,
  `tid` char(125) NOT NULL,
  `detail_pname` char(150) NOT NULL,
  `bp_info` char(200) NOT NULL,
  PRIMARY KEY (`txid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `ol_korea_payinfo_err`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_korea_payinfo_err` (
  `txid` char(25) NOT NULL,
  `userid` bigint(12) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `message` char(200) NOT NULL,
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `orderTime` int(11) NOT NULL,
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `ol_kugoupayinfo`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_kugoupayinfo` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `orderid` char(32) NOT NULL,
  `outorderid` char(64) NOT NULL,
  `amount` decimal(8,3) NOT NULL,
  `username` char(50) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `time` int(11) NOT NULL,
  `ext1` char(100) NOT NULL,
  `ext2` char(100) NOT NULL,
  `sign` char(64) NOT NULL,
  PRIMARY KEY (`intID`),
  UNIQUE KEY `orderid` (`orderid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `ol_kugoupayinfo_error`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_kugoupayinfo_error` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `orderid` char(32) NOT NULL,
  `outorderid` char(64) NOT NULL,
  `amount` decimal(8,3) NOT NULL,
  `username` char(50) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `time` int(11) NOT NULL,
  `ext1` char(100) NOT NULL,
  `ext2` char(100) NOT NULL,
  `sign` char(64) NOT NULL,
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `ol_ld_payinfo`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_ld_payinfo` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(50) NOT NULL,
  `amount` float(5,0) NOT NULL,
  `actualAmount` float(5,0) NOT NULL,
  `extraInfo` varchar(50) NOT NULL,
  `success` tinyint(1) NOT NULL,
  `msg` varchar(50) NOT NULL,
  `sign` varchar(100) NOT NULL,
  `orderId` varchar(50) NOT NULL,
  PRIMARY KEY (`intID`),
  KEY `orderId` (`orderId`),
  KEY `success` (`success`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ol_ld_payinfo
-- ----------------------------

-- ----------------------------
-- Table structure for `ol_ld_payinfo_error`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_ld_payinfo_error` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(50) NOT NULL,
  `amount` float(5,0) NOT NULL,
  `actualAmount` float(5,0) NOT NULL,
  `extraInfo` varchar(50) NOT NULL,
  `success` tinyint(1) NOT NULL,
  `msg` varchar(50) NOT NULL,
  `sign` varchar(100) NOT NULL,
  `orderId` varchar(50) NOT NULL,
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- ----------------------------
-- Table structure for `ol_lenovpayinfo`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_lenovpayinfo` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `transid` char(36) NOT NULL,
  `transdata` char(255) NOT NULL,
  `sign` char(255) NOT NULL,
  `payTime` int(11) NOT NULL,
  PRIMARY KEY (`intID`),
  UNIQUE KEY `transid` (`transid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `ol_lenovpayinfo_error`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_lenovpayinfo_error` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `transid` char(36) NOT NULL,
  `transdata` char(255) NOT NULL,
  `sign` char(255) NOT NULL,
  `payTime` int(11) NOT NULL,
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `ol_ltsjpayinfo`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_ltsjpayinfo` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `result` tinyint(1) NOT NULL,
  `paymentid` char(100) NOT NULL,
  `errorstr` char(255) NOT NULL,
  `company` char(25) NOT NULL,
  `channelid` char(5) NOT NULL,
  `softgood` char(25) NOT NULL,
  `customer` char(50) NOT NULL,
  `money` int(3) NOT NULL,
  `softserver` char(30) NOT NULL,
  `playername` char(30) NOT NULL,
  `date` datetime NOT NULL,
  `pkey` char(100) NOT NULL,
  `paytype` char(5) NOT NULL,
  PRIMARY KEY (`intID`),
  UNIQUE KEY `paymentid` (`paymentid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- ----------------------------
-- Table structure for `ol_ltsjpayinfo_error`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_ltsjpayinfo_error` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `result` tinyint(1) NOT NULL,
  `paymentid` char(100) NOT NULL,
  `errorstr` char(255) NOT NULL,
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `ol_mi_payinfo`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_mi_payinfo` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `appId` varchar(100) NOT NULL,
  `cpOrderId` varchar(255) NOT NULL,
  `cpUserInfo` varchar(100) NOT NULL,
  `uid` varchar(16) NOT NULL,
  `orderId` varchar(255) NOT NULL,
  `orderStatus` varchar(30) NOT NULL,
  `payFee` varchar(11) NOT NULL,
  `productCode` varchar(30) NOT NULL,
  `productName` varchar(50) NOT NULL,
  `productCount` int(11) NOT NULL,
  `payTime` datetime NOT NULL,
  `signature` varchar(255) NOT NULL,
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `ol_mi_payinfo_error`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_mi_payinfo_error` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `appId` varchar(100) NOT NULL,
  `cpOrderId` varchar(255) NOT NULL,
  `cpUserInfo` varchar(100) NOT NULL,
  `uid` varchar(16) NOT NULL,
  `orderId` varchar(255) NOT NULL,
  `orderStatus` varchar(30) NOT NULL,
  `payFee` varchar(11) NOT NULL,
  `productCode` varchar(30) NOT NULL,
  `productName` varchar(50) NOT NULL,
  `productCount` int(11) NOT NULL,
  `payTime` datetime NOT NULL,
  `signature` varchar(255) NOT NULL,
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `ol_tom_payinfo`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_tom_payinfo` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `returnCode` char(32) NOT NULL,
  `rechargeId` char(32) NOT NULL,
  `rechargeMoney` char(32) NOT NULL,
  `rechargeType` char(32) NOT NULL,
  `userId` bigint(12) NOT NULL,
  `orderId` varchar(64) NOT NULL,
  `orderStatus` tinyint(1) NOT NULL,
  `payId` char(32) NOT NULL,
  `payType` tinyint(1) NOT NULL,
  `payMoney` char(8) NOT NULL,
  `time` char(32) NOT NULL,
  `Balance` char(32) NOT NULL,
  `ssoId` varchar(32) NOT NULL,
  `versionId` char(32) NOT NULL,
  `payKey` char(32) NOT NULL,
  `TomUserId` char(32) NOT NULL,
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `ol_tom_payinfo_error`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_tom_payinfo_error` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `returnCode` char(32) NOT NULL,
  `rechargeId` char(32) NOT NULL,
  `rechargeMoney` char(32) NOT NULL,
  `rechargeType` char(2) NOT NULL,
  `userId` bigint(12) NOT NULL DEFAULT '0',
  `orderId` varchar(64) NOT NULL,
  `orderStatus` tinyint(1) NOT NULL,
  `payId` char(12) NOT NULL,
  `payType` tinyint(1) NOT NULL,
  `payMoney` char(32) NOT NULL,
  `time` char(32) NOT NULL,
  `Balance` char(32) NOT NULL,
  `ssoId` varchar(32) NOT NULL,
  `versionId` char(32) NOT NULL,
  `payKey` char(32) NOT NULL,
  `TomUserId` char(32) NOT NULL,
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `ol_uc_payinfo`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_uc_payinfo` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `orderId` varchar(50) NOT NULL,
  `gameId` int(11) NOT NULL,
  `serverId` int(11) NOT NULL,
  `ucid` varchar(50) NOT NULL,
  `payWay` int(3) NOT NULL,
  `amount` double NOT NULL,
  `callbackInfo` varchar(50) NOT NULL,
  `orderStatus` char(1) NOT NULL,
  `failedDesc` varchar(100) NOT NULL,
  `processed` tinyint(1) NOT NULL DEFAULT '0',
  `createTime` int(11) NOT NULL,
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- ----------------------------
-- Table structure for `ol_uc_payinfo_error`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_uc_payinfo_error` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `orderId` varchar(50) NOT NULL,
  `gameId` int(11) NOT NULL,
  `serverId` int(11) NOT NULL,
  `ucid` int(11) NOT NULL,
  `payWay` int(3) NOT NULL,
  `amount` double NOT NULL,
  `callbackInfo` varchar(50) NOT NULL,
  `orderStatus` char(1) NOT NULL,
  `failedDesc` varchar(100) NOT NULL,
  `createTime` int(11) NOT NULL,
  `toserver` tinyint(3) NOT NULL,
  `processed` tinyint(1) NOT NULL,
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `ol_yxbar_payinfo`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_yxbar_payinfo` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `usercode` varchar(64) NOT NULL,
  `tradnum` varchar(64) NOT NULL,
  `account` varchar(64) NOT NULL,
  `servername` varchar(64) NOT NULL,
  `amt` int(10) NOT NULL,
  `checking` varchar(64) NOT NULL,
  `time` int(10) NOT NULL,
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ol_yxbar_payinfo
-- ----------------------------

-- ----------------------------
-- Table structure for `ol_yxbar_payinfo_error`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_yxbar_payinfo_error` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `usercode` varchar(64) NOT NULL,
  `tradnum` varchar(64) NOT NULL,
  `account` varchar(64) NOT NULL,
  `servername` varchar(64) NOT NULL,
  `amt` int(10) NOT NULL,
  `checking` varchar(64) NOT NULL,
  `time` int(10) NOT NULL,
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `ol_yxh_payinfo`
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `ol_yxh_payinfo` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL,
  `change_id` char(10) NOT NULL,
  `money` decimal(8,3) NOT NULL,
  `hash` char(128) NOT NULL,
  `ad_key` char(32) NOT NULL,
  `object` char(64) NOT NULL,
  `orderTime` int(11) NOT NULL,
  PRIMARY KEY (`intID`),
  UNIQUE KEY `change_id` (`change_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS  `ol_wdjpayinfo` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `signType` char(50) NOT NULL,
  `sign` char(100) NOT NULL,
  `payTime` int(11) NOT NULL,
  `orderid` char(50) NOT NULL,
  PRIMARY KEY (`intID`),
  UNIQUE KEY `orderid` (`orderid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS  `ol_wdjpayinfo_error` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `signType` char(50) NOT NULL,
  `sign` char(100) NOT NULL,
  `payTime` int(11) NOT NULL,
  `orderid` char(50) NOT NULL,
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;