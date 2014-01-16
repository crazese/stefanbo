#!/bin/bash
# directory init
mkdir -p /opt/lnmp/tar_package
mkdir -p /opt/lnmp/app/
mkdir -p /opt/lnmp/sbin
mkdir -p /opt/lnmp/lib 
# global variable
export PATH=/opt/lnmp/sbin:$PATH

# ftp 
cd /opt/lnmp/tar_package
cur_dir=$(pwd)
# ftp get
ftp_get() {
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
ftp_get
# init install from source 
init_install()
{
apt-get update
apt-get upgrade -y
apt-get install -y build-essential gcc g++ make
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

InstallNginx() {
echo "============================Install Nginx================================="
groupadd www-data
useradd -s /sbin/nologin -g www-data www-data

mkdir -p /opt/lnmp/app/nginx/www/
chown -R www-data.www-data /opt/lnmp/app/nginx/www 
chmod -R 755 /opt/lnmp/app/nginx/www 

# nginx
cd $cur_dir
tar -zxvf pcre-8.30.tar.gz
cd pcre-8.30
./configure --prefix=/opt/lnmp/app/pcre8 
make && make install 
cd ..

tar -zxvf openssl-1.0.1e.tar.gz
cd openssl-1.0.1e
./config --prefix=/opt/lnmp/app/openssl 
make && make install
cd ..

tar -zxvf zlib-1.2.8.tar.gz
cd zlib-1.2.8
./configure --prefix=/opt/lnmp/app/zlib 
make && make install
cd ..

tar zxvf nginx-1.0.9.tar.gz

mkdir -p /opt/lnmp/log/nginx/

cd nginx-1.0.9/
./configure \
--prefix=/opt/lnmp/app/nginx \
--sbin-path=/opt/lnmp/sbin/ \
--pid-path=/opt/lnmp/app/nginx/nginx.pid \
--user=www-data \
--group=www-data \
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
mkdir -p /opt/lnmp/app/nginx/conf/sites-enabled
mkdir -p /opt/lnmp/app/nginx/conf/sites-available
cat > /opt/lnmp/app/nginx/conf/nginx.conf <<EOF
user www-data;
worker_processes 4;
pid /opt/lnmp/app/nginx/nginx.pid;

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

	include /opt/lnmp/app/nginx/conf/mime.types;
	default_type application/octet-stream;

	##
	# Logging Settings
	##

	access_log /opt/lnmp/app/nginx/logs/access.log;
	error_log /opt/lnmp/app/nginx/logs/error.log;

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

	include /opt/lnmp/app/nginx/conf/conf.d/*.conf;
	include /opt/lnmp/app/nginx/conf/sites-enabled/*;
}
EOF

mkdir /opt/lnmp/app/nginx/bin -p
cat > /opt/lnmp/app/nginx/bin/nginx <<EOF
#!/bin/sh

### BEGIN INIT INFO
# Provides:	  nginx
# Required-Start:    $local_fs $remote_fs $network $syslog $named
# Required-Stop:     $local_fs $remote_fs $network $syslog $named
# Default-Start:     2 3 4 5
# Default-Stop:      0 1 6
# Short-Description: starts the nginx web server
# Description:       starts nginx using start-stop-daemon
### END INIT INFO

PATH=/opt/lnmp/app/nginx/sbin/:$PATH
DAEMON=/opt/lnmp/app/nginx/sbin/nginx
NAME=nginx
DESC=nginx

# Include nginx defaults if available
if [ -f /etc/default/nginx ]; then
	. /etc/default/nginx
fi

test -x $DAEMON || exit 0

set -e

. /lib/lsb/init-functions

PID=$(awk -F'[ ;]' '/[^#]pid/ {print $2}' /opt/lnmp/app/nginx/conf/nginx.conf)
if [ -z "$PID" ]
then
  PID=/var/run/nginx.pid
fi

# Check if the ULIMIT is set in /etc/default/nginx
if [ -n "$ULIMIT" ]; then
  # Set the ulimits
  ulimit $ULIMIT
fi

test_nginx_config() {
		$DAEMON -t $DAEMON_OPTS >/dev/null 2>&1
		retvar=$?
		if [ $retvar -ne 0 ]
		then
			exit $retvar
		fi
}

start() {
		start-stop-daemon --start --quiet --pidfile $PID \
			--retry 5 --exec $DAEMON --oknodo -- $DAEMON_OPTS
}

stop() {
		start-stop-daemon --stop --quiet --pidfile $PID \
			--retry 5 --oknodo --exec $DAEMON
}

case "$1" in
	start)
		test_nginx_config
		log_daemon_msg "Starting $DESC" "$NAME"
		start
		log_end_msg $?
		;;

	stop)
		log_daemon_msg "Stopping $DESC" "$NAME"
		stop
		log_end_msg $?
		;;

	restart|force-reload)
		test_nginx_config
		log_daemon_msg "Restarting $DESC" "$NAME"
		stop
		sleep 1
		start
		log_end_msg $?
		;;

	reload)
		test_nginx_config
		log_daemon_msg "Reloading $DESC configuration" "$NAME"
		start-stop-daemon --stop --signal HUP --quiet --pidfile $PID \
			--oknodo --exec $DAEMON
		log_end_msg $?
		;;

	configtest|testconfig)
		log_daemon_msg "Testing $DESC configuration"
		if test_nginx_config; then
			log_daemon_msg "$NAME"
		else
			exit $?
		fi
		log_end_msg $?
		;;

	status)
		status_of_proc -p $PID "$DAEMON" nginx
		;;

	*)
		echo "Usage: $NAME {start|stop|restart|reload|force-reload|status|configtest}" >&2
		exit 1
		;;
esac

exit 0
EOF
chmod +x /opt/lnmp/app/nginx/bin/nginx

cat > /opt/lnmp/app/nginx/conf/sites-enabled/herouser <<EOF
#configure the nginx and php

server {
        listen   80 default;
        server_name  localhost;

        access_log  /opt/lnmp/app/nginx/logs/localhost.80.access.log;
        root   /opt/lnmp/app/nginx/www/herouser;
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
                fastcgi_param  SCRIPT_FILENAME  /opt/lnmp/app/nginx/www/herouser$fastcgi_script_name;
                include fastcgi_params;
        }
}
EOF
}
InstallNginx

install_mysql() {
echo "============================Install MySQL================================="
# lib available
cat >> /etc/ld.so.conf.d/libc.conf <<EOF
/opt/lnmp/app/libiconv/lib
/opt/lnmp/app/libmcrypt/lib
/opt/lnmp/app/libxml2/lib
/opt/lnmp/app/ncurses/lib
/opt/lnmp/app/openssl/lib
/opt/lnmp/app/pcre8/lib
/opt/lnmp/app/zlib/lib
/opt/lnmp/app/termcap/lib
EOF

ldconfig 

apt-get install libncurses5-dev -y

tar -zxvf ncurses.tar.gz 
cd ncurses-5.9
./configure --prefix=/opt/lnmp/app/ncurses \
--with-shared \
--with-profile \
--with-termlib \
--with-ticlib \
--without-debug
make && make install
cd ..

mkdir -p /opt/lnmp/app/mysql/data -p 
mkdir -p /opt/lnmp/app/mysql/etc/init.d -p
mkdir -p /opt/lnmp/app/mysql/tmp -p
mkdir -p /opt/lnmp/app/mysql/log -p

groupadd mysql 
useradd -g mysql -d /opt/lnmp/app/mysql/ -M mysql 

cd $cur_dir
tar -zxvf mysql-5.0.27.tar.gz
cd mysql-5.0.27


./configure \
--prefix=/opt/lnmp/app/mysql \
--with-unix-socket-path=/opt/lnmp/app/mysql/mysql.sock \
--localstatedir=/opt/lnmp/app/mysql/data \
--with-mysqld-user=mysql \
--sysconfdir=/opt/lnmp/app/mysql/etc/ \
--with-charset=utf8

make -j2 && make install
cd ../


#cp /opt/lnmp/app/mysql/share/mysql/my-medium.cnf /opt/lnmp/app/mysql/etc/my.cnf
#sed -i 's/skip-locking/skip-external-locking/g' /opt/lnmp/app/mysql/etc/my.cnf
#sed -i 's:#innodb:innodb:g' /opt/lnmp/app/mysql/etc/my.cnf

cat > /opt/lnmp/app/mysql/etc/my.cnf <<EOF
#
# The MySQL database server configuration file.
#
# You can copy this to one of:
# - "/etc/mysql/my.cnf" to set global options,
# - "~/.my.cnf" to set user-specific options.
# 
# One can use all long options that the program supports.
# Run program with --help to get a list of available options and with
# --print-defaults to see which it would actually understand and use.
#
# For explanations see
# http://dev.mysql.com/doc/mysql/en/server-system-variables.html

# This will be passed to all mysql clients
# It has been reported that passwords should be enclosed with ticks/quotes
# escpecially if they contain "#" chars...
# Remember to edit /etc/mysql/debian.cnf when changing the socket location.
[client]
port            = 3306
socket          = /opt/lnmp/app/mysql/mysqld.sock

# Here is entries for some specific programs
# The following values assume you have at least 32M ram

# This was formally known as [safe_mysqld]. Both versions are currently parsed.
[mysqld_safe]
socket          = /opt/lnmp/app/mysql/mysqld.sock
nice            = 0

[mysqld]
#
# * Basic Settings
#
user            = mysql
pid-file        = /opt/lnmp/app/mysql/mysqld.pid
socket          = /opt/lnmp/app/mysql/mysqld.sock
port            = 3306
basedir         = /opt/lnmp/app/mysql
datadir         = /opt/lnmp/app/mysql/data/
tmpdir          = /opt/lnmp/app/mysql/tmp/
lc-messages-dir = /opt/lnmp/app/mysql/share/mysql
skip-external-locking
#
# Instead of skip-networking the default is now to listen only on
# localhost which is more compatible and is not less secure.
bind-address            = 127.0.0.1
#
# * Fine Tuning
#
key_buffer              = 16M
max_allowed_packet      = 16M
thread_stack            = 192K
thread_cache_size       = 8
# This replaces the startup script and checks MyISAM tables if needed
# the first time they are touched
myisam-recover         = BACKUP
#max_connections        = 100
#table_cache            = 64
#thread_concurrency     = 10
#
# * Query Cache Configuration
#
query_cache_limit       = 1M
query_cache_size        = 16M
#
# * Logging and Replication
#
# Both location gets rotated by the cronjob.
# Be aware that this log type is a performance killer.
# As of 5.1 you can enable the log at runtime!
general_log_file        = /opt/lnmp/app/mysql/log/mysql.log
general_log             = 1
#
# Error log - should be very few entries.
#
log_error = /opt/lnmp/app/mysql/log/error.log
#
# Here you can see queries with especially long duration
#log_slow_queries       = /opt/lnmp/app/mysql/log/mysql-slow.log
#long_query_time = 2
#log-queries-not-using-indexes
#
# The following can be used as easy to replay backup logs or for replication.
# note: if you are setting up a replication slave, see README.Debian about
#       other settings you may need to change.
#server-id              = 1
#log_bin                        = /opt/lnmp/app/mysql/log/mysql-bin.log
expire_logs_days        = 10
max_binlog_size         = 100M
#binlog_do_db           = include_database_name
#binlog_ignore_db       = include_database_name
#
# * InnoDB
#
# InnoDB is enabled by default with a 10MB datafile in /var/lib/mysql/.
# Read the manual for more InnoDB related options. There are many!
#
# * Security Features
#
# Read the manual, too, if you want chroot!
# chroot = /var/lib/mysql/
#
# For generating SSL certificates I recommend the OpenSSL GUI "tinyca".
#
# ssl-ca=/etc/mysql/cacert.pem
# ssl-cert=/etc/mysql/server-cert.pem
# ssl-key=/etc/mysql/server-key.pem



[mysqldump]
quick
quote-names
max_allowed_packet      = 16M

[mysql]
#no-auto-rehash # faster start of mysql but no tab completition

[isamchk]
key_buffer              = 16M

#
# * IMPORTANT: Additional settings that can override those from this file!
#   The files must end with '.cnf', otherwise they'll be ignored.
#
#!includedir /etc/mysql/conf.d/
EOF


/opt/lnmp/app/mysql/bin/mysql_install_db --user=mysql --basedir=/opt/lnmp/app/mysql --datadir=/opt/lnmp/app/mysql/data
ln -s /opt/lnmp/app/mysql/share/mysql /usr/share/

chown -R mysql /opt/lnmp/app/mysql/var
chgrp -R mysql /opt/lnmp/app/mysql/.
cp /opt/lnmp/app/mysql/share/mysql/mysql.server /opt/lnmp/app/mysql/etc/init.d/mysql
chmod 755 /opt/lnmp/app/mysql/etc/init.d/mysql

cat >> /etc/ld.so.conf.d/mysql.conf<<EOF
/opt/lnmp/app/mysql/lib/mysql
/opt/lnmp/app/lib
EOF
ldconfig

export PATH=/opt/lnmp/app/mysql/bin:$PATH

/opt/lnmp/app/mysql/etc/init.d/mysql start
/opt/lnmp/app/mysql/bin/mysqladmin -u root password 123456

cat > /tmp/mysql_sec_script<<EOF
use mysql;
update user set password=password('123456') where user='root';
delete from user where not (user='root') ;
delete from user where user='root' and password=''; 
drop database test;
DROP USER ''@'%';
flush privileges;
EOF

/opt/lnmp/app/mysql/bin/mysql -u root -p123456 -h localhost < /tmp/mysql_sec_script

rm -f /tmp/mysql_sec_script

/etc/init.d/mysql restart
/etc/init.d/mysql stop
}
install_mysql

configure_mysql() {
--with-named-curses-libs=/opt/lnmp/app/ncurses/lib/libncurses.so \
################################################
sed -i '/^LIBS/{s/$/ -ldl/}' vio/Makefile
sed -i '/^LIBS/{s/$/ -ldl/}' client/Makefile
sed -i '/^LIBS/{s/$/ -ldl/}' tests/Makefile

#5.1
./configure --prefix=/opt/lnmp/app/mysql \
--sysconfdir=/opt/lnmp/app/mysql/etc/ \
--localstatedir=/opt/lnmp/app/mysql/data \
--enable-assembler \
--enable-local-infile \
--enable-thread-safe-client \
--with-mysqld-user=mysql \
--with-big-tables \
--with-plugins=partition,innobase,innodb_plugin \
--enable-local-infile \
--with-charset=utf8 \
--with-collation=utf8_general_ci \
--with-extra-charset=all \
--with-zlib-dir=/opt/lnmp/app/zlib \
--with-named-curses-libs=/opt/lnmp/app/ncurses/lib/libncurses.so

--with-big-tables \
--with-plugins=partition,innobase,innodb_plugin \
--with-mysqld-ldflags=-all-static \
--with-client-ldflags=-all-static \
--with-zlib-dir=/opt/lnmp/app/zlib \
--with-openssl=/opt/lnmp/app/openssl \
--with-openssl-libs=/opt/lnmp/app/openssl/lib \
--with-openssl-includes=/opt/lnmp/app/openssl/include
##########################################################

#5.0.27
./configure \
--prefix=/opt/lnmp/app/mysql \
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
--with-plugins=innobase \
--with-named-curses-libs=/opt/lnmp/app/ncurses/lib/libncurses.so

# 5.5
/opt/lnmp/app/cmake/bin/cmake \
-DCMAKE_INSTALL_PREFIX=/opt/lnmp/app/mysql \
-DMYSQL_DATADIR=/opt/lnmp/app/mysql/data \
-DSYSCONFDIR=/opt/lnmp/app/mysql/etc \
-DEXTRA_CHARSETS=all \
-DDEFAULT_CHARSET=utf8 \
-DDEFAULT_COLLATION=utf8_general_ci \
-DENABLED_LOCAL_INFILE=1 \
-DWITH_READLINE=1 \
-DWITH_DEBUG=0 \
-DWITH_EMBEDDED_SERVER=1 \
-DWITH_INNOBASE_STORAGE_ENGINE=1 \
-DCURSES_LIBRARY=/opt/lnmp/app/ncurses/lib/libncurses.so \
-DCURSES_INCLUDE_PATH=/opt/lnmp/app/ncurses/include

cmake . \
-DCMAKE_INSTALL_PREFIX=/usr/mysql \
-DMYSQL_DATADIR=/usr/mysql/data
-DDEFAULT_CHARSET=utf8 \
-DDEFAULT_COLLATION=utf8_general_ci \
-DMYSQL_UNIX_ADDR=/tmp/mysqld.sock \
-DWITH_DEBUG=0 \
-DWITH_INNOBASE_STORAGE_ENGINE=1

OPENSSL_ROOT_DIR (missing:  OPENSSL_LIBRARIES OPENSSL_INCLUDE_DIR) 



/opt/lnmp/tar_package/mysql/scripts/mysql_install_db --user=mysql --defaults-file=/opt/lnmp/app/mysql/etc/my.cnf --datadir=/opt/lnmp/app/mysql/data --basedir=/opt/lnmp/app/mysql


#5.0
./configure \
--prefix=/opt/lnmp/app/mysql \
--with-unix-socket-path=/opt/lnmp/app/mysql/mysql.sock \
--localstatedir=/opt/lnmp/app/mysql/data \
--enable-assembler \
--enable-thread-safe-client \
--with-mysqld-user=mysql \
--with-big-tables \
--without-debug \
--with-pthread \
--enable-assembler \
--with-extra-charsets=complex \
--sysconfdir=/opt/lnmp/app/mysql/etc/ \
--with-readline \
--with-ssl \
--with-embedded-server \
--enable-local-infile \
--with-plugins=partition,innobase \
--with-plugin-PLUGIN \
--with-mysqld-ldflags=-all-static \
--with-client-ldflags=-all-static
}

# mysql test
