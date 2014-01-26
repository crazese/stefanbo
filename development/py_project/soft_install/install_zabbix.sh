#!/bin/bash

# install packages


#apt-get install  libghc6-hsql-mysql-dev -y#

#apt-get install libphp-jabber -y
#apt-get install libnet-jabber-loudmouth-perl -y
#apt-get install jabber-dev -y
#apt-get install libiksemel-dev  -y#

#apt-get install libcurl4-openssl-dev -y#

#apt-get install libsnmp-dev -y
#apt-get install snmp -y


# install server
cd /usr/local/lnmp/tar_package/zabbix/
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

# install agent
./configure \
--prefix=/usr/local/app/zabbix \
--enable-agent \
--enable-ipv6 \
--with-net-snmp \
--with-libcurl \
--with-libxml2 \
--with-jabber

/data/lnmp/monitor_tools/app

./configure \
--prefix=/data/lnmp/monitor_tools/app/zabbix \
--enable-agent \
--with-libcurl=/data/lnmp/cur/lib/ \
--with-libxml2=/usr/local/app/locale/libxml2/lib/ \
--with-jabber

./configure \
--prefix=/usr/local/app/zabbix \
--enable-proxy \
--with-mysql \
--enable-ipv6 \
--with-net-snmp \
--with-libcurl \
--with-libxml2 \
--with-jabber