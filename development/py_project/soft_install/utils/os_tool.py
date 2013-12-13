import os
import subprocess
import sys
import pwd
import shlex

def os_cmd(order):
	try:
		print "I will run the command %s on the system!" % order
		retcode = subprocess.call(order, shell=True)
		if retcode < 0:
			print >>sys.stderr, "Child was terminated by singal", -retcode
		else:
			print >>sys.stderr, "Child returned", retcode
	except OSError as e:
		print "Something wrong here with the command  %s executed" % order
		print >>sys.stderr, "Execution Falied: ", e


def make_dir(folder):
	if not(os.path.exists(folder)):
		print "The %s doesn't exists, I will create it !" % folder
		os.makedirs(folder)
	else:
		print "The %s have aready created !" % folder


def read_file(file_name):
	temp_file = open(file_name, 'r')
	return temp_file.read()


def write_file(file_name, stri):
	temp_file = open(file_name, 'w')
	temp_file.write(stri)


def filter_tar(stri):
	if '.tar.gz' in stri:
		return stri.replace('.tar.gz','')
	elif '.tar.bz2' in stri:
		return stri.replace('.tar.bz2','')
	elif '.zip' in stri:
		return stri.replace('.zip','')
	else:
		return "I do nothing"


def add_user(uname,grp):
	order = 'id %s' % uname
	order = shlex.split(order)
	j_name = subprocess.Popen(order, stdout=subprocess.PIPE)
	if not (uname in str(j_name.stdout.read())):
		print "The user name %s and its group %s isn't exists." % (uname ,grp)
		print "I will create them! "
		try:
			os_cmd('groupadd %s && useradd -g %s %s' % (grp, grp, uname))
		except OSError as e:
			print >>sys.stderr, "Execution Falied: ", e
	else:
		print "The user name %s and its group %s does exists. " % (uname ,grp)
		print "I will do nothing"

def j_folder_chown(uname, grp , path):
	t_uid = pwd.getpwnam(uname).pw_uid
	t_gid = pwd.getpwnam(uname).pw_gid
	if os.stat(path).st_uid != t_uid and os.stat(path).st_gid != t_gid:
		try:
			print "The %s is now own others " % path
			print "I will change to own %s %s !" % (uname, grp)
			os.chown(path, t_uid, t_gid)
		except OSError as e:
			print >>sys.stdout, "Execution Falied: ", e
	else:
		print "The %s is own %s %s now. keep it !" % (path, uname ,grp)