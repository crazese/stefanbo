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
		print "-- memcache"
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
		
		elif action == "memcache":
			print "######## I will install memcahce next! ########"
			return 'memcache_install'

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
		for dir in [conf['init_path'], conf['soft_path'], conf['tar_path']]:
			make_dir(dir)

		# download tar package from server
		self.get_tar(conf['tar_path'], conf['ftp_path'])

		# install init package
		self.init_pak_install(init_package, conf['tar_path'], conf['init_path'])

		# install nginx soft
		self.soft_install(conf['soft_name'],
						   soft_package, 
						   conf['tar_path'],
						   conf['soft_path'], 
						   soft_options)

		# nginx conf and website's configuration deployment
		for dir in ['sites-enabled', 'sites-available']:
			make_dir(os.path.join(conf['soft_path'], 'conf', dir))

		for dir in ['bin', 'www', 'log']:
			make_dir(os.path.join(conf['soft_path'], dir))

		# configuration file loading
		for file in ['nginx.conf', 'nginx.sh', 'herouser_virtualhost']:
			# load file into temp_content
			temp_content = read_file(os.path.join('../conf', file))
			
			# write file to the path
			if 'conf' in file:
				file_path = os.path.join(conf['soft_path'], 'conf', file)
				print "I will add the file %s" % file_path
				write_file(file_path, temp_content)

			elif 'sh' in file:
				file_path = os.path.join(conf['soft_path'], 'bin', file)
				print "I will add the file %s" % file_path
				write_file(file_path, temp_content)

			elif 'herouser' in file:
				for t_path in ['sites-available', 'sites-enabled']
					file_path = os.path.join(conf['soft_path'], t_path, file)
					print "I will add the file %s" % file_path
					write_file(file_path, temp_content)

			else:
				print "######## Something Wrong! ########"
		
		# chown the nginx install path
		j_folder_chown(conf['user_name'], conf['user_name'], conf['soft_path'])


	def mysql_install(self):
		'''
		Install mysql on the system!
		'''
		# soft need to download from ftp server
		init_package = ['ncurses-5.9.tar.gz',
						'cmake-2.8.12.1.tar.gz'
					   ]
		# mysql soft
		soft_package = 'mysql-5.5.34.tar.gz'

		# install procedure setting
		conf = {
				'tar_path' 		: 	'/opt/lnmp/tar_package/mysql',
				'ftp_path' 		: 	'/lnmp/mysql',
				'init_path'		: 	'/opt/lnmp/app/init'
				'soft_path'		: 	'/opt/lnmp/app/mysql',
				'soft_name'		: 	'mysql',
				'user_name'		: 	'mysql',
				'mysql_user'	: 	'root',
				'mysql_pw'		:	'123456'
				}

		# configure options
		soft_options = '''
			-DMYSQL_DATADIR=%s \
			-DSYSCONFDIR=%s \
			-DEXTRA_CHARSETS=all \
			-DDEFAULT_CHARSET=utf8 \
			-DDEFAULT_COLLATION=utf8_general_ci \
			-DENABLED_LOCAL_INFILE=1 \
			-DWITH_READLINE=1 \
			-DWITH_DEBUG=0 \
			-DWITH_EMBEDDED_SERVER=1 \
			-DWITH_INNOBASE_STORAGE_ENGINE=1''' % (os.path.join(conf['soft_path'],'data'),
												   os.path.join(conf['soft_path'],'etc'))

		# add user
		add_user(conf['user_name'], conf['user_name'])

		# make directory
		for dir in [conf['init_path'], conf['soft_path']]:
			make_dir(dir)

		for dir in ['data', 'etc', 'log']:
			make_dir(os.path.join(conf['soft_path'], dir))
			if dir == 'etc':
				make_dir(os.path.join(conf['soft_path'], dir, 'init.d'))

		# download tar package from server
		self.get_tar(conf['tar_path'], conf['ftp_path'])

		# install init package
		self.init_pak_install(init_package, conf['tar_path'], conf['init_path'])

		# install mysql soft
		self.soft_install(conf['soft_name'], 
						  soft_package,
						  conf['tar_path'], 
						  conf['soft_path'],
						  soft_options)

		# mysql conf and run the scripts
		# configuration file loading
		for file in ['my.cnf', 'mysql.server']:
			# load file into temp_content
			temp_content = read_file(os.path.join('../conf', file))

			# write file to the path
			if 'cnf' in file:
				file_path = os.path.join(conf['soft_path'], 'etc', file)
				print "I will add the file %s" % file_path
				write_file(file_path, temp_content)

			elif 'mysql.server' in file:
				file_path = os.path.join(conf['soft_path'], 'etc/init.d', file)
				print "I will add the file %s" % file_path
				write_file(file_path, temp_content)

			else:
				print "Something Wrong!"

		# mysql_install_db file path
		install_file = os.path.join(conf['tar_path'], 
										filter_tar(conf['soft_package']),
										'scripts/mysql_install_db')
		# run the install_file
		# add +x to the scripts
		os_cmd('chmod +x %s' % install_file)
		# install_options
		install_options = '''
		--user=%s \
		--defaults-file=%s \
		--datadir=%s \
		--basedir=%s ''' % (conf['user_name'], 
							os.path.join(conf['soft_path'], 'etc/my.cnf'),
							os.path.join(conf['soft_path'], 'data'),
							conf['soft_path'])
		print "I will run the scripts"
		try:
			os_cmd('%s %s' % (install_file, install_options))
		except OSError as e:
			print >>sys.stdout, "Execution Failed", e

		# mysql initialization
		j_folder_chown(conf['user_name'], conf['user_name'])

		# start mysql service
		print "I will start the mysql server"
		os_cmd('%s/etc/init.d/mysql.server %s' % (conf['soft_path'],  'start'))
		
		# add privileges
		print "I will add the priviledge to mysql"
		os_cmd('%s/bin/mysqladmin -u %s password %s' % (conf['soft_path'],
														conf['mysql_user'], 
														conf['mysql_pw']))

		# load the sql to sql_content 
		sql_content = read_file('../conf/mysql_sec_script.sql')
		sql_file = os.path.join(conf['soft_path'], 'mysql_sec_script.sql')
		# write the sql to local system 
		try:
			write_file(sql_file, sql_content)
		except OSError as e:
			print >>sys.stdout , 'Execution Failed', e

		# run the mysql_sec_script.sql
		print "I will run the sql to complete the initialization !"
		mysql_bin = os.path.join(conf['soft_path'], 'bin', 'mysql')
		os_cmd('%s -u %s -p%s <%s ' % (mysql_bin, 
									   conf['mysql_user'], 
									   conf['mysql_pw'], 
									   sql_file))

		print "Mysql install complete ! "
		print "To start/stop the mysql, to run %s start/stop" % os.path.join(conf['soft_path'], 'etc/init.d/mysql.server')


	def php_install(self):
		'''
		Install the php soft 
		'''
		# soft need to download from ftp server
		init_package = ['jpegsrc.v9.tar.gz',
						'libevent-2.0.21.stable.tar.gz',
						'freetype-2.4.12.tar.gz',
						'mhash-0.9.9.9.tar.gz',
						'curl-7.33.0.tar.gz',
						'libpng-1.6.7.tar.gz',
						'pkg-config-0.24.tar.gz',
						'xproto-7.0.14.tar.bz2',
						'xextproto-7.0.4.tar.bz2',
						'xtrans-1.2.7.tar.bz2',
						'xcb-proto-1.5.tar.bz2',
						'libxslt-1.1.24.tar.gz',
						'gd-2.0.35.tar.gz']
		soft_package =	'php-5.3.10.tar.gz'

		# install procedure setting
		conf = {
				'tar_path'		:		'/opt/lnmp/tar_package/php',
				'ftp_path'		:		'/lnmp/php',
				'init_path'		:		'/opt/lnmp/app/init',
				'soft_path'		:		'/opt/lnmp/app/php',
				'soft_name'		:		'php',
				'user_name'		:		'www-data',
				'mysql_path'	:		'/opt/lnmp/app/mysql'
		}

		# php configure options
		soft_options = '''
				--with-bz2 \
				--with-config-file-path=%s \
				--with-curl \
				--with-freetype-dir=%s \
				--with-fpm-user=%s \
				--with-fpm-group=%s \
				--with-gettext \
				--with-gd \
				--with-jpeg-dir=%s \
				--with-libxml-dir=%s \
				--with-mysql=%s \
				--with-mhash \
				--with-mysqli \
				--with-openssl-dir=%s \
				--with-png-dir=%s \
				--with-pdo-mysql=%s \
				--with-xmlrpc \
				--with-zlib-dir=%s \
				--without-pear \
				--enable-bcmath \
				--enable-calendar \
				--enable-dba \
				--enable-exif \
				--enable-fpm \
				--enable-fileinfo \
				--enable-ftp \
				--enable-gd-native-ttf \
				--enable-inline-optimization \
				--enable-mbregex \
				--enable-mbstring \
				--enable-magic-quotes \
				--enable-embedded-mysqli \
				--enable-pcntl \
				--enable-pdo \
				--enable-safe-mode \
				--enable-shmop \
				--enable-sysvsem \
				--enable-sysvmsg \
				--enable-sockets \
				--enable-soap \
				--enable-tokenizer \
				--enable-wddx \
				--enable-xml \
				--enable-zip \
				--disable-rpath
				''' % (os.path.join(conf['soft_path'], 'etc'),
					   conf['init_path'],
					   conf['user_name'],
					   conf['user_name'],
					   conf['init_path'],
					   conf['init_path'],
					   conf['mysql_path'],
					   conf['init_path'],
					   conf['init_path'],
					   conf['mysql_path'],
					   conf['init_path'])

		# add user
		add_user(conf['user_name'], conf['user_name'])

		# make directory
		for dir in [conf['init_path'], conf['soft_path'], conf['tar_path']]:
			make_dir(dir)

		# download tar package from server
		self.get_tar(conf['tar_path'], conf['ftp_path'])

		# install init_package
		self.init_pak_install(init_package, conf['tar_path'], conf['init_path'])

		# install php soft
		self.soft_install(conf['soft_name'],
						  soft_package,
						  conf['tar_path'],
						  conf['soft_path'],
						  soft_options)

		# php conf settings






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

			elif 'jpegsrc' in folder_name:
				temp_options = '--enable-shared --enable-static'
				file_config(os.path.join(tar_path, folder_name), init_path, temp_options)
				file_make(os.path.join(path, folder_name))

			elif 'libpng' in folder_name:
				os_cmd('''export LDFLAGS="-L%s" ''' % os.path.join(init_path, 'lib')
				os_cmd('''export CPPFLAGS=')




	def soft_install(self, name, soft_package, tar_path, soft_path, options):
		''' install soft in the system '''
		print " I will change to directory %s" % tar_path
		os.chdir(tar_path)

		# extract file with extract_file()
		extract_file(soft_package)

		# cofigure file with file_config()
		folder_name = filter_tar(soft_package)
		file_config(os.path.join(tar_path, folder_name), soft_path, options)

		# if the soft name is nginx, it need to change the Makefile 
		if name == 'nginx':
			# change the Makefile to tend to adjust the init if soft name is nginx
			makefile = os.path.join(path,folder_name,'/objs/Makefile')
			makefile = read_file(makefile)
			result_1 = re.sub(r'./configure','./configure --prefix=/opt/lnmp/app/init',makefile)
			result = re.sub(r'./configure --prefix=/opt/lnmp/app/init --disable-shared','./configure --prefix=/opt/lnmp/app/init',result_1)
			write_file(makefile, result)

		elif name == 'mysql':
			# do nothing
			pass

		elif name == 'php':
			#
			pass

		elif name == 'memcache':
			#
			pass

		else:
			pass

		# make file with file_make()
		file_make(os.path.join(path, folder_name))



		




a_install = Install("init_install")
a_install.install_start()