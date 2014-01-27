from ftplib import FTP
import sys, subprocess, shlex

def pak_configure(name, f_name, install_base, tar_base, *args):
    if name in ['mysql','nginx','php','memcache']:
        install_name = os.path.join(install_base, name)
    else:
        install_name = os.path.join(install_base,'init')
    tar_name = os.path.join(tar_base, f_name)

    options = ' '.join(args)
    if name in 'mysql':
        config = '/opt/lnmp/app/init/bin/cmake -DCMAKE_INSTALL_PREFIX=%s %s' % (install_name,options)
    else:
        config = './configure --prefix=%s %s' % (install_name,options)

    cwd = os.getcwd()
    os.chdir(tar_name)
    
    try:
        print "Configuring the %s" % name
        os.system(config)
    finally:
        os.chdir(cwd)

def pak_make(f_name, tar_base):
    tar_name = os.path.join(tar_base, f_name)
    cwd = os.getcwd()
    os.chdir(tar_name)

    try:
        print "Begin make and make install %s" % f_name
        os.system('make && make install')
    finally:
        os.chdir(cwd)