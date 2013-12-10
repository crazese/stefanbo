from ftplib import FTP
import os, subprocess


def ftp_download(c_path,r_path):
	host = 'hut.jofgame.com'
	user = 'zhubo'
	pw = 'Jof1Game8yzl'
	ftp = FTP(host)
	ftp.login(user,pw)
	os.chdir(c_path)
	ftp.cwd(r_path)
	tar_list = ftp.nlst()
	for fname in tar_list:
		ftp.retrbinary('RETR %s' %(fname),open(fname,'wb').write, 1024)
		open(fname,'wb').close
	ftp.set_debuglevel(0)
	ftp.quit()
	print "ftp download OK!"


def apt_update():
	apt_conf = """
	deb http://mirrors.163.com/ubuntu/ lucid main universe restricted multiverse
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
	apt_file=open('/etc/apt/sources.list','w+')
	apt_file.write(apt_conf)
	os.system('apt-get update')


def install(name):
	os.system('apt-get install -y' + name)
	os.system('apt-get -fy install')
	os.system('apt-get -y autoremove')


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
		apt_update()
	else:
		os.system('apt-get -y upgrade')
		for soft in soft_list:
			install(soft)


def folder_and_user_init(name,uname):
	install_base = '/opt/lnmp/app'
	install_name = os.path.join(install_base, name)
	tar_base = '/opt/lnmp/tar_package'
	tar_name = os.path.join(tar_base, name)
	order = 'id %s' % uname
	order = shlex.split(order)
	j_name = subprocess.Popen(order,stdout=subprocess.PIPE)
	if not (uname in str(j_name.stdout.read())):
		os.system('groupadd %s && useradd -g %s %s' % (uname, uname, uname))
	if not(os.path.exists(install_name)):
		os.makedirs(install_name)
		os.chown(install_name, uname, uname)
	if not(os.path.exists(tar_name)):
		os.makedirs(tar_name)

