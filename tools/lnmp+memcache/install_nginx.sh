#!/bin/bash
# apt source update
cat > /etc/apt/sources.list <<EOF
deb http://mirrors.163.com/ubuntu/ lucid main universe restricted multiverse
deb-src http://mirrors.163.com/ubuntu/ lucid main universe restricted multiverse
deb http://mirrors.163.com/ubuntu/ lucid-security universe main multiverse restricted
deb-src http://mirrors.163.com/ubuntu/ lucid-security universe main multiverse restricted
deb http://mirrors.163.com/ubuntu/ lucid-updates universe main multiverse restricted
deb http://mirrors.163.com/ubuntu/ lucid-proposed universe main multiverse restricted
deb-src http://mirrors.163.com/ubuntu/ lucid-proposed universe main multiverse restricted
deb http://mirrors.163.com/ubuntu/ lucid-backports universe main multiverse restricted
deb-src http://mirrors.163.com/ubuntu/ lucid-backports universe main multiverse restricted
deb-src http://mirrors.163.com/ubuntu/ lucid-updates universe main multiverse restricted
EOF

apt-get update
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

mkdir -p /opt/lnmp/app/nginx/logs/
mkdir -p /opt/lnmp/app/nginx/sbin/

cd nginx-1.0.9/
./configure \
--prefix=/opt/lnmp/app/nginx \
--sbin-path=/opt/lnmp/app/nginx/sbin/ \
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

mkdir -p /opt/lnmp/app/nginx/www/herouser/
cat > /opt/lnmp/app/nginx/www/herouser/index.html<<EOF
welcome to the nginx!
It is just test!
EOF

chown -R www-data.www-data /opt/lnmp/app/nginx
chmod -R 755 /opt/lnmp/app/nginx/www 

}
InstallNginx



