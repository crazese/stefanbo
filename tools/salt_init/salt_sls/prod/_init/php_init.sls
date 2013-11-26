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
      - libpcre3-dev
      - libcloog-ppl-dev

php-pear
  pkg.installed:
    - skip_verify: True
    
php5-fpm:
  pkg.installed

apc:
  pecl.installed:
    - require:
      - pkg: php-pear

memcache:
  pecl.installed:
    - require:
      - pkg: php-pear
