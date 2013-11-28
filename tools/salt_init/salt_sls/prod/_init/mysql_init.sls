apt-get_fix:
  cmd.run:
    - name: apt-get -f install -y

mysqld:
  pkg:
    - installed
    - name: mysql-server
  service:
    - running
    - name: mysql
    - enable: True

mysql-client:
  pkg.installed:
    - require:
      - pkg: mysqld

mysql-common:
  pkg.installed:
    - require:
      - pkg: mysql-client

mysql-python:
  pkg.installed:
    - name: python-mysqldb
    - require:
      - pkg: mysql-common

