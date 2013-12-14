import os
import tarfile
import zipfile
from os_tool import os_cmd


def extract_file(soft, to_directory='.'):
    ''' 
    It can extract file in format zip, tar.gz, tgz, tar.bz2, tbz . 
    '''
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
        file = opener(soft, mode)
        try: 
            file.extractall()
            print "I complete the extract !"
        finally: file.close()
    finally:
        os.chdir(cwd)


def file_config(path, prefix, *options):
    '''
    It configure the soft in general way :
        ./configure --prefix='prefix' 'options'
    or  ./config --prefix='prefix' 'options'
    or  -cmake -DCMAKE_INSTALL_PREFIX='prefix' 'options' on the 'path'
    The options can be strings like '--some-option=xxx --some-option=xxx'
    or  can be the args like '--some-option=xxx','-some-options=xxx'
    '''
    config_cmd = ['./configure', './config']
    # del the ' ' in the options
    options = ' '.join(options)
    if os.path.exists(os.path.join(path, config_cmd[0].strip('./'))): 
        con_list = [config_cmd , '--prefix=', prefix, options]
        con = ' '.join(con_list)
        os.chdir(path)
        os_cmd(con)
    elif os.path.exists(os.path.join(path, config_cmd[1].strip('./'))):
        con_list = [config_cmd, '--prefix=', prefix, options]
        con = ' '.join(con_list)
        os.chdir(path)
        os_cmd(con)
    else:
        cmake_cmd = '/opt/lnmp/app/init/bin/cmake'
        if os.path.exists(cmake_cmd):
            con_list = [cmake_cmd, '-DCMAKE_INSTALL_PREFIX=', prefix, options]
            con = ' '.join(con_list)
            os.chdir(path)
            os_cmd(con)
        else:
            print "You need to install cmake !"

def file_make(path):
    os.chdir(path)
    try:
        print "Begin make and make install on the path %s" % path
        os_cmd('make && make install')
    except OSError as e:
        print "Something wrong during make the soft"
        print >>sys.stderr, "Execution Failed!", e