# -*- coding: utf-8 -*-
# import Python Lib
import sys
sys.path.append('..')

# import some function from utils
from utils.apt_tool import *
from utils.ftp_tool import *
from util.os_tool import *
from utils.apt_tool import Apt

class Install(object):

	def __init__(self, start):
		# ftp loging setting
		self.host = 'hut.jofgame.com'
		self.user = 'zhubo'
		self.pw = 'Jof1Game8yzl'

		self.start = start

	def install_start(self):
		next = self.start

		while True:
			print "\n-----------"
			soft = getattr(self, next)
			next = soft()

	def install_end(self):
		print "######## Thanks for you use the soft! #########"
		exit(0)


	def start_point(self):
		# You need to choose what soft you want to install
		# nginx or mysql or php
		print "What soft do you want to install next or others ?"
		print "-- nginx"
		print "-- php"
		print "-- mysql"
		print "-- init 		# init the system"
		print "-- quit		# exit the soft install procedure"

		action = raw_input(">>>")

		if action == "nginx":
			print "######## I will install nginx next! ########"
			return 'nginx_install'

		elif action == "php":
			print "######## I  will install php next! ########"
			return 'php_install'

		elif action == "mysql":
			print "######## I will install mysql next! #######"
			return 'mysql_install'

		elif action == "init":
			print "######## I will init the system with apt tools! ########"
			return 'init_install'
		
		elif action == "end":
			print "######## You will quit the soft install procedure! ########"
			return 'install_end'

		else:
			print "######## WRONG INPUT!! ########"
			return 'start_point'


	def init_install(self):
		'''
		Init the system , change the /etc/apt/source.list and update it.
		'''	
		apt = Apt()
		apt.apt_mod()
		apt.apt_update()

		soft_list = ['build-essential',
				 'gcc',
				 'g++',
				 'make',
				 'libncurses5-dev',
				 'libxp-dev',
				 'libmotif-dev',
				 'libxt-dev',
				 'libstdc++6']
		for soft in soft_list:
			apt.apt_install(soft)

		print "######## The system init install complete successfully! #########"
		return 'start_point'


	def nginx_install(self):
		'''
		Install the nginx soft 
		'''
		# soft need to download from ftp server
		init_package = ['m4-1.4.9.tar.gz',
						'autoconf-2.13.tar.gz',
						'libiconv-1.14.tar.gz',
						'libmcrypt-2.5.8.tar.gz',
						'libxml2-2.7.8.tar.gz',
						'pcre-8.30.tar.gz',
						'openssl-1.0.1e.tar.gz',
						'zlib-1.2.8.tar.gz'
						]
		soft_package = 'nginx-1.0.9.tar.gz'

		# install procedure setting
		conf = {
				'tar_path' 		: 	'/opt/lnmp/tar_package/nginx',
				'ftp_path' 		: 	'/lnmp/nginx',
				'init_path'		: 	'/opt/lnmp/app/init',
				'soft_path'		: 	'/opt/lnmp/app/nginx',
				'soft_name'		: 	'nginx',
				'user_name'		: 	'www-data'
				}

		# nginx insatll pcre , openssl and zlib source path
		pcre_path = os.path.join(conf['tar_path'], filter_tar(init_package[5]))
		openssl_path = os.path.join(conf['tar_path'], filter_tar(init_package[6]))
		zlib_path = os.path.join(self.conf['tar_path'], filter_tar(init_package[7]))
		
		# configure options
		soft_options = '''
			--sbin-path=%s/sbin/ \
			--pid-path=%s/nginx.pid \
			--user=%s \
			--group=%s \
			--with-http_stub_status_module \
			--with-http_ssl_module \
			--whti-http_gzip_static_module \
			--with-pcre=%s \
			--with-openssl=%s \
			--with-zlib=%s''' % (conf['soft_path'],
								 conf['soft_path'],
								 conf['user_name'],
								 conf['user_name'],
								 pcre_path,
								 openssl_path,
								 zlib_path)
		# add user
		add_user(conf['user_name'], conf['user_name'])
		
		# make directory
		for dir in [conf['init_path'], conf['soft_path']]:
			make_dir(dir)

		# download tar package from server
		self.get_tar(conf['tar_path'], conf['ftp_path'])
		

	def get_tar(self, tar_path, ftp_path):
		make_dir(tar_path)
		
		download(self.host,
				 self.user,
				 self.pw,
				 ftp_path,
				 tar_path)


	def init_pak_install(self, soft_list, tar_path, init_path):
		''' extract the 'init_package' , and configure it , make install it '''
		print "I will change to directory %s" % tar_path
		os.chdir(tar_path)

		# install soft with file_config(), file_make()
		for soft in soft_list:
			extract_file(soft)
			folder_name = filter_tar(soft)
			file_config(os.path.join(tar_path, folder_name), init_path)
			file_make(os.path.join(path, folder_name))
			
			# some soft need install additional options
			if 'mcrypt' in folder_name:
				temp_path = os.path.join(tar_path, folder_name, 'libltdl')
				file_config(temp_path, init_path)
				file_make(temp_path)

			elif :




	def soft_install(self):
		''' install soft in the system '''
		print " I will change to directory %s" % self.conf['tar_path']
		path = self.conf['tar_path']
		os.chdir(path)

		# extract file with extract_file()
		extract_file(self.soft_package)
		# cofigure file with file_config()
		folder_name = filter_tar(self.soft_package)
		file_config(os.path.join(path, folder_name),
					self.conf['soft_path'])

		# if the soft name is nginx, it need to change the Makefile 
		if self.name = 'nginx':
			# change the Makefile to tend to adjust the init if soft name is nginx
			makefile = os.path.join(path,folder_name,'/objs/Makefile')
			makefile = read_file(makefile)
			result_1 = re.sub(r'./configure','./configure --prefix=/opt/lnmp/app/init',makefile)
			result = re.sub(r'./configure --prefix=/opt/lnmp/app/init --disable-shared','./configure --prefix=/opt/lnmp/app/init',result_1)
			write_file(makefile, result)

		# make file with file_make()
		file_make(os.path.join(path, folder_name))



		




a_install = Install("init_install")
a_install.install_start()