#!/usr/bin/env python
# ftp module
from ftplib import FTP 
import os, shlex, subprocess
import ConfigParser, io
import tarfile, zipfile

class ftp_get(object):
	def __init__(self):
		self.host = 'hut.jofgame.com'
		self.user = 'zhubo'
		self.pw = 'Jof1Game8yzl'
		self.c_path = '/opt/lnmp/tar_package'
		self.r_path = '/lnmp'

	def ftp_download(self):
		ftp = FTP(self.host)
		ftp.login(self.user,self.pw)
		os.chdir(self.c_path)
		ftp.cwd(self.r_path)
		tar_list = ftp.nlst()
		for fname in tar_list:
			ftp.retrbinary('RETR %s' %(fname),open(fname,'wb').write, 1024)
			open(fname,'wb').close
		ftp.set_debuglevel(0)
		ftp.quit()
		print "ftp download OK!"

class enviroment_init(object):
	def __init__(self,name):
		self.name = name
		if name == 'mysql':
			self.u_name = name
		elif name == 'php' or name == 'nginx':
			self.u_name = 'www-data'
		else:
			print "no else username will be add!"
		self.init_base = '/opt/lnmp/app/extend'
		self.tar_base  = '/opt/lnmp/tar_package'

	def user_add(self):
		order = 'id %s' %(self.uname)
		order = shlex.split(order)
		j_name = subprocess.Popen(order,stdout=subprocess.PIPE)
		if not(u_name in str(j_name.stdout.read())):
			os.system('groupadd %s && useradd -g %s %s' %(uname, uname, uname))
		name_folder = '/opt/lnmp/app/' + name
		if not(os.path.exists(name_folder)):
			os.makedirs(name_folder)
			os.chown(name_folder,uname,uname)

	def apt_update(self):
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
		self.apt_file.write(apt_conf)
		os.system('apt-get update')

	def install_init(self):
		apt_file=open('/etc/apt/sources.list','r')
		count = len(apt_file.readlines())
		soft_list = ['build-essential','gcc','g++','make']
		if count != 10:
			self.apt_update()
		else:
			os.system('apt-get -y upgrade')
			for soft in soft_list:
				os.system('apt-get install -y' + soft)

	def extract_file(self,path,to_directory='.'):
		self.path = path
		self.to_directory = to_directory
		if path.endswith('.zip'):
			opener, mode = zipfile.ZipFile, 'r'
		elif path.endswith('.tar.gz') or path.endswith('.tgz'):
			opener, mode = tarfile.open, 'r:gz'
		elif path.endswith('tar.bz2') or path.endswith('.tbz'):
			opener, mode = tarfile.open, 'r:bz2'
		else:
			raise ValueError, "Could not extract `%s` as no appropriate extractor is found" % path
		cwd = os.getcwd()
		os.chdir(to_directory)

		try:
			file = opener(path, mode)
			try: file.extractall()
			finally: file.close()
		finally:
			os.chdir(cwd)



	def package_install(self):
		package_list = ['m4-1.4.9.tar.gz',
		 				'autoconf-2.13.tar.gz',
		 				'libiconv-1.14.tar.gz',
		 				'libmcrypt-2.5.8.tar.gz',
		 				'libxml2-2.7.8.tar.gz',
		 				'pcre-8.30.tar.gz',
		 				'openssl-1.0.1e.tar.gz',
		 				'zlib-1.2.8.tar.gz',
		 				'cmake-2.8.12.1.tar.gz',
		 				'jpegsrc.v9.tar.gz',
		 				'libevent-2.0.21-stable.tar.gz',
		 				'freetype-2.4.12.tar.gz',
		 				'mhash-0.9.9.9.tar.gz',
		 				'curl-7.33.0.tar.gz',
		 				'libpng-1.6.7.tar.gz',
		 				'pkg-config-0.24.tar.gz',
		 				'xproto-7.0.14.tar.bz2',
		 				'xextproto-7.0.4.tar.bz2',
		 				'xtrans-1.2.7.tar.bz2',
		 				'xcb-proto-1.5.tar.bz2',
		 				'libxslt-1.1.24.tar.gz']
		folder_list = []
		
		def filter(stri):
			if '.tar.gz' in stri:
				stri.strip('.tar.gz')
				return stri
			elif '.tar.bz2' in stri:
				stri.strip('.tar.bz2')
				return stri
			elif '.zip' in stri:
				stri.strip('.zip')
				return stri

		for package in package_list:
			tar = tarfile.open(package)
			tar.extractall()
			tar.close()
			folder_name = filter(package)
			os.chdir(os.path.join(tar_base,folder_name))
			args = '--prefix=' + init_base
			config = './configure %s' %(args)
			os.system(config + '&& make && make install')
			if 'mcrypt' in folder_name:
				os.chdir(os.path.join(tar_base,folder_name,libltdl))
				os.system('./configure --enable-ltdl-install && make && make install')







# apt source update
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
apt_file.write(apt_source)


# install nginx 
