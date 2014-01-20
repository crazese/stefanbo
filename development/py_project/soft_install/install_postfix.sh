#!/bin/sh

# install openssl
# reference http://www.linuxfromscratch.org/blfs/view/svn/postlfs/openssl.html
mkdir -p /usr/local/app/openssl_tar
cd !$


wget http://www.openssl.org/source/openssl-1.0.1f.tar.gz
wget http://www.linuxfromscratch.org/patches/blfs/svn/openssl-1.0.1f-fix_parallel_build-1.patch
wget http://www.linuxfromscratch.org/patches/blfs/svn/openssl-1.0.1f-fix_pod_syntax-1.patch

tar -zxvf openssl-1.0.1f.tar.gz
cd openssl-1.0.1f
patch -Np1 -i ../openssl-1.0.1f-fix_parallel_build-1.patch &&
patch -Np1 -i ../openssl-1.0.1f-fix_pod_syntax-1.patch &&



./config --prefix=/usr/local/app/openssl-1.0.1f        \
         --openssldir=/etc/ssl \
         --libdir=/lib          \
         shared                \
         zlib-dynamic &&
make

make MANDIR=/usr/share/man MANSUFFIX=ssl install &&
install -dv -m755 /usr/share/doc/openssl-1.0.1f  &&
cp -vfr doc/*     /usr/share/doc/openssl-1.0.1f
cd .. 

# install openssl 

# install cyrus-sasl 2.26
# reference http://www.linuxfromscratch.org/blfs/view/svn/postlfs/cyrus-sasl.html

wget ftp://ftp.cyrusimap.org/cyrus-sasl/cyrus-sasl-2.1.26.tar.gz
wget http://www.linuxfromscratch.org/patches/blfs/svn/cyrus-sasl-2.1.26-fixes-1.patch

tar -zxvf cyrus-sasl-2.1.26.tar.gz
cd cyrus-sasl-2.1.26

patch -Np1 -i ../cyrus-sasl-2.1.26-fixes-1.patch &&
autoreconf -fi &&
pushd saslauthd
autoreconf -fi &&
popd
./configure --prefix=/usr        \
            --sysconfdir=/etc    \
            --enable-auth-sasldb \
            --with-dbpath=/var/lib/sasl/sasldb2 \
            --with-saslauthd=/var/run/saslauthd \
            CFLAGS=-fPIC
make

make install &&
install -v -dm755 /usr/share/doc/cyrus-sasl-2.1.26 &&
install -v -m644  doc/{*.{html,txt,fig},ONEWS,TODO} \
    saslauthd/LDAP_SASLAUTHD /usr/share/doc/cyrus-sasl-2.1.26 &&
install -v -dm700 /var/lib/sasl

#make install-saslauthd

# install cyrus-sasl 2.1.22
# reference http://www.linuxfromscratch.org/blfs/view/6.3/postlfs/cyrus-sasl.html

wget http://ftp.andrew.cmu.edu/pub/cyrus-mail/cyrus-sasl-2.1.22.tar.gz

tar -zxvf cyrus-sasl-2.1.22.tar.gz
cd cyrus-sasl-2.1.22

./configure --prefix=/usr --sysconfdir=/etc \
            --with-dbpath=/var/lib/sasl/sasldb2 \
            --with-saslauthd=/var/run/saslauthd &&
make

make install &&
install -v -m755 -d /usr/share/doc/cyrus-sasl-2.1.22 &&
install -v -m644 doc/{*.{html,txt,fig},ONEWS,TODO} \
    saslauthd/LDAP_SASLAUTHD /usr/share/doc/cyrus-sasl-2.1.22 &&
install -v -m700 -d /var/lib/sasl /var/run/saslauthd

# make install-cyrus-sasl


# install postfix-2.10.2
# reference http://www.linuxfromscratch.org/blfs/view/svn/server/postfix.html

wget ftp://ftp.porcupine.org/mirrors/postfix-release/official/postfix-2.10.2.tar.gz
tar -zxvf postfix-2.10.2.tar.gz
cd postfix-2.10.2
groupadd -g 1032 postfix &&
groupadd -g 1033 postdrop &&
useradd -c "Postfix Daemon User" -d /var/spool/postfix -g postfix \
        -s /bin/false -u 1032 postfix &&
chown -v postfix:postfix /var/mail

make CCARGS="-DNO_NIS -DUSE_TLS -I/usr/include/openssl/            \
             -DUSE_SASL_AUTH -DUSE_CYRUS_SASL -I/usr/include/sasl  \
             -DHAS_MYSQL -I/usr/include/mysql"                     \
     AUXLIBS="-lssl -lcrypto -lsasl2 -L/usr/lib/mysql -lmysqlclient -lz -lm"        \
     makefiles &&
make



sh postfix-install -non-interactive \
   daemon_directory=/usr/lib/postfix \
   manpage_directory=/usr/share/man \
   html_directory=/usr/share/doc/postfix-2.10.2/html \
   readme_directory=/usr/share/doc/postfix-2.10.2/readme

cat >> /etc/aliases << "EOF"
# Begin /etc/aliases#

MAILER-DAEMON:    postmaster
postmaster:       root

root:             <LOGIN>
# End /etc/aliases
EOF


# install postfix-2.5.1
# reference http://www.linuxfromscratch.org/blfs/view/6.3/server/postfix.html

wget http://postfix.energybeam.com/source/official/postfix-2.5.1.tar.gz

tar -zxvf postfix-2.5.1.tar.gz
cd postfix-2.5.1

make makefiles \
CCARGS='-DUSE_TLS -I/usr/include/openssl/  \
		-DUSE_SASL_AUTH -DUSE_CYRUS_SASL -I/usr/include/sasl \
    	-DDEF_DAEMON_DIR=\"/usr/lib/postfix\" \
    	-DDEF_MANPAGE_DIR=\"/usr/share/man\" \
    	-DDEF_HTML_DIR=\"/usr/share/doc/postfix-2.5.1/html\" \
    	-DDEF_README_DIR=\"/usr/share/doc/postfix-2.5.1/README\" \
    	-DHAS_MYSQL -I/usr/include/mysql' \
    AUXLIBS='-L/usr/lib -lssl -lcrypto -lsasl2 -L/usr/lib/mysql -lmysqlclient -lz -lm' &&
make

sh postfix-install -non-interactive

cat >> /etc/aliases << "EOF"
# Begin /etc/aliases

MAILER-DAEMON:    postmaster
postmaster:       root

root:             LOGIN
# End /etc/aliases
EOF




/usr/sbin/alternatives --install /usr/sbin/sendmail mta /usr/sbin/sendmail.postfix 30 \
--slave /usr/bin/mailq mta-mailq /usr/bin/mailq.postfix \
--slave /usr/bin/newaliases mta-newaliases /usr/bin/newaliases.postfix \
--slave /etc/pam.d/smtp mta-pam /etc/pam.d/smtp.postfix \
--slave /usr/bin/rmail mta-rmail /usr/bin/rmail.postfix \
--slave /usr/lib/sendmail mta-sendmail /usr/lib/sendmail.postfix \
--initscript postfix

wget http://ftp.wl0.org/yum/postfix/2.4/rhel4/RPMS.postfix/postfix-2.4.8-1.rhel4.i386.rpm


Jan 17 11:33:57 mail postfix/error[5839]: 9E3071824A1: to=<root@localhost.sothink.com>, orig_to=<root@localhost>, relay=none, d
elay=2636, delays=2636/0/0/0.05, dsn=4.3.0, status=deferred (mail transport unavailable)
Jan 17 11:36:26 mail postfix/smtpd[6865]: warning: run-time library vs. compile-time header version mismatch: OpenSSL 0.9.7 may
 not be compatible with OpenSSL 1.0.1
Jan 17 11:36:26 mail postfix/master[5832]: warning: process /usr/libexec/postfix/smtpd pid 6865 killed by signal 11
Jan 17 11:36:26 mail postfix/master[5832]: warning: /usr/libexec/postfix/smtpd: bad command startup -- throttling


# install clamav 
# reference http://pkgs.repoforge.org/clamav/
wget http://pkgs.repoforge.org/clamav/clamav-0.96.2-1.el4.rf.i386.rpm
wget http://pkgs.repoforge.org/clamav/clamav-devel-0.96.2-1.el4.rf.i386.rpm
wget http://pkgs.repoforge.org/clamav/clamav-db-0.96.2-1.el4.rf.i386.rpm



mv  /etc/postfix/transport.rpmsave /etc/postfix/transport
mv /etc/postfix/master.cf.rpmsave /etc/postfix/master.cf
mv /etc/postfix/main.cf.rpmsave /etc/postfix/main.cf
mv /etc/postfix/header_checks.rpmsave /etc/postfix/header_checks
mv /etc/postfix/aliases.rpmsave /etc/postfix/aliases
mv /etc/postfix/access.rpmsave /etc/postfix/access


wget ftp://ftp.redhat.com/pub/redhat/linux/enterprise/5Server/en/os/SRPMS/postfix-2.3.3-2.1.el5_2.src.rpm 
rpm -ivh postfix-2.3.3-2.1.el5_2.src.rpm 
cd /usr/src/redhat/SOURCES/
wget http://vda.sourceforge.net/VDA/postfix-2.3.3-vda.patch.gz
gunzip postfix-2.3.3-vda.patch.gz  
vim /usr/src/redhat/SPECS postfix.spec

rpmbuild -ba postfix.spec

cd /usr/src/redhat/RPMS/i386/

perl -MCPAN -e 'install Date::Calc'



# reinstall openssl 0.9.7
wget http://www.openssl.org/source/openssl-0.9.7a.tar.gz
tar -zxvf openssl-0.9.7a
cd openssl-0.9.7a
./config --prefix=/usr/local/app/openssl-0.9.7
make && make insatll
cd ..

#rm -rf postfix-2.10.2#

#tar -zxvf postfix-2.10.2.tar.gz
#cd postfix-2.10.2#

#make CCARGS="-DNO_NIS -DUSE_TLS -I/usr/local/app/openssl-0.9.7/include/openssl   \
#             -DUSE_SASL_AUTH -DUSE_CYRUS_SASL -I/usr/include/sasl  \
#             -DHAS_MYSQL -I/usr/include/mysql"                     \
#     AUXLIBS="-lssl -lcrypto -lsasl2 -L/usr/lib/mysql -lmysqlclient -lz -lm"        \
#     makefiles &&
#make#
#
#

#sh postfix-install -non-interactive \
#   daemon_directory=/usr/lib/postfix \
#   manpage_directory=/usr/share/man \
#   html_directory=/usr/share/doc/postfix-2.10.2/html \
#   readme_directory=/usr/share/doc/postfix-2.10.2/readme

# install postfix 2.5.2 from rpm
wget http://ftp.wl0.org/official/2.5/RPMS-rhel4-i386/postfix-2.5.2-1.rhel4.i386.rpm

# install zlib-1.2.3
wget ftp://ftp.pbone.net/mirror/ftp5.gwdg.de/pub/opensuse/repositories/home:/Obesotoma/RedHat_RHEL-4/i386/zlib-1.2.3-16.1.i386.rpm
wget ftp://ftp.pbone.net/mirror/ftp5.gwdg.de/pub/opensuse/repositories/home:/Obesotoma/RedHat_RHEL-4/i386/zlib-devel-1.2.3-16.1.i386.rpm


