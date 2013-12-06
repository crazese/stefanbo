#!/bin/sh

install_php()
{
echo "============================Install PHP 5.3.10================================"
cd /opt/lnmp/tar_package
cur_dir=$(pwd)
mkdir -p /opt/lnmp/app/phpextend -p 

cd $cur_dir
tar -zxvf jpegsrc.v9.tar.gz
cd jpeg-9/
./configure --prefix=/opt/lnmp/app/phpextend \
--enable-shared --enable-static
make -j2 && make install
cd ..

tar -zxvf libevent-2.0.21-stable.tar.gz
cd libevent-2.0.21-stable
./configure --prefix=/opt/lnmp/app/phpextend 
make -j2 && make install 
cd ..

tar -zxvf freetype-2.4.12.tar.gz
cd freetype-2.4.12
./configure --prefix=/opt/lnmp/app/phpextend
make -j2 && make install
cd ..

tar -zxvf mhash-0.9.9.9.tar.gz
cd mhash-0.9.9.9
./configure --prefix=/opt/lnmp/app/phpextend
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
./configure --prefix=/opt/lnmp/app/phpextend
make -j2 && make install 
cd ..

tar -zxvf pkg-config-0.24.tar.gz
cd pkg-config-0.24
./configure --prefix=/opt/lnmp/app/phpextend
make && make install
cd ..

tar -xvf xproto-7.0.14.tar.bz2
cd xproto-7.0.14
./configure --prefix=/opt/lnmp/app/phpextend
make && make install
cd ..

tar -xvf xextproto-7.0.4.tar.bz2
cd xextproto-7.0.4
./configure --prefix=/opt/lnmp/app/phpextend
make && make install
cd ..

tar -xvf xtrans-1.2.7.tar.bz2
cd xtrans-1.2.7
./configure --prefix=/opt/lnmp/app/phpextend
make && make install
cd ..

tar -xvf xcb-proto-1.5.tar.bz2
cd xcb-proto-1.5
./configure --prefix=/opt/lnmp/app/phpextend
make && make install 
cd ..

tar -zxvf libxslt-1.1.24.tar.gz
cd libxslt-1.1.24
./configure --prefix=/opt/lnmp/app/phpextend \
--with-libxml-prefix=/opt/lnmp/app/libxml2
make && make install 
cd ..

#tar -zxvf libxcb-1.5.tar.gz
#cd libxcb-1.5
#./configure --prefix=/opt/lnmp/app/phpextend
#make && make install
#cd ..#

#tar -xvf libX11-1.3.2.tar.bz2 
#cd libX11-1.3.2
#./configure --prefix=/opt/lnmp/app/phpextend#

#bzip2 -d libXpm-3.5.5.tar.bz2
#tar -xvf libXpm-3.5.5.tar
#cd libXpm-3.5.5
#./configure --prefix=/opt/lnmp/app/phpextend


tar -zxvf gd-2.0.35.tar.gz
cd gd-2.0.35
./configure --prefix=/opt/lnmp/app/phpextend \
--with-png=/opt/lnmp/app/phpextend \
--with-freetype=/opt/lnmp/app/phpextend \
--with-jpeg=/opt/lnmp/app/phpextend 

sed -i 's#zlib.h#/opt/lnmp/app/zlib/include/zlib.c#' gd_gd2.c

make -j2 && make install 
cd ..

cat >> /etc/ld.so.conf.d/phpextend.conf <<EOF
/opt/lnmp/app/libiconv/lib
/opt/lnmp/app/libmcrypt/lib
/opt/lnmp/app/libxml2/lib
/opt/lnmp/app/openssl/lib
/opt/lnmp/app/pcre8/lib
/opt/lnmp/app/zlib/lib
/opt/lnmp/app/phpextend/lib
/opt/lnmp/app/curl/lib
/opt/lnmp/app/mysql/lib
EOF
ldconfig




#export PHP_AUTOCONF=/usr/local/autoconf-2.13/bin/autoconf
#export PHP_AUTOHEADER=/usr/local/autoconf-2.13/bin/autoheader
#export C_INCLUDE_PATH=/opt/lnmp/app/phpextend/include:$C_INCLUDE_PATH
#export CPLUS_INCLUDE_PATH=/opt/lnmp/app/phpextend/include:$CPLUS_INCLUDE_PATH
cd $cur_dir
tar zxvf php-5.3.10.tar.gz
cd php-5.3.10/
./configure --prefix=/opt/lnmp/app/php \
--with-config-file-path=/opt/lnmp/app/php/etc \
--enable-fpm \
--with-fpm-user=www-data \
--with-fpm-group=www-data \
--with-mysql=/opt/lnmp/app/mysql \
--with-iconv-dir=/opt/lnmp/app/libiconv \
--with-freetype-dir=/opt/lnmp/app/phpextend \
--with-jpeg-dir=/opt/lnmp/app/phpextend \
--with-png-dir=/opt/lnmp/app/phpextend \
--with-zlib-dir=/opt/lnmp/app/zlib \
--with-libxml-dir=/opt/lnmp/app/libxml2 \
--enable-xml \
--disable-rpath \
--enable-magic-quotes \
--enable-safe-mode \
--enable-bcmath \
--enable-shmop \
--enable-sysvsem \
--enable-inline-optimization \
--with-curl=/opt/lnmp/app/curl \
--with-curlwrappers \
--enable-mbregex \
--enable-mbstring \
--with-mcrypt=/opt/lnmp/app/libmcrypt \
--enable-ftp \
--with-gd \
--enable-gd-native-ttf \
--with-openssl-dir=/opt/lnmp/app/openssl \
--with-mhash \
--enable-pcntl \
--enable-sockets \
--with-xmlrpc \
--enable-zip \
--enable-soap \
--without-pear \
--with-gettext \
--disable-fileinfo

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
pm.start_servers = 2
pm.min_spare_servers = 1
pm.max_spare_servers = 6
request_terminate_timeout = 100
EOF


rm -f /usr/bin/php
ln -s /usr/local/php/bin/php /usr/bin/php
ln -s /usr/local/php/bin/phpize /usr/bin/phpize
ln -s /usr/local/php/sbin/php-fpm /usr/bin/php-fpm

echo "Copy new php configure file."
mkdir -p /usr/local/php/etc
cp php.ini-production /usr/local/php/etc/php.ini

echo "============================PHP 5.3.10 install completed======================"
}
install_php