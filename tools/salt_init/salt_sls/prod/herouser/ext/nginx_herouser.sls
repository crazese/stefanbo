include: 
  - prod._init.nginx_init

site_herouser:
  file.managed:
    - name: /etc/nginx/site-available/herouser
    - source: salt://prod/herouser/template/herouser
    - user: root
    - group: root
    - require:
      - pkg: nginx

/etc/nginx/site-available/herouser:  
  file.symlink:
    - target: /etc/nginx/site-enable/herouser

/var/www/herouser:
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

dir_herouser:
  file.recurse:
    - name: /var/www/herouser
    - source: salt://prod/herouser/HeroUser
    - include_empty: True
