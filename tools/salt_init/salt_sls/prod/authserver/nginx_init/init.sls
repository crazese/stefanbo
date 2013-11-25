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