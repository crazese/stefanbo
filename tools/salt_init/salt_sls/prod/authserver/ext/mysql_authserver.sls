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
    - file_mode: 775
    - dir_mode: 755
    - makedirs: True
    - recurse:
      - mode

init_auth_sql:
  cmd.run:
    - name: sh /srv/salt/prod/authserver/init_auth_sql.sh
    - user: root
    - group: root
    - umask: 022
    - require:
      - file: init_auth_sql_file

init_auth_sql_file:
  file.managed:
    - name: /srv/salt/prod/authserver/init_auth_sql.sh
    - source: salt://prod/authserver/template/init_auth_sql.sh
    - mode: 755