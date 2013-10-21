#/bin/bash
#
# get the xtrabackup.tar.gz from ftp server "hut.jofgame.com"
#
#ftp user and paswd
server="hut.jofgame.com"
username="zhubo"
passwd="Jof1Game8yzl"
xtrabackup_PACKAGE=percona-xtrabackup-2.1.5.tar.gz
easy_install=setuptools-0.6c11-py2.6.egg
TEMP=/tmp/XtraBackup
mkdir -p $TEMP
cd $TEMP

ftp -n $server <<EOF
prompt off
user $username $passwd
get $xtrabackup_PACKAGE
get $easy_install
bye
EOF

#
#Prerequisites
#The following packages and tools must be installed to compile Percona XtraBackup from source. 
#These might vary from system to system.
#There is ubuntu 10.04 server x64

apt-get install debhelper autotools-dev libaio-dev wget automake \
  libtool bison libncurses-dev libz-dev cmake bzr libgcrypt11-dev libmysqld-dev python-dev -y

#build the python eash install 
#Prepare the environment
sh $easy_install
eash_install MySQL-python
easy_install mysql-connector-python

#build the mysql version 5.1
cd percona-*
PWD=$(pwd)
$PWD/utils/build.sh plugin 

# After this you’ll need to copy innobackupex 
# (in the root folder used to retrieve Percona XtraBackup) 
# and the corresponding xtrabackup binary (in the src folder) 
# to some directory listed in the PATH environment variable
cp $PWD/innobackupex /usr/bin/
cp $PWD/src/xbcrypt /usr/bin/
cp $PWD/src/xbstream /usr/bin/
cp $PWD/src/xtrabackup_plugin /usr/bin/xtrabackup 



#Perform a full backup
<<<<<<< HEAD
#mkdir -p /data/backup/mysql
#xtrabackup_plugin --backup --datadir=/var/lib/mysql --target-dir=/data/backups/mysql/
=======
mkdir -p /data/backup/mysql
xtrabackup --backup --datadir=/var/lib/mysql --target-dir=/data/backups/mysql/
>>>>>>> e9e3173a2984b82a33321b46ee102144defe0a5a



