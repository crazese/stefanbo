#!/bin/bash
#init system on ubuntu server 10.04
# update the apt-get sourcelist to 163 source and backup the previous list
cp /etc/apt/sources.list /etc/apt/sources.list.bak 
cat > /etc/apt/sources.list <<EOF
deb http://mirrors.163.com/ubuntu/ precise main universe restricted multiverse
deb http://mirrors.163.com/ubuntu/ precise-security universe main multiverse restricted
deb http://mirrors.163.com/ubuntu/ precise-updates universe main multiverse restricted
deb http://mirrors.163.com/ubuntu/ precise-proposed universe main multiverse restricted
deb http://mirrors.163.com/ubuntu/ precise-backports universe main multiverse restricted
EOF

apt-get -y update

apt-get -y remove apache2 apache2-doc apache2-utils apache2.2-common apache2.2-bin apache2-mpm-prefork apache2-doc apache2-mpm-worker mysql-client mysql-server mysql-common php5 php5-common php5-cgi php5-mysql php5-curl php5-gd
dpkg -p apache2 apache2-doc apache2-mpm-prefork apache2-utils apache2.2-common libmysqlclient15off libmysqlclient15-dev mysql-common php5 php5-common php5-cgi php5-mysql php5-curl php5-gd

dpkg -l |grep mysql 
dpkg -P libmysqlclient15off libmysqlclient15-dev mysql-common 
dpkg -l |grep apache 
dpkg -P apache2 apache2-doc apache2-mpm-prefork apache2-utils apache2.2-common
dpkg -l |grep php 
dpkg -P php5 php5-common php5-cgi php5-mysql php5-curl php5-gd
apt-get purge `dpkg -l | grep php| awk '{print $2}'`

# Install needed packages
apt-get install gcc g++ make curl vim zip unzip wget openssl libssl0.9.8 libssl-dev libpcre3 libpcre3-dev<<EOF
y
y
EOF
apt-get install libncurses5-dev -y

if [ ! -z "`cat /etc/issue | grep 13`" ];then
       apt-get -y install libcloog-ppl1
elif [ ! -z "`cat /etc/issue | grep 10`" ];then
       apt-get -y install libcloog-ppl0
fi

# history size 
sed -i 's/HISTSIZE=.*$/HISTSIZE=100/g' ~/.bashrc 
[ -z "`cat ~/.bashrc | grep history-timestamp`" ] && echo "export PROMPT_COMMAND='{ msg=\$(history 1 | { read x y; echo \$y; });user=\$(whoami); echo \$(date \"+%Y-%m-%d %H:%M:%S\"):\$user:\`pwd\`/:\$msg ---- \$(who am i); } >> /tmp/\`hostname\`.\`whoami\`.history-timestamp'" >> ~/.bashrc

# /etc/security/limits.conf
[ -z "`cat /etc/security/limits.conf | grep 'nproc 65535'`" ] && cat >> /etc/security/limits.conf <<EOF
* soft nproc 65535
* hard nproc 65535
* soft nofile 65535
* hard nofile 65535
EOF
[ -z "`cat /etc/rc.local | grep 'ulimit -SH 65535'`" ] && echo "ulimit -SH 65535" >> /etc/rc.local

# /etc/hosts
[ "$(hostname -i | awk '{print $1}')" != "127.0.0.1" ] && sed -i "s@^127.0.0.1\(.*\)@127.0.0.1   `hostname` \1@" /etc/hosts

# Set timezone
rm -rf /etc/localtime
ln -s /usr/share/zoneinfo/Asia/Shanghai /etc/localtime

# Set OpenDNS
if [ ! -z "`cat /etc/resolv.conf | grep '8\.8\.8\.8'`" ];then
cat > /etc/resolv.conf << EOF
nameserver 192.168.1.8
nameserver 202.103.44.150
EOF
fi

# /etc/sysctl.conf
[ -z "`cat /etc/sysctl.conf | grep 'fs.file-max'`" ] && cat >> /etc/sysctl.conf << EOF
net.ipv4.tcp_syncookies = 1
fs.file-max=65535
net.ipv4.tcp_tw_reuse = 1
net.ipv4.tcp_tw_recycle = 1
net.ipv4.ip_local_port_range = 1024 65000
EOF
sysctl -p

# Update time
ntpdate pool.ntp.org 
echo '*/20 * * * * /usr/sbin/ntpdate pool.ntp.org > /dev/null 2>&1' > /var/spool/cron/crontabs/root;chmod 600 /var/spool/cron/crontabs/root 
service cron restart

source ~/.bashrc

# mkdir the Install directory
mkdir /opt/lnmp/php -p
mkdir /opt/lnmp/mysql -p
mkdir /opt/lnmp/nginx -p
mkdir /opt/lnmp/memcached -p
mkdir /opt/lnmp/tar_package -p
mkdir /opt/lnmp/lib -p
mkdir /opt/lnmp/libexec -p
mkdir /opt/lnmp/bin -p

cat > ~/.profile <<EOF
export PATH=$PATH:/opt/lnmp/bin/
EOF

#Disable SeLinux
if [ -s /etc/selinux/config ]; then
sed -i 's/SELINUX=enforcing/SELINUX=disabled/g' /etc/selinux/config
fi

if [ -s /etc/ld.so.conf.d/libc6-xen.conf ]; then
sed -i 's/hwcap 1 nosegneg/hwcap 0 nosegneg/g' /etc/ld.so.conf.d/libc6-xen.conf

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