#!/usr/bin/env python
# -*- coding: utf-8 -*-

import MySQLdb as mdb 
import sys

try:
	con = mdb.connect('localhost', 'testuser', '123456', 'testdb')

	cur = con.cursor()
	cur.execute('select version()')

	ver = cur.fetchone()

	print "Database version : %s " % ver

except mdb.Error, e:

	print "Error %d: %s" % (e.args[0], e.args[1])
	sys.exit(1)

finally:

	if con:
		con.close()