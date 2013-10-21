#/usr/bin/env python
#
# Backup MySQL with innobackupex tool in python script
# Using the 2.0.0 Xtrabackup
# tested with MySQL Ver 14.14 Distrib 5.1.70, for debian-linux-gnu (x86_64) using readline 6.1
# Improved by stefan_bo / stefan_bo@163.com
# #############################################################################################

import os, io 
import ConfigParser, subprocess, shlex, shutil
import mysql.connector
from datetime import datetime
from mysql.connector import errorcode

bckconf="""
[MySQL]
mysql = /usr/bin/mysql
mycnf = /etc/mysql/my.cnf
mysqladmin = /usr/bin/mysqladmin
useroption = --user=root --password=123456
xtra = --defaults-file=/etc/mysql/my.cnf --port=3306 --socket=/var/lib/mysql/zhongsi2.pid
datadir = /var/lib/mysql

[Backup]
backupdir = /home/sh/backup
backup_tool = /usr/bin/innobackupex

[Remote]
remote_conn = root@192.168.1.201
remote_dir = /home/test/bak_set
"""

# Creating backup class

class Backup:
	def __init__(self, conf='bckconf'):
		con = ConfigParser.RawConfigParser()
		con.readfp(io.BytesIO(bckconf))
		bolme = con.sections()
		bolme.reverse()

		DB = bolme[0]
		self.mysql        = con.get(DB, "mysql")
		self.mycnf        = con.get(DB, "mycnf")
        self.mysqladmin   = con.get(DB, "mysqladmin")
        self.myuseroption = con.get(DB, "useroption")
        self.xtrabck      = con.get(DB, "xtra")
        self.datadir      = con.get(DB, "datadir")

        BCK = bolme[1]
        self.backupdir    = con.get(BCK, "backupdir")
        self.full_dir     = self.backupdir + '/full'
        self.inc_dir      = self.backupdir + '/inc'
        self.backup_tool  = con.get(BCK, "backup_tool")

        RM = bolme[2]
        self.remote_conn  = con.get(RM, "remote_conn")
        self.remote_dir   = con.get(RM, "remote_dir")
        statusargs = '%s %s status' % (self.mysqladmin, self.myuseroption)
        statusargs = shlex.split(statusargs)

        myadmin = subprocess.Popen(statusargs, stdout=subprocess.PIPE)

        if not ('Uptime' in str(myadmin.stdout.read())):
        	print """ Mysql Server is Not UP """
        else:
        	print " Mysql Server is Up! "

        if not os.path.exists(self.mycnf):
        	print """ Please Check Mysql configuration file path """

        if not os.path.exists(self.mysql):
        	print """ Please Check Mysql """

        if not os.path.exists(self.mysqladmin):
        	print """ Please Check mysqladmin """

        if not os.path.exists(self.backup_tool):
        	print """ Please Check backup tool innobackupex """

        if not os.path.exists(self.backupdir):
        	print """ %s doesn't exists , Please Check core backup directory """ % self.backupdir
        	os.makedirs(self.backupdir)
        	print """ %s has been created sucessful ! """ % self.backupdir

        if not os.path.exists(self.full_dir):
        	print """ Full directory is not exists. Creating full backup directory %s """ % self.full_dir
        	os.makedirs(self.full_dir)
        	print """ %s has been created sucessful ! """ % self.full_dir

        if not os.path.exists(self.inc_dir):
        	print """ Increment directory is not exists. Creating Increment backup  directory %s """ % self.inc_dir
        	os.makedirs(self.inc_dir)
        	print """ %s has been created sucessful ! """ % self.inc_dir

        	

