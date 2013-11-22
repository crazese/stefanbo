#!/bin/bash


if [ ! `grep -l "/lib"    '/etc/ld.so.conf'` ]; then
	echo "/lib" >> /etc/ld.so.conf
fi

if [ ! `grep -l '/usr/lib'    '/etc/ld.so.conf'` ]; then
	echo "/usr/lib" >> /etc/ld.so.conf
fi

if [ ! `grep -l '/opt/lnmp/lib'    '/etc/ld.so.conf'` ]; then
	echo "/opt/lnmp/lib" >> /etc/ld.so.conf
fi

ldconfig