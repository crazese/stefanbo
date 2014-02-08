#!/bin/sh
#export LD_LIBRARY_PATH='/data/lnmp/monitor_tools/app/mysql/lib:/data/lnmp/cur/lib:/usr/local/app/locale/libxml2/lib'

zabbix_path='/data/lnmp/monitor_tools/app/zabbix_agent'

mkdir $zabbix_path/libexec -p
cat >> $zabbix_path/etc/zabbix_agentd.conf <<EOF
Include=$zabbix_path/etc/zabbix_agentd.conf.d/
EOF


server="59.175.238.8"
username="zabbix"
passwd="19880103"


ftp -n $server <<EOF
prompt off
user $username $passwd
lcd /data/lnmp/monitor_tools/app/zabbix_agent/libexec
get discover_disk.pl
lcd /data/lnmp/monitor_tools/app/zabbix_agent/etc/zabbix_agentd.conf.d
get iostat.conf
bye
EOF

pkill zabbix_agentd
$zabbix_path/sbin/zabbix_agentd 

chmod +x /data/lnmp/monitor_tools/app/zabbix_agent/libexec/discover_disk.pl
