include: 
  - prod._init.nginx_init

site_authserver:
  file.managed:
    - name: /etc/nginx/sites-enable/authserver
    - source: salt://prod/authserver/template/authserver
    - require:
      - pkg: nginx

/etc/nginx/sites-available/authserver:  
  file.symlink:
    - target: /etc/nginx/sites-enable/authserver

/var/www/authserver:
  file.directory:
    - file_mode: 744
    - dir_mode: 755
    - makedirs: True
    - recurse: 
      - user
      - group
      - mode

dir_authserver:
  file.recurse:
    - name: /var/www/authserver
    - source: salt://prod/authserver/authServer
    - include_empty: True

remove_default1:
  file.rename:
    - name: /tmp/default1
    - source: /etc/nginx/sites-enable/default
    - force: True

remove_default2:
  file.rename:
    - name: /tmp/default2
    - source: /etc/nginx/sites-available/default
    - force: True