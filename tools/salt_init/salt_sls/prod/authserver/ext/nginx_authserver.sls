include: 
  - prod._init.nginx_init

site_authserver:
  file.managed:
    - name: /etc/nginx/site-available/authserver
    - source: salt://prod/authserver/template/authserver
    - user: root
    - group: root
    - require:
      - pkg: nginx

/etc/nginx/site-available/authserver:  
  file.symlink:
    - target: /etc/nginx/site-enable/authserver

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

dir_authserver:
  file.recurse:
    - name: /var/www/authserver
    - source: salt://prod/authserver/authServer
    - include_empty: True
