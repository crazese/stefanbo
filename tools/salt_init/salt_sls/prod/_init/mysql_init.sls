ifupdown:
  pkg.installed:

mysqld:
  pkg:
    - installed
    - name: mysql-server
  service:
    - running
    - name: mysqld
    - enable: True
    - watch:
      - pkg: ifupdown

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

