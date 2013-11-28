include: 
  - prod._init.nginx_init

site_herouser:
  file.managed:
    - name: /etc/nginx/sites-enabled/herouser
    - source: salt://prod/herouser/template/herouser
    - require:
      - pkg: nginx

/etc/nginx/sites-available/herouser:  
  file.symlink:
    - target: /etc/nginx/sites-enabled/herouser

/var/www/herouser:
  file.directory:
    - file_mode: 744
    - dir_mode: 755
    - makedirs: True
    - recurse: 
      - mode

dir_herouser:
  file.recurse:
    - name: /var/www/herouser
    - source: salt://prod/herouser/HeroUser
    - include_empty: True

remove_default1:
  file.rename:
    - name: /tmp/default1
    - source: /etc/nginx/sites-enabled/default
    - force: True

remove_default2:
  file.rename:
    - name: /tmp/default2
    - source: /etc/nginx/sites-available/default
    - force: True

herouser_reload_nginx:
  service:
    - name: nginx
    - running
    - reload: True
    - watch:
      - file: site_herouser
