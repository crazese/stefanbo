#!/bin/bash
install_mysql() {
echo "============================Install MySQL================================="
# lib available
#cat >> /etc/ld.so.conf.d/libc.conf <<EOF
#/opt/lnmp/app/libiconv/lib
#/opt/lnmp/app/libmcrypt/lib
#/opt/lnmp/app/libxml2/lib
#/opt/lnmp/app/ncurses/lib
#/opt/lnmp/app/openssl/lib
#/opt/lnmp/app/pcre8/lib
#/opt/lnmp/app/zlib/lib
#/opt/lnmp/app/termcap/lib
#EOF


ldconfig 
cd /opt/lnmp/tar_package
apt-get install -y build-essential gcc g++ make
apt-get install libncurses5-dev -y
cur_dir=$(pwd)
apt-get install libxp-dev libmotif-dev libxt-dev libstdc++6 -y

tar -zxvf ncurses.tar.gz 
cd ncurses-5.9
./configure --prefix=/opt/lnmp/app/ncurses \
--with-shared \
--with-profile \
--with-termlib \
--with-ticlib \
--without-debug
make -j2 && make install
cd ..

mkdir -p /opt/lnmp/app/mysql/data -p 
mkdir -p /opt/lnmp/app/mysql/etc/init.d -p
mkdir -p /opt/lnmp/app/mysql/tmp -p
mkdir -p /opt/lnmp/app/mysql/log -p

groupadd mysql 
useradd -g mysql -d /opt/lnmp/app/mysql/ -M mysql 

#cd $cur_dir
#tar -zxvf mysql-5.0.27.tar.gz
#cd mysql-5.0.27#
#

#./configure \
#--prefix=/opt/lnmp/app/mysql \
#--with-unix-socket-path=/opt/lnmp/app/mysql/mysql.sock \
#--localstatedir=/opt/lnmp/app/mysql/data \
#--sysconfdir=/opt/lnmp/app/mysql/etc/ \
#--enable-assembler \
#--enable-local-infile \
#--enable-thread-safe-client \
#--with-mysqld-user=mysql \
#--with-big-tables \
#--with-plugins=partition,innobase,innodb_plugin \
#--with-charset=utf8 \
#--with-collation=utf8_general_ci \
#--with-extra-charset=all#
#

# \
#--with-zlib-dir=/opt/lnmp/app/zlib \
#--with-named-curses-libs=/opt/lnmp/app/ncurses/lib/libncurses.so
#--with-client-ldflags="-all-static -ltinfo" \
#--with-mysqld-ldflags="-all-static -ltinfo" \#
#

#make -j2 && make install
#cd ../


#cp /opt/lnmp/app/mysql/share/mysql/my-medium.cnf /opt/lnmp/app/mysql/etc/my.cnf
#sed -i 's/skip-locking/skip-external-locking/g' /opt/lnmp/app/mysql/etc/my.cnf
#sed -i 's:#innodb:innodb:g' /opt/lnmp/app/mysql/etc/my.cnf

cd $cur_dir
tar -zxvf cmake-2.8.12.1.tar.gz
cd cmake-2.8.12.1
./configure --prefix=/opt/lnmp/app/cmake
make -j2 && make install
cd ..

tar -zxvf mysql-5.5.34.tar.gz
cd mysql-5.5.34
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
-DWITH_INNOBASE_STORAGE_ENGINE=1

make -j2 && make install 


cat > /opt/lnmp/app/mysql/etc/my.cnf <<EOF
# Example MySQL config file for medium systems.
#
# This is for a system with little memory (32M - 64M) where MySQL plays
# an important part, or systems up to 128M where MySQL is used together with
# other programs (such as a web server)
#
# MySQL programs look for option files in a set of
# locations which depend on the deployment platform.
# You can copy this option file to one of those
# locations. For information about these locations, see:
# http://dev.mysql.com/doc/mysql/en/option-files.html
#
# In this file, you can use all long options that a program supports.
# If you want to know which options a program supports, run the program
# with the "--help" option.

# The following options will be passed to all MySQL clients
[client]
#password       = your_password
port            = 3306
socket          = /opt/lnmp/app/mysql/mysql.sock

# Here follows entries for some specific programs

# The MySQL server
[mysqld]
port            = 3306
socket          = /opt/lnmp/app/mysql/mysql.sock
skip-external-locking
key_buffer_size = 16M
max_allowed_packet = 1M
table_open_cache = 64
sort_buffer_size = 512K
net_buffer_length = 8K
read_buffer_size = 256K
read_rnd_buffer_size = 512K
myisam_sort_buffer_size = 8M

# Don't listen on a TCP/IP port at all. This can be a security enhancement,
# if all processes that need to connect to mysqld run on the same host.
# All interaction with mysqld must be made via Unix sockets or named pipes.
# Note that using this option without enabling named pipes on Windows
# (via the "enable-named-pipe" option) will render mysqld useless!
# 
#skip-networking

# Replication Master Server (default)
# binary logging is required for replication
log-bin=mysql-bin

# binary logging format - mixed recommended
binlog_format=mixed

# required unique id between 1 and 2^32 - 1
# defaults to 1 if master-host is not set
# but will not function as a master if omitted
server-id       = 1

# Replication Slave (comment out master section to use this)
#
# To configure this host as a replication slave, you can choose between
# two methods :
#
# 1) Use the CHANGE MASTER TO command (fully described in our manual) -
#    the syntax is:
#
#    CHANGE MASTER TO MASTER_HOST=<host>, MASTER_PORT=<port>,
#    MASTER_USER=<user>, MASTER_PASSWORD=<password> ;
#
#    where you replace <host>, <user>, <password> by quoted strings and
#    <port> by the master's port number (3306 by default).
#
#    Example:
#
#    CHANGE MASTER TO MASTER_HOST='125.564.12.1', MASTER_PORT=3306,
#    MASTER_USER='joe', MASTER_PASSWORD='secret';
#
# OR
#
# 2) Set the variables below. However, in case you choose this method, then
#    start replication for the first time (even unsuccessfully, for example
#    if you mistyped the password in master-password and the slave fails to
#    connect), the slave will create a master.info file, and any later
#    change in this file to the variables' values below will be ignored and
#    overridden by the content of the master.info file, unless you shutdown
#    the slave server, delete master.info and restart the slaver server.
#    For that reason, you may want to leave the lines below untouched
#    (commented) and instead use CHANGE MASTER TO (see above)
#
# required unique id between 2 and 2^32 - 1
# (and different from the master)
# defaults to 2 if master-host is set
# but will not function as a slave if omitted
#server-id       = 2
#
# The replication master for this slave - required
#master-host     =   <hostname>
#
# The username the slave will use for authentication when connecting
# to the master - required
#master-user     =   <username>
#
# The password the slave will authenticate with when connecting to
# the master - required
#master-password =   <password>
#
# The port the master is listening on.
# optional - defaults to 3306
#master-port     =  <port>
#
# binary logging - not required for slaves, but recommended
#log-bin=mysql-bin

# Uncomment the following if you are using InnoDB tables
innodb_data_home_dir = /opt/lnmp/app/mysql/data
innodb_data_file_path = ibdata1:10M:autoextend
innodb_log_group_home_dir = /opt/lnmp/app/mysql/data
# You can set .._buffer_pool_size up to 50 - 80 %
# of RAM but beware of setting memory usage too high
innodb_buffer_pool_size = 16M
innodb_additional_mem_pool_size = 2M
# Set .._log_file_size to 25 % of buffer pool size
innodb_log_file_size = 5M
innodb_log_buffer_size = 8M
innodb_flush_log_at_trx_commit = 1
innodb_lock_wait_timeout = 50

[mysqldump]
quick
max_allowed_packet = 16M

[mysql]
no-auto-rehash
# Remove the next comment character if you are not familiar with SQL
#safe-updates

[myisamchk]
key_buffer_size = 20M
sort_buffer_size = 20M
read_buffer = 2M
write_buffer = 2M

[mysqlhotcopy]
interactive-timeout
EOF

chmod +x /opt/lnmp/tar_package/mysql-5.5.34/scripts/mysql_install_db 

/opt/lnmp/tar_package/mysql/mysql-5.5.34/scripts/mysql_install_db \
--user=mysql \
--defaults-file=/opt/lnmp/app/mysql/etc/my.cnf \
--datadir=/opt/lnmp/app/mysql/data \
--basedir=/opt/lnmp/app/mysql

cp /opt/lnmp/tar_package/mysql-5.5.34/support-files/mysql.server /opt/lnmp/app/mysql/etc/init.d/mysql 
chmod +x /opt/lnmp/app/mysql/etc/init.d/mysql 
chown -R mysql.mysql /opt/lnmp/app/mysql 

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
/opt/lnmp/app/mysql/etc/init.d/mysql stop
/opt/lnmp/app/mysql/etc/init.d/mysql start
}
install_mysql