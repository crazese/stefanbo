#!/bin/sh
cd /opt/lnmp/tar_package
cur_dir=$(pwd)

tar -zxvf memcached-1.4.9.tar.gz
cd memcached-1.4.9 
./configure \
--prefix=/opt/lnmp/app/memcache \
--with-libevent=/opt/lnmp/app/phpextend

make && make install

/opt/lnmp/app/memcache/bin/memcached -m 64 -p 11211 -u nobody -l 127.0.0.1

cat > /opt/lnmp/app/nginx/www/herouser/test.php <<EOF
< ?php
$mem = new Memcache;
$mem->connect("127.0.0.1", 11211);
$mem->set('key', 'This is a test!', 0, 60);
$val = $mem->get('key');
echo $val;
?>
EOF