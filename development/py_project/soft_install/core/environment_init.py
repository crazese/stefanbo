import os, subprocess,shlex
import sys
import pwd
from tools import os_cmd


apt_conf = """deb http://mirrors.163.com/ubuntu/ lucid main universe restricted multiverse
deb-src http://mirrors.163.com/ubuntu/ lucid main universe restricted multiverse
deb http://mirrors.163.com/ubuntu/ lucid-security universe main multiverse restricted
deb-src http://mirrors.163.com/ubuntu/ lucid-security universe main multiverse restricted
deb http://mirrors.163.com/ubuntu/ lucid-updates universe main multiverse restricted
deb http://mirrors.163.com/ubuntu/ lucid-proposed universe main multiverse restricted
deb-src http://mirrors.163.com/ubuntu/ lucid-proposed universe main multiverse restricted
deb http://mirrors.163.com/ubuntu/ lucid-backports universe main multiverse restricted
deb-src http://mirrors.163.com/ubuntu/ lucid-backports universe main multiverse restricted
deb-src http://mirrors.163.com/ubuntu/ lucid-updates universe main multiverse restricted
"""

def apt_conf():
	apt_file=open('/etc/apt/sources.list','w')
	apt_file.write(apt_conf)
	apt_file.close

def apt_update():
	os_cmd('apt-get update')
	os_cmd('apt-get -y upgrade')

def _install(name):
	os_cmd('apt-get install -y %s' % name)
	os_cmd('apt-get -fy install')
	os_cmd('apt-get -y autoremove')


def install_init():
	apt_f=open('/etc/apt/sources.list','r')
	count = len(apt_f.readlines())
	soft_list = ['build-essential',
				 'gcc',
				 'g++',
				 'make',
				 'libncurses5-dev',
				 'libxp-dev',
				 'libmotif-dev',
				 'libxt-dev',
				 'libstdc++6']
	if count != 10:
		apt_conf()
	else:
		apt_update()
		for soft in soft_list:
			_install(soft)


def folder_and_user_init(name,uname):
	install_base = '/opt/lnmp/app'
	install_name = os.path.join(install_base, name)
	tar_base = '/opt/lnmp/tar_package'
	tar_name = os.path.join(tar_base, name)
	order = 'id %s' % uname
	order = shlex.split(order)
	j_name = subprocess.Popen(order, stdout=subprocess.PIPE)
	if not (uname in str(j_name.stdout.read())):
		os.system('groupadd %s && useradd -g %s %s' % (uname, uname, uname))
	if not(os.path.exists(install_name)):
		os.makedirs(install_name)
		t_uid = pwd.getpwnam(uname).pw_uid
		t_gid = pwd.getpwnam(uname).pw_gid
		os.chown(install_name, t_uid,t_gid)
	if not(os.path.exists(tar_name)):
		os.makedirs(tar_name)

#def os_cmd(order):
#	order = shlex.split(order)
#	o_name = subprocess.Popen(order,stdout=subprocess.PIPE)
#	return str(o_name.stdout.read()) 

