/*
Navicat MySQL Data Transfer

Source Server         : 59.175.238.6_mysql
Source Server Version : 40120
Source Host           : 59.175.238.6:3306
Source Database       : vpopmail

Target Server Type    : MYSQL
Target Server Version : 40120
File Encoding         : 65001

Date: 2014-01-23 14:24:51
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for vpopmail
-- ----------------------------
DROP TABLE IF EXISTS `vpopmail`;
CREATE TABLE `vpopmail` (
  `pw_name` char(32) NOT NULL default '',
  `pw_domain` char(64) NOT NULL default '',
  `pw_passwd` char(40) default NULL,
  `pw_uid` int(11) default NULL,
  `pw_gid` int(11) default NULL,
  `pw_gecos` char(48) default NULL,
  `pw_dir` char(160) default NULL,
  `pw_shell` char(20) default NULL,
  `pw_clear_passwd` char(16) default NULL,
  PRIMARY KEY  (`pw_name`,`pw_domain`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of vpopmail
-- ----------------------------
INSERT INTO `vpopmail` VALUES ('postmaster', 'sothink.com', '$1$9yrzmd55$k39kk65iFghBXJPdahdPU/', '0', '0', 'Postmaster', '/home/vpopmail/domains/sothink.com/postmaster', 'NOQUOTA', '123456');
INSERT INTO `vpopmail` VALUES ('zhanghong', 'sothink.com', '$1$n/1KIcuX$cwBIK1hS5m0.P2xOjTwtF1', '0', '0', 'zhanghong', '/home/vpopmail/domains/sothink.com/zhanghong', 'NOQUOTA', 'aptech');
INSERT INTO `vpopmail` VALUES ('dy', 'tutu-mobile.com', '$1$/1r7JyOH$PoFYE03HeGEJvXhdOg1Px1', '0', '0', 'dengyang', '/home/vpopmail/domains/tutu-mobile.com/dy', 'NOQUOTA', '182021093');
INSERT INTO `vpopmail` VALUES ('postmaster', 'flashpioneer.com', '$1$p32y2.4M$yPsjuCu2anWdYUMgaBALS/', '0', '0', 'Postmaster', '/home/vpopmail/domains/flashpioneer.com/postmaster', 'NOQUOTA', 'Jan20Year08');
INSERT INTO `vpopmail` VALUES ('zfy', 'sothink.com', '$1$N.n.ntD8$Uu2iJmC2Fjh2Wkphs.dyL0', '0', '0', 'ZhengFengYing', '/home/vpopmail/domains/sothink.com/zfy', 'NOQUOTA', 'Windy612');
INSERT INTO `vpopmail` VALUES ('info', 'sothink.com', '$1$qy2KUrpk$uKaEKUK5Wz2skY2A7PO540', '0', '0', 'sothink.com', '/home/vpopmail/domains/sothink.com/info', 'NOQUOTA', 'Ye8Vx5gP');
INSERT INTO `vpopmail` VALUES ('hohband', 'sothink.com', '$1$3QCuqEXC$byWroGkFalYqhUtKwvo/x/', '0', '0', 'Li Li', '/home/vpopmail/domains/sothink.com/hohband', 'NOQUOTA', 'dGT4%j&3');
INSERT INTO `vpopmail` VALUES ('sourcetec', 'sothink.com', '$1$CwWnZvzu$M/T4INGbPrYSiuDXdZlXD0', '0', '0', 'SourceTec', '/home/vpopmail/domains/sothink.com/sourcetec', 'NOQUOTA', 'abc123');
INSERT INTO `vpopmail` VALUES ('hb.tang', 'sothink.com', '$1$ifFokOOB$ser65.EH4LVnDHneunCzK0', '0', '0', 'TangHongbo', '/home/vpopmail/domains/sothink.com/hb.tang', 'NOQUOTA', 'Just456');
INSERT INTO `vpopmail` VALUES ('sophie', 'sothink.com', '$1$8uDLX/Oa$htypsZ9yRu5FAUkgyc.Vw0', '0', '0', 'sophie@sothink.com', '/home/vpopmail/domains/sothink.com/sophie', 'NOQUOTA', '045621');
INSERT INTO `vpopmail` VALUES ('postmaster', 'flash-animation-maker.com', '$1$GQrlanOR$XeTKAoDxBd5nifsfbKBoP1', '0', '0', 'Postmaster', '/home/vpopmail/domains/flash-animation-maker.com/postmaster', 'NOQUOTA', 'Jan20Year08');
INSERT INTO `vpopmail` VALUES ('yanzi', 'sothink.com', '$1$a0zCxnhC$uNR8fPSngzEpWe0PCOL4u1', '0', '0', 'yanzi@sothink.com', '/home/vpopmail/domains/sothink.com/yanzi', 'NOQUOTA', 'Yunif5&#F');
INSERT INTO `vpopmail` VALUES ('ch', 'sothink.com', '$1$NWLsABMT$plP.ezWK8Pu/SBp7X5mrJ/', '0', '0', 'ChenHao', '/home/vpopmail/domains/sothink.com/ch', 'NOQUOTA', 'gh5Y%r#_Y');
INSERT INTO `vpopmail` VALUES ('lilyzhang', 'sothink.com', '$1$tCEKr7jW$OjpnrmuxgKgNnAPUeuHqm0', '0', '0', 'lilyzhanghong@sothink.com', '/home/vpopmail/domains/sothink.com/lilyzhang', 'NOQUOTA', 'CY/ADq\\N');
INSERT INTO `vpopmail` VALUES ('zhangxia', 'sothink.com', '$1$nKaD/cpA$BBf1aAzhaNuxKDSi.4tcl/', '0', '0', 'zhangxia', '/home/vpopmail/domains/sothink.com/zhangxia', 'NOQUOTA', 'hustershrimp86');
INSERT INTO `vpopmail` VALUES ('info', 'srctec.com', '$1$kvHaLb5r$Sjj3aD9YWesRa3XfnkBJB/', '0', '0', 'info@srctec.com', '/home/vpopmail/domains/srctec.com/info', 'NOQUOTA', 'sgE4q7Trh');
INSERT INTO `vpopmail` VALUES ('postmaster', 'myconverters.com', '$1$plmXSTH6$E.0pB7716BwQXeGE.fTaS1', '0', '0', 'Postmaster', '/home/vpopmail/domains/myconverters.com/postmaster', 'NOQUOTA', 'Jan20Year08');
INSERT INTO `vpopmail` VALUES ('fsc', 'sothink.com', '$1$gCv0uSbO$v4nTpWy8hizz3Qy1enugr0', '0', '0', 'FuShuChi', '/home/vpopmail/domains/sothink.com/fsc', 'NOQUOTA', 'fushuchi');
INSERT INTO `vpopmail` VALUES ('liwenjing', 'sothink.com', '$1$bHeK92iy$Z9HpghGUXYLdrQY1F8l1n1', '0', '0', 'liwenjing', '/home/vpopmail/domains/sothink.com/liwenjing', 'NOQUOTA', 'liwenjing830116');
INSERT INTO `vpopmail` VALUES ('wangting', 'sothink.com', '$1$H7olxCsT$kDUud38opZmXjEe4LGESg0', '0', '0', 'WangTing', '/home/vpopmail/domains/sothink.com/wangting', 'NOQUOTA', 'wt1122');
INSERT INTO `vpopmail` VALUES ('zhongsi', 'sothink.com', '$1$5i/geSzk$e3x6n0w4aMe0wRRYPAwea/', '0', '0', 'ÖÓË¹', '/home/vpopmail/domains/sothink.com/zhongsi', '104857600S', '1q2w3e?');
INSERT INTO `vpopmail` VALUES ('wangyuan', 'sothink.com', '$1$R6C.OMXz$mTFl3slQpHVlAa2LBfjm41', '0', '0', 'WangYuan', '/home/vpopmail/domains/sothink.com/wangyuan', 'NOQUOTA', 'KJ2002valley');
INSERT INTO `vpopmail` VALUES ('lengjiancui', 'sothink.com', '$1$xzP5MWLV$Ii3DGaDrD1iWa0FmBLEFI1', '0', '0', 'lengjiancui', '/home/vpopmail/domains/sothink.com/lengjiancui', 'NOQUOTA', 'df5Y&J%7w');
INSERT INTO `vpopmail` VALUES ('master_test', 'sothink.com', '$1$8YndOSqF$A0PtfWot.iwKpJf1b5W8R0', '0', '0', 'aaaa', '/home/vpopmail/domains/sothink.com/master_test', '104857600S', '1q2w3e?');
INSERT INTO `vpopmail` VALUES ('melisa', 'sothink.com', '$1$az63BIPF$WMjmj4UZS1Xr5Qw9rhp.g1', '0', '0', 'yelu', '/home/vpopmail/domains/sothink.com/melisa', 'NOQUOTA', 'kG4%7*f3');
INSERT INTO `vpopmail` VALUES ('submit', 'sothink.com', '$1$0Rxs1IPg$JWvzFyAmlaJMia9snr25f.', '0', '0', 'submit', '/home/vpopmail/domains/sothink.com/submit', 'NOQUOTA', 'dft78k9');
INSERT INTO `vpopmail` VALUES ('support', 'sothink.com', '$1$af11khUu$QmBjb.o4Sc0uY9bmuAu1w0', '0', '0', 'Sothink Support', '/home/vpopmail/domains/sothink.com/support', 'NOQUOTA', 'b31AK4702');
INSERT INTO `vpopmail` VALUES ('marketing', 'sothink.com', '$1$LLrouDHI$LHLy4hOc7or/HvUMqOBE8.', '0', '0', 'Sothink Marketing', '/home/vpopmail/domains/sothink.com/marketing', 'NOQUOTA', 'sgE4q7Tr');
INSERT INTO `vpopmail` VALUES ('webmaster', 'sothink.com', '$1$gOrawZqL$bANltygn3qhskVyHnxDOF/', '0', '0', 'Sothink Webmaster', '/home/vpopmail/domains/sothink.com/webmaster', 'NOQUOTA', 'YQ9t1F8hM3');
INSERT INTO `vpopmail` VALUES ('affiliate', 'sothink.com', '$1$BNNPKlF1$RF5YN2ca/CuMd8tfpDUd9/', '0', '0', 'Sothink Affiliate', '/home/vpopmail/domains/sothink.com/affiliate', 'NOQUOTA', 'K28ing6box9');
INSERT INTO `vpopmail` VALUES ('reseller', 'sothink.com', '$1$yKU1n4DP$I5wJKjKdnkmEXuzjDk8k0.', '0', '0', 'Sothink Reseller', '/home/vpopmail/domains/sothink.com/reseller', 'NOQUOTA', 'K28ing6box9');
INSERT INTO `vpopmail` VALUES ('newsletter', 'sothink.com', '$1$MLni/on6$4qlGeT4EdaMRUBvizYUXj/', '0', '0', 'Sothink Newsletter', '/home/vpopmail/domains/sothink.com/newsletter', 'NOQUOTA', 'De97wan410');
INSERT INTO `vpopmail` VALUES ('test', 'sothink.com', '$1$AqGaQgV8$yVZn/rO9/UMZE8zo/oZSr.', '0', '0', 'OTRS Test Account', '/home/vpopmail/domains/sothink.com/test', 'NOQUOTA', 'otrs_2008$%');
INSERT INTO `vpopmail` VALUES ('postmaster', 'sothink.com.cn', '$1$6C4vK98t$yVE3msVXsXYP947e9.mhQ/', '0', '0', 'Postmaster', '/home/vpopmail/domains/sothink.com.cn/postmaster', 'NOQUOTA', 'Jan20Year08');
INSERT INTO `vpopmail` VALUES ('marketing', 'srctec.com', '$1$.e1Y3PmO$.ABwj28Lc3zUhF4shBUc01', '0', '0', 'marketing', '/home/vpopmail/domains/srctec.com/marketing', 'NOQUOTA', 'sgE4q7Tr');
INSERT INTO `vpopmail` VALUES ('postmaster', 'srctec.com', '$1$6exCrHhL$LurOZcccwgBs44eDIld9E0', '0', '0', 'Postmaster', '/home/vpopmail/domains/srctec.com/postmaster', 'NOQUOTA', 'Jan20Year08');
INSERT INTO `vpopmail` VALUES ('webmaster', 'srctec.com', '$1$R..FOF3s$VHDgSaVgwQVVZzunLvsHy0', '0', '0', 'webmaster', '/home/vpopmail/domains/srctec.com/webmaster', 'NOQUOTA', 'YQ9t1F8hM3');
INSERT INTO `vpopmail` VALUES ('luosen', 'jofgame.com', '$1$RepB3/q5$UmUxYlt0GPAiKNV2cSx4k.', '0', '0', 'luosen@jofgame.com', '/home/vpopmail/domains/jofgame.com/luosen', 'NOQUOTA', 'y3nZSnBq');
INSERT INTO `vpopmail` VALUES ('postmaster', 'tutu-mobile.com', '$1$W1M0XwhQ$Rflz6dmR0zPs/WLJYNVr8/', '0', '0', 'Postmaster', '/home/vpopmail/domains/tutu-mobile.com/postmaster', 'NOQUOTA', 'Jan20Year08');
INSERT INTO `vpopmail` VALUES ('webmaster', 'sothink.com.cn', '$1$VEpmonph$BRGDZCBwzp0Uj.SliYf2P.', '0', '0', 'Sothink CN Webmaster', '/home/vpopmail/domains/sothink.com.cn/webmaster', 'NOQUOTA', 'E429src');
INSERT INTO `vpopmail` VALUES ('support', 'sothink.com.cn', '$1$YxF2qBIX$ifzIOQNpybj0elhovNCv41', '0', '0', 'Sothink CN Support', '/home/vpopmail/domains/sothink.com.cn/support', 'NOQUOTA', 'Just4issw');
INSERT INTO `vpopmail` VALUES ('info', 'sothink.com.cn', '$1$eSQiLUXI$OoVbbFFCCAoR/bQGmzJe8.', '0', '0', 'Sothink CN Info', '/home/vpopmail/domains/sothink.com.cn/info', 'NOQUOTA', '+g3LMJS0');
INSERT INTO `vpopmail` VALUES ('sale', 'sothink.com.cn', '$1$lXqGvtga$V2tzSkY1PdeoYorMJTWXA0', '0', '0', 'Sothink CN Sale', '/home/vpopmail/domains/sothink.com.cn/sale', 'NOQUOTA', 'Just4issw');
INSERT INTO `vpopmail` VALUES ('lining', 'sothink.com', '$1$i8xAwZv6$gOsEJi8HEJl7zgXuIIep10', '0', '0', 'lining@sothink.com', '/home/vpopmail/domains/sothink.com/lining', 'NOQUOTA', 'Wc*h&6$A');
INSERT INTO `vpopmail` VALUES ('lili', 'sothink.com.cn', '$1$ZFqN1q5K$AxX5uDF56Zxmlk.6DJeD8.', '0', '0', 'lili', '/home/vpopmail/domains/sothink.com.cn/lili', 'NOQUOTA', '411700');
INSERT INTO `vpopmail` VALUES ('hbtang', 'sothink.com.cn', '$1$hr1Ip0PV$ip5lNAOBdNu6Gk1aMp3YH1', '0', '0', 'TangHongbo', '/home/vpopmail/domains/sothink.com.cn/hbtang', 'NOQUOTA', 'Just456');
INSERT INTO `vpopmail` VALUES ('myconverters', 'sothinkmedia.com', '$1$d7rebM.H$bGMbHp4lYsFVmSEkm8y7X0', '0', '0', 'myconverters', '/home/vpopmail/domains/sothinkmedia.com/myconverters', 'NOQUOTA', 'Just4issw');
INSERT INTO `vpopmail` VALUES ('newsletter', 'sothinkmedia.com', '$1$RRD3HiYy$R63rcg6nnx9L/v1AS.K770', '0', '0', 'newsletter@sothinkmedia.com', '/home/vpopmail/domains/sothinkmedia.com/newsletter', 'NOQUOTA', 'bh2B704');
INSERT INTO `vpopmail` VALUES ('support', 'tutu-mobile.com', '$1$l1xZA3PV$mbrc5QsNUw.8/r7041Gwm0', '0', '0', 'TuTu Support', '/home/vpopmail/domains/tutu-mobile.com/support', 'NOQUOTA', 'Free789Bird');
INSERT INTO `vpopmail` VALUES ('webmaster', 'tutu-mobile.com', '$1$NtvJ6glX$F/JLZC3Gf1q5d4F8EEaGH.', '0', '0', 'TuTu Webmaster', '/home/vpopmail/domains/tutu-mobile.com/webmaster', 'NOQUOTA', 'Free789Bird');
INSERT INTO `vpopmail` VALUES ('info', 'tutu-mobile.com', '$1$VxO1d3JQ$xrC8K2LCTk7B6hmGuCcm..', '0', '0', 'TuTu info', '/home/vpopmail/domains/tutu-mobile.com/info', 'NOQUOTA', 'Free789Bird');
INSERT INTO `vpopmail` VALUES ('dagou', 'tutu-mobile.com', '$1$c35W.SOY$uNjUqT/QlHd0P69suuIYD/', '0', '0', 'TuTu Dagou', '/home/vpopmail/domains/tutu-mobile.com/dagou', 'NOQUOTA', 'Free789Bird');
INSERT INTO `vpopmail` VALUES ('zhanghaitao', 'tutu-mobile.com', '$1$I2krMTYl$ZesJqmbcHSUsmgtcIO.bN1', '0', '0', 'ZhangHaiTao', '/home/vpopmail/domains/tutu-mobile.com/zhanghaitao', 'NOQUOTA', 'sarala');
INSERT INTO `vpopmail` VALUES ('lvwei', 'tutu-mobile.com', '$1$.PDk9adz$EzwEATXgSy0YjHqPUw.r/0', '0', '0', 'LvWei', '/home/vpopmail/domains/tutu-mobile.com/lvwei', 'NOQUOTA', 'Yuni810313');
INSERT INTO `vpopmail` VALUES ('lixia', 'sothink.com', '$1$Eu27z0mW$laCx7rvdJ1bsU45pRgugk1', '0', '0', 'lixia', '/home/vpopmail/domains/sothink.com/lixia', 'NOQUOTA', 'hj7vcp9');
INSERT INTO `vpopmail` VALUES ('liangy', 'sothink.com', '$1$i0Pxx0GP$Q5ydsERepw2deY3fFSozw1', '0', '0', 'liangy@sothink.com', '/home/vpopmail/domains/sothink.com/liangy', 'NOQUOTA', 'cindyrae1982');
INSERT INTO `vpopmail` VALUES ('lijing', 'tutu-mobile.com', '$1$bisZty4N$h9a1dItMKkD3ZyP716siu.', '0', '0', 'LiJIng', '/home/vpopmail/domains/tutu-mobile.com/lijing', 'NOQUOTA', 'Li009900');
INSERT INTO `vpopmail` VALUES ('xiongxin', 'tutu-mobile.com', '$1$Fz1t6w3t$WjC6iHYdXTKC9ZlhNi5Ir1', '0', '0', 'XiongXin', '/home/vpopmail/domains/tutu-mobile.com/xiongxin', 'NOQUOTA', 'xiongxin');
INSERT INTO `vpopmail` VALUES ('wangying', 'tutu-mobile.com', '$1$MSUHsL54$ihcZV./LlroKN5vjmSqOh0', '0', '0', 'WangYing', '/home/vpopmail/domains/tutu-mobile.com/wangying', 'NOQUOTA', 'Wy47825233');
INSERT INTO `vpopmail` VALUES ('postmaster', 'photo-album-maker.com', '$1$Qp5zyhsn$1krhfKuv/OmCNrkbnAR2f1', '0', '0', 'Postmaster', '/home/vpopmail/domains/photo-album-maker.com/postmaster', 'NOQUOTA', 'Jan20Year08');
INSERT INTO `vpopmail` VALUES ('tracy', 'sothink.com', '$1$ihf21Jy5$BHztqtI90ATJBw1fSceY3/', '0', '0', 'tracy@sothink.com', '/home/vpopmail/domains/sothink.com/tracy', 'NOQUOTA', 'Cjmd123');
INSERT INTO `vpopmail` VALUES ('dengyang', 'tutu-mobile.com', '$1$l8Se2eiY$4.dZStlZyM13njD9Yj2fy/', '0', '0', 'dengyang', '/home/vpopmail/domains/tutu-mobile.com/dengyang', 'NOQUOTA', 'asd4547');
INSERT INTO `vpopmail` VALUES ('wuhuijun', 'tutu-mobile.com', '$1$5xEEkyJy$1sR7FlVkSbvCcfkNkAXcY/', '0', '0', 'WuHuiJun', '/home/vpopmail/domains/tutu-mobile.com/wuhuijun', 'NOQUOTA', 'copy8dir');
INSERT INTO `vpopmail` VALUES ('submit', 'photo-album-maker.com', '$1$0HmtnIzC$I2CTPAegompBv3DrHoGiJ1', '0', '0', 'submit@photo-album-maker.com', '/home/vpopmail/domains/photo-album-maker.com/submit', 'NOQUOTA', 'mEIvDMs');
INSERT INTO `vpopmail` VALUES ('support', 'srctec.com', '$1$CCy9zPTk$oJoGKtsXucWNVbDPpFnN00', '0', '0', 'Support', '/home/vpopmail/domains/srctec.com/support', 'NOQUOTA', 'b31AK4702');
INSERT INTO `vpopmail` VALUES ('newsletter', 'srctec.com', '$1$jmFwtjFp$I0i1nebB6rV1tD6yHRtX71', '0', '0', 'newsletter', '/home/vpopmail/domains/srctec.com/newsletter', 'NOQUOTA', 'De97wan410');
INSERT INTO `vpopmail` VALUES ('postmaster', 'movie-burners.com', '$1$bbWqbfMR$gOZ6w3CGnjV4s55tPuMFa/', '0', '0', 'Postmaster', '/home/vpopmail/domains/movie-burners.com/postmaster', 'NOQUOTA', 'Jan20Year08');
INSERT INTO `vpopmail` VALUES ('zhanghong', 'jofgame.com', '$1$E2GiiwI3$e/CU2pZhTNOQpIxkZdvid0', '0', '0', 'zhanghong@jofgame.com', '/home/vpopmail/domains/jofgame.com/zhanghong', 'NOQUOTA', '0306942');
INSERT INTO `vpopmail` VALUES ('support', 'photo-album-maker.com', '$1$jCnfUgTg$./N8Ti.BEE7qiLX8zotoe1', '0', '0', 'support', '/home/vpopmail/domains/photo-album-maker.com/support', 'NOQUOTA', 'y3nZSnBq');
INSERT INTO `vpopmail` VALUES ('zhanghong', 'sothink.com.cn', '$1$daySfd9/$uJRrQ.k/w3ByUFG9sOiAK1', '0', '0', 'zhanghong', '/home/vpopmail/domains/sothink.com.cn/zhanghong', 'NOQUOTA', '0306942');
INSERT INTO `vpopmail` VALUES ('postmaster', 'sothinkmedia.com', '$1$6cVuBiJ/$V5Iu34aQylFvnHdz/8MkI/', '0', '0', 'Postmaster', '/home/vpopmail/domains/sothinkmedia.com/postmaster', 'NOQUOTA', 'Jan20Year08');
INSERT INTO `vpopmail` VALUES ('marketing', 'tutu-mobile.com', '$1$gZiliQhb$rIwHVgDjPcOHH8TZDVMf8.', '0', '0', 'Marketing', '/home/vpopmail/domains/tutu-mobile.com/marketing', 'NOQUOTA', 'Free789Bird');
INSERT INTO `vpopmail` VALUES ('postmaster', 'dhtml-menu-builder.com', '$1$cMRJyJDT$GvnuALFMl.zHwCg5S.1jf.', '0', '0', 'Postmaster', '/home/vpopmail/domains/dhtml-menu-builder.com/postmaster', 'NOQUOTA', 'Jan20Year08');
INSERT INTO `vpopmail` VALUES ('marketing', 'swf-decompiler.com', '$1$seYnx2Iz$m6kNtmiLV2TCWX1dAym5a0', '0', '0', 'marketing@swf-decompiler.com', '/home/vpopmail/domains/swf-decompiler.com/marketing', 'NOQUOTA', 'dvhy8u6');
INSERT INTO `vpopmail` VALUES ('postmaster', 'swf-decompiler.com', '$1$wt0xxFkW$jBms/TuHuAKmzF8Xl5Zc60', '0', '0', 'Postmaster', '/home/vpopmail/domains/swf-decompiler.com/postmaster', 'NOQUOTA', 'Jan20Year08');
INSERT INTO `vpopmail` VALUES ('germany', 'sothink.com', '$1$tUFc71ty$uuHsrRnAfZh4mKq/AjZAd0', '0', '0', 'germany', '/home/vpopmail/domains/sothink.com/germany', 'NOQUOTA', 'rt57jnmw');
INSERT INTO `vpopmail` VALUES ('wangjin', 'sothink.com', '$1$falbMWHw$tqrUTxI8Y5HTI4Q/sa0AQ.', '0', '0', 'wangjin@sothink.com', '/home/vpopmail/domains/sothink.com/wangjin', 'NOQUOTA', '240630');
INSERT INTO `vpopmail` VALUES ('marketing', 'flash-animation-maker.com', '$1$/FBEN0yb$0uaWDLR42NCDEiIltEU98/', '0', '0', 'marketing', '/home/vpopmail/domains/flash-animation-maker.com/marketing', 'NOQUOTA', '47jncdw');
INSERT INTO `vpopmail` VALUES ('yuwei', 'jofgame.com', '$1$CjWmjRUK$PuY5S4vaErkVm6B//uNAu0', '0', '0', 'yuwei', '/home/vpopmail/domains/jofgame.com/yuwei', 'NOQUOTA', 'aptech');
INSERT INTO `vpopmail` VALUES ('marketing', 'swf-to-fla.com', '$1$OSHxq4aV$FNMOLaSsX5.U3kjfkYUpj0', '0', '0', 'marketing@swf-to-fla.com', '/home/vpopmail/domains/swf-to-fla.com/marketing', 'NOQUOTA', 'dsdffrg');
INSERT INTO `vpopmail` VALUES ('postmaster', 'logo-maker.net', '$1$ziDwQkLT$nMX0Lnep0l56zYrDHjZ6o/', '0', '0', 'Postmaster', '/home/vpopmail/domains/logo-maker.net/postmaster', 'NOQUOTA', 'Jan20Year08');
INSERT INTO `vpopmail` VALUES ('submit', 'myconverters.com', '$1$qzcFcknc$kcgEvViRFwNGCVKLkseqV1', '0', '0', 'submit@myconverters.com', '/home/vpopmail/domains/myconverters.com/submit', 'NOQUOTA', 'ghjcdwr6');
INSERT INTO `vpopmail` VALUES ('marketinglinks', 'sothink.com', '$1$mGyQVJQF$eD76XqfMa2/XhLThxSUij0', '0', '0', 'marketinglinks', '/home/vpopmail/domains/sothink.com/marketinglinks', 'NOQUOTA', 'VchS5xPU');
INSERT INTO `vpopmail` VALUES ('marketing', 'dhtml-menu-builder.com', '$1$OE5KdqOo$yBLYM1K4B6i3XqH2ldIJn1', '0', '0', 'marketing', '/home/vpopmail/domains/dhtml-menu-builder.com/marketing', 'NOQUOTA', 'e4kopbg');
INSERT INTO `vpopmail` VALUES ('yuwei', 'sothink.com', '$1$EuaXeMxo$2LODJ1hA7sy5m3fNBDQ9k0', '0', '0', 'yuwei', '/home/vpopmail/domains/sothink.com/yuwei', 'NOQUOTA', 'aptech');
INSERT INTO `vpopmail` VALUES ('xujing', 'sothink.com', '$1$DLqCMBV4$tXI25tqxO2yX3fRg5f8qJ.', '0', '0', 'XuJing', '/home/vpopmail/domains/sothink.com/xujing', 'NOQUOTA', 'cellox2008');
INSERT INTO `vpopmail` VALUES ('zhanghong', 'srctec.com', '$1$a2PDyg.F$RuSYKA6ml7Jf.bj9suVuw1', '0', '0', 'zhanghong', '/home/vpopmail/domains/srctec.com/zhanghong', 'NOQUOTA', '0306942');
INSERT INTO `vpopmail` VALUES ('test', 'sothinkmedia.com', '$1$BBkCnkB5$gkZd5N8b2mQWyv5rhhQAt/', '0', '0', 'test@sothinkmedia.com', '/home/vpopmail/domains/sothinkmedia.com/test', 'NOQUOTA', 'otrs_2008$%');
INSERT INTO `vpopmail` VALUES ('qyt', 'sothink.com', '$1$xRZ/OdYU$Vy/uxoz81PBQSInbdhREj0', '0', '0', 'QiaoYuTing', '/home/vpopmail/domains/sothink.com/qyt', 'NOQUOTA', 'Zxc_789%');
INSERT INTO `vpopmail` VALUES ('webmaster', 'sothinkmedia.com', '$1$ntioYHwR$7GLEO5freH1f5XYB7dRjh1', '0', '0', 'webmaster@sothinkmedia.com', '/home/vpopmail/domains/sothinkmedia.com/webmaster', 'NOQUOTA', 'Just4issw');
INSERT INTO `vpopmail` VALUES ('marketing', 'sothinkmedia.com', '$1$pcSR6VPq$N0b9anadqJOT3dnel3m1p/', '0', '0', 'marketing@sothinkmedia.com', '/home/vpopmail/domains/sothinkmedia.com/marketing', 'NOQUOTA', 'bh2B704');
INSERT INTO `vpopmail` VALUES ('support', 'sothinkmedia.com', '$1$vQRQka7I$j79saZ1j8zx7ZFH6N1.es1', '0', '0', 'support@sothinkmedia.com', '/home/vpopmail/domains/sothinkmedia.com/support', 'NOQUOTA', 'Just4issw');
INSERT INTO `vpopmail` VALUES ('zhanghong', 'sothinkmedia.com', '$1$n/p3cIeL$qI54PB6EMCzZIr7NfrKrf0', '0', '0', 'zhanghong@sothinkmedia.com', '/home/vpopmail/domains/sothinkmedia.com/zhanghong', 'NOQUOTA', '0306942');
INSERT INTO `vpopmail` VALUES ('postmaster', 'flash-video-converters.com', '$1$74wOBtyQ$xHeYmRrgUQRNVnlp8KQhn1', '0', '0', 'Postmaster', '/home/vpopmail/domains/flash-video-converters.com/postmaster', 'NOQUOTA', 'Jan20Year08');
INSERT INTO `vpopmail` VALUES ('support', 'flashpioneer.com', '$1$6N7yTu3Y$AEJ4rjfnLqiKvTkCLhULM1', '0', '0', 'support@flashpioneer.com', '/home/vpopmail/domains/flashpioneer.com/support', 'NOQUOTA', 'Blaze365Cup');
INSERT INTO `vpopmail` VALUES ('webmaster', 'flashpioneer.com', '$1$VZQq6xE4$VyXgx4tpwerynnY.l6d3Z.', '0', '0', 'webmaster@flashpioneer.com', '/home/vpopmail/domains/flashpioneer.com/webmaster', 'NOQUOTA', 'Blaze365Cup');
INSERT INTO `vpopmail` VALUES ('marketing', 'flashpioneer.com', '$1$Bb4Ak0ma$cYzG20LVvsk10P1EJjc3J/', '0', '0', 'marketing@flashpioneer.com', '/home/vpopmail/domains/flashpioneer.com/marketing', 'NOQUOTA', 'Blaze365Cup');
INSERT INTO `vpopmail` VALUES ('order', 'flashpioneer.com', '$1$RGexzrE/$L135xDQnELyqlQMBIkwjI.', '0', '0', 'order@flashpioneer.com', '/home/vpopmail/domains/flashpioneer.com/order', 'NOQUOTA', 'Blaze365Cup');
INSERT INTO `vpopmail` VALUES ('zhanghong', 'flashpioneer.com', '$1$7QMvAujB$qfT1XHp1czzqHocb/X/q60', '0', '0', 'zhanghong@flashpioneer.como', '/home/vpopmail/domains/flashpioneer.com/zhanghong', 'NOQUOTA', '0306942');
INSERT INTO `vpopmail` VALUES ('submit', 'movie-burners.com', '$1$f8Vv2gig$BeUakSSvHMSi1ws2WeBOf/', '0', '0', 'submit@movie-burners.com', '/home/vpopmail/domains/movie-burners.com/submit', 'NOQUOTA', 'kuUJ4JpL');
INSERT INTO `vpopmail` VALUES ('xbp', 'tutu-mobile.com', '$1$rEancQ17$qtlauiawMY4IveuLAiYK51', '0', '0', 'xiaobaoping', '/home/vpopmail/domains/tutu-mobile.com/xbp', 'NOQUOTA', '434081');
INSERT INTO `vpopmail` VALUES ('noelle', 'sothink.com', '$1$E5qPqDaD$/2ohwlVfg9pe7cBLJSURf1', '0', '0', 'liuya', '/home/vpopmail/domains/sothink.com/noelle', 'NOQUOTA', 'n1o2e319450915');
INSERT INTO `vpopmail` VALUES ('wln', 'sothink.com', '$1$TXGXAZCT$CRkOhflbzNpYlQdX/sv8x1', '0', '0', 'wanglina', '/home/vpopmail/domains/sothink.com/wln', 'NOQUOTA', 'edna82');
INSERT INTO `vpopmail` VALUES ('joey', 'sothink.com', '$1$c58PXNg8$6fVtRqSGrU8Bjt/p/WIaS1', '0', '0', 'dengqiuting', '/home/vpopmail/domains/sothink.com/joey', 'NOQUOTA', 'joeydeng');
INSERT INTO `vpopmail` VALUES ('stefan_bo', 'sothink.com', '$1$RkvKp7fZ$dbKZZ6BVB8k2hEw6dUbV60', '0', '0', 'stefan_bo', '/home/vpopmail/domains/sothink.com/stefan_bo', 'NOQUOTA', '19880103');
INSERT INTO `vpopmail` VALUES ('postmaster', 'freetodown.com', '$1$BBrRqMtc$YCgz91yG2bUpao1hg6X1b1', '0', '0', 'Postmaster', '/home/vpopmail/domains/freetodown.com/postmaster', 'NOQUOTA', 'Jan20Year08');
INSERT INTO `vpopmail` VALUES ('sourcetec-jofsoft', 'sothink.com', '$1$LlUwMmVz$yJcOLbLYWkj4WUkEw9ZdS.', '0', '0', 'sourcetec-jofsoft', '/home/vpopmail/domains/sothink.com/sourcetec-jofsoft', 'NOQUOTA', 'sothink123456');
INSERT INTO `vpopmail` VALUES ('lzq', 'sothink.com', '$1$vBeksYaZ$0RcsWZFd0opOy3c.SupAi/', '0', '0', 'lzq@sothink.com', '/home/vpopmail/domains/sothink.com/lzq', 'NOQUOTA', 'lzq888');
INSERT INTO `vpopmail` VALUES ('itservice', 'sothink.com', '$1$dteZGf8Y$0vmwk.AQbih0WMq06CR6X1', '0', '0', 'itservice', '/home/vpopmail/domains/sothink.com/itservice', 'NOQUOTA', 'itservice');
INSERT INTO `vpopmail` VALUES ('wuhongyun', 'tutu-mobile.com', '$1$Rris5t5n$IZGcMw.GiHcNl76KEYJyW/', '0', '0', 'wuhongyun', '/home/vpopmail/domains/tutu-mobile.com/wuhongyun', 'NOQUOTA', 'qwert12366');
INSERT INTO `vpopmail` VALUES ('aili', 'sothink.com', '$1$7y6DyIpn$EJRFG8vTOX2PJ7BXoRotv1', '0', '0', 'aili@sothink.com', '/home/vpopmail/domains/sothink.com/aili', 'NOQUOTA', 'kuUJ4JpL');
INSERT INTO `vpopmail` VALUES ('wk', 'tutu-mobile.com', '$1$mY3U43uk$A1xRqqkj/jxHnqIzoCNG./', '0', '0', 'wangkai', '/home/vpopmail/domains/tutu-mobile.com/wk', 'NOQUOTA', 'wk7788');
INSERT INTO `vpopmail` VALUES ('gzp', 'sothink.com', '$1$kFb9d190$fgPuzdiLzIrv6.L6tcmCz/', '0', '0', 'gaozhenpin', '/home/vpopmail/domains/sothink.com/gzp', 'NOQUOTA', '1981');
INSERT INTO `vpopmail` VALUES ('webmis', 'sothink.com', '$1$6rLCPbE4$LrONsc1dmLtv1qfoMGzIl.', '0', '0', 'webmis', '/home/vpopmail/domains/sothink.com/webmis', 'NOQUOTA', 'Apr414');
INSERT INTO `vpopmail` VALUES ('iguodong', 'tutu-mobile.com', '$1$v/SkapUJ$WEQhIfy89OuPh0kMAVFGH.', '0', '0', 'iguodong@tutu-mobile.com', '/home/vpopmail/domains/tutu-mobile.com/iguodong', 'NOQUOTA', 'fNU8GRi9');
INSERT INTO `vpopmail` VALUES ('tas', 'tutu-mobile.com', '$1$SsAggyxC$xVXHGKZXR4gw/EKGj5SDy/', '0', '0', 'TAS', '/home/vpopmail/domains/tutu-mobile.com/tas', 'NOQUOTA', 'tutu123456');
INSERT INTO `vpopmail` VALUES ('caimaowei', 'jofgame.com', '$1$N6H9cYpq$ttCZCR79Ct72wsxjk.MuR1', '0', '0', 'caimaowei@jofgame.com', '/home/vpopmail/domains/jofgame.com/caimaowei', 'NOQUOTA', 'dfdeg6ac');
INSERT INTO `vpopmail` VALUES ('czy', 'sothink.com', '$1$TL7E1cRs$YG4.5vLxFxJBXw3VtoAs80', '0', '0', 'czy@sothink.com', '/home/vpopmail/domains/sothink.com/czy', 'NOQUOTA', 'dgtE#5&k');
INSERT INTO `vpopmail` VALUES ('submit', 'flash-video-converters.com', '$1$khvDqZzc$EU94aCOMVEE.D1061uXNZ0', '0', '0', 'submit@flash-video-converters.com', '/home/vpopmail/domains/flash-video-converters.com/submit', 'NOQUOTA', 'hUh3SQu');
INSERT INTO `vpopmail` VALUES ('ccw', 'tutu-mobile.com', '$1$mQu0hyU6$y5inxVTH3F3..2GKv3G6Z/', '0', '0', 'chengchuanwu', '/home/vpopmail/domains/tutu-mobile.com/ccw', 'NOQUOTA', '798597039');
INSERT INTO `vpopmail` VALUES ('postmaster', 'swf-to-fla.com', '$1$4vFRllEX$mxWVxvWoxp9uVdosmwKid1', '0', '0', 'Postmaster', '/home/vpopmail/domains/swf-to-fla.com/postmaster', 'NOQUOTA', 'Jan20Year08');
INSERT INTO `vpopmail` VALUES ('xier_zhu', 'sothink.com', '$1$Aq7nuaiX$Vk8dDxaDaEs1OQn48WJWN.', '0', '0', 'zhuxi', '/home/vpopmail/domains/sothink.com/xier_zhu', 'NOQUOTA', 'kuU$4JpL');
INSERT INTO `vpopmail` VALUES ('support', 'freetodown.com', '$1$JgQnj5v0$gs0VeOD1xbqP949NM8zGP0', '0', '0', 'support@freetodown.com', '/home/vpopmail/domains/freetodown.com/support', 'NOQUOTA', 'src8351');
INSERT INTO `vpopmail` VALUES ('advertise', 'freetodown.com', '$1$akA5Cb8e$z5/UndhI/FYVPfxBjohUK0', '0', '0', 'advertise@freetodown.com', '/home/vpopmail/domains/freetodown.com/advertise', 'NOQUOTA', 'src8351');
INSERT INTO `vpopmail` VALUES ('submit', 'freetodown.com', '$1$2XMPMYjm$O.5kIMdYqJzgP9b39BD3V1', '0', '0', 'submit@freetodown.com', '/home/vpopmail/domains/freetodown.com/submit', 'NOQUOTA', 'src8351');
INSERT INTO `vpopmail` VALUES ('liuyulu', 'sothink.com', '$1$WjD0Cmhq$WfVY.zaX15ukYL5oqA9FH/', '0', '0', 'liuyulu', '/home/vpopmail/domains/sothink.com/liuyulu', 'NOQUOTA', 'mao820527');
INSERT INTO `vpopmail` VALUES ('serveradmin', 'sothink.com', '$1$3n7MA98G$L7Znqlm6HeEowgcxUocbK1', '0', '0', 'serveradmin@sothink.com', '/home/vpopmail/domains/sothink.com/serveradmin', 'NOQUOTA', '1q2w3e?');
INSERT INTO `vpopmail` VALUES ('zhanghongtheplanet', 'sothink.com', '$1$s0pzVB0E$ktw70rjsO1mAkJdR2V1IX/', '0', '0', 'zhanghongtheplanet@sothink.com', '/home/vpopmail/domains/sothink.com/zhanghongtheplanet', 'NOQUOTA', '0306942');
INSERT INTO `vpopmail` VALUES ('misapp', 'sothink.com', '$1$zTS5jfqC$d9hvCf8ky/otTChkz3R1c1', '0', '0', 'misapp@sothink.com', '/home/vpopmail/domains/sothink.com/misapp', 'NOQUOTA', 'ac1kAx9');
INSERT INTO `vpopmail` VALUES ('marketing2', 'sothinkmedia.com', '$1$cuVpEY0i$EMDkP.Qp1.ZYB/KlfEx0/1', '0', '0', 'marketing2@sothinkmedia.com', '/home/vpopmail/domains/sothinkmedia.com/marketing2', 'NOQUOTA', 'HOUEzjBG');
INSERT INTO `vpopmail` VALUES ('bzk', 'tutu-mobile.com', '$1$Qie4BqTz$7S7hKQ5eJ1GZa6ZB8kGQo0', '0', '0', 'bizhikuiÍøÕ¾ÓÃµÄÕÊºÅ', '/home/vpopmail/domains/tutu-mobile.com/bzk', 'NOQUOTA', 'gh5#%G8X$');
INSERT INTO `vpopmail` VALUES ('duhuan', 'sothink.com', '$1$19EAdSos$LmWhgoCnFH.o38VEaAoL41', '0', '0', '¶Å»¶', '/home/vpopmail/domains/sothink.com/duhuan', '104857600S', 'hch812928');
INSERT INTO `vpopmail` VALUES ('monitor', 'sothink.com', '$1$DR7gFYSG$QjCKfVmHIh2pmiS.yd04t1', '0', '0', '·þÎñÆ÷¼à¿Ø', '/home/vpopmail/domains/sothink.com/monitor', 'NOQUOTA', '0306942');
INSERT INTO `vpopmail` VALUES ('logomaker', 'sothink.com', '$1$qbHsWD.6$ct.YvSqs3ZR0r0UzHbTwp.', '0', '0', 'logomaker@sothink.com', '/home/vpopmail/domains/sothink.com/logomaker', 'NOQUOTA', 'fdfdr6hmh');
INSERT INTO `vpopmail` VALUES ('flashbanner', 'sothink.com', '$1$/3luruNe$kjm9HbQPnXszbFU7R9p0S.', '0', '0', 'flashbanner@sothink.com', '/home/vpopmail/domains/sothink.com/flashbanner', 'NOQUOTA', 'dfdw3cxa6');
INSERT INTO `vpopmail` VALUES ('brenda', 'sothink.com', '$1$UX/VJyXL$uF569z1L2WhdHDhdFuyvG1', '0', '0', 'hudan', '/home/vpopmail/domains/sothink.com/brenda', 'NOQUOTA', 'df56y64f');
INSERT INTO `vpopmail` VALUES ('annie', 'sothink.com', '$1$6v3qN8.5$RgibeJnrNiTdFP4lXR8BU.', '0', '0', 'liangyan', '/home/vpopmail/domains/sothink.com/annie', 'NOQUOTA', '061123yy');
INSERT INTO `vpopmail` VALUES ('eva', 'sothink.com', '$1$/b8M6d6z$vDc4MfjwYdzFU4B2zLJgl/', '0', '0', 'zhoujinxia', '/home/vpopmail/domains/sothink.com/eva', 'NOQUOTA', 'dfdsf9r');
INSERT INTO `vpopmail` VALUES ('socialnetwork', 'sothink.com', '$1$3T7jf.jB$96XoKZOl8g/7IU4XVEEDM1', '0', '0', 'socialnetwork@sothink.com', '/home/vpopmail/domains/sothink.com/socialnetwork', 'NOQUOTA', 'ft$h&#D6');
INSERT INTO `vpopmail` VALUES ('partner', 'sothink.com', '$1$yb22ImPP$YTaAnyQpN5ewJKZG2y/ml.', '0', '0', 'partner@sothink.com', '/home/vpopmail/domains/sothink.com/partner', 'NOQUOTA', 'ghgjuo866k');
INSERT INTO `vpopmail` VALUES ('rital', 'sothink.com', '$1$gy/990.i$vjIPiN0qg8e0aUYfeIaN60', '0', '0', 'luoyizhi', '/home/vpopmail/domains/sothink.com/rital', 'NOQUOTA', 'df*hy6tG');
INSERT INTO `vpopmail` VALUES ('viki', 'sothink.com', '$1$w4v3Ttbi$DBB4GR/ezYl0YjoEzRq13.', '0', '0', 'huguangyan', '/home/vpopmail/domains/sothink.com/viki', 'NOQUOTA', 'yy411700');
INSERT INTO `vpopmail` VALUES ('weeebstore', 'sothink.com', '$1$d5x/dhmu$DeUMWBD29I6DPUjqenmDP/', '0', '0', 'weeebstore@sothink.com', '/home/vpopmail/domains/sothink.com/weeebstore', 'NOQUOTA', 'dfht67k');
INSERT INTO `vpopmail` VALUES ('postmaster', 'jofgame.com', '$1$b4clJQk3$QSQLXTxEp4DtTqAsBVXiK.', '0', '0', 'Postmaster', '/home/vpopmail/domains/jofgame.com/postmaster', 'NOQUOTA', 'Jan20Year08');
INSERT INTO `vpopmail` VALUES ('lili', 'jofgame.com', '$1$jPHgJq6b$Qa8cknU8J1LhY1YORRD35/', '0', '0', 'lili@jofgame.com', '/home/vpopmail/domains/jofgame.com/lili', 'NOQUOTA', 'ghjcdwr6');
INSERT INTO `vpopmail` VALUES ('zhanghaitao', 'jofgame.com', '$1$s.6WKp7o$NyLvtOzbQzGdQYQZGWXMQ/', '0', '0', 'zhanghaitao@jofgame.com', '/home/vpopmail/domains/jofgame.com/zhanghaitao', 'NOQUOTA', 'Seacat77');
INSERT INTO `vpopmail` VALUES ('wuhuijun', 'jofgame.com', '$1$ISYMkm8M$PYFOPywMULpY1sFrR0qMw.', '0', '0', 'wuhuijun@jofgame.com', '/home/vpopmail/domains/jofgame.com/wuhuijun', 'NOQUOTA', 'dfdw3ca6');
INSERT INTO `vpopmail` VALUES ('chengchuanwu', 'jofgame.com', '$1$nd544p3U$aQ7ehDeix9/Xu.LQTN9.D/', '0', '0', 'chengchuanwu@jofgame.com', '/home/vpopmail/domains/jofgame.com/chengchuanwu', 'NOQUOTA', 'kuUJ4JpL');
INSERT INTO `vpopmail` VALUES ('xujing', 'jofgame.com', '$1$t15v41LB$ILbE9nh2eRM4rF6/CkUUp1', '0', '0', 'xujing@jofgame.com', '/home/vpopmail/domains/jofgame.com/xujing', 'NOQUOTA', 'mEIvDMsr');
INSERT INTO `vpopmail` VALUES ('kefu', 'jofgame.com', '$1$YCVsN3Ec$/z4QA2pQLHmAh5MBV1z5F/', '0', '0', 'kefu@jofgame.com', '/home/vpopmail/domains/jofgame.com/kefu', 'NOQUOTA', 'fNu8GRi9');
INSERT INTO `vpopmail` VALUES ('chenyan', 'jofgame.com', '$1$O5SCVcvK$c68pSzqq.Xv7OA4PpyS/j.', '0', '0', 'chenyan@jofgame.com', '/home/vpopmail/domains/jofgame.com/chenyan', 'NOQUOTA', 'y3nZSnBq');
INSERT INTO `vpopmail` VALUES ('yangyi', 'jofgame.com', '$1$e0WMSMFf$J3g5yViLG7L9BWSgFlBar.', '0', '0', 'yangyi@jofgame.com', '/home/vpopmail/domains/jofgame.com/yangyi', 'NOQUOTA', 'mEIvDMse');
INSERT INTO `vpopmail` VALUES ('weeebstore2', 'sothink.com', '$1$8S2y0NRc$QC.8zr8w9rkVZnqAkrWBn1', '0', '0', 'weeebstore2', '/home/vpopmail/domains/sothink.com/weeebstore2', 'NOQUOTA', 'dft78k9');
INSERT INTO `vpopmail` VALUES ('marketing', 'logo-maker.net', '$1$XjdT8Bhj$aAh59FGYoVW/9GCcnGuO90', '0', '0', 'marketing@logo-maker.net', '/home/vpopmail/domains/logo-maker.net/marketing', 'NOQUOTA', 'dfdw3cd3');
INSERT INTO `vpopmail` VALUES ('wuhongyun', 'jofgame.com', '$1$uFjsXcCh$kWGcSNYL3IhFrMvodASgX.', '0', '0', 'wuhongyun@jofgame.com', '/home/vpopmail/domains/jofgame.com/wuhongyun', 'NOQUOTA', 'wuhy12366');
INSERT INTO `vpopmail` VALUES ('zjx', 'sothink.com', '$1$/0an8vP6$U1KhtqXp2Vsrm/3hgxvQ61', '0', '0', 'zjx', '/home/vpopmail/domains/sothink.com/zjx', 'NOQUOTA', 'qqqqqq');
INSERT INTO `vpopmail` VALUES ('ts', 'jofgame.com', '$1$cPsFSV.9$BdnJU25CVkZdKJ6Y9PYqR.', '0', '0', 'ts@jofgame.com', '/home/vpopmail/domains/jofgame.com/ts', 'NOQUOTA', 'fggj%&k9');
INSERT INTO `vpopmail` VALUES ('liwenjing', 'jofgame.com', '$1$bOqQIKHl$LQ1dSHpakW.fAUu71U4AU1', '0', '0', 'liwenjing@jofgame.com', '/home/vpopmail/domains/jofgame.com/liwenjing', 'NOQUOTA', 'mEIvDswh');
INSERT INTO `vpopmail` VALUES ('zhanghongtest', 'sothink.com', '$1$HSt6r.b3$vYIVojFgUUwChRh8Y1GC.1', '0', '0', 'zhanghongtest@sothink.com', '/home/vpopmail/domains/sothink.com/zhanghongtest', 'NOQUOTA', '0306942');
INSERT INTO `vpopmail` VALUES ('cindyliang', 'sothink.com', '$1$THf2PSC3$63i4vgJJmY5QdLQAnXk0v/', '0', '0', 'cindyliang@sothink.com', '/home/vpopmail/domains/sothink.com/cindyliang', 'NOQUOTA', 'Just4issw');
INSERT INTO `vpopmail` VALUES ('kf', 'jofgame.com', '$1$2IgvG0f7$Ck48xhzxQUVXyl/EKOiIb0', '0', '0', 'kf@jofgame.com', '/home/vpopmail/domains/jofgame.com/kf', 'NOQUOTA', 'm5I$DMcs');
INSERT INTO `vpopmail` VALUES ('xielei', 'jofgame.com', '$1$vi.B56qm$J9nnJlCi0c4Ga1YdttaOD1', '0', '0', 'xielei', '/home/vpopmail/domains/jofgame.com/xielei', 'NOQUOTA', 'dfdw3cfd');
INSERT INTO `vpopmail` VALUES ('postmaster', 'mylogomaker.de', '$1$H3WrtMul$8kiGCiArO2e56.ozxtJiB.', '0', '0', 'Postmaster', '/home/vpopmail/domains/mylogomaker.de/postmaster', 'NOQUOTA', 'Jan20Year08');
INSERT INTO `vpopmail` VALUES ('marketing', 'mylogomaker.de', '$1$8B/bQpk0$mda4SWduNXNcpKiDQu1VV1', '0', '0', 'marketing', '/home/vpopmail/domains/mylogomaker.de/marketing', 'NOQUOTA', 'kuUJ4JpL');
INSERT INTO `vpopmail` VALUES ('postmaster', 'sothinkmedia.de', '$1$fM675hKK$npPplEUdkwpH9RCn4jKoJ.', '0', '0', 'Postmaster', '/home/vpopmail/domains/sothinkmedia.de/postmaster', 'NOQUOTA', 'Jan20Year08');
INSERT INTO `vpopmail` VALUES ('marketing', 'sothinkmedia.de', '$1$e/yE1Pby$NNeoUe8QTUlYV.r0MuxP8/', '0', '0', 'marketing', '/home/vpopmail/domains/sothinkmedia.de/marketing', 'NOQUOTA', 'de411700');
INSERT INTO `vpopmail` VALUES ('chenhao', 'jofgame.com', '$1$nte3fgXA$n3Z685t9dzEshl5T0q3xI0', '0', '0', 'chenhao', '/home/vpopmail/domains/jofgame.com/chenhao', 'NOQUOTA', 'd#fji%*lj');
INSERT INTO `vpopmail` VALUES ('zhangying', 'jofgame.com', '$1$iuT16OLW$.TjNffiPbbd3MRJDFElVC0', '0', '0', 'zhangying', '/home/vpopmail/domains/jofgame.com/zhangying', 'NOQUOTA', '0306942');
INSERT INTO `vpopmail` VALUES ('wangying', 'jofgame.com', '$1$HdmDgQDv$AV/A2zsi0eGhUm/5JtfnD/', '0', '0', 'wangying', '/home/vpopmail/domains/jofgame.com/wangying', 'NOQUOTA', 'hkuo76nc');
INSERT INTO `vpopmail` VALUES ('yowee', 'sothink.com', '$1$/bjrnTpt$xdzw/lCotvSH5fjY4DYyb.', '0', '0', 'yowee', '/home/vpopmail/domains/sothink.com/yowee', 'NOQUOTA', '521gr999');
INSERT INTO `vpopmail` VALUES ('hukun', 'jofgame.com', '$1$yZ1uEl.u$8EpUo9lX13Ch14RthJWQr.', '0', '0', 'hukun@jofgame.com', '/home/vpopmail/domains/jofgame.com/hukun', 'NOQUOTA', 'ghjcdwgt');
INSERT INTO `vpopmail` VALUES ('sarahye', 'sothink.com', '$1$UL46TVIV$aq4qsUt8SWgyzwtlWmJT9/', '0', '0', 'yelu', '/home/vpopmail/domains/sothink.com/sarahye', 'NOQUOTA', 'jdljie*7t');
INSERT INTO `vpopmail` VALUES ('postmaster', 'flash-to-html5.net', '$1$G4JvMNqB$s0ThZqeZ3Pct5klmEOrKE0', '0', '0', 'Postmaster', '/home/vpopmail/domains/flash-to-html5.net/postmaster', 'NOQUOTA', 'Jan20Year08');
INSERT INTO `vpopmail` VALUES ('marketing', 'flash-to-html5.net', '$1$VVAgMsQ8$W01SziXl4A/FdLqokXfkI1', '0', '0', 'marketing', '/home/vpopmail/domains/flash-to-html5.net/marketing', 'NOQUOTA', 'df*hy6tB');
INSERT INTO `vpopmail` VALUES ('postmaster', 'logo-creator.net', '$1$vB9uWeI5$/bfBUP6s4pg7l/PQgOkfp1', '0', '0', 'Postmaster', '/home/vpopmail/domains/logo-creator.net/postmaster', 'NOQUOTA', 'Jan20Year08');
INSERT INTO `vpopmail` VALUES ('marketing', 'logo-creator.net', '$1$9T4sViC8$DAAsjR4nTfT/G4j2NbDsX1', '0', '0', 'marketing', '/home/vpopmail/domains/logo-creator.net/marketing', 'NOQUOTA', 'ec*hy6$B');
INSERT INTO `vpopmail` VALUES ('backlink', 'sothink.com', '$1$.dHcuROD$mi7lGSoGcyLJLnaL59yeO1', '0', '0', 'backlink', '/home/vpopmail/domains/sothink.com/backlink', 'NOQUOTA', 'YQ9&F8hW#');
