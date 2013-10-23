#/bin/bash
#add ubuntu source
deb http://ppa.launchpad.net/nginx/stable/ubuntu lucid main
apt-get update -y

#install nginx
apt-get install nginx -y

#install php5-fpm
aptitude install python-software-properties -y

apt-key adv --recv-keys --keyserver hkp://keyserver.ubuntu.com:80 A42227CB8D0DC64F

add-apt-repository ppa:brianmercer/php5

aptitude -y install php5-cli \
 		php5-common\
 		php5-mysql \
 		php5-suhosin \
 		php5-gd \
 		php5-mcrypt \
 		php5-fpm \
 		php5-cgi \
 		php-pear \
 		php5-curl \
 		php5-openssl \
 		php5-dev

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

#install extension
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


mv /etc/php5/fpm/php.ini /etc/php5/cli/php.ini.bak

vi /etc/php5/fpm/php.ini
extension=igbinary.so
extension=memcached.so




#unmark the "#"
sed -n 's/#/;/g' /etc/php5/fpm/conf.d/mcrypt.ini

/etc/nginx/sites-available/default
php5_default=
"""
#configure the nginx and php

server {
        listen   80 default;
        server_name  localhost;

        access_log  /var/log/nginx/localhost.access.log;

        location / {
                root   /var/www/html;
                index  index.php index.html index.htm;
        }

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
                fastcgi_param  SCRIPT_FILENAME  /var/www/html$fastcgi_script_name;
                include fastcgi_params;
        }
}
"""

#install memcache
pecl install memcache



/etc/nginx/sites-available/herouser
herouser=
"""
server {
	listen 81;
	root /var/www/herouser;
	index index.php index.html index.htm;

	server_name localhost;

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
		fastcgi_pass 127.0.0.1:9000;
		fastcgi_index index.php;
		fastcgi_param SCRIPT_FILENAME /var/www/herouser$fastcgi_script_name;
		include fastcgi_params;
	}
}

"""

ol_serverlist

