#!/usr/bin/env python
# ftp module
from ftplib import FTP 
import os, shlex, subprocess
import ConfigParser, io
import tarfile

ftp_conf = """
[FTP]
ftp_host = hut.jofgame.com
ftp_user = zhubo
ftp_pw = Jof1Game8yzl
c_path = /opt/lnmp/tar_package
r_path = /lnmp
"""

class ftp_get:
	def __init__(self,conf='ftp_conf'):
		con = ConfigParser.RawConfigParser()
		con.readfp(io.BytesIO(ftp_conf))
		bolme = con.sections()
		FTP_G = bolme[0]
		self.host = con.get(FTP_G,"ftp_host")
		self.user = con.get(FTP_G,"ftp_user")
		self.pw = con.get(FTP_G,"ftp_pw")
		self.c_path = con.get(FTP_G,"c_path")
		self.r_path = con.get(FTP_G,"r_path")

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

class package_manage:
	def __init__(self,name,uname):
		self.name = name
		self.uname = uname
		if name == 'mysql':
			u_name = name
		elif name == 'php':
			u_name = 'www-data'
		elif name == 'nginx':
			u_name = 'www-data'
		else:
			print "no else username define"

	def user_add(self):
		order = 'id %s' %(self.uname)
		order = shlex.split(order)
		j_name = subprocess.Popen(order,stdout=subprocess.PIPE)
		if not(u_name in str(j_name.stdout.read())):
			os.system('groupadd %s && useradd -g %s %s' %(uname, uname, uname))
		name_folder = '/opt/lnmp/app/' + name
		if not(os.path.exists(name_folder)
			os.makedirs(name_folder)
			os.chown(name_folder,uname,uname)

	def targz(self):
		init_base = '/opt/lnmp/app/extend'
		tar_base = '/opt/lnmp/tar_package'
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
			re_h=re.compile('.tar.gz')
			stri=re_h.sub('',str(stri))
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



	def install_init(self):



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
