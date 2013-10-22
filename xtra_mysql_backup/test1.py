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
import time

now=time.strftime("%Y%m%d", time.localtime())
ipadd="192.168.1.202"


bckconf="""
[MySQL]
mysql = /usr/bin/mysql
mycnf = /etc/mysql/my.cnf
mysqladmin = /usr/bin/mysqladmin
useroption = --user=root --password=123456
xtra = --defaults-file=/etc/mysql/my.cnf --port=3306 --socket=/var/run/mysqld/mysqld.sock
datadir = /var/lib/mysql

[Backup]
backupdir = /home/mysql/backup/%s
backup_tool = /usr/bin/innobackupex

[Remote]
remote_conn = root@192.168.1.201
remote_dir = /home/mysql/backup/%s
""" % (now, ipadd)

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

        	
def last_full_backup_date(self):
        # Finding last full backup date from dir/folder name

        max = self.recent_full_backup_file()
        dir_date_str = max[:4] + '-' + max[5:7] + '-' + max[8:10] + ' ' + max[11:13] + ':' + max[14:16]
        dir_date = datetime.strptime(dir_date_str, "%Y-%m-%d %H:%M")
        now = datetime.now().replace(second=0, microsecond=0)

        # Defining variables for comparison.

        a = '2013-09-04 15:29'
        b = '2013-09-03 15:29'
        a = datetime.strptime(a, "%Y-%m-%d %H:%M")
        b = datetime.strptime(b, "%Y-%m-%d %H:%M")
        diff = a - b

        # Finding if last full backup is 1 day or more from now!

        if now - dir_date >= diff:
            return 1
        else:
            return 0


    def recent_full_backup_file(self):
        # Return last full backup dir name

        if len(os.listdir(self.full_dir)) > 0:
            return max(os.listdir(self.full_dir))
        else:
            return 0


    def mysql_connection_flush_logs(self):
        """
        It is highly recomended to flush binary logs before each full backup for easy maintenance.
        That's why we will execute "flush logs" command before each full backup!
        Also for security purposes you must create a MySQL user with only RELOAD privilege,
        It is sufficient for this backup script.
        I provide eg. create user statement:
        """
        # ############################################################
        # create user 'test_backup'@'127.0.0.1' identified by '12345';
        # grant all on bck.* to 'test_backup'@'127.0.0.1';
        # grant reload on *.* to 'test_backup'@'127.0.0.1';
        # ############################################################

        # Also create a test database for connection
        # create database bck;


        config = {

            'user': 'root',
            'password': '123456',
            'host': '127.0.0.1',
            'database': 'test_db1',
            'raise_on_warnings': True,

        }

        # Open connection
        try:
            cnx = mysql.connector.connect(**config)
            cursor = cnx.cursor()
            query = "flush logs"
            print("Flushing Binary Logs")
            time.sleep(2)
            cursor.execute(query)
            cursor.close()
            cnx.close()
        except mysql.connector.Error as err:
            if err.errno == errorcode.ER_ACCESS_DENIED_ERROR:
                print("Something is wrong with your user name or password")
            elif err.errno == errorcode.ER_BAD_DB_ERROR:
                print("Database does not exists")
            else:
                print(err)


    def copy_backup_to_remote_host(self):
        # Copying backup directory to remote server
        print("#################################################################################################")
        print("Copying backups to remote server")
        print("#################################################################################################")
        copy_it = 'scp -r %s %s:%s' % (self.backupdir, self.remote_conn, self.remote_dir)
        copy_it = shlex.split(copy_it)
        cp = subprocess.Popen(copy_it, stdout=subprocess.PIPE)
        print(str(cp.stdout.read()))


    def full_backup(self):
        # Taking Full backup
        #innobackupex --user=root --password=123456 
        #-defaults-file=/etc/mysql/my.cnf 
        #--database=test_db1 
        #/home/mysql/backup/20131022
        args = '%s %s %s %s' % (self.backup_tool, self.myuseroption, self.xtrabck, self.full_dir)
        args = shlex.split(args)
        fb = subprocess.Popen(args, stdout=subprocess.PIPE)
        print(str(fb.stdout.read()))


    def inc_backup(self):
        # Taking Incremental backup

        recent_bck = self.recent_full_backup_file()
        args = '%s %s %s --incremental %s --incremental-basedir %s/%s' % (
            self.backup_tool, self.myuseroption, self.xtrabck, self.inc_dir, self.full_dir, recent_bck)
        args = shlex.split(args)
        ib = subprocess.Popen(args, stdout=subprocess.PIPE)
        print(str(ib.stdout.read()))


    def main_backup(self):
        """
         This function firstly checks for full backup directory if it is empty takes full backup.
         If it is not empty then checks for full backup time.if the recent full backup taken 1 day ago takes full backup
         Any other options it takes incremental backup
        """
        if self.recent_full_backup_file() == 0:
            print("################################################################")
            print("You have no backups : Taking very first Full Backup!")
            print("################################################################")

            time.sleep(3)

            # Flushing Logs
            self.mysql_connection_flush_logs()

            # Taking fullbackup
            self.full_backup()

            # Copying backups to remote server
            self.copy_backup_to_remote_host()

        else:
            print("################################################################")
            print("You have a full backup. "
                  "We will take an incremental one based on recent Full Backup")
            print("################################################################")

            time.sleep(3)

            # Taking incremental backup
            self.inc_backup()

            # Copying backups to remote server
            self.copy_backup_to_remote_host()


b = Backup()
b.main_backup()
