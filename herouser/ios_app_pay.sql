-- ----------------------------
-- Table structure for `ol_app_payinfo`
-- ----------------------------
drop table if EXISTS`ol_app_payinfo`;
CREATE TABLE  IF NOT EXISTS  `ol_app_payinfo` (
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
-- Table structure for `ol_app_payinfo_error`
-- ----------------------------
drop table if EXISTS`ol_app_payinfo_error`;
CREATE TABLE  IF NOT EXISTS  `ol_app_payinfo_error` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `errorno` int(5) NOT NULL,
  `errormsg` varchar(50) NOT NULL,
  `errorDate` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `ol_app_productInfo`
-- ----------------------------
drop table if EXISTS`ol_app_productInfo`;
CREATE TABLE  IF NOT EXISTS  `ol_app_productInfo` (
  `productID` varchar(50) NOT NULL,
  `mc` varchar(50) NOT NULL COMMENT '产品名称',
  `iid` varchar(100) NOT NULL COMMENT '图标地址',
  `jg` int(4) NOT NULL COMMENT '产品价格',
  `publish` tinyint(1) NOT NULL COMMENT '是否发布',
  `yb` int(6) NOT NULL COMMENT '元宝数量',
  `apple_ID` int(11) NOT NULL,
  `sfgq` tinyint(1) NOT NULL,
  PRIMARY KEY (`apple_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ol_app_productInfo
-- ----------------------------
INSERT INTO `ol_app_productInfo` VALUES ('60gold', '60元宝', 'yb1', '6', '1', '60', '531071160', '1');
INSERT INTO `ol_app_productInfo` VALUES ('300gold', '300元宝', 'yb2', '30', '1', '300', '531073447', '1');
INSERT INTO `ol_app_productInfo` VALUES ('2500gold', '2500元宝', 'yb4', '198', '1', '2500', '531073957', '1');
INSERT INTO `ol_app_productInfo` VALUES ('5500gold', '5500元宝', 'yb5', '328', '1', '5500', '531074333', '1');
INSERT INTO `ol_app_productInfo` VALUES ('11000gold', '11000元宝', 'yb6', '648', '1', '11000', '531074558', '1');
INSERT INTO `ol_app_productInfo` VALUES ('1000gold', '1000元宝', 'yb3', '88', '1', '1000', '531211526', '1');
INSERT INTO `ol_app_productInfo` VALUES ('60goldHD', '60元宝', 'yb1', '6', '1', '60', '535737828', '2');
INSERT INTO `ol_app_productInfo` VALUES ('300goldHD', '300元宝', 'yb2', '30', '1', '300', '535738125', '2');
INSERT INTO `ol_app_productInfo` VALUES ('1000goldHD', '1000元宝', 'yb3', '88', '1', '1000', '535738462', '2');
INSERT INTO `ol_app_productInfo` VALUES ('2500goldHD', '2500元宝', 'yb4', '198', '1', '2500', '535738538', '2');
INSERT INTO `ol_app_productInfo` VALUES ('5500goldHD', '5500元宝', 'yb5', '328', '1', '5500', '535739194', '2');
INSERT INTO `ol_app_productInfo` VALUES ('11000goldHD', '11000元宝', 'yb6', '648', '1', '11000', '535739493', '2');
INSERT INTO `ol_app_productInfo` VALUES ('com.jofgame.nizhuanfengyun.120', '120元寶', 'yb1', '60', '1', '120', '645440588', '3');
INSERT INTO `ol_app_productInfo` VALUES ('com.jofgame.nizhuanfengyun.2200', '2200元寶', 'yb4', '990', '1', '2200', '645441636', '3');
INSERT INTO `ol_app_productInfo` VALUES ('com.jofgame.nizhuanfengyun.310', '310元寶', 'yb2', '150', '1', '310', '645442037', '3');
INSERT INTO `ol_app_productInfo` VALUES ('com.jofgame.nizhuanfengyun.3600', '3600元寶', 'yb5', '1490', '1', '3600', '645443261', '3');
INSERT INTO `ol_app_productInfo` VALUES ('com.jofgame.nizhuanfengyun.7500', '7500元寶', 'yb6', '2990', '1', '7500', '645443643', '3');
INSERT INTO `ol_app_productInfo` VALUES ('com.jofgame.nizhuanfengyun.970', '970元寶', 'yb3', '450', '1', '970', '645443697', '3');
INSERT INTO `ol_app_productInfo` VALUES ('0901230620', '10元宝', 'yb1', '1', '1', '1', '901230620', '4');
INSERT INTO `ol_app_productInfo` VALUES ('0901230623', '100000元宝', 'yb6', '10000', '1', '100000', '901230623', '4');
INSERT INTO `ol_app_productInfo` VALUES ('com.sothinksoft.zjsh.iap.60', '60元宝', 'yb1', '6', '1', '60', '657678368', '5');
INSERT INTO `ol_app_productInfo` VALUES ('com.sothinksoft.zjsh.iap.310', '310元宝', 'yb2', '30', '1', '310', '657678484', '5');
INSERT INTO `ol_app_productInfo` VALUES ('com.sothinksoft.zjsh.iap.950', '950元宝', 'yb3', '88', '1', '950', '657678551', '5');
INSERT INTO `ol_app_productInfo` VALUES ('com.sothinksoft.zjsh.iap.6500', '6500元宝', 'yb6', '488', '1', '6500', '657679301', '5');
INSERT INTO `ol_app_productInfo` VALUES ('com.sothinksoft.zjsh.iap.2200', '2200元宝', 'yb4', '198', '1', '2200', '657679554', '5');
INSERT INTO `ol_app_productInfo` VALUES ('com.sothinksoft.zjsh.iap.4000', '4000元宝', 'yb5', '328', '1', '4000', '657680073', '5');
INSERT INTO `ol_app_productInfo` VALUES ('1', '小元宝包', 'yb1', '2', '1', '10', '1', '6');
INSERT INTO `ol_app_productInfo` VALUES ('2', '中元宝包', 'yb2', '5', '1', '25', '2', '6');
INSERT INTO `ol_app_productInfo` VALUES ('3', '大元宝包', 'yb3', '10', '1', '50', '3', '6');
INSERT INTO `ol_app_productInfo` VALUES ('4', '超大元宝包', 'yb4', '30', '1', '150', '4', '6');
INSERT INTO `ol_app_productInfo` VALUES ('1', '10元宝', 'yb1', '1', '1', '10', '100000000', '7');
INSERT INTO `ol_app_productInfo` VALUES ('2', '50元宝', 'yb2', '5', '1', '50', '100000001', '7');
INSERT INTO `ol_app_productInfo` VALUES ('3', '100元宝', 'yb3', '10', '1', '100', '100000002', '7');
INSERT INTO `ol_app_productInfo` VALUES ('4', '500元宝', 'yb4', '50', '1', '500', '100000003', '7');
INSERT INTO `ol_app_productInfo` VALUES ('5', '1000元宝', 'yb5', '100', '1', '1000', '100000004', '7');
INSERT INTO `ol_app_productInfo` VALUES ('6', '5000元宝', 'yb6', '500', '1', '5000', '100000005', '7');
INSERT INTO `ol_app_productInfo` VALUES ('1', '10元宝', 'yb1', '1', '1', '10', '200000001', '8');
INSERT INTO `ol_app_productInfo` VALUES ('2', '100元宝', 'yb2', '10', '1', '100', '200000002', '8');
INSERT INTO `ol_app_productInfo` VALUES ('3', '200元宝', 'yb3', '20', '1', '200', '200000003', '8');
INSERT INTO `ol_app_productInfo` VALUES ('4', '500元宝', 'yb4', '50', '1', '500', '200000004', '8');
INSERT INTO `ol_app_productInfo` VALUES ('5', '1000元宝', 'yb5', '100', '1', '1000', '200000005', '8');
INSERT INTO `ol_app_productInfo` VALUES ('6', '3000元宝', 'yb6', '300', '1', '3000', '200000006', '8');
INSERT INTO `ol_app_productInfo` VALUES ('1', '10元宝', 'yb1', '1', '1', '10', '300000001', '9');
INSERT INTO `ol_app_productInfo` VALUES ('2', '100元宝', 'yb2', '10', '1', '100', '300000002', '9');
INSERT INTO `ol_app_productInfo` VALUES ('3', '200元宝', 'yb3', '20', '1', '200', '300000003', '9');
INSERT INTO `ol_app_productInfo` VALUES ('4', '500元宝', 'yb4', '50', '1', '500', '300000004', '9');
INSERT INTO `ol_app_productInfo` VALUES ('5', '1000元宝', 'yb5', '100', '1', '1000', '300000005', '9');
INSERT INTO `ol_app_productInfo` VALUES ('6', '3000元宝', 'yb6', '300', '1', '3000', '300000006', '9');
INSERT INTO `ol_app_productInfo` VALUES ('1', '10元宝', 'yb1', '1', '1', '10', '400000001', '10');
INSERT INTO `ol_app_productInfo` VALUES ('2', '20元宝', 'yb2', '2', '1', '20', '400000002', '10');
INSERT INTO `ol_app_productInfo` VALUES ('3', '50元宝', 'yb3', '5', '1', '50', '400000003', '10');
INSERT INTO `ol_app_productInfo` VALUES ('4', '100元宝', 'yb4', '10', '1', '100', '400000004', '10');
INSERT INTO `ol_app_productInfo` VALUES ('5', '200元宝', 'yb5', '20', '1', '200', '400000005', '10');
INSERT INTO `ol_app_productInfo` VALUES ('6', '300元宝', 'yb6', '30', '1', '300', '400000006', '10');

