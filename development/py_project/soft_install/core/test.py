# -*- coding: utf-8 -*-
# import Python Lib
import re
import sys
sys.path.append('..')

# import some function from utils
from utils.apt_tool import *
from utils.ftp_tool import *
from utils.os_tool import *
from utils.con_tool import *

from utils.apt_tool import Apt


class Install(object):

	def __init__(self, start):
		# ftp loging setting
		self.host = 'hut.jofgame.com'
		self.user = 'zhubo'
		self.pw = 'Jof1Game8yzl'

		self.cwd = os.getcwd()

		self.start = start

	def install_start(self):
		next = self.start

		while True:
			print "\n-----------"
			soft = getattr(self, next)
			next = soft()

	def install_end(self):
		print "######## Thanks for you use the soft! #########"
		sys.exit(0)


	def start_point(self):
		# You need to choose what soft you want to install
		# nginx or mysql or php
		print "What soft do you want to install next or others ?"
		print "-- init 		# you must run it first if you run the scripts first on the host"
		print "-- nginx"
		print "-- php"
		print "-- mysql"
		print "-- memcache"
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

		elif action == "quit":
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
		zlib_path = os.path.join(conf['tar_path'], filter_tar(init_package[7]))
		
		# configure options
		soft_options = ''' \
			--sbin-path=%s \
			--pid-path=%s \
			--user=%s \
			--group=%s \
			--with-http_stub_status_module \
			--with-http_ssl_module \
			--with-http_gzip_static_module \
			--with-pcre=%s \
			--with-openssl=%s \
			--with-zlib=%s''' % (os.path.join(conf['soft_path'], 'sbin', 'nginx'),
								 os.path.join(conf['soft_path'], 'nginx.pid'),
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
			os.chdir(self.cwd)
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
				for t_path in ['sites-available', 'sites-enabled']:
					file_path = os.path.join(conf['soft_path'], 'conf', t_path, file)
					print "I will add the file %s" % file_path
					write_file(file_path, temp_content)

			else:
				print "######## Something Wrong! ########"
		
		# chown the nginx install path
		j_folder_chown(conf['user_name'], conf['user_name'], conf['soft_path'])

		# come back to function start_point
		print "######## The system install nginx complete successfully! #########"
		return 'start_point'

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
				'init_path'		: 	'/opt/lnmp/app/init',
				'soft_path'		: 	'/opt/lnmp/app/mysql',
				'soft_name'		: 	'mysql',
				'user_name'		: 	'mysql',
				'mysql_user'	: 	'root',
				'mysql_pw'		:	'123456'
				}

		# configure options
		soft_options = ''' \
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
			os.chdir(self.cwd)
			temp_content = read_file(os.path.join('../conf', file))

			# write file to the path
			if 'cnf' in file:
				file_path = os.path.join(conf['soft_path'], 'etc', file)
				print "I will add the file %s" % file_path
				write_file(file_path, temp_content)

			elif 'mysql.server' in file:
				file_path = os.path.join(conf['tar_path'], filter_tar(soft_package), 'support-files', file)
				print "I will add the file %s" % file_path
				os_cmd('cp %s %s' % (file_path, os.path.join(conf['soft_path'], 'etc/init.d', file)))
				os_cmd('chmod +x %s' % os.path.join(conf['soft_path'], 'etc/init.d', file))

			else:
				print "Something Wrong!"

		# mysql_install_db file path
		install_file = os.path.join(conf['tar_path'], 
										filter_tar(soft_package),
										'scripts/mysql_install_db')
		# run the install_file
		# add +x to the scripts
		os_cmd('chmod +x %s' % install_file)
		# install_options
		install_options = ''' \
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
		j_folder_chown(conf['user_name'], conf['user_name'], conf['soft_path'])

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

		# return to the function start_point
		print "######## The system install MySQL complete successfully! #########"
		return 'start_point'

	def php_install(self):
		'''
		Install the php soft 
		'''
		# soft need to download from ftp server
		init_package = ['gd-2.0.35.tar.gz']
		soft_package =	'php-5.3.10.tar.gz'
		extend_package = ['APC-3.1.9.tgz',
						  'bz2-1.0.tgz',
						  'igbinary-igbinary-1.1.1-28-gc35d48f.zip',
						  'libmemcached-1.0.10.tar.gz',
						  'memcached-2.1.0.tgz',
						  'memcache-2.2.7.tgz'
						  ]
		# install procedure setting
		conf = {
				'tar_path'		:		'/opt/lnmp/tar_package/php',
				'ftp_path'		:		'/lnmp/php',
				'init_path'		:		'/opt/lnmp/app/init',
				'soft_path'		:		'/opt/lnmp/app/php',
				'soft_name'		:		'php',
				'user_name'		:		'www-data',
				'mysql_path'	:		'/opt/lnmp/app/mysql',
				'php_config'	:		'/opt/lnmp/app/php/bin/php-config',
				'phpize_bin'	:		'/opt/lnmp/app/php/bin/phpize'
		}

		# php configure options
		soft_options = ''' \
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

		for dir in ['etc','etc/init.d/']:
			make_dir(os.path.join(conf['soft_path'], dir))

		# download tar package from server
		#self.get_tar(conf['tar_path'], conf['ftp_path'])

		# install init_package
		self.init_pak_install(init_package, conf['tar_path'], conf['init_path'])

		# install php soft
		self.soft_install(conf['soft_name'],
						  soft_package,
						  conf['tar_path'],
						  conf['soft_path'],
						  soft_options)

		# php conf settings
		# php.ini and php-fpm configuration
		for file in ['php.ini', 'php-fpm.conf']:
			os.chdir(self.cwd)
			temp_file = read_file(os.path.join('../conf', file))
			write_file(os.path.join(conf['soft_path'], 'etc', file ), temp_file)

		# php-fpm start scripts
		os.chdir(self.cwd)
		phpfpm = read_file('../conf/php-fpm.sh')
		write_file(os.path.join(conf['soft_path'], 'etc/init.d/php-fpm'), phpfpm)

		self.extentd_soft_install(extend_package, 
								  conf['tar_path'],
								  conf['init_path'],
								  conf['phpize_path'],
								  conf['phpconfig_path'])

		print "######## The system install php complete successfully! #########"
		return 'start_point'


	def memcache_install(self):
		''' Install memcache soft in the system 
		'''
		# soft need to download from ftp server
		soft_package = 'memcached-1.4.9.tar.gz'

		# install procedure configuration
		conf = {
				'tar_path'		:		'/opt/lnmp/tar_package/memcache',
				'ftp_path'		:		'/lnmp/memcache',
				'init_path'		:		'/opt/lnmp/app/init',
				'soft_path'		:		'/opt/lnmp/app/memcache',
				'soft_name'		:		'memcache'
		}

		# configure options
		soft_options = '--with-libevent=%s' % conf['init_path']

		# make directory
		for dir in [conf['init_path'], conf['soft_path'], conf['tar_path']]:
			make_dir(dir)

		# download tar package from ftp server
		self.get_tar(conf['tar_path'], conf['ftp_path'])

		# install memcache soft
		self.soft_install(conf['soft_name'],
						  soft_package,
						  conf['tar_path'],
						  conf['soft_path'],
						  soft_options)

		print "######## The system install memcache complete successfully! #########"
		print '''######## to start memcache, you need to run follow scripts in root:
				/opt/lnmp/app/memcache/bin/memcached -m 64 -p 11211 -u www-data -l 127.0.0.1'''

		return 'start_point'


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
			# export PATH AND LD_LIBRARY_PATH
			self.export_tool(init_path)
			# configure the soft
			# some soft need install additional options
			if 'jpegsrc' in folder_name:
				folder_name = 'jpeg-9'
				temp_options = '--enable-shared --enable-static'
				file_config(os.path.join(tar_path, folder_name), init_path, temp_options)

			elif 'libpng' in folder_name:
				#os_cmd('''export LDFLAGS="-L%s" ''' % os.path.join(init_path, 'lib'))
				#os_cmd('''export CPPFLAGS="-I%s" ''' % os.path.join(init_path, 'include'))
				print '''I will add LDFLAGS="-L%s"'''  % os.path.join(init_path, 'lib')
				print '''I will add CPPFLAGS="-L%s"'''  % os.path.join(init_path, 'include')
				#os.environ['LDFLAGS'] = '-L%s' % os.path.join(init_path, 'lib')
				#os.environ['CPPFLAGS'] = '-I%s' % os.path.join(init_path, 'include')
				os.environ.setdefault('LDFLAGS','-L%s' % os.path.join(init_path, 'lib'))
				os.environ.setdefault('CPPFLAGS','-I%s' % os.path.join(init_path, 'include'))

				file_config(os.path.join(tar_path, folder_name), init_path)

			elif 'libxslt' in folder_name:
				temp_options = '--with-libxml-prefix=%s' % init_path
				file_config(os.path.join(tar_path, folder_name), init_path, temp_options)

			elif 'gd' in folder_name:
				temp_options = '''--with-png=%s --with-freetype=%s --with-jpeg=%s''' % (init_path, init_path, init_path)
				file_config(os.path.join(tar_path, folder_name), init_path, temp_options)
				
				# change 'zlib.h' to /opt/lnmp/app/init/include/zlib.h
				temp_file = os.path.join(tar_path, folder_name, 'gd_gd2.c')
				temp_content = read_file(temp_file)
				result = re.sub(r'zlib.h','%s/zlib.h' % os.path.join(init_path, 'include'), temp_content)
				write_file(temp_file, result)

			elif 'autoconf' in folder_name:
				file_config(os.path.join(tar_path, folder_name), init_path)
				
				temp_file = os.path.join(tar_path, folder_name, 'Makefile')
				temp_content = read_file(temp_file)
				result = re.sub(r'M4 = m4','M4 = %s/m4' % os.path.join(init_path, 'bin') , temp_content)
				write_file(temp_file, result)

			else:
				file_config(os.path.join(tar_path, folder_name), init_path)

			file_make(os.path.join(tar_path, folder_name))
			print "The soft %s install complete!" % folder_name

			if 'mcrypt' in folder_name:
				temp_path = os.path.join(tar_path, folder_name, 'libltdl')
				file_config(temp_path, init_path, '--enable-ltdl-install')
				file_make(temp_path)

			print "I will change to the %s" % tar_path
			os.chdir(tar_path)
			test = raw_input('>>>')


	def extentd_soft_install(self, soft_list, tar_path, init_path, phpize_path, phpconfig_path):
		''' Install phpextend soft 
		'''
		print "I will change to directory %s" % tar_path
		os.chdir(tar_path)

		# configure file with phpize and configure
		for soft in soft_list:
			extract_file(soft)
			folder_name = filter_tar(soft)

			if 'bz2-1' in folder_name:
				temp_options = '--with-php-config=%s' % phpconfig_path
				file_config(os.path.join(tar_path, folder_name), init_path, temp_options)
			
			elif 'libmemc' in folder_name:
				file_config(os.path.join(tar_path, folder_name), init_path)
			
			elif 'APC' in folder_name:
				temp_options = '--enable-apc --with-apc-mmap --with-php-config=%s' % phpconfig_path
				self.phpize(os.path.join(tar_path, folder_name), phpize_path)
				file_config(os.path.join(tar_path, folder_name), init_path, temp_options)

			elif 'igbinary' in folder_name:
				temp_options = '--enable-igbinary --with-php-config=%s' % phpconfig_path
				self.phpize(os.path.join(tar_path, folder_name), phpize_path)
				file_config(os.path.join(tar_path, folder_name), init_path, temp_options)

			elif 'memcached-2.1.0' == folder_name:
				temp_options = '''--enable-memcached \
								  --enable-memcached-igbinary \
								  --with-php-config=%s \
								  --with-zlib-dir=%s \
								  --with-libmemcached-dir=%s''' % (phpconfig_path,
								  								   init_path,
								  								   init_path)
				self.phpize(os.path.join(tar_path, folder_name), phpize_path)
				file_config(os.path.join(tar_path, folder_name), init_path, temp_options)

			elif 'memcache-2.2.7' == folder_name:
				temp_options = '--with-php-config=%s' % phpconfig_path
				self.phpize(os.path.join(tar_path, folder_name), phpize_path)
				file_config(os.path.join(tar_path, folder_name), init_path, temp_options)

			else:
				file_config(os.path.join(tar_path, folder_name), init_path)

			file_make(os.path.join(tar_path, folder_name))
			print "The soft %s install complete!"

			print "I will change to the %s" % tar_path
			os.chdir(tar_path)
			test = raw_input('>>>')


	def soft_install(self, name, soft_package, tar_path, soft_path, options):
		''' install soft in the system '''
		print " I will change to directory %s" % tar_path
		os.chdir(tar_path)

		# extract file with extract_file()
		extract_file(soft_package)

		# cofigure file with file_config()
		folder_name = filter_tar(soft_package)
		file_config(os.path.join(tar_path, folder_name), soft_path, options)
		test = raw_input('>>>')

		# if the soft name is nginx, it need to change the Makefile 
		if name == 'nginx':
			# change the Makefile to tend to adjust the init if soft name is nginx
			makefile = '%s/objs/Makefile' % os.path.join(tar_path, folder_name)
			print makefile
			makefile_content = read_file(makefile)
			result_1 = re.sub(r'./configure','./configure --prefix=/opt/lnmp/app/init',makefile_content)
			result = re.sub(r'./configure --prefix=/opt/lnmp/app/init --disable-shared','./configure --prefix=/opt/lnmp/app/init ', result_1)
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
		file_make(os.path.join(tar_path, folder_name))

		# export PATH and LD_LIBRARY_PATH
		self.export_tool(soft_path)

		os.chdir(tar_path)
		test = raw_input('>>>')


	def export_tool(self, path):
		'''
		Export the LD_LIBRARY_PATH to 'libpath' example like : /opt/lnmp/app/init/
		It will change to the directory /opt/lnmp/app/init/lib
		'''
		#os_cmd('export LD_LIBRARY_PATH=%s:$LD_LIBRARY_PATH' % os.path.join(path, 'lib'))
		#os_cmd('export PATH=%s:$PATH' % os.path.join(path, 'bin'))
		libpath = 'LD_LIBRARY_PATH'
		binpath = 'PATH'

		L_key = [libpath, binpath]
		L_val = ['lib', 'bin']

		for key , val in zip(L_key, L_val):
			if not os.environ.has_key(key):
				os.environ.setdefault(key, os.path.join(path, val))
			else:
				os.environ[key] = '%s:%s' % (os.path.join(path, val), os.environ[key])

			print "I will add %s = %s" % (key, os.path.join(path, val))

		# print the PATH and LD_LIBRARY_PATH
		os_cmd('echo $PATH && echo $LD_LIBRARY_PATH')

		#if not os.environ.has_key(binpath):
#			os.environ.setdefault(binpath, os.path.join(path, 'bin'))
#		else:
#			os.environ[binpath] = '%s:%s' % (os.path.join(path, 'bin')), os.environ[binpath]#

#		os.environ['LD_LIBRARY_PATH'] = '%s:%s' % (os.path.join(path, 'lib'), os.environ['LD_LIBRARY_PATH'])
#		os.environ['PATH'] = '%s:%s' % (os.path.join(path, 'bin'), os.environ['PATH'])

	def phpize(self, path, phpize_cmd):
		'''phpize the path '''
		os.chdir(path)
		os_cmd(phpize_cmd)


		




a_install = Install("start_point")
a_install.install_start()