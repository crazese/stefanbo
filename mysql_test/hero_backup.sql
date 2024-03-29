/*
MySQL Backup
Source Server Version: 5.1.70
Source Database: hero
Date: 2013/11/5 11:34:20
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
--  Table structure for `admin_logs`
-- ----------------------------
DROP TABLE IF EXISTS `admin_logs`;
CREATE TABLE `admin_logs` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `playersid` int(11) NOT NULL,
  `nickname` varchar(50) NOT NULL,
  `logs` text NOT NULL,
  `ly` varchar(10) NOT NULL,
  `wData` int(11) NOT NULL,
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_91dj_payinfo`
-- ----------------------------
DROP TABLE IF EXISTS `ol_91dj_payinfo`;
CREATE TABLE `ol_91dj_payinfo` (
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
  KEY `Urecharge_Id` (`Urecharge_Id`) USING BTREE,
  KEY `Pay_Status` (`Pay_Status`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_91game_payinfo`
-- ----------------------------
DROP TABLE IF EXISTS `ol_91game_payinfo`;
CREATE TABLE `ol_91game_payinfo` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_91game_payinfo_error`
-- ----------------------------
DROP TABLE IF EXISTS `ol_91game_payinfo_error`;
CREATE TABLE `ol_91game_payinfo_error` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_91h5_payinfo`
-- ----------------------------
DROP TABLE IF EXISTS `ol_91h5_payinfo`;
CREATE TABLE `ol_91h5_payinfo` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `AccountID` char(64) NOT NULL,
  `GameServerID` int(10) NOT NULL,
  `Timestamp` char(16) NOT NULL,
  `OrderSerial` char(64) NOT NULL,
  `Amount` decimal(8,3) NOT NULL,
  `Point` int(5) NOT NULL,
  `Sign` char(64) NOT NULL,
  PRIMARY KEY (`intID`),
  UNIQUE KEY `OrderSerial` (`OrderSerial`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_accepted_quests`
-- ----------------------------
DROP TABLE IF EXISTS `ol_accepted_quests`;
CREATE TABLE `ol_accepted_quests` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `QuestID` int(4) NOT NULL,
  `playersid` int(11) NOT NULL,
  `Qstatus` tinyint(1) NOT NULL COMMENT '0：未完成 1：已完成但未领取奖励',
  `Progress` tinyint(1) NOT NULL DEFAULT '0',
  `AcceptTime` int(11) NOT NULL,
  `ExtraData` varchar(255) NOT NULL,
  `readStatus` tinyint(1) NOT NULL DEFAULT '0' COMMENT '务任是否被阅读',
  `published` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示任务',
  `RepeatInterval` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否日常任务',
  `mblx` int(2) NOT NULL DEFAULT '0' COMMENT '目标类型',
  PRIMARY KEY (`intID`),
  KEY `QuestID_quests` (`QuestID`) USING BTREE,
  KEY `playersid_quests` (`playersid`,`RepeatInterval`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_activatecode`
-- ----------------------------
DROP TABLE IF EXISTS `ol_activatecode`;
CREATE TABLE `ol_activatecode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `activateCode` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `activateCode` (`activateCode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_aggressor_message`
-- ----------------------------
DROP TABLE IF EXISTS `ol_aggressor_message`;
CREATE TABLE `ol_aggressor_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `playersid` int(11) NOT NULL DEFAULT '0' COMMENT '被占领方角色id',
  `aggressor_playersid` int(11) NOT NULL DEFAULT '0' COMMENT '占领方角色id',
  `aggressor_message` varchar(50) NOT NULL DEFAULT '' COMMENT '占领消息',
  `type` int(2) NOT NULL DEFAULT '1' COMMENT '类型 1 系统提供 2 自己创作',
  `create_time` int(11) NOT NULL COMMENT '时间',
  PRIMARY KEY (`id`),
  KEY `pid` (`playersid`) USING BTREE,
  KEY `apid` (`aggressor_playersid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_alipay_ord`
-- ----------------------------
DROP TABLE IF EXISTS `ol_alipay_ord`;
CREATE TABLE `ol_alipay_ord` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `playersid` int(11) NOT NULL,
  `ord_no` varchar(100) NOT NULL COMMENT '订单号对应回调的out_trade_no',
  `ord_amt` decimal(11,3) NOT NULL COMMENT '支付金额 元(用户选择的金额)',
  `cashier_code` varchar(50) NOT NULL COMMENT '支付方式 空支付宝账户 其他分银行卡和信用卡',
  `trade_no` varchar(20) DEFAULT NULL COMMENT '支付宝内部交易号',
  `buyer_email` varchar(100) DEFAULT NULL COMMENT '买家帐号',
  `gmt_create` varchar(50) DEFAULT NULL COMMENT '交易创建时间',
  `notify_time` int(11) DEFAULT NULL COMMENT '回调时间(时间戳)',
  `seller_id` varchar(50) DEFAULT NULL COMMENT '卖家ID',
  `total_fee` decimal(11,1) DEFAULT NULL COMMENT '支付金额 元',
  `gmt_payment` varchar(50) DEFAULT NULL COMMENT '付款时间，如未付款无此属性',
  `notify_id` varchar(50) DEFAULT NULL COMMENT '支付宝异步通知ID号,同一笔订单不会变',
  `use_coupon` varchar(5) DEFAULT NULL,
  `verify` tinyint(2) NOT NULL DEFAULT '0' COMMENT '单订验证 0创建订单 1支付成功 2为支付失败 3等待支付',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_app_payinfo`
-- ----------------------------
DROP TABLE IF EXISTS `ol_app_payinfo`;
CREATE TABLE `ol_app_payinfo` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `quantity` int(6) NOT NULL,
  `product_id` varchar(50) NOT NULL,
  `original_purchase_date_pst` varchar(50) NOT NULL,
  `original_transaction_id` varchar(50) NOT NULL,
  `original_purchase_date_ms` varchar(50) NOT NULL,
  `transaction_id` varchar(50) NOT NULL,
  `bvrs` varchar(50) NOT NULL,
  `purchase_date_ms` varchar(50) NOT NULL,
  `purchase_date` varchar(50) NOT NULL,
  `original_purchase_date` varchar(50) NOT NULL,
  `purchase_date_pst` varchar(50) NOT NULL,
  `bid` varchar(50) NOT NULL,
  `item_id` varchar(50) NOT NULL,
  `app_item_id` varchar(50) NOT NULL,
  `version_external_identifier` varchar(50) NOT NULL,
  `product_name` varchar(50) NOT NULL,
  `price` int(4) NOT NULL,
  `sfgq` tinyint(1) NOT NULL,
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_app_payinfo_error`
-- ----------------------------
DROP TABLE IF EXISTS `ol_app_payinfo_error`;
CREATE TABLE `ol_app_payinfo_error` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `errorno` int(5) NOT NULL,
  `errormsg` varchar(50) NOT NULL,
  `errorDate` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_app_productInfo`
-- ----------------------------
DROP TABLE IF EXISTS `ol_app_productInfo`;
CREATE TABLE `ol_app_productInfo` (
  `productID` varchar(50) NOT NULL,
  `mc` varchar(50) NOT NULL COMMENT 'äº§å“åç§°',
  `iid` varchar(100) NOT NULL COMMENT 'å›¾æ ‡åœ°å€',
  `jg` int(4) NOT NULL COMMENT 'äº§å“ä»·æ ¼',
  `publish` tinyint(1) NOT NULL COMMENT 'æ˜¯å¦å‘å¸ƒ',
  `yb` int(6) NOT NULL COMMENT 'å…ƒå®æ•°é‡',
  `apple_ID` int(11) NOT NULL,
  `sfgq` tinyint(1) NOT NULL,
  PRIMARY KEY (`apple_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_az_payinfo`
-- ----------------------------
DROP TABLE IF EXISTS `ol_az_payinfo`;
CREATE TABLE `ol_az_payinfo` (
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
--  Table structure for `ol_az_payinfo_error`
-- ----------------------------
DROP TABLE IF EXISTS `ol_az_payinfo_error`;
CREATE TABLE `ol_az_payinfo_error` (
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
--  Table structure for `ol_bck_log`
-- ----------------------------
DROP TABLE IF EXISTS `ol_bck_log`;
CREATE TABLE `ol_bck_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `logValue` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_br_payinfo`
-- ----------------------------
DROP TABLE IF EXISTS `ol_br_payinfo`;
CREATE TABLE `ol_br_payinfo` (
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
--  Table structure for `ol_br_payinfo_error`
-- ----------------------------
DROP TABLE IF EXISTS `ol_br_payinfo_error`;
CREATE TABLE `ol_br_payinfo_error` (
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
--  Table structure for `ol_br1_payinfo`
-- ----------------------------
DROP TABLE IF EXISTS `ol_br1_payinfo`;
CREATE TABLE `ol_br1_payinfo` (
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
--  Table structure for `ol_br1_payinfo_error`
-- ----------------------------
DROP TABLE IF EXISTS `ol_br1_payinfo_error`;
CREATE TABLE `ol_br1_payinfo_error` (
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
--  Table structure for `ol_chat_blacklist`
-- ----------------------------
DROP TABLE IF EXISTS `ol_chat_blacklist`;
CREATE TABLE `ol_chat_blacklist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_pid` int(11) NOT NULL COMMENT '屏蔽发起人',
  `to_pid` text NOT NULL COMMENT '被屏蔽人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_chat_report`
-- ----------------------------
DROP TABLE IF EXISTS `ol_chat_report`;
CREATE TABLE `ol_chat_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fwqdm` varchar(50) NOT NULL,
  `bjb_pid` int(11) NOT NULL,
  `chat_type` tinyint(4) NOT NULL,
  `chat_content` varchar(100) NOT NULL,
  `jbr_pid` int(11) NOT NULL,
  `date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jbr_pid` (`jbr_pid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_city_msg`
-- ----------------------------
DROP TABLE IF EXISTS `ol_city_msg`;
CREATE TABLE `ol_city_msg` (
  `playersid` bigint(12) NOT NULL,
  `msg` text NOT NULL COMMENT '留言信息',
  `newmsg` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`playersid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_city_msg_blacklist`
-- ----------------------------
DROP TABLE IF EXISTS `ol_city_msg_blacklist`;
CREATE TABLE `ol_city_msg_blacklist` (
  `intID` bigint(18) NOT NULL AUTO_INCREMENT,
  `from_pid` bigint(18) NOT NULL COMMENT '屏蔽发起人',
  `to_pid` bigint(18) NOT NULL COMMENT '被屏蔽人',
  PRIMARY KEY (`intID`),
  KEY `from_pid` (`from_pid`) USING BTREE,
  KEY `to_pid` (`to_pid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_combin_rule`
-- ----------------------------
DROP TABLE IF EXISTS `ol_combin_rule`;
CREATE TABLE `ol_combin_rule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `itemId` int(11) NOT NULL,
  `combinId` int(11) NOT NULL,
  `needNum` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_dalei_2`
-- ----------------------------
DROP TABLE IF EXISTS `ol_dalei_2`;
CREATE TABLE `ol_dalei_2` (
  `playersid` int(11) NOT NULL,
  `pm` int(11) NOT NULL AUTO_INCREMENT COMMENT 'æŽ’å',
  `credits` int(11) NOT NULL DEFAULT '0' COMMENT 'ç§¯åˆ†',
  `winning` int(11) NOT NULL DEFAULT '0' COMMENT 'è¿žèƒœåœºæ¬¡',
  `dl_c` int(11) NOT NULL DEFAULT '20' COMMENT 'æ‰“æ“‚å‰©ä½™æ¬¡æ•°',
  `buy_c` int(11) NOT NULL DEFAULT '0' COMMENT 'è´­ä¹°æ¬¡æ•°',
  `last_buy_time` int(11) NOT NULL DEFAULT '0' COMMENT 'æœ€åŽè´­ä¹°æ‰“æ“‚æ¬¡æ•°æ—¶é—´',
  `last_dl_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'æœ€åŽæ‰“æ“‚æ—¶é—´',
  `last_cdt_time` int(11) NOT NULL DEFAULT '0' COMMENT 'æœ€åŽèŽ·å–ç§¯åˆ†æ—¶é—´',
  `g_ids` varchar(128) NOT NULL DEFAULT '' COMMENT 'ä¸Šæ¬¡ä¸»åŠ¨æ‰“æ“‚çš„å°†é¢†ID',
  `flush_times` int(11) NOT NULL DEFAULT '0' COMMENT 'è¯•ç‚¼åœºä¸»åŠ¨åˆ·æ–°æ¬¡æ•°',
  `last_flush_time` int(11) NOT NULL DEFAULT '0' COMMENT 'è¯•ç‚¼åœºæœ€åŽåˆ·æ–°æ—¶é—´',
  PRIMARY KEY (`pm`),
  UNIQUE KEY `index_ol_dalei_2_playersid` (`playersid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_dalei_jf`
-- ----------------------------
DROP TABLE IF EXISTS `ol_dalei_jf`;
CREATE TABLE `ol_dalei_jf` (
  `playersid` int(11) NOT NULL,
  `lxsl` int(4) NOT NULL DEFAULT '0' COMMENT '续连胜利次数',
  `jf` int(5) NOT NULL DEFAULT '0' COMMENT '积分',
  `pm` int(11) NOT NULL DEFAULT '0',
  `pmsjf` int(5) NOT NULL DEFAULT '0' COMMENT '排名时积分',
  `dlcs` int(4) NOT NULL DEFAULT '0' COMMENT '今日打擂次数',
  `yzjcs` tinyint(4) NOT NULL DEFAULT '0' COMMENT '已增加次数(指使用过多少次元宝)',
  `dhcs` int(4) NOT NULL DEFAULT '0' COMMENT '兑换次数',
  `zdlcs` int(11) NOT NULL DEFAULT '0' COMMENT '赛季总打擂次数',
  `lsh` int(11) NOT NULL DEFAULT '0' COMMENT '流水号',
  `pmsj` int(11) NOT NULL DEFAULT '0' COMMENT '排名时间',
  `lsh_m` int(11) NOT NULL DEFAULT '0',
  `g_ids` varchar(128) NOT NULL DEFAULT '' COMMENT '上次主动打擂的将领ID',
  `sw_v` int(11) NOT NULL DEFAULT '0' COMMENT '玩家可领取声望值',
  `xuantie_v` int(11) NOT NULL DEFAULT '0' COMMENT '玩家可领取的玄铁值',
  `yb_ref` int(11) NOT NULL DEFAULT '0' COMMENT '元宝刷新次数',
  PRIMARY KEY (`playersid`),
  KEY `jf_lshm_pmsj` (`jf`,`lsh_m`,`pmsj`) USING BTREE,
  KEY `pm` (`pm`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_dalei_jf_count`
-- ----------------------------
DROP TABLE IF EXISTS `ol_dalei_jf_count`;
CREATE TABLE `ol_dalei_jf_count` (
  `jf` int(11) NOT NULL,
  `num` int(11) DEFAULT '0' COMMENT '对应积分玩家数量',
  `lsh` int(11) DEFAULT '0',
  PRIMARY KEY (`jf`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_dalei_process_lock`
-- ----------------------------
DROP TABLE IF EXISTS `ol_dalei_process_lock`;
CREATE TABLE `ol_dalei_process_lock` (
  `p_date` date NOT NULL,
  `p_time` datetime NOT NULL,
  PRIMARY KEY (`p_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_dena_payinfo`
-- ----------------------------
DROP TABLE IF EXISTS `ol_dena_payinfo`;
CREATE TABLE `ol_dena_payinfo` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `orderId` varchar(128) NOT NULL,
  `orderTime` int(11) NOT NULL,
  `userid` bigint(11) NOT NULL,
  `amount` int(4) NOT NULL,
  PRIMARY KEY (`intID`),
  KEY `orderId` (`orderId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_dkpayinfo`
-- ----------------------------
DROP TABLE IF EXISTS `ol_dkpayinfo`;
CREATE TABLE `ol_dkpayinfo` (
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
--  Table structure for `ol_dkpayinfo_error`
-- ----------------------------
DROP TABLE IF EXISTS `ol_dkpayinfo_error`;
CREATE TABLE `ol_dkpayinfo_error` (
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
--  Table structure for `ol_dl_payinfo`
-- ----------------------------
DROP TABLE IF EXISTS `ol_dl_payinfo`;
CREATE TABLE `ol_dl_payinfo` (
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
--  Table structure for `ol_dwhd`
-- ----------------------------
DROP TABLE IF EXISTS `ol_dwhd`;
CREATE TABLE `ol_dwhd` (
  `playersid` int(11) NOT NULL,
  `createTime` date NOT NULL,
  PRIMARY KEY (`playersid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- ----------------------------
--  Table structure for `ol_dx_payinfo`
-- ----------------------------
DROP TABLE IF EXISTS `ol_dx_payinfo`;
CREATE TABLE `ol_dx_payinfo` (
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
  UNIQUE KEY `LinkID` (`LinkID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_dx_visitinfo`
-- ----------------------------
DROP TABLE IF EXISTS `ol_dx_visitinfo`;
CREATE TABLE `ol_dx_visitinfo` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `orderId` char(20) NOT NULL,
  `msg` varchar(255) NOT NULL,
  `ms` char(32) NOT NULL,
  `playersid` bigint(11) NOT NULL,
  `fwqdm` varchar(20) NOT NULL,
  `orderTime` int(11) NOT NULL,
  PRIMARY KEY (`intID`),
  KEY `orderId` (`orderId`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_easoupay_ord`
-- ----------------------------
DROP TABLE IF EXISTS `ol_easoupay_ord`;
CREATE TABLE `ol_easoupay_ord` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `playersid` int(11) NOT NULL,
  `ord_no` varchar(100) NOT NULL COMMENT '订单号对应回调的out_trade_no',
  `ord_amt` decimal(11,3) NOT NULL COMMENT '支付金额 元(用户选择的金额)',
  `cashier_code` varchar(50) NOT NULL COMMENT '支付方式 ',
  `trade_no` varchar(20) DEFAULT NULL COMMENT '支付宝内部交易号',
  `buyer_email` varchar(100) DEFAULT NULL COMMENT '买家帐号',
  `gmt_create` varchar(50) DEFAULT NULL COMMENT '交易创建时间',
  `notify_time` int(11) DEFAULT NULL COMMENT '回调时间(时间戳)',
  `seller_id` varchar(50) DEFAULT NULL COMMENT '卖家ID',
  `total_fee` decimal(11,1) DEFAULT NULL COMMENT '支付金额 元',
  `gmt_payment` varchar(50) DEFAULT NULL COMMENT '付款时间，如未付款无此属性',
  `notify_id` varchar(50) DEFAULT NULL COMMENT '支付宝异步通知ID号,同一笔订单不会变',
  `use_coupon` varchar(5) DEFAULT NULL,
  `verify` tinyint(2) NOT NULL DEFAULT '0' COMMENT '单订验证 0创建订单 1支付成功 2为支付失败 3等待支付',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_exchange`
-- ----------------------------
DROP TABLE IF EXISTS `ol_exchange`;
CREATE TABLE `ol_exchange` (
  `exid` int(11) NOT NULL AUTO_INCREMENT COMMENT '兑换id',
  `title` varchar(64) NOT NULL,
  `desc` varchar(255) NOT NULL,
  `exObj` varchar(64) NOT NULL COMMENT '目标玩家',
  `showTime` int(11) NOT NULL,
  `startTime` int(11) NOT NULL,
  `endTime` int(11) NOT NULL,
  `exCount` int(11) NOT NULL COMMENT '表示每兑换时段可兑换次数',
  `cond` text NOT NULL,
  `result` text NOT NULL,
  `secTimes` text NOT NULL,
  `public` tinyint(3) NOT NULL DEFAULT '0' COMMENT '1表示发布，0表示不能兑换',
  PRIMARY KEY (`exid`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_game_use`
-- ----------------------------
DROP TABLE IF EXISTS `ol_game_use`;
CREATE TABLE `ol_game_use` (
  `type` tinyint(3) NOT NULL COMMENT '存储的相关类型1,抽奖，2,增加战斗次数，3,战斗江湖令替换军粮',
  `difficulty` tinyint(3) NOT NULL COMMENT '关卡度难',
  `stageID` tinyint(3) NOT NULL,
  `subStageID` tinyint(3) NOT NULL,
  `giveUpNum` tinyint(3) NOT NULL COMMENT '牌弃数',
  `jl` tinyint(3) NOT NULL,
  `yp` tinyint(3) NOT NULL,
  `yb` tinyint(3) NOT NULL,
  `jhl` tinyint(3) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `key_ol_game_use_index` (`type`,`difficulty`,`stageID`,`subStageID`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=306 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_gembox_info`
-- ----------------------------
DROP TABLE IF EXISTS `ol_gembox_info`;
CREATE TABLE `ol_gembox_info` (
  `playersid` int(11) NOT NULL,
  `gemBoxInfo` varchar(160) NOT NULL COMMENT 'json序列化的数据，表示每个宝箱的状态',
  `validay` date NOT NULL COMMENT '有效日期，同时也是宝箱生成日期',
  KEY `gambox_info_playersid` (`playersid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_general`
-- ----------------------------
DROP TABLE IF EXISTS `ol_general`;
CREATE TABLE `ol_general` (
  `generalid` int(3) NOT NULL AUTO_INCREMENT COMMENT '将领id',
  `general_name` varchar(64) DEFAULT NULL COMMENT '将领名称',
  `professional` varchar(64) DEFAULT NULL COMMENT '职业（如炮兵将领或者弓兵将领）',
  `sex` tinyint(1) DEFAULT NULL COMMENT ' 将领性别',
  `attack_value` int(11) DEFAULT NULL COMMENT '攻击值',
  `defense_value` int(11) DEFAULT NULL COMMENT '防御值',
  `physical_value` int(11) DEFAULT NULL COMMENT '体力值',
  `agility_value` int(11) DEFAULT NULL COMMENT '将领敏捷值',
  `avatar` tinyint(3) DEFAULT NULL COMMENT '将领头像',
  `general_desc` text COMMENT '将领功能介绍',
  PRIMARY KEY (`generalid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_general_update`
-- ----------------------------
DROP TABLE IF EXISTS `ol_general_update`;
CREATE TABLE `ol_general_update` (
  `playerid` int(11) NOT NULL,
  `last_updatetime` int(11) DEFAULT NULL COMMENT '记录上次将领更新的时间',
  PRIMARY KEY (`playerid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_glb`
-- ----------------------------
DROP TABLE IF EXISTS `ol_glb`;
CREATE TABLE `ol_glb` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_guide_event`
-- ----------------------------
DROP TABLE IF EXISTS `ol_guide_event`;
CREATE TABLE `ol_guide_event` (
  `playersid` int(11) NOT NULL,
  `guide_event` text NOT NULL COMMENT '所有已接收和完成事件',
  PRIMARY KEY (`playersid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_hqjl_log`
-- ----------------------------
DROP TABLE IF EXISTS `ol_hqjl_log`;
CREATE TABLE `ol_hqjl_log` (
  `playersid` int(11) NOT NULL,
  `jlhq_time` int(11) NOT NULL,
  `jl` tinyint(2) NOT NULL,
  PRIMARY KEY (`playersid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_huodong`
-- ----------------------------
DROP TABLE IF EXISTS `ol_huodong`;
CREATE TABLE `ol_huodong` (
  `hdid` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `desc` varchar(255) NOT NULL COMMENT '活动描述',
  `hddx` varchar(50) NOT NULL COMMENT '活动对象',
  `startTime` int(11) NOT NULL COMMENT '活动开始时间',
  `endTime` int(11) NOT NULL COMMENT '活动结束时间',
  `jpsj` text NOT NULL COMMENT '奖品数据',
  `params` text NOT NULL COMMENT '脚本参数',
  `jbstr` text NOT NULL COMMENT '活动完成脚本字串，',
  `published` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否发布，1发布，0不发布',
  `wccs` tinyint(3) NOT NULL COMMENT '可完成次数',
  `jslj` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否结束时领奖，1是，0不是',
  `showTime` int(11) NOT NULL COMMENT '活动展示时间',
  `secTime` text NOT NULL COMMENT '活动结算时间配置',
  `secCptCount` varchar(168) NOT NULL DEFAULT '' COMMENT '每个阶段可以完成次数的定义',
  PRIMARY KEY (`hdid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_ifengpayinfo`
-- ----------------------------
DROP TABLE IF EXISTS `ol_ifengpayinfo`;
CREATE TABLE `ol_ifengpayinfo` (
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
--  Table structure for `ol_ifengpayinfo_error`
-- ----------------------------
DROP TABLE IF EXISTS `ol_ifengpayinfo_error`;
CREATE TABLE `ol_ifengpayinfo_error` (
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
--  Table structure for `ol_jf_payinfo`
-- ----------------------------
DROP TABLE IF EXISTS `ol_jf_payinfo`;
CREATE TABLE `ol_jf_payinfo` (
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
--  Table structure for `ol_jf1_payinfo`
-- ----------------------------
DROP TABLE IF EXISTS `ol_jf1_payinfo`;
CREATE TABLE `ol_jf1_payinfo` (
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
--  Table structure for `ol_jq`
-- ----------------------------
DROP TABLE IF EXISTS `ol_jq`;
CREATE TABLE `ol_jq` (
  `jq_id` bigint(18) NOT NULL AUTO_INCREMENT,
  `wfpid` bigint(12) NOT NULL COMMENT '我方玩家ID',
  `wfmc` varchar(50) NOT NULL COMMENT '我方玩家名称',
  `wfgid` bigint(12) NOT NULL COMMENT '我方武将ID',
  `wfgname` varchar(10) NOT NULL COMMENT '我方武将名称',
  `zyid` bigint(12) NOT NULL COMMENT '资源ID',
  `zydowner` bigint(12) NOT NULL,
  `ddsj` int(10) NOT NULL COMMENT '到达时间',
  `jqlx` tinyint(1) NOT NULL COMMENT '军情类型2、占领3、掠夺4、召回1、驻守',
  `dfpid` bigint(12) NOT NULL COMMENT '地方玩家ID',
  `dfmc` varchar(50) NOT NULL COMMENT '敌方',
  `createTime` int(10) NOT NULL COMMENT '军情生成日期',
  `xhsj` int(4) NOT NULL COMMENT '路途消耗时间',
  `jlsj` varchar(50) NOT NULL COMMENT '奖励数据',
  PRIMARY KEY (`jq_id`),
  KEY `wfpid` (`wfpid`) USING BTREE,
  KEY `jqlx` (`jqlx`) USING BTREE,
  KEY `zyid` (`zyid`) USING BTREE,
  KEY `dfpid` (`dfpid`) USING BTREE,
  KEY `ddsj` (`ddsj`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_korea_payinfo`
-- ----------------------------
DROP TABLE IF EXISTS `ol_korea_payinfo`;
CREATE TABLE `ol_korea_payinfo` (
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
--  Table structure for `ol_korea_payinfo_err`
-- ----------------------------
DROP TABLE IF EXISTS `ol_korea_payinfo_err`;
CREATE TABLE `ol_korea_payinfo_err` (
  `txid` char(25) NOT NULL,
  `userid` bigint(12) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `message` char(200) NOT NULL,
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `orderTime` int(11) NOT NULL,
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_kugoupayinfo`
-- ----------------------------
DROP TABLE IF EXISTS `ol_kugoupayinfo`;
CREATE TABLE `ol_kugoupayinfo` (
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
--  Table structure for `ol_kugoupayinfo_error`
-- ----------------------------
DROP TABLE IF EXISTS `ol_kugoupayinfo_error`;
CREATE TABLE `ol_kugoupayinfo_error` (
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
--  Table structure for `ol_ld_payinfo`
-- ----------------------------
DROP TABLE IF EXISTS `ol_ld_payinfo`;
CREATE TABLE `ol_ld_payinfo` (
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
--  Table structure for `ol_ld_payinfo_error`
-- ----------------------------
DROP TABLE IF EXISTS `ol_ld_payinfo_error`;
CREATE TABLE `ol_ld_payinfo_error` (
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
--  Table structure for `ol_lenovpayinfo`
-- ----------------------------
DROP TABLE IF EXISTS `ol_lenovpayinfo`;
CREATE TABLE `ol_lenovpayinfo` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `transid` char(36) NOT NULL,
  `transdata` char(255) NOT NULL,
  `sign` char(255) NOT NULL,
  `payTime` int(11) NOT NULL,
  PRIMARY KEY (`intID`),
  UNIQUE KEY `transid` (`transid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_lenovpayinfo_error`
-- ----------------------------
DROP TABLE IF EXISTS `ol_lenovpayinfo_error`;
CREATE TABLE `ol_lenovpayinfo_error` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `transid` char(36) NOT NULL,
  `transdata` char(255) NOT NULL,
  `sign` char(255) NOT NULL,
  `payTime` int(11) NOT NULL,
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_letters`
-- ----------------------------
DROP TABLE IF EXISTS `ol_letters`;
CREATE TABLE `ol_letters` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT ' ',
  `playersid` int(11) NOT NULL COMMENT '收信人 playersid',
  `type` int(11) NOT NULL COMMENT '类型 1游戏内部信件，2 UC系统信件',
  `genre` int(11) NOT NULL,
  `is_passive` int(1) NOT NULL DEFAULT '0' COMMENT '是否被动送礼 1 是 0 不是',
  `practiceid` int(11) NOT NULL DEFAULT '0' COMMENT '历练id',
  `subject` varchar(30) NOT NULL COMMENT '标题',
  `is_interaction` int(11) NOT NULL DEFAULT '0' COMMENT '是否交互  0 不交互  1  交互',
  `interaction_result` int(2) NOT NULL COMMENT '要求返回，1同意 0不同意',
  `parameters` varchar(200) NOT NULL COMMENT '参数',
  `fromplayersid` int(11) NOT NULL DEFAULT '0' COMMENT '发信人',
  `message` text NOT NULL COMMENT '消息',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '状态 0 未接收， 1 未处理，2 已处理',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `request` text NOT NULL COMMENT '返回值',
  `tradeid` int(8) NOT NULL COMMENT '绑定送礼id',
  PRIMARY KEY (`id`),
  KEY `pid` (`playersid`) USING BTREE,
  KEY `ctime` (`create_time`,`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_log`
-- ----------------------------
DROP TABLE IF EXISTS `ol_log`;
CREATE TABLE `ol_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_url` varchar(30) NOT NULL,
  `return_data` varchar(30) NOT NULL,
  `userid` bigint(11) NOT NULL,
  `ucid` bigint(11) NOT NULL,
  `operate` varchar(30) NOT NULL,
  `create_time` int(11) NOT NULL,
  `type` int(4) NOT NULL,
  `level` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_log_login`
-- ----------------------------
DROP TABLE IF EXISTS `ol_log_login`;
CREATE TABLE `ol_log_login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` bigint(11) NOT NULL,
  `ucid` bigint(11) NOT NULL,
  `operate` varchar(30) NOT NULL,
  `create_time` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- ----------------------------
--  Table structure for `ol_login_award`
-- ----------------------------
DROP TABLE IF EXISTS `ol_login_award`;
CREATE TABLE `ol_login_award` (
  `playersid` int(12) NOT NULL,
  `award` varchar(32) NOT NULL COMMENT '连续登录奖励key=0是20天其他1到7天，value0没有，1已领取，2可领取',
  `lastDay` date NOT NULL,
  `wday` int(11) NOT NULL COMMENT '连续登录7日',
  `mday` int(11) NOT NULL COMMENT '连续登录20天数据',
  PRIMARY KEY (`playersid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_lpm`
-- ----------------------------
DROP TABLE IF EXISTS `ol_lpm`;
CREATE TABLE `ol_lpm` (
  `lpm` char(8) NOT NULL,
  `endTime` int(11) NOT NULL,
  `playersid` bigint(12) NOT NULL,
  `lpmlx` char(2) NOT NULL,
  PRIMARY KEY (`lpm`),
  KEY `playersid` (`playersid`),
  KEY `lpmlx` (`lpmlx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_ltsjpayinfo`
-- ----------------------------
DROP TABLE IF EXISTS `ol_ltsjpayinfo`;
CREATE TABLE `ol_ltsjpayinfo` (
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
--  Table structure for `ol_ltsjpayinfo_error`
-- ----------------------------
DROP TABLE IF EXISTS `ol_ltsjpayinfo_error`;
CREATE TABLE `ol_ltsjpayinfo_error` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `result` tinyint(1) NOT NULL,
  `paymentid` char(100) NOT NULL,
  `errorstr` char(255) NOT NULL,
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_message_failed`
-- ----------------------------
DROP TABLE IF EXISTS `ol_message_failed`;
CREATE TABLE `ol_message_failed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `playersid` int(11) NOT NULL COMMENT '发送方playersid',
  `fucid` int(11) NOT NULL COMMENT '收信方ucid',
  `type` int(11) NOT NULL COMMENT '类型 1邀请 2送礼',
  `create_time` int(11) DEFAULT NULL COMMENT '发送失败日期',
  `message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_message_status`
-- ----------------------------
DROP TABLE IF EXISTS `ol_message_status`;
CREATE TABLE `ol_message_status` (
  `playersid` int(11) NOT NULL,
  `messageType` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1消息2战报',
  `messageTime` int(11) NOT NULL,
  `messageCount` int(4) NOT NULL DEFAULT '1' COMMENT '消息数量',
  `xzdrz` int(11) DEFAULT '0' COMMENT '新战斗日志条数',
  `xhyrz` int(11) DEFAULT '0' COMMENT '新好友日志条数',
  `xxxrz` int(11) DEFAULT '0' COMMENT '新系统日志条数',
  `xzyrz` int(11) DEFAULT '0' COMMENT '新资源信件条数',
  `zxlx` int(11) DEFAULT NULL COMMENT '最新类型',
  PRIMARY KEY (`playersid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_mi_payinfo`
-- ----------------------------
DROP TABLE IF EXISTS `ol_mi_payinfo`;
CREATE TABLE `ol_mi_payinfo` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_mi_payinfo_error`
-- ----------------------------
DROP TABLE IF EXISTS `ol_mi_payinfo_error`;
CREATE TABLE `ol_mi_payinfo_error` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_monster_bak`
-- ----------------------------
DROP TABLE IF EXISTS `ol_monster_bak`;
CREATE TABLE `ol_monster_bak` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `npcid` int(11) NOT NULL,
  `general_sort` tinyint(2) NOT NULL,
  `general_name` varchar(64) NOT NULL,
  `general_level` int(3) NOT NULL,
  `sex` tinyint(1) NOT NULL DEFAULT '1',
  `professional` tinyint(1) NOT NULL,
  `attack_value` int(11) NOT NULL,
  `defense_value` int(11) NOT NULL,
  `physical_value` int(11) NOT NULL,
  `agility_value` int(11) NOT NULL,
  `understanding_value` int(11) NOT NULL,
  `professional_level` tinyint(1) NOT NULL,
  `mobility` int(4) NOT NULL,
  `avatar` char(20) NOT NULL,
  PRIMARY KEY (`intID`),
  KEY `npcid` (`npcid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=862 DEFAULT CHARSET=utf8 COMMENT='记录热点怪物信息';

-- ----------------------------
--  Table structure for `ol_my_exchange`
-- ----------------------------
DROP TABLE IF EXISTS `ol_my_exchange`;
CREATE TABLE `ol_my_exchange` (
  `playersid` int(12) NOT NULL,
  `exc_id` int(11) NOT NULL,
  `exc_count` int(11) NOT NULL,
  `expire_time` int(11) NOT NULL,
  UNIQUE KEY `index_ol_my_exchange_pid_exid` (`playersid`,`exc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_my_huodong`
-- ----------------------------
DROP TABLE IF EXISTS `ol_my_huodong`;
CREATE TABLE `ol_my_huodong` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `playersid` int(11) NOT NULL,
  `hdid` int(11) NOT NULL COMMENT '活动ID',
  `wccs` tinyint(3) NOT NULL COMMENT '活动可完成次数',
  `createTime` int(11) NOT NULL,
  `cptTimes` smallint(8) NOT NULL DEFAULT '0' COMMENT '活动完成次数',
  `startTime` int(11) NOT NULL,
  `endTime` int(11) NOT NULL,
  `jljd` tinyint(3) NOT NULL DEFAULT '-1' COMMENT '奖励进度',
  `title` varchar(50) NOT NULL,
  `desc` varchar(255) NOT NULL,
  `hddx` varchar(50) NOT NULL COMMENT '活动对象',
  `jpsj` text NOT NULL COMMENT '奖品数据',
  `procData` varchar(255) DEFAULT '' COMMENT '活动处理状态数据',
  `wczt` smallint(8) NOT NULL DEFAULT '0' COMMENT '是否已领取奖励次数，如果wccs和这个值一致活动最终完成',
  `jslj` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否结束时领奖，1是，0不是',
  `jpjl` varchar(256) NOT NULL DEFAULT '' COMMENT '奖品数据，记录完成后可以领取的奖励',
  `secCptTimes` int(11) NOT NULL DEFAULT '0' COMMENT '表示当前玩家活动的当前阶段已完成次数',
  `lastSection` int(11) NOT NULL DEFAULT '0' COMMENT '最后记录的阶段位置，帮助secCptTimes字段确认完成次数',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `hd_playersid` (`playersid`,`hdid`) USING BTREE,
  KEY `index_key_hd_hdid` (`hdid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_node_event`
-- ----------------------------
DROP TABLE IF EXISTS `ol_node_event`;
CREATE TABLE `ol_node_event` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `node_id` int(11) NOT NULL,
  `event_type` tinyint(3) NOT NULL,
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_notice`
-- ----------------------------
DROP TABLE IF EXISTS `ol_notice`;
CREATE TABLE `ol_notice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notice` text,
  `start_date` varchar(200) DEFAULT NULL,
  `end_date` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_npc`
-- ----------------------------
DROP TABLE IF EXISTS `ol_npc`;
CREATE TABLE `ol_npc` (
  `npcid` int(11) NOT NULL,
  `level` int(3) NOT NULL,
  `name` varchar(10) NOT NULL,
  `profession` varchar(20) NOT NULL,
  `npcType` tinyint(1) NOT NULL COMMENT '1:普通打怪点；2:BOSS打怪点',
  PRIMARY KEY (`npcid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_online`
-- ----------------------------
DROP TABLE IF EXISTS `ol_online`;
CREATE TABLE `ol_online` (
  `playersid` int(11) NOT NULL,
  `updateTime` int(11) NOT NULL,
  PRIMARY KEY (`playersid`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- ----------------------------
--  Table structure for `ol_player`
-- ----------------------------
DROP TABLE IF EXISTS `ol_player`;
CREATE TABLE `ol_player` (
  `playersid` bigint(12) NOT NULL AUTO_INCREMENT COMMENT '玩家id',
  `userid` int(11) NOT NULL COMMENT '所属用户id',
  `nickname` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '玩家昵称',
  `ucid` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `vip` int(1) NOT NULL DEFAULT '0',
  `vip_end_time` int(11) NOT NULL,
  `sex` tinyint(1) NOT NULL,
  `player_level` tinyint(3) NOT NULL DEFAULT '1',
  `current_experience_value` int(11) NOT NULL DEFAULT '0',
  `regionid` int(11) NOT NULL,
  `sgpf` char(8) NOT NULL DEFAULT '00000000' COMMENT '评分奖励使用',
  `last_update_food` int(11) NOT NULL,
  `last_zl_collect_time` int(11) NOT NULL DEFAULT '0' COMMENT '领占者上次征税时间',
  `last_collect_time` int(11) NOT NULL DEFAULT '0' COMMENT '上次征税时间',
  `last_update_general` int(11) NOT NULL,
  `coins` int(11) NOT NULL DEFAULT '0' COMMENT '钱铜',
  `silver` int(11) NOT NULL DEFAULT '200' COMMENT '银票',
  `ingot` int(11) NOT NULL COMMENT '元宝',
  `food` decimal(9,3) NOT NULL DEFAULT '10.000',
  `completeQuests` text NOT NULL COMMENT '已完成任务',
  `boxOpen` text NOT NULL COMMENT '开宝箱',
  `boxTime` int(11) NOT NULL COMMENT '开宝箱时间',
  `bagpack` smallint(6) NOT NULL COMMENT '背包已使用格数',
  `inviteid` int(11) NOT NULL,
  `last_twk_time` int(11) NOT NULL DEFAULT '0' COMMENT '上次偷挖时间',
  `last_wk_time` int(11) NOT NULL DEFAULT '0' COMMENT '上次挖矿时间',
  `wk_count` int(2) NOT NULL DEFAULT '0' COMMENT '挖矿次数',
  `sc_level` tinyint(3) NOT NULL DEFAULT '1' COMMENT '市场等级',
  `ld_level` tinyint(3) NOT NULL DEFAULT '1' COMMENT '领地等级',
  `tjp_level` tinyint(3) NOT NULL DEFAULT '1' COMMENT '匠铺铁等级',
  `jg_level` tinyint(3) NOT NULL DEFAULT '1' COMMENT '酒馆等级',
  `djt_level` tinyint(3) NOT NULL DEFAULT '1' COMMENT '点将台等级',
  `aggressor_playersid` int(11) NOT NULL DEFAULT '0' COMMENT '寨山所有者，默认0',
  `aggressor_nickname` varchar(50) NOT NULL COMMENT '占领者姓名',
  `aggressor_level` int(3) NOT NULL DEFAULT '0' COMMENT '领占者级别',
  `aggressor_prestige` int(3) NOT NULL DEFAULT '0' COMMENT '领占方地位等级',
  `aggressor_general` int(11) NOT NULL DEFAULT '0' COMMENT '领占者将领',
  `zf_aggressor_general` int(11) NOT NULL DEFAULT '0' COMMENT '原始驻防武将ID',
  `is_defend` tinyint(1) NOT NULL DEFAULT '0' COMMENT '否是设防',
  `end_defend_time` int(11) NOT NULL DEFAULT '0' COMMENT '守驻结束时间',
  `strategy` tinyint(1) NOT NULL DEFAULT '0' COMMENT '守驻或占领策略',
  `zf_strategy` tinyint(1) NOT NULL DEFAULT '0' COMMENT '单纯驻防策略',
  `mg_level` int(1) NOT NULL DEFAULT '1' COMMENT '爵位 1兵卒',
  `prestige` int(11) NOT NULL DEFAULT '0' COMMENT '声望值',
  `jscs` tinyint(1) NOT NULL DEFAULT '0' COMMENT '征税加速次数',
  `jssjrq` int(11) NOT NULL DEFAULT '0' COMMENT '加速时间日期',
  `is_reason` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否封禁',
  `createTime` int(11) NOT NULL DEFAULT '0' COMMENT '角色创建时间',
  `tb_cs` smallint(6) NOT NULL DEFAULT '0' COMMENT '已探宝次数',
  `tb_gk` smallint(6) NOT NULL DEFAULT '1' COMMENT '探宝关卡',
  `tb_sjrq` int(11) NOT NULL DEFAULT '0' COMMENT '上次探宝时间',
  `rw_vip` tinyint(4) NOT NULL DEFAULT '0',
  `rw_vip_end_time` int(11) NOT NULL DEFAULT '0',
  `frd` int(5) NOT NULL COMMENT '繁荣度',
  `lssxcs` int(11) NOT NULL COMMENT '蓝色刷新次数',
  `zssxcs` int(11) NOT NULL,
  `cssxcs` int(11) NOT NULL,
  `last_dalei_time` int(11) NOT NULL DEFAULT '0' COMMENT '最后打擂的时间',
  `username` varchar(64) NOT NULL COMMENT '用户名称',
  `reason_memo` text NOT NULL,
  `reason_time` int(11) NOT NULL,
  `isActivated` tinyint(1) NOT NULL,
  `last_login_time` int(11) NOT NULL,
  `rcrws` tinyint(2) NOT NULL DEFAULT '0' COMMENT '日常完成任务数',
  `xwzt` char(35) NOT NULL DEFAULT '00000000000000000000000000000000' COMMENT '用户行为记录',
  `ba` int(11) DEFAULT '0' COMMENT '玩家战功',
  `rank` int(11) DEFAULT '109' COMMENT '座次',
  `rank_cd` varchar(50) DEFAULT '0' COMMENT '座次挑战失败cd',
  `today_addba` int(11) DEFAULT '0' COMMENT '每日增加战功',
  `today_reduceba` int(11) DEFAULT '0' COMMENT '每日减少战功',
  `curr_ba_date` varchar(50) DEFAULT '0' COMMENT '当前累计和减少战功日期',
  `qzcs` tinyint(1) NOT NULL DEFAULT '0' COMMENT '强征次数',
  `qzsjrq` int(11) NOT NULL DEFAULT '0' COMMENT '强征时间日期',
  `jcb` int(3) NOT NULL DEFAULT '50' COMMENT '武将传承比率',
  `rwsj` char(32) NOT NULL DEFAULT '000000000000000000000000000000' COMMENT '任务事件记录',
  `dqzmbid` int(11) NOT NULL DEFAULT '0' COMMENT '当前主目标ID',
  `hyd` int(3) NOT NULL DEFAULT '0' COMMENT '活跃度',
  `hyjl` char(5) NOT NULL DEFAULT '00000' COMMENT '活跃度奖励',
  `ykzjrl` int(11) NOT NULL DEFAULT '0' COMMENT '银库增加容量',
  `today_ba_uplimit` int(11) NOT NULL DEFAULT '0',
  `today_ba_downlimit` int(11) NOT NULL DEFAULT '0',
  `last_update_level` int(11) DEFAULT '0' COMMENT '最后升级时间',
  `phone` varchar(64) DEFAULT '0' COMMENT 'çŽ©å®¶ç»‘å®šæ‰‹æœºå·,0æ ‡è¯†æ²¡æœ‰ç»‘å®šï¼Œ999999è¡¨ç¤ºç­‰å¾…çŽ©å®¶è®¿é—®æ ‡è¯†',
  `r_code` varchar(24) DEFAULT '' COMMENT '用户邀请码',
  PRIMARY KEY (`playersid`),
  KEY `aggressor_playersid` (`aggressor_playersid`) USING BTREE,
  KEY `userid` (`userid`) USING BTREE,
  KEY `ucid` (`ucid`) USING BTREE,
  KEY `nick` (`nickname`) USING BTREE,
  KEY `ol_player_player_level` (`player_level`) USING BTREE,
  KEY `IDX_last_update_food` (`last_update_food`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_player_achieve`
-- ----------------------------
DROP TABLE IF EXISTS `ol_player_achieve`;
CREATE TABLE `ol_player_achieve` (
  `pid` bigint(12) NOT NULL,
  `cj` text NOT NULL,
  PRIMARY KEY (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_player_boss`
-- ----------------------------
DROP TABLE IF EXISTS `ol_player_boss`;
CREATE TABLE `ol_player_boss` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `playersid` int(11) NOT NULL,
  `npcid` int(11) NOT NULL,
  `general_sort` tinyint(2) NOT NULL,
  `general_name` varchar(64) NOT NULL,
  `general_level` int(3) NOT NULL,
  `sex` tinyint(1) NOT NULL DEFAULT '1',
  `professional` tinyint(1) NOT NULL,
  `attack_value` int(11) NOT NULL,
  `defense_value` int(11) NOT NULL,
  `physical_value` int(11) NOT NULL,
  `agility_value` int(11) NOT NULL,
  `understanding_value` int(11) NOT NULL,
  `professional_level` tinyint(1) NOT NULL,
  `mobility` int(4) NOT NULL,
  `bossLife` int(11) NOT NULL,
  `avatar` char(20) NOT NULL,
  PRIMARY KEY (`intID`),
  KEY `npcid` (`npcid`,`playersid`) USING BTREE,
  KEY `IDX_playersid` (`playersid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_player_cj`
-- ----------------------------
DROP TABLE IF EXISTS `ol_player_cj`;
CREATE TABLE `ol_player_cj` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `playersid` int(11) NOT NULL,
  `item_pack` text NOT NULL COMMENT '道具id itemid 道具名 name 图标 iconid 成功率 cgl',
  `stageID` tinyint(3) NOT NULL,
  `subStageID` tinyint(3) NOT NULL,
  `difficulty` tinyint(3) NOT NULL,
  `letterID` int(11) DEFAULT NULL,
  `create_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ol_player_cj_id_pkey` (`id`) USING BTREE,
  KEY `ol_player_cj_letterid` (`letterID`) USING BTREE,
  KEY `ctime` (`create_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_player_items`
-- ----------------------------
DROP TABLE IF EXISTS `ol_player_items`;
CREATE TABLE `ol_player_items` (
  `ID` bigint(12) NOT NULL AUTO_INCREMENT,
  `ItemID` int(11) NOT NULL DEFAULT '0',
  `EquipCount` smallint(6) NOT NULL DEFAULT '0' COMMENT '道具数量',
  `QhLevel` tinyint(3) NOT NULL DEFAULT '0' COMMENT '强化等级',
  `Playersid` int(11) NOT NULL DEFAULT '0' COMMENT '装备对应的玩家id',
  `SortNo` tinyint(3) NOT NULL DEFAULT '0' COMMENT '装备道具在背包中的序号',
  `IsEquipped` int(11) NOT NULL DEFAULT '0' COMMENT 'æ˜¯å¦æ˜¯å•†åŸŽé“å…· 1 æ˜¯ 0å¦ å¦‚æžœæ˜¯ç¬¬2ä½ 1 é“å…· 2ææ–™ 3é“œé’± ç¬¬3ä½ 0 éžçƒ­å– 1 çƒ­å–',
  `luck` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `Playersid` (`Playersid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_player_ly_count`
-- ----------------------------
DROP TABLE IF EXISTS `ol_player_ly_count`;
CREATE TABLE `ol_player_ly_count` (
  `playersid` int(11) NOT NULL,
  `lysl` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`playersid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_player_pm`
-- ----------------------------
DROP TABLE IF EXISTS `ol_player_pm`;
CREATE TABLE `ol_player_pm` (
  `playersid` int(11) NOT NULL,
  `mc` varchar(64) DEFAULT NULL,
  `zl` int(11) NOT NULL DEFAULT '0' COMMENT '玩家战力',
  `increase` int(11) NOT NULL DEFAULT '0' COMMENT '战力增加',
  `lv` int(11) NOT NULL DEFAULT '0' COMMENT '排名时等级',
  `exp` int(11) NOT NULL DEFAULT '0' COMMENT '排名时玩家经验值',
  `lv_last_time` int(11) NOT NULL DEFAULT '0' COMMENT '排名时等级最后更新时间',
  `jw` int(11) NOT NULL DEFAULT '1' COMMENT '排名时爵位',
  `prestige` int(11) NOT NULL DEFAULT '0' COMMENT '排名时声望值',
  `chkTime` int(11) NOT NULL DEFAULT '0' COMMENT '更新玩家数据时间',
  PRIMARY KEY (`playersid`),
  KEY `inx_ol_player_pm_increase` (`increase`),
  KEY `inx_ol_player_pm_zl` (`zl`),
  KEY `inx_ol_player_pm_lv` (`lv`,`exp`,`lv_last_time`),
  KEY `inx_ol_player_pm_jw` (`jw`,`prestige`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_player_shop`
-- ----------------------------
DROP TABLE IF EXISTS `ol_player_shop`;
CREATE TABLE `ol_player_shop` (
  `pid` int(11) NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_player_stage`
-- ----------------------------
DROP TABLE IF EXISTS `ol_player_stage`;
CREATE TABLE `ol_player_stage` (
  `playersid` int(11) NOT NULL,
  `difficulty` tinyint(2) NOT NULL DEFAULT '1' COMMENT '难度模式',
  `unlock` int(11) NOT NULL DEFAULT '1' COMMENT '解锁关卡',
  `curr_difficulty` int(11) NOT NULL DEFAULT '0' COMMENT '日次需重置',
  `curr_stage` int(11) NOT NULL DEFAULT '0' COMMENT '当前关卡 日次需重置',
  `curr_subStage` int(11) NOT NULL DEFAULT '0' COMMENT '当前小关卡 日次需重置',
  `last_cg_date` varchar(100) NOT NULL COMMENT '最后闯关时间',
  `attackBossTimes` tinyint(2) NOT NULL DEFAULT '0' COMMENT '打Boss次数 次日需重置',
  `buyTimes` int(11) DEFAULT '0',
  `addTimes` int(11) NOT NULL DEFAULT '0' COMMENT '用江湖令增加的闯关次数 次日需重置',
  `cgTimes` int(11) NOT NULL DEFAULT '0' COMMENT '闯关次数 次日需重置',
  `timesLimt` int(11) NOT NULL DEFAULT '2',
  PRIMARY KEY (`playersid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_player_weibo`
-- ----------------------------
DROP TABLE IF EXISTS `ol_player_weibo`;
CREATE TABLE `ol_player_weibo` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `playersid` bigint(12) NOT NULL,
  `cjid` int(8) NOT NULL,
  `cjjd` varchar(64) NOT NULL,
  `zt` tinyint(1) NOT NULL,
  PRIMARY KEY (`intID`),
  KEY `playersid` (`playersid`),
  KEY `cjid` (`cjid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_playergeneral`
-- ----------------------------
DROP TABLE IF EXISTS `ol_playergeneral`;
CREATE TABLE `ol_playergeneral` (
  `intID` bigint(12) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `playerid` int(11) NOT NULL,
  `general_sort` tinyint(2) NOT NULL COMMENT '将领排序',
  `general_name` varchar(10) NOT NULL COMMENT '将领名称',
  `general_level` tinyint(3) NOT NULL DEFAULT '1' COMMENT '将领级别',
  `general_sex` tinyint(1) NOT NULL COMMENT '将领性别',
  `general_life` int(11) NOT NULL DEFAULT '0' COMMENT '领将生命值',
  `avatar` varchar(10) NOT NULL COMMENT '头像标识',
  `last_income_time` int(11) NOT NULL DEFAULT '0' COMMENT '次上获取收益时间',
  `occupied_end_time` int(11) NOT NULL,
  `occupied_playersid` int(11) NOT NULL DEFAULT '0' COMMENT '占领山寨ID',
  `occupied_player_level` int(3) NOT NULL DEFAULT '0' COMMENT '被占领玩家级别',
  `occupied_player_nickname` varchar(50) NOT NULL COMMENT '被占领玩家名称',
  `current_experience` int(11) NOT NULL COMMENT '将领当前的经验值， 将领升级使用',
  `professional` tinyint(1) NOT NULL COMMENT '职业（炮兵将领还是弓箭兵将领等）',
  `f_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '战斗状态，出征或空闲,1出征0空闲',
  `understanding_value` tinyint(2) NOT NULL COMMENT '天赋',
  `professional_level` int(11) NOT NULL DEFAULT '1' COMMENT '军阶 1,2,3,4,5',
  `helmet` int(11) NOT NULL DEFAULT '0' COMMENT '头盔id(对应我的物品表)',
  `carapace` int(11) NOT NULL DEFAULT '0' COMMENT '胸甲id(对应我的物品表)',
  `arms` int(11) NOT NULL DEFAULT '0' COMMENT '武器id(对应我的物品表)',
  `shoes` int(11) NOT NULL DEFAULT '0' COMMENT '战靴id(对应我的物品表)',
  `suitid` tinyint(2) NOT NULL DEFAULT '0' COMMENT '套装类型，如果不是套装则为0',
  `arm` tinyint(3) NOT NULL DEFAULT '0' COMMENT '兵种与职业一样',
  `mj` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否名将，1是0否',
  `llzt` int(3) NOT NULL DEFAULT '0' COMMENT '练历状态',
  `llcs` tinyint(2) NOT NULL DEFAULT '0' COMMENT '练历次数',
  `last_end_ll` int(11) NOT NULL DEFAULT '0' COMMENT '上次历练时间',
  `jn1` tinyint(3) NOT NULL DEFAULT '0' COMMENT '将武技能1',
  `jn1_level` tinyint(2) NOT NULL DEFAULT '0' COMMENT '将武技能1等级',
  `jn2` tinyint(3) NOT NULL DEFAULT '0' COMMENT '将武技能2',
  `jn2_level` tinyint(2) NOT NULL DEFAULT '0' COMMENT '将武技能2等级',
  `xl_end_time` int(11) NOT NULL DEFAULT '0' COMMENT '练训结束时间',
  `gohomeTime` int(11) NOT NULL DEFAULT '0' COMMENT '武将回营时间',
  `jqid` bigint(12) NOT NULL DEFAULT '0' COMMENT '军情ID',
  `zydid` bigint(12) NOT NULL DEFAULT '0' COMMENT '资源点ID',
  `act` tinyint(1) NOT NULL DEFAULT '0' COMMENT '武将活动1、驻守中2、占领中3、掠夺中4、回城中',
  `py_gj` int(6) NOT NULL DEFAULT '0' COMMENT '培养攻击属性值',
  `py_fy` int(6) NOT NULL DEFAULT '0' COMMENT '培养防御属性值',
  `py_tl` int(6) NOT NULL DEFAULT '0' COMMENT '培养体力属性值',
  `py_mj` int(6) NOT NULL DEFAULT '0' COMMENT '培养敏捷属性值',
  PRIMARY KEY (`intID`),
  KEY `occupied_playersid` (`occupied_playersid`) USING BTREE,
  KEY `gpid` (`playerid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_playergeneral_pm`
-- ----------------------------
DROP TABLE IF EXISTS `ol_playergeneral_pm`;
CREATE TABLE `ol_playergeneral_pm` (
  `playersid` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `zl` int(11) NOT NULL DEFAULT '0' COMMENT '武将战力',
  `giid` varchar(10) NOT NULL COMMENT '武将头像',
  `gname` varchar(10) NOT NULL,
  `glevel` int(10) NOT NULL COMMENT '武将等级',
  `gxyd` int(11) NOT NULL DEFAULT '0' COMMENT '武将稀有度',
  `gxj` int(11) NOT NULL COMMENT '武将星级',
  PRIMARY KEY (`gid`),
  KEY `inx_ol_playergeneral_pm_zl` (`zl`),
  KEY `inx_ol_playergeneral_pm_gid` (`gid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_playerxlw`
-- ----------------------------
DROP TABLE IF EXISTS `ol_playerxlw`;
CREATE TABLE `ol_playerxlw` (
  `intID` bigint(12) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `playersid` int(11) NOT NULL DEFAULT '0' COMMENT '家玩ID',
  `gid` int(11) NOT NULL DEFAULT '0' COMMENT '将武ID',
  `g_end_time` int(11) NOT NULL DEFAULT '0' COMMENT '将武完成训练时间',
  `end_time` int(11) NOT NULL DEFAULT '0' COMMENT '练训位结束时间',
  `is_open` tinyint(1) NOT NULL DEFAULT '0' COMMENT '练训位是否开通',
  `px` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'ç»ƒè®­ä½æŽ’åº1,2æ°¸ä¹…ä½3é“å…·ä½4å…ƒå®ä½5ï¼Œ6ï¼Œ7ï¼Œ8vipä½',
  PRIMARY KEY (`intID`),
  KEY `xl_playersid` (`playersid`) USING BTREE,
  KEY `xl_px` (`px`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_pm_inc`
-- ----------------------------
DROP TABLE IF EXISTS `ol_pm_inc`;
CREATE TABLE `ol_pm_inc` (
  `pm` int(11) NOT NULL AUTO_INCREMENT,
  `playersid` int(11) NOT NULL,
  PRIMARY KEY (`pm`),
  UNIQUE KEY `index_tmp_inc_pm` (`playersid`)
) ENGINE=InnoDB AUTO_INCREMENT=8192 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_pm_jw`
-- ----------------------------
DROP TABLE IF EXISTS `ol_pm_jw`;
CREATE TABLE `ol_pm_jw` (
  `pm` int(11) NOT NULL AUTO_INCREMENT,
  `playersid` int(11) NOT NULL,
  PRIMARY KEY (`pm`),
  UNIQUE KEY `index_tmp_jw_pm` (`playersid`)
) ENGINE=InnoDB AUTO_INCREMENT=8192 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_pm_plevel`
-- ----------------------------
DROP TABLE IF EXISTS `ol_pm_plevel`;
CREATE TABLE `ol_pm_plevel` (
  `pm` int(11) NOT NULL AUTO_INCREMENT,
  `playersid` int(11) NOT NULL,
  PRIMARY KEY (`pm`),
  UNIQUE KEY `index_tmp_plevel_pm` (`playersid`)
) ENGINE=InnoDB AUTO_INCREMENT=8192 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_pm_wj`
-- ----------------------------
DROP TABLE IF EXISTS `ol_pm_wj`;
CREATE TABLE `ol_pm_wj` (
  `pm` int(11) NOT NULL AUTO_INCREMENT,
  `playersid` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  PRIMARY KEY (`pm`),
  UNIQUE KEY `index_tmp_wj_pm` (`gid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8192 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_pm_zl`
-- ----------------------------
DROP TABLE IF EXISTS `ol_pm_zl`;
CREATE TABLE `ol_pm_zl` (
  `pm` int(11) NOT NULL AUTO_INCREMENT,
  `playersid` int(11) NOT NULL,
  PRIMARY KEY (`pm`),
  UNIQUE KEY `index_tmp_zl_pm` (`playersid`)
) ENGINE=InnoDB AUTO_INCREMENT=8200 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_posting_package`
-- ----------------------------
DROP TABLE IF EXISTS `ol_posting_package`;
CREATE TABLE `ol_posting_package` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `articleid` int(11) DEFAULT NULL,
  `article_num` tinyint(2) DEFAULT NULL,
  `coins` int(11) DEFAULT NULL,
  `ingot` int(11) DEFAULT NULL,
  `posting_playerid` int(11) DEFAULT NULL,
  `receive_playerid` int(11) DEFAULT NULL,
  `package_stuta` tinyint(1) DEFAULT NULL COMMENT '包裹目前的状态，如是否被接收或者拒收等',
  `posting_date` int(11) DEFAULT NULL,
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_practice`
-- ----------------------------
DROP TABLE IF EXISTS `ol_practice`;
CREATE TABLE `ol_practice` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `playersid` int(11) NOT NULL COMMENT '角色id',
  `generalid` int(11) NOT NULL DEFAULT '0' COMMENT '将领id',
  `yq_playersid` text NOT NULL,
  `is_agree` text NOT NULL,
  `invite_time` text NOT NULL,
  `invite_num` int(11) NOT NULL DEFAULT '0' COMMENT '需要练将人数',
  `tool_num` int(11) NOT NULL DEFAULT '0' COMMENT '使用江湖令个数',
  `current_points` int(11) NOT NULL DEFAULT '0' COMMENT '目前得到积分',
  `status` int(4) NOT NULL DEFAULT '0' COMMENT '状态 1结束  0有效 2取消',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `practice_player_gen` (`playersid`,`generalid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_practice_block`
-- ----------------------------
DROP TABLE IF EXISTS `ol_practice_block`;
CREATE TABLE `ol_practice_block` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `playersid` int(11) NOT NULL,
  `toplayersid` int(11) NOT NULL,
  `blocktime` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `practice_block_playersid` (`playersid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_proc_log`
-- ----------------------------
DROP TABLE IF EXISTS `ol_proc_log`;
CREATE TABLE `ol_proc_log` (
  `server` varchar(64) NOT NULL COMMENT '处理的日志来源的服务器',
  `last_log_file` varchar(32) NOT NULL COMMENT '最后处理的日志文件名',
  `last_log_time` datetime NOT NULL COMMENT '最后处理的日志对应的开始时间',
  `status` varchar(16) DEFAULT 'uncheck',
  PRIMARY KEY (`server`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_prop`
-- ----------------------------
DROP TABLE IF EXISTS `ol_prop`;
CREATE TABLE `ol_prop` (
  `propid` int(11) NOT NULL AUTO_INCREMENT,
  `prop_nam` varchar(64) DEFAULT NULL,
  `pic_tag` int(3) DEFAULT NULL,
  `prop_type` tinyint(3) DEFAULT NULL COMMENT '灵石，符还是其它等',
  `gem_level` tinyint(1) DEFAULT NULL COMMENT '如果类型是灵石，则此处为灵石级别，否则为0',
  `prop_desc` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`propid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_quests_new_status`
-- ----------------------------
DROP TABLE IF EXISTS `ol_quests_new_status`;
CREATE TABLE `ol_quests_new_status` (
  `playersid` int(11) NOT NULL,
  `qstatusInfo` text NOT NULL COMMENT '任务状态保存',
  PRIMARY KEY (`playersid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_ranklist`
-- ----------------------------
DROP TABLE IF EXISTS `ol_ranklist`;
CREATE TABLE `ol_ranklist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rank` int(11) NOT NULL DEFAULT '0' COMMENT '座次',
  `playersid` int(11) NOT NULL DEFAULT '0' COMMENT '玩家ID',
  `kill` tinyint(2) NOT NULL DEFAULT '0' COMMENT '攻',
  `speed` tinyint(2) NOT NULL DEFAULT '0' COMMENT '速',
  `technique` int(11) NOT NULL DEFAULT '0' COMMENT '技',
  `record` text NOT NULL COMMENT '回放',
  PRIMARY KEY (`id`),
  KEY `rank` (`rank`)
) ENGINE=InnoDB AUTO_INCREMENT=541 DEFAULT CHARSET=utf8 COMMENT='座次排行榜';

-- ----------------------------
--  Table structure for `ol_restitution`
-- ----------------------------
DROP TABLE IF EXISTS `ol_restitution`;
CREATE TABLE `ol_restitution` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ucids` text NOT NULL,
  `rule` text NOT NULL,
  `current` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_save_ginfo`
-- ----------------------------
DROP TABLE IF EXISTS `ol_save_ginfo`;
CREATE TABLE `ol_save_ginfo` (
  `playersid` int(11) NOT NULL,
  `ginfo` text NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`playersid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_seatingrank`
-- ----------------------------
DROP TABLE IF EXISTS `ol_seatingrank`;
CREATE TABLE `ol_seatingrank` (
  `intID` int(11) NOT NULL DEFAULT '0',
  `npcid` int(11) NOT NULL,
  `general_sort` tinyint(2) NOT NULL,
  `general_name` varchar(64) NOT NULL,
  `general_level` int(3) NOT NULL,
  `sex` tinyint(1) NOT NULL DEFAULT '1',
  `professional` tinyint(1) NOT NULL,
  `attack_value` int(11) NOT NULL,
  `defense_value` int(11) NOT NULL,
  `physical_value` int(11) NOT NULL,
  `agility_value` int(11) NOT NULL,
  `understanding_value` int(11) NOT NULL,
  `professional_level` tinyint(1) NOT NULL,
  `mobility` int(4) NOT NULL,
  `avatar` char(20) NOT NULL,
  PRIMARY KEY (`intID`),
  KEY `npcid` (`npcid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='座次怪物系统表';

-- ----------------------------
--  Table structure for `ol_sg_payinfo`
-- ----------------------------
DROP TABLE IF EXISTS `ol_sg_payinfo`;
CREATE TABLE `ol_sg_payinfo` (
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
--  Table structure for `ol_sg_payinfo_error`
-- ----------------------------
DROP TABLE IF EXISTS `ol_sg_payinfo_error`;
CREATE TABLE `ol_sg_payinfo_error` (
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
--  Table structure for `ol_shenzhoupay_record`
-- ----------------------------
DROP TABLE IF EXISTS `ol_shenzhoupay_record`;
CREATE TABLE `ol_shenzhoupay_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `playersid` int(11) NOT NULL COMMENT '用户ID',
  `version` varchar(5) NOT NULL COMMENT '神州支付提供的API版本号',
  `payMoney` int(11) NOT NULL COMMENT '支付金额',
  `orderId` varchar(50) NOT NULL COMMENT '订单号',
  `cardInfo` varchar(50) NOT NULL COMMENT '充值卡加密信息',
  `verifyType` int(11) NOT NULL COMMENT '数据校验方式',
  `cardTypeCombine` int(11) NOT NULL COMMENT '充值卡类型  0：移动；1：联通；2：电信',
  `privateField` varchar(200) NOT NULL COMMENT '商户私有数据',
  `hmac` varchar(32) NOT NULL COMMENT '签名数据',
  `verify` int(11) NOT NULL COMMENT '单订验证 0建立订单 1为验证成功 2为验证失败',
  `errcode` varchar(20) NOT NULL COMMENT '错误编号',
  `ordDate` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_shop`
-- ----------------------------
DROP TABLE IF EXISTS `ol_shop`;
CREATE TABLE `ol_shop` (
  `ItemID` int(11) NOT NULL,
  `Condition` varchar(4) NOT NULL DEFAULT '0',
  `TimeStart` varchar(100) NOT NULL,
  `TimeEnd` varchar(100) NOT NULL,
  `BuyOfDay` int(11) NOT NULL,
  `Total` int(11) NOT NULL,
  PRIMARY KEY (`ItemID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_shop_sortrule`
-- ----------------------------
DROP TABLE IF EXISTS `ol_shop_sortrule`;
CREATE TABLE `ol_shop_sortrule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sort_rule` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_social`
-- ----------------------------
DROP TABLE IF EXISTS `ol_social`;
CREATE TABLE `ol_social` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '全部送礼按钮点击时间',
  `userid` bigint(20) NOT NULL DEFAULT '0' COMMENT 'uc用户id',
  `playersid` bigint(20) NOT NULL DEFAULT '0' COMMENT '玩家id',
  `invitenum` int(11) NOT NULL DEFAULT '0' COMMENT '邀请成功数',
  `friendsnum` int(11) NOT NULL DEFAULT '0' COMMENT '好友数',
  `blacklistnum` int(11) NOT NULL DEFAULT '0',
  `enemynum` int(11) NOT NULL DEFAULT '0' COMMENT '仇人数',
  `gift` int(11) NOT NULL DEFAULT '0' COMMENT '奖励物品 1:军粮',
  `steal_time` int(11) NOT NULL DEFAULT '0',
  `stealnum` int(11) NOT NULL DEFAULT '0' COMMENT '当天被挖墙角数',
  `is_practice` int(11) NOT NULL DEFAULT '0',
  `random_friend` varchar(100) NOT NULL COMMENT '好友旁边的邻居',
  `random_time` int(11) NOT NULL DEFAULT '0',
  `random_friend_cr` varchar(100) NOT NULL COMMENT '仇人旁边的邻居',
  `random_time_cr` int(11) NOT NULL DEFAULT '0',
  `gift_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `playersid` (`playersid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_social_trade`
-- ----------------------------
DROP TABLE IF EXISTS `ol_social_trade`;
CREATE TABLE `ol_social_trade` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `fromplayersid` int(11) NOT NULL DEFAULT '0' COMMENT '送礼方',
  `toplayersid` int(11) NOT NULL DEFAULT '0' COMMENT '收礼方',
  `type` int(1) NOT NULL DEFAULT '1' COMMENT '1 送礼， 2回赠， 3索要申请， 4打劫',
  `return_count` int(11) NOT NULL DEFAULT '1' COMMENT '(废弃)回礼次数',
  `interval_time` int(11) NOT NULL DEFAULT '0' COMMENT '（废弃）间隔时间（时间戳小时为单位）',
  `received_time` int(11) DEFAULT NULL COMMENT '(废弃)第一次收到时间',
  `feedback_time` int(11) DEFAULT NULL COMMENT '(废弃)反馈时间(每成功反馈后，字段清空)',
  `gift_type` int(4) DEFAULT NULL COMMENT '要送礼的itemId，对应ol_items',
  `isrobbery` int(1) DEFAULT '0' COMMENT '(废弃)否是打劫',
  `robbery_time` int(11) NOT NULL DEFAULT '0' COMMENT '(废弃)打劫日期',
  `robberyplayersid` int(11) DEFAULT NULL COMMENT '(废弃)打劫人',
  `history` varchar(200) DEFAULT NULL COMMENT '(废弃)历史情况，状态为1,2,6|2,1,8|1,2,9 表示3次交易 第三位为打劫人',
  `is_received` int(11) DEFAULT '0' COMMENT '(废弃)是否领取',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '用于判断是否收过礼物0是没收，1是已收，只对type=1有效',
  `create_time` int(11) NOT NULL COMMENT '送礼开始时间',
  `sponsor_playersid` int(11) NOT NULL DEFAULT '0' COMMENT '(废弃)发起人',
  PRIMARY KEY (`id`),
  KEY `ol_social_trade_fromplayersid` (`fromplayersid`) USING BTREE,
  KEY `topid` (`toplayersid`) USING BTREE,
  KEY `ctime` (`create_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_social_user`
-- ----------------------------
DROP TABLE IF EXISTS `ol_social_user`;
CREATE TABLE `ol_social_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `parent_playersid` int(11) NOT NULL DEFAULT '0' COMMENT '父id',
  `type` int(11) NOT NULL DEFAULT '1' COMMENT '类型:1好友,2黑名单,3仇人',
  `playersid` int(11) NOT NULL DEFAULT '0' COMMENT '角色id',
  `add_time` int(11) DEFAULT NULL COMMENT '加入时间',
  `feel` int(11) NOT NULL DEFAULT '0' COMMENT '好感度',
  PRIMARY KEY (`id`),
  KEY `index_ol_social_user_p_playersid` (`parent_playersid`) USING BTREE,
  KEY `playersid` (`playersid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_st_ingot`
-- ----------------------------
DROP TABLE IF EXISTS `ol_st_ingot`;
CREATE TABLE `ol_st_ingot` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自动主键',
  `playerid` int(11) NOT NULL COMMENT '角色ID',
  `variation` int(11) NOT NULL COMMENT '宝元变化值(正数为充值 负数为花费)',
  `ingot` int(11) NOT NULL COMMENT '剩余元宝数',
  `operation` varchar(255) NOT NULL COMMENT '操作',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_stage`
-- ----------------------------
DROP TABLE IF EXISTS `ol_stage`;
CREATE TABLE `ol_stage` (
  `id` int(11) NOT NULL,
  `difficulty` tinyint(2) NOT NULL,
  `stage` tinyint(2) NOT NULL,
  `subStage` tinyint(2) NOT NULL,
  `npcid` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `sex` tinyint(1) NOT NULL DEFAULT '1',
  `stageLevel` tinyint(2) NOT NULL,
  `exp` int(11) NOT NULL,
  `maxHP` int(11) NOT NULL,
  `dlItem` varchar(100) NOT NULL DEFAULT '0' COMMENT '掉落',
  `dlItemID` int(11) NOT NULL,
  `iconId` varchar(100) NOT NULL DEFAULT '0',
  `stageName` varchar(100) NOT NULL,
  `dialog1` text NOT NULL COMMENT '对话内容一，对话中先说的内容',
  `dialog2` text NOT NULL COMMENT '对话内容二，对话中后说的内容',
  `dialog3` text NOT NULL COMMENT '美完击杀时对话',
  `dialog4` text NOT NULL COMMENT '美完击杀时对话',
  `dialogSort` tinyint(1) NOT NULL COMMENT '对话顺序 \r\n1是NPC先说，玩家后说 \r\n2是玩家先说，NPC后说\r\n',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_suit`
-- ----------------------------
DROP TABLE IF EXISTS `ol_suit`;
CREATE TABLE `ol_suit` (
  `suitid` int(11) NOT NULL AUTO_INCREMENT COMMENT '套装编号',
  `suit_name` varchar(65) DEFAULT NULL,
  `suit_num` tinyint(1) DEFAULT NULL COMMENT '记录此套装是8件套还是6件套等',
  `suit_desc` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`suitid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_suits`
-- ----------------------------
DROP TABLE IF EXISTS `ol_suits`;
CREATE TABLE `ol_suits` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `SuitID` varchar(4) NOT NULL,
  `SuitName` varchar(500) NOT NULL COMMENT '套装名称、其他套装部件名称和套装属性',
  `Attack_value` int(7) DEFAULT '0' COMMENT '攻击',
  `Defense_value` int(7) DEFAULT '0' COMMENT '御防',
  `Physical_value` int(7) DEFAULT '0' COMMENT '体力',
  `Agility_value` int(7) DEFAULT '0' COMMENT '捷敏',
  `Addition_attack_value` int(7) DEFAULT '0' COMMENT '伤害属性提升',
  `Addition_defense_value` int(7) DEFAULT '0' COMMENT '减伤属性提升',
  `Addition_physical_value` int(7) DEFAULT '0' COMMENT '生命值上限提升',
  `Addition_agility_value` int(7) DEFAULT '0' COMMENT '命中率提升',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_tenpay_ord`
-- ----------------------------
DROP TABLE IF EXISTS `ol_tenpay_ord`;
CREATE TABLE `ol_tenpay_ord` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `playersid` int(11) NOT NULL,
  `ord_no` varchar(100) NOT NULL COMMENT '订单号对应回调的out_trade_no',
  `ord_amt` decimal(11,3) NOT NULL COMMENT '支付金额 元(用户选择的金额)',
  `cashier_code` varchar(50) DEFAULT NULL COMMENT '支付方式 空财付通',
  `trade_no` varchar(20) DEFAULT NULL COMMENT '支付宝内部交易号',
  `buyer_email` varchar(100) DEFAULT NULL COMMENT '买家帐号',
  `gmt_create` varchar(50) DEFAULT NULL COMMENT '交易创建时间',
  `notify_time` int(11) DEFAULT NULL COMMENT '回调时间(时间戳)',
  `seller_id` varchar(50) DEFAULT NULL COMMENT '卖家ID',
  `total_fee` decimal(11,1) DEFAULT NULL COMMENT '支付金额 元',
  `gmt_payment` varchar(50) DEFAULT NULL COMMENT '付款时间，如未付款无此属性',
  `notify_id` varchar(50) DEFAULT NULL COMMENT '支付宝异步通知ID号,同一笔订单不会变',
  `use_coupon` varchar(5) DEFAULT NULL,
  `verify` tinyint(2) NOT NULL DEFAULT '0' COMMENT '单订验证 0创建订单 1支付成功 2为支付失败 3等待支付',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_tom_payinfo`
-- ----------------------------
DROP TABLE IF EXISTS `ol_tom_payinfo`;
CREATE TABLE `ol_tom_payinfo` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_tom_payinfo_error`
-- ----------------------------
DROP TABLE IF EXISTS `ol_tom_payinfo_error`;
CREATE TABLE `ol_tom_payinfo_error` (
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
--  Table structure for `ol_uc_client_payinfo`
-- ----------------------------
DROP TABLE IF EXISTS `ol_uc_client_payinfo`;
CREATE TABLE `ol_uc_client_payinfo` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `orderId` varchar(50) NOT NULL,
  `callbackInfo` varchar(50) NOT NULL,
  `processed` tinyint(1) NOT NULL DEFAULT '0',
  `createTime` int(11) NOT NULL,
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_uc_coin`
-- ----------------------------
DROP TABLE IF EXISTS `ol_uc_coin`;
CREATE TABLE `ol_uc_coin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `amount` int(8) NOT NULL,
  `result` varchar(10) DEFAULT NULL,
  `errmsg` varchar(200) DEFAULT NULL,
  `ucid` varchar(30) NOT NULL,
  `token` varchar(200) DEFAULT NULL,
  `trade_id` varchar(100) DEFAULT NULL,
  `add_info` varchar(30) DEFAULT NULL,
  `prod_nbr` int(8) DEFAULT NULL,
  `trade_time` datetime DEFAULT NULL,
  `prder_id` varchar(50) DEFAULT NULL,
  `prod_name` varchar(30) DEFAULT NULL,
  `sign` varchar(100) DEFAULT NULL,
  `type` int(2) DEFAULT '1' COMMENT '1充值',
  `status` int(1) DEFAULT '0' COMMENT '是否已兑换1兑换，0未处理',
  `level` int(4) DEFAULT NULL COMMENT '等级',
  `yb` int(8) DEFAULT NULL COMMENT '兑换元宝',
  PRIMARY KEY (`id`),
  KEY `ol_uc_coin_orderid_key` (`prder_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_uc_fill`
-- ----------------------------
DROP TABLE IF EXISTS `ol_uc_fill`;
CREATE TABLE `ol_uc_fill` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `result` varchar(2) NOT NULL,
  `error_code` varchar(5) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `recharge_amt` int(8) NOT NULL COMMENT '充值的Q点数量',
  `pay_amt` int(8) NOT NULL COMMENT '扣除的U点数量',
  `trade_id` varchar(50) NOT NULL,
  `trade_time` datetime NOT NULL,
  `sign` varchar(100) NOT NULL,
  `status` int(2) NOT NULL,
  `level` int(4) NOT NULL COMMENT '登记',
  `yb` int(8) NOT NULL COMMENT '兑换元宝数',
  PRIMARY KEY (`id`),
  KEY `order_key` (`order_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_uc_friends`
-- ----------------------------
DROP TABLE IF EXISTS `ol_uc_friends`;
CREATE TABLE `ol_uc_friends` (
  `ucid` int(11) NOT NULL,
  `fucid` int(11) NOT NULL,
  `realname` varchar(64) NOT NULL
) ENGINE=MEMORY DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- ----------------------------
--  Table structure for `ol_uc_log`
-- ----------------------------
DROP TABLE IF EXISTS `ol_uc_log`;
CREATE TABLE `ol_uc_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_uc_order`
-- ----------------------------
DROP TABLE IF EXISTS `ol_uc_order`;
CREATE TABLE `ol_uc_order` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `order_id` varchar(50) NOT NULL COMMENT '订单号',
  `user_id` varchar(30) NOT NULL COMMENT 'ucid',
  `create_time` int(11) NOT NULL COMMENT '充值时间',
  `card_no` varchar(30) NOT NULL COMMENT '卡号',
  `card_pwd` varchar(30) NOT NULL COMMENT '密码',
  `card_amt` int(4) NOT NULL COMMENT '面值',
  `rc_type` int(2) NOT NULL COMMENT '1：移动充值卡，2：联通充值卡',
  `status` int(2) NOT NULL DEFAULT '0' COMMENT '状态，是否处理',
  PRIMARY KEY (`id`),
  KEY `ol_uc_order_order_id_key` (`order_id`) USING BTREE,
  KEY `ol_uc_order_createTime_key` (`create_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_uc_order_notify`
-- ----------------------------
DROP TABLE IF EXISTS `ol_uc_order_notify`;
CREATE TABLE `ol_uc_order_notify` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `order_id` varchar(100) NOT NULL,
  `amount` int(4) NOT NULL,
  `ucid` varchar(30) NOT NULL,
  `create_time` int(11) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '1是成功，0是失败',
  PRIMARY KEY (`id`),
  KEY `ol_uc_order_notify_order_id_key` (`order_id`) USING BTREE,
  KEY `ol_uc_order_notify_create_time_key` (`create_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_uc_payinfo`
-- ----------------------------
DROP TABLE IF EXISTS `ol_uc_payinfo`;
CREATE TABLE `ol_uc_payinfo` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_uc_payinfo_error`
-- ----------------------------
DROP TABLE IF EXISTS `ol_uc_payinfo_error`;
CREATE TABLE `ol_uc_payinfo_error` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_user`
-- ----------------------------
DROP TABLE IF EXISTS `ol_user`;
CREATE TABLE `ol_user` (
  `userid` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `realname` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'uc用户名称',
  `password` varchar(64) NOT NULL,
  `email` varchar(64) NOT NULL,
  `client` tinyint(1) NOT NULL,
  `mobile` int(11) NOT NULL,
  `register_time` int(11) NOT NULL,
  `last_login_time` int(11) NOT NULL COMMENT '记录上次用户登录时间',
  `uzone_token` varchar(255) NOT NULL,
  `inviteid` int(11) NOT NULL,
  `sexid` int(4) NOT NULL,
  `status` int(4) NOT NULL,
  `is_reason` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否封禁',
  `reason_memo` text NOT NULL COMMENT '封禁原因',
  `reason_time` int(11) NOT NULL COMMENT '封禁时间',
  `nc` varchar(255) DEFAULT NULL,
  `isActivated` tinyint(1) NOT NULL DEFAULT '0' COMMENT '账户是否激活',
  `qd` varchar(64) DEFAULT '0' COMMENT '用户来源渠道',
  `register_date` date DEFAULT NULL COMMENT '用户注册日期',
  `kszc` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userid`),
  KEY `index_ol_user_username` (`username`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_vip_discount`
-- ----------------------------
DROP TABLE IF EXISTS `ol_vip_discount`;
CREATE TABLE `ol_vip_discount` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vip_level` tinyint(2) NOT NULL,
  `discount` decimal(6,3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_vip_money_log`
-- ----------------------------
DROP TABLE IF EXISTS `ol_vip_money_log`;
CREATE TABLE `ol_vip_money_log` (
  `playersid` int(11) NOT NULL,
  `insertTime` datetime NOT NULL,
  `money` decimal(10,3) NOT NULL,
  `ingot` int(11) NOT NULL,
  `orderid` varchar(50) NOT NULL,
  `seqnum` varchar(200) DEFAULT NULL,
  `special` varchar(30) DEFAULT '' COMMENT '保存类似tom充值这样的特殊处理过的渠道类型',
  KEY `ol_vip_money_log` (`playersid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_wborderid`
-- ----------------------------
DROP TABLE IF EXISTS `ol_wborderid`;
CREATE TABLE `ol_wborderid` (
  `intID` bigint(12) NOT NULL AUTO_INCREMENT,
  `ortime` int(11) NOT NULL,
  `playersid` int(11) NOT NULL,
  `sinauid` varchar(32) NOT NULL,
  PRIMARY KEY (`intID`),
  KEY `index_wborder_playersid` (`playersid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=993990856 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_wdjpayinfo`
-- ----------------------------
DROP TABLE IF EXISTS `ol_wdjpayinfo`;
CREATE TABLE `ol_wdjpayinfo` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `signType` char(50) NOT NULL,
  `sign` char(100) NOT NULL,
  `payTime` int(11) NOT NULL,
  `orderid` char(50) NOT NULL,
  PRIMARY KEY (`intID`),
  UNIQUE KEY `orderid` (`orderid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_wdjpayinfo_error`
-- ----------------------------
DROP TABLE IF EXISTS `ol_wdjpayinfo_error`;
CREATE TABLE `ol_wdjpayinfo_error` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `signType` char(50) NOT NULL,
  `sign` char(100) NOT NULL,
  `payTime` int(11) NOT NULL,
  `orderid` char(50) NOT NULL,
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_weibo_payinfo`
-- ----------------------------
DROP TABLE IF EXISTS `ol_weibo_payinfo`;
CREATE TABLE `ol_weibo_payinfo` (
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

-- ----------------------------
--  Table structure for `ol_yeepay_ord`
-- ----------------------------
DROP TABLE IF EXISTS `ol_yeepay_ord`;
CREATE TABLE `ol_yeepay_ord` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `playersid` int(11) NOT NULL COMMENT '家玩id',
  `type` varchar(200) NOT NULL COMMENT '业务类型 ChargeCardDirect为非银行卡',
  `memberId` varchar(11) NOT NULL COMMENT '户商编号 商户在易宝支付系统的唯一身份标识',
  `orderNo` varchar(50) NOT NULL COMMENT '订单号',
  `orderAmt` decimal(11,1) NOT NULL COMMENT '订单金额',
  `currency` varchar(10) DEFAULT 'CNY' COMMENT '行银卡支付所需',
  `descr` varchar(30) NOT NULL COMMENT '品商描述',
  `extInfo` varchar(50) NOT NULL COMMENT '扩展信息 userid',
  `cardAmt` int(11) DEFAULT NULL COMMENT '充值卡面额 充值卡必须',
  `cardNo` varchar(300) DEFAULT NULL COMMENT '充值卡号 充值卡必须',
  `cardPwd` varchar(300) DEFAULT NULL COMMENT '充值卡密码 充值卡必须',
  `frpId` varchar(10) NOT NULL COMMENT '支付通道编码',
  `hmac` varchar(32) NOT NULL COMMENT '签名数据',
  `verify` tinyint(2) NOT NULL DEFAULT '0' COMMENT '单订验证 0建立订单 1为验证成功 2为验证失败',
  `errcode` varchar(10) DEFAULT NULL COMMENT '误错代码(非银行卡支付并且支付失败)',
  `trxId` varchar(50) DEFAULT NULL COMMENT '易宝支付交易流水号(银行卡支付)',
  `ordDate` int(11) NOT NULL,
  `toplayersid` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_yxbar_payinfo`
-- ----------------------------
DROP TABLE IF EXISTS `ol_yxbar_payinfo`;
CREATE TABLE `ol_yxbar_payinfo` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `usercode` varchar(64) NOT NULL,
  `tradnum` varchar(64) NOT NULL,
  `account` varchar(64) NOT NULL,
  `servername` varchar(64) NOT NULL,
  `amt` int(10) NOT NULL,
  `checking` varchar(64) NOT NULL,
  `time` int(10) NOT NULL,
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_yxbar_payinfo_error`
-- ----------------------------
DROP TABLE IF EXISTS `ol_yxbar_payinfo_error`;
CREATE TABLE `ol_yxbar_payinfo_error` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `usercode` varchar(64) NOT NULL,
  `tradnum` varchar(64) NOT NULL,
  `account` varchar(64) NOT NULL,
  `servername` varchar(64) NOT NULL,
  `amt` int(10) NOT NULL,
  `checking` varchar(64) NOT NULL,
  `time` int(10) NOT NULL,
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_yxh_payinfo`
-- ----------------------------
DROP TABLE IF EXISTS `ol_yxh_payinfo`;
CREATE TABLE `ol_yxh_payinfo` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL,
  `change_id` char(10) NOT NULL,
  `money` decimal(8,3) NOT NULL,
  `hash` char(128) NOT NULL,
  `ad_key` char(32) NOT NULL,
  `object` char(64) NOT NULL,
  `orderTime` int(11) NOT NULL,
  PRIMARY KEY (`intID`),
  UNIQUE KEY `change_id` (`change_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_zg_log`
-- ----------------------------
DROP TABLE IF EXISTS `ol_zg_log`;
CREATE TABLE `ol_zg_log` (
  `intID` bigint(12) NOT NULL AUTO_INCREMENT,
  `playersid` bigint(12) NOT NULL DEFAULT '0' COMMENT '玩家ID',
  `zgz` int(8) NOT NULL DEFAULT '0' COMMENT '获取或失去的战功值',
  `zglx` tinyint(2) NOT NULL DEFAULT '0' COMMENT '战功类型',
  `zgtime` int(11) NOT NULL DEFAULT '0' COMMENT '战功变更时间',
  `pname` varchar(64) NOT NULL COMMENT '角色名称',
  `tuid` bigint(12) NOT NULL DEFAULT '0' COMMENT '显示城池ID',
  PRIMARY KEY (`intID`),
  KEY `playersid` (`playersid`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_zyd`
-- ----------------------------
DROP TABLE IF EXISTS `ol_zyd`;
CREATE TABLE `ol_zyd` (
  `yd_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `yd_type` tinyint(1) NOT NULL COMMENT '野地类型',
  `yd_level` tinyint(1) NOT NULL COMMENT '野地等级',
  `playersid` bigint(12) NOT NULL,
  `nickname` varchar(50) NOT NULL COMMENT '所有者名称',
  `zlpid` bigint(12) NOT NULL COMMENT '占领资源者ID',
  `zlpname` varchar(50) NOT NULL COMMENT '玩家名称',
  `zlwjid` bigint(12) NOT NULL COMMENT '占领资源点武将ID',
  `zlwjmc` varchar(10) NOT NULL COMMENT '占领武将名称',
  `ydscsj` date NOT NULL COMMENT '野地生成时间',
  `scsysj` int(10) NOT NULL COMMENT '上次资源收益时间',
  `zlsj` int(10) NOT NULL COMMENT '占领时间',
  `zydiid` varchar(20) NOT NULL COMMENT '资源点图标',
  `wjmc` varchar(10) NOT NULL COMMENT '武将名称',
  `ldsl` int(4) NOT NULL COMMENT '掠夺资源数量',
  `ldzyid` int(8) NOT NULL COMMENT '掠夺资源ID',
  `ldbhcs` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`yd_id`),
  KEY `yd_pid` (`playersid`) USING BTREE,
  KEY `zlpid` (`zlpid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ol_zyd_qxzl`
-- ----------------------------
DROP TABLE IF EXISTS `ol_zyd_qxzl`;
CREATE TABLE `ol_zyd_qxzl` (
  `yd_id` tinyint(2) NOT NULL AUTO_INCREMENT,
  `yd_level` int(2) NOT NULL,
  `yd_type` tinyint(1) NOT NULL,
  `xm` varchar(10) NOT NULL COMMENT 'æ­¦å°†å§“å',
  `sex` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0ç”·æ€§1å¥³æ€§',
  `zy` tinyint(1) NOT NULL COMMENT 'èŒä¸š',
  `gj` int(4) NOT NULL,
  `fy` int(4) NOT NULL,
  `tl` int(4) NOT NULL,
  `mj` int(4) NOT NULL,
  `kssj` int(2) NOT NULL COMMENT 'æ´»åŠ¨å¼€å§‹æ—¶é—´',
  `zlpid` bigint(12) NOT NULL,
  `zlpname` varchar(50) NOT NULL,
  `zlwjid` bigint(12) NOT NULL,
  `zlwjmc` varchar(10) NOT NULL,
  `zlsj` int(11) NOT NULL,
  `zlmc` varchar(50) NOT NULL COMMENT 'é€é¹¿åç§°',
  `djmc` varchar(20) NOT NULL COMMENT 'å¤§å¥–åç§°',
  `djnr` varchar(255) NOT NULL COMMENT 'å¤§å¥–å†…å®¹',
  `zljj` varchar(255) NOT NULL COMMENT 'é€é¹¿ç®€ä»‹',
  `zldd` varchar(12) NOT NULL COMMENT 'æ´»åŠ¨åœ°ç‚¹',
  `dj` tinyint(2) NOT NULL,
  `avatar` char(20) NOT NULL,
  PRIMARY KEY (`yd_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ol_zyd_zldj_his`
-- ----------------------------
DROP TABLE IF EXISTS `ol_zyd_zldj_his`;
CREATE TABLE `ol_zyd_zldj_his` (
  `intID` int(11) NOT NULL AUTO_INCREMENT,
  `playersid` bigint(12) NOT NULL,
  `zydid` bigint(12) NOT NULL,
  `hjsj` int(11) NOT NULL,
  `dateTime` date NOT NULL,
  `djlx` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1逐鹿大奖0得分王大奖',
  PRIMARY KEY (`intID`),
  KEY `zydj_pid` (`playersid`) USING BTREE,
  KEY `zydj_date` (`dateTime`) USING BTREE,
  KEY `djlx` (`djlx`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- ----------------------------
--  Table structure for `ol_zyd_zlsjlog`
-- ----------------------------
DROP TABLE IF EXISTS `ol_zyd_zlsjlog`;
CREATE TABLE `ol_zyd_zlsjlog` (
  `intID` bigint(12) NOT NULL AUTO_INCREMENT,
  `zydid` tinyint(1) NOT NULL COMMENT '逐鹿点ID',
  `zlpid` bigint(12) NOT NULL COMMENT '占领玩家ID',
  `zlpname` varchar(50) NOT NULL COMMENT '占领玩家名称',
  `zlsj` int(11) NOT NULL COMMENT '占领时长',
  `zlrq` date NOT NULL COMMENT '占领日期',
  PRIMARY KEY (`intID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  View definition for `ol_v_uc_eden_pay`
-- ----------------------------
DROP VIEW IF EXISTS `ol_v_uc_eden_pay`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `ol_v_uc_eden_pay` AS select `o`.`user_id` AS `ucid`,`o`.`create_time` AS `createTime`,'chongzhika' AS `payWay`,if(isnull(`f`.`pay_amt`),-(1),(`f`.`pay_amt` / 10)) AS `amount`,`o`.`order_id` AS `orderId`,if(isnull(`f`.`status`),0,`f`.`status`) AS `status`,`f`.`error_code` AS `errorInfo` from (`ol_uc_order` `o` left join `ol_uc_fill` `f` on((`o`.`order_id` = `f`.`order_id`))) union select `n`.`ucid` AS `ucid`,`n`.`create_time` AS `createTime`,'u dian' AS `payWay`,if(isnull(`c`.`amount`),-(1),(`c`.`amount` / 10)) AS `amount`,`n`.`order_id` AS `orderId`,if(isnull(`c`.`status`),0,`c`.`status`) AS `status`,`c`.`errmsg` AS `errorInfo` from (`ol_uc_order_notify` `n` left join `ol_uc_coin` `c` on((`c`.`ucid` = `n`.`ucid`)));

-- ----------------------------
--  Records 
-- ----------------------------
INSERT INTO `ol_accepted_quests` VALUES ('1','5001001','1','0','0','1382730379','','0','1','0','1'), ('2','5002030','1','0','0','1382730379','','0','1','0','1'), ('3','5002031','1','0','0','1382730379','','0','1','0','1'), ('4','5002032','1','0','0','1382730379','','0','1','0','1'), ('5','5002033','1','0','0','1382730379','','0','1','0','1');
INSERT INTO `ol_app_productInfo` VALUES ('1','å°å…ƒå®åŒ…','yb1','2','1','10','1','6'), ('2','ä¸­å…ƒå®åŒ…','yb2','5','1','25','2','6'), ('3','å¤§å…ƒå®åŒ…','yb3','10','1','50','3','6'), ('4','è¶…å¤§å…ƒå®åŒ…','yb4','30','1','150','4','6'), ('1','10å…ƒå®','yb1','1','1','10','100000000','7'), ('2','50å…ƒå®','yb2','5','1','50','100000001','7'), ('3','100å…ƒå®','yb3','10','1','100','100000002','7'), ('4','500å…ƒå®','yb4','50','1','500','100000003','7'), ('5','1000å…ƒå®','yb5','100','1','1000','100000004','7'), ('6','5000å…ƒå®','yb6','500','1','5000','100000005','7'), ('1','10å…ƒå®','yb1','1','1','10','200000001','8'), ('2','100å…ƒå®','yb2','10','1','100','200000002','8'), ('3','200å…ƒå®','yb3','20','1','200','200000003','8'), ('4','500å…ƒå®','yb4','50','1','500','200000004','8'), ('5','1000å…ƒå®','yb5','100','1','1000','200000005','8'), ('6','3000å…ƒå®','yb6','300','1','3000','200000006','8'), ('1','10å…ƒå®','yb1','1','1','10','300000001','9'), ('2','100å…ƒå®','yb2','10','1','100','300000002','9'), ('3','200å…ƒå®','yb3','20','1','200','300000003','9'), ('4','500å…ƒå®','yb4','50','1','500','300000004','9'), ('5','1000å…ƒå®','yb5','100','1','1000','300000005','9'), ('6','3000å…ƒå®','yb6','300','1','3000','300000006','9'), ('1','10å…ƒå®','yb1','1','1','10','400000001','10'), ('2','20å…ƒå®','yb2','2','1','20','400000002','10'), ('3','50å…ƒå®','yb3','5','1','50','400000003','10'), ('4','100å…ƒå®','yb4','10','1','100','400000004','10'), ('5','200å…ƒå®','yb5','20','1','200','400000005','10'), ('6','300å…ƒå®','yb6','30','1','300','400000006','10'), ('60gold','60å…ƒå®','yb1','6','1','60','531071160','1'), ('300gold','300å…ƒå®','yb2','30','1','300','531073447','1'), ('2500gold','2500å…ƒå®','yb4','198','1','2500','531073957','1'), ('5500gold','5500å…ƒå®','yb5','328','1','5500','531074333','1'), ('11000gold','11000å…ƒå®','yb6','648','1','11000','531074558','1'), ('1000gold','1000å…ƒå®','yb3','88','1','1000','531211526','1'), ('60goldHD','60å…ƒå®','yb1','6','1','60','535737828','2'), ('300goldHD','300å…ƒå®','yb2','30','1','300','535738125','2'), ('1000goldHD','1000å…ƒå®','yb3','88','1','1000','535738462','2'), ('2500goldHD','2500å…ƒå®','yb4','198','1','2500','535738538','2'), ('5500goldHD','5500å…ƒå®','yb5','328','1','5500','535739194','2'), ('11000goldHD','11000å…ƒå®','yb6','648','1','11000','535739493','2'), ('com.jofgame.nizhuanfengyun.120','120å…ƒå¯¶','yb1','60','1','120','645440588','3'), ('com.jofgame.nizhuanfengyun.2200','2200å…ƒå¯¶','yb4','990','1','2200','645441636','3'), ('com.jofgame.nizhuanfengyun.310','310å…ƒå¯¶','yb2','150','1','310','645442037','3'), ('com.jofgame.nizhuanfengyun.3600','3600å…ƒå¯¶','yb5','1490','1','3600','645443261','3'), ('com.jofgame.nizhuanfengyun.7500','7500å…ƒå¯¶','yb6','2990','1','7500','645443643','3'), ('com.jofgame.nizhuanfengyun.970','970å…ƒå¯¶','yb3','450','1','970','645443697','3'), ('com.sothinksoft.zjsh.iap.60','60å…ƒå®','yb1','6','1','60','657678368','5'), ('com.sothinksoft.zjsh.iap.310','310å…ƒå®','yb2','30','1','310','657678484','5'), ('com.sothinksoft.zjsh.iap.950','950å…ƒå®','yb3','88','1','950','657678551','5'), ('com.sothinksoft.zjsh.iap.6500','6500å…ƒå®','yb6','488','1','6500','657679301','5'), ('com.sothinksoft.zjsh.iap.2200','2200å…ƒå®','yb4','198','1','2200','657679554','5'), ('com.sothinksoft.zjsh.iap.4000','4000å…ƒå®','yb5','328','1','4000','657680073','5'), ('0901230620','10å…ƒå®','yb1','1','1','1','901230620','4'), ('0901230623','100000å…ƒå®','yb6','10000','1','100000','901230623','4');
INSERT INTO `ol_bck_log` VALUES ('1','{\"request_url\":\"/app.php?option=user&task=register&qd=bddk1_1&ssn=1&token=bGVkH5XjjqXreVs%2B7Xo8WGtHQTmMyyZ1syYnaL5joVxmRZ%2FLMxcVKTwjZzUwGXImiHJG%2BxEmhPAMtu819jTW5bSw%2FuerrysVQhpiUY5OyOaEQYfOoA7jbOoiOjGhAJVQiFc6hmsJb69aZ1Th5Aw%2B1WAeUSTdRpSzmsAJkn9B5Vc%3D&apkvs=1.36&userId=4bf764883dad11e3bd95005056a3001f&bs=1&client=2&r=465\",\"create_time\":\"1382730372\",\"level\":-1,\"userid\":\"1\",\"result\":{\"czdz\":\"http://119.79.232.99/app.php\",\"sydz\":\"http://hut.jofgame.com\",\"cv\":\"1.31\",\"zfbdz\":\"http://117.135.138.248:8080/components/alipay_self/index.php\",\"paycardurl\":\"http://117.135.139.30:17000/index.html\",\"iosczdz\":\"\",\"kfcz\":1,\"fkdz\":\"http://119.97.226.138:808/new_feedback/send_feedback/feedback.php\",\"status\":0,\"userId\":1,\"inviteId\":\"\",\"servers\":\"\",\"loginKey\":\"34bf764883dad11e3bd95005056a3001f20526aca84d38d0\",\"jsxx\":[{\"userId\":1}],\"realname\":\"u77bfu5982u84c9\",\"xyh\":1,\"rsn\":1,\"kszc\":0,\"gv\":\"1.0\",\"AppStoreURL\":\"itunes.com/apps/streetfighteriicollection\",\"fwqdm\":\"hud\",\"tips\":\"\",\"tompay\":\"http://hud.jofgame.com:9000/xsyd/ucpay/tompay.php\",\"vipmode\":2,\"helpdocumenturl\":\"http://q.jofgame.com/helpcenter_V1.37/index.html\",\"sgpayurl\":\"\",\"brpayurl\":\"http://117.135.138.248:8080/ucpay/brpay.php\",\"email\":\"Mobile7@unalis.com.tw\",\"jfpay\":\"http://192.168.1.202:81/ucpay/jforder.php\",\"brzf\":\"http://www.baoruan.com/nested/account/login?cid=852&uid=4bf764883dad11e3bd95005056a3001f&type=qjsh&token=c6ceaec43425315a57cd49c141b3e6ca&notify_url=aHR0cDovLzE5Mi4xNjguMS4yMDI6ODEvdWNwYXkvYnJwYXlfMS5waHA/dXNlcmlkPTEmc2VydmVyaWQ9aHVk\",\"sggg\":\"0,0,0\",\"zfzx\":\"http://ucs.jofgame.com/paycenter/index.php\",\"krpayurl\":\"http://192.168.1.202:81/pay/hfpay.php\",\"lbhd\":0,\"ltdz\":\"http://ucs.jofgame.com/authserver/forum/luntan.php\",\"_DEBUG\":1},\"serv_add\":\"192.168.1.202\",\"run_time\":0.10383009910583,\"sqltimes\":6}'), ('2','{\"request_url\":\"/app.php?option=role&task=login&qd=bddk1_1&ssn=2&loginKey=34bf764883dad11e3bd95005056a3001f20526aca84d38d0&userId=1&bs=1&r=678\",\"create_time\":\"1382730373\",\"level\":-1,\"userid\":\"1\",\"result\":{\"status\":0,\"loginKey\":\"34bf764883dad11e3bd95005056a3001f20526aca84d38d0\",\"userid\":\"1\",\"scdl\":1,\"rsn\":2},\"serv_add\":\"192.168.1.202\",\"run_time\":0.10604190826416,\"sqltimes\":1}'), ('3','{\"request_url\":\"/app.php?option=role&sex=0&task=createRole&nickname=stefan&ssn=3&jcid=0&loginKey=34bf764883dad11e3bd95005056a3001f20526aca84d38d0&userId=1&jcmgc=1&bs=1&r=870\",\"create_time\":\"1382730379\",\"level\":1,\"userid\":\"1\",\"result\":{\"status\":0,\"jssycs\":0,\"loginKey\":\"34bf764883dad11e3bd95005056a3001f20526aca84d38d0\",\"roleName\":\"stefan\",\"sexId\":0,\"jy\":0,\"jl\":13,\"level\":1,\"lsdj\":0.01,\"xjjy\":1150,\"bbgs\":30,\"xlwkqyb\":1,\"yb\":0,\"tq\":25000,\"yp\":100,\"jlbhyb\":20,\"jltqhyp\":[{\"jl\":1,\"tq\":100,\"yp\":1},{\"jl\":5,\"tq\":500,\"yp\":5},{\"jl\":8,\"tq\":800,\"yp\":8},{\"jl\":15,\"tq\":1500,\"yp\":15}],\"zbqhsx\":6,\"jlhf\":5,\"vip\":0,\"pid\":1,\"kzwjsx\":3,\"zljbxz\":5,\"zcyp\":5,\"qrlist\":[{\"day\":1,\"zt\":2,\"dljl\":[{\"mc\":\"u5927u94dcu94b1u5305\",\"sl\":1,\"iid\":\"Ico093\",\"xyd\":2}]},{\"day\":2,\"zt\":0,\"dljl\":[{\"mc\":\"u5927u94dcu94b1u5305\",\"sl\":1,\"iid\":\"Ico093\",\"xyd\":2},{\"mc\":\"u91d1u521au77f3\",\"sl\":1,\"iid\":\"Ico000\",\"xyd\":0}]},{\"day\":3,\"zt\":0,\"dljl\":[{\"mc\":\"u5c0fu5f3au5316u5305\",\"sl\":1,\"iid\":\"Ico094\",\"xyd\":0},{\"mc\":\"u77ffu5de5u9504\",\"sl\":2,\"iid\":\"Ico073\",\"xyd\":0}]},{\"day\":4,\"zt\":0,\"dljl\":[{\"mc\":\"u5927u94dcu94b1u5305\",\"sl\":2,\"iid\":\"Ico093\",\"xyd\":2},{\"mc\":\"u91d1u521au77f3\",\"sl\":1,\"iid\":\"Ico000\",\"xyd\":0},{\"mc\":\"u94f6u7968\",\"sl\":20,\"iid\":\"icon_yp\",\"xyd\":0}]},{\"day\":5,\"zt\":0,\"dljl\":[{\"mc\":\"u7384u94c1\",\"sl\":1,\"iid\":\"Ico001\",\"xyd\":0},{\"mc\":\"u7effu6b66u9b42\",\"sl\":5,\"iid\":\"Ico072\",\"xyd\":2},{\"mc\":\"u94f6u7968\",\"sl\":30,\"iid\":\"icon_yp\",\"xyd\":0}]},{\"day\":6,\"zt\":0,\"dljl\":[{\"mc\":\"u5927u94dcu94b1u5305\",\"sl\":3,\"iid\":\"Ico093\",\"xyd\":2},{\"mc\":\"u5c0fu5f3au5316u5305\",\"sl\":1,\"iid\":\"Ico094\",\"xyd\":0},{\"mc\":\"u84ddu6b66u9b42\",\"sl\":3,\"iid\":\"Ico072\",\"xyd\":3},{\"mc\":\"u94f6u7968\",\"sl\":35,\"iid\":\"icon_yp\",\"xyd\":0}]},{\"day\":7,\"zt\":0,\"dljl\":[{\"mc\":\"u5927u94dcu94b1u5305\",\"sl\":5,\"iid\":\"Ico093\",\"xyd\":2},{\"mc\":\"u5c0fu5f3au5316u5305\",\"sl\":2,\"iid\":\"Ico094\",\"xyd\":0},{\"mc\":\"u7d2bu6b66u9b42\",\"sl\":2,\"iid\":\"Ico072\",\"xyd\":4},{\"mc\":\"u94f6u7968\",\"sl\":40,\"iid\":\"icon_yp\",\"xyd\":0},{\"mc\":\"u58f0u671bu5361\",\"sl\":1,\"iid\":\"Ico083\",\"xyd\":0}]}],\"qrjlts2\":1,\"qrjllq2\":0,\"jlsx\":13,\"jlzf\":2,\"dqjw\":\"u5175u5352\",\"jwid\":1,\"xjjw\":\"u5c6fu957f\",\"dqjc\":0,\"xjjc\":2,\"swz\":0,\"swsx\":300,\"jsyp\":5,\"generals\":[{\"gid\":1,\"scg\":0,\"px\":1,\"tx\":\"YX016\",\"xm\":\"u6731u8d35\",\"jb\":1,\"zy\":2,\"wjxb\":1,\"tf\":20,\"jj\":1,\"zysx\":\"0.3,0.2,0.3,0.2,80\",\"gj\":30,\"fy\":20,\"tl\":30,\"mj\":20,\"jy\":0,\"xjjy\":575,\"gjp\":0,\"fyp\":0,\"tlp\":0,\"mjp\":0,\"pysx\":6,\"smsx\":300,\"smz\":300,\"sfll\":0,\"sfzxl\":0,\"zb1\":0,\"zb2\":0,\"zb3\":0,\"zb4\":0,\"czzt\":1,\"czzt2\":1,\"llyl\":1,\"llcs\":0,\"lshf\":0.01,\"tuid\":0},{\"gid\":2,\"scg\":0,\"px\":2,\"tx\":\"YX015\",\"xm\":\"u5f20u987a\",\"jb\":1,\"zy\":3,\"wjxb\":1,\"tf\":18,\"jj\":1,\"zysx\":\"0.25,0.15,0.25,0.35,80\",\"gj\":25,\"fy\":15,\"tl\":25,\"mj\":35,\"jy\":0,\"xjjy\":575,\"gjp\":0,\"fyp\":0,\"tlp\":0,\"mjp\":0,\"pysx\":6,\"smsx\":250,\"smz\":250,\"sfll\":0,\"sfzxl\":0,\"zb1\":0,\"zb2\":0,\"zb3\":0,\"zb4\":0,\"czzt\":1,\"czzt2\":1,\"llyl\":1,\"llcs\":0,\"lshf\":0.01,\"tuid\":0},{\"gid\":3,\"scg\":0,\"px\":3,\"tx\":\"YX011\",\"xm\":\"u6b66u5927u90ce\",\"jb\":1,\"zy\":1,\"wjxb\":1,\"tf\":16,\"jj\":1,\"zysx\":\"0.25,0.25,0.3,0.2,80\",\"gj\":24,\"fy\":24,\"tl\":29,\"mj\":20,\"jy\":0,\"xjjy\":575,\"gjp\":0,\"fyp\":0,\"tlp\":0,\"mjp\":0,\"pysx\":6,\"smsx\":290,\"smz\":290,\"sfll\":0,\"sfzxl\":0,\"zb1\":0,\"zb2\":0,\"zb3\":0,\"zb4\":0,\"czzt\":1,\"czzt2\":1,\"llyl\":1,\"llcs\":0,\"lshf\":0.01,\"tuid\":0}],\"nextUpdate\":21599,\"jlsl\":1,\"jcclhl\":[{\"jgs\":10,\"jgt\":10,\"llp\":10,\"jlx\":10,\"fyr\":10}],\"sjclhl\":[{\"jb\":5,\"sc\":5,\"tk\":5,\"tt\":5,\"mc\":5}],\"sjyp\":5,\"hydyp\":5,\"scdjsx\":12,\"djtdjsx\":12,\"jgdjsx\":12,\"lddjsx\":12,\"tjpdjsx\":12,\"list\":[{\"wid\":9,\"djid\":18522,\"num\":1,\"qhcs\":0,\"lx\":2,\"st\":87,\"iconid\":\"Ico056\",\"jg\":0,\"mc\":\"u65e7u94dcu94a5u5319\",\"sm\":\"u83b7u5f97u65b9u5f0fuff1au5267u60c5u83b7u5f97uff0cu4f5cu7528uff1au5f00u542fu9633u8c37u53bfu5173u5361\",\"zy\":0,\"jb\":1,\"xyd\":0,\"kysy\":1,\"nfdz\":0,\"nfqh\":0,\"hclx\":0},{\"wid\":7,\"djid\":10001,\"num\":10,\"qhcs\":0,\"lx\":1,\"st\":6,\"iconid\":\"Ico000\",\"jg\":0,\"mc\":\"u91d1u521au77f3\",\"sm\":\"u5728u94c1u5320u94fau4e2du4f7fu7528uff0cu7528u4e8eu5f3au5316u88c5u5907u3002u4e3bu8981u4eceu6316u77ffu548cu63a2u5b9du4e2du83b7u5f97\",\"zy\":0,\"jb\":1,\"xyd\":0,\"nfdz\":0,\"nfqh\":0,\"hclx\":0},{\"wid\":6,\"djid\":10032,\"num\":7,\"qhcs\":0,\"lx\":1,\"st\":3,\"iconid\":\"Ico009\",\"jg\":5,\"mc\":\"u77f3u6750\",\"sm\":\"u5347u7ea7u201cu9152u9986u201du5efau7b51u65f6u6d88u8017uff0cu901au8fc7u597du53cbu9001u793cu83b7u5f97\",\"zy\":0,\"jb\":1,\"xyd\":0,\"nfdz\":0,\"nfqh\":0,\"hclx\":0},{\"wid\":3,\"djid\":10033,\"num\":7,\"qhcs\":0,\"lx\":1,\"st\":4,\"iconid\":\"Ico010\",\"jg\":5,\"mc\":\"u6728u6750\",\"sm\":\"u5347u7ea7u201cu9886u5730u201du5efau7b51u65f6u6d88u8017uff0cu901au8fc7u597du53cbu9001u793cu83b7u5f97\",\"zy\":0,\"jb\":1,\"xyd\":0,\"nfdz\":0,\"nfqh\":0,\"hclx\":0},{\"wid\":5,\"djid\":10034,\"num\":7,\"qhcs\":0,\"lx\":1,\"st\":5,\"iconid\":\"Ico011\",\"jg\":5,\"mc\":\"u9676u571f\",\"sm\":\"u5347u7ea7u201cu70b9u5c06u53f0u201du5efau7b51u65f6u6d88u8017uff0cu901au8fc7u597du53cbu9001u793cu83b7u5f97\",\"zy\":0,\"jb\":1,\"xyd\":0,\"nfdz\":0,\"nfqh\":0,\"hclx\":0},{\"wid\":4,\"djid\":10035,\"num\":7,\"qhcs\":0,\"lx\":1,\"st\":11,\"iconid\":\"Ico012\",\"jg\":5,\"mc\":\"u94c1u77ff\",\"sm\":\"u5347u7ea7u201cu94c1u5320u94fau201du5efau7b51u65f6u6d88u8017uff0cu901au8fc7u597du53cbu9001u793cu83b7u5f97\",\"zy\":0,\"jb\":1,\"xyd\":0,\"nfdz\":0,\"nfqh\":0,\"hclx\":0},{\"wid\":2,\"djid\":10036,\"num\":7,\"qhcs\":0,\"lx\":1,\"st\":12,\"iconid\":\"Ico013\",\"jg\":5,\"mc\":\"u7ee2u5e03\",\"sm\":\"u5347u7ea7u201cu5e02u573au201du5efau7b51u65f6u6d88u8017uff0cu901au8fc7u597du53cbu9001u793cu83b7u5f97\",\"zy\":0,\"jb\":1,\"xyd\":0,\"nfdz\":0,\"nfqh\":0,\"hclx\":0},{\"wid\":8,\"djid\":10040,\"num\":10,\"qhcs\":0,\"lx\":1,\"st\":106,\"iconid\":\"Ico001\",\"jg\":5,\"mc\":\"u7384u94c1\",\"sm\":\"u953bu9020u88c5u5907u65f6u9700u8981u7684u7269u54c1u3002u901au8fc7u6253u64c2u53f0u83b7u5f97\",\"zy\":0,\"jb\":1,\"xyd\":0,\"nfdz\":0,\"nfqh\":0,\"hclx\":0},{\"wid\":1,\"djid\":20075,\"num\":1,\"qhcs\":0,\"lx\":2,\"st\":60,\"iconid\":\"Ico015\",\"jg\":0,\"mc\":\"1u7ea7u6210u957fu793cu5305\",\"sm\":\"1u7ea7u53efu6253u5f00uff0cu6253u5f00u53efu83b7u5f97uff1au94dcu94b1X2000u3001u94f6u7968X5u3001u5c0fu519bu7caeu5305X1\",\"zy\":0,\"jb\":1,\"xyd\":3,\"kysy\":1,\"nfdz\":0,\"nfqh\":0,\"hclx\":0}],\"gv\":\"1.0\",\"lscc\":null,\"ltjf\":null,\"msglist\":\"u5bb9u4e0du4e0bu6211uff0cu8bf4u660eu4f60u5fc3u80f8u72edu9698;u79c3u9a74uff0cu6562u8ddfu8d2bu9053u62a2u5e08u592a;u6c42u8650u3001u6c42u6307u6559;u4f60u8981u8bb0u4f4fuff0cu6211u6253u4f60u662fu4e3au4e86u4f60u597d;u522bu7740u6025uff0cu5c4eu58f3u90ceu4e5fu4f1au6709u6625u5929;u997fu72fcu592au591auff0cu7b28u7f8au660eu663eu4e0du591fu7528u4e86\",\"qzcs\":5,\"qzyp\":0,\"tqzzl\":0.1,\"frd\":80,\"tjhf\":\"20,0,150,50,799,200\",\"ct\":30000,\"xwzt\":\"00000000000000000000000000000000\",\"srvsj\":\"1382730379\",\"zllist\":[{\"id\":1,\"mc\":\"u00e5u0081u2021u00e6u009du017du00e9u20acu00b5u00e5u2030u00aau00e5u00beu201e\",\"time\":12,\"type\":2},{\"id\":2,\"mc\":\"u00e9u2020u2030u00e5u2026u00a5u00e9u201du20acu00e9u2021u2018u00e5u00b8u0090\",\"time\":13,\"type\":4},{\"id\":3,\"mc\":\"u00e5u00a4u00a7u00e9u2014u00b9u00e9u2021u017du00e7u0152u00aau00e6u017eu2014\",\"time\":17,\"type\":3},{\"id\":4,\"mc\":\"u00e6u2122u00bau00e5u008fu2013u00e7u201du0178u00e8u00beu00b0u00e7u00bau00b2\",\"time\":18,\"type\":4},{\"id\":5,\"mc\":\"u00e6u00b4u00bbu00e6u008du2030u00e5u008fu00b2u00e6u2013u2021u00e6u0081u00ad\",\"time\":19,\"type\":3},{\"id\":6,\"mc\":\"u00e6u00b1u0178u00e5u00b7u017eu00e5u0160u00abu00e6u00b3u2022u00e5u0153u00ba\",\"time\":20,\"type\":1},{\"id\":7,\"mc\":\"u00e5u2021u00bfu00e8u02c6u00b9u00e6u201cu2019u00e9u00abu02dcu00e4u00bfu2026\",\"time\":21,\"type\":5}],\"qzyb\":10,\"qztq\":10000,\"jcbl\":50,\"jcblyb\":20,\"yjblyb\":1500,\"pyybhf\":[5,20,40,120],\"jctqhf\":30000,\"bxzt\":\"00000\",\"dqhyd\":0,\"djhyd\":[5,30,60,100,150],\"mbTitle\":\"u521du5165u6c34u6d52\",\"mbiid\":\"mb001\",\"ykjbrl\":960,\"ykjc\":0,\"xjykjc\":10,\"tstj\":[0,10],\"dwsszl\":8,\"tgzd\":1,\"rsn\":3},\"serv_add\":\"192.168.1.202\",\"run_time\":0.24335718154907,\"sqltimes\":55}'), ('4','{\"request_url\":\"/app.php?ssn=4&option=city&task=getCityInfo&more=1&loginKey=34bf764883dad11e3bd95005056a3001f20526aca84d38d0&userId=1&r=443\",\"create_time\":\"1382730675\",\"level\":1,\"userid\":\"1\",\"result\":{\"status\":0,\"oct\":2,\"gbnr\":\"\",\"xrzsl\":0,\"tq\":25000,\"jl\":13,\"jzzt\":\"2,3,0,0,0,0\",\"ykccs\":0,\"kscd\":0,\"kczcs\":10,\"sccd\":0,\"binfo\":[{\"jzdj\":1,\"jzid\":1,\"sfmj\":0,\"wjjb\":4,\"sjtq\":200,\"zyid\":1,\"djid\":10036,\"zysl\":2},{\"jzdj\":\"s\",\"jzid\":2,\"sfmj\":0,\"wjjb\":12,\"sjtq\":200,\"zyid\":2,\"djid\":10033,\"zysl\":2},{\"jzdj\":\"s\",\"jzid\":3,\"sfmj\":0,\"wjjb\":5,\"sjtq\":200,\"zyid\":3,\"djid\":10035,\"zysl\":2},{\"jzdj\":1,\"jzid\":4,\"sfmj\":0,\"wjjb\":6,\"sjtq\":200,\"zyid\":4,\"djid\":10032,\"zysl\":2},{\"jzdj\":1,\"jzid\":5,\"sfmj\":0,\"wjjb\":5,\"sjtq\":200,\"zyid\":5,\"djid\":10034,\"zysl\":2}],\"yb\":0,\"yp\":100,\"vip\":0,\"xlysl\":0,\"scljtq\":960,\"zg\":0,\"ltjl\":\"\",\"ysl\":0,\"jq\":0,\"gv\":\"1.0\",\"rsn\":4},\"serv_add\":\"192.168.1.202\",\"run_time\":0.020806074142456,\"sqltimes\":3}'), ('5','{\"request_url\":\"/app.php?option=user&task=register&qd=bddk1_1&ssn=1&token=tuAJznfzJXvQ9CuDqfYhJGeSV4hKUGMXDFj%2BzQ%2BC9nn0QNRfgmGF2P5SfYGFQMEmBxNp%2FcEjdWhmVpKTkXRyLPeQFE7md2dDSsrz0N5B8FmTQvM7pyeTCMnsmfSMzKS54zJ%2FDXeSWDL1yOrwh05i2PM92bsha3Z%2B7MSljy5szag%3D&apkvs=1.36&userId=57d9c9243db911e3bd95005056a3001f&bs=1&client=2&r=655\",\"create_time\":\"1382967123\",\"level\":-1,\"userid\":\"2\",\"result\":{\"czdz\":\"http://119.79.232.99/app.php\",\"sydz\":\"http://hut.jofgame.com\",\"cv\":\"1.31\",\"zfbdz\":\"http://117.135.138.248:8080/components/alipay_self/index.php\",\"paycardurl\":\"http://117.135.139.30:17000/index.html\",\"iosczdz\":\"\",\"kfcz\":1,\"fkdz\":\"http://119.97.226.138:808/new_feedback/send_feedback/feedback.php\",\"status\":0,\"userId\":2,\"inviteId\":\"\",\"servers\":\"\",\"loginKey\":\"357d9c9243db911e3bd95005056a3001f20526e6753d3375\",\"jsxx\":[{\"userId\":2}],\"realname\":\"u71d5u6d77u7476\",\"xyh\":1,\"rsn\":1,\"kszc\":0,\"gv\":\"1.0\",\"AppStoreURL\":\"itunes.com/apps/streetfighteriicollection\",\"fwqdm\":\"hud\",\"tips\":\"\",\"tompay\":\"http://hud.jofgame.com:9000/xsyd/ucpay/tompay.php\",\"vipmode\":2,\"helpdocumenturl\":\"http://q.jofgame.com/helpcenter_V1.37/index.html\",\"sgpayurl\":\"\",\"brpayurl\":\"http://117.135.138.248:8080/ucpay/brpay.php\",\"email\":\"Mobile7@unalis.com.tw\",\"jfpay\":\"http://192.168.1.202:81/ucpay/jforder.php\",\"brzf\":\"http://www.baoruan.com/nested/account/login?cid=852&uid=57d9c9243db911e3bd95005056a3001f&type=qjsh&token=8ff9b99a0f8d3584d7dc75e51f11a6b8&notify_url=aHR0cDovLzE5Mi4xNjguMS4yMDI6ODEvdWNwYXkvYnJwYXlfMS5waHA/dXNlcmlkPTImc2VydmVyaWQ9aHVk\",\"sggg\":\"0,0,0\",\"zfzx\":\"http://ucs.jofgame.com/paycenter/index.php\",\"krpayurl\":\"http://192.168.1.202:81/pay/hfpay.php\",\"lbhd\":0,\"ltdz\":\"http://ucs.jofgame.com/authserver/forum/luntan.php\",\"_DEBUG\":1},\"serv_add\":\"192.168.1.202\",\"run_time\":0.025372982025146,\"sqltimes\":6}'), ('6','{\"request_url\":\"/app.php?option=role&task=login&qd=bddk1_1&ssn=2&loginKey=357d9c9243db911e3bd95005056a3001f20526e6753d3375&userId=2&bs=1&r=615\",\"create_time\":\"1382967123\",\"level\":-1,\"userid\":\"2\",\"result\":{\"status\":0,\"loginKey\":\"357d9c9243db911e3bd95005056a3001f20526e6753d3375\",\"userid\":\"2\",\"scdl\":1,\"rsn\":2},\"serv_add\":\"192.168.1.202\",\"run_time\":0.0091609954833984,\"sqltimes\":1}'), ('7','{\"request_url\":\"/app.php?option=user&task=register&qd=bddk1_1&ssn=1&token=hSvO5ULsaU0rLrdpdTGSKjkQYRbVTF85kpVxoSkx7Zex%2BoOtyGpDRvXYhAnyxkPhxq1h0C3ylzkOSSwo4wZKn6JnyS4iSi9MqjwCsppElUw3AKN8aZY57L174IayU4edtZDBYIKBU6hVudpF0Z3%2FWwNSzeQ%2Fv4xb9cG0TXwRyYY%3D&apkvs=1.36&userId=57d9c9243db911e3bd95005056a3001f&bs=1&client=2&r=824\",\"create_time\":\"1382967131\",\"level\":-1,\"userid\":\"2\",\"result\":{\"czdz\":\"http://119.79.232.99/app.php\",\"sydz\":\"http://hut.jofgame.com\",\"cv\":\"1.31\",\"zfbdz\":\"http://117.135.138.248:8080/components/alipay_self/index.php\",\"paycardurl\":\"http://117.135.139.30:17000/index.html\",\"iosczdz\":\"\",\"kfcz\":1,\"fkdz\":\"http://119.97.226.138:808/new_feedback/send_feedback/feedback.php\",\"status\":0,\"userId\":2,\"inviteId\":\"\",\"servers\":\"\",\"loginKey\":\"357d9c9243db911e3bd95005056a3001f19526e675be9876\",\"jsxx\":[{\"userId\":2}],\"realname\":\"u71d5u6d77u7476\",\"xyh\":0,\"rsn\":1,\"kszc\":0,\"gv\":\"1.0\",\"AppStoreURL\":\"itunes.com/apps/streetfighteriicollection\",\"fwqdm\":\"hud\",\"tips\":\"\",\"tompay\":\"http://hud.jofgame.com:9000/xsyd/ucpay/tompay.php\",\"vipmode\":2,\"helpdocumenturl\":\"http://q.jofgame.com/helpcenter_V1.37/index.html\",\"sgpayurl\":\"\",\"brpayurl\":\"http://117.135.138.248:8080/ucpay/brpay.php\",\"email\":\"Mobile7@unalis.com.tw\",\"jfpay\":\"http://192.168.1.202:81/ucpay/jforder.php\",\"brzf\":\"http://www.baoruan.com/nested/account/login?cid=852&uid=57d9c9243db911e3bd95005056a3001f&type=qjsh&token=8ff9b99a0f8d3584d7dc75e51f11a6b8&notify_url=aHR0cDovLzE5Mi4xNjguMS4yMDI6ODEvdWNwYXkvYnJwYXlfMS5waHA/dXNlcmlkPTImc2VydmVyaWQ9aHVk\",\"sggg\":\"0,0,0\",\"zfzx\":\"http://ucs.jofgame.com/paycenter/index.php\",\"krpayurl\":\"http://192.168.1.202:81/pay/hfpay.php\",\"lbhd\":0,\"ltdz\":\"http://ucs.jofgame.com/authserver/forum/luntan.php\",\"_DEBUG\":1},\"serv_add\":\"192.168.1.202\",\"run_time\":0.11731600761414,\"sqltimes\":5}'), ('8','{\"request_url\":\"/app.php?option=role&task=login&qd=bddk1_1&ssn=2&loginKey=357d9c9243db911e3bd95005056a3001f19526e675be9876&userId=2&bs=1&r=20\",\"create_time\":\"1382967132\",\"level\":-1,\"userid\":\"2\",\"result\":{\"status\":0,\"loginKey\":\"357d9c9243db911e3bd95005056a3001f19526e675be9876\",\"userid\":\"2\",\"scdl\":1,\"rsn\":2},\"serv_add\":\"192.168.1.202\",\"run_time\":0.014981985092163,\"sqltimes\":1}');
INSERT INTO `ol_city_msg` VALUES ('1','','0');
INSERT INTO `ol_combin_rule` VALUES ('1','20009','10001','10'), ('2','20010','10002','10'), ('3','20011','10003','10'), ('4','20012','10004','10'), ('5','20013','10005','10'), ('6','20006','10011','4'), ('7','20007','10012','6'), ('8','20008','10013','8'), ('9','20018','10032','10'), ('10','20019','10033','10'), ('11','20020','10034','10'), ('12','20021','10035','10'), ('13','20022','10036','10'), ('14','20023','10037','1'), ('15','20024','10037','1'), ('16','20031','10037','1'), ('17','20032','10037','1'), ('18','20025','10038','1'), ('19','20026','10038','1'), ('20','20033','10038','1'), ('21','20034','10038','1'), ('22','20027','10039','1'), ('23','20028','10039','1'), ('24','20035','10039','1'), ('25','20036','10039','1'), ('26','20090','10057','1'), ('27','20091','10057','1'), ('28','20092','10057','1'), ('29','20093','10057','1'), ('30','18516','18521','1'), ('31','18517','18521','1'), ('32','18518','18521','1'), ('33','18519','18521','1'), ('34','18520','18521','1'), ('35','20130','20129','1'), ('36','20131','20129','1'), ('37','20132','20129','1'), ('38','20133','20129','1'), ('39','20127','10040','10'), ('40','18555','18559','1'), ('41','18556','18559','1'), ('42','18557','18559','1'), ('43','18558','18559','1'), ('44','18569','18570','6'), ('45','18576','41001','25'), ('46','18577','42001','30'), ('47','18578','43001','40'), ('48','18579','44001','50'), ('49','18580','18581','10'), ('50','18581','18582','10'), ('51','18582','18583','10'), ('52','20037','20041','1'), ('53','20038','20041','1'), ('54','20039','20041','1'), ('55','20040','20041','1'), ('56','18602','18607','1'), ('57','18603','18607','1'), ('58','18604','18607','1'), ('59','18605','18607','1'), ('60','18610','18609','10'), ('61','18616','18620','1'), ('62','18617','18620','1'), ('63','18618','18620','1'), ('64','18619','18620','1');
INSERT INTO `ol_dalei_jf_count` VALUES ('0','0','0'), ('1','0','0'), ('2','0','0'), ('3','0','0'), ('4','0','0'), ('5','0','0'), ('6','0','0'), ('7','0','0'), ('8','0','0'), ('9','0','0'), ('10','0','0'), ('11','0','0'), ('12','0','0'), ('13','0','0'), ('14','0','0'), ('15','0','0'), ('16','0','0'), ('17','0','0'), ('18','0','0'), ('19','0','0'), ('20','0','0'), ('21','0','0'), ('22','0','0'), ('23','0','0'), ('24','0','0'), ('25','0','0'), ('26','0','0'), ('27','0','0'), ('28','0','0'), ('29','0','0'), ('30','0','0'), ('31','0','0'), ('32','0','0'), ('33','0','0'), ('34','0','0'), ('35','0','0'), ('36','0','0'), ('37','0','0'), ('38','0','0'), ('39','0','0'), ('40','0','0'), ('41','0','0'), ('42','0','0'), ('43','0','0'), ('44','0','0'), ('45','0','0'), ('46','0','0'), ('47','0','0'), ('48','0','0'), ('49','0','0'), ('50','0','0'), ('51','0','0'), ('52','0','0'), ('53','0','0'), ('54','0','0'), ('55','0','0'), ('56','0','0'), ('57','0','0'), ('58','0','0'), ('59','0','0'), ('60','0','0'), ('61','0','0'), ('62','0','0'), ('63','0','0'), ('64','0','0'), ('65','0','0'), ('66','0','0'), ('67','0','0'), ('68','0','0'), ('69','0','0'), ('70','0','0'), ('71','0','0'), ('72','0','0'), ('73','0','0'), ('74','0','0'), ('75','0','0'), ('76','0','0'), ('77','0','0'), ('78','0','0'), ('79','0','0'), ('80','0','0'), ('81','0','0'), ('82','0','0'), ('83','0','0'), ('84','0','0'), ('85','0','0'), ('86','0','0'), ('87','0','0'), ('88','0','0'), ('89','0','0'), ('90','0','0'), ('91','0','0'), ('92','0','0'), ('93','0','0'), ('94','0','0'), ('95','0','0'), ('96','0','0'), ('97','0','0'), ('98','0','0'), ('99','0','0');
INSERT INTO `ol_dalei_jf_count` VALUES ('100','0','0'), ('101','0','0'), ('102','0','0'), ('103','0','0'), ('104','0','0'), ('105','0','0'), ('106','0','0'), ('107','0','0'), ('108','0','0'), ('109','0','0'), ('110','0','0'), ('111','0','0'), ('112','0','0'), ('113','0','0'), ('114','0','0'), ('115','0','0'), ('116','0','0'), ('117','0','0'), ('118','0','0'), ('119','0','0'), ('120','0','0'), ('121','0','0'), ('122','0','0'), ('123','0','0'), ('124','0','0'), ('125','0','0'), ('126','0','0'), ('127','0','0'), ('128','0','0'), ('129','0','0'), ('130','0','0'), ('131','0','0'), ('132','0','0'), ('133','0','0'), ('134','0','0'), ('135','0','0'), ('136','0','0'), ('137','0','0'), ('138','0','0'), ('139','0','0'), ('140','0','0'), ('141','0','0'), ('142','0','0'), ('143','0','0'), ('144','0','0'), ('145','0','0'), ('146','0','0'), ('147','0','0'), ('148','0','0'), ('149','0','0'), ('150','0','0'), ('151','0','0'), ('152','0','0'), ('153','0','0'), ('154','0','0'), ('155','0','0'), ('156','0','0'), ('157','0','0'), ('158','0','0'), ('159','0','0'), ('160','0','0'), ('161','0','0'), ('162','0','0'), ('163','0','0'), ('164','0','0'), ('165','0','0'), ('166','0','0'), ('167','0','0'), ('168','0','0'), ('169','0','0'), ('170','0','0'), ('171','0','0'), ('172','0','0'), ('173','0','0'), ('174','0','0'), ('175','0','0'), ('176','0','0'), ('177','0','0'), ('178','0','0'), ('179','0','0'), ('180','0','0'), ('181','0','0'), ('182','0','0'), ('183','0','0'), ('184','0','0'), ('185','0','0'), ('186','0','0'), ('187','0','0'), ('188','0','0'), ('189','0','0'), ('190','0','0'), ('191','0','0'), ('192','0','0'), ('193','0','0'), ('194','0','0'), ('195','0','0'), ('196','0','0'), ('197','0','0'), ('198','0','0'), ('199','0','0');
INSERT INTO `ol_dalei_jf_count` VALUES ('200','0','0'), ('201','0','0'), ('202','0','0'), ('203','0','0'), ('204','0','0'), ('205','0','0'), ('206','0','0'), ('207','0','0'), ('208','0','0'), ('209','0','0'), ('210','0','0'), ('211','0','0'), ('212','0','0'), ('213','0','0'), ('214','0','0'), ('215','0','0'), ('216','0','0'), ('217','0','0'), ('218','0','0'), ('219','0','0'), ('220','0','0'), ('221','0','0'), ('222','0','0'), ('223','0','0'), ('224','0','0'), ('225','0','0'), ('226','0','0'), ('227','0','0'), ('228','0','0'), ('229','0','0'), ('230','0','0'), ('231','0','0'), ('232','0','0'), ('233','0','0'), ('234','0','0'), ('235','0','0'), ('236','0','0'), ('237','0','0'), ('238','0','0'), ('239','0','0'), ('240','0','0'), ('241','0','0'), ('242','0','0'), ('243','0','0'), ('244','0','0'), ('245','0','0'), ('246','0','0'), ('247','0','0'), ('248','0','0'), ('249','0','0'), ('250','0','0'), ('251','0','0'), ('252','0','0'), ('253','0','0'), ('254','0','0'), ('255','0','0'), ('256','0','0'), ('257','0','0'), ('258','0','0'), ('259','0','0'), ('260','0','0'), ('261','0','0'), ('262','0','0'), ('263','0','0'), ('264','0','0'), ('265','0','0'), ('266','0','0'), ('267','0','0'), ('268','0','0'), ('269','0','0'), ('270','0','0'), ('271','0','0'), ('272','0','0'), ('273','0','0'), ('274','0','0'), ('275','0','0'), ('276','0','0'), ('277','0','0'), ('278','0','0'), ('279','0','0'), ('280','0','0'), ('281','0','0'), ('282','0','0'), ('283','0','0'), ('284','0','0'), ('285','0','0'), ('286','0','0'), ('287','0','0'), ('288','0','0'), ('289','0','0'), ('290','0','0'), ('291','0','0'), ('292','0','0'), ('293','0','0'), ('294','0','0'), ('295','0','0'), ('296','0','0'), ('297','0','0'), ('298','0','0'), ('299','0','0');
INSERT INTO `ol_dalei_jf_count` VALUES ('300','0','0'), ('301','0','0'), ('302','0','0'), ('303','0','0'), ('304','0','0'), ('305','0','0'), ('306','0','0'), ('307','0','0'), ('308','0','0'), ('309','0','0'), ('310','0','0'), ('311','0','0'), ('312','0','0'), ('313','0','0'), ('314','0','0'), ('315','0','0'), ('316','0','0'), ('317','0','0'), ('318','0','0'), ('319','0','0'), ('320','0','0'), ('321','0','0'), ('322','0','0'), ('323','0','0'), ('324','0','0'), ('325','0','0'), ('326','0','0'), ('327','0','0'), ('328','0','0'), ('329','0','0'), ('330','0','0'), ('331','0','0'), ('332','0','0'), ('333','0','0'), ('334','0','0'), ('335','0','0'), ('336','0','0'), ('337','0','0'), ('338','0','0'), ('339','0','0'), ('340','0','0'), ('341','0','0'), ('342','0','0'), ('343','0','0'), ('344','0','0'), ('345','0','0'), ('346','0','0'), ('347','0','0'), ('348','0','0'), ('349','0','0'), ('350','0','0'), ('351','0','0'), ('352','0','0'), ('353','0','0'), ('354','0','0'), ('355','0','0'), ('356','0','0'), ('357','0','0'), ('358','0','0'), ('359','0','0'), ('360','0','0'), ('361','0','0'), ('362','0','0'), ('363','0','0'), ('364','0','0'), ('365','0','0'), ('366','0','0'), ('367','0','0'), ('368','0','0'), ('369','0','0'), ('370','0','0'), ('371','0','0'), ('372','0','0'), ('373','0','0'), ('374','0','0'), ('375','0','0'), ('376','0','0'), ('377','0','0'), ('378','0','0'), ('379','0','0'), ('380','0','0'), ('381','0','0'), ('382','0','0'), ('383','0','0'), ('384','0','0'), ('385','0','0'), ('386','0','0'), ('387','0','0'), ('388','0','0'), ('389','0','0'), ('390','0','0'), ('391','0','0'), ('392','0','0'), ('393','0','0'), ('394','0','0'), ('395','0','0'), ('396','0','0'), ('397','0','0'), ('398','0','0'), ('399','0','0');
INSERT INTO `ol_dalei_jf_count` VALUES ('400','0','0'), ('401','0','0'), ('402','0','0'), ('403','0','0'), ('404','0','0'), ('405','0','0'), ('406','0','0'), ('407','0','0'), ('408','0','0'), ('409','0','0'), ('410','0','0'), ('411','0','0'), ('412','0','0'), ('413','0','0'), ('414','0','0'), ('415','0','0'), ('416','0','0'), ('417','0','0'), ('418','0','0'), ('419','0','0'), ('420','0','0'), ('421','0','0'), ('422','0','0'), ('423','0','0'), ('424','0','0'), ('425','0','0'), ('426','0','0'), ('427','0','0'), ('428','0','0'), ('429','0','0'), ('430','0','0'), ('431','0','0'), ('432','0','0'), ('433','0','0'), ('434','0','0'), ('435','0','0'), ('436','0','0'), ('437','0','0'), ('438','0','0'), ('439','0','0'), ('440','0','0'), ('441','0','0'), ('442','0','0'), ('443','0','0'), ('444','0','0'), ('445','0','0'), ('446','0','0'), ('447','0','0'), ('448','0','0'), ('449','0','0'), ('450','0','0'), ('451','0','0'), ('452','0','0'), ('453','0','0'), ('454','0','0'), ('455','0','0'), ('456','0','0'), ('457','0','0'), ('458','0','0'), ('459','0','0'), ('460','0','0'), ('461','0','0'), ('462','0','0'), ('463','0','0'), ('464','0','0'), ('465','0','0'), ('466','0','0'), ('467','0','0'), ('468','0','0'), ('469','0','0'), ('470','0','0'), ('471','0','0'), ('472','0','0'), ('473','0','0'), ('474','0','0'), ('475','0','0'), ('476','0','0'), ('477','0','0'), ('478','0','0'), ('479','0','0'), ('480','0','0'), ('481','0','0'), ('482','0','0'), ('483','0','0'), ('484','0','0'), ('485','0','0'), ('486','0','0'), ('487','0','0'), ('488','0','0'), ('489','0','0'), ('490','0','0'), ('491','0','0'), ('492','0','0'), ('493','0','0'), ('494','0','0'), ('495','0','0'), ('496','0','0'), ('497','0','0'), ('498','0','0'), ('499','0','0');
INSERT INTO `ol_dalei_jf_count` VALUES ('500','0','0'), ('501','0','0'), ('502','0','0'), ('503','0','0'), ('504','0','0'), ('505','0','0'), ('506','0','0'), ('507','0','0'), ('508','0','0'), ('509','0','0'), ('510','0','0'), ('511','0','0'), ('512','0','0'), ('513','0','0'), ('514','0','0'), ('515','0','0'), ('516','0','0'), ('517','0','0'), ('518','0','0'), ('519','0','0'), ('520','0','0'), ('521','0','0'), ('522','0','0'), ('523','0','0'), ('524','0','0'), ('525','0','0'), ('526','0','0'), ('527','0','0'), ('528','0','0'), ('529','0','0'), ('530','0','0'), ('531','0','0'), ('532','0','0'), ('533','0','0'), ('534','0','0'), ('535','0','0'), ('536','0','0'), ('537','0','0'), ('538','0','0'), ('539','0','0'), ('540','0','0'), ('541','0','0'), ('542','0','0'), ('543','0','0'), ('544','0','0'), ('545','0','0'), ('546','0','0'), ('547','0','0'), ('548','0','0'), ('549','0','0'), ('550','0','0'), ('551','0','0'), ('552','0','0'), ('553','0','0'), ('554','0','0'), ('555','0','0'), ('556','0','0'), ('557','0','0'), ('558','0','0'), ('559','0','0'), ('560','0','0'), ('561','0','0'), ('562','0','0'), ('563','0','0'), ('564','0','0'), ('565','0','0'), ('566','0','0'), ('567','0','0'), ('568','0','0'), ('569','0','0'), ('570','0','0'), ('571','0','0'), ('572','0','0'), ('573','0','0'), ('574','0','0'), ('575','0','0'), ('576','0','0'), ('577','0','0'), ('578','0','0'), ('579','0','0'), ('580','0','0'), ('581','0','0'), ('582','0','0'), ('583','0','0'), ('584','0','0'), ('585','0','0'), ('586','0','0'), ('587','0','0'), ('588','0','0'), ('589','0','0'), ('590','0','0'), ('591','0','0'), ('592','0','0'), ('593','0','0'), ('594','0','0'), ('595','0','0'), ('596','0','0'), ('597','0','0'), ('598','0','0'), ('599','0','0');
INSERT INTO `ol_dalei_jf_count` VALUES ('600','0','0'), ('601','0','0'), ('602','0','0'), ('603','0','0'), ('604','0','0'), ('605','0','0'), ('606','0','0'), ('607','0','0'), ('608','0','0'), ('609','0','0'), ('610','0','0'), ('611','0','0'), ('612','0','0'), ('613','0','0'), ('614','0','0'), ('615','0','0'), ('616','0','0'), ('617','0','0'), ('618','0','0'), ('619','0','0'), ('620','0','0'), ('621','0','0'), ('622','0','0'), ('623','0','0'), ('624','0','0'), ('625','0','0'), ('626','0','0'), ('627','0','0'), ('628','0','0'), ('629','0','0'), ('630','0','0'), ('631','0','0'), ('632','0','0'), ('633','0','0'), ('634','0','0'), ('635','0','0'), ('636','0','0'), ('637','0','0'), ('638','0','0'), ('639','0','0'), ('640','0','0'), ('641','0','0'), ('642','0','0'), ('643','0','0'), ('644','0','0'), ('645','0','0'), ('646','0','0'), ('647','0','0'), ('648','0','0'), ('649','0','0'), ('650','0','0'), ('651','0','0'), ('652','0','0'), ('653','0','0'), ('654','0','0'), ('655','0','0'), ('656','0','0'), ('657','0','0'), ('658','0','0'), ('659','0','0'), ('660','0','0'), ('661','0','0'), ('662','0','0'), ('663','0','0'), ('664','0','0'), ('665','0','0'), ('666','0','0'), ('667','0','0'), ('668','0','0'), ('669','0','0'), ('670','0','0'), ('671','0','0'), ('672','0','0'), ('673','0','0'), ('674','0','0'), ('675','0','0'), ('676','0','0'), ('677','0','0'), ('678','0','0'), ('679','0','0'), ('680','0','0'), ('681','0','0'), ('682','0','0'), ('683','0','0'), ('684','0','0'), ('685','0','0'), ('686','0','0'), ('687','0','0'), ('688','0','0'), ('689','0','0'), ('690','0','0'), ('691','0','0'), ('692','0','0'), ('693','0','0'), ('694','0','0'), ('695','0','0'), ('696','0','0'), ('697','0','0'), ('698','0','0'), ('699','0','0');
INSERT INTO `ol_dalei_jf_count` VALUES ('700','0','0'), ('701','0','0'), ('702','0','0'), ('703','0','0'), ('704','0','0'), ('705','0','0'), ('706','0','0'), ('707','0','0'), ('708','0','0'), ('709','0','0'), ('710','0','0'), ('711','0','0'), ('712','0','0'), ('713','0','0'), ('714','0','0'), ('715','0','0'), ('716','0','0'), ('717','0','0'), ('718','0','0'), ('719','0','0'), ('720','0','0'), ('721','0','0'), ('722','0','0'), ('723','0','0'), ('724','0','0'), ('725','0','0'), ('726','0','0'), ('727','0','0'), ('728','0','0'), ('729','0','0'), ('730','0','0'), ('731','0','0'), ('732','0','0'), ('733','0','0'), ('734','0','0'), ('735','0','0'), ('736','0','0'), ('737','0','0'), ('738','0','0'), ('739','0','0'), ('740','0','0'), ('741','0','0'), ('742','0','0'), ('743','0','0'), ('744','0','0'), ('745','0','0'), ('746','0','0'), ('747','0','0'), ('748','0','0'), ('749','0','0'), ('750','0','0'), ('751','0','0'), ('752','0','0'), ('753','0','0'), ('754','0','0'), ('755','0','0'), ('756','0','0'), ('757','0','0'), ('758','0','0'), ('759','0','0'), ('760','0','0'), ('761','0','0'), ('762','0','0'), ('763','0','0'), ('764','0','0'), ('765','0','0'), ('766','0','0'), ('767','0','0'), ('768','0','0'), ('769','0','0'), ('770','0','0'), ('771','0','0'), ('772','0','0'), ('773','0','0'), ('774','0','0'), ('775','0','0'), ('776','0','0'), ('777','0','0'), ('778','0','0'), ('779','0','0'), ('780','0','0'), ('781','0','0'), ('782','0','0'), ('783','0','0'), ('784','0','0'), ('785','0','0'), ('786','0','0'), ('787','0','0'), ('788','0','0'), ('789','0','0'), ('790','0','0'), ('791','0','0'), ('792','0','0'), ('793','0','0'), ('794','0','0'), ('795','0','0'), ('796','0','0'), ('797','0','0'), ('798','0','0'), ('799','0','0');
INSERT INTO `ol_dalei_jf_count` VALUES ('800','0','0'), ('801','0','0'), ('802','0','0'), ('803','0','0'), ('804','0','0'), ('805','0','0'), ('806','0','0'), ('807','0','0'), ('808','0','0'), ('809','0','0'), ('810','0','0'), ('811','0','0'), ('812','0','0'), ('813','0','0'), ('814','0','0'), ('815','0','0'), ('816','0','0'), ('817','0','0'), ('818','0','0'), ('819','0','0'), ('820','0','0'), ('821','0','0'), ('822','0','0'), ('823','0','0'), ('824','0','0'), ('825','0','0'), ('826','0','0'), ('827','0','0'), ('828','0','0'), ('829','0','0'), ('830','0','0'), ('831','0','0'), ('832','0','0'), ('833','0','0'), ('834','0','0'), ('835','0','0'), ('836','0','0'), ('837','0','0'), ('838','0','0'), ('839','0','0'), ('840','0','0'), ('841','0','0'), ('842','0','0'), ('843','0','0'), ('844','0','0'), ('845','0','0'), ('846','0','0'), ('847','0','0'), ('848','0','0'), ('849','0','0'), ('850','0','0'), ('851','0','0'), ('852','0','0'), ('853','0','0'), ('854','0','0'), ('855','0','0'), ('856','0','0'), ('857','0','0'), ('858','0','0'), ('859','0','0'), ('860','0','0'), ('861','0','0'), ('862','0','0'), ('863','0','0'), ('864','0','0'), ('865','0','0'), ('866','0','0'), ('867','0','0'), ('868','0','0'), ('869','0','0'), ('870','0','0'), ('871','0','0'), ('872','0','0'), ('873','0','0'), ('874','0','0'), ('875','0','0'), ('876','0','0'), ('877','0','0'), ('878','0','0'), ('879','0','0'), ('880','0','0'), ('881','0','0'), ('882','0','0'), ('883','0','0'), ('884','0','0'), ('885','0','0'), ('886','0','0'), ('887','0','0'), ('888','0','0'), ('889','0','0'), ('890','0','0'), ('891','0','0'), ('892','0','0'), ('893','0','0'), ('894','0','0'), ('895','0','0'), ('896','0','0'), ('897','0','0'), ('898','0','0'), ('899','0','0');
INSERT INTO `ol_dalei_jf_count` VALUES ('900','0','0'), ('901','0','0'), ('902','0','0'), ('903','0','0'), ('904','0','0'), ('905','0','0'), ('906','0','0'), ('907','0','0'), ('908','0','0'), ('909','0','0'), ('910','0','0'), ('911','0','0'), ('912','0','0'), ('913','0','0'), ('914','0','0'), ('915','0','0'), ('916','0','0'), ('917','0','0'), ('918','0','0'), ('919','0','0'), ('920','0','0'), ('921','0','0'), ('922','0','0'), ('923','0','0'), ('924','0','0'), ('925','0','0'), ('926','0','0'), ('927','0','0'), ('928','0','0'), ('929','0','0'), ('930','0','0'), ('931','0','0'), ('932','0','0'), ('933','0','0'), ('934','0','0'), ('935','0','0'), ('936','0','0'), ('937','0','0'), ('938','0','0'), ('939','0','0'), ('940','0','0'), ('941','0','0'), ('942','0','0'), ('943','0','0'), ('944','0','0'), ('945','0','0'), ('946','0','0'), ('947','0','0'), ('948','0','0'), ('949','0','0'), ('950','0','0'), ('951','0','0'), ('952','0','0'), ('953','0','0'), ('954','0','0'), ('955','0','0'), ('956','0','0'), ('957','0','0'), ('958','0','0'), ('959','0','0'), ('960','0','0'), ('961','0','0'), ('962','0','0'), ('963','0','0'), ('964','0','0'), ('965','0','0'), ('966','0','0'), ('967','0','0'), ('968','0','0'), ('969','0','0'), ('970','0','0'), ('971','0','0'), ('972','0','0'), ('973','0','0'), ('974','0','0'), ('975','0','0'), ('976','0','0'), ('977','0','0'), ('978','0','0'), ('979','0','0'), ('980','0','0'), ('981','0','0'), ('982','0','0'), ('983','0','0'), ('984','0','0'), ('985','0','0'), ('986','0','0'), ('987','0','0'), ('988','0','0'), ('989','0','0'), ('990','0','0'), ('991','0','0'), ('992','0','0'), ('993','0','0'), ('994','0','0'), ('995','0','0'), ('996','0','0'), ('997','0','0'), ('998','0','0'), ('999','0','0');
INSERT INTO `ol_dalei_jf_count` VALUES ('1000','0','0'), ('1001','0','0'), ('1002','0','0'), ('1003','0','0'), ('1004','0','0'), ('1005','0','0'), ('1006','0','0'), ('1007','0','0'), ('1008','0','0'), ('1009','0','0'), ('1010','0','0'), ('1011','0','0'), ('1012','0','0'), ('1013','0','0'), ('1014','0','0'), ('1015','0','0'), ('1016','0','0'), ('1017','0','0'), ('1018','0','0'), ('1019','0','0'), ('1020','0','0'), ('1021','0','0'), ('1022','0','0'), ('1023','0','0'), ('1024','0','0'), ('1025','0','0'), ('1026','0','0'), ('1027','0','0'), ('1028','0','0'), ('1029','0','0'), ('1030','0','0'), ('1031','0','0'), ('1032','0','0'), ('1033','0','0'), ('1034','0','0'), ('1035','0','0'), ('1036','0','0'), ('1037','0','0'), ('1038','0','0'), ('1039','0','0'), ('1040','0','0'), ('1041','0','0'), ('1042','0','0'), ('1043','0','0'), ('1044','0','0'), ('1045','0','0'), ('1046','0','0'), ('1047','0','0'), ('1048','0','0'), ('1049','0','0'), ('1050','0','0'), ('1051','0','0'), ('1052','0','0'), ('1053','0','0'), ('1054','0','0'), ('1055','0','0'), ('1056','0','0'), ('1057','0','0'), ('1058','0','0'), ('1059','0','0'), ('1060','0','0'), ('1061','0','0'), ('1062','0','0'), ('1063','0','0'), ('1064','0','0'), ('1065','0','0'), ('1066','0','0'), ('1067','0','0'), ('1068','0','0'), ('1069','0','0'), ('1070','0','0'), ('1071','0','0'), ('1072','0','0'), ('1073','0','0'), ('1074','0','0'), ('1075','0','0'), ('1076','0','0'), ('1077','0','0'), ('1078','0','0'), ('1079','0','0'), ('1080','0','0'), ('1081','0','0'), ('1082','0','0'), ('1083','0','0'), ('1084','0','0'), ('1085','0','0'), ('1086','0','0'), ('1087','0','0'), ('1088','0','0'), ('1089','0','0'), ('1090','0','0'), ('1091','0','0'), ('1092','0','0'), ('1093','0','0'), ('1094','0','0'), ('1095','0','0'), ('1096','0','0'), ('1097','0','0'), ('1098','0','0'), ('1099','0','0');
INSERT INTO `ol_dalei_jf_count` VALUES ('1100','0','0'), ('1101','0','0'), ('1102','0','0'), ('1103','0','0'), ('1104','0','0'), ('1105','0','0'), ('1106','0','0'), ('1107','0','0'), ('1108','0','0'), ('1109','0','0'), ('1110','0','0'), ('1111','0','0'), ('1112','0','0'), ('1113','0','0'), ('1114','0','0'), ('1115','0','0'), ('1116','0','0'), ('1117','0','0'), ('1118','0','0'), ('1119','0','0'), ('1120','0','0'), ('1121','0','0'), ('1122','0','0'), ('1123','0','0'), ('1124','0','0'), ('1125','0','0'), ('1126','0','0'), ('1127','0','0'), ('1128','0','0'), ('1129','0','0'), ('1130','0','0'), ('1131','0','0'), ('1132','0','0'), ('1133','0','0'), ('1134','0','0'), ('1135','0','0'), ('1136','0','0'), ('1137','0','0'), ('1138','0','0'), ('1139','0','0'), ('1140','0','0'), ('1141','0','0'), ('1142','0','0'), ('1143','0','0'), ('1144','0','0'), ('1145','0','0'), ('1146','0','0'), ('1147','0','0'), ('1148','0','0'), ('1149','0','0'), ('1150','0','0'), ('1151','0','0'), ('1152','0','0'), ('1153','0','0'), ('1154','0','0'), ('1155','0','0'), ('1156','0','0'), ('1157','0','0'), ('1158','0','0'), ('1159','0','0'), ('1160','0','0'), ('1161','0','0'), ('1162','0','0'), ('1163','0','0'), ('1164','0','0'), ('1165','0','0'), ('1166','0','0'), ('1167','0','0'), ('1168','0','0'), ('1169','0','0'), ('1170','0','0'), ('1171','0','0'), ('1172','0','0'), ('1173','0','0'), ('1174','0','0'), ('1175','0','0'), ('1176','0','0'), ('1177','0','0'), ('1178','0','0'), ('1179','0','0'), ('1180','0','0'), ('1181','0','0'), ('1182','0','0'), ('1183','0','0'), ('1184','0','0'), ('1185','0','0'), ('1186','0','0'), ('1187','0','0'), ('1188','0','0'), ('1189','0','0'), ('1190','0','0'), ('1191','0','0'), ('1192','0','0'), ('1193','0','0'), ('1194','0','0'), ('1195','0','0'), ('1196','0','0'), ('1197','0','0'), ('1198','0','0'), ('1199','0','0');
INSERT INTO `ol_dalei_jf_count` VALUES ('1200','0','0'), ('1201','0','0'), ('1202','0','0'), ('1203','0','0'), ('1204','0','0'), ('1205','0','0'), ('1206','0','0'), ('1207','0','0'), ('1208','0','0'), ('1209','0','0'), ('1210','0','0'), ('1211','0','0'), ('1212','0','0'), ('1213','0','0'), ('1214','0','0'), ('1215','0','0'), ('1216','0','0'), ('1217','0','0'), ('1218','0','0'), ('1219','0','0'), ('1220','0','0'), ('1221','0','0'), ('1222','0','0'), ('1223','0','0'), ('1224','0','0'), ('1225','0','0'), ('1226','0','0'), ('1227','0','0'), ('1228','0','0'), ('1229','0','0'), ('1230','0','0'), ('1231','0','0'), ('1232','0','0'), ('1233','0','0'), ('1234','0','0'), ('1235','0','0'), ('1236','0','0'), ('1237','0','0'), ('1238','0','0'), ('1239','0','0'), ('1240','0','0'), ('1241','0','0'), ('1242','0','0'), ('1243','0','0'), ('1244','0','0'), ('1245','0','0'), ('1246','0','0'), ('1247','0','0'), ('1248','0','0'), ('1249','0','0'), ('1250','0','0'), ('1251','0','0'), ('1252','0','0'), ('1253','0','0'), ('1254','0','0'), ('1255','0','0'), ('1256','0','0'), ('1257','0','0'), ('1258','0','0'), ('1259','0','0'), ('1260','0','0'), ('1261','0','0'), ('1262','0','0'), ('1263','0','0'), ('1264','0','0'), ('1265','0','0'), ('1266','0','0'), ('1267','0','0'), ('1268','0','0'), ('1269','0','0'), ('1270','0','0'), ('1271','0','0'), ('1272','0','0'), ('1273','0','0'), ('1274','0','0'), ('1275','0','0'), ('1276','0','0'), ('1277','0','0'), ('1278','0','0'), ('1279','0','0'), ('1280','0','0'), ('1281','0','0'), ('1282','0','0'), ('1283','0','0'), ('1284','0','0'), ('1285','0','0'), ('1286','0','0'), ('1287','0','0'), ('1288','0','0'), ('1289','0','0'), ('1290','0','0'), ('1291','0','0'), ('1292','0','0'), ('1293','0','0'), ('1294','0','0'), ('1295','0','0'), ('1296','0','0'), ('1297','0','0'), ('1298','0','0'), ('1299','0','0');
INSERT INTO `ol_dalei_jf_count` VALUES ('1300','0','0'), ('1301','0','0'), ('1302','0','0'), ('1303','0','0'), ('1304','0','0'), ('1305','0','0'), ('1306','0','0'), ('1307','0','0'), ('1308','0','0'), ('1309','0','0'), ('1310','0','0'), ('1311','0','0'), ('1312','0','0'), ('1313','0','0'), ('1314','0','0'), ('1315','0','0'), ('1316','0','0'), ('1317','0','0'), ('1318','0','0'), ('1319','0','0'), ('1320','0','0'), ('1321','0','0'), ('1322','0','0'), ('1323','0','0'), ('1324','0','0'), ('1325','0','0'), ('1326','0','0'), ('1327','0','0'), ('1328','0','0'), ('1329','0','0'), ('1330','0','0'), ('1331','0','0'), ('1332','0','0'), ('1333','0','0'), ('1334','0','0'), ('1335','0','0'), ('1336','0','0'), ('1337','0','0'), ('1338','0','0'), ('1339','0','0'), ('1340','0','0'), ('1341','0','0'), ('1342','0','0'), ('1343','0','0'), ('1344','0','0'), ('1345','0','0'), ('1346','0','0'), ('1347','0','0'), ('1348','0','0'), ('1349','0','0'), ('1350','0','0'), ('1351','0','0'), ('1352','0','0'), ('1353','0','0'), ('1354','0','0'), ('1355','0','0'), ('1356','0','0'), ('1357','0','0'), ('1358','0','0'), ('1359','0','0'), ('1360','0','0'), ('1361','0','0'), ('1362','0','0'), ('1363','0','0'), ('1364','0','0'), ('1365','0','0'), ('1366','0','0'), ('1367','0','0'), ('1368','0','0'), ('1369','0','0'), ('1370','0','0'), ('1371','0','0'), ('1372','0','0'), ('1373','0','0'), ('1374','0','0'), ('1375','0','0'), ('1376','0','0'), ('1377','0','0'), ('1378','0','0'), ('1379','0','0'), ('1380','0','0'), ('1381','0','0'), ('1382','0','0'), ('1383','0','0'), ('1384','0','0'), ('1385','0','0'), ('1386','0','0'), ('1387','0','0'), ('1388','0','0'), ('1389','0','0'), ('1390','0','0'), ('1391','0','0'), ('1392','0','0'), ('1393','0','0'), ('1394','0','0'), ('1395','0','0'), ('1396','0','0'), ('1397','0','0'), ('1398','0','0'), ('1399','0','0');
INSERT INTO `ol_dalei_jf_count` VALUES ('1400','0','0'), ('1401','0','0'), ('1402','0','0'), ('1403','0','0'), ('1404','0','0'), ('1405','0','0'), ('1406','0','0'), ('1407','0','0'), ('1408','0','0'), ('1409','0','0'), ('1410','0','0'), ('1411','0','0'), ('1412','0','0'), ('1413','0','0'), ('1414','0','0'), ('1415','0','0'), ('1416','0','0'), ('1417','0','0'), ('1418','0','0'), ('1419','0','0'), ('1420','0','0'), ('1421','0','0'), ('1422','0','0'), ('1423','0','0'), ('1424','0','0'), ('1425','0','0'), ('1426','0','0'), ('1427','0','0'), ('1428','0','0'), ('1429','0','0'), ('1430','0','0'), ('1431','0','0'), ('1432','0','0'), ('1433','0','0'), ('1434','0','0'), ('1435','0','0'), ('1436','0','0'), ('1437','0','0'), ('1438','0','0'), ('1439','0','0'), ('1440','0','0'), ('1441','0','0'), ('1442','0','0'), ('1443','0','0'), ('1444','0','0'), ('1445','0','0'), ('1446','0','0'), ('1447','0','0'), ('1448','0','0'), ('1449','0','0'), ('1450','0','0'), ('1451','0','0'), ('1452','0','0'), ('1453','0','0'), ('1454','0','0'), ('1455','0','0'), ('1456','0','0'), ('1457','0','0'), ('1458','0','0'), ('1459','0','0'), ('1460','0','0'), ('1461','0','0'), ('1462','0','0'), ('1463','0','0'), ('1464','0','0'), ('1465','0','0'), ('1466','0','0'), ('1467','0','0'), ('1468','0','0'), ('1469','0','0'), ('1470','0','0'), ('1471','0','0'), ('1472','0','0'), ('1473','0','0'), ('1474','0','0'), ('1475','0','0'), ('1476','0','0'), ('1477','0','0'), ('1478','0','0'), ('1479','0','0'), ('1480','0','0'), ('1481','0','0'), ('1482','0','0'), ('1483','0','0'), ('1484','0','0'), ('1485','0','0'), ('1486','0','0'), ('1487','0','0'), ('1488','0','0'), ('1489','0','0'), ('1490','0','0'), ('1491','0','0'), ('1492','0','0'), ('1493','0','0'), ('1494','0','0'), ('1495','0','0'), ('1496','0','0'), ('1497','0','0'), ('1498','0','0'), ('1499','0','0');
INSERT INTO `ol_dalei_jf_count` VALUES ('1500','0','0'), ('1501','0','0'), ('1502','0','0'), ('1503','0','0'), ('1504','0','0'), ('1505','0','0'), ('1506','0','0'), ('1507','0','0'), ('1508','0','0'), ('1509','0','0'), ('1510','0','0'), ('1511','0','0'), ('1512','0','0'), ('1513','0','0'), ('1514','0','0'), ('1515','0','0'), ('1516','0','0'), ('1517','0','0'), ('1518','0','0'), ('1519','0','0'), ('1520','0','0'), ('1521','0','0'), ('1522','0','0'), ('1523','0','0'), ('1524','0','0'), ('1525','0','0'), ('1526','0','0'), ('1527','0','0'), ('1528','0','0'), ('1529','0','0'), ('1530','0','0'), ('1531','0','0'), ('1532','0','0'), ('1533','0','0'), ('1534','0','0'), ('1535','0','0'), ('1536','0','0'), ('1537','0','0'), ('1538','0','0'), ('1539','0','0'), ('1540','0','0'), ('1541','0','0'), ('1542','0','0'), ('1543','0','0'), ('1544','0','0'), ('1545','0','0'), ('1546','0','0'), ('1547','0','0'), ('1548','0','0'), ('1549','0','0'), ('1550','0','0'), ('1551','0','0'), ('1552','0','0'), ('1553','0','0'), ('1554','0','0'), ('1555','0','0'), ('1556','0','0'), ('1557','0','0'), ('1558','0','0'), ('1559','0','0'), ('1560','0','0'), ('1561','0','0'), ('1562','0','0'), ('1563','0','0'), ('1564','0','0'), ('1565','0','0'), ('1566','0','0'), ('1567','0','0'), ('1568','0','0'), ('1569','0','0'), ('1570','0','0'), ('1571','0','0'), ('1572','0','0'), ('1573','0','0'), ('1574','0','0'), ('1575','0','0'), ('1576','0','0'), ('1577','0','0'), ('1578','0','0'), ('1579','0','0'), ('1580','0','0'), ('1581','0','0'), ('1582','0','0'), ('1583','0','0'), ('1584','0','0'), ('1585','0','0'), ('1586','0','0'), ('1587','0','0'), ('1588','0','0'), ('1589','0','0'), ('1590','0','0'), ('1591','0','0'), ('1592','0','0'), ('1593','0','0'), ('1594','0','0'), ('1595','0','0'), ('1596','0','0'), ('1597','0','0'), ('1598','0','0'), ('1599','0','0');
INSERT INTO `ol_dalei_jf_count` VALUES ('1600','0','0'), ('1601','0','0'), ('1602','0','0'), ('1603','0','0'), ('1604','0','0'), ('1605','0','0'), ('1606','0','0'), ('1607','0','0'), ('1608','0','0'), ('1609','0','0'), ('1610','0','0'), ('1611','0','0'), ('1612','0','0'), ('1613','0','0'), ('1614','0','0'), ('1615','0','0'), ('1616','0','0'), ('1617','0','0'), ('1618','0','0'), ('1619','0','0'), ('1620','0','0'), ('1621','0','0'), ('1622','0','0'), ('1623','0','0'), ('1624','0','0'), ('1625','0','0'), ('1626','0','0'), ('1627','0','0'), ('1628','0','0'), ('1629','0','0'), ('1630','0','0'), ('1631','0','0'), ('1632','0','0'), ('1633','0','0'), ('1634','0','0'), ('1635','0','0'), ('1636','0','0'), ('1637','0','0'), ('1638','0','0'), ('1639','0','0'), ('1640','0','0'), ('1641','0','0'), ('1642','0','0'), ('1643','0','0'), ('1644','0','0'), ('1645','0','0'), ('1646','0','0'), ('1647','0','0'), ('1648','0','0'), ('1649','0','0'), ('1650','0','0'), ('1651','0','0'), ('1652','0','0'), ('1653','0','0'), ('1654','0','0'), ('1655','0','0'), ('1656','0','0'), ('1657','0','0'), ('1658','0','0'), ('1659','0','0'), ('1660','0','0'), ('1661','0','0'), ('1662','0','0'), ('1663','0','0'), ('1664','0','0'), ('1665','0','0'), ('1666','0','0'), ('1667','0','0'), ('1668','0','0'), ('1669','0','0'), ('1670','0','0'), ('1671','0','0'), ('1672','0','0'), ('1673','0','0'), ('1674','0','0'), ('1675','0','0'), ('1676','0','0'), ('1677','0','0'), ('1678','0','0'), ('1679','0','0'), ('1680','0','0'), ('1681','0','0'), ('1682','0','0'), ('1683','0','0'), ('1684','0','0'), ('1685','0','0'), ('1686','0','0'), ('1687','0','0'), ('1688','0','0'), ('1689','0','0'), ('1690','0','0'), ('1691','0','0'), ('1692','0','0'), ('1693','0','0'), ('1694','0','0'), ('1695','0','0'), ('1696','0','0'), ('1697','0','0'), ('1698','0','0'), ('1699','0','0');
INSERT INTO `ol_dalei_jf_count` VALUES ('1700','0','0'), ('1701','0','0'), ('1702','0','0'), ('1703','0','0'), ('1704','0','0'), ('1705','0','0'), ('1706','0','0'), ('1707','0','0'), ('1708','0','0'), ('1709','0','0'), ('1710','0','0'), ('1711','0','0'), ('1712','0','0'), ('1713','0','0'), ('1714','0','0'), ('1715','0','0'), ('1716','0','0'), ('1717','0','0'), ('1718','0','0'), ('1719','0','0'), ('1720','0','0'), ('1721','0','0'), ('1722','0','0'), ('1723','0','0'), ('1724','0','0'), ('1725','0','0'), ('1726','0','0'), ('1727','0','0'), ('1728','0','0'), ('1729','0','0'), ('1730','0','0'), ('1731','0','0'), ('1732','0','0'), ('1733','0','0'), ('1734','0','0'), ('1735','0','0'), ('1736','0','0'), ('1737','0','0'), ('1738','0','0'), ('1739','0','0'), ('1740','0','0'), ('1741','0','0'), ('1742','0','0'), ('1743','0','0'), ('1744','0','0'), ('1745','0','0'), ('1746','0','0'), ('1747','0','0'), ('1748','0','0'), ('1749','0','0'), ('1750','0','0'), ('1751','0','0'), ('1752','0','0'), ('1753','0','0'), ('1754','0','0'), ('1755','0','0'), ('1756','0','0'), ('1757','0','0'), ('1758','0','0'), ('1759','0','0'), ('1760','0','0'), ('1761','0','0'), ('1762','0','0'), ('1763','0','0'), ('1764','0','0'), ('1765','0','0'), ('1766','0','0'), ('1767','0','0'), ('1768','0','0'), ('1769','0','0'), ('1770','0','0'), ('1771','0','0'), ('1772','0','0'), ('1773','0','0'), ('1774','0','0'), ('1775','0','0'), ('1776','0','0'), ('1777','0','0'), ('1778','0','0'), ('1779','0','0'), ('1780','0','0'), ('1781','0','0'), ('1782','0','0'), ('1783','0','0'), ('1784','0','0'), ('1785','0','0'), ('1786','0','0'), ('1787','0','0'), ('1788','0','0'), ('1789','0','0'), ('1790','0','0'), ('1791','0','0'), ('1792','0','0'), ('1793','0','0'), ('1794','0','0'), ('1795','0','0'), ('1796','0','0'), ('1797','0','0'), ('1798','0','0'), ('1799','0','0');
INSERT INTO `ol_dalei_jf_count` VALUES ('1800','0','0'), ('1801','0','0'), ('1802','0','0'), ('1803','0','0'), ('1804','0','0'), ('1805','0','0'), ('1806','0','0'), ('1807','0','0'), ('1808','0','0'), ('1809','0','0'), ('1810','0','0'), ('1811','0','0'), ('1812','0','0'), ('1813','0','0'), ('1814','0','0'), ('1815','0','0'), ('1816','0','0'), ('1817','0','0'), ('1818','0','0'), ('1819','0','0'), ('1820','0','0'), ('1821','0','0'), ('1822','0','0'), ('1823','0','0'), ('1824','0','0'), ('1825','0','0'), ('1826','0','0'), ('1827','0','0'), ('1828','0','0'), ('1829','0','0'), ('1830','0','0'), ('1831','0','0'), ('1832','0','0'), ('1833','0','0'), ('1834','0','0'), ('1835','0','0'), ('1836','0','0'), ('1837','0','0'), ('1838','0','0'), ('1839','0','0'), ('1840','0','0'), ('1841','0','0'), ('1842','0','0'), ('1843','0','0'), ('1844','0','0'), ('1845','0','0'), ('1846','0','0'), ('1847','0','0'), ('1848','0','0'), ('1849','0','0'), ('1850','0','0'), ('1851','0','0'), ('1852','0','0'), ('1853','0','0'), ('1854','0','0'), ('1855','0','0'), ('1856','0','0'), ('1857','0','0'), ('1858','0','0'), ('1859','0','0'), ('1860','0','0'), ('1861','0','0'), ('1862','0','0'), ('1863','0','0'), ('1864','0','0'), ('1865','0','0'), ('1866','0','0'), ('1867','0','0'), ('1868','0','0'), ('1869','0','0'), ('1870','0','0'), ('1871','0','0'), ('1872','0','0'), ('1873','0','0'), ('1874','0','0'), ('1875','0','0'), ('1876','0','0'), ('1877','0','0'), ('1878','0','0'), ('1879','0','0'), ('1880','0','0'), ('1881','0','0'), ('1882','0','0'), ('1883','0','0'), ('1884','0','0'), ('1885','0','0'), ('1886','0','0'), ('1887','0','0'), ('1888','0','0'), ('1889','0','0'), ('1890','0','0'), ('1891','0','0'), ('1892','0','0'), ('1893','0','0'), ('1894','0','0'), ('1895','0','0'), ('1896','0','0'), ('1897','0','0'), ('1898','0','0'), ('1899','0','0');
INSERT INTO `ol_dalei_jf_count` VALUES ('1900','0','0'), ('1901','0','0'), ('1902','0','0'), ('1903','0','0'), ('1904','0','0'), ('1905','0','0'), ('1906','0','0'), ('1907','0','0'), ('1908','0','0'), ('1909','0','0'), ('1910','0','0'), ('1911','0','0'), ('1912','0','0'), ('1913','0','0'), ('1914','0','0'), ('1915','0','0'), ('1916','0','0'), ('1917','0','0'), ('1918','0','0'), ('1919','0','0'), ('1920','0','0'), ('1921','0','0'), ('1922','0','0'), ('1923','0','0'), ('1924','0','0'), ('1925','0','0'), ('1926','0','0'), ('1927','0','0'), ('1928','0','0'), ('1929','0','0'), ('1930','0','0'), ('1931','0','0'), ('1932','0','0'), ('1933','0','0'), ('1934','0','0'), ('1935','0','0'), ('1936','0','0'), ('1937','0','0'), ('1938','0','0'), ('1939','0','0'), ('1940','0','0'), ('1941','0','0'), ('1942','0','0'), ('1943','0','0'), ('1944','0','0'), ('1945','0','0'), ('1946','0','0'), ('1947','0','0'), ('1948','0','0'), ('1949','0','0'), ('1950','0','0'), ('1951','0','0'), ('1952','0','0'), ('1953','0','0'), ('1954','0','0'), ('1955','0','0'), ('1956','0','0'), ('1957','0','0'), ('1958','0','0'), ('1959','0','0'), ('1960','0','0'), ('1961','0','0'), ('1962','0','0'), ('1963','0','0'), ('1964','0','0'), ('1965','0','0'), ('1966','0','0'), ('1967','0','0'), ('1968','0','0'), ('1969','0','0'), ('1970','0','0'), ('1971','0','0'), ('1972','0','0'), ('1973','0','0'), ('1974','0','0'), ('1975','0','0'), ('1976','0','0'), ('1977','0','0'), ('1978','0','0'), ('1979','0','0'), ('1980','0','0'), ('1981','0','0'), ('1982','0','0'), ('1983','0','0'), ('1984','0','0'), ('1985','0','0'), ('1986','0','0'), ('1987','0','0'), ('1988','0','0'), ('1989','0','0'), ('1990','0','0'), ('1991','0','0'), ('1992','0','0'), ('1993','0','0'), ('1994','0','0'), ('1995','0','0'), ('1996','0','0'), ('1997','0','0'), ('1998','0','0'), ('1999','0','0');
INSERT INTO `ol_dalei_jf_count` VALUES ('2000','0','0'), ('2001','0','0'), ('2002','0','0'), ('2003','0','0'), ('2004','0','0'), ('2005','0','0'), ('2006','0','0'), ('2007','0','0'), ('2008','0','0'), ('2009','0','0'), ('2010','0','0'), ('2011','0','0'), ('2012','0','0'), ('2013','0','0'), ('2014','0','0'), ('2015','0','0'), ('2016','0','0'), ('2017','0','0'), ('2018','0','0'), ('2019','0','0'), ('2020','0','0'), ('2021','0','0'), ('2022','0','0'), ('2023','0','0'), ('2024','0','0'), ('2025','0','0'), ('2026','0','0'), ('2027','0','0'), ('2028','0','0'), ('2029','0','0'), ('2030','0','0'), ('2031','0','0'), ('2032','0','0'), ('2033','0','0'), ('2034','0','0'), ('2035','0','0'), ('2036','0','0'), ('2037','0','0'), ('2038','0','0'), ('2039','0','0'), ('2040','0','0'), ('2041','0','0'), ('2042','0','0'), ('2043','0','0'), ('2044','0','0'), ('2045','0','0'), ('2046','0','0'), ('2047','0','0'), ('2048','0','0'), ('2049','0','0'), ('2050','0','0'), ('2051','0','0'), ('2052','0','0'), ('2053','0','0'), ('2054','0','0'), ('2055','0','0'), ('2056','0','0'), ('2057','0','0'), ('2058','0','0'), ('2059','0','0'), ('2060','0','0'), ('2061','0','0'), ('2062','0','0'), ('2063','0','0'), ('2064','0','0'), ('2065','0','0'), ('2066','0','0'), ('2067','0','0'), ('2068','0','0'), ('2069','0','0'), ('2070','0','0'), ('2071','0','0'), ('2072','0','0'), ('2073','0','0'), ('2074','0','0'), ('2075','0','0'), ('2076','0','0'), ('2077','0','0'), ('2078','0','0'), ('2079','0','0'), ('2080','0','0'), ('2081','0','0'), ('2082','0','0'), ('2083','0','0'), ('2084','0','0'), ('2085','0','0'), ('2086','0','0'), ('2087','0','0'), ('2088','0','0'), ('2089','0','0'), ('2090','0','0'), ('2091','0','0'), ('2092','0','0'), ('2093','0','0'), ('2094','0','0'), ('2095','0','0'), ('2096','0','0'), ('2097','0','0'), ('2098','0','0'), ('2099','0','0');
INSERT INTO `ol_dalei_jf_count` VALUES ('2100','0','0'), ('2101','0','0'), ('2102','0','0'), ('2103','0','0'), ('2104','0','0'), ('2105','0','0'), ('2106','0','0'), ('2107','0','0'), ('2108','0','0'), ('2109','0','0'), ('2110','0','0'), ('2111','0','0'), ('2112','0','0'), ('2113','0','0'), ('2114','0','0'), ('2115','0','0'), ('2116','0','0'), ('2117','0','0'), ('2118','0','0'), ('2119','0','0'), ('2120','0','0'), ('2121','0','0'), ('2122','0','0'), ('2123','0','0'), ('2124','0','0'), ('2125','0','0'), ('2126','0','0'), ('2127','0','0'), ('2128','0','0'), ('2129','0','0'), ('2130','0','0'), ('2131','0','0'), ('2132','0','0'), ('2133','0','0'), ('2134','0','0'), ('2135','0','0'), ('2136','0','0'), ('2137','0','0'), ('2138','0','0'), ('2139','0','0'), ('2140','0','0'), ('2141','0','0'), ('2142','0','0'), ('2143','0','0'), ('2144','0','0'), ('2145','0','0'), ('2146','0','0'), ('2147','0','0'), ('2148','0','0'), ('2149','0','0'), ('2150','0','0'), ('2151','0','0'), ('2152','0','0'), ('2153','0','0'), ('2154','0','0'), ('2155','0','0'), ('2156','0','0'), ('2157','0','0'), ('2158','0','0'), ('2159','0','0'), ('2160','0','0'), ('2161','0','0'), ('2162','0','0'), ('2163','0','0'), ('2164','0','0'), ('2165','0','0'), ('2166','0','0'), ('2167','0','0'), ('2168','0','0'), ('2169','0','0'), ('2170','0','0'), ('2171','0','0'), ('2172','0','0'), ('2173','0','0'), ('2174','0','0'), ('2175','0','0'), ('2176','0','0'), ('2177','0','0'), ('2178','0','0'), ('2179','0','0'), ('2180','0','0'), ('2181','0','0'), ('2182','0','0'), ('2183','0','0'), ('2184','0','0'), ('2185','0','0'), ('2186','0','0'), ('2187','0','0'), ('2188','0','0'), ('2189','0','0'), ('2190','0','0'), ('2191','0','0'), ('2192','0','0'), ('2193','0','0'), ('2194','0','0'), ('2195','0','0'), ('2196','0','0'), ('2197','0','0'), ('2198','0','0'), ('2199','0','0');
INSERT INTO `ol_dalei_jf_count` VALUES ('2200','0','0'), ('2201','0','0'), ('2202','0','0'), ('2203','0','0'), ('2204','0','0'), ('2205','0','0'), ('2206','0','0'), ('2207','0','0'), ('2208','0','0'), ('2209','0','0'), ('2210','0','0'), ('2211','0','0'), ('2212','0','0'), ('2213','0','0'), ('2214','0','0'), ('2215','0','0'), ('2216','0','0'), ('2217','0','0'), ('2218','0','0'), ('2219','0','0'), ('2220','0','0'), ('2221','0','0'), ('2222','0','0'), ('2223','0','0'), ('2224','0','0'), ('2225','0','0'), ('2226','0','0'), ('2227','0','0'), ('2228','0','0'), ('2229','0','0'), ('2230','0','0'), ('2231','0','0'), ('2232','0','0'), ('2233','0','0'), ('2234','0','0'), ('2235','0','0'), ('2236','0','0'), ('2237','0','0'), ('2238','0','0'), ('2239','0','0'), ('2240','0','0'), ('2241','0','0'), ('2242','0','0'), ('2243','0','0'), ('2244','0','0'), ('2245','0','0'), ('2246','0','0'), ('2247','0','0'), ('2248','0','0'), ('2249','0','0'), ('2250','0','0'), ('2251','0','0'), ('2252','0','0'), ('2253','0','0'), ('2254','0','0'), ('2255','0','0'), ('2256','0','0'), ('2257','0','0'), ('2258','0','0'), ('2259','0','0'), ('2260','0','0'), ('2261','0','0'), ('2262','0','0'), ('2263','0','0'), ('2264','0','0'), ('2265','0','0'), ('2266','0','0'), ('2267','0','0'), ('2268','0','0'), ('2269','0','0'), ('2270','0','0'), ('2271','0','0'), ('2272','0','0'), ('2273','0','0'), ('2274','0','0'), ('2275','0','0'), ('2276','0','0'), ('2277','0','0'), ('2278','0','0'), ('2279','0','0'), ('2280','0','0'), ('2281','0','0'), ('2282','0','0'), ('2283','0','0'), ('2284','0','0'), ('2285','0','0'), ('2286','0','0'), ('2287','0','0'), ('2288','0','0'), ('2289','0','0'), ('2290','0','0'), ('2291','0','0'), ('2292','0','0'), ('2293','0','0'), ('2294','0','0'), ('2295','0','0'), ('2296','0','0'), ('2297','0','0'), ('2298','0','0'), ('2299','0','0');
INSERT INTO `ol_dalei_jf_count` VALUES ('2300','0','0'), ('2301','0','0'), ('2302','0','0'), ('2303','0','0'), ('2304','0','0'), ('2305','0','0'), ('2306','0','0'), ('2307','0','0'), ('2308','0','0'), ('2309','0','0'), ('2310','0','0'), ('2311','0','0'), ('2312','0','0'), ('2313','0','0'), ('2314','0','0'), ('2315','0','0'), ('2316','0','0'), ('2317','0','0'), ('2318','0','0'), ('2319','0','0'), ('2320','0','0'), ('2321','0','0'), ('2322','0','0'), ('2323','0','0'), ('2324','0','0'), ('2325','0','0'), ('2326','0','0'), ('2327','0','0'), ('2328','0','0'), ('2329','0','0'), ('2330','0','0'), ('2331','0','0'), ('2332','0','0'), ('2333','0','0'), ('2334','0','0'), ('2335','0','0'), ('2336','0','0'), ('2337','0','0'), ('2338','0','0'), ('2339','0','0'), ('2340','0','0'), ('2341','0','0'), ('2342','0','0'), ('2343','0','0'), ('2344','0','0'), ('2345','0','0'), ('2346','0','0'), ('2347','0','0'), ('2348','0','0'), ('2349','0','0'), ('2350','0','0'), ('2351','0','0'), ('2352','0','0'), ('2353','0','0'), ('2354','0','0'), ('2355','0','0'), ('2356','0','0'), ('2357','0','0'), ('2358','0','0'), ('2359','0','0'), ('2360','0','0'), ('2361','0','0'), ('2362','0','0'), ('2363','0','0'), ('2364','0','0'), ('2365','0','0'), ('2366','0','0'), ('2367','0','0'), ('2368','0','0'), ('2369','0','0'), ('2370','0','0'), ('2371','0','0'), ('2372','0','0'), ('2373','0','0'), ('2374','0','0'), ('2375','0','0'), ('2376','0','0'), ('2377','0','0'), ('2378','0','0'), ('2379','0','0'), ('2380','0','0'), ('2381','0','0'), ('2382','0','0'), ('2383','0','0'), ('2384','0','0'), ('2385','0','0'), ('2386','0','0'), ('2387','0','0'), ('2388','0','0'), ('2389','0','0'), ('2390','0','0'), ('2391','0','0'), ('2392','0','0'), ('2393','0','0'), ('2394','0','0'), ('2395','0','0'), ('2396','0','0'), ('2397','0','0'), ('2398','0','0'), ('2399','0','0');
INSERT INTO `ol_dalei_jf_count` VALUES ('2400','0','0'), ('2401','0','0'), ('2402','0','0'), ('2403','0','0'), ('2404','0','0'), ('2405','0','0'), ('2406','0','0'), ('2407','0','0'), ('2408','0','0'), ('2409','0','0'), ('2410','0','0'), ('2411','0','0'), ('2412','0','0'), ('2413','0','0'), ('2414','0','0'), ('2415','0','0'), ('2416','0','0'), ('2417','0','0'), ('2418','0','0'), ('2419','0','0'), ('2420','0','0'), ('2421','0','0'), ('2422','0','0'), ('2423','0','0'), ('2424','0','0'), ('2425','0','0'), ('2426','0','0'), ('2427','0','0'), ('2428','0','0'), ('2429','0','0'), ('2430','0','0'), ('2431','0','0'), ('2432','0','0'), ('2433','0','0'), ('2434','0','0'), ('2435','0','0'), ('2436','0','0'), ('2437','0','0'), ('2438','0','0'), ('2439','0','0'), ('2440','0','0'), ('2441','0','0'), ('2442','0','0'), ('2443','0','0'), ('2444','0','0'), ('2445','0','0'), ('2446','0','0'), ('2447','0','0'), ('2448','0','0'), ('2449','0','0'), ('2450','0','0'), ('2451','0','0'), ('2452','0','0'), ('2453','0','0'), ('2454','0','0'), ('2455','0','0'), ('2456','0','0'), ('2457','0','0'), ('2458','0','0'), ('2459','0','0'), ('2460','0','0'), ('2461','0','0'), ('2462','0','0'), ('2463','0','0'), ('2464','0','0'), ('2465','0','0'), ('2466','0','0'), ('2467','0','0'), ('2468','0','0'), ('2469','0','0'), ('2470','0','0'), ('2471','0','0'), ('2472','0','0'), ('2473','0','0'), ('2474','0','0'), ('2475','0','0'), ('2476','0','0'), ('2477','0','0'), ('2478','0','0'), ('2479','0','0'), ('2480','0','0'), ('2481','0','0'), ('2482','0','0'), ('2483','0','0'), ('2484','0','0'), ('2485','0','0'), ('2486','0','0'), ('2487','0','0'), ('2488','0','0'), ('2489','0','0'), ('2490','0','0'), ('2491','0','0'), ('2492','0','0'), ('2493','0','0'), ('2494','0','0'), ('2495','0','0'), ('2496','0','0'), ('2497','0','0'), ('2498','0','0'), ('2499','0','0');
INSERT INTO `ol_dalei_jf_count` VALUES ('2500','0','0'), ('2501','0','0'), ('2502','0','0'), ('2503','0','0'), ('2504','0','0'), ('2505','0','0'), ('2506','0','0'), ('2507','0','0'), ('2508','0','0'), ('2509','0','0'), ('2510','0','0'), ('2511','0','0'), ('2512','0','0'), ('2513','0','0'), ('2514','0','0'), ('2515','0','0'), ('2516','0','0'), ('2517','0','0'), ('2518','0','0'), ('2519','0','0'), ('2520','0','0'), ('2521','0','0'), ('2522','0','0'), ('2523','0','0'), ('2524','0','0'), ('2525','0','0'), ('2526','0','0'), ('2527','0','0'), ('2528','0','0'), ('2529','0','0'), ('2530','0','0'), ('2531','0','0'), ('2532','0','0'), ('2533','0','0'), ('2534','0','0'), ('2535','0','0'), ('2536','0','0'), ('2537','0','0'), ('2538','0','0'), ('2539','0','0'), ('2540','0','0'), ('2541','0','0'), ('2542','0','0'), ('2543','0','0'), ('2544','0','0'), ('2545','0','0'), ('2546','0','0'), ('2547','0','0'), ('2548','0','0'), ('2549','0','0'), ('2550','0','0'), ('2551','0','0'), ('2552','0','0'), ('2553','0','0'), ('2554','0','0'), ('2555','0','0'), ('2556','0','0'), ('2557','0','0'), ('2558','0','0'), ('2559','0','0'), ('2560','0','0'), ('2561','0','0'), ('2562','0','0'), ('2563','0','0'), ('2564','0','0'), ('2565','0','0'), ('2566','0','0'), ('2567','0','0'), ('2568','0','0'), ('2569','0','0'), ('2570','0','0'), ('2571','0','0'), ('2572','0','0'), ('2573','0','0'), ('2574','0','0'), ('2575','0','0'), ('2576','0','0'), ('2577','0','0'), ('2578','0','0'), ('2579','0','0'), ('2580','0','0'), ('2581','0','0'), ('2582','0','0'), ('2583','0','0'), ('2584','0','0'), ('2585','0','0'), ('2586','0','0'), ('2587','0','0'), ('2588','0','0'), ('2589','0','0'), ('2590','0','0'), ('2591','0','0'), ('2592','0','0'), ('2593','0','0'), ('2594','0','0'), ('2595','0','0'), ('2596','0','0'), ('2597','0','0'), ('2598','0','0'), ('2599','0','0');
INSERT INTO `ol_dalei_jf_count` VALUES ('2600','0','0'), ('2601','0','0'), ('2602','0','0'), ('2603','0','0'), ('2604','0','0'), ('2605','0','0'), ('2606','0','0'), ('2607','0','0'), ('2608','0','0'), ('2609','0','0'), ('2610','0','0'), ('2611','0','0'), ('2612','0','0'), ('2613','0','0'), ('2614','0','0'), ('2615','0','0'), ('2616','0','0'), ('2617','0','0'), ('2618','0','0'), ('2619','0','0'), ('2620','0','0'), ('2621','0','0'), ('2622','0','0'), ('2623','0','0'), ('2624','0','0'), ('2625','0','0'), ('2626','0','0'), ('2627','0','0'), ('2628','0','0'), ('2629','0','0'), ('2630','0','0'), ('2631','0','0'), ('2632','0','0'), ('2633','0','0'), ('2634','0','0'), ('2635','0','0'), ('2636','0','0'), ('2637','0','0'), ('2638','0','0'), ('2639','0','0'), ('2640','0','0'), ('2641','0','0'), ('2642','0','0'), ('2643','0','0'), ('2644','0','0'), ('2645','0','0'), ('2646','0','0'), ('2647','0','0'), ('2648','0','0'), ('2649','0','0'), ('2650','0','0'), ('2651','0','0'), ('2652','0','0'), ('2653','0','0'), ('2654','0','0'), ('2655','0','0'), ('2656','0','0'), ('2657','0','0'), ('2658','0','0'), ('2659','0','0'), ('2660','0','0'), ('2661','0','0'), ('2662','0','0'), ('2663','0','0'), ('2664','0','0'), ('2665','0','0'), ('2666','0','0'), ('2667','0','0'), ('2668','0','0'), ('2669','0','0'), ('2670','0','0'), ('2671','0','0'), ('2672','0','0'), ('2673','0','0'), ('2674','0','0'), ('2675','0','0'), ('2676','0','0'), ('2677','0','0'), ('2678','0','0'), ('2679','0','0'), ('2680','0','0'), ('2681','0','0'), ('2682','0','0'), ('2683','0','0'), ('2684','0','0'), ('2685','0','0'), ('2686','0','0'), ('2687','0','0'), ('2688','0','0'), ('2689','0','0'), ('2690','0','0'), ('2691','0','0'), ('2692','0','0'), ('2693','0','0'), ('2694','0','0'), ('2695','0','0'), ('2696','0','0'), ('2697','0','0'), ('2698','0','0'), ('2699','0','0');
INSERT INTO `ol_dalei_jf_count` VALUES ('2700','0','0'), ('2701','0','0'), ('2702','0','0'), ('2703','0','0'), ('2704','0','0'), ('2705','0','0'), ('2706','0','0'), ('2707','0','0'), ('2708','0','0'), ('2709','0','0'), ('2710','0','0'), ('2711','0','0'), ('2712','0','0'), ('2713','0','0'), ('2714','0','0'), ('2715','0','0'), ('2716','0','0'), ('2717','0','0'), ('2718','0','0'), ('2719','0','0'), ('2720','0','0'), ('2721','0','0'), ('2722','0','0'), ('2723','0','0'), ('2724','0','0'), ('2725','0','0'), ('2726','0','0'), ('2727','0','0'), ('2728','0','0'), ('2729','0','0'), ('2730','0','0'), ('2731','0','0'), ('2732','0','0'), ('2733','0','0'), ('2734','0','0'), ('2735','0','0'), ('2736','0','0'), ('2737','0','0'), ('2738','0','0'), ('2739','0','0'), ('2740','0','0'), ('2741','0','0'), ('2742','0','0'), ('2743','0','0'), ('2744','0','0'), ('2745','0','0'), ('2746','0','0'), ('2747','0','0'), ('2748','0','0'), ('2749','0','0'), ('2750','0','0'), ('2751','0','0'), ('2752','0','0'), ('2753','0','0'), ('2754','0','0'), ('2755','0','0'), ('2756','0','0'), ('2757','0','0'), ('2758','0','0'), ('2759','0','0'), ('2760','0','0'), ('2761','0','0'), ('2762','0','0'), ('2763','0','0'), ('2764','0','0'), ('2765','0','0'), ('2766','0','0'), ('2767','0','0'), ('2768','0','0'), ('2769','0','0'), ('2770','0','0'), ('2771','0','0'), ('2772','0','0'), ('2773','0','0'), ('2774','0','0'), ('2775','0','0'), ('2776','0','0'), ('2777','0','0'), ('2778','0','0'), ('2779','0','0'), ('2780','0','0'), ('2781','0','0'), ('2782','0','0'), ('2783','0','0'), ('2784','0','0'), ('2785','0','0'), ('2786','0','0'), ('2787','0','0'), ('2788','0','0'), ('2789','0','0'), ('2790','0','0'), ('2791','0','0'), ('2792','0','0'), ('2793','0','0'), ('2794','0','0'), ('2795','0','0'), ('2796','0','0'), ('2797','0','0'), ('2798','0','0'), ('2799','0','0');
INSERT INTO `ol_dalei_jf_count` VALUES ('2800','0','0'), ('2801','0','0'), ('2802','0','0'), ('2803','0','0'), ('2804','0','0'), ('2805','0','0'), ('2806','0','0'), ('2807','0','0'), ('2808','0','0'), ('2809','0','0'), ('2810','0','0'), ('2811','0','0'), ('2812','0','0'), ('2813','0','0'), ('2814','0','0'), ('2815','0','0'), ('2816','0','0'), ('2817','0','0'), ('2818','0','0'), ('2819','0','0'), ('2820','0','0'), ('2821','0','0'), ('2822','0','0'), ('2823','0','0'), ('2824','0','0'), ('2825','0','0'), ('2826','0','0'), ('2827','0','0'), ('2828','0','0'), ('2829','0','0'), ('2830','0','0'), ('2831','0','0'), ('2832','0','0'), ('2833','0','0'), ('2834','0','0'), ('2835','0','0'), ('2836','0','0'), ('2837','0','0'), ('2838','0','0'), ('2839','0','0'), ('2840','0','0'), ('2841','0','0'), ('2842','0','0'), ('2843','0','0'), ('2844','0','0'), ('2845','0','0'), ('2846','0','0'), ('2847','0','0'), ('2848','0','0'), ('2849','0','0'), ('2850','0','0'), ('2851','0','0'), ('2852','0','0'), ('2853','0','0'), ('2854','0','0'), ('2855','0','0'), ('2856','0','0'), ('2857','0','0'), ('2858','0','0'), ('2859','0','0'), ('2860','0','0'), ('2861','0','0'), ('2862','0','0'), ('2863','0','0'), ('2864','0','0'), ('2865','0','0'), ('2866','0','0'), ('2867','0','0'), ('2868','0','0'), ('2869','0','0'), ('2870','0','0'), ('2871','0','0'), ('2872','0','0'), ('2873','0','0'), ('2874','0','0'), ('2875','0','0'), ('2876','0','0'), ('2877','0','0'), ('2878','0','0'), ('2879','0','0'), ('2880','0','0'), ('2881','0','0'), ('2882','0','0'), ('2883','0','0'), ('2884','0','0'), ('2885','0','0'), ('2886','0','0'), ('2887','0','0'), ('2888','0','0'), ('2889','0','0'), ('2890','0','0'), ('2891','0','0'), ('2892','0','0'), ('2893','0','0'), ('2894','0','0'), ('2895','0','0'), ('2896','0','0'), ('2897','0','0'), ('2898','0','0'), ('2899','0','0');
INSERT INTO `ol_dalei_jf_count` VALUES ('2900','0','0'), ('2901','0','0'), ('2902','0','0'), ('2903','0','0'), ('2904','0','0'), ('2905','0','0'), ('2906','0','0'), ('2907','0','0'), ('2908','0','0'), ('2909','0','0'), ('2910','0','0'), ('2911','0','0'), ('2912','0','0'), ('2913','0','0'), ('2914','0','0'), ('2915','0','0'), ('2916','0','0'), ('2917','0','0'), ('2918','0','0'), ('2919','0','0'), ('2920','0','0'), ('2921','0','0'), ('2922','0','0'), ('2923','0','0'), ('2924','0','0'), ('2925','0','0'), ('2926','0','0'), ('2927','0','0'), ('2928','0','0'), ('2929','0','0'), ('2930','0','0'), ('2931','0','0'), ('2932','0','0'), ('2933','0','0'), ('2934','0','0'), ('2935','0','0'), ('2936','0','0'), ('2937','0','0'), ('2938','0','0'), ('2939','0','0'), ('2940','0','0'), ('2941','0','0'), ('2942','0','0'), ('2943','0','0'), ('2944','0','0'), ('2945','0','0'), ('2946','0','0'), ('2947','0','0'), ('2948','0','0'), ('2949','0','0'), ('2950','0','0'), ('2951','0','0'), ('2952','0','0'), ('2953','0','0'), ('2954','0','0'), ('2955','0','0'), ('2956','0','0'), ('2957','0','0'), ('2958','0','0'), ('2959','0','0'), ('2960','0','0'), ('2961','0','0'), ('2962','0','0'), ('2963','0','0'), ('2964','0','0'), ('2965','0','0'), ('2966','0','0'), ('2967','0','0'), ('2968','0','0'), ('2969','0','0'), ('2970','0','0'), ('2971','0','0'), ('2972','0','0'), ('2973','0','0'), ('2974','0','0'), ('2975','0','0'), ('2976','0','0'), ('2977','0','0'), ('2978','0','0'), ('2979','0','0'), ('2980','0','0'), ('2981','0','0'), ('2982','0','0'), ('2983','0','0'), ('2984','0','0'), ('2985','0','0'), ('2986','0','0'), ('2987','0','0'), ('2988','0','0'), ('2989','0','0'), ('2990','0','0'), ('2991','0','0'), ('2992','0','0'), ('2993','0','0'), ('2994','0','0'), ('2995','0','0'), ('2996','0','0'), ('2997','0','0'), ('2998','0','0'), ('2999','0','0');
INSERT INTO `ol_dalei_jf_count` VALUES ('3000','0','0');
INSERT INTO `ol_game_use` VALUES ('1','1','2','0','1','0','1','0','0','2'), ('1','1','1','0','2','0','1','1','0','3'), ('1','1','1','0','3','0','1','3','0','4'), ('1','1','7','0','1','0','2','0','0','5'), ('1','1','1','0','4','0','1','5','0','6'), ('1','1','1','0','5','0','1','10','0','7'), ('1','1','1','0','1','0','1','0','0','8'), ('1','1','2','0','2','0','1','1','0','9'), ('1','1','2','0','3','0','1','3','0','10'), ('1','1','2','0','4','0','1','5','0','11'), ('1','1','2','0','5','0','1','10','0','12'), ('1','1','3','0','1','0','1','0','0','13'), ('1','1','3','0','2','0','1','1','0','14'), ('1','1','3','0','3','0','1','3','0','15'), ('1','1','3','0','4','0','1','5','0','16'), ('1','1','3','0','5','0','1','10','0','17'), ('1','1','4','0','1','0','1','0','0','18'), ('1','1','4','0','2','0','1','1','0','19'), ('1','1','4','0','3','0','1','3','0','20'), ('1','1','4','0','4','0','1','5','0','21'), ('1','1','4','0','5','0','1','10','0','22'), ('1','1','5','0','1','0','2','0','0','23'), ('1','1','5','0','2','0','2','2','0','24'), ('1','1','5','0','3','0','2','5','0','25'), ('1','1','5','0','4','0','2','9','0','26'), ('1','1','5','0','5','0','2','14','0','27'), ('1','1','6','0','1','0','2','0','0','28'), ('1','1','6','0','2','0','2','2','0','29'), ('1','1','6','0','3','0','2','5','0','30'), ('1','1','6','0','4','0','2','9','0','31'), ('1','1','6','0','5','0','2','14','0','32'), ('1','1','7','0','2','0','2','2','0','33'), ('1','1','7','0','3','0','2','5','0','34'), ('1','1','7','0','4','0','2','9','0','35'), ('1','1','7','0','5','0','2','14','0','36'), ('1','1','8','0','1','0','2','0','0','37'), ('1','1','8','0','2','0','2','2','0','38'), ('1','1','8','0','3','0','2','5','0','39'), ('1','1','8','0','4','0','2','9','0','40'), ('1','1','8','0','5','0','2','14','0','41'), ('1','2','1','0','1','0','3','0','0','42'), ('1','2','1','0','2','0','3','3','0','43'), ('1','2','1','0','3','0','3','7','0','44'), ('1','2','1','0','4','0','3','12','0','45'), ('1','2','1','0','5','0','3','18','0','46'), ('1','2','2','0','1','0','3','0','0','47'), ('1','2','2','0','2','0','3','3','0','48'), ('1','2','2','0','3','0','3','7','0','49'), ('1','2','2','0','4','0','3','12','0','50'), ('1','2','2','0','5','0','3','18','0','51'), ('1','2','3','0','1','0','3','0','0','52'), ('1','2','3','0','2','0','3','3','0','53'), ('1','2','3','0','3','0','3','7','0','54'), ('1','2','3','0','4','0','3','12','0','55'), ('1','2','3','0','5','0','3','18','0','56'), ('1','2','4','0','1','0','3','0','0','57'), ('1','2','4','0','2','0','3','3','0','58'), ('1','2','4','0','3','0','3','7','0','59'), ('1','2','4','0','4','0','3','12','0','60'), ('1','2','4','0','5','0','3','18','0','61'), ('1','2','5','0','1','0','4','0','0','62'), ('1','2','5','0','2','0','4','4','0','63'), ('1','2','5','0','3','0','4','9','0','64'), ('1','2','5','0','4','0','4','15','0','65'), ('1','2','5','0','5','0','4','22','0','66'), ('1','2','6','0','1','0','4','0','0','67'), ('1','2','6','0','2','0','4','4','0','68'), ('1','2','6','0','3','0','4','9','0','69'), ('1','2','6','0','4','0','4','15','0','70'), ('1','2','6','0','5','0','4','22','0','71'), ('1','2','7','0','1','0','4','0','0','72'), ('1','2','7','0','2','0','4','4','0','73'), ('1','2','7','0','3','0','4','9','0','74'), ('1','2','7','0','4','0','4','15','0','75'), ('1','2','7','0','5','0','4','22','0','76'), ('1','2','8','0','1','0','4','0','0','77'), ('1','2','8','0','2','0','4','4','0','78'), ('1','2','8','0','3','0','4','9','0','79'), ('1','2','8','0','4','0','4','15','0','80'), ('1','2','8','0','5','0','4','22','0','81'), ('1','3','1','0','1','0','5','0','0','82'), ('1','3','1','0','2','0','5','5','0','83'), ('1','3','1','0','3','0','5','11','0','84'), ('1','3','1','0','4','0','5','18','0','85'), ('1','3','1','0','5','0','5','26','0','86'), ('1','3','2','0','1','0','5','0','0','87'), ('1','3','2','0','2','0','5','5','0','88'), ('1','3','2','0','3','0','5','11','0','89'), ('1','3','2','0','4','0','5','18','0','90'), ('1','3','2','0','5','0','5','26','0','91'), ('1','3','3','0','1','0','5','0','0','92'), ('1','3','3','0','2','0','5','5','0','93'), ('1','3','3','0','3','0','5','11','0','94'), ('1','3','3','0','4','0','5','18','0','95'), ('1','3','3','0','5','0','5','26','0','96'), ('1','3','4','0','1','0','5','0','0','97'), ('1','3','4','0','2','0','5','5','0','98'), ('1','3','4','0','3','0','5','11','0','99'), ('1','3','4','0','4','0','5','18','0','100'), ('1','3','4','0','5','0','5','26','0','101');
INSERT INTO `ol_game_use` VALUES ('1','3','5','0','1','0','6','0','0','102'), ('1','3','5','0','2','0','6','6','0','103'), ('1','3','5','0','3','0','6','13','0','104'), ('1','3','5','0','4','0','6','21','0','105'), ('1','3','5','0','5','0','6','30','0','106'), ('1','3','6','0','1','0','6','0','0','107'), ('1','3','6','0','2','0','6','6','0','108'), ('1','3','6','0','3','0','6','13','0','109'), ('1','3','6','0','4','0','6','21','0','110'), ('1','3','6','0','5','0','6','30','0','111'), ('1','3','7','0','1','0','6','0','0','112'), ('1','3','7','0','2','0','6','6','0','113'), ('1','3','7','0','3','0','6','13','0','114'), ('1','3','7','0','4','0','6','21','0','115'), ('1','3','7','0','5','0','6','30','0','116'), ('1','3','8','0','1','0','6','0','0','117'), ('1','3','8','0','2','0','6','6','0','118'), ('1','3','8','0','3','0','6','13','0','119'), ('1','3','8','0','4','0','6','21','0','120'), ('1','3','8','0','5','0','6','30','0','121'), ('2','1','1','0','0','0','10','0','1','122'), ('2','1','2','0','0','0','10','0','1','123'), ('2','1','3','0','0','0','10','0','1','124'), ('2','1','4','0','0','0','10','0','1','125'), ('2','1','5','0','0','0','20','0','2','126'), ('2','1','6','0','0','0','20','0','2','127'), ('2','1','7','0','0','0','20','0','2','128'), ('2','1','8','0','0','0','20','0','2','129'), ('2','2','1','0','0','0','30','0','3','130'), ('2','2','2','0','0','0','30','0','3','131'), ('2','2','3','0','0','0','30','0','3','132'), ('2','2','4','0','0','0','30','0','3','133'), ('2','2','5','0','0','0','40','0','4','134'), ('2','2','6','0','0','0','40','0','4','135'), ('2','2','7','0','0','0','40','0','4','136'), ('2','2','8','0','0','0','40','0','4','137'), ('2','3','1','0','0','0','50','0','5','138'), ('2','3','2','0','0','0','50','0','5','139'), ('2','3','3','0','0','0','50','0','5','140'), ('2','3','4','0','0','0','50','0','5','141'), ('2','3','5','0','0','0','60','0','6','142'), ('2','3','6','0','0','0','60','0','6','143'), ('2','3','7','0','0','0','60','0','6','144'), ('2','3','8','0','0','0','60','0','6','145'), ('1','4','1','0','1','0','8','0','0','146'), ('1','4','1','0','2','0','8','8','0','147'), ('1','4','1','0','3','0','8','18','0','148'), ('1','4','1','0','4','0','8','30','0','149'), ('1','4','1','0','5','0','8','44','0','150'), ('1','4','2','0','1','0','8','0','0','151'), ('1','4','2','0','2','0','8','8','0','152'), ('1','4','2','0','3','0','8','18','0','153'), ('1','4','2','0','4','0','8','30','0','154'), ('1','4','2','0','5','0','8','44','0','155'), ('1','4','3','0','1','0','8','0','0','156'), ('1','4','3','0','2','0','8','8','0','157'), ('1','4','3','0','3','0','8','18','0','158'), ('1','4','3','0','4','0','8','30','0','159'), ('1','4','3','0','5','0','8','44','0','160'), ('1','4','4','0','1','0','8','0','0','161'), ('1','4','4','0','2','0','8','8','0','162'), ('1','4','4','0','3','0','8','18','0','163'), ('1','4','4','0','4','0','8','30','0','164'), ('1','4','4','0','5','0','8','44','0','165'), ('1','4','5','0','1','0','10','0','0','166'), ('1','4','5','0','2','0','10','10','0','167'), ('1','4','5','0','3','0','10','22','0','168'), ('1','4','5','0','4','0','10','36','0','169'), ('1','4','5','0','5','0','10','52','0','170'), ('1','4','6','0','1','0','10','0','0','171'), ('1','4','6','0','2','0','10','10','0','172'), ('1','4','6','0','3','0','10','22','0','173'), ('1','4','6','0','4','0','10','36','0','174'), ('1','4','6','0','5','0','10','52','0','175'), ('1','4','7','0','1','0','10','0','0','176'), ('1','4','7','0','2','0','10','10','0','177'), ('1','4','7','0','3','0','10','22','0','178'), ('1','4','7','0','4','0','10','36','0','179'), ('1','4','7','0','5','0','10','52','0','180'), ('1','4','8','0','1','0','10','0','0','181'), ('1','4','8','0','2','0','10','10','0','182'), ('1','4','8','0','3','0','10','22','0','183'), ('1','4','8','0','4','0','10','36','0','184'), ('1','4','8','0','5','0','10','52','0','185'), ('1','5','1','0','1','0','14','0','0','186'), ('1','5','1','0','2','0','14','15','0','187'), ('1','5','1','0','3','0','14','33','0','188'), ('1','5','1','0','4','0','14','55','0','189'), ('1','5','1','0','5','0','14','85','0','190'), ('1','5','2','0','1','0','14','0','0','191'), ('1','5','2','0','2','0','14','15','0','192'), ('1','5','2','0','3','0','14','33','0','193'), ('1','5','2','0','4','0','14','55','0','194'), ('1','5','2','0','5','0','14','85','0','195'), ('1','5','3','0','1','0','14','0','0','196'), ('1','5','3','0','2','0','14','15','0','197'), ('1','5','3','0','3','0','14','33','0','198'), ('1','5','3','0','4','0','14','55','0','199'), ('1','5','3','0','5','0','14','85','0','200'), ('1','5','4','0','1','0','14','0','0','201');
INSERT INTO `ol_game_use` VALUES ('1','5','4','0','2','0','14','15','0','202'), ('1','5','4','0','3','0','14','33','0','203'), ('1','5','4','0','4','0','14','55','0','204'), ('1','5','4','0','5','0','14','85','0','205'), ('1','5','5','0','1','0','14','0','0','206'), ('1','5','5','0','2','0','14','15','0','207'), ('1','5','5','0','3','0','14','33','0','208'), ('1','5','5','0','4','0','14','55','0','209'), ('1','5','5','0','5','0','14','85','0','210'), ('1','5','6','0','1','0','14','0','0','211'), ('1','5','6','0','2','0','14','15','0','212'), ('1','5','6','0','3','0','14','33','0','213'), ('1','5','6','0','4','0','14','55','0','214'), ('1','5','6','0','5','0','14','85','0','215'), ('1','5','7','0','1','0','14','0','0','216'), ('1','5','7','0','2','0','14','15','0','217'), ('1','5','7','0','3','0','14','33','0','218'), ('1','5','7','0','4','0','14','55','0','219'), ('1','5','7','0','5','0','14','85','0','220'), ('1','5','8','0','1','0','14','0','0','221'), ('1','5','8','0','2','0','14','15','0','222'), ('1','5','8','0','3','0','14','33','0','223'), ('1','5','8','0','4','0','14','55','0','224'), ('1','5','8','0','5','0','14','85','0','225'), ('1','6','1','0','1','0','18','0','0','226'), ('1','6','1','0','2','0','18','20','0','227'), ('1','6','1','0','3','0','18','46','0','228'), ('1','6','1','0','4','0','18','78','0','229'), ('1','6','1','0','5','0','18','118','0','230'), ('1','6','2','0','1','0','18','0','0','231'), ('1','6','2','0','2','0','18','20','0','232'), ('1','6','2','0','3','0','18','46','0','233'), ('1','6','2','0','4','0','18','78','0','234'), ('1','6','2','0','5','0','18','118','0','235'), ('1','6','3','0','1','0','18','0','0','236'), ('1','6','3','0','2','0','18','20','0','237'), ('1','6','3','0','3','0','18','46','0','238'), ('1','6','3','0','4','0','18','78','0','239'), ('1','6','3','0','5','0','18','118','0','240'), ('1','6','4','0','1','0','18','0','0','241'), ('1','6','4','0','2','0','18','20','0','242'), ('1','6','4','0','3','0','18','46','0','243'), ('1','6','4','0','4','0','18','78','0','244'), ('1','6','4','0','5','0','18','118','0','245'), ('1','6','5','0','1','0','18','0','0','246'), ('1','6','5','0','2','0','18','20','0','247'), ('1','6','5','0','3','0','18','46','0','248'), ('1','6','5','0','4','0','18','78','0','249'), ('1','6','5','0','5','0','18','118','0','250'), ('1','6','6','0','1','0','18','0','0','251'), ('1','6','6','0','2','0','18','20','0','252'), ('1','6','6','0','3','0','18','46','0','253'), ('1','6','6','0','4','0','18','78','0','254'), ('1','6','6','0','5','0','18','118','0','255'), ('1','6','7','0','1','0','18','0','0','256'), ('1','6','7','0','2','0','18','20','0','257'), ('1','6','7','0','3','0','18','46','0','258'), ('1','6','7','0','4','0','18','78','0','259'), ('1','6','7','0','5','0','18','118','0','260'), ('1','6','8','0','1','0','18','0','0','261'), ('1','6','8','0','2','0','18','20','0','262'), ('1','6','8','0','3','0','18','46','0','263'), ('1','6','8','0','4','0','18','78','0','264'), ('1','6','8','0','5','0','18','118','0','265'), ('1','5','1','0','1','0','14','0','0','266'), ('1','5','1','0','2','0','14','15','0','267'), ('1','5','1','0','3','0','14','33','0','268'), ('1','5','1','0','4','0','14','55','0','269'), ('1','5','1','0','5','0','14','85','0','270'), ('1','5','2','0','1','0','14','0','0','271'), ('1','5','2','0','2','0','14','15','0','272'), ('1','5','2','0','3','0','14','33','0','273'), ('1','5','2','0','4','0','14','55','0','274'), ('1','5','2','0','5','0','14','85','0','275'), ('1','5','3','0','1','0','14','0','0','276'), ('1','5','3','0','2','0','14','15','0','277'), ('1','5','3','0','3','0','14','33','0','278'), ('1','5','3','0','4','0','14','55','0','279'), ('1','5','3','0','5','0','14','85','0','280'), ('1','5','4','0','1','0','14','0','0','281'), ('1','5','4','0','2','0','14','15','0','282'), ('1','5','4','0','3','0','14','33','0','283'), ('1','5','4','0','4','0','14','55','0','284'), ('1','5','4','0','5','0','14','85','0','285'), ('1','5','5','0','1','0','14','0','0','286'), ('1','5','5','0','2','0','14','15','0','287'), ('1','5','5','0','3','0','14','33','0','288'), ('1','5','5','0','4','0','14','55','0','289'), ('1','5','5','0','5','0','14','85','0','290'), ('1','5','6','0','1','0','14','0','0','291'), ('1','5','6','0','2','0','14','15','0','292'), ('1','5','6','0','3','0','14','33','0','293'), ('1','5','6','0','4','0','14','55','0','294'), ('1','5','6','0','5','0','14','85','0','295'), ('1','5','7','0','1','0','14','0','0','296'), ('1','5','7','0','2','0','14','15','0','297'), ('1','5','7','0','3','0','14','33','0','298'), ('1','5','7','0','4','0','14','55','0','299'), ('1','5','7','0','5','0','14','85','0','300'), ('1','5','8','0','1','0','14','0','0','301');
INSERT INTO `ol_game_use` VALUES ('1','5','8','0','2','0','14','15','0','302'), ('1','5','8','0','3','0','14','33','0','303'), ('1','5','8','0','4','0','14','55','0','304'), ('1','5','8','0','5','0','14','85','0','305');
INSERT INTO `ol_login_award` VALUES ('1','[0,2,0,0,0,0,0,0]','2013-10-26','1','1');
INSERT INTO `ol_monster_bak` VALUES ('1','40111','1','店小二','1','0','1','21','21','25','17','10','0','1','NPC_Icon1'), ('2','40121','1','药贩子','2','0','1','21','21','25','17','10','0','1','NPC_Icon2'), ('3','40131','1','潘金莲','2','0','3','21','13','21','29','10','0','1','NPC_Icon3'), ('4','40141','1','西门庆','3','0','2','28','19','94','19','10','0','1','NPC_Icon4'), ('5','40211','1','伙计','3','0','2','32','21','32','21','10','0','1','NPC_Icon5'), ('6','40221','1','镇关西','4','0','3','26','16','26','37','10','0','1','NPC_Icon6'), ('7','40231','1','打手','4','0','1','26','26','32','21','10','0','1','NPC_Icon7'), ('8','40241','1','蒋门神','5','0','2','42','28','198','28','10','0','1','NPC_Icon8'), ('9','40311','1','大厨','5','0','2','64','43','64','43','10','0','1','NPC_Icon9'), ('10','40311','2','洗菜员','5','0','1','53','53','64','43','10','0','1','YX041'), ('11','40321','1','管事','6','0','3','58','35','58','81','10','0','1','NPC_Icon10'), ('12','40321','2','佣人','6','0','2','70','47','70','47','10','0','1','YX024'), ('13','40331','1','张团练','7','0','1','63','63','76','51','10','0','1','NPC_Icon11'), ('14','40331','2','小陪练','7','0','4','76','38','76','63','10','0','1','YX027'), ('15','40341','1','张都监','7','0','1','82','82','266','65','10','0','1','NPC_Icon12'), ('16','40341','2','张秘书','7','0','3','82','49','362','114','10','0','1','YX017'), ('17','40411','1','刘老汉','8','0','2','97','65','57','65','10','0','1','NPC_Icon13'), ('18','40411','2','小儿子','8','0','1','81','81','57','65','10','0','1','YX027'), ('19','40421','1','刘夫人','8','0','1','81','81','57','65','10','0','1','NPC_Icon14'), ('20','40421','2','邻居','8','0','2','97','65','57','65','10','0','1','YX011'), ('21','40431','1','镇三山','9','0','1','86','86','61','69','10','0','1','NPC_Icon15'), ('22','40431','2','黑兄弟','9','0','4','103','52','61','86','10','0','1','YX040'), ('23','40441','1','刘高','9','0','1','98','98','272','79','10','0','1','NPC_Icon16'), ('24','40441','2','打手','9','0','2','118','79','328','79','10','0','1','NPC_Icon7'), ('25','40511','1','士兵','10','0','2','111','74','74','74','10','0','1','NPC_Icon17'), ('26','40511','2','士兵甲','10','0','1','92','92','74','74','10','0','1','YX004'), ('27','40511','3','士兵乙','10','0','1','92','92','74','74','10','0','1','YX006'), ('28','40521','1','黄通判','10','0','2','111','74','74','74','10','0','1','NPC_Icon18'), ('29','40521','2','传令员','10','0','4','111','56','74','92','10','0','1','YX027'), ('30','40521','3','士兵甲','10','0','1','92','92','74','74','10','0','1','YX004'), ('31','40531','1','监斩官','11','0','4','117','59','78','98','10','0','1','NPC_Icon19'), ('32','40531','2','行刑员','11','0','2','117','78','78','78','10','0','1','YX012'), ('33','40531','3','士兵甲','11','0','1','98','98','78','78','10','0','1','YX004'), ('34','40541','1','蔡九','11','0','1','107','107','257','86','10','0','1','NPC_Icon20'), ('35','40541','2','芹蔡','11','0','2','129','86','257','86','10','0','1','YX041'), ('36','40541','3','白蔡','11','0','4','129','65','257','107','10','0','1','YX025'), ('37','40611','1','李应','12','0','4','134','67','267','112','10','0','1','NPC_Icon21'), ('38','40611','2','打手','12','0','1','112','112','267','89','10','0','1','NPC_Icon7'), ('39','40611','3','帮手','12','0','2','134','89','267','89','10','0','1','YX044'), ('40','40621','1','扈三娘','13','0','4','141','71','281','117','10','0','1','NPC_Icon22'), ('41','40621','2','帮手','13','0','2','141','94','281','94','10','0','1','YX044'), ('42','40621','3','凶手','13','0','4','141','71','281','117','10','0','1','YX029'), ('43','40631','1','祝家三虎','13','0','4','141','71','281','117','10','0','1','NPC_Icon23'), ('44','40631','2','打手','13','0','4','141','71','281','117','10','0','1','NPC_Icon7'), ('45','40631','3','凶手','13','0','2','141','94','281','94','10','0','1','YX029'), ('46','40641','1','祝庄主','14','0','1','135','135','454','108','10','0','1','NPC_Icon24'), ('47','40641','2','打手','14','0','2','162','108','454','108','10','0','1','NPC_Icon7'), ('48','40641','3','杀手','14','0','4','162','81','454','135','10','0','1','YX026'), ('49','40711','1','秀才','14','0','3','136','82','271','190','10','0','1','NPC_Icon25'), ('50','40711','2','黄师爷','14','0','1','136','136','326','109','10','0','1','YX033'), ('51','40711','3','秀才甲','14','0','2','163','109','326','109','10','0','1','YX035'), ('52','40721','1','衙役','15','0','1','141','141','339','113','10','0','1','NPC_Icon26'), ('53','40721','2','班头','15','0','2','170','113','339','113','10','0','1','BJ005'), ('54','40721','3','捕役甲','15','0','4','170','85','339','141','10','0','1','BJ001'), ('55','40731','1','王太守','15','0','4','170','85','339','141','10','0','1','NPC_Icon27'), ('56','40731','2','捕快甲','15','0','1','141','141','339','113','10','0','1','BJ001'), ('57','40731','3','捕快乙','15','0','2','170','113','339','113','10','0','1','BJ002'), ('58','40741','1','梁中书','16','0','1','162','162','545','129','10','0','1','NPC_Icon28'), ('59','40741','2','杨提辖','16','0','2','194','129','545','129','10','0','1','YX022'), ('60','40741','3','张提辖','16','0','4','194','97','545','162','10','0','1','YX015'), ('61','40811','1','报名官','16','0','4','193','97','385','161','10','0','1','NPC_Icon29'), ('62','40811','2','协会长','16','0','1','161','161','385','129','10','0','1','YX023'), ('63','40811','3','协会员','16','0','2','193','129','385','129','10','0','1','YX022'), ('64','40811','4','蹴鞠员','16','0','3','161','97','321','225','10','0','1','YX010'), ('65','40821','1','陆谦','17','0','1','166','166','399','133','10','0','1','NPC_Icon30'), ('66','40821','2','协会长','17','0','2','200','133','399','133','10','0','1','YX023'), ('67','40821','3','协会员','17','0','3','166','100','332','233','10','0','1','YX022'), ('68','40821','4','蹴鞠员','17','0','4','200','100','399','166','10','0','1','YX010'), ('69','40831','1','高衙内','17','0','1','166','166','399','133','10','0','1','NPC_Icon31'), ('70','40831','2','协会长','17','0','2','200','133','399','133','10','0','1','YX023'), ('71','40831','3','协会官','17','0','3','166','100','332','233','10','0','1','YX027'), ('72','40831','4','蹴鞠员','17','0','4','200','100','399','166','10','0','1','YX010'), ('73','40841','1','高俅','18','0','1','189','189','638','151','10','0','1','NPC_Icon32'), ('74','40841','2','协会长','18','0','2','227','151','638','151','10','0','1','YX023'), ('75','40841','3','协会员','18','0','3','189','114','532','265','10','0','1','YX022'), ('76','40841','4','蹴鞠员','18','0','4','227','114','638','189','10','0','1','YX010'), ('77','40911','1','店小二','19','0','4','225','113','449','187','10','0','1','NPC_Icon1'), ('78','40911','2','店小三','19','0','4','225','113','449','187','10','0','1','YX029'), ('79','40911','3','店小四','19','0','4','225','113','449','187','10','0','1','YX034'), ('80','40911','4','店小五','19','0','3','187','113','374','262','10','0','1','YX037'), ('81','40921','1','药贩子','19','0','4','225','113','449','187','10','0','1','NPC_Icon2'), ('82','40921','2','二把手','19','0','4','225','113','449','187','10','0','1','YX032'), ('83','40921','3','三把手','19','0','2','225','150','449','150','10','0','1','YX041'), ('84','40921','4','四把手','19','0','4','225','113','449','187','10','0','1','YX007'), ('85','40931','1','潘金莲','20','0','2','232','155','463','155','10','0','1','NPC_Icon3'), ('86','40931','2','银莲','20','0','4','232','116','463','193','10','0','1','YX029'), ('87','40931','3','翠莲','20','0','4','232','116','463','193','10','0','1','YX030'), ('88','40931','4','花莲','20','0','1','193','193','463','155','10','0','1','BJ006'), ('89','40941','1','西门庆','20','0','3','213','128','598','297','10','0','1','NPC_Icon4'), ('90','40941','2','打手','20','0','4','255','128','718','213','10','0','1','NPC_Icon7'), ('91','40941','3','杀手','20','0','4','255','128','718','213','10','0','1','YX026'), ('92','40941','4','帮手','20','0','4','255','128','718','213','10','0','1','YX044'), ('93','41011','1','伙计','21','0','4','238','119','476','199','10','0','1','NPC_Icon5'), ('94','41011','2','二伙计','21','0','2','238','159','476','159','10','0','1','YX020'), ('95','41011','3','三伙计','21','0','4','238','119','476','199','10','0','1','YX020'), ('96','41011','4','四伙计','21','0','2','238','159','476','159','10','0','1','YX001'), ('97','41021','1','镇关西','21','0','3','199','119','397','278','10','0','1','NPC_Icon6'), ('98','41021','2','小徒弟','21','0','2','238','159','476','159','10','0','1','YX034'), ('99','41021','3','二徒弟','21','0','4','238','119','476','199','10','0','1','YX025'), ('100','41021','4','三徒弟','21','0','4','238','119','476','199','10','0','1','YX008');
INSERT INTO `ol_monster_bak` VALUES ('101','41031','1','打手','22','0','4','246','123','492','205','10','0','1','NPC_Icon7'), ('102','41031','2','杀手','22','0','2','246','164','492','164','10','0','1','YX026'), ('103','41031','3','凶手','22','0','2','246','164','492','164','10','0','1','YX029'), ('104','41031','4','助手','22','0','4','246','123','492','205','10','0','1','YX038'), ('105','41041','1','蒋门神','22','0','4','271','136','762','226','10','0','1','NPC_Icon8'), ('106','41041','2','打手','22','0','3','226','136','635','316','10','0','1','NPC_Icon7'), ('107','41041','3','杀手','22','0','3','226','136','635','316','10','0','1','YX026'), ('108','41041','4','凶手','22','0','3','226','136','635','316','10','0','1','YX029'), ('109','41111','1','大厨','23','0','4','324','162','647','270','10','0','1','NPC_Icon9'), ('110','41111','2','大洗菜员','23','0','4','324','162','647','270','10','0','1','YX041'), ('111','41111','3','二洗菜员','23','0','4','324','162','647','270','10','0','1','YX015'), ('112','41111','4','三洗菜员','23','0','1','270','270','647','216','10','0','1','YX023'), ('113','41121','1','管事','23','0','3','270','162','539','377','10','0','1','NPC_Icon10'), ('114','41121','2','佣人甲','23','0','3','270','162','539','377','10','0','1','YX024'), ('115','41121','3','佣人乙','23','0','1','270','270','647','216','10','0','1','YX024'), ('116','41121','4','佣人丙','23','0','4','324','162','647','270','10','0','1','YX014'), ('117','41131','1','张团练','24','0','3','278','167','556','389','10','0','1','NPC_Icon11'), ('118','41131','2','小陪练','24','0','2','334','223','667','223','10','0','1','YX027'), ('119','41131','3','二陪练','24','0','3','278','167','556','389','10','0','1','YX030'), ('120','41131','4','三陪练','24','0','3','278','167','556','389','10','0','1','YX039'), ('121','41141','1','张都监','25','0','2','379','253','1066','253','10','0','1','NPC_Icon12'), ('122','41141','2','张秘书','25','0','4','379','190','1066','316','10','0','1','YX017'), ('123','41141','3','张车夫','25','0','4','379','190','1066','316','10','0','1','YX002'), ('124','41141','4','张执行','25','0','4','379','190','1066','316','10','0','1','YX006'), ('125','41211','1','刘老汉','25','0','3','305','183','609','426','10','0','1','NPC_Icon13'), ('126','41211','2','小儿子','25','0','4','366','183','731','305','10','0','1','YX027'), ('127','41211','3','二儿子','25','0','2','366','244','731','244','10','0','1','YX033'), ('128','41211','4','三儿子','25','0','3','305','183','609','426','10','0','1','YX015'), ('129','41221','1','刘夫人','26','0','3','313','188','626','439','10','0','1','NPC_Icon14'), ('130','41221','2','邻居','26','0','4','376','188','752','313','10','0','1','YX011'), ('131','41221','3','泼妇','26','0','3','313','188','626','439','10','0','1','YX017'), ('132','41221','4','悍女','26','0','4','376','188','752','313','10','0','1','YX030'), ('133','41231','1','镇三山','26','0','4','376','188','752','313','10','0','1','NPC_Icon15'), ('134','41231','2','黑兄弟','26','0','3','313','188','626','439','10','0','1','YX040'), ('135','41231','3','二兄弟','26','0','2','376','251','752','251','10','0','1','YX020'), ('136','41231','4','三兄弟','26','0','4','376','188','752','313','10','0','1','YX042'), ('137','41241','1','刘高','27','0','1','355','355','1199','284','10','0','1','NPC_Icon16'), ('138','41241','2','打手','27','0','2','426','284','1199','284','10','0','1','NPC_Icon7'), ('139','41241','3','刺客','27','0','2','426','284','1199','284','10','0','1','YX009'), ('140','41241','4','杀手','27','0','2','426','284','1199','284','10','0','1','YX026'), ('141','41311','1','士兵','27','0','2','422','281','843','281','10','0','1','NPC_Icon17'), ('142','41311','2','士兵甲','27','0','2','422','281','843','281','10','0','1','YX004'), ('143','41311','3','士兵乙','27','0','4','422','211','843','352','10','0','1','YX006'), ('144','41311','4','士兵丙','27','0','1','352','352','843','281','10','0','1','YX003'), ('145','41311','5','士兵丁','27','0','4','422','211','843','352','10','0','1','YX016'), ('146','41321','1','黄通判','28','0','3','361','217','721','505','10','0','1','NPC_Icon18'), ('147','41321','2','传令员','28','0','1','361','361','866','289','10','0','1','YX027'), ('148','41321','3','士兵甲','28','0','4','433','217','866','361','10','0','1','YX004'), ('149','41321','4','士兵乙','28','0','1','361','361','866','289','10','0','1','YX006'), ('150','41321','5','士兵丙','28','0','1','361','361','866','289','10','0','1','YX003'), ('151','41331','1','监斩官','28','0','4','433','217','866','361','10','0','1','NPC_Icon19'), ('152','41331','2','行刑员','28','0','3','361','217','721','505','10','0','1','YX012'), ('153','41331','3','士兵甲','28','0','4','433','217','866','361','10','0','1','YX004'), ('154','41331','4','士兵乙','28','0','3','361','217','721','505','10','0','1','YX006'), ('155','41331','5','士兵丙','28','0','3','361','217','721','505','10','0','1','YX003'), ('156','41341','1','蔡九','29','0','4','489','245','1377','407','10','0','1','NPC_Icon20'), ('157','41341','2','芹蔡','29','0','1','407','407','1377','326','10','0','1','YX041'), ('158','41341','3','花蔡','29','0','1','407','407','1377','326','10','0','1','YX042'), ('159','41341','4','韭蔡','29','0','1','407','407','1377','326','10','0','1','YX017'), ('160','41341','5','白蔡','29','0','1','407','407','1377','326','10','0','1','YX025'), ('161','41411','1','李应','29','0','3','363','218','726','508','10','0','1','NPC_Icon21'), ('162','41411','2','打手','29','0','3','363','218','726','508','10','0','1','NPC_Icon7'), ('163','41411','3','杀手','29','0','1','363','363','871','291','10','0','1','YX026'), ('164','41411','4','帮手','29','0','4','436','218','871','363','10','0','1','YX044'), ('165','41411','5','凶手','29','0','2','436','291','871','291','10','0','1','YX029'), ('166','41421','1','扈三娘','30','0','2','447','298','894','298','10','0','1','NPC_Icon22'), ('167','41421','2','打手','30','0','4','447','224','894','373','10','0','1','NPC_Icon7'), ('168','41421','3','杀手','30','0','3','373','224','745','522','10','0','1','YX026'), ('169','41421','4','帮手','30','0','2','447','298','894','298','10','0','1','YX044'), ('170','41421','5','凶手','30','0','1','373','373','894','298','10','0','1','YX029'), ('171','41431','1','祝家三虎','31','0','4','459','230','918','383','10','0','1','NPC_Icon23'), ('172','41431','2','打手','31','0','2','459','306','918','306','10','0','1','NPC_Icon7'), ('173','41431','3','杀手','31','0','2','459','306','918','306','10','0','1','YX026'), ('174','41431','4','帮手','31','0','3','383','230','765','536','10','0','1','YX044'), ('175','41431','5','凶手','31','0','1','383','383','918','306','10','0','1','YX029'), ('176','41441','1','祝庄主','31','0','3','421','253','1185','589','10','0','1','NPC_Icon24'), ('177','41441','2','打手','31','0','3','421','253','1185','589','10','0','1','NPC_Icon7'), ('178','41441','3','杀手','31','0','4','505','253','1422','421','10','0','1','YX026'), ('179','41441','4','帮手','31','0','4','505','253','1422','421','10','0','1','YX044'), ('180','41441','5','凶手','31','0','4','505','253','1422','421','10','0','1','YX029'), ('181','41511','1','秀才','32','0','4','482','241','964','402','10','0','1','NPC_Icon25'), ('182','41511','2','黄师爷','32','0','1','402','402','964','322','10','0','1','YX033'), ('183','41511','3','秀才甲','32','0','2','482','322','964','322','10','0','1','YX035'), ('184','41511','4','秀才乙','32','0','4','482','241','964','402','10','0','1','YX036'), ('185','41511','5','秀才丙','32','0','2','482','322','964','322','10','0','1','YX038'), ('186','41521','1','衙役','32','0','4','482','241','964','402','10','0','1','NPC_Icon26'), ('187','41521','2','班头','32','0','3','402','241','803','563','10','0','1','BJ005'), ('188','41521','3','捕役甲','32','0','1','402','402','964','322','10','0','1','BJ001'), ('189','41521','4','捕役乙','32','0','4','482','241','964','402','10','0','1','BJ002'), ('190','41521','5','捕役丙','32','0','2','482','322','964','322','10','0','1','BJ003'), ('191','41531','1','王太守','33','0','4','495','248','989','412','10','0','1','NPC_Icon27'), ('192','41531','2','捕快甲','33','0','3','412','248','824','577','10','0','1','BJ001'), ('193','41531','3','捕快乙','33','0','4','495','248','989','412','10','0','1','BJ002'), ('194','41531','4','捕快丙','33','0','1','412','412','989','330','10','0','1','BJ003'), ('195','41531','5','捕快丁','33','0','4','495','248','989','412','10','0','1','BJ004'), ('196','41541','1','梁中书','33','0','4','544','272','1532','453','10','0','1','NPC_Icon28'), ('197','41541','2','杨提辖','33','0','4','544','272','1532','453','10','0','1','YX022'), ('198','41541','3','张提辖','33','0','3','453','272','1277','635','10','0','1','YX015'), ('199','41541','4','李提辖','33','0','3','453','272','1277','635','10','0','1','YX003'), ('200','41541','5','王提辖','33','0','3','453','272','1277','635','10','0','1','YX021');
INSERT INTO `ol_monster_bak` VALUES ('201','41611','1','报名官','34','0','4','551','276','1101','459','10','0','1','NPC_Icon29'), ('202','41611','2','协会长','34','0','4','551','276','1101','459','10','0','1','YX023'), ('203','41611','3','协会官','34','0','3','459','276','918','643','10','0','1','YX027'), ('204','41611','4','协会员','34','0','2','551','367','1101','367','10','0','1','YX022'), ('205','41611','5','蹴鞠员','34','0','2','551','367','1101','367','10','0','1','YX010'), ('206','41621','1','陆谦','34','0','1','459','459','1101','367','10','0','1','NPC_Icon30'), ('207','41621','2','协会长','34','0','4','551','276','1101','459','10','0','1','YX023'), ('208','41621','3','协会官','34','0','4','551','276','1101','459','10','0','1','YX027'), ('209','41621','4','协会员','34','0','1','459','459','1101','367','10','0','1','YX022'), ('210','41621','5','蹴鞠员','34','0','4','551','276','1101','459','10','0','1','YX010'), ('211','41631','1','高衙内','35','0','1','470','470','1127','376','10','0','1','NPC_Icon31'), ('212','41631','2','协会长','35','0','4','564','282','1127','470','10','0','1','YX023'), ('213','41631','3','协会官','35','0','4','564','282','1127','470','10','0','1','YX027'), ('214','41631','4','协会员','35','0','4','564','282','1127','470','10','0','1','YX022'), ('215','41631','5','蹴鞠员','35','0','4','564','282','1127','470','10','0','1','YX010'), ('216','41641','1','高俅','35','0','5','724','414','1165','517','10','0','1','NPC_Icon32'), ('217','41641','2','协会长','35','0','5','724','414','1165','517','10','0','1','YX023'), ('218','41641','3','协会官','35','0','4','620','310','1747','517','10','0','1','YX027'), ('219','41641','4','协会员','35','0','4','620','310','1747','517','10','0','1','YX022'), ('220','41641','5','蹴鞠员','35','0','4','620','310','1747','517','10','0','1','YX010'), ('221','41711','1','店小二','36','0','1','586','586','1407','469','10','0','1','NPC_Icon1'), ('222','41711','2','店小三','36','0','1','586','586','1407','469','10','0','1','YX029'), ('223','41711','3','店小四','36','0','3','586','352','1172','821','10','0','1','YX034'), ('224','41711','4','店小五','36','0','1','586','586','1407','469','10','0','1','YX037'), ('225','41711','5','店小六','36','0','3','586','352','1172','821','10','0','1','YX018'), ('226','41721','1','药贩子','37','0','4','719','360','1438','599','10','0','1','NPC_Icon2'), ('227','41721','2','二把手','37','0','3','599','360','1198','839','10','0','1','YX032'), ('228','41721','3','三把手','37','0','4','719','360','1438','599','10','0','1','YX041'), ('229','41721','4','四把手','37','0','4','719','360','1438','599','10','0','1','YX007'), ('230','41721','5','五把手','37','0','1','599','599','1438','480','10','0','1','YX035'), ('231','41731','1','潘金莲','37','0','4','719','360','1438','599','10','0','1','NPC_Icon3'), ('232','41731','2','银莲','37','0','4','719','360','1438','599','10','0','1','YX029'), ('233','41731','3','翠莲','37','0','3','599','360','1198','839','10','0','1','YX030'), ('234','41731','4','宝莲','37','0','1','599','599','1438','480','10','0','1','YX014'), ('235','41731','5','花莲','37','0','4','719','360','1438','599','10','0','1','BJ006'), ('236','41741','1','西门庆','38','0','1','673','673','2276','539','10','0','1','NPC_Icon4'), ('237','41741','2','打手','38','0','1','673','673','2276','539','10','0','1','NPC_Icon7'), ('238','41741','3','杀手','38','0','2','808','539','2276','539','10','0','1','YX026'), ('239','41741','4','帮手','38','0','2','808','539','2276','539','10','0','1','YX044'), ('240','41741','5','凶手','38','0','2','808','539','2276','539','10','0','1','YX029'), ('241','41811','1','伙计','38','0','3','645','387','1289','903','10','0','1','NPC_Icon5'), ('242','41811','2','二伙计','38','0','4','774','387','1547','645','10','0','1','YX020'), ('243','41811','3','三伙计','38','0','3','645','387','1289','903','10','0','1','YX020'), ('244','41811','4','四伙计','38','0','3','645','387','1289','903','10','0','1','YX001'), ('245','41811','5','五伙计','38','0','1','645','645','1547','516','10','0','1','YX005'), ('246','41821','1','镇关西','39','0','1','659','659','1580','527','10','0','1','NPC_Icon6'), ('247','41821','2','小徒弟','39','0','4','790','395','1580','659','10','0','1','YX034'), ('248','41821','3','二徒弟','39','0','4','790','395','1580','659','10','0','1','YX025'), ('249','41821','4','三徒弟','39','0','2','790','527','1580','527','10','0','1','YX008'), ('250','41821','5','四徒弟','39','0','2','790','527','1580','527','10','0','1','YX017'), ('251','41831','1','打手','39','0','2','790','527','1580','527','10','0','1','NPC_Icon7'), ('252','41831','2','杀手','39','0','4','790','395','1580','659','10','0','1','YX026'), ('253','41831','3','帮手','39','0','2','790','527','1580','527','10','0','1','YX044'), ('254','41831','4','凶手','39','0','4','790','395','1580','659','10','0','1','YX029'), ('255','41831','5','助手','39','0','3','659','395','1317','922','10','0','1','YX038'), ('256','41841','1','蒋门神','40','0','4','887','444','2499','739','10','0','1','NPC_Icon8'), ('257','41841','2','打手','40','0','4','887','444','2499','739','10','0','1','NPC_Icon7'), ('258','41841','3','杀手','40','0','1','739','739','2499','592','10','0','1','YX026'), ('259','41841','4','凶手','40','0','1','739','739','2499','592','10','0','1','YX029'), ('260','41841','5','帮手','40','0','1','739','739','2499','592','10','0','1','YX044'), ('261','41911','1','大厨','40','0','4','799','400','1597','666','10','0','1','NPC_Icon9'), ('262','41911','2','大洗菜员','40','0','3','666','400','1331','932','10','0','1','YX041'), ('263','41911','3','二洗菜员','40','0','2','799','533','1597','533','10','0','1','YX015'), ('264','41911','4','三洗菜员','40','0','4','799','400','1597','666','10','0','1','YX023'), ('265','41911','5','四洗菜员','40','0','4','799','400','1597','666','10','0','1','YX037'), ('266','41921','1','管事','41','0','2','816','544','1631','544','10','0','1','NPC_Icon10'), ('267','41921','2','佣人甲','41','0','4','816','408','1631','680','10','0','1','YX024'), ('268','41921','3','佣人乙','41','0','3','680','408','1359','951','10','0','1','YX024'), ('269','41921','4','佣人丙','41','0','1','680','680','1631','544','10','0','1','YX014'), ('270','41921','5','佣人丁','41','0','4','816','408','1631','680','10','0','1','YX027'), ('271','41931','1','张团练','42','0','2','832','555','1664','555','10','0','1','NPC_Icon11'), ('272','41931','2','小陪练','42','0','4','832','416','1664','693','10','0','1','YX027'), ('273','41931','3','二陪练','42','0','4','832','416','1664','693','10','0','1','YX030'), ('274','41931','4','三陪练','42','0','3','693','416','1386','971','10','0','1','YX039'), ('275','41931','5','四陪练','42','0','4','832','416','1664','693','10','0','1','YX010'), ('276','41941','1','张都监','42','0','4','915','458','2578','763','10','0','1','NPC_Icon12'), ('277','41941','2','张秘书','42','0','3','763','458','2149','1068','10','0','1','YX017'), ('278','41941','3','张车夫','42','0','3','763','458','2149','1068','10','0','1','YX002'), ('279','41941','4','张执行','42','0','4','915','458','2578','763','10','0','1','YX006'), ('280','41941','5','张工','42','0','4','915','458','2578','763','10','0','1','YX043'), ('281','42011','1','刘老汉','43','0','1','721','721','1731','577','10','0','1','NPC_Icon13'), ('282','42011','2','小儿子','43','0','4','866','433','1731','721','10','0','1','YX027'), ('283','42011','3','二儿子','43','0','4','866','433','1731','721','10','0','1','YX033'), ('284','42011','4','三儿子','43','0','4','866','433','1731','721','10','0','1','YX015'), ('285','42011','5','四儿子','43','0','4','866','433','1731','721','10','0','1','YX003'), ('286','42021','1','刘夫人','43','0','3','721','433','1442','1010','10','0','1','NPC_Icon14'), ('287','42021','2','邻居','43','0','2','866','577','1731','577','10','0','1','YX011'), ('288','42021','3','泼妇','43','0','4','866','433','1731','721','10','0','1','YX017'), ('289','42021','4','悍女','43','0','1','721','721','1731','577','10','0','1','YX030'), ('290','42021','5','彪女','43','0','4','866','433','1731','721','10','0','1','YX037'), ('291','42031','1','镇三山','44','0','2','882','588','1764','588','10','0','1','NPC_Icon15'), ('292','42031','2','黑兄弟','44','0','2','882','588','1764','588','10','0','1','YX040'), ('293','42031','3','二兄弟','44','0','1','735','735','1764','588','10','0','1','YX020'), ('294','42031','4','三兄弟','44','0','1','735','735','1764','588','10','0','1','YX042'), ('295','42031','5','四兄弟','44','0','4','882','441','1764','735','10','0','1','YX008'), ('296','42041','1','刘高','44','0','4','971','486','2735','809','10','0','1','NPC_Icon16'), ('297','42041','2','打手','44','0','2','971','647','2735','647','10','0','1','NPC_Icon7'), ('298','42041','3','刺客','44','0','2','971','647','2735','647','10','0','1','YX009'), ('299','42041','4','杀手','44','0','4','971','486','2735','809','10','0','1','YX026'), ('300','42041','5','凶手','44','0','3','809','486','2279','1132','10','0','1','YX029');
INSERT INTO `ol_monster_bak` VALUES ('301','42111','1','士兵','45','0','3','824','495','1648','1154','10','0','1','NPC_Icon17'), ('302','42111','2','士兵甲','45','0','2','989','660','1978','660','10','0','1','YX004'), ('303','42111','3','士兵乙','45','0','4','989','495','1978','824','10','0','1','YX006'), ('304','42111','4','士兵丙','45','0','1','824','824','1978','660','10','0','1','YX003'), ('305','42111','5','士兵丁','45','0','4','989','495','1978','824','10','0','1','YX016'), ('306','42121','1','黄通判','45','0','4','989','495','1978','824','10','0','1','NPC_Icon18'), ('307','42121','2','传令员','45','0','3','824','495','1648','1154','10','0','1','YX027'), ('308','42121','3','士兵甲','45','0','1','824','824','1978','660','10','0','1','YX004'), ('309','42121','4','士兵乙','45','0','4','989','495','1978','824','10','0','1','YX006'), ('310','42121','5','士兵丙','45','0','2','989','660','1978','660','10','0','1','YX003'), ('311','42131','1','监斩官','46','0','4','1007','504','2013','839','10','0','1','NPC_Icon19'), ('312','42131','2','行刑员','46','0','1','839','839','2013','671','10','0','1','YX012'), ('313','42131','3','士兵甲','46','0','1','839','839','2013','671','10','0','1','YX004'), ('314','42131','4','士兵乙','46','0','4','1007','504','2013','839','10','0','1','YX006'), ('315','42131','5','士兵丙','46','0','3','839','504','1678','1175','10','0','1','YX003'), ('316','42141','1','蔡九','46','0','1','923','923','3121','739','10','0','1','NPC_Icon20'), ('317','42141','2','芹蔡','46','0','4','1108','554','3121','923','10','0','1','YX041'), ('318','42141','3','花蔡','46','0','4','1108','554','3121','923','10','0','1','YX042'), ('319','42141','4','韭蔡','46','0','2','1108','739','3121','739','10','0','1','YX017'), ('320','42141','5','白蔡','46','0','2','1108','739','3121','739','10','0','1','YX025'), ('321','42211','1','李应','47','0','4','1194','597','2387','995','10','0','1','NPC_Icon21'), ('322','42211','2','打手','47','0','3','995','597','1989','1393','10','0','1','NPC_Icon7'), ('323','42211','3','杀手','47','0','3','995','597','1989','1393','10','0','1','YX026'), ('324','42211','4','帮手','47','0','4','1194','597','2387','995','10','0','1','YX044'), ('325','42211','5','凶手','47','0','4','1194','597','2387','995','10','0','1','YX029'), ('326','42221','1','扈三娘','48','0','2','1214','810','2428','810','10','0','1','NPC_Icon22'), ('327','42221','2','打手','48','0','2','1214','810','2428','810','10','0','1','NPC_Icon7'), ('328','42221','3','杀手','48','0','1','1012','1012','2428','810','10','0','1','YX026'), ('329','42221','4','帮手','48','0','4','1214','607','2428','1012','10','0','1','YX044'), ('330','42221','5','凶手','48','0','3','1012','607','2023','1417','10','0','1','YX029'), ('331','42231','1','祝家三虎','48','0','1','1012','1012','2428','810','10','0','1','NPC_Icon23'), ('332','42231','2','打手','48','0','3','1012','607','2023','1417','10','0','1','NPC_Icon7'), ('333','42231','3','杀手','48','0','4','1214','607','2428','1012','10','0','1','YX026'), ('334','42231','4','帮手','48','0','3','1012','607','2023','1417','10','0','1','YX044'), ('335','42231','5','凶手','48','0','4','1214','607','2428','1012','10','0','1','YX029'), ('336','42241','1','祝庄主','49','0','4','1358','679','3827','1132','10','0','1','NPC_Icon24'), ('337','42241','2','打手','49','0','1','1132','1132','3827','906','10','0','1','NPC_Icon7'), ('338','42241','3','杀手','49','0','1','1132','1132','3827','906','10','0','1','YX026'), ('339','42241','4','帮手','49','0','3','1132','679','3190','1585','10','0','1','YX044'), ('340','42241','5','凶手','49','0','3','1132','679','3190','1585','10','0','1','YX029'), ('341','42311','1','秀才','49','0','1','1023','1023','2454','818','10','0','1','NPC_Icon25'), ('342','42311','2','黄师爷','49','0','4','1227','614','2454','1023','10','0','1','YX033'), ('343','42311','3','秀才甲','49','0','1','1023','1023','2454','818','10','0','1','YX035'), ('344','42311','4','秀才乙','49','0','4','1227','614','2454','1023','10','0','1','YX036'), ('345','42311','5','秀才丙','49','0','4','1227','614','2454','1023','10','0','1','YX038'), ('346','42321','1','衙役','50','0','3','1041','625','2081','1457','10','0','1','NPC_Icon26'), ('347','42321','2','班头','50','0','3','1041','625','2081','1457','10','0','1','BJ005'), ('348','42321','3','捕役甲','50','0','4','1249','625','2497','1041','10','0','1','BJ001'), ('349','42321','4','捕役乙','50','0','4','1249','625','2497','1041','10','0','1','BJ002'), ('350','42321','5','捕役丙','50','0','3','1041','625','2081','1457','10','0','1','BJ003'), ('351','42331','1','王太守','50','0','1','1041','1041','2497','833','10','0','1','NPC_Icon27'), ('352','42331','2','捕快甲','50','0','2','1249','833','2497','833','10','0','1','BJ001'), ('353','42331','3','捕快乙','50','0','1','1041','1041','2497','833','10','0','1','BJ002'), ('354','42331','4','捕快丙','50','0','3','1041','625','2081','1457','10','0','1','BJ003'), ('355','42331','5','捕快丁','50','0','4','1249','625','2497','1041','10','0','1','BJ004'), ('356','42341','1','梁中书','51','0','1','1165','1165','3938','932','10','0','1','NPC_Icon28'), ('357','42341','2','杨提辖','51','0','3','1165','699','3282','1631','10','0','1','YX022'), ('358','42341','3','张提辖','51','0','2','1398','932','3938','932','10','0','1','YX015'), ('359','42341','4','李提辖','51','0','4','1398','699','3938','1165','10','0','1','YX003'), ('360','42341','5','王提辖','51','0','4','1398','699','3938','1165','10','0','1','YX021'), ('361','42411','1','报名官','51','0','3','1078','647','2156','1510','10','0','1','NPC_Icon29'), ('362','42411','2','协会长','51','0','4','1294','647','2588','1078','10','0','1','YX023'), ('363','42411','3','协会官','51','0','1','1078','1078','2588','863','10','0','1','YX027'), ('364','42411','4','协会员','51','0','4','1294','647','2588','1078','10','0','1','YX022'), ('365','42411','5','蹴鞠员','51','0','1','1078','1078','2588','863','10','0','1','YX010'), ('366','42421','1','陆谦','52','0','4','1316','658','2631','1097','10','0','1','NPC_Icon30'), ('367','42421','2','协会长','52','0','3','1097','658','2193','1535','10','0','1','YX023'), ('368','42421','3','协会官','52','0','4','1316','658','2631','1097','10','0','1','YX027'), ('369','42421','4','协会员','52','0','1','1097','1097','2631','877','10','0','1','YX022'), ('370','42421','5','蹴鞠员','52','0','2','1316','877','2631','877','10','0','1','YX010'), ('371','42431','1','高衙内','52','0','2','1316','877','2631','877','10','0','1','NPC_Icon31'), ('372','42431','2','协会长','52','0','4','1316','658','2631','1097','10','0','1','YX023'), ('373','42431','3','协会官','52','0','2','1316','877','2631','877','10','0','1','YX027'), ('374','42431','4','协会员','52','0','4','1316','658','2631','1097','10','0','1','YX022'), ('375','42431','5','蹴鞠员','52','0','4','1316','658','2631','1097','10','0','1','YX010'), ('376','42441','1','高俅','53','0','2','1472','981','4146','981','10','0','1','NPC_Icon32'), ('377','42441','2','协会长','53','0','5','1717','981','2764','1226','10','0','1','YX023'), ('378','42441','3','协会官','53','0','5','1717','981','2764','1226','10','0','1','YX027'), ('379','42441','4','协会员','53','0','4','1472','736','4146','1226','10','0','1','YX022'), ('380','42441','5','蹴鞠员','53','0','5','1717','981','2764','1226','10','0','1','YX010'), ('381','42511','1','店小二','54','0','1','1237','1237','2967','989','10','0','1','NPC_Icon1'), ('382','42511','2','店小三','54','0','1','1237','1237','2967','989','10','0','1','YX029'), ('383','42511','3','店小四','54','0','3','1237','742','2473','1731','10','0','1','YX034'), ('384','42511','4','店小五','54','0','1','1237','1237','2967','989','10','0','1','YX037'), ('385','42511','5','店小六','54','0','3','1237','742','2473','1731','10','0','1','YX018'), ('386','42521','1','药贩子','54','0','4','1484','742','2967','1237','10','0','1','NPC_Icon2'), ('387','42521','2','二把手','54','0','3','1237','742','2473','1731','10','0','1','YX032'), ('388','42521','3','三把手','54','0','4','1484','742','2967','1237','10','0','1','YX041'), ('389','42521','4','四把手','54','0','4','1484','742','2967','1237','10','0','1','YX007'), ('390','42521','5','五把手','54','0','1','1237','1237','2967','989','10','0','1','YX035'), ('391','42531','1','潘金莲','55','0','4','1507','754','3014','1256','10','0','1','NPC_Icon3'), ('392','42531','2','银莲','55','0','4','1507','754','3014','1256','10','0','1','YX029'), ('393','42531','3','翠莲','55','0','3','1256','754','2511','1758','10','0','1','YX030'), ('394','42531','4','宝莲','55','0','1','1256','1256','3014','1005','10','0','1','YX014'), ('395','42531','5','花莲','55','0','4','1507','754','3014','1256','10','0','1','BJ006'), ('396','42541','1','西门庆','55','0','1','1382','1382','4671','1105','10','0','1','NPC_Icon4'), ('397','42541','2','打手','55','0','1','1382','1382','4671','1105','10','0','1','NPC_Icon7'), ('398','42541','3','杀手','55','0','2','1658','1105','4671','1105','10','0','1','YX026'), ('399','42541','4','帮手','55','0','2','1658','1105','4671','1105','10','0','1','YX044'), ('400','42541','5','凶手','55','0','2','1658','1105','4671','1105','10','0','1','YX029');
INSERT INTO `ol_monster_bak` VALUES ('401','42611','1','伙计','56','0','3','1306','784','2612','1829','10','0','1','NPC_Icon5'), ('402','42611','2','二伙计','56','0','4','1567','784','3134','1306','10','0','1','YX020'), ('403','42611','3','三伙计','56','0','3','1306','784','2612','1829','10','0','1','YX020'), ('404','42611','4','四伙计','56','0','3','1306','784','2612','1829','10','0','1','YX001'), ('405','42611','5','五伙计','56','0','1','1306','1306','3134','1045','10','0','1','YX005'), ('406','42621','1','镇关西','56','0','1','1306','1306','3134','1045','10','0','1','NPC_Icon6'), ('407','42621','2','小徒弟','56','0','4','1567','784','3134','1306','10','0','1','YX034'), ('408','42621','3','二徒弟','56','0','4','1567','784','3134','1306','10','0','1','YX025'), ('409','42621','4','三徒弟','56','0','2','1567','1045','3134','1045','10','0','1','YX008'), ('410','42621','5','四徒弟','56','0','2','1567','1045','3134','1045','10','0','1','YX017'), ('411','42631','1','打手','57','0','2','1591','1061','3182','1061','10','0','1','NPC_Icon7'), ('412','42631','2','杀手','57','0','4','1591','796','3182','1326','10','0','1','YX026'), ('413','42631','3','帮手','57','0','2','1591','1061','3182','1061','10','0','1','YX044'), ('414','42631','4','凶手','57','0','4','1591','796','3182','1326','10','0','1','YX029'), ('415','42631','5','助手','57','0','3','1326','796','2651','1856','10','0','1','YX038'), ('416','42641','1','蒋门神','57','0','4','1750','875','4931','1459','10','0','1','NPC_Icon8'), ('417','42641','2','打手','57','0','4','1750','875','4931','1459','10','0','1','NPC_Icon7'), ('418','42641','3','杀手','57','0','1','1459','1459','4931','1167','10','0','1','YX026'), ('419','42641','4','凶手','57','0','1','1459','1459','4931','1167','10','0','1','YX029'), ('420','42641','5','帮手','57','0','1','1459','1459','4931','1167','10','0','1','YX044'), ('421','42711','1','大厨','58','0','4','1890','945','3780','1575','10','0','1','NPC_Icon9'), ('422','42711','2','大洗菜员','58','0','3','1575','945','3150','2205','10','0','1','YX041'), ('423','42711','3','二洗菜员','58','0','2','1890','1260','3780','1260','10','0','1','YX015'), ('424','42711','4','三洗菜员','58','0','4','1890','945','3780','1575','10','0','1','YX023'), ('425','42711','5','四洗菜员','58','0','4','1890','945','3780','1575','10','0','1','YX037'), ('426','42721','1','管事','58','0','2','1890','1260','3780','1260','10','0','1','NPC_Icon10'), ('427','42721','2','佣人甲','58','0','4','1890','945','3780','1575','10','0','1','YX024'), ('428','42721','3','佣人乙','58','0','3','1575','945','3150','2205','10','0','1','YX024'), ('429','42721','4','佣人丙','58','0','1','1575','1575','3780','1260','10','0','1','YX014'), ('430','42721','5','佣人丁','58','0','4','1890','945','3780','1575','10','0','1','YX027'), ('431','42731','1','张团练','59','0','2','1917','1278','3834','1278','10','0','1','NPC_Icon11'), ('432','42731','2','小陪练','59','0','4','1917','959','3834','1598','10','0','1','YX027'), ('433','42731','3','二陪练','59','0','4','1917','959','3834','1598','10','0','1','YX030'), ('434','42731','4','三陪练','59','0','3','1598','959','3195','2237','10','0','1','YX039'), ('435','42731','5','四陪练','59','0','4','1917','959','3834','1598','10','0','1','YX010'), ('436','42741','1','张都监','60','0','4','2139','1070','6027','1782','10','0','1','NPC_Icon12'), ('437','42741','2','张秘书','60','0','3','1782','1070','5022','2495','10','0','1','YX017'), ('438','42741','3','张车夫','60','0','3','1782','1070','5022','2495','10','0','1','YX002'), ('439','42741','4','张执行','60','0','4','2139','1070','6027','1782','10','0','1','YX006'), ('440','42741','5','张工','60','0','4','2139','1070','6027','1782','10','0','1','YX043'), ('441','42811','1','刘老汉','60','0','1','1563','1563','3750','1250','10','0','1','NPC_Icon13'), ('442','42811','2','小儿子','60','0','4','1875','938','3750','1563','10','0','1','YX027'), ('443','42811','3','二儿子','60','0','4','1875','938','3750','1563','10','0','1','YX033'), ('444','42811','4','三儿子','60','0','4','1875','938','3750','1563','10','0','1','YX015'), ('445','42811','5','四儿子','60','0','4','1875','938','3750','1563','10','0','1','YX003'), ('446','42821','1','刘夫人','61','0','3','1586','952','3171','2220','10','0','1','NPC_Icon14'), ('447','42821','2','邻居','61','0','2','1903','1269','3805','1269','10','0','1','YX011'), ('448','42821','3','泼妇','61','0','4','1903','952','3805','1586','10','0','1','YX017'), ('449','42821','4','悍女','61','0','1','1586','1586','3805','1269','10','0','1','YX030'), ('450','42821','5','彪女','61','0','4','1903','952','3805','1586','10','0','1','YX037'), ('451','42831','1','镇三山','61','0','2','1903','1269','3805','1269','10','0','1','NPC_Icon15'), ('452','42831','2','黑兄弟','61','0','2','1903','1269','3805','1269','10','0','1','YX040'), ('453','42831','3','二兄弟','61','0','1','1586','1586','3805','1269','10','0','1','YX020'), ('454','42831','4','三兄弟','61','0','1','1586','1586','3805','1269','10','0','1','YX042'), ('455','42831','5','四兄弟','61','0','4','1903','952','3805','1586','10','0','1','YX008'), ('456','42841','1','刘高','62','0','4','2123','1062','5982','1769','10','0','1','NPC_Icon16'), ('457','42841','2','打手','62','0','2','2123','1416','5982','1416','10','0','1','NPC_Icon7'), ('458','42841','3','刺客','62','0','2','2123','1416','5982','1416','10','0','1','YX009'), ('459','42841','4','杀手','62','0','4','2123','1062','5982','1769','10','0','1','YX026'), ('460','42841','5','凶手','62','0','3','1769','1062','4985','2477','10','0','1','YX029'), ('461','42911','1','士兵','62','0','3','1642','986','3284','2299','10','0','1','NPC_Icon17'), ('462','42911','2','士兵甲','62','0','2','1971','1314','3941','1314','10','0','1','YX004'), ('463','42911','3','士兵乙','62','0','4','1971','986','3941','1642','10','0','1','YX006'), ('464','42911','4','士兵丙','62','0','1','1642','1642','3941','1314','10','0','1','YX003'), ('465','42911','5','士兵丁','62','0','4','1971','986','3941','1642','10','0','1','YX016'), ('466','42921','1','黄通判','63','0','4','1999','1000','3997','1666','10','0','1','NPC_Icon18'), ('467','42921','2','传令员','63','0','3','1666','1000','3331','2332','10','0','1','YX027'), ('468','42921','3','士兵甲','63','0','1','1666','1666','3997','1333','10','0','1','YX004'), ('469','42921','4','士兵乙','63','0','4','1999','1000','3997','1666','10','0','1','YX006'), ('470','42921','5','士兵丙','63','0','2','1999','1333','3997','1333','10','0','1','YX003'), ('471','42931','1','监斩官','63','0','4','1999','1000','3997','1666','10','0','1','NPC_Icon19'), ('472','42931','2','行刑员','63','0','1','1666','1666','3997','1333','10','0','1','YX012'), ('473','42931','3','士兵甲','63','0','1','1666','1666','3997','1333','10','0','1','YX004'), ('474','42931','4','士兵乙','63','0','4','1999','1000','3997','1666','10','0','1','YX006'), ('475','42931','5','士兵丙','63','0','3','1666','1000','3331','2332','10','0','1','YX003'), ('476','42941','1','蔡九','64','0','1','1858','1858','6282','1486','10','0','1','NPC_Icon20'), ('477','42941','2','芹蔡','64','0','4','2229','1115','6282','1858','10','0','1','YX041'), ('478','42941','3','花蔡','64','0','4','2229','1115','6282','1858','10','0','1','YX042'), ('479','42941','4','韭蔡','64','0','2','2229','1486','6282','1486','10','0','1','YX017'), ('480','42941','5','白蔡','64','0','2','2229','1486','6282','1486','10','0','1','YX025'), ('481','43011','1','李应','64','0','4','2118','1059','4236','1765','10','0','1','NPC_Icon21'), ('482','43011','2','打手','64','0','3','1765','1059','3530','2471','10','0','1','NPC_Icon7'), ('483','43011','3','杀手','64','0','3','1765','1059','3530','2471','10','0','1','YX026'), ('484','43011','4','帮手','64','0','4','2118','1059','4236','1765','10','0','1','YX044'), ('485','43011','5','凶手','64','0','4','2118','1059','4236','1765','10','0','1','YX029'), ('486','43021','1','扈三娘','65','0','2','2147','1431','4293','1431','10','0','1','NPC_Icon22'), ('487','43021','2','打手','65','0','2','2147','1431','4293','1431','10','0','1','NPC_Icon7'), ('488','43021','3','杀手','65','0','1','1789','1789','4293','1431','10','0','1','YX026'), ('489','43021','4','帮手','65','0','4','2147','1074','4293','1789','10','0','1','YX044'), ('490','43021','5','凶手','65','0','3','1789','1074','3578','2505','10','0','1','YX029'), ('491','43031','1','祝家三虎','66','0','1','1812','1812','4349','1450','10','0','1','NPC_Icon23'), ('492','43031','2','打手','66','0','3','1812','1088','3624','2537','10','0','1','NPC_Icon7'), ('493','43031','3','杀手','66','0','4','2175','1088','4349','1812','10','0','1','YX026'), ('494','43031','4','帮手','66','0','3','1812','1088','3624','2537','10','0','1','YX044'), ('495','43031','5','凶手','66','0','4','2175','1088','4349','1812','10','0','1','YX029'), ('496','43041','1','祝庄主','66','0','4','2392','1196','6741','1994','10','0','1','NPC_Icon24'), ('497','43041','2','打手','66','0','1','1994','1994','6741','1595','10','0','1','NPC_Icon7'), ('498','43041','3','杀手','66','0','1','1994','1994','6741','1595','10','0','1','YX026'), ('499','43041','4','帮手','66','0','3','1994','1196','5618','2791','10','0','1','YX044'), ('500','43041','5','凶手','66','0','3','1994','1196','5618','2791','10','0','1','YX029');
INSERT INTO `ol_monster_bak` VALUES ('501','43111','1','秀才','67','0','1','1927','1927','4623','1541','10','0','1','NPC_Icon25'), ('502','43111','2','黄师爷','67','0','4','2312','1156','4623','1927','10','0','1','YX033'), ('503','43111','3','秀才甲','67','0','1','1927','1927','4623','1541','10','0','1','YX035'), ('504','43111','4','秀才乙','67','0','4','2312','1156','4623','1927','10','0','1','YX036'), ('505','43111','5','秀才丙','67','0','4','2312','1156','4623','1927','10','0','1','YX038'), ('506','43121','1','衙役','67','0','3','1927','1156','3853','2697','10','0','1','NPC_Icon26'), ('507','43121','2','班头','67','0','3','1927','1156','3853','2697','10','0','1','BJ005'), ('508','43121','3','捕役甲','67','0','4','2312','1156','4623','1927','10','0','1','BJ001'), ('509','43121','4','捕役乙','67','0','4','2312','1156','4623','1927','10','0','1','BJ002'), ('510','43121','5','捕役丙','67','0','3','1927','1156','3853','2697','10','0','1','BJ003'), ('511','43131','1','王太守','68','0','1','1950','1950','4680','1560','10','0','1','NPC_Icon27'), ('512','43131','2','捕快甲','68','0','2','2340','1560','4680','1560','10','0','1','BJ001'), ('513','43131','3','捕快乙','68','0','1','1950','1950','4680','1560','10','0','1','BJ002'), ('514','43131','4','捕快丙','68','0','3','1950','1170','3900','2730','10','0','1','BJ003'), ('515','43131','5','捕快丁','68','0','4','2340','1170','4680','1950','10','0','1','BJ004'), ('516','43141','1','梁中书','68','0','1','2145','2145','7254','1716','10','0','1','NPC_Icon28'), ('517','43141','2','杨提辖','68','0','3','2145','1287','6045','3003','10','0','1','YX022'), ('518','43141','3','张提辖','68','0','2','2574','1716','7254','1716','10','0','1','YX015'), ('519','43141','4','李提辖','68','0','4','2574','1287','7254','2145','10','0','1','YX003'), ('520','43141','5','王提辖','68','0','4','2574','1287','7254','2145','10','0','1','YX021'), ('521','43211','1','报名官','69','0','3','1987','1192','3973','2782','10','0','1','NPC_Icon29'), ('522','43211','2','协会长','69','0','4','2384','1192','4768','1987','10','0','1','YX023'), ('523','43211','3','协会官','69','0','1','1987','1987','4768','1590','10','0','1','YX027'), ('524','43211','4','协会员','69','0','4','2384','1192','4768','1987','10','0','1','YX022'), ('525','43211','5','蹴鞠员','69','0','1','1987','1987','4768','1590','10','0','1','YX010'), ('526','43221','1','陆谦','69','0','4','2384','1192','4768','1987','10','0','1','NPC_Icon30'), ('527','43221','2','协会长','69','0','3','1987','1192','3973','2782','10','0','1','YX023'), ('528','43221','3','协会官','69','0','4','2384','1192','4768','1987','10','0','1','YX027'), ('529','43221','4','协会员','69','0','1','1987','1987','4768','1590','10','0','1','YX022'), ('530','43221','5','蹴鞠员','69','0','2','2384','1590','4768','1590','10','0','1','YX010'), ('531','43231','1','高衙内','70','0','2','2413','1609','4826','1609','10','0','1','NPC_Icon31'), ('532','43231','2','协会长','70','0','4','2413','1207','4826','2011','10','0','1','YX023'), ('533','43231','3','协会官','70','0','2','2413','1609','4826','1609','10','0','1','YX027'), ('534','43231','4','协会员','70','0','4','2413','1207','4826','2011','10','0','1','YX022'), ('535','43231','5','蹴鞠员','70','0','4','2413','1207','4826','2011','10','0','1','YX010'), ('536','43241','1','高俅','70','0','1','2212','2212','7480','1770','10','0','1','NPC_Icon32'), ('537','43241','2','协会长','70','0','5','3097','1770','4987','2212','10','0','1','YX023'), ('538','43241','3','协会官','70','0','5','3097','1770','4987','2212','10','0','1','YX027'), ('539','43241','4','协会员','70','0','5','3097','1770','4987','2212','10','0','1','YX022'), ('540','43241','5','蹴鞠员','70','0','5','3097','1770','4987','2212','10','0','1','YX010'), ('541','43311','1','店小二','70','0','1','1927','1927','5086','1542','10','0','1','NPC_Icon1'), ('542','43311','2','店小三','70','0','1','1927','1927','5086','1542','10','0','1','YX029'), ('543','43311','3','店小四','70','0','3','1927','1156','4239','2698','10','0','1','YX034'), ('544','43311','4','店小五','70','0','1','1927','1927','5086','1542','10','0','1','YX037'), ('545','43311','5','店小六','70','0','3','1927','1156','4239','2698','10','0','1','YX018'), ('546','43321','1','药贩子','70','0','4','2312','1156','5086','1927','10','0','1','NPC_Icon2'), ('547','43321','2','二把手','70','0','3','1927','1156','4239','2698','10','0','1','YX032'), ('548','43321','3','三把手','70','0','4','2312','1156','5086','1927','10','0','1','YX041'), ('549','43321','4','四把手','70','0','4','2312','1156','5086','1927','10','0','1','YX007'), ('550','43321','5','五把手','70','0','1','1927','1927','5086','1542','10','0','1','YX035'), ('551','43331','1','潘金莲','70','0','4','2517','1259','5537','2097','10','0','1','NPC_Icon3'), ('552','43331','2','银莲','70','0','4','2517','1259','5537','2097','10','0','1','YX029'), ('553','43331','3','翠莲','70','0','3','2097','1259','4614','2936','10','0','1','YX030'), ('554','43331','4','宝莲','70','0','1','2097','2097','5537','1678','10','0','1','YX014'), ('555','43331','5','花莲','70','0','4','2517','1259','5537','2097','10','0','1','BJ006'), ('556','43341','1','西门庆','70','0','1','2307','2307','9440','1846','10','0','1','NPC_Icon4'), ('557','43341','2','打手','70','0','1','2307','2307','9440','1846','10','0','1','NPC_Icon7'), ('558','43341','3','杀手','70','0','2','2769','1846','9440','1846','10','0','1','YX026'), ('559','43341','4','帮手','70','0','2','2769','1846','9440','1846','10','0','1','YX044'), ('560','43341','5','凶手','70','0','2','2769','1846','9440','1846','10','0','1','YX029'), ('561','43411','1','伙计','70','0','3','2202','1322','5285','3083','10','0','1','NPC_Icon5'), ('562','43411','2','二伙计','70','0','4','2643','1322','6342','2202','10','0','1','YX020'), ('563','43411','3','三伙计','70','0','3','2202','1322','5285','3083','10','0','1','YX020'), ('564','43411','4','四伙计','70','0','3','2202','1322','5285','3083','10','0','1','YX001'), ('565','43411','5','五伙计','70','0','1','2202','2202','6342','1762','10','0','1','YX005'), ('566','43421','1','镇关西','70','0','1','2202','2202','6342','1762','10','0','1','NPC_Icon6'), ('567','43421','2','小徒弟','70','0','4','2643','1322','6342','2202','10','0','1','YX034'), ('568','43421','3','二徒弟','70','0','4','2643','1322','6342','2202','10','0','1','YX025'), ('569','43421','4','三徒弟','70','0','2','2643','1762','6342','1762','10','0','1','YX008'), ('570','43421','5','四徒弟','70','0','2','2643','1762','6342','1762','10','0','1','YX017'), ('571','43431','1','打手','70','0','2','2784','1856','6681','1856','10','0','1','NPC_Icon7'), ('572','43431','2','杀手','70','0','4','2784','1392','6681','2320','10','0','1','YX026'), ('573','43431','3','帮手','70','0','2','2784','1856','6681','1856','10','0','1','YX044'), ('574','43431','4','凶手','70','0','4','2784','1392','6681','2320','10','0','1','YX029'), ('575','43431','5','助手','70','0','3','2320','1392','5568','3248','10','0','1','YX038'), ('576','43441','1','蒋门神','70','0','4','3063','1532','12427','2552','10','0','1','NPC_Icon8'), ('577','43441','2','打手','70','0','4','3063','1532','12427','2552','10','0','1','NPC_Icon7'), ('578','43441','3','杀手','70','0','1','2552','2552','12427','2042','10','0','1','YX026'), ('579','43441','4','凶手','70','0','1','2552','2552','12427','2042','10','0','1','YX029'), ('580','43441','5','帮手','70','0','1','2552','2552','12427','2042','10','0','1','YX044'), ('581','43511','1','大厨','70','0','4','2745','1373','7137','2288','10','0','1','NPC_Icon9'), ('582','43511','2','大洗菜员','70','0','3','2288','1373','5948','3203','10','0','1','YX041'), ('583','43511','3','二洗菜员','70','0','2','2745','1830','7137','1830','10','0','1','YX015'), ('584','43511','4','三洗菜员','70','0','4','2745','1373','7137','2288','10','0','1','YX023'), ('585','43511','5','四洗菜员','70','0','4','2745','1373','7137','2288','10','0','1','YX037'), ('586','43521','1','管事','70','0','2','2745','1830','7137','1830','10','0','1','NPC_Icon10'), ('587','43521','2','佣人甲','70','0','4','2745','1373','7137','2288','10','0','1','YX024'), ('588','43521','3','佣人乙','70','0','3','2288','1373','5948','3203','10','0','1','YX024'), ('589','43521','4','佣人丙','70','0','1','2288','2288','7137','1830','10','0','1','YX014'), ('590','43521','5','佣人丁','70','0','4','2745','1373','7137','2288','10','0','1','YX027'), ('591','43531','1','张团练','70','0','2','2928','1952','7613','1952','10','0','1','NPC_Icon11'), ('592','43531','2','小陪练','70','0','4','2928','1464','7613','2440','10','0','1','YX027'), ('593','43531','3','二陪练','70','0','4','2928','1464','7613','2440','10','0','1','YX030'), ('594','43531','4','三陪练','70','0','3','2440','1464','6344','3416','10','0','1','YX039'), ('595','43531','5','四陪练','70','0','4','2928','1464','7613','2440','10','0','1','YX010'), ('596','43541','1','张都监','70','0','4','3221','1611','15340','2684','10','0','1','NPC_Icon12'), ('597','43541','2','张秘书','70','0','3','2684','1611','12784','3758','10','0','1','YX017'), ('598','43541','3','张车夫','70','0','3','2684','1611','12784','3758','10','0','1','YX002'), ('599','43541','4','张执行','70','0','4','3221','1611','15340','2684','10','0','1','YX006'), ('600','43541','5','张工','70','0','4','3221','1611','15340','2684','10','0','1','YX043');
INSERT INTO `ol_monster_bak` VALUES ('601','43611','1','刘老汉','70','0','1','2335','2335','7845','1868','10','0','1','NPC_Icon13'), ('602','43611','2','小儿子','70','0','4','2802','1401','7845','2335','10','0','1','YX027'), ('603','43611','3','二儿子','70','0','4','2802','1401','7845','2335','10','0','1','YX033'), ('604','43611','4','三儿子','70','0','4','2802','1401','7845','2335','10','0','1','YX015'), ('605','43611','5','四儿子','70','0','4','2802','1401','7845','2335','10','0','1','YX003'), ('606','43621','1','刘夫人','70','0','3','2335','1401','6538','3269','10','0','1','NPC_Icon14'), ('607','43621','2','邻居','70','0','2','2802','1868','7845','1868','10','0','1','YX011'), ('608','43621','3','泼妇','70','0','4','2802','1401','7845','2335','10','0','1','YX017'), ('609','43621','4','悍女','70','0','1','2335','2335','7845','1868','10','0','1','YX030'), ('610','43621','5','彪女','70','0','4','2802','1401','7845','2335','10','0','1','YX037'), ('611','43631','1','镇三山','70','0','2','3081','2054','8626','2054','10','0','1','NPC_Icon15'), ('612','43631','2','黑兄弟','70','0','2','3081','2054','8626','2054','10','0','1','YX040'), ('613','43631','3','二兄弟','70','0','1','2568','2568','8626','2054','10','0','1','YX020'), ('614','43631','4','三兄弟','70','0','1','2568','2568','8626','2054','10','0','1','YX042'), ('615','43631','5','四兄弟','70','0','4','3081','1541','8626','2568','10','0','1','YX008'), ('616','43641','1','刘高','70','0','4','3389','1695','18719','2824','10','0','1','NPC_Icon16'), ('617','43641','2','打手','70','0','2','3389','2260','18719','2260','10','0','1','NPC_Icon7'), ('618','43641','3','刺客','70','0','2','3389','2260','18719','2260','10','0','1','YX009'), ('619','43641','4','杀手','70','0','4','3389','1695','18719','2824','10','0','1','YX026'), ('620','43641','5','凶手','70','0','3','2824','1695','15599','3954','10','0','1','YX029'), ('621','43711','1','士兵','70','0','3','2365','1419','6621','3311','10','0','1','NPC_Icon17'), ('622','43711','2','士兵甲','70','0','2','2838','1892','7945','1892','10','0','1','YX004'), ('623','43711','3','士兵乙','70','0','4','2838','1419','7945','2365','10','0','1','YX006'), ('624','43711','4','士兵丙','70','0','1','2365','2365','7945','1892','10','0','1','YX003'), ('625','43711','5','士兵丁','70','0','4','2838','1419','7945','2365','10','0','1','YX016'), ('626','43721','1','黄通判','70','0','4','2838','1419','7945','2365','10','0','1','NPC_Icon18'), ('627','43721','2','传令员','70','0','3','2365','1419','6621','3311','10','0','1','YX027'), ('628','43721','3','士兵甲','70','0','1','2365','2365','7945','1892','10','0','1','YX004'), ('629','43721','4','士兵乙','70','0','4','2838','1419','7945','2365','10','0','1','YX006'), ('630','43721','5','士兵丙','70','0','2','2838','1892','7945','1892','10','0','1','YX003'), ('631','43731','1','监斩官','70','0','4','3193','1597','8939','2661','10','0','1','NPC_Icon19'), ('632','43731','2','行刑员','70','0','1','2661','2661','8939','2129','10','0','1','YX012'), ('633','43731','3','士兵甲','70','0','1','2661','2661','8939','2129','10','0','1','YX004'), ('634','43731','4','士兵乙','70','0','4','3193','1597','8939','2661','10','0','1','YX006'), ('635','43731','5','士兵丙','70','0','3','2661','1597','7449','3725','10','0','1','YX003'), ('636','43741','1','蔡九','70','0','1','2927','2927','19397','2342','10','0','1','NPC_Icon20'), ('637','43741','2','芹蔡','70','0','4','3512','1756','19397','2927','10','0','1','YX041'), ('638','43741','3','花蔡','70','0','4','3512','1756','19397','2927','10','0','1','YX042'), ('639','43741','4','韭蔡','70','0','2','3512','2342','19397','2342','10','0','1','YX017'), ('640','43741','5','白蔡','70','0','2','3512','2342','19397','2342','10','0','1','YX025'), ('641','43811','1','李应','70','0','4','3041','1521','8514','2534','10','0','1','NPC_Icon21'), ('642','43811','2','打手','70','0','3','2534','1521','7095','3548','10','0','1','NPC_Icon7'), ('643','43811','3','杀手','70','0','3','2534','1521','7095','3548','10','0','1','YX026'), ('644','43811','4','帮手','70','0','4','3041','1521','8514','2534','10','0','1','YX044'), ('645','43811','5','凶手','70','0','4','3041','1521','8514','2534','10','0','1','YX029'), ('646','43821','1','扈三娘','70','0','2','3041','2027','8514','2027','10','0','1','NPC_Icon22'), ('647','43821','2','打手','70','0','2','3041','2027','8514','2027','10','0','1','NPC_Icon7'), ('648','43821','3','杀手','70','0','1','2534','2534','8514','2027','10','0','1','YX026'), ('649','43821','4','帮手','70','0','4','3041','1521','8514','2534','10','0','1','YX044'), ('650','43821','5','凶手','70','0','3','2534','1521','7095','3548','10','0','1','YX029'), ('651','43831','1','祝家三虎','70','0','1','2820','2820','9475','2256','10','0','1','NPC_Icon23'), ('652','43831','2','打手','70','0','3','2820','1692','7896','3948','10','0','1','NPC_Icon7'), ('653','43831','3','杀手','70','0','4','3384','1692','9475','2820','10','0','1','YX026'), ('654','43831','4','帮手','70','0','3','2820','1692','7896','3948','10','0','1','YX044'), ('655','43831','5','凶手','70','0','4','3384','1692','9475','2820','10','0','1','YX029'), ('656','43841','1','祝庄主','70','0','4','3723','1862','20560','3102','10','0','1','NPC_Icon24'), ('657','43841','2','打手','70','0','1','3102','3102','20560','2482','10','0','1','NPC_Icon7'), ('658','43841','3','杀手','70','0','1','3102','3102','20560','2482','10','0','1','YX026'), ('659','43841','4','帮手','70','0','3','3102','1862','17133','4343','10','0','1','YX044'), ('660','43841','5','凶手','70','0','3','3102','1862','17133','4343','10','0','1','YX029'), ('661','43911','1','秀才','70','0','1','2655','2655','8919','2124','10','0','1','NPC_Icon25'), ('662','43911','2','黄师爷','70','0','4','3186','1593','8919','2655','10','0','1','YX033'), ('663','43911','3','秀才甲','70','0','1','2655','2655','8919','2124','10','0','1','YX035'), ('664','43911','4','秀才乙','70','0','4','3186','1593','8919','2655','10','0','1','YX036'), ('665','43911','5','秀才丙','70','0','4','3186','1593','8919','2655','10','0','1','YX038'), ('666','43921','1','衙役','70','0','3','2655','1593','7432','3716','10','0','1','NPC_Icon26'), ('667','43921','2','班头','70','0','3','2655','1593','7432','3716','10','0','1','BJ005'), ('668','43921','3','捕役甲','70','0','4','3186','1593','8919','2655','10','0','1','BJ001'), ('669','43921','4','捕役乙','70','0','4','3186','1593','8919','2655','10','0','1','BJ002'), ('670','43921','5','捕役丙','70','0','3','2655','1593','7432','3716','10','0','1','BJ003'), ('671','43931','1','王太守','70','0','1','2972','2972','9986','2378','10','0','1','NPC_Icon27'), ('672','43931','2','捕快甲','70','0','2','3567','2378','9986','2378','10','0','1','BJ001'), ('673','43931','3','捕快乙','70','0','1','2972','2972','9986','2378','10','0','1','BJ002'), ('674','43931','4','捕快丙','70','0','3','2972','1784','8322','4161','10','0','1','BJ003'), ('675','43931','5','捕快丁','70','0','4','3567','1784','9986','2972','10','0','1','BJ004'), ('676','43941','1','梁中书','70','0','1','3270','3270','21670','2616','10','0','1','NPC_Icon28'), ('677','43941','2','杨提辖','70','0','3','3270','1962','18058','4577','10','0','1','YX022'), ('678','43941','3','张提辖','70','0','2','3924','2616','21670','2616','10','0','1','YX015'), ('679','43941','4','李提辖','70','0','4','3924','1962','21670','3270','10','0','1','YX003'), ('680','43941','5','王提辖','70','0','4','3924','1962','21670','3270','10','0','1','YX021'), ('681','44011','1','报名官','70','0','3','2829','1698','7922','3961','10','0','1','NPC_Icon29'), ('682','44011','2','协会长','70','0','4','3395','1698','9506','2829','10','0','1','YX023'), ('683','44011','3','协会官','70','0','1','2829','2829','9506','2264','10','0','1','YX027'), ('684','44011','4','协会员','70','0','4','3395','1698','9506','2829','10','0','1','YX022'), ('685','44011','5','蹴鞠员','70','0','1','2829','2829','9506','2264','10','0','1','YX010'), ('686','44021','1','陆谦','70','0','4','3395','1698','9506','2829','10','0','1','NPC_Icon30'), ('687','44021','2','协会长','70','0','3','2829','1698','7922','3961','10','0','1','YX023'), ('688','44021','3','协会官','70','0','4','3395','1698','9506','2829','10','0','1','YX027'), ('689','44021','4','协会员','70','0','1','2829','2829','9506','2264','10','0','1','YX022'), ('690','44021','5','蹴鞠员','70','0','2','3395','2264','9506','2264','10','0','1','YX010'), ('691','44031','1','高衙内','70','0','2','3819','2546','10693','2546','10','0','1','NPC_Icon31'), ('692','44031','2','协会长','70','0','4','3819','1910','10693','3183','10','0','1','YX023'), ('693','44031','3','协会官','70','0','2','3819','2546','10693','2546','10','0','1','YX027'), ('694','44031','4','协会员','70','0','4','3819','1910','10693','3183','10','0','1','YX022'), ('695','44031','5','蹴鞠员','70','0','4','3819','1910','10693','3183','10','0','1','YX010'), ('696','44041','1','高俅','70','0','1','3501','3501','23203','2801','10','0','1','NPC_Icon32'), ('697','44041','2','协会长','70','0','5','4901','2801','15469','3501','10','0','1','YX023'), ('698','44041','3','协会官','70','0','5','4901','2801','15469','3501','10','0','1','YX027'), ('699','44041','4','协会员','70','0','5','4901','2801','15469','3501','10','0','1','YX022'), ('700','44041','5','蹴鞠员','70','0','5','4901','2801','15469','3501','10','0','1','YX010');
INSERT INTO `ol_monster_bak` VALUES ('701','44111','1','店小二','70','0','1','3279','3279','11017','2623','10','0','1','NPC_Icon1'), ('702','44111','2','店小三','70','0','1','3279','3279','11017','2623','10','0','1','YX029'), ('703','44111','3','店小四','70','0','3','3279','1968','9181','4591','10','0','1','YX034'), ('704','44111','4','店小五','70','0','1','3279','3279','11017','2623','10','0','1','YX037'), ('705','44111','5','店小六','70','0','3','3279','1968','9181','4591','10','0','1','YX018'), ('706','44121','1','药贩子','70','0','4','3935','1968','11017','3279','10','0','1','NPC_Icon2'), ('707','44121','2','二把手','70','0','3','3279','1968','9181','4591','10','0','1','YX032'), ('708','44121','3','三把手','70','0','4','3935','1968','11017','3279','10','0','1','YX041'), ('709','44121','4','四把手','70','0','4','3935','1968','11017','3279','10','0','1','YX007'), ('710','44121','5','五把手','70','0','1','3279','3279','11017','2623','10','0','1','YX035'), ('711','44131','1','潘金莲','70','0','4','4046','2023','11327','3371','10','0','1','NPC_Icon3'), ('712','44131','2','银莲','70','0','4','4046','2023','11327','3371','10','0','1','YX029'), ('713','44131','3','翠莲','70','0','3','3371','2023','9439','4720','10','0','1','YX030'), ('714','44131','4','宝莲','70','0','1','3371','3371','11327','2697','10','0','1','YX014'), ('715','44131','5','花莲','70','0','4','4046','2023','11327','3371','10','0','1','BJ006'), ('716','44141','1','西门庆','70','0','1','3709','3709','24579','2967','10','0','1','NPC_Icon4'), ('717','44141','2','打手','70','0','1','3709','3709','24579','2967','10','0','1','NPC_Icon7'), ('718','44141','3','杀手','70','0','2','4450','2967','24579','2967','10','0','1','YX026'), ('719','44141','4','帮手','70','0','2','4450','2967','24579','2967','10','0','1','YX044'), ('720','44141','5','凶手','70','0','2','4450','2967','24579','2967','10','0','1','YX029'), ('721','44211','1','伙计','70','0','3','3404','2043','9531','4766','10','0','1','NPC_Icon5'), ('722','44211','2','二伙计','70','0','4','4085','2043','11437','3404','10','0','1','YX020'), ('723','44211','3','三伙计','70','0','3','3404','2043','9531','4766','10','0','1','YX020'), ('724','44211','4','四伙计','70','0','3','3404','2043','9531','4766','10','0','1','YX001'), ('725','44211','5','五伙计','70','0','1','3404','3404','11437','2723','10','0','1','YX005'), ('726','44221','1','镇关西','70','0','1','3404','3404','11437','2723','10','0','1','NPC_Icon6'), ('727','44221','2','小徒弟','70','0','4','4085','2043','11437','3404','10','0','1','YX034'), ('728','44221','3','二徒弟','70','0','4','4085','2043','11437','3404','10','0','1','YX025'), ('729','44221','4','三徒弟','70','0','2','4085','2723','11437','2723','10','0','1','YX008'), ('730','44221','5','四徒弟','70','0','2','4085','2723','11437','2723','10','0','1','YX017'), ('731','44231','1','打手','70','0','2','4290','2860','12010','2860','10','0','1','NPC_Icon7'), ('732','44231','2','杀手','70','0','4','4290','2145','12010','3575','10','0','1','YX022'), ('733','44231','3','帮手','70','0','2','4290','2860','12010','2860','10','0','1','YX005'), ('734','44231','4','凶手','70','0','4','4290','2145','12010','3575','10','0','1','YX035'), ('735','44231','5','助手','70','0','3','3575','2145','10008','5004','10','0','1','YX038'), ('736','44241','1','蒋门神','70','0','4','4719','2360','26061','3932','10','0','1','NPC_Icon8'), ('737','44241','2','打手','70','0','4','4719','2360','26061','3932','10','0','1','NPC_Icon7'), ('738','44241','3','杀手','70','0','1','3932','3932','26061','3146','10','0','1','YX028'), ('739','44241','4','凶手','70','0','1','3932','3932','26061','3146','10','0','1','YX004'), ('740','44241','5','帮手','70','0','1','3932','3932','26061','3146','10','0','1','YX018'), ('741','44311','1','大厨','70','0','4','4235','2118','11857','3529','10','0','1','NPC_Icon9'), ('742','44311','2','大洗菜员','70','0','3','3529','2118','9881','4941','10','0','1','YX041'), ('743','44311','3','二洗菜员','70','0','2','4235','2823','11857','2823','10','0','1','YX015'), ('744','44311','4','三洗菜员','70','0','4','4235','2118','11857','3529','10','0','1','YX023'), ('745','44311','5','四洗菜员','70','0','4','4235','2118','11857','3529','10','0','1','YX037'), ('746','44321','1','管事','70','0','2','4235','2823','11857','2823','10','0','1','NPC_Icon10'), ('747','44321','2','佣人甲','70','0','4','4235','2118','11857','3529','10','0','1','YX024'), ('748','44321','3','佣人乙','70','0','3','3529','2118','9881','4941','10','0','1','YX024'), ('749','44321','4','佣人丙','70','0','1','3529','3529','11857','2823','10','0','1','YX014'), ('750','44321','5','佣人丁','70','0','4','4235','2118','11857','3529','10','0','1','YX027'), ('751','44331','1','张团练','70','0','2','4440','2960','12430','2960','10','0','1','NPC_Icon11'), ('752','44331','2','小陪练','70','0','4','4440','2220','12430','3700','10','0','1','YX027'), ('753','44331','3','二陪练','70','0','4','4440','2220','12430','3700','10','0','1','YX030'), ('754','44331','4','三陪练','70','0','3','3700','2220','10358','5179','10','0','1','YX039'), ('755','44331','5','四陪练','70','0','4','4440','2220','12430','3700','10','0','1','YX010'), ('756','44341','1','张都监','70','0','4','4884','2442','26972','4070','10','0','1','NPC_Icon12'), ('757','44341','2','张秘书','70','0','3','4070','2442','22477','5697','10','0','1','YX017'), ('758','44341','3','张车夫','70','0','3','4070','2442','22477','5697','10','0','1','YX002'), ('759','44341','4','张执行','70','0','4','4884','2442','26972','4070','10','0','1','YX006'), ('760','44341','5','张工','70','0','4','4884','2442','26972','4070','10','0','1','YX043'), ('761','44411','1','刘老汉','70','0','1','3654','3654','12277','2923','10','0','1','NPC_Icon13'), ('762','44411','2','小儿子','70','0','4','4385','2193','12277','3654','10','0','1','YX027'), ('763','44411','3','二儿子','70','0','4','4385','2193','12277','3654','10','0','1','YX033'), ('764','44411','4','三儿子','70','0','4','4385','2193','12277','3654','10','0','1','YX015'), ('765','44411','5','四儿子','70','0','4','4385','2193','12277','3654','10','0','1','YX003'), ('766','44421','1','刘夫人','70','0','3','3654','2193','10231','5116','10','0','1','NPC_Icon14'), ('767','44421','2','邻居','70','0','2','4385','2923','12277','2923','10','0','1','YX011'), ('768','44421','3','泼妇','70','0','4','4385','2193','12277','3654','10','0','1','YX017'), ('769','44421','4','悍女','70','0','1','3654','3654','12277','2923','10','0','1','YX030'), ('770','44421','5','彪女','70','0','4','4385','2193','12277','3654','10','0','1','YX037'), ('771','44431','1','镇三山','70','0','2','4590','3060','12850','3060','10','0','1','NPC_Icon15'), ('772','44431','2','黑兄弟','70','0','2','4590','3060','12850','3060','10','0','1','YX040'), ('773','44431','3','二兄弟','70','0','1','3825','3825','12850','3060','10','0','1','YX020'), ('774','44431','4','三兄弟','70','0','1','3825','3825','12850','3060','10','0','1','YX042'), ('775','44431','5','四兄弟','70','0','4','4590','2295','12850','3825','10','0','1','YX008'), ('776','44441','1','刘高','70','0','4','5049','2525','27884','4207','10','0','1','NPC_Icon16'), ('777','44441','2','打手','70','0','2','5049','3366','27884','3366','10','0','1','NPC_Icon7'), ('778','44441','3','刺客','70','0','2','5049','3366','27884','3366','10','0','1','YX009'), ('779','44441','4','杀手','70','0','4','5049','2525','27884','4207','10','0','1','YX040'), ('780','44441','5','凶手','70','0','3','4207','2525','23237','5890','10','0','1','YX037'), ('781','44511','1','士兵','70','0','3','3779','2268','10581','5291','10','0','1','NPC_Icon17'), ('782','44511','2','士兵甲','70','0','2','4535','3023','12824','3023','10','0','1','YX004'), ('783','44511','3','士兵乙','70','0','4','4535','2268','12824','3779','10','0','1','YX006'), ('784','44511','4','士兵丙','70','0','1','3779','3779','12824','3023','10','0','1','YX003'), ('785','44511','5','士兵丁','70','0','4','4535','2268','12824','3779','10','0','1','YX016'), ('786','44521','1','黄通判','70','0','4','4535','2268','12824','3779','10','0','1','NPC_Icon18'), ('787','44521','2','传令员','70','0','3','3779','2268','10687','5291','10','0','1','YX027'), ('788','44521','3','士兵甲','70','0','1','3779','3779','12824','3023','10','0','1','YX034'), ('789','44521','4','士兵乙','70','0','4','4535','2268','12824','3779','10','0','1','YX007'), ('790','44521','5','士兵丙','70','0','2','4535','3023','12824','3023','10','0','1','YX009'), ('791','44531','1','监斩官','70','0','4','4740','2370','13403','3950','10','0','1','NPC_Icon19'), ('792','44531','2','行刑员','70','0','1','3950','3950','13403','3160','10','0','1','YX012'), ('793','44531','3','士兵甲','70','0','1','3950','3950','13403','3160','10','0','1','YX026'), ('794','44531','4','士兵乙','70','0','4','4740','2370','13403','3950','10','0','1','YX029'), ('795','44531','5','士兵丙','70','0','3','3950','2370','11169','5529','10','0','1','YX015'), ('796','44541','1','蔡九','70','0','1','4345','4345','29083','3476','10','0','1','NPC_Icon20'), ('797','44541','2','芹蔡','70','0','4','5214','2607','29083','4345','10','0','1','YX041'), ('798','44541','3','花蔡','70','0','4','5214','2607','29083','4345','10','0','1','YX042'), ('799','44541','4','韭蔡','70','0','2','5214','3476','29083','3476','10','0','1','YX017'), ('800','44541','5','白蔡','70','0','2','5214','3476','29083','3476','10','0','1','YX025');
INSERT INTO `ol_monster_bak` VALUES ('801','44611','1','李应','70','0','4','4685','2343','13379','3904','10','0','1','NPC_Icon21'), ('802','44611','2','打手','70','0','3','3904','2343','11150','5466','10','0','1','NPC_Icon7'), ('803','44611','3','杀手','70','0','3','3904','2343','11150','5466','10','0','1','BJ005'), ('804','44611','4','帮手','70','0','4','4685','2343','13379','3904','10','0','1','BJ006'), ('805','44611','5','凶手','70','0','4','4685','2343','13379','3904','10','0','1','BJ007'), ('806','44621','1','扈三娘','70','0','2','4685','3123','13379','3123','10','0','1','NPC_Icon22'), ('807','44621','2','打手','70','0','2','4685','3123','13379','3123','10','0','1','NPC_Icon7'), ('808','44621','3','杀手','70','0','1','3904','3904','13379','3123','10','0','1','BJ005'), ('809','44621','4','帮手','70','0','4','4685','2343','13379','3904','10','0','1','BJ006'), ('810','44621','5','凶手','70','0','3','3904','2343','11150','5466','10','0','1','BJ007'), ('811','44631','1','祝家三虎','70','0','1','4075','4075','13964','3260','10','0','1','NPC_Icon23'), ('812','44631','2','打手','70','0','3','4075','2445','11637','5704','10','0','1','NPC_Icon7'), ('813','44631','3','杀手','70','0','4','4890','2445','13964','4075','10','0','1','BJ005'), ('814','44631','4','帮手','70','0','3','4075','2445','11637','5704','10','0','1','BJ006'), ('815','44631','5','凶手','70','0','4','4890','2445','13964','4075','10','0','1','BJ007'), ('816','44641','1','祝庄主','70','0','4','5379','2690','30301','4482','10','0','1','NPC_Icon24'), ('817','44641','2','打手','70','0','1','4482','4482','30301','3586','10','0','1','NPC_Icon7'), ('818','44641','3','杀手','70','0','1','4482','4482','30301','3586','10','0','1','BJ005'), ('819','44641','4','帮手','70','0','3','4482','2690','25251','6275','10','0','1','BJ006'), ('820','44641','5','凶手','70','0','3','4482','2690','25251','6275','10','0','1','BJ007'), ('821','44711','1','秀才','70','0','1','4154','4154','14376','3323','10','0','1','NPC_Icon25'), ('822','44711','2','黄师爷','70','0','4','4985','2493','14376','4154','10','0','1','YX033'), ('823','44711','3','秀才甲','70','0','1','4154','4154','14376','3323','10','0','1','YX035'), ('824','44711','4','秀才乙','70','0','4','4985','2493','14376','4154','10','0','1','YX036'), ('825','44711','5','秀才丙','70','0','4','4985','2493','14376','4154','10','0','1','YX038'), ('826','44721','1','衙役','70','0','3','4154','2493','11980','5816','10','0','1','NPC_Icon26'), ('827','44721','2','班头','70','0','3','4154','2493','11980','5816','10','0','1','BJ005'), ('828','44721','3','捕役甲','70','0','4','4985','2493','14376','4154','10','0','1','BJ001'), ('829','44721','4','捕役乙','70','0','4','4985','2493','14376','4154','10','0','1','BJ002'), ('830','44721','5','捕役丙','70','0','3','4154','2493','11980','5816','10','0','1','BJ003'), ('831','44731','1','王太守','70','0','1','4325','4325','14966','3460','10','0','1','NPC_Icon27'), ('832','44731','2','捕快甲','70','0','2','5190','3460','14966','3460','10','0','1','BJ001'), ('833','44731','3','捕快乙','70','0','1','4325','4325','14966','3460','10','0','1','BJ002'), ('834','44731','4','捕快丙','70','0','3','4325','2595','12472','6054','10','0','1','BJ003'), ('835','44731','5','捕快丁','70','0','4','5190','2595','14966','4325','10','0','1','BJ004'), ('836','44741','1','梁中书','70','0','1','4757','4757','32475','3806','10','0','1','NPC_Icon28'), ('837','44741','2','杨提辖','70','0','3','4757','2855','27063','6660','10','0','1','YX022'), ('838','44741','3','张提辖','70','0','2','5709','3806','32475','3806','10','0','1','YX015'), ('839','44741','4','李提辖','70','0','4','5709','2855','32475','4757','10','0','1','YX003'), ('840','44741','5','王提辖','70','0','4','5709','2855','32475','4757','10','0','1','YX021'), ('841','44811','1','报名官','70','0','3','4404','2643','12948','6166','10','0','1','NPC_Icon29'), ('842','44811','2','协会长','70','0','4','5285','2643','15537','4404','10','0','1','YX023'), ('843','44811','3','协会官','70','0','1','4404','4404','15537','3523','10','0','1','YX027'), ('844','44811','4','协会员','70','0','4','5285','2643','15537','4404','10','0','1','YX022'), ('845','44811','5','蹴鞠员','70','0','1','4404','4404','15537','3523','10','0','1','YX010'), ('846','44821','1','陆谦','70','0','4','5285','2643','15537','4404','10','0','1','NPC_Icon30'), ('847','44821','2','协会长','70','0','3','4404','2643','12948','6166','10','0','1','YX023'), ('848','44821','3','协会官','70','0','4','5285','2643','15537','4404','10','0','1','YX027'), ('849','44821','4','协会员','70','0','1','4404','4404','15537','3523','10','0','1','YX022'), ('850','44821','5','蹴鞠员','70','0','2','5285','3523','15537','3523','10','0','1','YX010'), ('851','44831','1','高衙内','70','0','2','5490','3660','16138','3660','10','0','1','NPC_Icon31'), ('852','44831','2','协会长','70','0','4','5490','2745','16138','4575','10','0','1','YX023'), ('853','44831','3','协会官','70','0','2','5490','3660','16138','3660','10','0','1','YX027'), ('854','44831','4','协会员','70','0','4','5490','2745','16138','4575','10','0','1','YX022'), ('855','44831','5','蹴鞠员','70','0','4','5490','2745','16138','4575','10','0','1','YX010'), ('856','44841','1','高俅','70','0','1','5032','5032','35020','4026','10','0','1','NPC_Icon32'), ('857','44841','2','协会长','70','0','5','7045','4026','23347','5032','10','0','1','YX023'), ('858','44841','3','协会官','70','0','5','7045','4026','23347','5032','10','0','1','YX027'), ('859','44841','4','协会员','70','0','5','7045','4026','23347','5032','10','0','1','YX022'), ('860','44841','5','蹴鞠员','70','0','5','7045','4026','23347','5032','10','0','1','YX010');
INSERT INTO `ol_notice` VALUES ('1','','',''), ('2','服务器暂未开放',NULL,NULL), ('3','请输入领取的激活码',NULL,NULL);
INSERT INTO `ol_npc` VALUES ('10131','5','闻达','重甲','1'), ('10141','4','李成','长枪','1'), ('10151','2','看守','长枪','1'), ('10161','3','看守','重甲','1'), ('10171','2','流氓','长枪','1'), ('10211','10','洪教头','长枪','2'), ('10221','9','董超','弓箭','1'), ('10231','8','薛霸','长枪','1'), ('10241','7','看守','重甲','1'), ('10251','6','老狱卒','长枪','1'), ('10311','15','高廉','长枪','2'), ('10321','14','殷天锡','轻骑','1'), ('10331','13','于直','连弩','1'), ('10341','12','温文宝','长枪','1'), ('10351','11','狱卒','弓箭','1'), ('10411','20','周瑾','弓箭','2'), ('10421','19','李鬼','重甲','1'), ('10431','18','衙役','轻骑','1'), ('10441','17','看守','弓箭','1'), ('10451','16','地痞','轻骑','1'), ('10511','25','王押司','轻骑','2'), ('10521','24','潘巧云','轻骑','1'), ('10531','23','海和尚','长枪','1'), ('10541','22','李三千','重甲','1'), ('10551','21','流氓','连弩','1'), ('10611','30','贺重宝','轻骑','2'), ('10621','29','罗真人','重甲','1'), ('10631','28','教头','重甲','1'), ('10641','27','看守','连弩','1'), ('10651','26','恶霸','轻骑','1'), ('20131','5','张旺','重甲','1'), ('20141','4','孙三','长枪','1'), ('20151','2','盗贼','长枪','1'), ('20161','3','盗贼','重甲','1'), ('20171','2','水匪','长枪','1'), ('20211','10','刘高','长枪','2'), ('20221','9','知寨夫人','弓箭','1'), ('20231','8','白健','长枪','1'), ('20241','7','盗贼','重甲','1'), ('20251','6','山贼','长枪','1'), ('20311','15','王道人','长枪','2'), ('20321','14','广智','轻骑','1'), ('20331','13','武教头','连弩','1'), ('20341','12','看守','长枪','1'), ('20351','11','土匪','弓箭','1'), ('20411','20','栾廷玉','弓箭','2'), ('20421','19','祝龙','重甲','1'), ('20431','18','祝虎','轻骑','1'), ('20441','17','祝彪','弓箭','1'), ('20451','16','强盗','轻骑','1'), ('20511','25','邓龙','轻骑','2'), ('20521','24','崔道成','轻骑','1'), ('20531','23','丘小乙','长枪','1'), ('20541','22','看守','重甲','1'), ('20551','21','土匪','连弩','1'), ('20611','30','包吉','轻骑','2'), ('20621','29','王正','重甲','1'), ('20631','28','黄文炳','重甲','1'), ('20641','27','盗贼','连弩','1'), ('20651','26','采花贼','轻骑','1'), ('30111','30','高衙内','轻骑','2'), ('30121','29','富安','重甲','1'), ('30131','28','陆谦','重甲','1'), ('30141','27','牛二','连弩','1'), ('30151','26','守门人','轻骑','1'), ('30211','35','张文远','连弩','2'), ('30221','34','阎婆惜','长枪','1'), ('30231','33','王小三','轻骑','1'), ('30241','32','看守','弓箭','1'), ('30251','31','强盗','长枪','1'), ('30311','40','西门庆','弓箭','2'), ('30321','39','王婆','长枪','1'), ('30331','38','知县','连弩','1'), ('30341','37','看守','重甲','1'), ('30351','36','山贼','轻骑','1'), ('30411','45','富安','轻骑','2'), ('30421','44','李吉','重甲','1'), ('30431','43','打手','弓箭','1'), ('30441','42','看守','连弩','1'), ('30451','41','土匪','轻骑','1'), ('30511','50','广慧','长枪','2'), ('30521','49','蒋门神','弓箭','1'), ('30531','48','恶霸','重甲','1'), ('30541','47','看守','连弩','1'), ('30551','46','家丁','连弩','1'), ('30611','55','童贯','连弩','2'), ('30621','54','郑屠户','连弩','1'), ('30631','53','打手','轻骑','1'), ('30641','52','看守','连弩','1'), ('30651','51','恶霸','重甲','1'), ('30711','60','蔡京','重甲','2'), ('30721','59','李彦','轻骑','1'), ('30731','58','王彬','长枪','1'), ('30741','57','看守','连弩','1'), ('30751','56','山贼','重甲','1');
INSERT INTO `ol_online` VALUES ('1','1382730675');
INSERT INTO `ol_player` VALUES ('1','1','stefan','4bf764883dad11e3bd95005056a3001f','0','0','0','1','0','0','00000000','1382730379','0','1382723179','1382730378','25000','100','0','13.000','','','1382730379','30','0','1382723178','1382730198','0','1','1','1','1','1','0','','0','0','0','0','0','0','0','0','1','0','0','0','0','1382730379','0','1','0','0','0','80','0','0','0','0','','','0','0','0','0','00000000000000000000000000000000','0','109','0','0','0','0','0','0','50','00000000000000000000000000000000','5001001','0','00000','0','0','0','0','0','');
INSERT INTO `ol_player_items` VALUES ('1','20075','1','0','1','0','0','0'), ('2','10036','7','0','1','0','0','0'), ('3','10033','7','0','1','0','0','0'), ('4','10035','7','0','1','0','0','0'), ('5','10034','7','0','1','0','0','0'), ('6','10032','7','0','1','0','0','0'), ('7','10001','10','0','1','0','0','0'), ('8','10040','10','0','1','0','0','0'), ('9','18522','1','0','1','0','0','0');
INSERT INTO `ol_player_stage` VALUES ('1','1','0','0','0','0','1382730379','0','0','0','0','6');
INSERT INTO `ol_playergeneral` VALUES ('1','1','1','朱贵','1','1','300','YX016','0','0','0','0','','0','2','1','20','1','0','0','0','0','0','2','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0'), ('2','1','2','张顺','1','1','250','YX015','0','0','0','0','','0','3','1','18','1','0','0','0','0','0','3','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0'), ('3','1','3','武大郎','1','1','290','YX011','0','0','0','0','','0','1','1','16','1','0','0','0','0','0','1','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0');
INSERT INTO `ol_playerxlw` VALUES ('1','1','0','0','0','1','1'), ('2','1','0','0','0','1','2'), ('3','1','0','0','0','0','3'), ('4','1','0','0','0','0','4'), ('5','1','0','0','0','0','5'), ('6','1','0','0','0','0','6'), ('7','1','0','0','0','0','7'), ('8','1','0','0','0','0','8'), ('9','1','0','0','0','0','9');
INSERT INTO `ol_quests_new_status` VALUES ('1','a:5:{i:5002033;i:2;i:5002032;i:2;i:5002031;i:2;i:5002030;i:2;i:5001001;i:2;}');
INSERT INTO `ol_ranklist` VALUES ('1','108','0','1','0','0','0'), ('2','108','0','1','0','0','0'), ('3','108','0','1','0','0','0'), ('4','108','0','1','0','0','0'), ('5','108','0','1','0','0','0'), ('6','108','0','0','1','0','0'), ('7','108','0','0','1','0','0'), ('8','108','0','0','1','0','0'), ('9','108','0','0','1','0','0'), ('10','108','0','0','1','0','0'), ('11','108','0','0','0','1','0'), ('12','108','0','0','0','1','0'), ('13','108','0','0','0','1','0'), ('14','108','0','0','0','1','0'), ('15','108','0','0','0','1','0'), ('16','107','0','1','0','0','0'), ('17','107','0','1','0','0','0'), ('18','107','0','1','0','0','0'), ('19','107','0','1','0','0','0'), ('20','107','0','1','0','0','0'), ('21','107','0','0','1','0','0'), ('22','107','0','0','1','0','0'), ('23','107','0','0','1','0','0'), ('24','107','0','0','1','0','0'), ('25','107','0','0','1','0','0'), ('26','107','0','0','0','1','0'), ('27','107','0','0','0','1','0'), ('28','107','0','0','0','1','0'), ('29','107','0','0','0','1','0'), ('30','107','0','0','0','1','0'), ('31','106','0','1','0','0','0'), ('32','106','0','1','0','0','0'), ('33','106','0','1','0','0','0'), ('34','106','0','1','0','0','0'), ('35','106','0','1','0','0','0'), ('36','106','0','0','1','0','0'), ('37','106','0','0','1','0','0'), ('38','106','0','0','1','0','0'), ('39','106','0','0','1','0','0'), ('40','106','0','0','1','0','0'), ('41','106','0','0','0','1','0'), ('42','106','0','0','0','1','0'), ('43','106','0','0','0','1','0'), ('44','106','0','0','0','1','0'), ('45','106','0','0','0','1','0'), ('46','105','0','1','0','0','0'), ('47','105','0','1','0','0','0'), ('48','105','0','1','0','0','0'), ('49','105','0','1','0','0','0'), ('50','105','0','1','0','0','0'), ('51','105','0','0','1','0','0'), ('52','105','0','0','1','0','0'), ('53','105','0','0','1','0','0'), ('54','105','0','0','1','0','0'), ('55','105','0','0','1','0','0'), ('56','105','0','0','0','1','0'), ('57','105','0','0','0','1','0'), ('58','105','0','0','0','1','0'), ('59','105','0','0','0','1','0'), ('60','105','0','0','0','1','0'), ('61','104','0','1','0','0','0'), ('62','104','0','1','0','0','0'), ('63','104','0','1','0','0','0'), ('64','104','0','1','0','0','0'), ('65','104','0','1','0','0','0'), ('66','104','0','0','1','0','0'), ('67','104','0','0','1','0','0'), ('68','104','0','0','1','0','0'), ('69','104','0','0','1','0','0'), ('70','104','0','0','1','0','0'), ('71','104','0','0','0','1','0'), ('72','104','0','0','0','1','0'), ('73','104','0','0','0','1','0'), ('74','104','0','0','0','1','0'), ('75','104','0','0','0','1','0'), ('76','103','0','1','0','0','0'), ('77','103','0','1','0','0','0'), ('78','103','0','1','0','0','0'), ('79','103','0','1','0','0','0'), ('80','103','0','1','0','0','0'), ('81','103','0','0','1','0','0'), ('82','103','0','0','1','0','0'), ('83','103','0','0','1','0','0'), ('84','103','0','0','1','0','0'), ('85','103','0','0','1','0','0'), ('86','103','0','0','0','1','0'), ('87','103','0','0','0','1','0'), ('88','103','0','0','0','1','0'), ('89','103','0','0','0','1','0'), ('90','103','0','0','0','1','0'), ('91','102','0','1','0','0','0'), ('92','102','0','1','0','0','0'), ('93','102','0','1','0','0','0'), ('94','102','0','1','0','0','0'), ('95','102','0','1','0','0','0'), ('96','102','0','0','1','0','0'), ('97','102','0','0','1','0','0'), ('98','102','0','0','1','0','0'), ('99','102','0','0','1','0','0'), ('100','102','0','0','1','0','0');
INSERT INTO `ol_ranklist` VALUES ('101','102','0','0','0','1','0'), ('102','102','0','0','0','1','0'), ('103','102','0','0','0','1','0'), ('104','102','0','0','0','1','0'), ('105','102','0','0','0','1','0'), ('106','101','0','1','0','0','0'), ('107','101','0','1','0','0','0'), ('108','101','0','1','0','0','0'), ('109','101','0','1','0','0','0'), ('110','101','0','1','0','0','0'), ('111','101','0','0','1','0','0'), ('112','101','0','0','1','0','0'), ('113','101','0','0','1','0','0'), ('114','101','0','0','1','0','0'), ('115','101','0','0','1','0','0'), ('116','101','0','0','0','1','0'), ('117','101','0','0','0','1','0'), ('118','101','0','0','0','1','0'), ('119','101','0','0','0','1','0'), ('120','101','0','0','0','1','0'), ('121','100','0','1','0','0','0'), ('122','100','0','1','0','0','0'), ('123','100','0','1','0','0','0'), ('124','100','0','1','0','0','0'), ('125','100','0','1','0','0','0'), ('126','100','0','0','1','0','0'), ('127','100','0','0','1','0','0'), ('128','100','0','0','1','0','0'), ('129','100','0','0','1','0','0'), ('130','100','0','0','1','0','0'), ('131','100','0','0','0','1','0'), ('132','100','0','0','0','1','0'), ('133','100','0','0','0','1','0'), ('134','100','0','0','0','1','0'), ('135','100','0','0','0','1','0'), ('136','99','0','1','0','0','0'), ('137','99','0','1','0','0','0'), ('138','99','0','1','0','0','0'), ('139','99','0','1','0','0','0'), ('140','99','0','1','0','0','0'), ('141','99','0','0','1','0','0'), ('142','99','0','0','1','0','0'), ('143','99','0','0','1','0','0'), ('144','99','0','0','1','0','0'), ('145','99','0','0','1','0','0'), ('146','99','0','0','0','1','0'), ('147','99','0','0','0','1','0'), ('148','99','0','0','0','1','0'), ('149','99','0','0','0','1','0'), ('150','99','0','0','0','1','0'), ('151','98','0','1','0','0','0'), ('152','98','0','1','0','0','0'), ('153','98','0','1','0','0','0'), ('154','98','0','1','0','0','0'), ('155','98','0','1','0','0','0'), ('156','98','0','0','1','0','0'), ('157','98','0','0','1','0','0'), ('158','98','0','0','1','0','0'), ('159','98','0','0','1','0','0'), ('160','98','0','0','1','0','0'), ('161','98','0','0','0','1','0'), ('162','98','0','0','0','1','0'), ('163','98','0','0','0','1','0'), ('164','98','0','0','0','1','0'), ('165','98','0','0','0','1','0'), ('166','97','0','1','0','0','0'), ('167','97','0','1','0','0','0'), ('168','97','0','1','0','0','0'), ('169','97','0','1','0','0','0'), ('170','97','0','1','0','0','0'), ('171','97','0','0','1','0','0'), ('172','97','0','0','1','0','0'), ('173','97','0','0','1','0','0'), ('174','97','0','0','1','0','0'), ('175','97','0','0','1','0','0'), ('176','97','0','0','0','1','0'), ('177','97','0','0','0','1','0'), ('178','97','0','0','0','1','0'), ('179','97','0','0','0','1','0'), ('180','97','0','0','0','1','0'), ('181','96','0','1','0','0','0'), ('182','96','0','1','0','0','0'), ('183','96','0','1','0','0','0'), ('184','96','0','1','0','0','0'), ('185','96','0','1','0','0','0'), ('186','96','0','0','1','0','0'), ('187','96','0','0','1','0','0'), ('188','96','0','0','1','0','0'), ('189','96','0','0','1','0','0'), ('190','96','0','0','1','0','0'), ('191','96','0','0','0','1','0'), ('192','96','0','0','0','1','0'), ('193','96','0','0','0','1','0'), ('194','96','0','0','0','1','0'), ('195','96','0','0','0','1','0'), ('196','95','0','1','0','0','0'), ('197','95','0','1','0','0','0'), ('198','95','0','1','0','0','0'), ('199','95','0','1','0','0','0'), ('200','95','0','1','0','0','0');
INSERT INTO `ol_ranklist` VALUES ('201','95','0','0','1','0','0'), ('202','95','0','0','1','0','0'), ('203','95','0','0','1','0','0'), ('204','95','0','0','1','0','0'), ('205','95','0','0','1','0','0'), ('206','95','0','0','0','1','0'), ('207','95','0','0','0','1','0'), ('208','95','0','0','0','1','0'), ('209','95','0','0','0','1','0'), ('210','95','0','0','0','1','0'), ('211','94','0','1','0','0','0'), ('212','94','0','1','0','0','0'), ('213','94','0','1','0','0','0'), ('214','94','0','1','0','0','0'), ('215','94','0','1','0','0','0'), ('216','94','0','0','1','0','0'), ('217','94','0','0','1','0','0'), ('218','94','0','0','1','0','0'), ('219','94','0','0','1','0','0'), ('220','94','0','0','1','0','0'), ('221','94','0','0','0','1','0'), ('222','94','0','0','0','1','0'), ('223','94','0','0','0','1','0'), ('224','94','0','0','0','1','0'), ('225','94','0','0','0','1','0'), ('226','93','0','1','0','0','0'), ('227','93','0','1','0','0','0'), ('228','93','0','1','0','0','0'), ('229','93','0','1','0','0','0'), ('230','93','0','1','0','0','0'), ('231','93','0','0','1','0','0'), ('232','93','0','0','1','0','0'), ('233','93','0','0','1','0','0'), ('234','93','0','0','1','0','0'), ('235','93','0','0','1','0','0'), ('236','93','0','0','0','1','0'), ('237','93','0','0','0','1','0'), ('238','93','0','0','0','1','0'), ('239','93','0','0','0','1','0'), ('240','93','0','0','0','1','0'), ('241','92','0','1','0','0','0'), ('242','92','0','1','0','0','0'), ('243','92','0','1','0','0','0'), ('244','92','0','1','0','0','0'), ('245','92','0','1','0','0','0'), ('246','92','0','0','1','0','0'), ('247','92','0','0','1','0','0'), ('248','92','0','0','1','0','0'), ('249','92','0','0','1','0','0'), ('250','92','0','0','1','0','0'), ('251','92','0','0','0','1','0'), ('252','92','0','0','0','1','0'), ('253','92','0','0','0','1','0'), ('254','92','0','0','0','1','0'), ('255','92','0','0','0','1','0'), ('256','91','0','1','0','0','0'), ('257','91','0','1','0','0','0'), ('258','91','0','1','0','0','0'), ('259','91','0','1','0','0','0'), ('260','91','0','1','0','0','0'), ('261','91','0','0','1','0','0'), ('262','91','0','0','1','0','0'), ('263','91','0','0','1','0','0'), ('264','91','0','0','1','0','0'), ('265','91','0','0','1','0','0'), ('266','91','0','0','0','1','0'), ('267','91','0','0','0','1','0'), ('268','91','0','0','0','1','0'), ('269','91','0','0','0','1','0'), ('270','91','0','0','0','1','0'), ('271','90','0','1','0','0','0'), ('272','90','0','1','0','0','0'), ('273','90','0','1','0','0','0'), ('274','90','0','1','0','0','0'), ('275','90','0','1','0','0','0'), ('276','90','0','0','1','0','0'), ('277','90','0','0','1','0','0'), ('278','90','0','0','1','0','0'), ('279','90','0','0','1','0','0'), ('280','90','0','0','1','0','0'), ('281','90','0','0','0','1','0'), ('282','90','0','0','0','1','0'), ('283','90','0','0','0','1','0'), ('284','90','0','0','0','1','0'), ('285','90','0','0','0','1','0'), ('286','89','0','1','0','0','0'), ('287','89','0','1','0','0','0'), ('288','89','0','1','0','0','0'), ('289','89','0','1','0','0','0'), ('290','89','0','1','0','0','0'), ('291','89','0','0','1','0','0'), ('292','89','0','0','1','0','0'), ('293','89','0','0','1','0','0'), ('294','89','0','0','1','0','0'), ('295','89','0','0','1','0','0'), ('296','89','0','0','0','1','0'), ('297','89','0','0','0','1','0'), ('298','89','0','0','0','1','0'), ('299','89','0','0','0','1','0'), ('300','89','0','0','0','1','0');
INSERT INTO `ol_ranklist` VALUES ('301','88','0','1','0','0','0'), ('302','88','0','1','0','0','0'), ('303','88','0','1','0','0','0'), ('304','88','0','1','0','0','0'), ('305','88','0','1','0','0','0'), ('306','88','0','0','1','0','0'), ('307','88','0','0','1','0','0'), ('308','88','0','0','1','0','0'), ('309','88','0','0','1','0','0'), ('310','88','0','0','1','0','0'), ('311','88','0','0','0','1','0'), ('312','88','0','0','0','1','0'), ('313','88','0','0','0','1','0'), ('314','88','0','0','0','1','0'), ('315','88','0','0','0','1','0'), ('316','87','0','1','0','0','0'), ('317','87','0','1','0','0','0'), ('318','87','0','1','0','0','0'), ('319','87','0','1','0','0','0'), ('320','87','0','1','0','0','0'), ('321','87','0','0','1','0','0'), ('322','87','0','0','1','0','0'), ('323','87','0','0','1','0','0'), ('324','87','0','0','1','0','0'), ('325','87','0','0','1','0','0'), ('326','87','0','0','0','1','0'), ('327','87','0','0','0','1','0'), ('328','87','0','0','0','1','0'), ('329','87','0','0','0','1','0'), ('330','87','0','0','0','1','0'), ('331','86','0','1','0','0','0'), ('332','86','0','1','0','0','0'), ('333','86','0','1','0','0','0'), ('334','86','0','1','0','0','0'), ('335','86','0','1','0','0','0'), ('336','86','0','0','1','0','0'), ('337','86','0','0','1','0','0'), ('338','86','0','0','1','0','0'), ('339','86','0','0','1','0','0'), ('340','86','0','0','1','0','0'), ('341','86','0','0','0','1','0'), ('342','86','0','0','0','1','0'), ('343','86','0','0','0','1','0'), ('344','86','0','0','0','1','0'), ('345','86','0','0','0','1','0'), ('346','85','0','1','0','0','0'), ('347','85','0','1','0','0','0'), ('348','85','0','1','0','0','0'), ('349','85','0','1','0','0','0'), ('350','85','0','1','0','0','0'), ('351','85','0','0','1','0','0'), ('352','85','0','0','1','0','0'), ('353','85','0','0','1','0','0'), ('354','85','0','0','1','0','0'), ('355','85','0','0','1','0','0'), ('356','85','0','0','0','1','0'), ('357','85','0','0','0','1','0'), ('358','85','0','0','0','1','0'), ('359','85','0','0','0','1','0'), ('360','85','0','0','0','1','0'), ('361','84','0','1','0','0','0'), ('362','84','0','1','0','0','0'), ('363','84','0','1','0','0','0'), ('364','84','0','1','0','0','0'), ('365','84','0','1','0','0','0'), ('366','84','0','0','1','0','0'), ('367','84','0','0','1','0','0'), ('368','84','0','0','1','0','0'), ('369','84','0','0','1','0','0'), ('370','84','0','0','1','0','0'), ('371','84','0','0','0','1','0'), ('372','84','0','0','0','1','0'), ('373','84','0','0','0','1','0'), ('374','84','0','0','0','1','0'), ('375','84','0','0','0','1','0'), ('376','83','0','1','0','0','0'), ('377','83','0','1','0','0','0'), ('378','83','0','1','0','0','0'), ('379','83','0','1','0','0','0'), ('380','83','0','1','0','0','0'), ('381','83','0','0','1','0','0'), ('382','83','0','0','1','0','0'), ('383','83','0','0','1','0','0'), ('384','83','0','0','1','0','0'), ('385','83','0','0','1','0','0'), ('386','83','0','0','0','1','0'), ('387','83','0','0','0','1','0'), ('388','83','0','0','0','1','0'), ('389','83','0','0','0','1','0'), ('390','83','0','0','0','1','0'), ('391','82','0','1','0','0','0'), ('392','82','0','1','0','0','0'), ('393','82','0','1','0','0','0'), ('394','82','0','1','0','0','0'), ('395','82','0','1','0','0','0'), ('396','82','0','0','1','0','0'), ('397','82','0','0','1','0','0'), ('398','82','0','0','1','0','0'), ('399','82','0','0','1','0','0'), ('400','82','0','0','1','0','0');
INSERT INTO `ol_ranklist` VALUES ('401','82','0','0','0','1','0'), ('402','82','0','0','0','1','0'), ('403','82','0','0','0','1','0'), ('404','82','0','0','0','1','0'), ('405','82','0','0','0','1','0'), ('406','81','0','1','0','0','0'), ('407','81','0','1','0','0','0'), ('408','81','0','1','0','0','0'), ('409','81','0','1','0','0','0'), ('410','81','0','1','0','0','0'), ('411','81','0','0','1','0','0'), ('412','81','0','0','1','0','0'), ('413','81','0','0','1','0','0'), ('414','81','0','0','1','0','0'), ('415','81','0','0','1','0','0'), ('416','81','0','0','0','1','0'), ('417','81','0','0','0','1','0'), ('418','81','0','0','0','1','0'), ('419','81','0','0','0','1','0'), ('420','81','0','0','0','1','0'), ('421','80','0','1','0','0','0'), ('422','80','0','1','0','0','0'), ('423','80','0','1','0','0','0'), ('424','80','0','1','0','0','0'), ('425','80','0','1','0','0','0'), ('426','80','0','0','1','0','0'), ('427','80','0','0','1','0','0'), ('428','80','0','0','1','0','0'), ('429','80','0','0','1','0','0'), ('430','80','0','0','1','0','0'), ('431','80','0','0','0','1','0'), ('432','80','0','0','0','1','0'), ('433','80','0','0','0','1','0'), ('434','80','0','0','0','1','0'), ('435','80','0','0','0','1','0'), ('436','79','0','1','0','0','0'), ('437','79','0','1','0','0','0'), ('438','79','0','1','0','0','0'), ('439','79','0','1','0','0','0'), ('440','79','0','1','0','0','0'), ('441','79','0','0','1','0','0'), ('442','79','0','0','1','0','0'), ('443','79','0','0','1','0','0'), ('444','79','0','0','1','0','0'), ('445','79','0','0','1','0','0'), ('446','79','0','0','0','1','0'), ('447','79','0','0','0','1','0'), ('448','79','0','0','0','1','0'), ('449','79','0','0','0','1','0'), ('450','79','0','0','0','1','0'), ('451','78','0','1','0','0','0'), ('452','78','0','1','0','0','0'), ('453','78','0','1','0','0','0'), ('454','78','0','1','0','0','0'), ('455','78','0','1','0','0','0'), ('456','78','0','0','1','0','0'), ('457','78','0','0','1','0','0'), ('458','78','0','0','1','0','0'), ('459','78','0','0','1','0','0'), ('460','78','0','0','1','0','0'), ('461','78','0','0','0','1','0'), ('462','78','0','0','0','1','0'), ('463','78','0','0','0','1','0'), ('464','78','0','0','0','1','0'), ('465','78','0','0','0','1','0'), ('466','77','0','1','0','0','0'), ('467','77','0','1','0','0','0'), ('468','77','0','1','0','0','0'), ('469','77','0','1','0','0','0'), ('470','77','0','1','0','0','0'), ('471','77','0','0','1','0','0'), ('472','77','0','0','1','0','0'), ('473','77','0','0','1','0','0'), ('474','77','0','0','1','0','0'), ('475','77','0','0','1','0','0'), ('476','77','0','0','0','1','0'), ('477','77','0','0','0','1','0'), ('478','77','0','0','0','1','0'), ('479','77','0','0','0','1','0'), ('480','77','0','0','0','1','0'), ('481','76','0','1','0','0','0'), ('482','76','0','1','0','0','0'), ('483','76','0','1','0','0','0'), ('484','76','0','1','0','0','0'), ('485','76','0','1','0','0','0'), ('486','76','0','0','1','0','0'), ('487','76','0','0','1','0','0'), ('488','76','0','0','1','0','0'), ('489','76','0','0','1','0','0'), ('490','76','0','0','1','0','0'), ('491','76','0','0','0','1','0'), ('492','76','0','0','0','1','0'), ('493','76','0','0','0','1','0'), ('494','76','0','0','0','1','0'), ('495','76','0','0','0','1','0'), ('496','75','0','1','0','0','0'), ('497','75','0','1','0','0','0'), ('498','75','0','1','0','0','0'), ('499','75','0','1','0','0','0'), ('500','75','0','1','0','0','0');
INSERT INTO `ol_ranklist` VALUES ('501','75','0','0','1','0','0'), ('502','75','0','0','1','0','0'), ('503','75','0','0','1','0','0'), ('504','75','0','0','1','0','0'), ('505','75','0','0','1','0','0'), ('506','75','0','0','0','1','0'), ('507','75','0','0','0','1','0'), ('508','75','0','0','0','1','0'), ('509','75','0','0','0','1','0'), ('510','75','0','0','0','1','0'), ('511','74','0','1','0','0','0'), ('512','74','0','1','0','0','0'), ('513','74','0','1','0','0','0'), ('514','74','0','1','0','0','0'), ('515','74','0','1','0','0','0'), ('516','74','0','0','1','0','0'), ('517','74','0','0','1','0','0'), ('518','74','0','0','1','0','0'), ('519','74','0','0','1','0','0'), ('520','74','0','0','1','0','0'), ('521','74','0','0','0','1','0'), ('522','74','0','0','0','1','0'), ('523','74','0','0','0','1','0'), ('524','74','0','0','0','1','0'), ('525','74','0','0','0','1','0'), ('526','73','0','1','0','0','0'), ('527','73','0','1','0','0','0'), ('528','73','0','1','0','0','0'), ('529','73','0','1','0','0','0'), ('530','73','0','1','0','0','0'), ('531','73','0','0','1','0','0'), ('532','73','0','0','1','0','0'), ('533','73','0','0','1','0','0'), ('534','73','0','0','1','0','0'), ('535','73','0','0','1','0','0'), ('536','73','0','0','0','1','0'), ('537','73','0','0','0','1','0'), ('538','73','0','0','0','1','0'), ('539','73','0','0','0','1','0'), ('540','73','0','0','0','1','0');
INSERT INTO `ol_save_ginfo` VALUES ('1','a:3:{i:0;a:14:{s:3:\"jn1\";i:0;s:9:\"jn1_level\";i:0;s:13:\"general_level\";i:1;s:12:\"professional\";s:1:\"4\";s:3:\"sex\";s:1:\"1\";s:12:\"general_name\";s:6:\"关胜\";s:6:\"avatar\";s:5:\"YX007\";s:19:\"understanding_value\";i:18;s:12:\"attack_value\";d:30;s:13:\"defense_value\";d:15;s:14:\"physical_value\";d:30;s:13:\"agility_value\";d:25;s:2:\"mj\";i:1;s:4:\"hftq\";d:1800;}i:1;a:14:{s:3:\"jn1\";i:0;s:9:\"jn1_level\";i:0;s:13:\"general_level\";i:1;s:12:\"professional\";i:5;s:3:\"sex\";i:1;s:12:\"general_name\";s:9:\"解子龙\";s:6:\"avatar\";s:5:\"BJ002\";s:19:\"understanding_value\";i:12;s:12:\"attack_value\";d:33;s:13:\"defense_value\";d:19;s:14:\"physical_value\";d:19;s:13:\"agility_value\";d:23;s:2:\"mj\";i:0;s:4:\"hftq\";d:600;}i:2;a:14:{s:3:\"jn1\";i:0;s:9:\"jn1_level\";i:0;s:13:\"general_level\";i:1;s:12:\"professional\";i:3;s:3:\"sex\";i:1;s:12:\"general_name\";s:9:\"吕伯毅\";s:6:\"avatar\";s:5:\"BJ002\";s:19:\"understanding_value\";i:15;s:12:\"attack_value\";d:24;s:13:\"defense_value\";d:15;s:14:\"physical_value\";d:24;s:13:\"agility_value\";d:34;s:2:\"mj\";i:0;s:4:\"hftq\";d:800;}}','1382751978');
INSERT INTO `ol_seatingrank` VALUES ('1','108','1','段景住','24','0','4','193','110','351','165','10','0','1','YX010'), ('2','108','2','长枪兵','24','0','2','193','138','351','138','10','0','1','YX020'), ('3','108','3','轻骑兵','24','0','4','193','110','351','165','10','0','1','YX044'), ('4','108','4','长枪兵','24','0','2','193','138','351','138','10','0','1','YX015'), ('5','107','1','时迁','26','0','2','225','161','409','161','10','0','1','YX036'), ('6','107','2','弓箭兵','26','0','3','193','129','351','257','10','0','1','YX026'), ('7','107','3','轻骑兵','26','0','4','225','129','409','193','10','0','1','YX031'), ('8','107','4','轻骑兵','26','0','4','225','129','409','193','10','0','1','YX037'), ('9','106','1','白胜','28','0','3','221','148','401','294','10','0','1','YX033'), ('10','106','2','长枪兵','28','0','2','257','184','467','184','10','0','1','YX027'), ('11','106','3','长枪兵','28','0','2','257','184','467','184','10','0','1','YX007'), ('12','106','4','轻骑兵','28','0','4','257','148','467','221','10','0','1','YX015'), ('13','105','1','郁保四','30','0','1','248','248','525','207','10','0','1','YX013'), ('14','105','2','弓箭兵','30','0','3','248','166','451','330','10','0','1','YX010'), ('15','105','3','弓箭兵','30','0','3','248','166','451','330','10','0','1','YX006'), ('16','105','4','弓箭兵','30','0','3','248','166','451','330','10','0','1','YX003'), ('17','104','1','王定六','32','0','4','321','185','583','276','10','0','1','YX029'), ('18','104','2','轻骑兵','32','0','4','321','185','583','276','10','0','1','YX026'), ('19','104','3','轻骑兵','32','0','4','321','185','583','276','10','0','1','YX032'), ('20','104','4','轻骑兵','32','0','4','321','185','583','276','10','0','1','YX006'), ('21','103','1','孙二娘','34','0','3','331','223','601','438','10','0','1','YX030'), ('22','103','2','重甲兵','34','0','1','331','331','699','277','10','0','1','YX003'), ('23','103','3','长枪兵','34','0','2','385','277','699','277','10','0','1','YX042'), ('24','103','4','轻骑兵','34','0','4','385','223','699','331','10','0','1','YX009'), ('25','103','5','轻骑兵','34','0','4','385','223','699','331','10','0','1','YX036'), ('26','102','1','张青','36','0','3','386','261','701','511','10','0','1','YX005'), ('27','102','2','轻骑兵','36','0','4','448','261','815','386','10','0','1','YX001'), ('28','102','3','弓箭兵','36','0','3','386','261','701','511','10','0','1','YX013'), ('29','102','4','重甲兵','36','0','1','386','386','815','324','10','0','1','YX035'), ('30','102','5','轻骑兵','36','0','4','448','261','815','386','10','0','1','YX030'), ('31','101','1','顾大嫂','38','0','5','583','370','673','441','10','0','1','YX014'), ('32','101','2','轻骑兵','38','0','4','512','299','930','441','10','0','1','YX037'), ('33','101','3','轻骑兵','38','0','4','512','299','930','441','10','0','1','YX017'), ('34','101','4','弓箭兵','38','0','3','441','299','802','583','10','0','1','YX010'), ('35','101','5','轻骑兵','38','0','4','512','299','930','441','10','0','1','YX004'), ('36','100','1','孙新','40','0','2','575','417','1046','417','10','0','1','BJ003'), ('37','100','2','弓箭兵','40','0','3','496','338','902','655','10','0','1','YX023'), ('38','100','3','弓箭兵','40','0','3','496','338','902','655','10','0','1','YX040'), ('39','100','4','轻骑兵','40','0','4','575','338','1046','496','10','0','1','YX029'), ('40','100','5','轻骑兵','40','0','4','575','338','1046','496','10','0','1','YX005'), ('41','99','1','石勇','42','0','1','551','551','1161','464','10','0','1','BJ001'), ('42','99','2','轻骑兵','42','0','4','639','376','1161','551','10','0','1','YX036'), ('43','99','3','轻骑兵','42','0','4','639','376','1161','551','10','0','1','YX008'), ('44','99','4','轻骑兵','42','0','4','639','376','1161','551','10','0','1','YX036'), ('45','99','5','轻骑兵','42','0','4','639','376','1161','551','10','0','1','YX042'), ('46','98','1','焦挺','44','0','1','607','607','1276','511','10','0','1','YX005'), ('47','98','2','长枪兵','44','0','2','702','511','1276','511','10','0','1','YX019'), ('48','98','3','轻骑兵','44','0','4','702','415','1276','607','10','0','1','YX020'), ('49','98','4','重甲兵','44','0','1','607','607','1276','511','10','0','1','YX006'), ('50','98','5','轻骑兵','44','0','4','702','415','1276','607','10','0','1','YX028'), ('51','97','1','李云','46','0','2','766','558','1392','558','10','0','1','YX041'), ('52','97','2','长枪兵','46','0','2','766','558','1392','558','10','0','1','YX025'), ('53','97','3','重甲兵','46','0','1','662','662','1392','558','10','0','1','YX023'), ('54','97','4','重甲兵','46','0','1','662','662','1392','558','10','0','1','YX032'), ('55','97','5','轻骑兵','46','0','4','766','455','1392','662','10','0','1','YX014'), ('56','96','1','李立','48','0','2','829','606','1506','606','10','0','1','YX019'), ('57','96','2','长枪兵','48','0','2','829','606','1506','606','10','0','1','YX033'), ('58','96','3','长枪兵','48','0','2','829','606','1506','606','10','0','1','YX016'), ('59','96','4','轻骑兵','48','0','4','829','494','1506','717','10','0','1','YX015'), ('60','96','5','弓箭兵','48','0','3','717','494','1303','940','10','0','1','YX021'), ('61','95','1','蔡庆','50','0','1','800','800','1679','676','10','0','1','YX038'), ('62','95','2','长枪兵','50','0','2','924','676','1679','676','10','0','1','YX009'), ('63','95','3','轻骑兵','50','0','4','924','553','1679','800','10','0','1','YX042'), ('64','95','4','重甲兵','50','0','1','800','800','1679','676','10','0','1','YX002'), ('65','95','5','轻骑兵','50','0','4','924','553','1679','800','10','0','1','YX039'), ('66','94','1','蔡福','52','0','1','910','910','1909','771','10','0','1','YX039'), ('67','94','2','弓箭兵','52','0','3','910','631','1655','1190','10','0','1','YX014'), ('68','94','3','重甲兵','52','0','1','910','910','1909','771','10','0','1','YX002'), ('69','94','4','轻骑兵','52','0','4','1050','631','1909','910','10','0','1','YX001'), ('70','94','5','长枪兵','52','0','2','1050','771','1909','771','10','0','1','YX029'), ('71','93','1','朱贵','54','0','2','1177','865','2139','865','10','0','1','YX016'), ('72','93','2','重甲兵','54','0','1','1021','1021','2139','865','10','0','1','YX011'), ('73','93','3','重甲兵','54','0','1','1021','1021','2139','865','10','0','1','YX001'), ('74','93','4','轻骑兵','54','0','4','1177','709','2139','1021','10','0','1','YX020'), ('75','93','5','弓箭兵','54','0','3','1021','709','1856','1333','10','0','1','YX027'), ('76','92','1','朱富','56','0','5','1475','960','1745','1131','10','0','1','BJ003'), ('77','92','2','轻骑兵','56','0','4','1303','788','2369','1131','10','0','1','YX018'), ('78','92','3','轻骑兵','56','0','4','1303','788','2369','1131','10','0','1','YX044'), ('79','92','4','长枪兵','56','0','2','1303','960','2369','960','10','0','1','YX039'), ('80','92','5','长枪兵','56','0','2','1303','960','2369','960','10','0','1','YX018'), ('81','91','1','邹渊','58','0','5','1616','1055','1918','1242','10','0','1','YX021'), ('82','91','2','弓箭兵','58','0','3','1242','868','2258','1616','10','0','1','YX015'), ('83','91','3','弓箭兵','58','0','3','1242','868','2258','1616','10','0','1','YX031'), ('84','91','4','轻骑兵','58','0','4','1429','868','2598','1242','10','0','1','YX019'), ('85','91','5','轻骑兵','58','0','4','1429','868','2598','1242','10','0','1','YX030'), ('86','90','1','邹润','60','0','1','1353','1353','2827','1150','10','0','1','YX023'), ('87','90','2','长枪兵','60','0','2','1555','1150','2827','1150','10','0','1','YX012'), ('88','90','3','重甲兵','60','0','1','1353','1353','2827','1150','10','0','1','YX008'), ('89','90','4','轻骑兵','60','0','4','1555','948','2827','1353','10','0','1','YX004'), ('90','90','5','弓箭兵','60','0','3','1353','948','2459','1757','10','0','1','YX024'), ('91','89','1','汤隆','62','0','4','1681','1028','3055','1463','10','0','1','YX005'), ('92','89','2','弓箭兵','62','0','3','1463','1028','2660','1898','10','0','1','YX024'), ('93','89','3','轻骑兵','62','0','4','1681','1028','3055','1463','10','0','1','YX008'), ('94','89','4','弓箭兵','62','0','3','1463','1028','2660','1898','10','0','1','YX017'), ('95','89','5','轻骑兵','62','0','4','1681','1028','3055','1463','10','0','1','YX041'), ('96','88','1','杜兴','64','0','1','1602','1602','3341','1365','10','0','1','YX028'), ('97','88','2','重甲兵','64','0','1','1602','1602','3341','1365','10','0','1','YX014'), ('98','88','3','重甲兵','64','0','1','1602','1602','3341','1365','10','0','1','YX024'), ('99','88','4','弓箭兵','64','0','3','1602','1129','2912','2074','10','0','1','YX008'), ('100','88','5','弓箭兵','64','0','3','1602','1129','2912','2074','10','0','1','YX011');
INSERT INTO `ol_seatingrank` VALUES ('101','87','1','周通','66','0','2','1963','1461','3569','1461','10','0','1','YX029'), ('102','87','2','轻骑兵','66','0','4','1963','1210','3569','1712','10','0','1','YX023'), ('103','87','3','重甲兵','66','0','1','1712','1712','3569','1461','10','0','1','YX010'), ('104','87','4','轻骑兵','66','0','4','1963','1210','3569','1712','10','0','1','YX044'), ('105','87','5','轻骑兵','66','0','4','1963','1210','3569','1712','10','0','1','YX012'), ('106','86','1','李忠','68','0','2','2120','1581','3854','1581','10','0','1','YX008'), ('107','86','2','弓箭兵','68','0','3','1851','1312','3365','2390','10','0','1','YX043'), ('108','86','3','轻骑兵','68','0','4','2120','1312','3854','1851','10','0','1','YX039'), ('109','86','4','轻骑兵','68','0','4','2120','1312','3854','1851','10','0','1','YX041'), ('110','86','5','弓箭兵','68','0','3','1851','1312','3365','2390','10','0','1','YX035'), ('111','85','1','施恩','70','0','2','2277','1702','4139','1702','10','0','1','YX012'), ('112','85','2','长枪兵','70','0','2','2277','1702','4139','1702','10','0','1','YX032'), ('113','85','3','重甲兵','70','0','1','1989','1989','4139','1702','10','0','1','YX031'), ('114','85','4','弓箭兵','70','0','3','1989','1414','3616','2564','10','0','1','YX024'), ('115','85','5','轻骑兵','70','0','4','2277','1414','4139','1989','10','0','1','YX038'), ('116','84','1','薛永','70','0','2','2465','1846','4481','1846','10','0','1','YX005'), ('117','84','2','弓箭兵','70','0','3','2155','1537','3918','2774','10','0','1','YX003'), ('118','84','3','长枪兵','70','0','2','2465','1846','4481','1846','10','0','1','YX020'), ('119','84','4','轻骑兵','70','0','4','2465','1537','4481','2155','10','0','1','YX007'), ('120','84','5','轻骑兵','70','0','4','2465','1537','4481','2155','10','0','1','YX016'), ('121','83','1','杜迁','70','0','3','2321','1660','4220','2983','10','0','1','YX031'), ('122','83','2','轻骑兵','70','0','4','2652','1660','4822','2321','10','0','1','YX027'), ('123','83','3','重甲兵','70','0','1','2321','2321','4822','1991','10','0','1','YX024'), ('124','83','4','轻骑兵','70','0','4','2652','1660','4822','2321','10','0','1','YX021'), ('125','83','5','重甲兵','70','0','1','2321','2321','4822','1991','10','0','1','YX007'), ('126','82','1','宋万','70','0','1','2488','2488','5163','2136','10','0','1','YX035'), ('127','82','2','弓箭兵','70','0','3','2488','1784','4523','3192','10','0','1','YX023'), ('128','82','3','轻骑兵','70','0','4','2840','1784','5163','2488','10','0','1','YX030'), ('129','82','4','重甲兵','70','0','1','2488','2488','5163','2136','10','0','1','YX011'), ('130','82','5','长枪兵','70','0','2','2840','2136','5163','2136','10','0','1','YX016'), ('131','81','1','曹正','70','0','1','2654','2654','5503','2281','10','0','1','YX023'), ('132','81','2','轻骑兵','70','0','4','3027','1908','5503','2654','10','0','1','YX032'), ('133','81','3','长枪兵','70','0','2','3027','2281','5503','2281','10','0','1','YX012'), ('134','81','4','轻骑兵','70','0','4','3027','1908','5503','2654','10','0','1','YX011'), ('135','81','5','轻骑兵','70','0','4','3027','1908','5503','2654','10','0','1','YX043'), ('136','80','1','穆春','70','0','1','2820','2820','5843','2427','10','0','1','YX021'), ('137','80','2','长枪兵','70','0','2','3214','2427','5843','2427','10','0','1','YX031'), ('138','80','3','轻骑兵','70','0','4','3214','2034','5843','2820','10','0','1','YX011'), ('139','80','4','重甲兵','70','0','1','2820','2820','5843','2427','10','0','1','YX034'), ('140','80','5','轻骑兵','70','0','4','3214','2034','5843','2820','10','0','1','YX017'), ('141','79','1','丁得孙','70','0','2','3401','2573','6182','2573','10','0','1','YX012'), ('142','79','2','弓箭兵','70','0','3','2987','2160','5430','3814','10','0','1','YX016'), ('143','79','3','重甲兵','70','0','1','2987','2987','6182','2573','10','0','1','YX021'), ('144','79','4','轻骑兵','70','0','4','3401','2160','6182','2987','10','0','1','YX007'), ('145','79','5','长枪兵','70','0','2','3401','2573','6182','2573','10','0','1','YX044'), ('146','78','1','龚旺','70','0','2','3587','2720','6521','2720','10','0','1','YX019'), ('147','78','2','重甲兵','70','0','1','3153','3153','6521','2720','10','0','1','YX038'), ('148','78','3','重甲兵','70','0','1','3153','3153','6521','2720','10','0','1','YX041'), ('149','78','4','轻骑兵','70','0','4','3587','2286','6521','3153','10','0','1','YX014'), ('150','78','5','弓箭兵','70','0','3','3153','2286','5733','4020','10','0','1','YX033'), ('151','77','1','乐和','70','0','5','4226','2867','5212','3320','10','0','1','YX007'), ('152','77','2','轻骑兵','70','0','4','3773','2414','6860','3320','10','0','1','YX003'), ('153','77','3','轻骑兵','70','0','4','3773','2414','6860','3320','10','0','1','YX012'), ('154','77','4','长枪兵','70','0','2','3773','2867','6860','2867','10','0','1','YX018'), ('155','77','5','长枪兵','70','0','2','3773','2867','6860','2867','10','0','1','YX005'), ('156','76','1','宋清','70','0','3','3487','2542','6339','4431','10','0','1','YX025'), ('157','76','2','弓箭兵','70','0','3','3487','2542','6339','4431','10','0','1','YX042'), ('158','76','3','弓箭兵','70','0','3','3487','2542','6339','4431','10','0','1','YX022'), ('159','76','4','轻骑兵','70','0','4','3959','2542','7198','3487','10','0','1','YX017'), ('160','76','5','轻骑兵','70','0','4','3959','2542','7198','3487','10','0','1','YX029'), ('161','75','1','陶宗旺','70','0','2','4176','3186','7592','3186','10','0','1','YX008'), ('162','75','2','长枪兵','70','0','2','4176','3186','7592','3186','10','0','1','YX015'), ('163','75','3','重甲兵','70','0','1','3681','3681','7592','3186','10','0','1','YX007'), ('164','75','4','轻骑兵','70','0','4','4176','2691','7592','3681','10','0','1','YX014'), ('165','75','5','弓箭兵','70','0','3','3681','2691','6692','4671','10','0','1','YX021'), ('166','74','1','郑天寿','70','0','3','3875','2841','7046','4910','10','0','1','YX006'), ('167','74','2','弓箭兵','70','0','3','3875','2841','7046','4910','10','0','1','YX019'), ('168','74','3','轻骑兵','70','0','4','4393','2841','7986','3875','10','0','1','YX038'), ('169','74','4','弓箭兵','70','0','3','3875','2841','7046','4910','10','0','1','YX002'), ('170','74','5','轻骑兵','70','0','4','4393','2841','7986','3875','10','0','1','YX011'), ('171','73','1','杨春','70','0','1','4153','4153','8550','3603','10','0','1','YX022'), ('172','73','2','连弩兵','70','0','5','5253','3603','6550','4153','10','0','1','YX036'), ('173','73','3','连弩兵','70','0','5','5253','3603','6550','4153','10','0','1','YX039'), ('174','73','4','连弩兵','70','0','5','5253','3603','6550','4153','10','0','1','YX040'), ('175','73','5','连弩兵','70','0','5','5253','3603','6550','4153','10','0','1','YX028');
INSERT INTO `ol_shop_sortrule` VALUES ('1','0');
INSERT INTO `ol_social` VALUES ('1','1','1','0','0','0','0','0','0','0','0','','0','','0','0');
INSERT INTO `ol_stage` VALUES ('1','1','1','1','40111','店小二','0','1','43','25','','0','','阳谷县','小二，你家的酒怎么一点酒味都没有？你是不是忘记往水里掺酒了？','鱼香肉丝里面有鱼吗？老婆饼里面有老婆吗？一看你小子就不是好人，想吃霸王餐，没门！','','','2'), ('2','1','1','2','40121','药贩子','0','2','43','25','','0','','阳谷县','幸好我拼命地护住了脸，我英俊的相貌才得以保存……哎，谁家的大花猫长得这么肥？','嗷！！！','','','2'), ('3','1','1','3','40131','潘金莲','1','2','43','21','','0','','阳谷县','潘小姐你好，请问你和西门庆是男女朋友吗？','西门大官人是搞房地产的大老板，你这小娱记可不配提他的名字哟，有事跟我的经纪人王婆说吧。','','','2'), ('4','1','1','4','40141','西门庆','0','3','129','94','斥候长靴图纸','13201','','阳谷县','西门庆你竟敢霸占本县第一美女潘金莲，今天我要替我的拜把兄弟武大讨个公道！','我看你们谁敢动我，我爸是西门刚！','令尊是西门吹雪也没用！兄弟们辛苦了，好好吃一顿吧！','老大，我们要吃肉！','2'), ('5','1','2','1','40211','伙计','0','3','72','32','','0','','快活林','你就是镇关西？馊了的肉还拿出来卖，怎么做生意的？','货物出门概不退换！我老大可是蒋门神，不想死就滚远点！','','','2'), ('6','1','2','2','40221','镇关西','0','4','72','26','','0','','快活林','此等奸商居然还开了连锁店？把你们的营业执照拿出来看看！','你砸了我们总店那笔帐还没算呢，今天让你竖着进来横着出去！','','','2'), ('7','1','2','3','40231','打手','0','4','72','32','','0','','快活林','蒋忠在哪里？我怀疑他跟几起食物中毒事件有关，要带他回去协助调查！','蒋总在睡午觉，你改天再来吧。','','','2'), ('8','1','2','4','40241','蒋门神','0','5','214','198','斥候斗笠图纸','11201','','快活林','一个小小的地头蛇居然这么猖狂，不信我扳不倒你！','哥上面有人，说出来吓死你。','听线人说在背后给蒋门神撑腰的是张都监，我们这就去鸳鸯楼闯一闯！','鸳鸯楼是很有名的会所啊，听说美女如云……','2'), ('9','1','3','1','40311','大厨','0','5','100','128','','0','','鸳鸯楼','我掌握了你们勾结黑道危害食品安全的证据，跟我走一趟吧。','放轻松，放轻松，先办张会员卡吧，优惠价888888，一年有效期。','','','2'), ('10','1','3','2','40321','管事','0','6','100','128','','0','','鸳鸯楼','你们都是张都监派来的吧？我一定要拿下他！','山珍美味现点现杀！穿山甲吃不吃？娃娃鱼吃不吃？','','','2'), ('11','1','3','3','40331','张团练','0','7','100','152','','0','','鸳鸯楼','又来一波？这私人会所里养了不少人啊！','总统套房，King size大床，无敌山景，外籍管家，只要9999一晚。','','','2'), ('12','1','3','4','40341','张都监','0','7','300','628','斥候锁甲图纸','12201','','鸳鸯楼','总算见到你了。你所辖的区域，很多商贩有严重的食品卫生问题，伏法吧！','不干不净，吃了没病。这可是文化人聚集的地方，休得撒野！','解决了张都监，查封了鸳鸯楼，心情真畅快！兄弟们，咱们去清风寨体验一下农家乐！','公费旅游！哦也！！！','2'), ('13','1','4','1','40411','刘老汉','0','8','129','114','','0','','清风寨','这位老大爷怎么走着走着自己就倒了？还往我车轮下滚，幸好我及时刹车。','哎哟哎哟，你撞到老夫了，老夫不能走路了！','','','2'), ('14','1','4','2','40421','刘夫人','0','8','129','114','','0','','清风寨','原来是传说中的碰瓷啊，想做个好人也不容易。我是阳谷县令，尔等不得放肆！','少废话，赶紧赔偿医药费、精神损失费、青春损失费、服装道具费，不赔别想走！','','','2'), ('15','1','4','3','40431','镇三山','1','9','129','122','','0','','清风寨','娘子军来了？好男不和女斗，让路！','哎哟喂，男人打女人啦！快来看哪，县令欺负百姓啦！','','','2'), ('16','1','4','4','40441','刘高','0','9','386','600','斥候朴刀图纸','14201','','清风寨','刘知寨啊，此地民风也太彪悍了，你这管理工作有难度吧？','这位老夫子是我七舅姥爷！你一定是酒驾！要么赔钱！要么蹲半年！','这清风寨可真够乱的，影响兄弟们的好兴致。谁会看地图，下一站是哪里？','老大，前面就是江州的地盘了。','2'), ('17','1','5','1','40511','士兵','0','10','158','222','','0','','江州城','哎？这地方怎么还有公路收费站呢？这又不是高速公路！','少废话！交钱走人！','','','2'), ('18','1','5','2','40521','黄通判','0','10','158','222','','0','','江州城','又是收费站？我当车匪路霸的时候你们还在穿开裆裤呢！','好汉不提当年勇！赶紧交钱，要不别想过！','','','2'), ('19','1','5','3','40531','监斩官','0','11','158','234','','0','','江州城','怎么又一个收费站，还有完没完了？这明明是私设站点乱收费！','这几个收费站都是当今太师蔡京的儿子蔡九爷设的，正宗富贵二代，怕了吧？','','','2'), ('20','1','5','4','40541','蔡九','0','11','472','771','都头长靴图纸','13202','','江州城','蔡九，你不光私设收费站，还随意提高加油站的油价！今天我就替你爹管教管教你！','既然知道我爸爸是谁，那就赶紧掏钱。花钱买平安，划算。','蔡京的儿子又怎么样，三两下就搞定了。兄弟们，咱们去前面村庄买点猪肉，犒赏三军！','跟着大哥混，有肉吃！','2'), ('21','1','6','1','40611','李应','0','12','187','801','','0','','祝家庄','你这猪肉是翻着倍涨价的吧？这不抢钱么？','爱买不买，猪饲料先涨价，我不涨价就得亏本。','','','2'), ('22','1','6','2','40621','扈三娘','1','13','187','843','','0','','祝家庄','你这里的猪饲料怎么这么贵？现在猪肉价格一涨，老百姓都吃不起肉了！','我也没办法，饲料原材料涨价了，我不跟着涨就没法开店了！','','','2'), ('23','1','6','3','40631','祝家三虎','0','13','187','843','','0','','祝家庄','你为什么要哄抬饲料原材料的价格？你知不知道这直接导致了猪肉价格上涨？','不关我事啊，祝家庄的祝老庄主是这些原材料的唯一指定总经销商，我们都是按他规定的价格进货的。','','','2'), ('24','1','6','4','40641','祝庄主','0','14','559','1362','都头发带图纸','11202','','祝家庄','你身为饲料原材料的总经销商，随意调整价格，扰乱物价水平，你当我这县令是摆设？','我是唯一指定总经销商，解释权归我所有。','杯具啊，连猪肉涨价也要我出手，还是以前当山大王时逍遥自在，要是有个压寨夫人该多好！','老大，大名府最近在举办鹊桥相亲会，凭你的人品武艺，定能冠压群雄！','2'), ('25','1','7','1','40711','秀才','0','14','216','923','','0','','大名府','在下阳谷县令，报名参加大名府鹊桥会！','知府提督尚且在排队，区区县令算什么？','','','2'), ('26','1','7','2','40721','衙役','0','15','216','1017','','0','','大名府','请光头评委给我个机会！','为什么我这么帅却要掉头发，你那么丑却不掉头发？我给你机会，谁给我机会？','','','2'), ('27','1','7','3','40731','王太守','1','15','216','1017','','0','','大名府','又来一个光头……请再给我这大龄青年一个机会吧！','小兄弟不要急，我们来讨论一下你最喜欢的颜色先。','','','2'), ('28','1','7','4','40741','梁中书','1','16','646','1635','都头长袍图纸','12202','','大名府','美女，我对你一见钟情，让我照顾你一辈子吧！','你年薪有一亿美金吗？有独栋别墅吗？有私人飞机吗？有私人游轮吗？','现在的妹子太拜金了！罢了，我们化悲痛为力量，去汴梁城参加汴梁杯蹴鞠赛吧，说不定还能见到皇上！','老大英明！赢了的话还能拿到一大笔奖金呢！','2'), ('29','1','8','1','40811','报名官','0','16','245','1476','','0','','汴梁城','狭路相逢勇者胜，这第一场热身赛，哥计划完胜！','那得问问我老人家，我能把有的球给吹没了，也能把没的球给吹有了。','','','2'), ('30','1','8','2','40821','陆谦','0','17','245','1529','','0','','汴梁城','你就是大帝？是森林大帝吧！','天亮了，你看，我的护球像亨利吧？','','','2'), ('31','1','8','3','40831','高衙内','0','17','245','1529','','0','','汴梁城','久仰久仰，蹴鞠一霸说的就是您吧？','好说，来，让你见识一下我的锁喉神功！','','','2'), ('32','1','8','4','40841','高俅','0','18','733','2446','都头大刀图纸','14202','','汴梁城','一路高歌猛进打入决赛，眼看冠军在望，兄弟们拼了！','井底之蛙大言不惭，老夫这首长秘书可是踢蹴鞠踢出来的，岂会输给你们这帮黄口小儿乎？','这奸贼高俅的确如此难搞，好在我没有让各位观众失望！兄弟们，咱们此举也当载入史册啊！','太牛了，老大！高俅这奸臣竟然败于咱们手上，咱们也可名垂青史，万古流芳了！','2'), ('33','2','1','1','40911','店小二','0','19','274','1721','','0','','阳谷县','小二，你家的酒怎么一点酒味都没有？你是不是忘记往水里掺酒了？','鱼香肉丝里面有鱼吗？老婆饼里面有老婆吗？一看你小子就不是好人，想吃霸王餐，没门！','','','2'), ('34','2','1','2','40921','药贩子','0','19','274','1796','','0','','阳谷县','幸好我拼命地护住了脸，我英俊的相貌才得以保存……哎，谁家的大花猫长得这么肥？','嗷！！！','','','2'), ('35','2','1','3','40931','潘金莲','1','20','274','1852','','0','','阳谷县','潘小姐你好，请问你和西门庆是男女朋友吗？','西门大官人是搞房地产的大老板，你这小娱记可不配提他的名字哟，有事跟我的经纪人王婆说吧。','','','2'), ('36','2','1','4','40941','西门庆','0','20','821','2752','骠骑战靴图纸','13303','','阳谷县','西门庆你竟敢霸占本县第一美女潘金莲，今天我要替我的拜把兄弟武大讨个公道！','我看你们谁敢动我，我爸是西门刚！','令尊是西门吹雪也没用！兄弟们辛苦了，好好吃一顿吧！','老大，我们要吃肉！','2'), ('37','2','2','1','41011','伙计','0','21','303','1904','','0','','快活林','你就是镇关西？馊了的肉还拿出来卖，怎么做生意的？','货物出门概不退换！我老大可是蒋门神，不想死就滚远点！','','','2'), ('38','2','2','2','41021','镇关西','0','21','303','1825','','0','','快活林','此等奸商居然还开了连锁店？把你们的营业执照拿出来看看！','你砸了我们总店那笔帐还没算呢，今天让你竖着进来横着出去！','','','2'), ('39','2','2','3','41031','打手','0','22','303','1968','','0','','快活林','蒋忠在哪里？我怀疑他跟几起食物中毒事件有关，要带他回去协助调查！','蒋总在睡午觉，你改天再来吧。','','','2'), ('40','2','2','4','41041','蒋门神','0','22','908','2667','骠骑头盔图纸','11303','','快活林','一个小小的地头蛇居然这么猖狂，不信我扳不倒你！','哥上面有人，说出来吓死你。','听线人说在背后给蒋门神撑腰的是张都监，我们这就去鸳鸯楼闯一闯！','鸳鸯楼是很有名的会所啊，听说美女如云……','2'), ('41','2','3','1','41111','大厨','0','23','332','2588','','0','','鸳鸯楼','我掌握了你们勾结黑道危害食品安全的证据，跟我走一趟吧。','放轻松，放轻松，先办张会员卡吧，优惠价888888，一年有效期。','','','2'), ('42','2','3','2','41121','管事','0','23','332','2372','','0','','鸳鸯楼','你们都是张都监派来的吧？我一定要拿下他！','山珍美味现点现杀！穿山甲吃不吃？娃娃鱼吃不吃？','','','2'), ('43','2','3','3','41131','张团练','0','24','332','2335','','0','','鸳鸯楼','又来一波？这私人会所里养了不少人啊！','总统套房，King size大床，无敌山景，外籍管家，只要9999一晚。','','','2'), ('44','2','3','4','41141','张都监','0','25','996','4264','骠骑鳞胄图纸','12303','','鸳鸯楼','总算见到你了。你所辖的区域，很多商贩有严重的食品卫生问题，伏法吧！','不干不净，吃了没病。这可是文化人聚集的地方，休得撒野！','解决了张都监，查封了鸳鸯楼，心情真畅快！兄弟们，咱们去清风寨体验一下农家乐！','公费旅游！哦也！！！','2'), ('45','2','4','1','41211','刘老汉','0','25','362','2680','','0','','清风寨','这位老大爷怎么走着走着自己就倒了？还往我车轮下滚，幸好我及时刹车。','哎哟哎哟，你撞到老夫了，老夫不能走路了！','','','2'), ('46','2','4','2','41221','刘夫人','0','26','362','2756','','0','','清风寨','原来是传说中的碰瓷啊，想做个好人也不容易。我是阳谷县令，尔等不得放肆！','少废话，赶紧赔偿医药费、精神损失费、青春损失费、服装道具费，不赔别想走！','','','2'), ('47','2','4','3','41231','镇三山','1','26','362','2882','','0','','清风寨','娘子军来了？好男不和女斗，让路！','哎哟喂，男人打女人啦！快来看哪，县令欺负百姓啦！','','','2'), ('48','2','4','4','41241','刘高','0','27','1084','4796','骠骑大刀图纸','14303','','清风寨','刘知寨啊，此地民风也太彪悍了，你这管理工作有难度吧？','这位老夫子是我七舅姥爷！你一定是酒驾！要么赔钱！要么蹲半年！','这清风寨可真够乱的，影响兄弟们的好兴致。谁会看地图，下一站是哪里？','老大，前面就是江州的地盘了。','2'), ('49','2','5','1','41311','士兵','0','27','391','4215','','0','','江州城','哎？这地方怎么还有公路收费站呢？这又不是高速公路！','少废话！交钱走人！','','','2'), ('50','2','5','2','41321','黄通判','0','28','391','4185','','0','','江州城','又是收费站？我当车匪路霸的时候你们还在穿开裆裤呢！','好汉不提当年勇！赶紧交钱，要不别想过！','','','2'), ('51','2','5','3','41331','监斩官','0','28','391','3895','','0','','江州城','怎么又一个收费站，还有完没完了？这明明是私设站点乱收费！','这几个收费站都是当今太师蔡京的儿子蔡九爷设的，正宗富贵二代，怕了吧？','','','2'), ('52','2','5','4','41341','蔡九','0','29','1173','6885','地煞战靴图纸','13304','','江州城','蔡九，你不光私设收费站，还随意提高加油站的油价！今天我就替你爹管教管教你！','既然知道我爸爸是谁，那就赶紧掏钱。花钱买平安，划算。','蔡京的儿子又怎么样，三两下就搞定了。兄弟们，咱们去前面村庄买点猪肉，犒赏三军！','跟着大哥混，有肉吃！','2'), ('53','2','6','1','41411','李应','0','29','421','4065','','0','','祝家庄','你这猪肉是翻着倍涨价的吧？这不抢钱么？','爱买不买，猪饲料先涨价，我不涨价就得亏本。','','','2'), ('54','2','6','2','41421','扈三娘','1','30','421','4321','','0','','祝家庄','你这里的猪饲料怎么这么贵？现在猪肉价格一涨，老百姓都吃不起肉了！','我也没办法，饲料原材料涨价了，我不跟着涨就没法开店了！','','','2'), ('55','2','6','3','41431','祝家三虎','0','31','421','4437','','0','','祝家庄','你为什么要哄抬饲料原材料的价格？你知不知道这直接导致了猪肉价格上涨？','不关我事啊，祝家庄的祝老庄主是这些原材料的唯一指定总经销商，我们都是按他规定的价格进货的。','','','2'), ('56','2','6','4','41441','祝庄主','0','31','1261','6636','地煞头盔图纸','11304','','祝家庄','你身为饲料原材料的总经销商，随意调整价格，扰乱物价水平，你当我这县令是摆设？','我是唯一指定总经销商，解释权归我所有。','杯具啊，连猪肉涨价也要我出手，还是以前当山大王时逍遥自在，要是有个压寨夫人该多好！','老大，大名府最近在举办鹊桥相亲会，凭你的人品武艺，定能冠压群雄！','2'), ('57','2','7','1','41511','秀才','0','32','450','4820','','0','','大名府','在下阳谷县令，报名参加大名府鹊桥会！','知府提督尚且在排队，区区县令算什么？','','','2'), ('58','2','7','2','41521','衙役','0','32','450','4659','','0','','大名府','请光头评委给我个机会！','为什么我这么帅却要掉头发，你那么丑却不掉头发？我给你机会，谁给我机会？','','','2'), ('59','2','7','3','41531','王太守','1','33','450','4780','','0','','大名府','又来一个光头……请再给我这大龄青年一个机会吧！','小兄弟不要急，我们来讨论一下你最喜欢的颜色先。','','','2'), ('60','2','7','4','41541','梁中书','1','33','1350','6895','地煞甲胄图纸','12304','','大名府','美女，我对你一见钟情，让我照顾你一辈子吧！','你年薪有一亿美金吗？有独栋别墅吗？有私人飞机吗？有私人游轮吗？','现在的妹子太拜金了！罢了，我们化悲痛为力量，去汴梁城参加汴梁杯蹴鞠赛吧，说不定还能见到皇上！','老大英明！赢了的话还能拿到一大笔奖金呢！','2'), ('61','2','8','1','41611','报名官','0','34','480','5322','','0','','汴梁城','狭路相逢勇者胜，这第一场热身赛，哥计划完胜！','那得问问我老人家，我能把有的球给吹没了，也能把没的球给吹有了。','','','2'), ('62','2','8','2','41621','陆谦','0','34','480','5505','','0','','汴梁城','你就是大帝？是森林大帝吧！','天亮了，你看，我的护球像亨利吧？','','','2'), ('63','2','8','3','41631','高衙内','0','35','480','5635','','0','','汴梁城','久仰久仰，蹴鞠一霸说的就是您吧？','好说，来，让你见识一下我的锁喉神功！','','','2'), ('64','2','8','4','41641','高俅','0','35','1439','7571','地煞钢刀图纸','14304','','汴梁城','一路高歌猛进打入决赛，眼看冠军在望，兄弟们拼了！','井底之蛙大言不惭，老夫这首长秘书可是踢蹴鞠踢出来的，岂会输给你们这帮黄口小儿乎？','这奸贼高俅的确如此难搞，好在我没有让各位观众失望！兄弟们，咱们此举也当载入史册啊！','太牛了，老大！高俅这奸臣竟然败于咱们手上，咱们也可名垂青史，万古流芳了！','2'), ('65','3','1','1','41711','店小二','0','36','510','6565','','0','','阳谷县','小二，你家的酒怎么一点酒味都没有？你是不是忘记往水里掺酒了？','鱼香肉丝里面有鱼吗？老婆饼里面有老婆吗？一看你小子就不是好人，想吃霸王餐，没门！','','','2'), ('66','3','1','2','41721','药贩子','0','37','510','6950','','0','','阳谷县','幸好我拼命地护住了脸，我英俊的相貌才得以保存……哎，谁家的大花猫长得这么肥？','嗷！！！','','','2'), ('67','3','1','3','41731','潘金莲','1','37','510','6950','','0','','阳谷县','潘小姐你好，请问你和西门庆是男女朋友吗？','西门大官人是搞房地产的大老板，你这小娱记可不配提他的名字哟，有事跟我的经纪人王婆说吧。','','','2'), ('68','3','1','4','41741','西门庆','0','38','1528','11380','天罡战靴图纸','13405','','阳谷县','西门庆你竟敢霸占本县第一美女潘金莲，今天我要替我的拜把兄弟武大讨个公道！','我看你们谁敢动我，我爸是西门刚！','令尊是西门吹雪也没用！兄弟们辛苦了，好好吃一顿吧！','老大，我们要吃肉！','2'), ('69','3','2','1','41811','伙计','0','38','540','6961','','0','','快活林','你就是镇关西？馊了的肉还拿出来卖，怎么做生意的？','货物出门概不退换！我老大可是蒋门神，不想死就滚远点！','','','2'), ('70','3','2','2','41821','镇关西','0','39','540','7900','','0','','快活林','此等奸商居然还开了连锁店？把你们的营业执照拿出来看看！','你砸了我们总店那笔帐还没算呢，今天让你竖着进来横着出去！','','','2'), ('71','3','2','3','41831','打手','0','39','540','7637','','0','','快活林','蒋忠在哪里？我怀疑他跟几起食物中毒事件有关，要带他回去协助调查！','蒋总在睡午觉，你改天再来吧。','','','2'), ('72','3','2','4','41841','蒋门神','0','40','1618','12495','天罡头盔图纸','11405','','快活林','一个小小的地头蛇居然这么猖狂，不信我扳不倒你！','哥上面有人，说出来吓死你。','听线人说在背后给蒋门神撑腰的是张都监，我们这就去鸳鸯楼闯一闯！','鸳鸯楼是很有名的会所啊，听说美女如云……','2'), ('73','3','3','1','41911','大厨','0','40','569','7719','','0','','鸳鸯楼','我掌握了你们勾结黑道危害食品安全的证据，跟我走一趟吧。','放轻松，放轻松，先办张会员卡吧，优惠价888888，一年有效期。','','','2'), ('74','3','3','2','41921','管事','0','41','569','7883','','0','','鸳鸯楼','你们都是张都监派来的吧？我一定要拿下他！','山珍美味现点现杀！穿山甲吃不吃？娃娃鱼吃不吃？','','','2'), ('75','3','3','3','41931','张团练','0','42','569','8042','','0','','鸳鸯楼','又来一波？这私人会所里养了不少人啊！','总统套房，King size大床，无敌山景，外籍管家，只要9999一晚。','','','2'), ('76','3','3','4','41941','张都监','0','42','1707','12032','天罡铠甲图纸','12405','','鸳鸯楼','总算见到你了。你所辖的区域，很多商贩有严重的食品卫生问题，伏法吧！','不干不净，吃了没病。这可是文化人聚集的地方，休得撒野！','解决了张都监，查封了鸳鸯楼，心情真畅快！兄弟们，咱们去清风寨体验一下农家乐！','公费旅游！哦也！！！','2'), ('77','3','4','1','42011','刘老汉','0','43','599','8655','','0','','清风寨','这位老大爷怎么走着走着自己就倒了？还往我车轮下滚，幸好我及时刹车。','哎哟哎哟，你撞到老夫了，老夫不能走路了！','','','2'), ('78','3','4','2','42021','刘夫人','0','43','599','8366','','0','','清风寨','原来是传说中的碰瓷啊，想做个好人也不容易。我是阳谷县令，尔等不得放肆！','少废话，赶紧赔偿医药费、精神损失费、青春损失费、服装道具费，不赔别想走！','','','2'), ('79','3','4','3','42031','镇三山','0','44','599','8820','','0','','清风寨','娘子军来了？好男不和女斗，让路！','哎哟喂，男人打女人啦！快来看哪，县令欺负百姓啦！','','','2'), ('80','3','4','4','42041','刘高','0','44','1797','13219','天罡雪刀图纸','14405','','清风寨','刘知寨啊，此地民风也太彪悍了，你这管理工作有难度吧？','这位老夫子是我七舅姥爷！你一定是酒驾！要么赔钱！要么蹲半年！','这清风寨可真够乱的，影响兄弟们的好兴致。谁会看地图，下一站是哪里？','老大，前面就是江州的地盘了。','2'), ('81','3','5','1','42111','士兵','0','45','629','9560','','0','','江州城','哎？这地方怎么还有公路收费站呢？这又不是高速公路！','少废话！交钱走人！','','','2'), ('82','3','5','2','42121','黄通判','0','45','629','9560','','0','','江州城','又是收费站？我当车匪路霸的时候你们还在穿开裆裤呢！','好汉不提当年勇！赶紧交钱，要不别想过！','','','2'), ('83','3','5','3','42131','监斩官','0','46','629','9730','','0','','江州城','怎么又一个收费站，还有完没完了？这明明是私设站点乱收费！','这几个收费站都是当今太师蔡京的儿子蔡九爷设的，正宗富贵二代，怕了吧？','','','2'), ('84','3','5','4','42141','蔡九','0','46','1887','15605','乾坤靴图纸','13406','','江州城','蔡九，你不光私设收费站，还随意提高加油站的油价！今天我就替你爹管教管教你！','既然知道我爸爸是谁，那就赶紧掏钱。花钱买平安，划算。','蔡京的儿子又怎么样，三两下就搞定了。兄弟们，咱们去前面村庄买点猪肉，犒赏三军！','跟着大哥混，有肉吃！','2'), ('85','3','6','1','42211','李应','0','47','659','11139','','0','','祝家庄','你这猪肉是翻着倍涨价的吧？这不抢钱么？','爱买不买，猪饲料先涨价，我不涨价就得亏本。','','','2'), ('86','3','6','2','42221','扈三娘','1','48','659','11735','','0','','祝家庄','你这里的猪饲料怎么这么贵？现在猪肉价格一涨，老百姓都吃不起肉了！','我也没办法，饲料原材料涨价了，我不跟着涨就没法开店了！','','','2'), ('87','3','6','3','42231','祝家三虎','0','48','659','11330','','0','','祝家庄','你为什么要哄抬饲料原材料的价格？你知不知道这直接导致了猪肉价格上涨？','不关我事啊，祝家庄的祝老庄主是这些原材料的唯一指定总经销商，我们都是按他规定的价格进货的。','','','2'), ('88','3','6','4','42241','祝庄主','0','49','1977','17861','乾坤盔图纸','11406','','祝家庄','你身为饲料原材料的总经销商，随意调整价格，扰乱物价水平，你当我这县令是摆设？','我是唯一指定总经销商，解释权归我所有。','杯具啊，连猪肉涨价也要我出手，还是以前当山大王时逍遥自在，要是有个压寨夫人该多好！','老大，大名府最近在举办鹊桥相亲会，凭你的人品武艺，定能冠压群雄！','2'), ('89','3','7','1','42311','秀才','0','49','689','12270','','0','','大名府','在下阳谷县令，报名参加大名府鹊桥会！','知府提督尚且在排队，区区县令算什么？','','','2'), ('90','3','7','2','42321','衙役','0','50','689','11237','','0','','大名府','请光头评委给我个机会！','为什么我这么帅却要掉头发，你那么丑却不掉头发？我给你机会，谁给我机会？','','','2'), ('91','3','7','3','42331','王太守','1','50','689','12069','','0','','大名府','又来一个光头……请再给我这大龄青年一个机会吧！','小兄弟不要急，我们来讨论一下你最喜欢的颜色先。','','','2'), ('92','3','7','4','42341','梁中书','0','51','2067','19034','乾坤胄图纸','12406','','大名府','美女，我对你一见钟情，让我照顾你一辈子吧！','你年薪有一亿美金吗？有独栋别墅吗？有私人飞机吗？有私人游轮吗？','现在的妹子太拜金了！罢了，我们化悲痛为力量，去汴梁城参加汴梁杯蹴鞠赛吧，说不定还能见到皇上！','老大英明！赢了的话还能拿到一大笔奖金呢！','2'), ('93','3','8','1','42411','报名官','0','51','719','12508','','0','','汴梁城','狭路相逢勇者胜，这第一场热身赛，哥计划完胜！','那得问问我老人家，我能把有的球给吹没了，也能把没的球给吹有了。','','','2'), ('94','3','8','2','42421','陆谦','0','52','719','12717','','0','','汴梁城','你就是大帝？是森林大帝吧！','天亮了，你看，我的护球像亨利吧？','','','2'), ('95','3','8','3','42431','高衙内','0','52','719','13155','','0','','汴梁城','久仰久仰，蹴鞠一霸说的就是您吧？','好说，来，让你见识一下我的锁喉神功！','','','2'), ('96','3','8','4','42441','高俅','0','53','2157','16584','青龙刀图纸','14406','','汴梁城','一路高歌猛进打入决赛，眼看冠军在望，兄弟们拼了！','井底之蛙大言不惭，老夫这首长秘书可是踢蹴鞠踢出来的，岂会输给你们这帮黄口小儿乎？','这奸贼高俅的确如此难搞，好在我没有让各位观众失望！兄弟们，咱们此举也当载入史册啊！','太牛了，老大！高俅这奸臣竟然败于咱们手上，咱们也可名垂青史，万古流芳了！','2'), ('97','4','1','1','42511','店小二','0','54','750','13847','','0','','阳谷县','小二，你家的酒怎么一点酒味都没有？你是不是忘记往水里掺酒了？','鱼香肉丝里面有鱼吗？老婆饼里面有老婆吗？一看你小子就不是好人，想吃霸王餐，没门！','','','0'), ('98','4','1','2','42521','药贩子','0','54','750','14341','','0','','阳谷县','幸好我拼命地护住了脸，我英俊的相貌才得以保存……哎，谁家的大花猫长得这么肥？','嗷！！！','','','0'), ('99','4','1','3','42531','潘金莲','1','55','750','14567','','0','','阳谷县','潘小姐你好，请问你和西门庆是男女朋友吗？','西门大官人是搞房地产的大老板，你这小娱记可不配提他的名字哟，有事跟我的经纪人王婆说吧。','','','0'), ('100','4','1','4','42541','西门庆','0','55','2248','23355','乾坤靴图纸','13406','','阳谷县','西门庆你竟敢霸占本县第一美女潘金莲，今天我要替我的拜把兄弟武大讨个公道！','我看你们谁敢动我，我爸是西门刚！','令尊是西门吹雪也没用！兄弟们辛苦了，好好吃一顿吧！','老大，我们要吃肉！','0');
INSERT INTO `ol_stage` VALUES ('101','4','2','1','42611','伙计','0','56','780','14104','','0','','快活林','你就是镇关西？馊了的肉还拿出来卖，怎么做生意的？','货物出门概不退换！我老大可是蒋门神，不想死就滚远点！','','','0'), ('102','4','2','2','42621','镇关西','0','56','780','15670','','0','','快活林','此等奸商居然还开了连锁店？把你们的营业执照拿出来看看！','你砸了我们总店那笔帐还没算呢，今天让你竖着进来横着出去！','','','0'), ('103','4','2','3','42631','打手','0','57','780','15379','','0','','快活林','蒋忠在哪里？我怀疑他跟几起食物中毒事件有关，要带他回去协助调查！','蒋总在睡午觉，你改天再来吧。','','','0'), ('104','4','2','4','42641','蒋门神','0','57','2339','24655','乾坤盔图纸','11406','','快活林','一个小小的地头蛇居然这么猖狂，不信我扳不倒你！','哥上面有人，说出来吓死你。','听线人说在背后给蒋门神撑腰的是张都监，我们这就去鸳鸯楼闯一闯！','鸳鸯楼是很有名的会所啊，听说美女如云……','0'), ('105','4','3','1','42711','大厨','0','58','810','18270','','0','','鸳鸯楼','我掌握了你们勾结黑道危害食品安全的证据，跟我走一趟吧。','放轻松，放轻松，先办张会员卡吧，优惠价888888，一年有效期。','','','0'), ('106','4','3','2','42721','管事','0','58','810','18270','','0','','鸳鸯楼','你们都是张都监派来的吧？我一定要拿下他！','山珍美味现点现杀！穿山甲吃不吃？娃娃鱼吃不吃？','','','0'), ('107','4','3','3','42731','张团练','0','59','810','18531','','0','','鸳鸯楼','又来一波？这私人会所里养了不少人啊！','总统套房，King size大床，无敌山景，外籍管家，只要9999一晚。','','','0'), ('108','4','3','4','42741','张都监','0','60','2430','28125','乾坤胄图纸','12406','','鸳鸯楼','总算见到你了。你所辖的区域，很多商贩有严重的食品卫生问题，伏法吧！','不干不净，吃了没病。这可是文化人聚集的地方，休得撒野！','解决了张都监，查封了鸳鸯楼，心情真畅快！兄弟们，咱们去清风寨体验一下农家乐！','公费旅游！哦也！！！','0'), ('109','4','4','1','42811','刘老汉','0','60','841','18750','','0','','清风寨','这位老大爷怎么走着走着自己就倒了？还往我车轮下滚，幸好我及时刹车。','哎哟哎哟，你撞到老夫了，老夫不能走路了！','','','0'), ('110','4','4','2','42821','刘夫人','0','61','841','18391','','0','','清风寨','原来是传说中的碰瓷啊，想做个好人也不容易。我是阳谷县令，尔等不得放肆！','少废话，赶紧赔偿医药费、精神损失费、青春损失费、服装道具费，不赔别想走！','','','0'), ('111','4','4','3','42831','镇三山','0','61','841','19025','','0','','清风寨','娘子军来了？好男不和女斗，让路！','哎哟喂，男人打女人啦！快来看哪，县令欺负百姓啦！','','','0'), ('112','4','4','4','42841','刘高','0','62','2521','28913','青龙刀图纸','14406','','清风寨','刘知寨啊，此地民风也太彪悍了，你这管理工作有难度吧？','这位老夫子是我七舅姥爷！你一定是酒驾！要么赔钱！要么蹲半年！','这清风寨可真够乱的，影响兄弟们的好兴致。谁会看地图，下一站是哪里？','老大，前面就是江州的地盘了。','0'), ('113','4','5','1','42911','士兵','0','62','871','19048','','0','','江州城','哎？这地方怎么还有公路收费站呢？这又不是高速公路！','少废话！交钱走人！','','','0'), ('114','4','5','2','42921','黄通判','0','63','871','19319','','0','','江州城','又是收费站？我当车匪路霸的时候你们还在穿开裆裤呢！','好汉不提当年勇！赶紧交钱，要不别想过！','','','0'), ('115','4','5','3','42931','监斩官','0','63','871','19319','','0','','江州城','怎么又一个收费站，还有完没完了？这明明是私设站点乱收费！','这几个收费站都是当今太师蔡京的儿子蔡九爷设的，正宗富贵二代，怕了吧？','','','0'), ('116','4','5','4','42941','蔡九','0','64','2612','31410','风火靴图纸','13507','','江州城','蔡九，你不光私设收费站，还随意提高加油站的油价！今天我就替你爹管教管教你！','既然知道我爸爸是谁，那就赶紧掏钱。花钱买平安，划算。','蔡京的儿子又怎么样，三两下就搞定了。兄弟们，咱们去前面村庄买点猪肉，犒赏三军！','跟着大哥混，有肉吃！','0'), ('117','4','6','1','43011','李应','0','64','901','19768','','0','','祝家庄','你这猪肉是翻着倍涨价的吧？这不抢钱么？','爱买不买，猪饲料先涨价，我不涨价就得亏本。','','','0'), ('118','4','6','2','43021','扈三娘','1','65','901','20750','','0','','祝家庄','你这里的猪饲料怎么这么贵？现在猪肉价格一涨，老百姓都吃不起肉了！','我也没办法，饲料原材料涨价了，我不跟着涨就没法开店了！','','','0'), ('119','4','6','3','43031','祝家三虎','0','66','901','20295','','0','','祝家庄','你为什么要哄抬饲料原材料的价格？你知不知道这直接导致了猪肉价格上涨？','不关我事啊，祝家庄的祝老庄主是这些原材料的唯一指定总经销商，我们都是按他规定的价格进货的。','','','0'), ('120','4','6','4','43041','祝庄主','0','66','2703','31459','玄天盔图纸','11507','','祝家庄','你身为饲料原材料的总经销商，随意调整价格，扰乱物价水平，你当我这县令是摆设？','我是唯一指定总经销商，解释权归我所有。','杯具啊，连猪肉涨价也要我出手，还是以前当山大王时逍遥自在，要是有个压寨夫人该多好！','老大，大名府最近在举办鹊桥相亲会，凭你的人品武艺，定能冠压群雄！','0'), ('121','4','7','1','43111','秀才','0','67','932','23115','','0','','大名府','在下阳谷县令，报名参加大名府鹊桥会！','知府提督尚且在排队，区区县令算什么？','','','0'), ('122','4','7','2','43121','衙役','0','67','932','20805','','0','','大名府','请光头评委给我个机会！','为什么我这么帅却要掉头发，你那么丑却不掉头发？我给你机会，谁给我机会？','','','0'), ('123','4','7','3','43131','王太守','1','68','932','22620','','0','','大名府','又来一个光头……请再给我这大龄青年一个机会吧！','小兄弟不要急，我们来讨论一下你最喜欢的颜色先。','','','0'), ('124','4','7','4','43141','梁中书','0','68','2794','35061','日月铠图纸','12507','','大名府','美女，我对你一见钟情，让我照顾你一辈子吧！','你年薪有一亿美金吗？有独栋别墅吗？有私人飞机吗？有私人游轮吗？','现在的妹子太拜金了！罢了，我们化悲痛为力量，去汴梁城参加汴梁杯蹴鞠赛吧，说不定还能见到皇上！','老大英明！赢了的话还能拿到一大笔奖金呢！','0'), ('125','4','8','1','43211','报名官','0','69','962','23045','','0','','汴梁城','狭路相逢勇者胜，这第一场热身赛，哥计划完胜！','那得问问我老人家，我能把有的球给吹没了，也能把没的球给吹有了。','','','0'), ('126','4','8','2','43221','陆谦','0','69','962','23045','','0','','汴梁城','你就是大帝？是森林大帝吧！','天亮了，你看，我的护球像亨利吧？','','','0'), ('127','4','8','3','43231','高衙内','0','70','962','24130','','0','','汴梁城','久仰久仰，蹴鞠一霸说的就是您吧？','好说，来，让你见识一下我的锁喉神功！','','','0'), ('128','4','8','4','43241','高俅','0','70','2886','27428','逆天刃图纸','14507','','汴梁城','一路高歌猛进打入决赛，眼看冠军在望，兄弟们拼了！','井底之蛙大言不惭，老夫这首长秘书可是踢蹴鞠踢出来的，岂会输给你们这帮黄口小儿乎？','这奸贼高俅的确如此难搞，好在我没有让各位观众失望！兄弟们，咱们此举也当载入史册啊！','太牛了，老大！高俅这奸臣竟然败于咱们手上，咱们也可名垂青史，万古流芳了！','0'), ('129','5','1','1','43311','店小二','0','70','993','23736','','0','','阳谷县','小二，你家的酒怎么一点酒味都没有？你是不是忘记往水里掺酒了？','鱼香肉丝里面有鱼吗？老婆饼里面有老婆吗？一看你小子就不是好人，想吃霸王餐，没门！','','','0'), ('130','5','1','2','43321','药贩子','0','70','993','24583','','0','','阳谷县','幸好我拼命地护住了脸，我英俊的相貌才得以保存……哎，谁家的大花猫长得这么肥？','嗷！！！','','','0'), ('131','5','1','3','43331','潘金莲','1','70','993','26762','','0','','阳谷县','潘小姐你好，请问你和西门庆是男女朋友吗？','西门大官人是搞房地产的大老板，你这小娱记可不配提他的名字哟，有事跟我的经纪人王婆说吧。','','','0'), ('132','5','1','4','43341','西门庆','0','70','2978','47200','降龙木','18555','','阳谷县','西门庆你竟敢霸占本县第一美女潘金莲，今天我要替我的拜把兄弟武大讨个公道！','我看你们谁敢动我，我爸是西门刚！','令尊是西门吹雪也没用！兄弟们辛苦了，好好吃一顿吧！','老大，我们要吃肉！','0'), ('133','5','2','1','43411','伙计','0','70','1024','28539','','0','','快活林','你就是镇关西？馊了的肉还拿出来卖，怎么做生意的？','货物出门概不退换！我老大可是蒋门神，不想死就滚远点！','','','0'), ('134','5','2','2','43421','镇关西','0','70','1024','31710','','0','','快活林','此等奸商居然还开了连锁店？把你们的营业执照拿出来看看！','你砸了我们总店那笔帐还没算呢，今天让你竖着进来横着出去！','','','0'), ('135','5','2','3','43431','打手','0','70','1024','32292','','0','','快活林','蒋忠在哪里？我怀疑他跟几起食物中毒事件有关，要带他回去协助调查！','蒋总在睡午觉，你改天再来吧。','','','0'), ('136','5','2','4','43441','蒋门神','0','70','3070','62135','淬火石','18556','','快活林','一个小小的地头蛇居然这么猖狂，不信我扳不倒你！','哥上面有人，说出来吓死你。','听线人说在背后给蒋门神撑腰的是张都监，我们这就去鸳鸯楼闯一闯！','鸳鸯楼是很有名的会所啊，听说美女如云……','0'), ('137','5','3','1','43511','大厨','0','70','1054','34496','','0','','鸳鸯楼','我掌握了你们勾结黑道危害食品安全的证据，跟我走一趟吧。','放轻松，放轻松，先办张会员卡吧，优惠价888888，一年有效期。','','','0'), ('138','5','3','2','43521','管事','0','70','1054','34496','','0','','鸳鸯楼','你们都是张都监派来的吧？我一定要拿下他！','山珍美味现点现杀！穿山甲吃不吃？娃娃鱼吃不吃？','','','0'), ('139','5','3','3','43531','张团练','0','70','1054','36796','','0','','鸳鸯楼','又来一波？这私人会所里养了不少人啊！','总统套房，King size大床，无敌山景，外籍管家，只要9999一晚。','','','0'), ('140','5','3','4','43541','张都监','0','70','3162','71588','陨铁','18557','','鸳鸯楼','总算见到你了。你所辖的区域，很多商贩有严重的食品卫生问题，伏法吧！','不干不净，吃了没病。这可是文化人聚集的地方，休得撒野！','解决了张都监，查封了鸳鸯楼，心情真畅快！兄弟们，咱们去清风寨体验一下农家乐！','公费旅游！哦也！！！','0'), ('141','5','4','1','43611','刘老汉','0','70','1085','39225','','0','','清风寨','这位老大爷怎么走着走着自己就倒了？还往我车轮下滚，幸好我及时刹车。','哎哟哎哟，你撞到老夫了，老夫不能走路了！','','','0'), ('142','5','4','2','43621','刘夫人','0','70','1085','37918','','0','','清风寨','原来是传说中的碰瓷啊，想做个好人也不容易。我是阳谷县令，尔等不得放肆！','少废话，赶紧赔偿医药费、精神损失费、青春损失费、服装道具费，不赔别想走！','','','0'), ('143','5','4','3','43631','镇三山','0','70','1085','43130','','0','','清风寨','娘子军来了？好男不和女斗，让路！','哎哟喂，男人打女人啦！快来看哪，县令欺负百姓啦！','','','0'), ('144','5','4','4','43641','刘高','0','70','3254','90475','赤鱬鳞','18558','','清风寨','刘知寨啊，此地民风也太彪悍了，你这管理工作有难度吧？','这位老夫子是我七舅姥爷！你一定是酒驾！要么赔钱！要么蹲半年！','这清风寨可真够乱的，影响兄弟们的好兴致。谁会看地图，下一站是哪里？','老大，前面就是江州的地盘了。','0'), ('145','5','5','1','43711','士兵','0','70','1116','38401','','0','','江州城','哎？这地方怎么还有公路收费站呢？这又不是高速公路！','少废话！交钱走人！','','','0'), ('146','5','5','2','43721','黄通判','0','70','1116','38401','','0','','江州城','又是收费站？我当车匪路霸的时候你们还在穿开裆裤呢！','好汉不提当年勇！赶紧交钱，要不别想过！','','','0'), ('147','5','5','3','43731','监斩官','0','70','1116','43205','','0','','江州城','怎么又一个收费站，还有完没完了？这明明是私设站点乱收费！','这几个收费站都是当今太师蔡京的儿子蔡九爷设的，正宗富贵二代，怕了吧？','','','0'), ('148','5','5','4','43741','蔡九','0','70','3346','96985','降龙木','18555','','江州城','蔡九，你不光私设收费站，还随意提高加油站的油价！今天我就替你爹管教管教你！','既然知道我爸爸是谁，那就赶紧掏钱。花钱买平安，划算。','蔡京的儿子又怎么样，三两下就搞定了。兄弟们，咱们去前面村庄买点猪肉，犒赏三军！','跟着大哥混，有肉吃！','0'), ('149','5','6','1','43811','李应','0','70','1147','39732','','0','','祝家庄','你这猪肉是翻着倍涨价的吧？这不抢钱么？','爱买不买，猪饲料先涨价，我不涨价就得亏本。','','','0'), ('150','5','6','2','43821','扈三娘','1','70','1147','41151','','0','','祝家庄','你这里的猪饲料怎么这么贵？现在猪肉价格一涨，老百姓都吃不起肉了！','我也没办法，饲料原材料涨价了，我不跟着涨就没法开店了！','','','0'), ('151','5','6','3','43831','祝家三虎','0','70','1147','44217','','0','','祝家庄','你为什么要哄抬饲料原材料的价格？你知不知道这直接导致了猪肉价格上涨？','不关我事啊，祝家庄的祝老庄主是这些原材料的唯一指定总经销商，我们都是按他规定的价格进货的。','','','0'), ('152','5','6','4','43841','祝庄主','0','70','3439','95946','淬火石','18556','','祝家庄','你身为饲料原材料的总经销商，随意调整价格，扰乱物价水平，你当我这县令是摆设？','我是唯一指定总经销商，解释权归我所有。','杯具啊，连猪肉涨价也要我出手，还是以前当山大王时逍遥自在，要是有个压寨夫人该多好！','老大，大名府最近在举办鹊桥相亲会，凭你的人品武艺，定能冠压群雄！','0'), ('153','5','7','1','43911','秀才','0','70','1177','44595','','0','','大名府','在下阳谷县令，报名参加大名府鹊桥会！','知府提督尚且在排队，区区县令算什么？','','','0'), ('154','5','7','2','43921','衙役','0','70','1177','40134','','0','','大名府','请光头评委给我个机会！','为什么我这么帅却要掉头发，你那么丑却不掉头发？我给你机会，谁给我机会？','','','0'), ('155','5','7','3','43931','王太守','1','70','1177','48266','','0','','大名府','又来一个光头……请再给我这大龄青年一个机会吧！','小兄弟不要急，我们来讨论一下你最喜欢的颜色先。','','','0'), ('156','5','7','4','43941','梁中书','0','70','3531','104738','陨铁','18557','','大名府','美女，我对你一见钟情，让我照顾你一辈子吧！','你年薪有一亿美金吗？有独栋别墅吗？有私人飞机吗？有私人游轮吗？','现在的妹子太拜金了！罢了，我们化悲痛为力量，去汴梁城参加汴梁杯蹴鞠赛吧，说不定还能见到皇上！','老大英明！赢了的话还能拿到一大笔奖金呢！','0'), ('157','5','8','1','44011','报名官','0','70','1208','45946','','0','','汴梁城','狭路相逢勇者胜，这第一场热身赛，哥计划完胜！','那得问问我老人家，我能把有的球给吹没了，也能把没的球给吹有了。','','','0'), ('158','5','8','2','44021','陆谦','0','70','1208','45946','','0','','汴梁城','你就是大帝？是森林大帝吧！','天亮了，你看，我的护球像亨利吧？','','','0'), ('159','5','8','3','44031','高衙内','0','70','1208','53465','','0','','汴梁城','久仰久仰，蹴鞠一霸说的就是您吧？','好说，来，让你见识一下我的锁喉神功！','','','0'), ('160','5','8','4','44041','高俅','0','70','3624','85079','赤鱬鳞','18558','','汴梁城','一路高歌猛进打入决赛，眼看冠军在望，兄弟们拼了！','井底之蛙大言不惭，老夫这首长秘书可是踢蹴鞠踢出来的，岂会输给你们这帮黄口小儿乎？','这奸贼高俅的确如此难搞，好在我没有让各位观众失望！兄弟们，咱们此举也当载入史册啊！','太牛了，老大！高俅这奸臣竟然败于咱们手上，咱们也可名垂青史，万古流芳了！','0'), ('161','6','1','1','44111','店小二','0','70','1239','51413','','0','','阳谷县','小二，你家的酒怎么一点酒味都没有？你是不是忘记往水里掺酒了？','鱼香肉丝里面有鱼吗？老婆饼里面有老婆吗？一看你小子就不是好人，想吃霸王餐，没门！','','','0'), ('162','6','1','2','44121','药贩子','0','70','1239','53249','','0','','阳谷县','幸好我拼命地护住了脸，我英俊的相貌才得以保存……哎，谁家的大花猫长得这么肥？','嗷！！！','','','0'), ('163','6','1','3','44131','潘金莲','1','70','1239','54747','','0','','阳谷县','潘小姐你好，请问你和西门庆是男女朋友吗？','西门大官人是搞房地产的大老板，你这小娱记可不配提他的名字哟，有事跟我的经纪人王婆说吧。','','','0'), ('164','6','1','4','44141','西门庆','0','70','3717','122895','【风】妖卡碎片','20037','','阳谷县','西门庆你竟敢霸占本县第一美女潘金莲，今天我要替我的拜把兄弟武大讨个公道！','我看你们谁敢动我，我爸是西门刚！','令尊是西门吹雪也没用！兄弟们辛苦了，好好吃一顿吧！','老大，我们要吃肉！','0'), ('165','6','2','1','44211','伙计','0','70','1270','51467','','0','','快活林','你就是镇关西？馊了的肉还拿出来卖，怎么做生意的？','货物出门概不退换！我老大可是蒋门神，不想死就滚远点！','','','0'), ('166','6','2','2','44221','镇关西','0','70','1270','57185','','0','','快活林','此等奸商居然还开了连锁店？把你们的营业执照拿出来看看！','你砸了我们总店那笔帐还没算呢，今天让你竖着进来横着出去！','','','0'), ('167','6','2','3','44231','打手','0','70','1270','58048','','0','','快活林','蒋忠在哪里？我怀疑他跟几起食物中毒事件有关，要带他回去协助调查！','蒋总在睡午觉，你改天再来吧。','','','0'), ('168','6','2','4','44241','蒋门神','0','70','3810','130305','【风】妖卡碎片','20037','','快活林','一个小小的地头蛇居然这么猖狂，不信我扳不倒你！','哥上面有人，说出来吓死你。','听线人说在背后给蒋门神撑腰的是张都监，我们这就去鸳鸯楼闯一闯！','鸳鸯楼是很有名的会所啊，听说美女如云……','0'), ('169','6','3','1','44311','大厨','0','70','1301','57309','','0','','鸳鸯楼','我掌握了你们勾结黑道危害食品安全的证据，跟我走一趟吧。','放轻松，放轻松，先办张会员卡吧，优惠价888888，一年有效期。','','','0'), ('170','6','3','2','44321','管事','0','70','1301','57309','','0','','鸳鸯楼','你们都是张都监派来的吧？我一定要拿下他！','山珍美味现点现杀！穿山甲吃不吃？娃娃鱼吃不吃？','','','0'), ('171','6','3','3','44331','张团练','0','70','1301','60078','','0','','鸳鸯楼','又来一波？这私人会所里养了不少人啊！','总统套房，King size大床，无敌山景，外籍管家，只要9999一晚。','','','0'), ('172','6','3','4','44341','张都监','0','70','3903','125870','【林】妖卡碎片','20038','','鸳鸯楼','总算见到你了。你所辖的区域，很多商贩有严重的食品卫生问题，伏法吧！','不干不净，吃了没病。这可是文化人聚集的地方，休得撒野！','解决了张都监，查封了鸳鸯楼，心情真畅快！兄弟们，咱们去清风寨体验一下农家乐！','公费旅游！哦也！！！','0'), ('173','6','4','1','44411','刘老汉','0','70','1332','61385','','0','','清风寨','这位老大爷怎么走着走着自己就倒了？还往我车轮下滚，幸好我及时刹车。','哎哟哎哟，你撞到老夫了，老夫不能走路了！','','','0'), ('174','6','4','2','44421','刘夫人','0','70','1332','59339','','0','','清风寨','原来是传说中的碰瓷啊，想做个好人也不容易。我是阳谷县令，尔等不得放肆！','少废话，赶紧赔偿医药费、精神损失费、青春损失费、服装道具费，不赔别想走！','','','0'), ('175','6','4','3','44431','镇三山','0','70','1332','64250','','0','','清风寨','娘子军来了？好男不和女斗，让路！','哎哟喂，男人打女人啦！快来看哪，县令欺负百姓啦！','','','0'), ('176','6','4','4','44441','刘高','0','70','3996','134773','【林】妖卡碎片','20038','','清风寨','刘知寨啊，此地民风也太彪悍了，你这管理工作有难度吧？','这位老夫子是我七舅姥爷！你一定是酒驾！要么赔钱！要么蹲半年！','这清风寨可真够乱的，影响兄弟们的好兴致。谁会看地图，下一站是哪里？','老大，前面就是江州的地盘了。','0'), ('177','6','5','1','44511','士兵','0','70','1364','61877','','0','','江州城','哎？这地方怎么还有公路收费站呢？这又不是高速公路！','少废话！交钱走人！','','','0'), ('178','6','5','2','44521','黄通判','0','70','1364','61983','','0','','江州城','又是收费站？我当车匪路霸的时候你们还在穿开裆裤呢！','好汉不提当年勇！赶紧交钱，要不别想过！','','','0'), ('179','6','5','3','44531','监斩官','0','70','1364','64781','','0','','江州城','怎么又一个收费站，还有完没完了？这明明是私设站点乱收费！','这几个收费站都是当今太师蔡京的儿子蔡九爷设的，正宗富贵二代，怕了吧？','','','0'), ('180','6','5','4','44541','蔡九','0','70','4090','145415','【火】妖卡碎片','20039','','江州城','蔡九，你不光私设收费站，还随意提高加油站的油价！今天我就替你爹管教管教你！','既然知道我爸爸是谁，那就赶紧掏钱。花钱买平安，划算。','蔡京的儿子又怎么样，三两下就搞定了。兄弟们，咱们去前面村庄买点猪肉，犒赏三军！','跟着大哥混，有肉吃！','0'), ('181','6','6','1','44611','李应','0','70','1395','62437','','0','','祝家庄','你这猪肉是翻着倍涨价的吧？这不抢钱么？','爱买不买，猪饲料先涨价，我不涨价就得亏本。','','','0'), ('182','6','6','2','44621','扈三娘','1','70','1395','64666','','0','','祝家庄','你这里的猪饲料怎么这么贵？现在猪肉价格一涨，老百姓都吃不起肉了！','我也没办法，饲料原材料涨价了，我不跟着涨就没法开店了！','','','0'), ('183','6','6','3','44631','祝家三虎','0','70','1395','65166','','0','','祝家庄','你为什么要哄抬饲料原材料的价格？你知不知道这直接导致了猪肉价格上涨？','不关我事啊，祝家庄的祝老庄主是这些原材料的唯一指定总经销商，我们都是按他规定的价格进货的。','','','0'), ('184','6','6','4','44641','祝庄主','0','70','4183','141405','【火】妖卡碎片','20039','','祝家庄','你身为饲料原材料的总经销商，随意调整价格，扰乱物价水平，你当我这县令是摆设？','我是唯一指定总经销商，解释权归我所有。','杯具啊，连猪肉涨价也要我出手，还是以前当山大王时逍遥自在，要是有个压寨夫人该多好！','老大，大名府最近在举办鹊桥相亲会，凭你的人品武艺，定能冠压群雄！','0'), ('185','6','7','1','44711','秀才','0','70','1426','71880','','0','','大名府','在下阳谷县令，报名参加大名府鹊桥会！','知府提督尚且在排队，区区县令算什么？','','','0'), ('186','6','7','2','44721','衙役','0','70','1426','64692','','0','','大名府','请光头评委给我个机会！','为什么我这么帅却要掉头发，你那么丑却不掉头发？我给你机会，谁给我机会？','','','0'), ('187','6','7','3','44731','王太守','1','70','1426','72336','','0','','大名府','又来一个光头……请再给我这大龄青年一个机会吧！','小兄弟不要急，我们来讨论一下你最喜欢的颜色先。','','','0'), ('188','6','7','4','44741','梁中书','0','70','4277','156963','【山】妖卡碎片','20040','','大名府','美女，我对你一见钟情，让我照顾你一辈子吧！','你年薪有一亿美金吗？有独栋别墅吗？有私人飞机吗？有私人游轮吗？','现在的妹子太拜金了！罢了，我们化悲痛为力量，去汴梁城参加汴梁杯蹴鞠赛吧，说不定还能见到皇上！','老大英明！赢了的话还能拿到一大笔奖金呢！','0'), ('189','6','8','1','44811','报名官','0','70','1457','75096','','0','','汴梁城','狭路相逢勇者胜，这第一场热身赛，哥计划完胜！','那得问问我老人家，我能把有的球给吹没了，也能把没的球给吹有了。','','','0'), ('190','6','8','2','44821','陆谦','0','70','1457','75096','','0','','汴梁城','你就是大帝？是森林大帝吧！','天亮了，你看，我的护球像亨利吧？','','','0'), ('191','6','8','3','44831','高衙内','0','70','1457','80690','','0','','汴梁城','久仰久仰，蹴鞠一霸说的就是您吧？','好说，来，让你见识一下我的锁喉神功！','','','0'), ('192','6','8','4','44841','高俅','0','70','4370','128408','【山】妖卡碎片','20040','','汴梁城','一路高歌猛进打入决赛，眼看冠军在望，兄弟们拼了！','井底之蛙大言不惭，老夫这首长秘书可是踢蹴鞠踢出来的，岂会输给你们这帮黄口小儿乎？','这奸贼高俅的确如此难搞，好在我没有让各位观众失望！兄弟们，咱们此举也当载入史册啊！','太牛了，老大！高俅这奸臣竟然败于咱们手上，咱们也可名垂青史，万古流芳了！','0');
INSERT INTO `ol_user` VALUES ('1','4bf764883dad11e3bd95005056a3001f','4bf764883dad11e3bd95005056a3001f','','','2','0','1382730372','1382730372','','0','0','0','0','','0',NULL,'0','bddk1_1','2013-10-26','0'), ('2','57d9c9243db911e3bd95005056a3001f','57d9c9243db911e3bd95005056a3001f','','','2','0','1382967123','1382967132','','0','0','0','0','','0',NULL,'0','bddk1_1','2013-10-28','0');
INSERT INTO `ol_vip_discount` VALUES ('1','1','0.950'), ('2','2','0.900'), ('3','3','0.850'), ('4','4','0.800');
INSERT INTO `ol_zyd` VALUES ('1','1','3','1','stefan','0','','0','','2013-10-26','0','0','a','','0','0','0'), ('2','5','3','1','stefan','0','','0','','2013-10-26','0','0','e','','0','0','0'), ('3','5','6','1','stefan','0','','0','','2013-10-26','0','0','e','','0','0','0'), ('4','3','1','1','stefan','0','','0','','2013-10-26','0','0','c','','0','0','0'), ('5','4','4','1','stefan','0','','0','','2013-10-26','0','0','d','','0','0','0'), ('6','1','3','1','stefan','0','','0','','2013-10-26','0','0','a','','0','0','0'), ('7','3','7','1','stefan','0','','0','','2013-10-26','0','0','c','','0','0','0'), ('8','4','7','1','stefan','0','','0','','2013-10-26','0','0','d','','0','0','0'), ('9','4','9','1','stefan','0','','0','','2013-10-26','0','0','d','','0','0','0');
INSERT INTO `ol_zyd_qxzl` VALUES ('1','10','2','æŽé¬¼','0','1','98','98','117','78','12','0','','0','','0','å‡æŽé€µå‰ªå¾„','æŽé¬¼å®¶è´¢','è“å°†å¡*1 éšæœº+6è£…å¤‡*1 é‡‘åˆšçŸ³*10 é“¶ç¥¨50','åœ°ç—žæŽé¬¼ï¼Œå‡å†’æŽé€µåœ¨æ²‚æ°´åŸŽå¤–å‰ªå¾„æ‰“åŠ«ï¼Œè¿‡å¾€è¡Œäººå¤šå—å…¶å®³ï¼Œå‰å¾€æ²‚æ°´ç™¾ä¸ˆæ‘ï¼Œé“²é™¤è¿™ä¸ªæ— è€»å°äººã€‚','ç™¾ä¸ˆæ‘','4','YX003'), ('2','20','4','å‘¨é€š','0','5','214','138','138','172','13','0','','0','','0','é†‰å…¥é”€é‡‘å¸','å°éœ¸çŽ‹å½©ç¤¼','è“å°†å¡*2 éšæœº+9è£…å¤‡*1 é‡‘åˆšçŸ³*20 é“¶ç¥¨100','åˆ˜å¤ªå…¬çš„å¥³å„¿å¹´æ–¹åä¹ï¼Œå°éœ¸çŽ‹å‘¨é€šåž‚æ¶Žå…¶ç¾Žè²Œï¼Œæ¬²è¡Œé€¼å©šã€‚ä½•ä¸ç”·æ‰®å¥³è£…ï¼Œæ½œå…¥é—ºæˆ¿ï¼Œç“®ä¸­æ‰é³–ã€‚','æ¡ƒèŠ±å±±','5','YX025'), ('3','30','3','è‘£è¶…','0','2','482','322','482','322','17','0','','0','','0','å¤§é—¹é‡ŽçŒªæž—','è±¹å­å¤´åŒ…è£¹','ç´«å°†å¡*1 éšæœº+12è£…å¤‡*1 çŽ›ç‘™*5 é“¶ç¥¨200','å…«åä¸‡ç¦å†›æ•™å¤´æž—å†²è¢«é«˜ä¿…é™·å®³ï¼Œåˆºé…æ²§å·žã€‚è§£å·®è‘£è¶…å¥‰é«˜ä¿…ä¹‹å‘½ï¼Œä¼ºæœºæ€å®³æž—å†²ã€‚é‡ŽçŒªæž—æ˜¯æŠ¼è§£çš„å¿…ç»ä¹‹è·¯ï¼Œè§£æ•‘è½éš¾è±¹å­å¤´ï¼Œåˆ»ä¸å®¹ç¼“ã€‚','é‡ŽçŒªæž—','6','YX001'), ('4','40','4','æ¨å¿—','0','1','839','839','1007','671','18','0','','0','','0','æ™ºå–ç”Ÿè¾°çº²','ç”Ÿè¾°çº²','ç´«å°†å¡*2 éšæœº+15è£…å¤‡*1 çŽ„é“*10 é“œé’±50000','åˆåˆ°äº†å¤ªå¸ˆè”¡äº¬çš„ç”Ÿè¾°ï¼Œå¤§ååºœæ¢ä¸­ä¹¦ç­¹å¾—åä¸‡è´¯ç”Ÿè¾°çº²ï¼Œè¿é€åˆ°äº¬å¸ˆä»¥ä½œè´ºç¤¼ï¼Œè½¦é˜Ÿè¦ç»è¿‡é»„æ³¥å²—ã€‚æ­¤ç­‰ä¸ä¹‰ä¹‹è´¢ï¼Œèµ¶å¿«åŽ»åŠ«äº†å®ƒï¼','é»„æ³¥å²—','7','YX022'), ('5','50','3','å²æ–‡æ­','0','4','1944','972','1944','1620','19','0','','0','','0','æ´»æ‰å²æ–‡æ­','æ›¾æ°è´¢å®','éšæœºæ©™å°†ç¢Žç‰‡*1 éšæœº+18è£…å¤‡*1 çŽ›ç‘™*20 é“œé’±100000','å²æ–‡æ­å°„æ€æ™ç›–ï¼Œæ˜¯æ¢å±±æœ€å¤§ä»‡å®¶ã€‚æ¢å±±äººé©¬å¤œè¢­æ›¾å¤´å¸‚ï¼Œå²æ–‡æ­ä¹˜åƒé‡Œé¾™é©¹é€ƒè„±ã€‚è‹¥èƒ½æå‰åŸ‹ä¼åœ¨æ ‘æž—é‡Œï¼Œå¿…èƒ½æ´»æ‰å²æ–‡æ­ï¼','æ›¾å¤´å¸‚','8','YX019'), ('6','60','1','é»„æ–‡ç‚³','0','3','1927','1156','1927','2697','20','0','','0','','0','æ±Ÿå·žåŠ«æ³•åœº','æ±Ÿå·žå†›éœ€','éšæœºæ©™å°†ç¢Žç‰‡*1 éšæœº+21è£…å¤‡*1 ç¿¡ç¿ *10 é“œé’±150000','å®‹æ±Ÿé†‰é…’åœ¨æµ”é˜³æ¥¼é¢˜è¯—å’å¿—ï¼Œè¢«é»„æ–‡ç‚³ä¸¾æŠ¥é­æ“’ï¼Œå®šäºŽä¸ƒæœˆåä¹é—®æ–©ã€‚å„è·¯è±ªæ°ç›¸é‚€ï¼Œé½èšæ±Ÿå·žæ•‘å…¬æ˜Žã€‚','æ±Ÿå·žæ³•åœº','9','YX029'), ('7','70','5','é«˜ä¿…','0','4','3196','1744','3196','2712','21','0','','0','','0','å‡¿èˆ¹æ“’é«˜ä¿…','å¤ªå°‰å®ç®±','éšæœºä¸“å±žæ­¦å™¨*1 ç™½çŽ‰*10 å…ƒå®*200 é“œé’±200000','å¤ªå°‰é«˜ä¿…é€ æµ·é³…èˆ¹ï¼ŒçŽ‡æ°´æ‰‹ä¸‡ä½™å¾æ¢å±±ã€‚æµ·é³…èˆ¹æ”»å‡»åŠ›æžå¼ºï¼Œéš¾ä»¥æ­£é¢å¯¹æŠ—ã€‚è‹¥æœ‰æ°´æ€§è°™ç†Ÿè€…æ½œåœ¨æ°´ä¸‹ï¼Œä¼ºæœºå‡¿ç©¿èˆ¹åº•ï¼Œå¿…èƒ½ä¸æˆ˜è€Œèƒœï¼','æ¢å±±æ°´é“','10','NPC_Icon32');

-- ----------------------------
--  Trigger definition for `ol_dalei_jf`
-- ----------------------------
DELIMITER ;;
CREATE TRIGGER `lsh_m_tigger` BEFORE UPDATE ON `ol_dalei_jf` FOR EACH ROW SET new.lsh_m = -1*NEW.lsh
;;
DELIMITER ;
