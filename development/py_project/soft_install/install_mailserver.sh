#!/bin/sh

# install 
wget http://down1.chinaunix.net/distfiles/maildrop-2.0.2.tar.bz2

tar -jxvf maildrop-2.0.2.tar.bz2
cd maildrop-2.0.2
./configure \
--enable-sendmail=/usr/sbin/sendmail.postfix \
--enable-trusted-users='root vmail' \
--enable-syslog=1 \
--enable-maildirquota \
--enable-maildrop-uid=1033 \
--enable-maildrop-gid=1033 \
--with-trashquota \
--with-dirsync

make && make -j2 install

wget http://down1.chinaunix.net/distfiles/courier-authlib-0.62.4.tar.bz2

tar -jxvf courier-authlib-0.62.4.tar.bz2
cd courier-authlib-0.62.4

./configure \
--prefix=/usr/local/courier-authlib \
--without-stdheaderdir \
--sysconfdir=/etc \
--without-authuserdb \
--without-authpam \
--without-authldap \
--without-authpwd \
--without-authshadow \
--without-authvchkpw \
--without-authpgsql \
--without-authcustom \
--with-authmysql \
--with-mysql-libs=/usr/lib/mysql \
--with-mysql-includes=/usr/inculde/mysql \
--with-redhat \
--with-mailuser=vmail \
--with-mailgroup=vmail 

make && make install

wget http://down1.chinaunix.net/distfiles/courier-imap-4.1.2.tar.bz2
tar jxvf courier-imap-4.1.2.tar.bz2

cd courier-imap-4.1.2
./configure \
--prefix=/usr/local/courier-imap \
--enable-unicode \
--disable-root-check \
--with-trashquota \
--without-ipv6 \
--with-redhat \
CPPFLAGS='-I/usr/local/courier-authlib/include -I/usr/include/openssl' \
LDFLAGS='-L/usr/local/courier-authlib/lib/courier-authlib' \
COURIERAUTHCONFIG='/usr/local/courier-authlib/bin/courierauthconfig'

make && make install

cp /usr/local/courier-imap/etc/imapd.dist /usr/local/courier-imap/etc/imapd
cp /usr/local/courier-imap/etc/imapd-ssl.dist /usr/local/courier-imap/etc/imapd-ssl

sed -i 's/IMAPDSTART=NO/IMAPDSTART=YES/g' /usr/local/courier-imap/etc/imapd
sed -i 's/POP3DSTART=NO/POP3DSTART=YES/g' /usr/local/courier-imap/etc/pop3d

mkdir -p /var/mailbox
chown -R postfix.postfix /var/mailbox

cp courier-imap.sysvinit /etc/rc.d/init.d/courier-imapd
chmod 755 /etc/rc.d/init.d/courier-imapd 
chkconfig --level 2345 courier-imapd on 

cat > /usr/lib/sasl2/smtpd.conf <<EOF
pwcheck_method: authdaemond
mech_list: PLAIN LOGIN
log_level: 3
authdaemond_path:/usr/local/courier-authlib/var/spool/authdaemon/socket
EOF


# install extmail

wget http://mirror3.extmail.net/dist/extmail-1.2.tar.gz
mkdir /var/www/extsuite -p
tar -zxvf extmail-1.2.tar.gz
mv extmail-1.2 /var/www/extsuite/extmail 
cp /var/www/extsuite/extmail/webmail.cf.default /var/www/extsuite/extmail/webmail.cf

sed -i 's/SYS_USER_LANG = en_US/SYS_USER_LANG = zh_CN/g' /var/www/extsuite/extmail/webmail.cf
sed -i 's#SYS_MAILDIR_BASE = /home/domains#SYS_MAILDIR_BASE = /var/mailbox#/g' /var/www/extsuite/extmail/webmail.cf
sed -i 's#SYS_MYSQL_USER = db_user#SYS_MYSQL_USER = extmail#g' /var/www/extsuite/extmail/webmail.cf
sed -i 's#SYS_MYSQL_PASS = db_pass#SYS_MYSQL_PASS = extmail#g' /var/www/extsuite/extmail/webmail.cf




# install perl DBI
perl -MCPAN -e 'install DBI'
# install perl DBD::Mysql
perl -MCPAN -e 'install DBD::mysql'
# install perl Unix::Syslog
perl -MCPAN -e 'install Unix::Syslog'

