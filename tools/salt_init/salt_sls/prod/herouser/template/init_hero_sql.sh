#!/bin/bash
mysql -u root -p123456 <<EOF
create database hero character set utf8;
create user hero identified by '123456';
grant all on *.* to hero identified by '123456';

use hero;
source /var/www/herouser/hero.sql;
source /var/www/herouser/append.sql;
source /var/www/herouser/ios_app_pay.sql;
source /var/www/herouser/pay.sql;
quit
EOF