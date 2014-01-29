name: web admin system (php)
version:1.0
author:shemily
website:www.oncecode.com
pub:2007-03-21
environment:
php5.2.0 + MySQL5.1.1 (session support)

----------------------------------------------------
介绍

这是一个网站的后台管理系统，
提供了可设置权限的帐号管理
和方便的后台管理页面菜单式设置，
可以方便地增加、修改后台管理的页面。

注：
使用环境：
PHP5.2.0 ＋ MySQL5.1.1
支持session
(目前大部分php虚拟主机并没有5.0以上，
MySQL也没有5.0以上
故旧版本不确保能正常使用)

-----------------------------------------------------
使用说明

1、解压后先配置Config/Config.inc文件中的MySQL
   或者MSSQL登陆帐号和密码；

2、设置系统菜单级别：
   只能设置为2或者3；

3、将所有文件上传至php服务器，
   运行Install/install.php进行数据库安装；

4、超级管理员帐号：admin
   密码：123456
   登陆页面：login.php

5、通过设置帐号的权限
   和后台菜单的访问权限，
   可以配置某类帐号的使用权限；