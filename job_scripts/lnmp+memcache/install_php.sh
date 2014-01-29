#!/bin/sh

install_php()
{
echo "============================Install PHP 5.3.10================================"
cd /opt/lnmp/tar_package
cur_dir=$(pwd)
mkdir -p /opt/lnmp/app/init -p 

cd $cur_dir
tar -zxvf jpegsrc.v9.tar.gz
cd jpeg-9/
./configure --prefix=/opt/lnmp/app/init \
--enable-shared --enable-static
make -j2 && make install
cd ..

tar -zxvf libevent-2.0.21-stable.tar.gz
cd libevent-2.0.21-stable
./configure --prefix=/opt/lnmp/app/init 
make -j2 && make install 
cd ..

tar -zxvf freetype-2.4.12.tar.gz
cd freetype-2.4.12
./configure --prefix=/opt/lnmp/app/init
make -j2 && make install
cd ..

tar -zxvf mhash-0.9.9.9.tar.gz
cd mhash-0.9.9.9
./configure --prefix=/opt/lnmp/app/init
make -j2 && make install
cd ..

tar -zxvf curl-7.33.0.tar.gz
cd curl-7.33.0
./configure --prefix=/opt/lnmp/app/curl
make -j2 && make install 
cd ..

export LDFLAGS="-L/opt/lnmp/app/zlib/lib" 
export CPPFLAGS="-I/opt/lnmp/app/zlib/include"
tar -zxvf libpng-1.6.7.tar.gz
cd libpng-1.6.7
./configure --prefix=/opt/lnmp/app/init
make -j2 && make install 
cd ..

tar -zxvf pkg-config-0.24.tar.gz
cd pkg-config-0.24
./configure --prefix=/opt/lnmp/app/init
make && make install
cd ..

tar -xvf xproto-7.0.14.tar.bz2
cd xproto-7.0.14
./configure --prefix=/opt/lnmp/app/init
make && make install
cd ..

tar -xvf xextproto-7.0.4.tar.bz2
cd xextproto-7.0.4
./configure --prefix=/opt/lnmp/app/init
make && make install
cd ..

tar -xvf xtrans-1.2.7.tar.bz2
cd xtrans-1.2.7
./configure --prefix=/opt/lnmp/app/init
make && make install
cd ..

tar -xvf xcb-proto-1.5.tar.bz2
cd xcb-proto-1.5
./configure --prefix=/opt/lnmp/app/init
make && make install 
cd ..

tar -zxvf libxslt-1.1.24.tar.gz
cd libxslt-1.1.24
./configure --prefix=/opt/lnmp/app/init \
--with-libxml-prefix=/opt/lnmp/app/libxml2
make && make install 
cd ..

#tar -zxvf libxcb-1.5.tar.gz
#cd libxcb-1.5
#./configure --prefix=/opt/lnmp/app/init
#make && make install
#cd ..#

#tar -xvf libX11-1.3.2.tar.bz2 
#cd libX11-1.3.2
#./configure --prefix=/opt/lnmp/app/init#

#bzip2 -d libXpm-3.5.5.tar.bz2
#tar -xvf libXpm-3.5.5.tar
#cd libXpm-3.5.5
#./configure --prefix=/opt/lnmp/app/init


tar -zxvf gd-2.0.35.tar.gz
cd gd-2.0.35
./configure --prefix=/opt/lnmp/app/init \
--with-png=/opt/lnmp/app/init \
--with-freetype=/opt/lnmp/app/init \
--with-jpeg=/opt/lnmp/app/init 

sed -i 's#zlib.h#/opt/lnmp/app/zlib/include/zlib.c#' gd_gd2.c

make -j2 && make install 
cd ..

cat >> /etc/ld.so.conf.d/init.conf <<EOF
/opt/lnmp/app/libiconv/lib
/opt/lnmp/app/libmcrypt/lib
/opt/lnmp/app/libxml2/lib
/opt/lnmp/app/openssl/lib
/opt/lnmp/app/pcre8/lib
/opt/lnmp/app/zlib/lib
/opt/lnmp/app/init/lib
/opt/lnmp/app/curl/lib
/opt/lnmp/app/mysql/lib
EOF
ldconfig




#export PHP_AUTOCONF=/usr/local/autoconf-2.13/bin/autoconf
#export PHP_AUTOHEADER=/usr/local/autoconf-2.13/bin/autoheader
#export C_INCLUDE_PATH=/opt/lnmp/app/init/include:$C_INCLUDE_PATH
#export CPLUS_INCLUDE_PATH=/opt/lnmp/app/init/include:$CPLUS_INCLUDE_PATH
cd $cur_dir
tar zxvf php-5.3.10.tar.gz
cd php-5.3.10/
./configure --prefix=/opt/lnmp/app/php \
--with-config-file-path=/opt/lnmp/app/php/etc/ \
--with-curl=/opt/lnmp/app/init \
--with-freetype-dir=/opt/lnmp/app/init \
--with-fpm-user=www-data \
--with-fpm-group=www-data \
--with-gettext \
--with-gd=/opt/lnmp/app/init \
--with-jpeg-dir=/opt/lnmp/app/init \
--with-libxml-dir=/opt/lnmp/app/init \
--with-mysql=/opt/lnmp/app/mysql \
--with-mhash \
--with-mysqli \
--with-openssl-dir=/opt/lnmp/app/init \
--with-png-dir=/opt/lnmp/app/init \
--with-pdo-mysql=/opt/lnmp/app/mysql \
--with-xmlrpc \
--with-zlib-dir=/opt/lnmp/app/init \
--without-pear \
--enable-bcmath \
--enable-calendar \
--enable-dba \
--enable-exif \
--enable-fpm \
--enable-fileinfo \
--enable-ftp \
--enable-gd-native-ttf \
--enable-inline-optimization \
--enable-mbregex \
--enable-mbstring \
--enable-magic-quotes \
--enable-embedded-mysqli \
--enable-pcntl \
--enable-pdo \
--enable-safe-mode \
--enable-shmop \
--enable-sysvsem \
--enable-sysvmsg \
--enable-sockets \
--enable-soap \
--enable-tokenizer \
--enable-wddx \
--enable-xml \
--enable-zip \
--disable-rpath


make 
make install

cp php.ini-development /opt/lnmp/app/php/etc/php.ini
mkdir -p /opt/lnmp/app/php/etc/init.d 
cp sapi/fpm/init.d.php-fpm /opt/lnmp/app/php/etc/init.d/php-fpm
chmod +x /opt/lnmp/app/php/etc/init.d/php-fpm

cat /opt/lnmp/app/php/etc/php-fpm.conf <<EOF
[global]
pid = /opt/lnmp/app/php/var/run/php-fpm.pid
error_log = /opt/lnmp/app/php/var/log/php-fpm.log
log_level = notice

[www]
listen = /tmp/php-cgi.sock
user = www-data
group = www-data
pm = dynamic
pm.max_children = 10
pm.start_servers = 4
pm.min_spare_servers = 2
pm.max_spare_servers = 6
request_terminate_timeout = 100
listen = 127.0.0.1:9000
EOF

rm -f /usr/bin/php
ln -s /usr/local/php/bin/php /usr/bin/php
ln -s /usr/local/php/bin/phpize /usr/bin/phpize
ln -s /usr/local/php/sbin/php-fpm /usr/bin/php-fpm

echo "Copy new php configure file."
mkdir -p /opt/lnmp/app/php/etc 
cp php.ini-production /opt/lnmp/app/php/etc/php.ini
sed -i 's#;date.timezone =#date.timezone = Asia/Shanghai' /opt/lnmp/app/php/etc/php.ini

/opt/lnmp/app/php/etc/init.d/php-fpm restart
echo "============================PHP 5.3.10 install completed======================"
}
install_php

