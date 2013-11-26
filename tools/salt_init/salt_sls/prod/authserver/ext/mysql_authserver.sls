include:
  - prod._init.mysql_init

root:
  mysql_user.present:
    - host: localhost
    - password: 123456
    - require:
      - pkg: mysql-python

/srv/salt/prod/authserver/:
  file.directory:
    - user: root
    - group: root
    - file_mode: 744
    - dir_mode: 755
    - makedirs: True

init_auth_sql:
  cmd:
    - run
    - watch:
      - file: init_auth_sql_file

init_auth_sql_file:
  file.managed:
    - name: /srv/salt/prod/herouser/init_auth_sql.sh
    - source: salt://prod/herouser/template/init_auth_sql.sh
