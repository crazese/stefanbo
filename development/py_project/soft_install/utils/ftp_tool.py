from ftplib import FTP
import os

def download(host, user, pw, r_path, c_path='.'):
    "ftp download from r_path to c_path, c_path default set '.' if not set !"
    ftp = FTP(host)
    ftp.login(user, pw)
    os.chdir(c_path)
    ftp.cwd(r_path)
    file_list = ftp.nlst()

    for fname in file_list:
        f = open(fname, 'wb').write
        try:
            ftp.retrbinary('RETR %s'%fname, f, 1024)
            #open(fname,'wb').close
        except ftplib.error_perm:
            print "######### ftp download Failed!! #########"
            return False

    ftp.set_debuglevel(0)
    ftp.quit()
    print "Ftp download successfully!"

def upload(host, user, pw, r_path, c_path='.'):  
    "ftp upload from c_path to r_path, c_path default set '.' if not set !"
    ftp = FTP(host)
    ftp.login(user, pw)
    os.chidr(c_path)
    ftp.cwd(r_path)
    file_list = os.listdir(c_path)

    for fname in file_list:
        f = open(fname, 'rb')
        try:
            ftp.storbinary('STOR %s'%fname, f, 1024)
        except ftplib.error_perm:
            print "######### ftp upload Failed!! ###########"
            return False

    ftp.set_debuglevel(0)
    ftp.quit()
    print "Ftp upload successfully!"
 
  
