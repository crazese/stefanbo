#!/bin/bash
# directory
mkdir -p /opt/lnmp/tar_package
mkdir -p /opt/lnmp/app/
cd /opt/lnmp/app
mkdir sbin lib 

export PATH=/opt/lnmp/app/sbin:$PATH
export LD_LIBRARY_PATH=/opt/lnmp/app/lib:$LD_LIBRARY_PATH 

cd /opt/lnmp/tar_package
cur_dir=$(pwd)
# ftp get
server="hut.jofgame.com"
username="zhubo"
passwd="Jof1Game8yzl"

cd /opt/lnmp/tar_package

ftp -n $server <<EOF
prompt off
user $username $passwd
cd lnmp
lcd /opt/lnmp/tar_package
mget * 
bye
EOF

# install gcc from apt
apt-get update
apt-get upgrade -y
apt-get install -y build-essential gcc g++ make

# init install from source 
init_install()
{
cd $cur_dir
tar -zxvf m4-1.4.9.tar.gz
cd m4-1.4.9
./configure --prefix=/opt/lnmp/app/m4 
make && make install
cd ../ 
ln -s /opt/lnmp/app/m4/bin/m4 /opt/lnmp/app/sbin/ 

cd $cur_dir
tar zxvf autoconf-2.13.tar.gz
cd autoconf-2.13/
./configure --prefix=/opt/lnmp/app/autoconf
make && make install
cd ../

cd $cur_dir
tar zxvf libiconv-1.14.tar.gz
cd libiconv-1.14/
./configure --prefix=/opt/lnmp/app/libiconv 
make && make install
cd ../

cd $cur_dir
tar zxvf libmcrypt-2.5.8.tar.gz
cd libmcrypt-2.5.8/
./configure --prefix=/opt/lnmp/app/libmcrypt
make && make install
/sbin/ldconfig
cd libltdl/
./configure --enable-ltdl-install
make && make install
cd ../../

cd $cur_dir
tar zxvf libxml2-2.7.8.tar.gz
cd libxml2-2.7.8/
./configure --prefix=/opt/lnmp/app/libxml2 
make && make install
cd ../
}
init_install

InstallNginx()
{
echo "============================Install Nginx================================="
groupadd www-data
useradd -s /sbin/nologin -g www-data www-data

mkdir -p /opt/lnmp/app/www/root
mkdir -p /opt/lnmp/app/www/logs
touch /opt/lnmp/app/www/logs/nginx_error.log
chown -R www-data.www-data /opt/lnmp/app/www 
chmod -R 755 /opt/lnmp/app/www 

# nginx
cd $cur_dir
tar -zxvf pcre-8.30.tar.gz
tar -zxvf openssl-1.0.1e.tar.gz
tar -zxvf zlib-1.2.8.tar.gz

tar zxvf nginx-1.0.9.tar.gz
cd nginx-1.0.9/
./configure \
--user=www-data \
--group=www-data \
--prefix=/opt/lnmp/app/nginx \
--pid-path=/opt/lnmp/app/nginx/nginx.pid \
--with-http_stub_status_module \
--with-http_ssl_module \
--with-http_gzip_static_module \
--with-pcre=/opt/lnmp/tar_package/pcre-8.30/ \
--with-openssl=/opt/lnmp/tar_package/openssl-1.0.1e/ \
--with-zlib=/opt/lnmp/tar_package/zlib-1.2.8

sed -i 's#./configure#./configure --prefix=/opt/lnmp/app/zlib#' ./objs/Makefile
sed -i 's#./configure --prefix=/opt/lnmp/app/zlib --disable-shared#./configure --prefix=/opt/lnmp/app/pcre8#' ./objs/Makefile
make && make install
cd ../

cd $cur_dir
mkdir -p /opt/lnmp/app/nginx/etc/sites-enabled
mkdir -p /opt/lnmp/app/nginx/etc/sites-available
cat > /opt/lnmp/app/nginx/etc/nginx.conf <<EOF
user www-data;
worker_processes 4;
pid /var/run/nginx.pid;

events {
	worker_connections 768;
	# multi_accept on;
}

http {

	##
	# Basic Settings
	##

	sendfile on;
	tcp_nopush on;
	tcp_nodelay on;
	keepalive_timeout 65;
	types_hash_max_size 2048;
	# server_tokens off;

	# server_names_hash_bucket_size 64;
	# server_name_in_redirect off;

	include /etc/nginx/mime.types;
	default_type application/octet-stream;

	##
	# Logging Settings
	##

	access_log /var/log/nginx/access.log;
	error_log /var/log/nginx/error.log;

	##
	# Gzip Settings
	##

	gzip on;
	gzip_disable "msie6";

	# gzip_vary on;
	# gzip_proxied any;
	# gzip_comp_level 6;
	# gzip_buffers 16 8k;
	# gzip_http_version 1.1;
	# gzip_types text/plain text/css application/json application/x-javascript text/xml application/xml application/xml+rss text/javascript;

	##
	# nginx-naxsi config
	##
	# Uncomment it if you installed nginx-naxsi
	##

	#include /etc/nginx/naxsi_core.rules;

	##
	# nginx-passenger config
	##
	# Uncomment it if you installed nginx-passenger
	##
	
	#passenger_root /usr;
	#passenger_ruby /usr/bin/ruby;

	##
	# Virtual Host Configs
	##

	include /etc/nginx/conf.d/*.conf;
	include /etc/nginx/sites-enabled/*;
}
EOF


cat > /opt/lnmp/app/nginx/etc/nginx/sites-enabled/herouser <<EOF
#configure the nginx and php

server {
        listen   80 default;
        server_name  localhost;

        access_log  /opt/lnmp/app/www/logs/localhost.80.access.log;
        root   /opt/lnmp/app/www/herouser;
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
                fastcgi_param  SCRIPT_FILENAME  /opt/lnmp/app/www/herouser$fastcgi_script_name;
                include fastcgi_params;
        }
}
EOF

}