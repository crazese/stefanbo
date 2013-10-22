# Backup Prepare and Copy-Back Script
# Originally Developed by Shahriyar Rzayev / rzayev.sehriyar@gmail.com
import ConfigParser
import os
import shlex
import subprocess
import shutil
import time,io

bckconf="""
[MySQL]
mysqladmin = /usr/bin/mysqladmin
xtra = --defaults-file=/etc/mysql/my.cnf --apply-log --redo-only
datadir = /var/lib/mysql
tmpdir = /tmp/mysql
tmp = /tmp

[Backup]
backupdir = /home/sh/backup
backup_tool = /usr/bin/innobackupex

[Commands]
start_mysql_command = service mysql start
stop_mysql_command = service mysql stop
mkdir_command = mkdir /var/lib/mysql
chown_command = chown -R mysql:mysql /var/lib/mysql
"""


class Prepare:
    def __init__(self, conf='bckconf'):
        con = ConfigParser.RawConfigParser()
        con.readfp(io.BytesIO(bckconf))
        bolme = con.sections()
        bolme.reverse()

        DB = bolme[0]
        self.mysqladmin = con.get(DB,'mysqladmin')
        self.xtrabck = con.get(DB, 'xtra')
        self.datadir = con.get(DB, 'datadir')
        self.tmpdir = con.get(DB, 'tmpdir')
        self.tmp = con.get(DB, 'tmp')

        BCK = bolme[1]
        self.backupdir = con.get(BCK, 'backupdir')
        self.full_dir = self.backupdir + '/full'
        self.inc_dir = self.backupdir + '/inc'
        self.backup_tool = con.get(BCK, 'backup_tool')

        CM = bolme[2]
        self.start_mysql = con.get(CM, 'start_mysql_command')
        self.stop_mysql = con.get(CM, 'stop_mysql_command')
        self.mkdir_command = con.get(CM, 'mkdir_command')
        self.chown_command = con.get(CM, 'chown_command')


    def recent_full_backup_file(self):
        # Return last full backup dir name

        if len(os.listdir(self.full_dir)) > 0:
            return max(os.listdir(self.full_dir))
        else:
            return 0


    def check_inc_backups(self):
        # Check for Incremental backups

        if len(os.listdir(self.inc_dir)) > 0:
            return 1
        else:
            return 0

    #############################
    # PREPARE ONLY FULL BACKUP  #
    #############################

    def prepare_only_full_backup(self):
        if self.recent_full_backup_file() == 0:
            print"""
            ################################################################
            #  You have no FULL backups. Please take backup for preparing  #
            ################################################################
            """

        elif self.check_inc_backups() == 0:
            print"""
            ##################################
            #  Preparing Full backup 1 time  #
            ##################################
            """
            time.sleep(3)
            args = '%s %s %s/%s' % (self.backup_tool, self.xtrabck, self.full_dir, self.recent_full_backup_file())
            args = shlex.split(args)
            fb = subprocess.Popen(args, stdout=subprocess.PIPE)
            print(str(fb.stdout.read()))

            print"""
            ##################################################
            #  Preparing Again Full Backup for final usage.  #
            #  It means that you have not got inc backups    #
            ##################################################
            """
            time.sleep(5)
            args2 = '%s --apply-log %s/%s' % (self.backup_tool, self.full_dir, self.recent_full_backup_file())
            args2 = shlex.split(args2)
            fb2 = subprocess.Popen(args2, stdout=subprocess.PIPE)
            print(str(fb2.stdout.read()))

        else:
            print"""
            ##########################################################
            #  Preparing Full backup 1 time. It means that           #
            #  you have got incremental backups and final preparing  #
            #  will occur after preparing all inc backups            #
            ##########################################################
            """
            time.sleep(3)
            args = '%s %s %s/%s' % (self.backup_tool, self.xtrabck, self.full_dir, self.recent_full_backup_file())
            args = shlex.split(args)
            fb = subprocess.Popen(args, stdout=subprocess.PIPE)
            print(str(fb.stdout.read()))


    #######################
    # PREPARE INC BACKUPS #
    #######################

    def prepare_inc_full_backups(self):
        if self.check_inc_backups() == 0:
            print"""
            ##############################################################################
            #  You have no Incremental backups. So will prepare only latest Full backup  #
            ##############################################################################
            """
            time.sleep(3)
            self.prepare_only_full_backup()
        else:
            print"""
            #########################################################################
            #  You have Incremental backups.                                        #
            #  Will prepare latest full backup then based on it, will prepare Incs  #
            #  Preparing Full backup  :)                                            #
            #########################################################################
            """
            time.sleep(3)
            self.prepare_only_full_backup()
            print"""
            #######################
            #  Preparing Incs :)  #
            #######################
            """
            time.sleep(3)
            list_of_dir = sorted(os.listdir(self.inc_dir))

            for i in list_of_dir:
                if i == max(os.listdir(self.inc_dir)):
                    print"""
                    ######################################################################
                    #  Preparing very first inc backup. First inc backup dir/name is %s  # 
                    ######################################################################
                    """ % i
                    time.sleep(3)
                    args = '%s %s %s/%s --incremental-dir=%s/%s' % (self.backup_tool, self.xtrabck,
                                                                    self.full_dir, self.recent_full_backup_file(),
                                                                    self.inc_dir, i)
                    args = shlex.split(args)
                    fb = subprocess.Popen(args, stdout=subprocess.PIPE)
                    print(str(fb.stdout.read()))

            print"""
            #################################################
            #  The end of Preparing Stage                   #
            #  The last step of backup preparing is         #
            #  preparing FULL backup again for final usage  #
            #  Preparing FULL backup Again                  #
            #################################################
            """
            time.sleep(3)

            args2 = '%s --apply-log %s/%s' % (self.backup_tool, self.full_dir, self.recent_full_backup_file())
            args2 = shlex.split(args2)
            fb2 = subprocess.Popen(args2, stdout=subprocess.PIPE)
            print(str(fb2.stdout.read()))

    #############################
    # COPY-BACK PREPARED BACKUP #
    #############################


    def copy_back(self):
        # Shut Down MySQL
        print"""
        ################################
        #  Shutting Down MySQL server  #
        ################################
        """
        time.sleep(3)
        shutdown = shlex.split(self.stop_mysql)
        sh = subprocess.Popen(shutdown, stdout=subprocess.PIPE)
        print(str(sh.stdout.read()))

        # Move datadir to new directory
        print"""
        ########################################
        #  Moving MySQL datadir to /tmp/mysql  #
        ########################################
        """
        time.sleep(3)
        if os.path.isdir(self.tmpdir):
            rmdirc = 'rm -rf %s' % self.tmpdir
            rmdirc = shlex.split(rmdirc)
            rm = subprocess.Popen(rmdirc, stdout=subprocess.PIPE)
            print(str(rm.stdout.read()))
            shutil.move(self.datadir, self.tmp)
            makedir = self.mkdir_command
            makedir = shlex.split(makedir)
            mk = subprocess.Popen(makedir, stdout=subprocess.PIPE)
            print(str(mk.stdout.read()))
        else:
            shutil.move(self.datadir, self.tmp)
            makedir = self.mkdir_command
            makedir = shlex.split(makedir)
            mk = subprocess.Popen(makedir, stdout=subprocess.PIPE)
            print(str(mk.stdout.read()))

        print"""
        ################################################
        #  Copying Back Already Prepared Final Backup  #
        ################################################
        """
        time.sleep(3)
        if len(os.listdir(self.datadir)) > 0:
            print("MySQL Datadir is not empty!")
        else:
            copy_back = '%s --copy-back %s/%s' % (self.backup_tool,
                                                  self.full_dir,
                                                  self.recent_full_backup_file())
            copy_back = shlex.split(copy_back)
            cp = subprocess.Popen(copy_back, stdout=subprocess.PIPE)
            print(str(cp.stdout.read()))
            print"""
            ####################################
            #  Data copied back successfully!  #
            ######################################################
            #  Giving chown of new copied datadir to mysql user  #
            ######################################################
            """
            time.sleep(3)
            give_chown = self.chown_command
            give_chown = shlex.split(give_chown)
            ch = subprocess.Popen(give_chown, stdout=subprocess.PIPE)
            print(str(ch.stdout.read()))

            print"""
            ############################
            #  Starting MySQL server!  #
            ############################
            """
            time.sleep(3)

            start_server = self.start_mysql
            start_server = shlex.split(start_server)
            str_srv = subprocess.Popen(start_server, stdout=subprocess.PIPE)
            print(str(str_srv.stdout.read()))

            print"""
            ######################################################################
            #  All data copied back successfully your MySQL server is UP again   #
            #  Congratulations!!!!!                                              #
            #  Backups are life savers                                           #
            ######################################################################
            """

    ####################################################################
    # FINAL FUNCTION FOR CALL: PREPARE/PREPARE AND COPY-BACK/COPY-BACK #
    ####################################################################


    def prepare_backup_and_copy_back(self):
    # Recovering/Copying Back Prepared Backup
        print"""
        ###########################################################################################################
        #  This script is Preparing full/inc backups!                                                             #
        #  What do you want to do?                                                                                #
        #    1. Prepare Backups and keep for future usage.NOTE('Once Prepared Backups Can not be prepared Again') #
        #    2. Prepare Backups and restore/recover/copy-back immediately")                                       #
        #    3. Just copy-back previously prepared backups")                                                      #
        ###########################################################################################################
        """
        #prepare = int(input("Please Choose one of options and type 1 or 2 or 3: "))          
#        
#        if prepare == 1:
#            self.prepare_inc_full_backups()
#        elif prepare == 2:
#            self.prepare_inc_full_backups()
#            self.copy_back()
#        elif prepare == 3:
#            self.copy_back()
#        else:
#            print("Please type 1 or 2 or 3 and nothing more!")
        self.prepare_inc_full_backups()
        self.copy_back()


a = Prepare()
a.prepare_backup_and_copy_back()