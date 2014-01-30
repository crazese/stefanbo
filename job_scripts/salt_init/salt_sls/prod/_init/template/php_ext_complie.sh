#!/bin/bash
cd /opt
unzip  igbinary-igbinary-1.1.1-28-gc35d48f.zip
cd /opt/igbinary-igbinary-c35d48f
phpize
./configure  --enable-igbinary config=/usr/local/bin/php-config
make 
make install
cd ..

tar zxvf libmemcached-1.0.10.tar.gz
cd libmemcached-1.0.10
./configure 
make && make install
cd ..


tar zxvf memcached-2.1.0.tgz
cd memcached-2.1.0
phpize
./configure  --enable-memcached --enable-memcached-igbinary config=/usr/local/bin/php-config
make && make install