# install perl DBD::Mysql from source
wget http://www.cpan.org/authors/id/C/CA/CAPTTOFU/DBD-mysql-3.0008.tar.gz

tar -zxvf DBD-mysql-3.0008.tar.gz
cd DBD-mysql-3.0008
perl Makefile.PL
make && make -j2 install

# install extman
wget http://mirror3.extmail.net/dist/extman-0.2.5.tar.gz
tar -zxvf extman-0.2.5.tar.gz
mv extman-0.2.5 /var/www/extsuite/extman

cd /var/www/extsuite/extman/docs/
cp mysql_virtual_* /etc/postfix/


wget http://pkgs.repoforge.org/perl-GD/perl-GD-2.35-1.el4.rf.i386.rpm
rpm -ivh perl-GD-2.35-1.el4.rf.i386.rpm

chown -R root.root /var/www/extsuite/
chmod -R 755 /var/www/extsuite
chown -R postfix.postfix /var/www/extsuite/extmail/cgi
chown -R postfix.postfix /var/www/extsuite/extman/cgi
chown -R postfix.postfix /var/www/extsuite/extmail/tmp
chown -R postfix.postfix /var/www/extsuite/extman/tmp

perl -MCPAN -e 'install Time::HiRes'
perl -MCPAN -e 'install File::Tail'

# install rrdtool
wget http://www.infodrom.org/projects/cgilib/download/cgilib-0.5.tar.gz
tar -zxvf cgilib-0.5.tar.gz
cd cgilib-0.5
sed -i '/Library/s#"#\"#g' cgitest.c
make && make install
cp libcgi.a /usr/lib/
cp cgi.h /usr/include/


wget http://oss.oetiker.ch/rrdtool/pub/rrdtool-1.2.3.tar.gz
tar -zxvf rrdtool-1.2.3.tar.gz
cd rrdtool-1.2.3
./configure --prefix=/usr/local/rrdtool --disable-tcl
make && make install

cp -r /var/www/extsuite/extman/addon/mailgraph_ext   /usr/local/
/usr/local/mailgraph_ext/mailgraph-init   start
ln -sv /usr/local/rrdtool/lib/perl/5.8.5/i386-linux-thread-multi/RRDs.pm /usr/lib/perl5/site_perl/5.8.5/i386-linux-thread-multi/
ln -sv /usr/local/rrdtool/lib/perl/5.8.5/i386-linux-thread-multi/auto/RRDs/RRDs.so /usr/lib/perl5/site_perl/5.8.5/i386-linux-thread-multi/

/usr/local/mailgraph_ext/mailgraph-init start
/usr/local/mailgraph_ext/qmonitor-init start

echo "/usr/local/mailgraph_ext/mailgraph-init start" >> /etc/rc.local

# install phpmyadmin
#wget http://sourceforge.net/projects/phpmyadmin/files/phpMyAdmin/4.1.4/phpMyAdmin-4.1.4-all-languages.tar.gz
#tar -zxvf phpMyAdmin-4.1.4-all-languages.tar.gz
#mv phpMyAdmin-4.1.4-all-languages /var/www/extsuite/phpmyadmin
http://sourceforge.net/projects/phpmyadmin/files/phpMyAdmin/2.11.11.3/phpMyAdmin-2.11.11.3-all-languages.tar.gz
tar -zxvf phpMyAdmin-2.11.11.3-all-languages.tar.gz
mv phpMyAdmin-2.11.11.3-all-languages /var/www/extsuite/phpmyadmin 
cp /var/www/extsuite/phpmyadmin/config.sample.inc.php /var/www/extsuite/phpmyadmin/config.inc.php



testsaslauthd -s smtp -u zhongsi@sothink.com -p 1q2w3e?123
<
mail from:root@sothink.com

rcpt to:zhongsi@sothink.com

data

subject: Mail test
new test
.
>


#smtp 测试
[root@mail postfix_tar]# perl -e 'use MIME::Base64;print encode_base64("zhongsi\@sothink.com")' 
emhvbmdzaUBzb3RoaW5rLmNvbQ==
[root@mail postfix_tar]# perl -e 'use MIME::Base64;print encode_base64("1q2w3e?")'          
MXEydzNlPw==

echo "test" | maildrop -V 10 -d zhongsi@sothink.com
