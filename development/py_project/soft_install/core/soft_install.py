# -*- coding: utf-8 -*-
#!/usr/bin/env python

# append sys.path to import module from other folder!
import sys
sys.path.append('..')

# import utils from utils folder
from utils.con_tool import *
from utils.ftp_tool import *
from utils.os_tool import *

class install_soft(object):

        def __init__(self, name):
                self.host = 'hut.jofgame.com'
                self.user = 'zhubo'
                self.pw = 'Jof1Game8yzl'
                self.name = name

                if name == 'nginx':
                        self.conf = {
                                'tar_path'                 :         '/opt/lnmp/tar_package/nginx',
                                'ftp_path'                 :         '/lnmp/nginx',
                                'init_path'                :         '/opt/lnmp/app/init',
                                'soft_path'                :         '/opt/lnmp/app/nginx',
                                'soft_name'                :         'nginx',
                                'user_name'                :         'www-data'
                                }
                        self.init_package = ['m4-1.4.9.tar.gz',
                                                                 'autoconf-2.13.tar.gz',
                                                                 'libiconv-1.14.tar.gz',
                                                                 'libmcrypt-2.5.8.tar.gz',
                                                                 'libxml2-2.7.8.tar.gz',
                                                                 'pcre-8.30.tar.gz',
                                                                 'openssl-1.0.1e.tar.gz',
                                                                 'zlib-1.2.8.tar.gz'
                                                                 ]
                        self.soft_package = 'nginx-1.0.9.tar.gz'
                        pcre_path = os.path.join(self.conf['tar_path'],
                                                                         filter_tar(self.init_package[5]))
                        openssl_path = os.path.join(self.conf['tar_path'],
                                                                         filter_tar(self.init_package[6]))
                        zlib_path = os.path.join(self.conf['tar_path'],
                                                                         filter_tar(self.init_package[7]))
                        self.soft_options = '''
                        --sbin-path=%s/sbin/ \
                        --pid-path=%s/nginx.pid \
                        --user=%s \
                        --group=%s \
                        --with-http_stub_status_module \
                        --with-http_ssl_module \
                        --whti-http_gzip_static_module \
                        --with-pcre=%s \
                        --with-openssl=%s \
                        --with-zlib=%s''' % (self.conf['soft_path'],
                                                                 self.conf['soft_path'],
                                                                 self.conf['user_name'],
                                                                 self.conf['user_name'],
                                                                 pcre_path,
                                                                 openssl_path,
                                                                 zlib_path)

                elif name == 'mysql':
                        self.conf = {
                                'tar_path'                :         '/opt/lnmp/tar_package/mysql',
                                'ftp_path'                :         '/lnmp/mysql',
                                'init_path'                :         '/opt/lnmp/app/init',
                                'soft_path'                :         '/opt/lnmp/app/mysql',
                                'soft_name'                :         'mysql',
                                'user_name'                :        'mysql'
                        }
                        self.init_package = ['ncurses-5.9.tar.gz',
                                                                 'cmake-2.8.12.1.tar.gz']
                        self.soft_package = 'mysql-5.5.34.tar.gz'
                        self.soft_options = '''
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
                        ''' % (os.path.join(self.conf['soft_path'], 'data'),
                                   os.path.join(self.conf['soft_path'], 'etc'))

                elif name == 'php':
                        self.conf

                else:
                        print "You will need set name = 'mysql' or 'nginx' or 'php'"
                        sys.exit(1)


        def get_tar(self):
                make_dir(self.conf['tar_path'])
                
                download(self.host,
                                 self.user,
                                 self.pw,
                                 self.conf['ftp_path'],
                                 self.conf['tar_path'])


        def init_pak_install(self):
                ''' extract the 'init_package' , and configure it , make install it '''
                print "I will change to directory %s" % self.conf['tar_path']
                path = self.conf['tar_path']
                os.chdir(path)

                # install soft with file_config(), file_make()
                for soft in self.init_package:
                        extract_file(soft)
                        folder_name = filter_tar(soft)
                        file_config(os.path.join(path, folder_name),
                                                self.conf['init_path'])
                        file_make(os.path.join(path, folder_name))
                        if 'mcrypt' in folder_name:
                                temp_path = os.path.join(path, folder_name, 'libltdl')
                                file_config(temp_path,
                                                        self.conf['init_path'])
                                file_make(temp_path)


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

        
        def soft_init(self):
                ''' Initialization the soft install , include  conf and others scripts need to run. '''