#!/bin/bash

# install packages


apt-get install  libghc6-hsql-mysql-dev -y

apt-get install libphp-jabber -y
apt-get install libnet-jabber-loudmouth-perl -y
apt-get install jabber-dev -y
apt-get install libiksemel-dev  -y

apt-get install libcurl4-openssl-dev -y

apt-get install libsnmp-dev -y
apt-get install snmp -y

cd /opt/lnmp/
tar -zxvf zabbix-2.2.1.tar.gz
cd zabbix-2.2.1
./configure \
--prefix=/opt/lnmp/zabbix_pptv \
--enable-server \
--enable-agent \
--with-mysql \
--enable-ipv6 \
--with-net-snmp \
--with-libcurl \
--with-libxml2 \
--with-jabber