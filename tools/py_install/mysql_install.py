#!/usr/bin/env python
# install mysql

import environment_init
import tools

base = {
	'tar_path' 		: 	'/opt/lnmp/tar_package/mysql',
	'ftp_path' 		: 	'/lnmp/mysql',
	'soft_path'		: 	'/opt/lnmp/app',
	'soft_name'		: 	'mysql',
	'user_name'		: 	'mysql'
}

mysql_path = os.path.join(base['soft_path'],base['soft_name'])

cwd = os.getcwd()

init_package = ['ncurses-5.9.tar.gz',
				'cmake-2.8.12.1.tar.gz'
				]

soft_package = 'mysql-5.5.34.tar.gz'
# prepare work
environment_init.folder_and_user_init(base['soft_name'],base['user_name'])
environment_init.ftp_download(tar_mysql_base,ftp_mysql_base)
environment_init.apt_update()
environment_init.install_init()

# install init_package
os.chdir(tar_mysql_base)
for soft in init_package:
	tools.extract_file(soft)
	folder_name = tools.filter(soft)
	tools.pak_configure(folder_name,folder_name,install_base,tar_mysql_base)
	tools.pak_make(folder_name,tar_mysql_base)

# install mysql
# extract mysql.tar
tools.extract_file(mysql_package)
# configure options
for dir in ['data','etc']
	tools.make_dir(dir,mysql_path)

options = 
"""
-DMYSQL_DATADIR=%s \
-DSYSCONFDIR=%s \
-DEXTRA_CHARSETS=all \
-DDEFAULT_CHARSET=utf8 \
-DDEFAULT_COLLATION=utf8_general_ci \
-DENABLED_LOCAL_INFILE=1 \
-DWITH_READLINE=1 \
-DWITH_DEBUG=0 \
-DWITH_EMBEDDED_SERVER=1 \
-DWITH_INNOBASE_STORAGE_ENGINE=1
""" % (os.path.join(mysql_path,'data'),os.path.join(mysql_path,'etc'))

# configure
tools.pak_configure(soft_name,soft_name,install_base,tar_mysql_base,options)

# make
tools.pak_make(tools.filter(mysql_package),tar_mysql_base)

# config mysql
# directory create init.d
tools.make_dir('init.d',os.path.join(mysql_path,'etc'))

# mysql_install_db
installdb_path = os.path.join(tar_mysql_base,tools.filter(mysql_package),'scripts')
installdb_file = install_path + '/mysql_install_db'
installdb_options = """
--user=%s \
--defaults-file=%s \
--datadir=%s \
--basedir=%s
""" % (user_name,os.path.join(mysql_path,'etc') + '/my.cnf', os.path.join(mysql_path,'data'),mysql_path)
os.chmod(installdb_file, stat.S_IEXEC)
os.system('%s %s' % (installdb_file, installdb_options))

# mysql startup scripts and conf
for file in ['my.cnf','mysql.server']:
	temp_content = tools.read_file(cwd + '/' + file)
	if 'cnf' in file:
		temp_file = os.path.join(mysql_path, 'etc') + '/' + file
	elif 'server' in file:
		temp_file = os.path.join(mysql_path, 'etc','init.d') + '/' + file
	tools.write_file(temp_file, temp_content)
	os.chmod(temp_file,stat.S_IRWXU + stat.S_IRWXG)


# config
os.system('chown -R %s.%s %s' % (user_name, user_name, mysql_path))
os.system('chmod -R 755 %s' % mysql_path)