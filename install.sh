#/bin/bash
#install needed packet
#
#
#
#
#
#
apt-get install openssl
apt-get install libssl-dev
apt-get install snmpd
apt-get install sysstat
cp ./snmpd.conf /etc/snmp/snmpd.conf

service snmpd restart

groupadd nagios
useradd -g nagios nagios

tar -zxvf nagios-plugins-1.4.15.tar.gz
cd nagios-plugins-1.4.15

./configure && make && make install



tar -zxvf nrpe-2.12.tar.gz
cd nrpe-2.12
./configure
make all
make install-plugin
make install-daemon
make install-daemon-config
make install-xinetd


cp ./nrpe.cfg /usr/local/nagios/etc/nrpe.cfg

/usr/local/nagios/bin/nrpe -c /usr/local/nagios/etc/nrpe.cfg -d

echo "/usr/local/nagios/bin/nrpe -c /usr/local/nagios/etc/nrpe.cfg -d" >> /etc/rc.local

cp -r /opt/temp/libexec/* /usr/local/nagios/libexec/

perl -MCPAN -e 'install Nagios::Plugin'<<EOF















EOF


perl -MCPAN -e 'install Regexp::Common'<<EOF














EOF


perl -MCPAN -e 'install Net::SNMP'<<EOF














EOF


