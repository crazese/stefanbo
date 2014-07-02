#!/bin/sh

# install mysql
#groupadd -g 572 mysql
#useradd -g mysql -u 572 -s /sbin/nologin mysql

# vim /etc/passwd  


mkdir -p /home/mysql/data
mkdir -p /home/mysql/log
chown -R mysql:mysql /home/mysql
chmod -R 755 /home/mysql 

cd /opt/tools/
#wget http://cdn.mysql.com/Downloads/MySQL-5.6/mysql-5.6.17.tar.gz
tar zxvf mysql-5.6.17.tar.gz
cd mysql-5.6.17/

cmake \
-DCMAKE_INSTALL_PREFIX=/usr/local/mysql \
-DCMAKE_BUILD_TYPE:STRING=Release \
-DENABLE_DEBUG_SYNC:BOOL=OFF \
-DMYSQL_UNIX_ADDR=/tmp/mysql.sock \
-DDEFAULT_CHARSET=utf8 \
-DDEFAULT_COLLATION=utf8_general_ci \
-DWITH_EXTRA_CHARSETS=all \
-DWITH_EMBEDDED_SERVER:BOOL=OFF \
-DWITH_MYISAM_STORAGE_ENGINE=1 \
-DWITH_INNOBASE_STORAGE_ENGINE=1 \
-DWITH_MEMORY_STORAGE_ENGINE=1 \
-DWITH_READLINE=1 \
-DWITH_LIBEDIT:BOOL=1 \
-DENABLED_LOCAL_INFILE=1 \
-DWITH_ZLIB:STRING=bundled \
-DWITH_LIBWRAP:BOOL=OFF \
-DWITH_SSL:STRING=system \
-DWITH_UNIT_TESTS:BOOL=OFF \
-DMYSQL_DATADIR=/home/mysql/data \
-DMYSQL_USER=mysql
make && make install
make clean

cd ..

ln -s /usr/local/mysql/lib/libmysqlclient.so.16 /usr/lib/libmysqlclient.so.16
ln -s /usr/local/mysql/lib/libmysqlclient.so.18 /usr/lib/libmysqlclient.so.18

scp   root@210.73.208.146:/etc/my.cnf   /etc/my.cnf
scp   root@210.73.208.146:/etc/init.d/mysqld  /etc/init.d/mysqld

/usr/local/mysql/scripts/mysql_install_db \
--defaults-file=/etc/my.cnf \
--basedir=/usr/local/mysql \
--datadir=/home/mysql/data \
--user=mysql

chmod +x /etc/init.d/mysqld

chkconfig --add mysqld
chkconfig --level 345 mysqld on

service mysqld start
/usr/local/mysql/bin/mysqladmin password qwe123


# install openssl
# reference http://www.linuxfromscratch.org/blfs/view/svn/postlfs/openssl.html
tar_folder="/usr/local/postfix/tar"
mkdir -p $tar_folder
cd $tar_folder

wget http://www.openssl.org/source/openssl-1.0.1h.tar.gz
wget http://www.linuxfromscratch.org/patches/blfs/svn/openssl-1.0.1h-fix_parallel_build-1.patch

tar -zxvf openssl-1.0.1h.tar.gz
cd openssl-1.0.1h

patch -Np1 -i ../openssl-1.0.1h-fix_parallel_build-1.patch &&

./config --prefix=/usr         \
         --openssldir=/etc/ssl \
         --libdir=lib          \
         shared                \
         zlib-dynamic &&
make

sed -i 's# libcrypto.a##;s# libssl.a##' Makefile

