#!/bin/sh

apt-get install mysql-server mysql-client -y
apt-get install python-dev -y
apt-get install libmysqlclient-dev
apt-get install python-setuptools
easy_install mysql-python
apt-get install -y nginx
apt-get install -y php5-cli php5-common php5-mysql php5-suhosin php5-gd php5-mcrypt php5-fpm php5-cgi php-pear php5-curl php5-openssl php5-dev

