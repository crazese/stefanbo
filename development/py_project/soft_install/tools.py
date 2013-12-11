import os
import tarfile
import zipfile


def extract_file(path, to_directory='.'):
    if path.endswith('.zip'):
        opener, mode = zipfile.ZipFile, 'r'
    elif path.endswith('.tar.gz') or path.endswith('.tgz'):
        opener, mode = tarfile.open, 'r:gz'
    elif path.endswith('.tar.bz2') or path.endswith('.tbz'):
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
        os.system(config)
    finally:
        os.chdir(cwd)

def pak_make(f_name, tar_base):
    tar_name = os.path.join(tar_base, f_name)
    cwd = os.getcwd()
    os.chdir(tar_name)

    try:
        os.system('make && make install')
    finally:
        os.chdir(cwd)

def filter(stri):
    if '.tar.gz' in stri:
        stri = stri.strip('.tar.gz')
        return stri
    elif '.tar.bz2' in stri:
        stri = stri.strip('.tar.bz2')
        return stri
    elif '.zip' in stri:
        stri = stri.strip('.zip')
        return stri
    else:
        return None

def write_file(file_name,stri):
    temp_file = open(file_name,'w')
    temp_file.write(stri)

def read_file(file_name):
    temp_file = open(file_name,'r')
    return temp_file.read()

def make_dir(f_name, path):
    folder = os.path.join(path,f_name)
    if not(os.path.exists(folder)):
        os.makedirs(folder)
    else: 
        print 'The folder %s have aready create' % folder