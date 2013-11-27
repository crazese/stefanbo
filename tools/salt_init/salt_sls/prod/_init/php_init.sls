php5-pkgs:
  pkg.installed:
    - names:
      - php5-cli
      - php5-common
      - php5-mysql
      - php5-suhosin
      - php5-gd
      - php5-mcrypt
      - php5-cgi
      - php5-curl
      - php5-dev
      - php5-memcached
      - libpcre3-dev
      - libcloog-ppl-dev

php-pear:
  pkg.installed:
    - skip_verify: True

php5-fpm:
  pkg.installed

apc:
  pecl.installed:
    - require:
      - pkg: php-pear

igbinary:
  pecl.installed

libmemcached_tar_get:
  file.managed:
    - name: /opt/libmemcached-1.0.10.tar.gz
    - source: salt://prod/_init/tar_pak/libmemcached-1.0.10.tar.gz

tar_x_libmemcached:
  cmd.wait:
    - name: cd /opt/ && tar -zxvf libmemcached-1.0.10.tar.gz
    - watch:
      - file: libmemcached_tar_get

complie_libmemcached:
  cmd.wait:
    - name: cd /opt/libmemcached-1.0.10 && ./configure && make && make installed
    - require:
      - cmd: tar_x_libmemcached

memcached_tar_get:
  file.managed:
    - name: /opt/memcached-2.1.0.tgz
    - source: salt://prod/_init/tar_pak/memcached-2.1.0.tgz
    - require:
      - cmd: complie_libmemcached

tar_x_memcached:
  cmd.wait:
    - name: cd /opt/ && tar -zxvf memcached-2.1.0.tgz
    - watch:
      - file: memcached_tar_get

complie_memcached:
  cmd.wait:
    - name: cd /opt/memcached-2.1.0 && phpize && ./configure --enable-memcached --enable-memcached-igbinary config=/usr/bin/php-config && make && make install
    - require:
      - cmd: tar_x_memcached
