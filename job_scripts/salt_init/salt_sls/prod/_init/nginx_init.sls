nginx:
  pkg:
    - installed
  service:
    - running
    - watch:
      - pkg: nginx
      - file: /etc/nginx/nginx.conf

/etc/nginx/nginx.conf:
  file.managed:
    - source: salt://prod/authserver/template/nginx.conf
    - user: root
    - group: root
    - require:
      - pkg: nginx