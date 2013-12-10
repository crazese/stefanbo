#!/usr/bin/env python
# install nginx

import environment_init
import tools

tar_nginx_base = '/opt/lnmp/tar_package/nginx'
ftp_nginx_base = '/lnmp/nginx'
install_base = '/opt/lnmp/app'
soft_name = 'nginx'
user_name = 'www-data'
cwd = os.getcwd()

init_package = ['m4-1.4.9.tar.gz',
				'autoconf-2.13.tar.gz',
				'libiconv-1.14.tar.gz',
				'libmcrypt-2.5.8.tar.gz',
				'libxml2-2.7.8.tar.gz',
				'pcre-8.30.tar.gz',
				'openssl-1.0.1e.tar.gz',
				'zlib-1.2.8.tar.gz'
				]

nginx_package = 'nginx-1.0.9.tar.gz'
# prepare work
environment_init.folder_and_user_init(soft_name,user_name)
environment_init.ftp_download(tar_nginx_base,ftp_nginx_base)
environment_init.apt_update()
environment_init.install_init()

# install init_package
os.chdir(tar_nginx_base)
for soft in init_package:
	tools.extract_file(soft)
	folder_name = tools.filter(soft)
	tools.pak_configure(folder_name,folder_name,install_base,tar_nginx_base)
	tools.pak_make(folder_name,tar_nginx_base)
	if 'mcrypt' in soft:
		temp_base = os.path.join(tar_nginx_base,folder_name)
		tools.pak_configure(folder_name,'libltdl',install_base,temp_base,'--enable-ltdl-install')
		tools.pak_make(folder_name,temp_base)

# install nginx
tools.extract_file(nginx_package)

nginx_path = os.path.join(install_base,soft_name)
pcre_path = os.path.join(tar_nginx_base,tools.filter(init_package[5]))
openssl_path = os.path.join(tar_nginx_base,tools.filter(init_package[6]))
zlib_path = os.path.join(tar_nginx_base,tools.filter(init_package[7]))

# configure options
options = 
"""
--sbin-path=%s/sbin/ \
--pid-path=%s/nginx.pid \
--user=%s \
--group=%s \
--with-http_stub_status_module \
--with-http_ssl_module \
--with-http_gzip_static_module \
--with-pcre=%s \
--with-openssl=%s \
--with-zlib=%s
""" % (nginx_path,nginx_path,user_name,user_name,pcre_path,openssl_path,zlib_path)

# configure
tools.pak_configure(soft_name,soft_name,install_base,tar_nginx_base,options)

# change the Makefile to tend to adjust the init
makefile_fix = os.path.join(tar_nginx_base,nginx_package,'/objs/Makefile')
makefile = tools.read_file(makefile_fix)
result_1 = re.sub(r'./configure','./configure --prefix=/opt/lnmp/app/init',makefile)
result = re.sub(r'./configure --prefix=/opt/lnmp/app/init --disable-shared','./configure --prefix=/opt/lnmp/app/init',result_1)
tools.write_file(makefile_fix,result)

# make
tools.pak_make(tools.filter(nginx_package),tar_nginx_base)

# config
for dir in ['sites-enabled','sites-available']:
	tools.make_dir(dir,os.path.join(nginx_path,'conf'))
tools.make_dir(bin,nginx_path)


for file in ['nginx.conf','nginx.sh','herouser_virtualhost']:
	temp_content = tools.read_file(cwd + '/' + file)
	if 'conf' in file:
		temp_file = os.path.join(nginx_path, 'conf') + '/' + file
	elif 'sh' in file:
		temp_file = os.path.join(nginx_path, 'bin') + '/' + file
	elif 'virtualhost' in file:
		temp_file = os.path.join(nginx_path, 'conf', 'sites-available') + '/' + file
	tools.write_file(temp_file, temp_content)
	os.chmod(temp_file,stat.S_IRWXU + stat.S_IRWXG)

tools.make_dir('www/herouser',nginx_path)
os.system('chown -R %s.%s %s' % (user_name, user_name, nginx_path))
os.system('chmod -R 755 %s' % nginx_path)