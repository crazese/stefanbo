#!/bin/bash
# Author: zhubo

# Check if user is root
[ $(id -u) != "0" ] && echo "Error: You must be root to run this script" && exit 1 

export PATH=/opt/lnmp/sbin:/opt/lnmp/bin:/sbin:/bin:/usr/sbin:/usr/bin
clear
printf "
#######################################################################
#	php-5.3.10 		                                                  #
#	extentsion(cli,mysql,gd,mcrypt,fpm,curl,openssl,memcached,apc)    #
#	nginx-1.0.9                                                       #
# 	mysql Ver 14.12 Distrib 5.0.27                                    #
#	memcached-1.5                                                     #
#	安装过程中所需要的第三方类库都安装在对应的路径下，例如：          #
#	Php扩展apc所需的libpcre3，需要编译安装至php所在目录。             #
#	目录设定如下:                                                     #
#	Php  /opt/lnmp/php                                                #
#	Mysql  /opt/lnmp/mysql                                            #
#	Nginx  /opt/lnmp/nginx                                            #
#	Memcached  /opt/lnmp/memcached                                    #
#                                                                     #
#######################################################################"
cur_dir=$(pwd)


#set mysql root password
mysqlrootpwd="123456"
installinnodb="y"
isinstallphp53="y"
isinstallmysql50="n"

#get tar.gz or tgz from ftp server
function ftp_get(){
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
}


