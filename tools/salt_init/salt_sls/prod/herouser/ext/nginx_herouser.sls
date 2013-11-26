include: 
  - prod._init.nginx_init

site_herouser:
  file.managed:
    - name: /etc/nginx/sites-available/herouser
    - source: salt://prod/herouser/template/herouser
    - require:
      - pkg: nginx

/etc/nginx/sites-enable/herouser:  
  file.symlink:
    - target: /etc/nginx/sites-available/herouser

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
