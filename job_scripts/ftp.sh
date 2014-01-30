#/bin/bash
server="hut.jofgame.com"
username="zhubo"
passwd="Jof1Game8yzl"

mkdir libexec

ftp -n $server <<EOF
prompt off
user $username $passwd
get nagios-plugins-1.4.15.tar.gz
get nrpe-2.12.tar.gz
get snmpd.conf
get nrpe.cfg
cd libexec
lcd libexec
mget * 
bye
EOF