function InstallDependsAndOpt()
{
cd $cur_dir

tar -zxvf m4-1.4.9.tar.gz
cd m4-1.4.9
./configure --prefix=/opt/lnmp/libexec/m4
make && make install
cd ../
ln -s /opt/lnmp/libexec/m4/bin/* /opt/lnmp/bin/

tar -zxvf autoconf-2.13.tar.gz
cd autoconf-2.13/
./configure --prefix=/opt/lnmp/libexec/autoconf
make && make install
cd ../
ln -s /opt/lnmp/libexec/autoconf/bin/* /opt/lnmp/bin/

tar -zxvf libiconv-1.14.tar.gz
cd libiconv-1.14/
./configure --prefix=/opt/lnmp/libexec/libiconv 
make && make install
cd ../
ln -s /opt/lnmp/libexec/libiconv/bin/* /opt/lnmp/bin/
ln -s /opt/lnmp/libexec/libiconv/lib/* /opt/lnmp/lib/

tar -zxvf libmcrypt-2.5.8.tar.gz
cd libmcrypt-2.5.8/
./configure --prefix=/opt/lnmp/libexec/libmcrypt
make && make install
/sbin/ldconfig
cd libltdl/
./configure --enable-ltdl-install --prefix=/opt/lnmp/libexec/libmcrypt/libltdl
make && make install
cd ../../

ln -s /opt/lnmp/libexec/libmcrypt/lib/* /opt/lnmp/lib/

tar -zxvf libxml2-2.7.8.tar.gz
cd libxml2-2.7.8/
./configure --prefix=/opt/lnmp/libexec/libxml2
make && make install
cd ../
ln -s /opt/lnmp/libexec/libxml2/lib/* /opt/lnmp/lib/
ln -s /opt/lnmp/libexec/libxml2/bin/* /opt/lnmp/bin/


#if [ `getconf WORD_BIT` = '32' ] && [ `getconf LONG_BIT` = '64' ] ; then
#        ln -s /usr/lib/x86_64-linux-gnu/libpng* /usr/lib/
#        ln -s /usr/lib/x86_64-linux-gnu/libjpeg* /usr/lib/
#else
#        ln -s /usr/lib/i386-linux-gnu/libpng* /usr/lib/
#        ln -s /usr/lib/i386-linux-gnu/libjpeg* /usr/lib/
#fi

ulimit -v unlimited

if [ ! `grep -l "/lib"    '/etc/ld.so.conf'` ]; then
	echo "/lib" >> /etc/ld.so.conf
fi

if [ ! `grep -l '/usr/lib'    '/etc/ld.so.conf'` ]; then
	echo "/usr/lib" >> /etc/ld.so.conf
fi

if [ ! `grep -l '/opt/lnmp/lib'    '/etc/ld.so.conf'` ]; then
	echo "/opt/lnmp/lib" >> /etc/ld.so.conf
fi

ldconfig
}


function InstallNginx()
{
echo "============================Install Nginx 1.0.9================================="
groupadd www
useradd -s /sbin/nologin -g www www

mkdir -p /opt/wwwroot/default
chmod +w /opt/wwwroot/default
mkdir -p /opt/wwwlogs
chmod 777 /opt/wwwlogs
touch /opt/wwwlogs/nginx_error.log

cd $cur_dir
chown -R www:www /opt/wwwroot/default

cd /opt/lnmp/tar_package
tar -zxvf pcre-8.12.tar.gz
tar -zxvf openssl-1.0.1e-src.tar.gz
tar -zxvf zlib-1.2.8.tar.gz

ldconfig


tar -zxvf nginx-1.0.9.tar.gz
cd nginx-1.0.9/
./configure --user=www --group=www \
--prefix=/opt/lnmp/nginx \
--with-http_stub_status_module \
--with-http_ssl_module \
--with-http_gzip_static_module \
--with-pcre=/opt/lnmp/tar_package/pcre-8.12 \
--with-openssl=/opt/lnmp/tar_package/openssl-1.0.1e \
--with-zlib=/opt/lnmp/tar_package/zlib-1.2.8

make && make install
cd ../

ln -s /opt/lnmp/nginx/sbin/nginx /opt/lnmp/bin/nginx

cd /opt/lnmp
cat > /opt/lnmp/nginx/conf/nginx.conf <<EOF
user  www www;

worker_processes 1;

error_log  /opt/wwwlogs/nginx_error.log  crit;

pid        /opt/lnmp/nginx/logs/nginx.pid;

#Specifies the value for maximum file descriptors that can be opened by this process.
worker_rlimit_nofile 51200;

events
	{
		use epoll;
		worker_connections 51200;
	}

http
	{
		include       mime.types;
		default_type  application/octet-stream;

		server_names_hash_bucket_size 128;
		client_header_buffer_size 32k;
		large_client_header_buffers 4 32k;
		client_max_body_size 50m;

		sendfile on;
		tcp_nopush     on;

		keepalive_timeout 60;

		tcp_nodelay on;

		fastcgi_connect_timeout 300;
		fastcgi_send_timeout 300;
		fastcgi_read_timeout 300;
		fastcgi_buffer_size 64k;
		fastcgi_buffers 4 64k;
		fastcgi_busy_buffers_size 128k;
		fastcgi_temp_file_write_size 256k;

		gzip on;
		gzip_min_length  1k;
		gzip_buffers     4 16k;
		gzip_http_version 1.0;
		gzip_comp_level 2;
		gzip_types       text/plain application/x-javascript text/css application/xml;
		gzip_vary on;
		gzip_proxied        expired no-cache no-store private auth;
		gzip_disable        "MSIE [1-6]\.";

		#limit_zone  crawler  $binary_remote_addr  10m;

		server_tokens off;
		#log format
		log_format  access  '$remote_addr - $remote_user [$time_local] "$request" '
             '$status $body_bytes_sent "$http_referer" '
             '"$http_user_agent" $http_x_forwarded_for';

server
	{
		listen       80;
		server_name www.lnmp.org;
		index index.html index.htm index.php;
		root  /opt/wwwroot/default;

		location ~ .*\.(php|php5)?$
			{
				try_files $uri =404;
				fastcgi_pass  unix:/tmp/php-cgi.sock;
				fastcgi_index index.php;
				include fcgi.conf;
			}

		location /status {
			stub_status on;
			access_log   off;
		}

		location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$
			{
				expires      30d;
			}

		location ~ .*\.(js|css)?$
			{
				expires      12h;
			}

		access_log  /opt/wwwlogs/access.log  access;
	}
include vhost/*.conf;
}
EOF
rm -f /opt/lnmp/nginx/conf/fcgi.conf
cat >  /opt/lnmp/nginx/conf/fcgi.conf <<EOF
fastcgi_param  GATEWAY_INTERFACE  CGI/1.1;
fastcgi_param  SERVER_SOFTWARE    nginx/$nginx_version;

fastcgi_param  QUERY_STRING       $query_string;
fastcgi_param  REQUEST_METHOD     $request_method;
fastcgi_param  CONTENT_TYPE       $content_type;
fastcgi_param  CONTENT_LENGTH     $content_length;

fastcgi_param  SCRIPT_FILENAME    $document_root$fastcgi_script_name;
fastcgi_param  SCRIPT_NAME        $fastcgi_script_name;
fastcgi_param  REQUEST_URI        $request_uri;
fastcgi_param  DOCUMENT_URI       $document_uri;
fastcgi_param  DOCUMENT_ROOT      $document_root;
fastcgi_param  SERVER_PROTOCOL    $server_protocol;

fastcgi_param  REMOTE_ADDR        $remote_addr;
fastcgi_param  REMOTE_PORT        $remote_port;
fastcgi_param  SERVER_ADDR        $server_addr;
fastcgi_param  SERVER_PORT        $server_port;
fastcgi_param  SERVER_NAME        $server_name;

# PHP only, required if PHP was built with --enable-force-cgi-redirect
fastcgi_param  REDIRECT_STATUS    200;
EOF
}


function InstallMySQL51()
{
echo "============================Install MySQL 5.0.27=================================="
cd /opt/lnmp/tar_package
rm /etc/my.cnf
rm /etc/mysql/my.cnf
rm -rf /etc/mysql/
apt-get remove -y mysql-server
apt-get remove -y mysql-common mysql-client

cd /opt/lnmp/tar_package
tar zxvf mysql-5.0.27.tar.gz
cd mysql-5.0.27/
./configure --prefix=/opt/lnmp/mysql \
--with-extra-charsets=complex \
--enable-thread-safe-client \
--enable-assembler \
--with-mysqld-ldflags=-all-static \
--with-charset=utf8 \
--enable-thread-safe-client \
--with-big-tables \
--with-readline \
--with-ssl \
--with-embedded-server \
--enable-local-infile \
--with-plugins=innobase

make && make install
cd ../

groupadd mysql
useradd -s /sbin/nologin -g mysql mysql
cp /opt/lnmp/mysql/share/mysql/my-medium.cnf /etc/my.cnf
sed -i 's/skip-locking/skip-external-locking/g' /etc/my.cnf
sed -i 's:#innodb:innodb:g' /etc/my.cnf

/opt/lnmp/mysql/bin/mysql_install_db --user=mysql --basedir=/opt/lnmp/mysql/data
ln -s /opt/lnmp/mysql/share/mysql /usr/share/

chown -R mysql.mysql /opt/lnmp/mysql/var
chgrp -R mysql /opt/lnmp/mysql/.
cp /opt/lnmp/mysql/share/mysql/mysql.server /etc/init.d/mysql
chmod 755 /etc/init.d/mysql

cat > /etc/ld.so.conf.d/mysql.conf<<EOF
/opt/lnmp/mysql/lib/mysql
/opt/lnmp/lib
EOF
ldconfig

ln -s /opt/lnmp/mysql/lib/mysql /usr/lib/mysql
ln -s /opt/lnmp/mysql/include/mysql /usr/include/mysql

ln -s /opt/lnmp/mysql/bin/mysql /usr/bin/mysql
ln -s /opt/lnmp/mysql/bin/mysqldump /usr/bin/mysqldump
ln -s /opt/lnmp/mysql/bin/myisamchk /usr/bin/myisamchk
ln -s /opt/lnmp/mysql/bin/mysqld_safe /usr/bin/mysqld_safe

/etc/init.d/mysql start
/opt/lnmp/mysql/bin/mysqladmin -u root password 123456

cat > /tmp/mysql_sec_script<<EOF
use mysql;
update user set password=password('123456') where user='root';
delete from user where not (user='root') ;
delete from user where user='root' and password=''; 
drop database test;
DROP USER ''@'%';
flush privileges;
EOF

/opt/lnmp/mysql/bin/mysql -u root -p123456 -h localhost < /tmp/mysql_sec_script

rm -f /tmp/mysql_sec_script

/etc/init.d/mysql restart
/etc/init.d/mysql stop
echo "============================MySQL 5.0.27 install completed========================="
}

function InstallPHP53()
{
echo "============================Install PHP 5.3.17================================"
cd $cur_dir
export PHP_AUTOCONF=/opt/lnmp/autoconf-2.13/bin/autoconf
export PHP_AUTOHEADER=/opt/lnmp/autoconf-2.13/bin/autoheader
tar zxvf php-5.3.17.tar.gz
cd php-5.3.17/
./configure --prefix=/opt/lnmp/php --with-config-file-path=/opt/lnmp/php/etc --enable-fpm --with-fpm-user=www --with-fpm-group=www --with-mysql=mysqlnd --with-mysqli=mysqlnd --with-pdo-mysql=mysqlnd --with-iconv-dir --with-freetype-dir --with-jpeg-dir --with-png-dir --with-zlib --with-libxml-dir=/usr --enable-xml --disable-rpath --enable-magic-quotes --enable-safe-mode --enable-bcmath --enable-shmop --enable-sysvsem --enable-inline-optimization --with-curl --with-curlwrappers --enable-mbregex --enable-mbstring --with-mcrypt --enable-ftp --with-gd --enable-gd-native-ttf --with-openssl --with-mhash --enable-pcntl --enable-sockets --with-xmlrpc --enable-zip --enable-soap --without-pear --with-gettext --disable-fileinfo

make ZEND_EXTRA_LIBS='-liconv'
make install

rm -f /usr/bin/php
ln -s /opt/lnmp/php/bin/php /usr/bin/php
ln -s /opt/lnmp/php/bin/phpize /usr/bin/phpize
ln -s /opt/lnmp/php/sbin/php-fpm /usr/bin/php-fpm

echo "Copy new php configure file."
mkdir -p /opt/lnmp/php/etc
cp php.ini-production /opt/lnmp/php/etc/php.ini

cd $cur_dir
# php extensions
echo "Modify php.ini......"
sed -i 's/post_max_size = 8M/post_max_size = 50M/g' /opt/lnmp/php/etc/php.ini
sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 50M/g' /opt/lnmp/php/etc/php.ini
sed -i 's/;date.timezone =/date.timezone = PRC/g' /opt/lnmp/php/etc/php.ini
sed -i 's/short_open_tag = Off/short_open_tag = On/g' /opt/lnmp/php/etc/php.ini
sed -i 's/; cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/g' /opt/lnmp/php/etc/php.ini
sed -i 's/; cgi.fix_pathinfo=0/cgi.fix_pathinfo=0/g' /opt/lnmp/php/etc/php.ini
sed -i 's/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/g' /opt/lnmp/php/etc/php.ini
sed -i 's/max_execution_time = 30/max_execution_time = 300/g' /opt/lnmp/php/etc/php.ini
sed -i 's/register_long_arrays = On/;register_long_arrays = On/g' /opt/lnmp/php/etc/php.ini
sed -i 's/magic_quotes_gpc = On/;magic_quotes_gpc = On/g' /opt/lnmp/php/etc/php.ini
sed -i 's/disable_functions =.*/disable_functions = passthru,exec,system,chroot,scandir,chgrp,chown,shell_exec,proc_open,proc_get_status,ini_alter,ini_restore,dl,pfsockopen,openlog,syslog,readlink,symlink,popepassthru,stream_socket_server,fsockopen/g' /opt/lnmp/php/etc/php.ini

echo "Install ZendGuardLoader for PHP 5.3"
if [ `getconf WORD_BIT` = '32' ] && [ `getconf LONG_BIT` = '64' ] ; then
	wget -c http://downloads.zend.com/guard/5.5.0/ZendGuardLoader-php-5.3-linux-glibc23-x86_64.tar.gz
	tar zxvf ZendGuardLoader-php-5.3-linux-glibc23-x86_64.tar.gz
	mkdir -p /opt/lnmp/zend/
	cp ZendGuardLoader-php-5.3-linux-glibc23-x86_64/php-5.3.x/ZendGuardLoader.so /opt/lnmp/zend/
else
	wget -c http://downloads.zend.com/guard/5.5.0/ZendGuardLoader-php-5.3-linux-glibc23-i386.tar.gz
	tar zxvf ZendGuardLoader-php-5.3-linux-glibc23-i386.tar.gz
	mkdir -p /opt/lnmp/zend/
	cp ZendGuardLoader-php-5.3-linux-glibc23-i386/php-5.3.x/ZendGuardLoader.so /opt/lnmp/zend/
fi

echo "Write ZendGuardLoader to php.ini......"
cat >>/opt/lnmp/php/etc/php.ini<<EOF
;eaccelerator

;ionCube

[Zend Optimizer] 
zend_extension=/opt/lnmp/zend/ZendGuardLoader.so
EOF

echo "Creating new php-fpm configure file......"
cat >/opt/lnmp/php/etc/php-fpm.conf<<EOF
[global]
pid = /opt/lnmp/php/var/run/php-fpm.pid
error_log = /opt/lnmp/php/var/log/php-fpm.log
log_level = notice

[www]
listen = /tmp/php-cgi.sock
user = www
group = www
pm = dynamic
pm.max_children = 10
pm.start_servers = 2
pm.min_spare_servers = 1
pm.max_spare_servers = 6
request_terminate_timeout = 100
EOF

echo "Copy php-fpm init.d file......"
cp $cur_dir/php-5.3.17/sapi/fpm/init.d.php-fpm /etc/init.d/php-fpm
chmod +x /etc/init.d/php-fpm

cp $cur_dir/lnmp /root/lnmp
chmod +x /root/lnmp
sed -i 's:/opt/lnmp/php/logs:/opt/lnmp/php/var/run:g' /root/lnmp
echo "============================PHP 5.3.17 install completed======================"
}