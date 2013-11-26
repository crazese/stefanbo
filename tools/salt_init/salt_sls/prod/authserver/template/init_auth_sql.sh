#!/bin/bash
mysql -u root -p123456 <<EOF
source /var/www/authserver/authServer/authserver.sql
source /var/www/authserver/authServer/append.sql

create user auth identified by '123456';
grant all on *.* to auth identified by '123456';
insert into ol_serverlist values ("","test","192.168.1.203","","1","","0","1","1.36");
quit
EOF