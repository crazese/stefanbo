include:
  - prod._init.mysql_init

root:
  mysql_user.present:
    - host: localhost
    - password: 123456
    - require:
      - pkg: mysql-python

/srv/salt/prod/herouser/:
  file.directory:
    - user: root
    - group: root
    - file_mode: 744
    - dir_mode: 755
    - makedirs: True

/srv/salt/prod/herouser/init_hero_sql.sh:
  cmd:
    - wait
    - watch:
      - file: init_hero_sql

init_hero_sql:
  file.managed:
    - name: /srv/salt/prod/herouser/init_hero_sql.sh
    - source: salt://prod/herouser/template/init_hero_sql.sh
    - require:
      - file: /srv/salt/prod/herouser/