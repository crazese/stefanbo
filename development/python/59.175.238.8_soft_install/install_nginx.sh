#!/bin/bash


cd /usr/local/lnmp/tar_package/nginx
cur_dir=$(pwd)

init_install()
{

cd $cur_dir
tar -zxvf m4-1.4.9.tar.gz
cd m4-1.4.9
./configure --prefix=/usr/local/lnmp/app/init
make && make install
cd ../ 


cd $cur_dir
tar zxvf autoconf-2.13.tar.gz
cd autoconf-2.13/
./configure --prefix=/usr/local/lnmp/app/init
make && make install
cd ../

cd $cur_dir
tar zxvf libiconv-1.14.tar.gz
cd libiconv-1.14/
./configure --prefix=/usr/local/lnmp/app/init
make && make install
cd ../

cd $cur_dir
tar zxvf libmcrypt-2.5.8.tar.gz
cd libmcrypt-2.5.8/
./configure --prefix=/usr/local/lnmp/app/init
make && make install
/sbin/ldconfig
cd libltdl/
./configure --enable-ltdl-install
make && make install
cd ../../

cd $cur_dir
tar zxvf libxml2-2.7.8.tar.gz
cd libxml2-2.7.8/
./configure --prefix=/usr/local/lnmp/app/init
make && make install
cd ../
}
#init_install

InstallNginx() {
echo "============================Install Nginx================================="
groupadd www-data
useradd -s /sbin/nologin -g www-data www-data



# nginx
cd $cur_dir

#tar -zxvf pcre-8.30.tar.gz
#cd pcre-8.30
#./configure --prefix=/usr/local/lnmp/app/init
#make && make install 
#cd ..#

#tar -zxvf openssl-1.0.1e.tar.gz
#cd openssl-1.0.1e
#./config --prefix=/usr/local/lnmp/app/init
#make && make install
#cd ..#

#tar -zxvf zlib-1.2.8.tar.gz
#cd zlib-1.2.8
#./configure --prefix=/usr/local/lnmp/app/init
#make && make install
#cd ..#

#tar zxvf nginx-1.0.9.tar.gz

mkdir -p /usr/local/lnmp/app/nginx/logs/
mkdir -p /usr/local/lnmp/app/nginx/sbin/

cd nginx-1.0.9/
./configure \
--prefix=/usr/local/lnmp/app/nginx \
--sbin-path=/usr/local/lnmp/app/nginx/sbin/ \
--pid-path=/usr/local/lnmp/app/nginx/nginx.pid \
--user=www-data \
--group=www-data \
--with-http_stub_status_module \
--with-http_ssl_module \
--with-http_gzip_static_module \
--with-pcre=/usr/local/lnmp/tar_package/nginx/pcre-8.30/ \
--with-openssl=/usr/local/lnmp/tar_package/nginx/openssl-1.0.1e/ \
--with-zlib=/usr/local/lnmp/tar_package/nginx/zlib-1.2.8

sed -i 's#./configure#./configure --prefix=/usr/local/lnmp/app/init#' ./objs/Makefile
sed -i 's#./configure --prefix=/usr/local/lnmp/app/init --disable-shared#./configure --prefix=/usr/local/lnmp/app/init#' ./objs/Makefile
make && make install
cd ../

cd $cur_dir
mkdir -p /usr/local/lnmp/app/nginx/conf/sites-enabled
mkdir -p /usr/local/lnmp/app/nginx/conf/sites-available
cat > /usr/local/lnmp/app/nginx/conf/nginx.conf <<EOF
user www-data;
worker_processes 4;
pid /usr/local/lnmp/app/nginx/nginx.pid;

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

	include /usr/local/lnmp/app/nginx/conf/mime.types;
	default_type application/octet-stream;

	##
	# Logging Settings
	##

	access_log /usr/local/lnmp/app/nginx/logs/access.log;
	error_log /usr/local/lnmp/app/nginx/logs/error.log;

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

	include /usr/local/lnmp/app/nginx/conf/conf.d/*.conf;
	include /usr/local/lnmp/app/nginx/conf/sites-enabled/*;
}
EOF

mkdir /usr/local/lnmp/app/nginx/bin -p
cat > /usr/local/lnmp/app/nginx/bin/nginx <<EOF
#!/bin/sh

### BEGIN INIT INFO
# Provides:       nginx
# Required-Start:    $local_fs $remote_fs $network $syslog $named
# Required-Stop:     $local_fs $remote_fs $network $syslog $named
# Default-Start:     2 3 4 5
# Default-Stop:      0 1 6
# Short-Description: starts the nginx web server
# Description:       starts nginx using start-stop-daemon
### END INIT INFO

PATH=/usr/local/lnmp/app/nginx/sbin:/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin
DAEMON=/usr/local/lnmp/app/nginx/sbin/nginx
NAME=nginx
DESC=nginx

# Include nginx defaults if available
if [ -f /etc/default/nginx ]; then
        . /etc/default/nginx
fi

test -x $DAEMON || exit 0

set -e

. /lib/lsb/init-functions

PID=$(awk -F'[ ;]' '/[^#]pid/ {print $2}' /usr/local/lnmp/app/nginx/conf/nginx.conf)
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
chmod +x /usr/local/lnmp/app/nginx/bin/nginx

cat > /usr/local/lnmp/app/nginx/conf/sites-enabled/zabbix <<EOF
server {
        listen   82 default;
        server_name  localhost;

        access_log  /usr/local/lnmp/app/nginx/logs/localhost.82.access.log;
        root   /usr/local/lnmp/app/nginx/www/zabbix;
        index  index.php index.html index.htm;
        
        location /doc {
                root   /usr/share;
                autoindex on;
                allow 127.0.0.1;
                deny all;
        }

        location /images {
                autoindex off;
        }

        location ~ \.php$ {
                fastcgi_pass   127.0.0.1:9000;
                fastcgi_index  index.php;
                fastcgi_param  SCRIPT_FILENAME  /usr/local/lnmp/app/nginx/www/herouser$fastcgi_script_name;
                include fastcgi_params;
        }
}
EOF

mkdir -p /usr/local/lnmp/app/nginx/www/zabbix
cat > /usr/local/lnmp/app/nginx/www/zabbix/index.html<<EOF
welcome to the nginx!
It is just test!
EOF

chown -R www-data.www-data /usr/local/lnmp/app/nginx
chmod -R 755 /usr/local/lnmp/app/nginx/www 

}
InstallNginx



