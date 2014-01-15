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



./config --prefix=/usr         \
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