make MANDIR=/usr/share/man MANSUFFIX=ssl install &&
install -dv -m755 /usr/share/doc/openssl-1.0.1h  &&
cp -vfr doc/*     /usr/share/doc/openssl-1.0.1h


# install cyrus-sasl 2.26
# reference http://www.linuxfromscratch.org/blfs/view/svn/postlfs/cyrus-sasl.html
#cd $tar_folder
#
#wget ftp://ftp.cyrusimap.org/cyrus-sasl/cyrus-sasl-2.1.26.tar.gz
#wget http://www.linuxfromscratch.org/patches/blfs/svn/cyrus-sasl-2.1.26-fixes-1.patch
#
#tar -zxvf cyrus-sasl-2.1.26.tar.gz
#cd cyrus-sasl-2.1.26
#
#patch -Np1 -i ../cyrus-sasl-2.1.26-fixes-1.patch &&
#autoreconf -fi &&
#pushd saslauthd
#autoreconf -fi &&
#popd &&
#./configure --prefix=/usr        \
#            --sysconfdir=/etc    \
#            --enable-auth-sasldb \
#            --with-dbpath=/var/lib/sasl/sasldb2 \
#            --with-saslauthd=/var/run/saslauthd \
#            CFLAGS=-fPIC &&
#make -j1
#
#make install &&
#install -v -dm755 /usr/share/doc/cyrus-sasl-2.1.26 &&
#install -v -m644  doc/{*.{html,txt,fig},ONEWS,TODO} \
#    saslauthd/LDAP_SASLAUTHD /usr/share/doc/cyrus-sasl-2.1.26 &&
#install -v -dm700 /var/lib/sasl

yum install -y cyrus-sasl \
               cyrus-sasl-lib \
               cyrus-sasl-sql \
               cyrus-imapd \
               cyrus-imapd-utils \
               cyrus-imapd-devel \
               cyrus-sasl-plain \
               cyrus-sasl-devel 


# install apache
yum install apr apr-devel apr-util-devel apr-util-mysql libapred2
yum install httpd httpd-devel -y

# install php 5.5
cd $tar_folder
yum install bzip2-devel -y
yum install  -y
yum install compat-readline5 \
            readline-devel \
            readline-static \
            compat-readline5-devel \
            compat-readline5-static -y

wget --output-document=php-5.5.12.tar.gz http://jp1.php.net/get/php-5.5.12.tar.gz/from/this/mirror
tar zxvf php-5.5.12.tar.gz
cd php-5.5.12

./configure \
--prefix=/usr/local/php \
--with-config-file-path=/usr/local/php/etc \
--with-apxs2=/usr/sbin/apxs \
--enable-shmop \
--enable-sysvsem \
--enable-pcntl \
--with-readline \
--enable-fpm \
--with-gettext \
--enable-mbstring \
--with-iconv \
--enable-mbregex \
--with-regex=php \
--with-pcre-regex \
--with-mcrypt \
--with-mhash \
--enable-bcmath \
--with-openssl \
--enable-xml \
--with-libxml-dir \
--with-xmlrpc \
--enable-soap \
--enable-sockets \
--with-curl \
--enable-ftp \
--enable-zip \
--with-zlib \
--with-bz2 \
--with-jpeg-dir \
--with-png-dir \
--enable-exif \
--with-freetype-dir \
--with-gd \
--enable-gd-native-ttf \
--enable-calendar \
--with-pdo-mysql=mysqlnd \
--with-mysql=mysqlnd \
--with-mysqli=mysqlnd \
--enable-calendar \
--without-sqlite3 \
--without-pdo-sqlite \
--with-pear \
--enable-inline-optimization \
--disable-debug \
--disable-rpath \
--enable-shared \
--enable-opcache 

make -j8
make install
make clean

rm /etc/init.d/phpfpm -f
cp sapi/fpm/init.d.php-fpm /etc/init.d/phpfpm
chmod a+x /etc/init.d/phpfpm
chkconfig --add phpfpm
chkconfig --level 345 phpfpm on

true > /var/log/phperror.log
chown www:www /var/log/phperror.log

cp php.ini-production /usr/local/php/etc/php.ini
sed -i 's/^disable_functions =$/disable_functions =  exec,passthru,shell_exec,system,popen,curl_multi_exec,parse_ini_file,show_source,dl,openlog,syslog,readlink,symlink,link,leak,proc_get_status,popen,chroot,chgrp,chown,escapeshellcmd,escapeshellarg/g' /usr/local/php/etc/php.ini
sed -i 's/^max_execution_time = 30$/max_execution_time = 300/g' /usr/local/php/etc/php.ini
sed -i 's/^max_input_time = 60$/max_input_time = 600/g' /usr/local/php/etc/php.ini
sed -i 's/^memory_limit = 128M$/memory_limit = 512M/g' /usr/local/php/etc/php.ini
sed -i 's/^display_errors = Off$/display_errors = On/g' /usr/local/php/etc/php.ini
sed -i 's/^;error_log = syslog$/error_log = \/var\/log\/phperror.log/g' /usr/local/php/etc/php.ini
sed -i 's/^post_max_size = 8M$/post_max_size = 50M/g' /usr/local/php/etc/php.ini
sed -i 's/^;default_charset = "UTF-8"$/default_charset = "UTF-8"/g' /usr/local/php/etc/php.ini
sed -i 's/^upload_max_filesize = 2M$/upload_max_filesize = 50M/g' /usr/local/php/etc/php.ini
sed -i 's/^;date.timezone =$/date.timezone = Asia\/Shanghai/g' /usr/local/php/etc/php.ini
sed -i 's/^session.save_handler = files$/session.save_handler = memcache/g' /usr/local/php/etc/php.ini
sed -i 's/^;session.save_path = "\/tmp"$/session.save_path = "tcp:\/\/192.168.0.150:11212"/g' /usr/local/php/etc/php.ini

cat >> /usr/local/php/etc/php.ini << END
zend_extension="/usr/local/php/lib/php/extensions/no-debug-non-zts-20121212/opcache.so"
opcache.enable=1
opcache.enable_cli=1
opcache.memory_consumption=256
opcache.max_wasted_percentage=20
opcache.interned_strings_buffer=32
opcache.max_accelerated_files=65000
opcache.validate_timestamps=1
opcache.revalidate_freq=15
opcache.fast_shutdown=1
END


cp /usr/local/php/etc/php-fpm.conf.default /usr/local/php/etc/php-fpm.conf
sed -i 's/^;pid = run\/php-fpm.pid$/pid = run\/php-fpm.pid/g' /usr/local/php/etc/php-fpm.conf
sed -i 's/^;error_log = log\/php-fpm.log$/;error_log = log\/php-fpm.log/g' /usr/local/php/etc/php-fpm.conf
sed -i 's/^;log_level = notic$/log_level = warning/g' /usr/local/php/etc/php-fpm.conf
sed -i 's/^;events.mechanism = epoll$/events.mechanism = epoll/g' /usr/local/php/etc/php-fpm.conf
sed -i 's/^user = nobody$/user = www/g' /usr/local/php/etc/php-fpm.conf
sed -i 's/^group = nobody$/group = www/g' /usr/local/php/etc/php-fpm.conf
sed -i 's/^pm = dynamic$/pm = ondemand/g' /usr/local/php/etc/php-fpm.conf
sed -i 's/^pm.max_children = 5$/pm.max_children = 4000/g' /usr/local/php/etc/php-fpm.conf
sed -i 's/^pm.start_servers = 2$/pm.start_servers = 50/g' /usr/local/php/etc/php-fpm.conf
sed -i 's/^pm.min_spare_servers = 1$/pm.min_spare_servers = 30/g' /usr/local/php/etc/php-fpm.conf
sed -i 's/^pm.max_spare_servers = 3$/pm.max_spare_servers = 2000/g' /usr/local/php/etc/php-fpm.conf
sed -i 's/^;pm.process_idle_timeout = 10s;$/pm.process_idle_timeout = 10s/g' /usr/local/php/etc/php-fpm.conf
sed -i 's/^;pm.max_requests = 500$/pm.max_requests = 30000/g' /usr/local/php/etc/php-fpm.conf
sed -i 's/^;pm.status_path = \/status$/pm.status_path = \/fpmstatus/g' /usr/local/php/etc/php-fpm.conf
sed -i 's/^;access.log = log\/\$pool.access.log$/access.log = \/var\/log\/$pool.access.log/g' /usr/local/php/etc/php-fpm.conf
sed -i 's/^;access.format = "%R - %u %t \\"%m %r%Q%q\\" %s %f %{mili}d %{kilo}M %C%%"$/access.format = "%R - %u %t  %{mili}d %C%%  %{kilo}M \\"%m %r%Q%q\\" %s %f "/g' /usr/local/php/etc/php-fpm.conf
sed -i 's/^;slowlog = log\/\$pool.log.slow$/slowlog = \/var\/log\/$pool.log.slow/g' /usr/local/php/etc/php-fpm.conf
sed -i 's/^;request_slowlog_timeout = 0$/request_slowlog_timeout = 5/g' /usr/local/php/etc/php-fpm.conf
sed -i 's/^;request_terminate_timeout = 0$/request_terminate_timeout = 900/g' /usr/local/php/etc/php-fpm.conf


export PHP_PREFIX="/usr/local/php"

cd $tar_folder
wget http://pecl.php.net/get/memcache-2.2.7.tgz
tar zxvf memcache-2.2.7.tgz
cd memcache-2.2.7
$PHP_PREFIX/bin/phpize
./configure --enable-memcache --with-php-config=$PHP_PREFIX/bin/php-config --with-zlib-dir
make &&make install
make clean
cat >> /usr/local/php/etc/php.ini << END
extension="/usr/local/php/lib/php/extensions/no-debug-non-zts-20121212/memcache.so"
END


cd $tar_folder
wget http://pecl.php.net/get/redis-2.2.5.tgz
tar zxvf redis-2.2.5.tgz
cd redis-2.2.5
$PHP_PREFIX/bin/phpize
./configure --with-php-config=$PHP_PREFIX/bin/php-config
make && make install
make clean
cat >> /usr/local/php/etc/php.ini << END
extension="/usr/local/php/lib/php/extensions/no-debug-non-zts-20121212/redis.so"
END

 
cd $tar_folder
wget http://pecl.php.net/get/mongo-1.5.2.tgz
tar zxvf mongo-1.5.2.tgz
cd mongo-1.5.2
$PHP_PREFIX/bin/phpize
./configure --enable-mongo=share --with-php-config=$PHP_PREFIX/bin/php-config
make && make install
make clean
cat >> /usr/local/php/etc/php.ini << END
extension="/usr/local/php/lib/php/extensions/no-debug-non-zts-20121212/mongo.so"
END

  
cd $tar_folder
wget http://pecl.php.net/get/imagick-3.1.2.tgz
tar zxvf imagick-3.1.2.tgz
cd  imagick-3.1.2
$PHP_PREFIX/bin/phpize
./configure --with-php-config=$PHP_PREFIX/bin/php-config
make && make install
make clean
cat >> /usr/local/php/etc/php.ini << END
extension="/usr/local/php/lib/php/extensions/no-debug-non-zts-20121212/imagick.so"
END


cd $tar_folder
wget http://pecl.php.net/get/xhprof-0.9.4.tgz
tar zxvf xhprof-0.9.4.tgz
cd xhprof-0.9.4/extension
$PHP_PREFIX/bin/phpize
./configure --with-php-config=$PHP_PREFIX/bin/php-config
make && make install
make clean
cat >> /usr/local/php/etc/php.ini << END
extension="/usr/local/php/lib/php/extensions/no-debug-non-zts-20121212/xhprof.so"
END

####################################################################################

# install postfix-2.11.0
# reference http://www.linuxfromscratch.org/blfs/view/svn/server/postfix.html

wget ftp://ftp.porcupine.org/mirrors/postfix-release/official/postfix-2.11.1.tar.gz
tar -zxvf postfix-2.11.1.tar.gz
cd postfix-2.11.1

groupadd -g 32 postfix &&
groupadd -g 33 postdrop &&
useradd -c "Postfix Daemon User" -d /var/spool/postfix -g postfix \
        -s /bin/false -u 32 postfix &&
chown -v postfix:postfix /var/mail

make CCARGS="-DNO_NIS -DUSE_TLS -I/usr/include/openssl/            \
             -DUSE_SASL_AUTH -DUSE_CYRUS_SASL -I/usr/include/sasl  \
             -DHAS_MYSQL -I/usr/local/mysql/include"               \
     AUXLIBS="-lsasl2 -lssl -lcrypto -L/usr/local/mysql/lib/ -lmysqlclient -lz -lm"  \
     makefiles &&
make

sh postfix-install -non-interactive \
   daemon_directory=/usr/lib/postfix \
   manpage_directory=/usr/share/man \
   html_directory=/usr/share/doc/postfix-2.11.1/html \
   readme_directory=/usr/share/doc/postfix-2.11.1/readme

cat >> /etc/aliases << "EOF"
# Begin /etc/aliases

MAILER-DAEMON:    postmaster
postmaster:       root

root:             <LOGIN>
# End /etc/aliases
EOF


# install clamav 
# reference http://pkgs.repoforge.org/clamav/
wget http://pkgs.repoforge.org/clamav/clamav-0.96.2-1.el4.rf.i386.rpm
wget http://pkgs.repoforge.org/clamav/clamav-devel-0.96.2-1.el4.rf.i386.rpm
wget http://pkgs.repoforge.org/clamav/clamav-db-0.96.2-1.el4.rf.i386.rpm



mv  /etc/postfix/transport.rpmsave /etc/postfix/transport
mv /etc/postfix/master.cf.rpmsave /etc/postfix/master.cf
mv /etc/postfix/main.cf.rpmsave /etc/postfix/main.cf
mv /etc/postfix/header_checks.rpmsave /etc/postfix/header_checks
mv /etc/postfix/aliases.rpmsave /etc/postfix/aliases
mv /etc/postfix/access.rpmsave /etc/postfix/access

# no use anymore
wget ftp://ftp.redhat.com/pub/redhat/linux/enterprise/5Server/en/os/SRPMS/postfix-2.3.3-2.1.el5_2.src.rpm 
rpm -ivh postfix-2.3.3-2.1.el5_2.src.rpm 
cd /usr/src/redhat/SOURCES/
wget http://vda.sourceforge.net/VDA/postfix-2.3.3-vda.patch.gz
gunzip postfix-2.3.3-vda.patch.gz  
vim /usr/src/redhat/SPECS postfix.spec

rpmbuild -ba postfix.spec

cd /usr/src/redhat/RPMS/i386/

perl -MCPAN -e 'install Date::Calc'