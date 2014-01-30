-- ----------------------------
-- Table structure for `ol_zyd_qxzl`
-- ----------------------------
DROP TABLE IF EXISTS `ol_zyd_qxzl`;
CREATE TABLE `ol_zyd_qxzl` (
  `yd_id` tinyint(2) NOT NULL AUTO_INCREMENT,
  `yd_level` int(2) NOT NULL,
  `yd_type` tinyint(1) NOT NULL,
  `xm` varchar(10) NOT NULL COMMENT '武将姓名',
  `sex` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0男性1女性',
  `zy` tinyint(1) NOT NULL COMMENT '职业',
  `gj` int(4) NOT NULL,
  `fy` int(4) NOT NULL,
  `tl` int(4) NOT NULL,
  `mj` int(4) NOT NULL,
  `kssj` int(2) NOT NULL COMMENT '活动开始时间',
  `zlpid` bigint(12) NOT NULL,
  `zlpname` varchar(50) NOT NULL,
  `zlwjid` bigint(12) NOT NULL,
  `zlwjmc` varchar(10) NOT NULL,
  `zlsj` int(11) NOT NULL,
  `zlmc` varchar(50) NOT NULL COMMENT '逐鹿名称',
  `djmc` varchar(20) NOT NULL COMMENT '大奖名称',
  `djnr` varchar(255) NOT NULL COMMENT '大奖内容',
  `zljj` varchar(255) NOT NULL COMMENT '逐鹿简介',
  `zldd` varchar(12) NOT NULL COMMENT '活动地点',
  `dj` tinyint(2) NOT NULL,
  `avatar` char(20) NOT NULL,
  PRIMARY KEY (`yd_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ol_zyd_qxzl
-- ----------------------------
INSERT INTO `ol_zyd_qxzl` VALUES ('1', '10', '2', '李鬼', '0', '1', '98', '98', '117', '78', '12', '0', '', '0', '', '0', '假李逵剪径', '李鬼家财', '蓝将卡*1 随机+6装备*1 金刚石*10 银票50', '地痞李鬼，假冒李逵在沂水城外剪径打劫，过往行人多受其害，前往沂水百丈村，铲除这个无耻小人。', '百丈村', '4', 'YX003');
INSERT INTO `ol_zyd_qxzl` VALUES ('2', '20', '4', '周通', '0', '5', '214', '138', '138', '172', '13', '0', '', '0', '', '0', '醉入销金帐', '小霸王彩礼', '蓝将卡*2 随机+9装备*1 金刚石*20 银票100', '刘太公的女儿年方十九，小霸王周通垂涎其美貌，欲行逼婚。何不男扮女装，潜入闺房，瓮中捉鳖。', '桃花山', '5', 'YX025');
INSERT INTO `ol_zyd_qxzl` VALUES ('3', '30', '3', '董超', '0', '2', '482', '322', '482', '322', '17', '0', '', '0', '', '0', '大闹野猪林', '豹子头包裹', '紫将卡*1 随机+12装备*1 玛瑙*5 银票200', '八十万禁军教头林冲被高俅陷害，刺配沧州。解差董超奉高俅之命，伺机杀害林冲。野猪林是押解的必经之路，解救落难豹子头，刻不容缓。', '野猪林', '6', 'YX001');
INSERT INTO `ol_zyd_qxzl` VALUES ('4', '40', '4', '杨志', '0', '1', '839', '839', '1007', '671', '18', '0', '', '0', '', '0', '智取生辰纲', '生辰纲', '紫将卡*2 随机+15装备*1 玄铁*10 铜钱50000', '又到了太师蔡京的生辰，大名府梁中书筹得十万贯生辰纲，运送到京师以作贺礼，车队要经过黄泥岗。此等不义之财，赶快去劫了它！', '黄泥岗', '7', 'YX022');
INSERT INTO `ol_zyd_qxzl` VALUES ('5', '50', '3', '史文恭', '0', '4', '1944', '972', '1944', '1620', '19', '0', '', '0', '', '0', '活捉史文恭', '曾氏财宝', '随机橙将碎片*1 随机+18装备*1 玛瑙*20 铜钱100000', '史文恭射杀晁盖，是梁山最大仇家。梁山人马夜袭曾头市，史文恭乘千里龙驹逃脱。若能提前埋伏在树林里，必能活捉史文恭！', '曾头市', '8', 'YX019');
INSERT INTO `ol_zyd_qxzl` VALUES ('6', '60', '1', '黄文炳', '0', '3', '1927', '1156', '1927', '2697', '20', '0', '', '0', '', '0', '江州劫法场', '江州军需', '随机橙将碎片*1 随机+21装备*1 翡翠*10 铜钱150000', '宋江醉酒在浔阳楼题诗咏志，被黄文炳举报遭擒，定于七月十九问斩。各路豪杰相邀，齐聚江州救公明。', '江州法场', '9', 'YX029');
INSERT INTO `ol_zyd_qxzl` VALUES ('7', '70', '5', '高俅', '0', '4', '3196', '1744', '3196', '2712', '21', '0', '', '0', '', '0', '凿船擒高俅', '太尉宝箱', '随机专属武器*1 白玉*10 元宝*200 铜钱200000', '太尉高俅造海鳅船，率水手万余征梁山。海鳅船攻击力极强，难以正面对抗。若有水性谙熟者潜在水下，伺机凿穿船底，必能不战而胜！', '梁山水道', '10', 'NPC_Icon32');

-- -----------------------------
-- Phone compatible EMAIL data
-- -----------------------------
ALTER TABLE `ol_player` MODIFY COLUMN `phone`  varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT '玩家绑定手机号,0标识没有绑定，999999表示等待玩家访问标识' AFTER `last_update_level`;


-- ----------------------------
-- Table structure for `ol_dalei_2`
-- ----------------------------
DROP TABLE IF EXISTS `ol_dalei_2`;
CREATE TABLE `ol_dalei_2` (
  `playersid` int(11) NOT NULL,
  `pm` int(11) NOT NULL AUTO_INCREMENT COMMENT '排名',
  `credits` int(11) NOT NULL DEFAULT '0' COMMENT '积分',
  `winning` int(11) NOT NULL DEFAULT '0' COMMENT '连胜场次',
  `dl_c` int(11) NOT NULL DEFAULT '20' COMMENT '打擂剩余次数',
  `buy_c` int(11) NOT NULL DEFAULT '0' COMMENT '购买次数',
  `last_buy_time` int(11) NOT NULL DEFAULT '0' COMMENT '最后购买打擂次数时间',
  `last_dl_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '最后打擂时间',
  `last_cdt_time` int(11) NOT NULL DEFAULT '0' COMMENT '最后获取积分时间',
  `g_ids` varchar(128) NOT NULL DEFAULT '' COMMENT '上次主动打擂的将领ID',
  `flush_times` int(11) NOT NULL DEFAULT '0' COMMENT '试炼场主动刷新次数',
  `last_flush_time` int(11) NOT NULL DEFAULT '0' COMMENT '试炼场最后刷新时间',
  PRIMARY KEY (`pm`),
  UNIQUE KEY `index_ol_dalei_2_playersid` (`playersid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ------------------------------
-- dump dalei data
-- ------------------------------
insert into ol_dalei_2(`playersid`, `last_cdt_time`) 
  select z.`playersid`, UNIX_TIMESTAMP(CURRENT_TIMESTAMP()) from ol_pm_zl z, ol_player p 
    where p.player_level>=7 and z.playersid=p.playersid
  order by pm ;

-- ------------------------------
-- init auto increment
-- ------------------------------
DELIMITER $$
DROP PROCEDURE IF EXISTS reset_dalei_autoincrement$$
CREATE PROCEDURE reset_dalei_autoincrement()
BEGIN
  SELECT IFNULL(MAX(`pm`),0)+1 into @max FROM ol_dalei_2;
	 SET @SqlCmd = concat('ALTER TABLE ol_dalei_2 AUTO_INCREMENT = ', @max);
	PREPARE stmt FROM @SqlCmd;
  EXECUTE stmt ;

  DEALLOCATE PREPARE stmt;
END $$
DELIMITER ;

CALL reset_dalei_autoincrement();

drop PROCEDURE reset_dalei_autoincrement;
  

