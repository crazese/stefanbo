#!/bin/sh

# install 
wget http://down1.chinaunix.net/distfiles/maildrop-2.0.2.tar.bz2

tar -jzxv maildrop-2.0.2.tar.bz2
cd maildrop-2.0.2
./configure \
--enable-sendmail=/usr/sbin/sendmail \
--enable-trusted-users='root postfix' \
--enable-syslog=1 \
--enable-maildirquota \
--enable-maildrop-uid=1032 \
--enable-maildrop-gid=1032 \
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
--with-mailuser=postfix \
--with-mailgroup=postfix 

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
mkdir /var/www/mailmanage -p
tar -zxvf extmail-1.2.tar.gz
mv extmail-1.2 /var/www/mailmanage/extmail 
cp /var/www/mailmanage/extmail/webmail.cf.default /var/www/mailmanage/extmail/webmail.cf

sed -i 's/SYS_USER_LANG = en_US/SYS_USER_LANG = zh_CN/g' /var/www/mailmanage/extmail/webmail.cf
sed -i 's#SYS_MAILDIR_BASE = /home/domains#SYS_MAILDIR_BASE = /var/mailbox#/g' /var/www/mailmanage/extmail/webmail.cf
sed -i 's#SYS_MYSQL_USER = db_user#SYS_MYSQL_USER = extmail#g' /var/www/mailmanage/extmail/webmail.cf
sed -i 's#SYS_MYSQL_PASS = db_pass#SYS_MYSQL_PASS = extmail#g' /var/www/mailmanage/extmail/webmail.cf




