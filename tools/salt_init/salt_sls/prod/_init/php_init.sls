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

igbinary_tar_get:
  file.managed:
    - name: /opt/igbinary-igbinary-1.1.1-28-gc35d48f.zip
    - source: salt://prod/_init/tar_pak/igbinary-igbinary-1.1.1-28-gc35d48f.zip
    - mode: 755

libmemcached_tar_get:
  file.managed:
    - name: /opt/libmemcached-1.0.10.tar.gz
    - source: salt://prod/_init/tar_pak/libmemcached-1.0.10.tar.gz
    - mode: 755

memcached_tar_get:
  file.managed:
    - name: /opt/memcached-2.1.0.tgz
    - source: salt://prod/_init/tar_pak/memcached-2.1.0.tgz

tar_x_igbinary:
  cmd.wait:
    - name: unzip igbinary-igbinary-1.1.1-28-gc35d48f.zip
    - cwd: /opt/
    - user: root
    - group: root
    - watch:
      - file: igbinary_tar_get

tar_x_libmemcached:
  cmd.wait:
    - name: tar -zxvf libmemcached-1.0.10.tar.gz
    - cwd: /opt/
    - user: root
    - group: root
    - watch:
      - file: libmemcached_tar_get

tar_x_memcached:
  cmd.wait:
    - name: tar -zxvf memcached-2.1.0.tgz
    - cwd: /opt/
    - user: root
    - group: root
    - watch:
      - file: memcached_tar_get

complie_sh_get:
  file.managed:
    - name: /srv/salt/prod/_init/php_ext_complie.sh 
    - source: salt://prod/_init/template/php_ext_complie.sh
    - makedirs: True
    - mode: 755

complie_sh:
  cmd.run:
    - name: sh /srv/salt/prod/_init/php_ext_complie.sh
    - user: root
    - group: root
    - umask: 022
    - require:
      - file: complie_sh_get