#/bin/bash

#add ubuntu source
echo "deb http://ppa.launchpad.net/nginx/stable/ubuntu lucid main" >> /etc/apt/sources.list
apt-get update

#add the key auth
apt-key adv --recv-keys --keyserver hkp://keyserver.ubuntu.com:80 00A6F0A3C300EE8C
apt-get update

###############################################################################
#install nginx
apt-get install nginx -y

#install mysql
apt-get install mysql-server -y


#install php5-fpm
aptitude install python-software-properties -y
add-apt-repository ppa:l-mierzwa/lucid-php5
aptitude update -y

aptitude -y install php5-cli php5-common php5-mysql php5-suhosin php5-gd php5-mcrypt php5-fpm php5-cgi php-pear php5-curl php5-openssl php5-dev

#install apc
cd /tmp
apt-get -y install libpcre3-dev
aptitude -y install php5-dev
wget http://pecl.php.net/get/APC-3.1.9.tgz
tar zxvf APC-3.1.9.tgz
cd APC-3.1.9
phpize
./configure --enable-apc --enable-apc-mmap  config=/usr/local/bin/php-config
make && make install

#install extension memcache
cd /tmp
apt-get -y install g++ libevent-dev libcloog-ppl-dev
wget ftp://ftpuser:5tgb6yhn@59.175.238.4/memcached-2.1.0.tgz
wget ftp://ftpuser:5tgb6yhn@59.175.238.4/libmemcached-1.0.10.tar.gz
wget ftp://ftpuser:5tgb6yhn@59.175.238.4/igbinary-igbinary-1.1.1-28-gc35d48f.zip


apt-get install unzip -y

unzip  igbinary-igbinary-1.1.1-28-gc35d48f.zip
cd igbinary-igbinary-c35d48f
phpize
./configure  --enable-igbinary config=/usr/local/bin/php-config
make 
make install
cd ..

tar zxvf libmemcached-1.0.10.tar.gz
cd libmemcached-1.0.10
./configure 
make && make install
cd ..


tar zxvf memcached-2.1.0.tgz
cd memcached-2.1.0
phpize
./configure  --enable-memcached --enable-memcached-igbinary config=/usr/local/bin/php-config
make && make install


cp /etc/php5/fpm/php.ini /etc/php5/cli/php.ini.bak

echo "extension=igbinary.so" >> /etc/php5/fpm/php.ini
echo "extension=memcached.so" >> /etc/php5/fpm/php.ini
echo "extension=apc.so" >> /etc/php5/fpm/php.ini
echo "apc.shm_size=250m" >> /etc/php5/fpm/php.ini
echo "cgi.fix_pathinfo = 0" >> /etc/php5/fpm/php.ini
echo "cgi.fix_pathinfo=1" >> /etc/php5/fpm/php.ini
###############################################################################

#unmark the "#"
sed -n 's/#/;/g' /etc/php5/fpm/conf.d/mcrypt.ini

#build directory
#mkdir -p /var/www/authserver  
mkdir -p /var/www/herouser

/etc/nginx/sites-available/default 
default_first=
"""
#configure the nginx and php

server {
        listen   80 default;
        server_name  localhost;

        access_log  /var/log/nginx/localhost.80.access.log;
        root   /var/www/authserver;
        index  index.php index.html index.htm;
        
        location /doc {
                root   /usr/share;
                autoindex on;
                allow 127.0.0.1;
                deny all;
        }

        location /images {
                root   /usr/share;
                autoindex off;
        }

        location ~ \.php$ {
                fastcgi_pass   127.0.0.1:9000;
                fastcgi_index  index.php;
                fastcgi_param  SCRIPT_FILENAME  /var/www/authserver$fastcgi_script_name;
                include fastcgi_params;
        }
}
"""


/etc/nginx/sites-available/herouser
herouser=
"""
server {
	listen 81;
	server_name localhost;

	access_log  /var/log/nginx/localhost.81.access.log;
	root /var/www/herouser;
	index index.php index.html index.htm;

	location /doc {
		root /usr/share;
		autoindex on;
		allow 127.0.0.1;
		deny all;
	}

	location /images {
		root /usr/share;
		autoindex off;
	}

	location ~ \.php$ {
		fastcgi_pass 	127.0.0.1:9000;
		fastcgi_index 	index.php;
		fastcgi_params	SCRIPT_FILENAME /var/www/herouser$fastcgi_script_name;
		include 		fastcgi_params;
	}
}

"""

ln -s /etc/nginx/sites-available/herouser /etc/nginx/sites-enabled/herouser



#install memcache
pecl install memcache<<EOF


EOF
apt-get install memcached

/usr/bin/memcached -m 64 -p 11211 -u nobody -l 127.0.0.1

#hero mysql init
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

#authserver mysql init
mysql -u root -p123456 <<EOF
source /var/www/authserver/authServer/authserver.sql
source /var/www/authserver/authServer/append.sql

create user auth identified by '123456';
grant all on *.* to auth identified by '123456';
insert into ol_serverlist values ("","test","192.168.1.203","","1","","0","1","1.36");
quit
EOF

scp -rp root@192.168.1.61:/var/key ./
#configure the config.php
#change the database user name
sed -ni 's/sothink/hero/g' /var/www/herouser/config.php 
#change the database user passwd
sed -ni 's/src8351/123456/g' /var/www/herouser/config.php 
#change the database name
sed -ni 's/136new/hero/g' /var/www/herouser/config.php 
#change the domain server ip address
sed -ni 's/119.79.232.99/192.168.1.203/' /var/www/herouser/config.php
#change the USER_SERVER (authserver ip)
sed -ni 's/192.168.1.61:8080/192.168.1.202:80/' /var/www/herouser/config.php


$_SC['domain'] = '192.168.1.202:81';
define('USER_SERVER', 'http://192.168.1.202:80/');
define('KEY_PATH', '/var/key');


mysql
ol_serverlist