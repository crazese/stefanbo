include:
  - prod._init.mysql_init

/srv/salt/prod/herouser/:
  file.directory:
    - user: root
    - group: root
    - file_mode: 775
    - dir_mode: 755
    - makedirs: True
    - recurse:
      - mode

init_hero_sql.sh:
  cmd.run:
    - name: sh /srv/salt/prod/herouser/init_hero_sql.sh
    - user: root
    - group: root
    - umask: 022
    - require:
      - file: init_hero_sql_file

init_hero_sql_file:
  file.managed:
    - name: /srv/salt/prod/herouser/init_hero_sql.sh
    - source: salt://prod/herouser/template/init_hero_sql.sh
    - mode: 755
