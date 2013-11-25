nginx:
  pkg:
    - installed
  service:
    - running
    - watch:
      - pkg: nginx
      - file: /etc/nginx/nginx.conf
      - user: root
  user.present:
  	- uid: 0
  	- gid: 0
  group.present:
  	- gid: 0
  	- require:
  		- pkg: nginx

/etc/nginx/nginx.conf:
  file.managed
    - source: salt://prod/authserver/nginx.conf
    - user: root
    - group: root
    - require:
      - pkg: nginx

/etc/nginx/site-available/authserver:
  file.managed
    - source: salt://prod/authserver/authserver
    - user: root
    - group: root
    - require:
      - pkg: nginx
  file.symlink:
  	- target: /etc/nginx/site-enable/authserver

mysql-server:
	pkg.installed
	service:
    - running
    - watch:
      - pkg: mysql-server

mysql-client:
	pkg.installed

mysql-python:
	pkg: 
		- installed
		- name: python-mysqldb

root:
	mysql_user.present:
		- host: localhost
		- password: 123456
		- require:
			- pkg: mysql-python


{% for pak in ['php5-cli', 'php5-common' ,'php5-mysql','php5-suhosin','php5-gd', 'php5-mcrypt', 'php5-fpm', 'php5-cgi', 'php-pear', 'php5-curl', 'php5-openssl', 'php5-dev','libpcre3-dev','libevent-dev','libcloog-ppl-dev'] %}
{{ pak }}:
	pkg.installed
{% endfor %}

php-pear:
	pkg.installed

apc:
	pecl.installed:
	  - require:
	  	- pkg: php-pear

memcache:
	pecl.installed:
		- require:
			- pkg: php-pear

memcached:
	pkg.installed
	
memcached_start:
	cmd:
		- name: /usr/bin/memcached -m 64 -p 11211 -u nobody -l 127.0.0.1
		- wait
		- require:
			- pkg: memcached

/etc/php5/fpm/php.ini:
  file.managed
    - source: salt://prod/authserver/php.ini
    - require:
      - pkg: php5-fpm

php5-fpm_restart:
  cmd:
    - name: /etc/init.d/php5-fpm restart
    - wait
    - watch:
      file: /etc/php5/fpm/php.ini

/var/www/authserver:
  file.directory:
    - user: root
    - group: root
    - file_mode: 744
    - dir_mode: 755
    - makedirs: True
    - recurse: 
      - user
      - group
      - mode
  file.recurse:
    - source: salt://prod/authserver/authServer
    - include_empty: True

/srv/salt/prod/authserver/:
  file.directory:
    - user: root
    - group: root
    - file_mode: 744
    - dir_mode: 755
    - makedirs: True

init_auth_sql:
  pkg:
    - installed
    - required:
      - file: /srv/salt/prod/authserver/init_auth_sql.sh

/srv/salt/prod/authserver/init_auth_sql.sh:
  cmd:
    - wait
    - require: init_auth_sql
  file.managed
    - source: salt://prod/authserver/init_auth_sql.sh