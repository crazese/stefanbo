#版本控制subversion 1.6.6
root@ubuntu:/tmpdir# svn import -m "importing project" . file:///home/svn/project/sesame/trunk
Adding         number.txt
Adding         day.txt

Committed revision 1.

root@ubuntu:/home/monkey/work/sesame# svn status day.txt 
M       day.txt
root@ubuntu:/home/monkey/work/sesame# svn diff day.txt 
Index: day.txt
===================================================================
--- day.txt     (revision 2)
+++ day.txt     (working copy)
@@ -3,3 +3,5 @@
 wednesday
 thursday
 friday
+saturday
+sunday
root@ubuntu:/home/monkey/work/sesame# svn commit -m "Client wants us to work on weekends"
Sending        day.txt
Transmitting file data .
Committed revision 3.
root@ubuntu:/home/monkey/work/sesame# svn log dat.txt
svn: 'dat.txt' is not under version control
root@ubuntu:/home/monkey/work/sesame# ls
day.txt  number.txt
root@ubuntu:/home/monkey/work/sesame# svn log day.txt 
------------------------------------------------------------------------
r3 | root | 2013-10-29 13:41:28 +0800 (Tue, 29 Oct 2013) | 1 line

Client wants us to work on weekends
------------------------------------------------------------------------
r2 | root | 2013-10-29 13:25:45 +0800 (Tue, 29 Oct 2013) | 1 line

importing project
------------------------------------------------------------------------
root@ubuntu:/home/monkey/work/sesame# svn log --verbose day.txt 
------------------------------------------------------------------------
r3 | root | 2013-10-29 13:41:28 +0800 (Tue, 29 Oct 2013) | 1 line
Changed paths:
   M /sesame/trunk/day.txt

Client wants us to work on weekends
------------------------------------------------------------------------
r2 | root | 2013-10-29 13:25:45 +0800 (Tue, 29 Oct 2013) | 1 line
Changed paths:
   A /sesame
   A /sesame/trunk
   A /sesame/trunk/day.txt
   A /sesame/trunk/number.txt

importing project
------------------------------------------------------------------------



root@ubuntu:~# svnlook youngest /home/svn/project
14
root@ubuntu:~# svnadmin dump /home/svn/project > /home/svn/backup/dumpfile.20131030
-bash: /home/svn/backup/dumpfile.20131030: No such file or directory
root@ubuntu:~# mkdir /home/svn/backup
root@ubuntu:~# svnadmin dump /home/svn/project > /home/svn/backup/dumpfile.20131030
* Dumped revision 0.
* Dumped revision 1.
* Dumped revision 2.
* Dumped revision 3.
* Dumped revision 4.
* Dumped revision 5.
* Dumped revision 6.
* Dumped revision 7.
* Dumped revision 8.
* Dumped revision 9.
* Dumped revision 10.
* Dumped revision 11.
* Dumped revision 12.
* Dumped revision 13.
* Dumped revision 14.
root@ubuntu:~# svnlook youngest /home/svn/project
15