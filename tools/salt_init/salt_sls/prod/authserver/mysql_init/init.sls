mysql-server:
	pkg.installed
	service:
    - running
    - watch:
      - pkg: mysql-server

mysql-client:
	pkg.installed

mysql-python:
	pkg: 
		- installed
		- name: python-mysqldb

