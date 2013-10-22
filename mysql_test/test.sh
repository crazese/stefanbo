#/bin/bash
/usr/bin/innobackupex --user=root --password=123456 --defaults-file=/etc/mysql/my.cnf --port=3306 --socket=/var/run/mysqld/mysqld.sock /home/mysql/backup/20131022

完整备份
innobackupex \
--user=root --password=123456 \
--defaults-file=/etc/mysql/my.cnf \
--port=3306 --socket=/var/run/mysqld/mysqld.sock \
/home/mysql/backup/20131022

插入数据时
avg-cpu:  %user   %nice %system %iowait  %steal   %idle
           1.00    0.00    2.00   95.00    0.00    2.00

Device:         rrqm/s   wrqm/s     r/s     w/s   rsec/s   wsec/s avgrq-sz avgqu-sz   await  svctm  %util
scd0              0.00     0.00    0.00    0.00     0.00     0.00     0.00     0.00    0.00   0.00   0.00
sda               0.00   288.00    0.00 3577.00     0.00 17008.00     4.75     0.93    0.26   0.26  92.00
dm-0              0.00     0.00    0.00 3667.00     0.00 17008.00     4.64     1.40    0.38   0.25  91.00
dm-1              0.00     0.00    0.00    0.00     0.00     0.00     0.00     0.00    0.00   0.00   0.00

增量备份
mkdir /home/mysql/backup/icn
innobackupex \
--user=root --password=123456 \
--incremental \
--incremental-basedir=/home/mysql/backup/20131022/2013-10-22_11-38-34 \
/home/mysql/backup/icn/  

bckconf="""
[MySQL]
mysqladmin = /usr/bin/mysqladmin
xtra = --apply-log --redo-only
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

全备的第一次prepare
/etc/init.d/mysql stop
rm -rf /var/lib/mysql/*

/usr/bin/innobackupex \
--user=root --password=123456 \
--apply-log --redo-only \
/home/sh/backup/full/2013-10-22_14-29-46

/usr/bin/innobackupex \
--user=root --password=123456 \
--copy-back \
/home/sh/backup/full/2013-10-22_14-29-46





增备
全备一次
增加2个表
插入1000行数据
增量备份一次
删除一个表
增量备份第二次
创建第三个表 插入100行数据
恢复到删除表的时候：
prepare full路径一次
/usr/bin/innobackupex \
--user=root --password=123456 \
--apply-log --redo-only \
/home/sh/backup/full/2013-10-22_15-33-12

再应用第一次增量

/usr/bin/innobackupex \
--user=root --password=123456 \
--apply-log --redo-only /home/sh/backup/full/2013-10-22_15-33-12 \
--incremental-dir=/home/sh/backup/inc/2013-10-22_15-35-58

应用第二次增量
/usr/bin/innobackupex \
--user=root --password=123456 \
--apply-log /home/sh/backup/full/2013-10-22_15-33-12 \
--incremental-dir=/home/sh/backup/inc/2013-10-22_15-37-14

--incremental-dir

backup timestamp:
root@zhongsi2:/home/sh/backup/full/2013-10-22_15-33-12# less xtrabackup_checkpoints 
backup_type = full-prepared
from_lsn = 0:0
to_lsn = 0:627972754
last_lsn = 0:627972754

root@zhongsi2:/home/sh/backup/inc/2013-10-22_15-35-58# cat xtrabackup_checkpoints 
backup_type = incremental
from_lsn = 0:627964636
to_lsn = 0:627970345
last_lsn = 0:627970345

root@zhongsi2:/home/sh/backup/inc/2013-10-22_15-37-14# cat xtrabackup_checkpoints 
backup_type = incremental
from_lsn = 0:627964636
to_lsn = 0:627971104
last_lsn = 0:627971104

root@zhongsi2:/home/sh/backup/inc/2013-10-22_15-58-05# cat xtrabackup_checkpoints 
backup_type = incremental
from_lsn = 0:627970345
to_lsn = 0:627972754
last_lsn = 0:627972754


check again:
full backup
root@zhongsi2:~# cat /home/sh/backup/full/2013-10-22_16-30-49/xtrabackup_checkpoints 
backup_type = full-backuped
from_lsn = 0:0
to_lsn = 0:627970572
last_lsn = 0:627970572

inc backup:


