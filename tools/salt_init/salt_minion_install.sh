#!/bin/bash
# install salt minion on ubuntu server 10.04
cp /etc/apt/sources.list /etc/apt/sources.list.bak 
cat > /etc/apt/sources.list <<EOF
deb http://mirrors.163.com/ubuntu/ precise main universe restricted multiverse
deb http://mirrors.163.com/ubuntu/ precise-security universe main multiverse restricted
deb http://mirrors.163.com/ubuntu/ precise-updates universe main multiverse restricted
deb http://mirrors.163.com/ubuntu/ precise-proposed universe main multiverse restricted
deb http://mirrors.163.com/ubuntu/ precise-backports universe main multiverse restricted
deb http://debian.saltstack.com/debian wheezy-saltstack main
EOF

wget -q -O- "http://debian.saltstack.com/debian-salt-team-joehealy.gpg.key" | apt-key add -
echo "b702969447140d5553e31e9701be13ca11cc0a7ed5fe2b30acb8491567560ee62f834772b5095d735dfcecb2384a5c1a20045f52861c417f50b68dd5ff4660e6  debian-salt-team-joehealy.gpg.key" | sha512sum -c

apt-get update

apt-get install -o APT::Immediate-Configure=false -f apt python-minimal<<EOF

EOF

apt-get install salt-common salt-minion -y

cat >> /etc/salt/minion <<EOF
master: 192.168.1.203
id: p-herouser205
EOF