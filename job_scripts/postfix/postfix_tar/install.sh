#!/bin/sh
# install cyrus-sasl 2.26
# reference http://www.linuxfromscratch.org/blfs/view/svn/postlfs/cyrus-sasl.html

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
