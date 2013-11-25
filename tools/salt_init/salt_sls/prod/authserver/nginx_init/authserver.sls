include: 
	- nginx

/etc/nginx/site-available/authserver:
  file.managed
    - source: salt://prod/authserver/authserver
    - user: root
    - group: root
    - require:
      - pkg: nginx
  file.symlink:
  	- target: /etc/nginx/site-enable/authserver