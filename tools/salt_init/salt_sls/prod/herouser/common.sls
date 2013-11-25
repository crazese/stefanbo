nginx:
  pkg:
    - installed
  service:
    - running
    - watch:
      - pkg: nginx
      - file: /etc/nginx/nginx.conf
      - user: root

/etc/nginx/nginx.conf
  file.managed:
    - source: salt://dev/nginx/nginx.conf


