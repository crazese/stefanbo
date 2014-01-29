ALTER TABLE `ol_users` MODIFY COLUMN `sex`  varchar(1) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `userName2`;
update ol_users set sex = '';
ALTER TABLE `ol_users` ADD COLUMN `deviceid`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'ЛњЦїТы' AFTER `sex`;