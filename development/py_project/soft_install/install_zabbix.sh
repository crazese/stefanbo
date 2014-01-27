#!/bin/bash

# install packages


# ubuntu 
apt-get install  libghc6-hsql-mysql-dev -y
apt-get install libphp-jabber -y
apt-get install libnet-jabber-loudmouth-perl -y
apt-get install jabber-dev -y
apt-get install libiksemel-dev  -y
apt-get install libcurl4-openssl-dev -y
apt-get install libsnmp-dev -y
apt-get install snmp -y

# centos
yum install zlib-devel libxml2-devel glibc-devel curl-devel gcc automake  libidn-devel openssl-devel net-snmp-devel rpm-devel OpenIPMI-devel

wget http://iksemel.googlecode.com/files/iksemel-1.4.tar.gz  
cd iksemel-1.4
./configure
make && make install

cd /opt/lnmp/
tar -zxvf zabbix-2.2.1.tar.gz
cd zabbix-2.2.1
./configure \
--prefix=/usr/local/lnmp/app/zabbix \
--enable-server \
--enable-agent \
--with-mysql \
--enable-ipv6 \
--with-net-snmp \
--with-libcurl \
--with-libxml2 \
--with-jabber