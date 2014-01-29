include: 
  - prod._init.nginx_init

site_authserver:
  file.managed:
    - name: /etc/nginx/sites-enabled/authserver
    - source: salt://prod/authserver/template/authserver
    - require:
      - pkg: nginx

/etc/nginx/sites-available/authserver:  
  file.symlink:
    - target: /etc/nginx/sites-enabled/authserver

/var/www/authserver:
  file.directory:
    - file_mode: 744
    - dir_mode: 755
    - makedirs: True
    - recurse: 
      - mode

dir_authserver:
  file.recurse:
    - name: /var/www/authserver
    - source: salt://prod/authserver/authServer
    - include_empty: True

default_test1:
  file.exists:
    - name: /etc/nginx/sites-enabled/default 

default_test2:
  file.exists:
    - name: /etc/nginx/sites-available/default

remove_default1:
  file.rename:
    - name: /tmp/default1
    - source: /etc/nginx/sites-enabled/default
    - force: True
    - require: 
      - file: default_test1

remove_default2:
  file.rename:
    - name: /tmp/default2
    - source: /etc/nginx/sites-available/default
    - force: 
    - require:
      - file: default_test2

authserver_reload_nginx:
  service:
    - name: nginx
    - running
    - reload: True
    - watch:
      - file: site_authserver