install_init() {
cd /opt/lnmp/tar_package
cur_dir=$(pwd)

tar -zxvf bz2-1.0.tgz
cd bz2-1.0
./configure \
--prefix=/opt/lnmp/app/init \
--with-php-config=/opt/lnmp/app/php/bin/php-config

tar zxvf APC-3.1.9.tgz
cd APC-3.1.9
/opt/lnmp/app/php/bin/phpize
./configure \
--prefix=/opt/lnmp/app/init/ \
--enable-apc \
--enable-apc-mmap \
--with-php-config=/opt/lnmp/app/php/bin/php-config
make && make install
cd ..

unzip  igbinary-igbinary-1.1.1-28-gc35d48f.zip
cd igbinary-igbinary-c35d48f
/opt/lnmp/app/php/bin/phpize
./configure  \
--prefix=/opt/lnmp/app/init \
--enable-igbinary \
--with-php-config=/opt/lnmp/app/php/bin/php-config
make && make install
cd ..

tar zxvf libmemcached-1.0.10.tar.gz
cd libmemcached-1.0.10
./configure --prefix=/opt/lnmp/app/init 
make && make install
cd ..

tar zxvf memcached-2.1.0.tgz
cd memcached-2.1.0
/opt/lnmp/app/php/bin/phpize
./configure  \
--prefix=/opt/lnmp/app/init \
--enable-memcached \
--enable-memcached-igbinary \
--with-php-config=/opt/lnmp/app/php/bin/php-config \
--with-zlib-dir=/opt/lnmp/app/zlib/ \
--with-libmemcached-dir=/opt/lnmp/app/init

make && make install
cd ..

tar -xvf memcache-2.2.7.tgz
cd memcache-2.2.7
/opt/lnmp/app/php/bin/phpize
./configure \
--prefix=/opt/lnmp/app/init \
--with-php-config=/opt/lnmp/app/php/bin/php-config 
make && make install

cat >> /opt/lnmp/app/php/etc/php.ini <<EOF
extension=igbinary.so
extension=memcached.so
extension=memcache.so
extension=apc.so
apc.shm_size=250m
cgi.fix_pathinfo=1
EOF
}
install_init