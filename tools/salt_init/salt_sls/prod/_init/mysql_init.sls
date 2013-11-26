mysql-server:
  pkg:
    - installed
  service:
    - running
    - require:
      - pkg: mysql-server

mysql-client:
  pkg.installed

mysql-python:
  pkg: 
    - installed
    - name: python-mysqldb

