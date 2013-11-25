#!/bin/bash
# init the apt source list
# install salt master
sudo add-apt-repository ppa:saltstack/salt

sudo apt-get install python-software-properties

sudo apt-get update

sudo apt-get install salt-master

mkdir /srv/salt/base -p 
mkdir /srv/salt/dev -p 
mkdir /srv/salt/prod -p 

cat > /etc/salt/master <<EOF
interface: 192.168.1.203

file_roots:
  base:
    - /srv/salt/base
  dev:
    - /srv/salt/dev
  prod:
    - /srv/salt/prod
EOF

cat > /srv/salt/base/top.sls <<EOF
base:
  '*':
    - global
EOF

/etc/init.d/salt-master restart

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
master 192.168.1.203
EOF




root@ubuntu:/srv/salt# tree
.
├── authserver
├── dev
├── global.sls
├── herouser
│   ├── init.sls
│   ├── mysql
│   ├── nginx
│   │   ├── default
│   │   └── herouser
│   └── php
├── prod
└── top.sls



# sls template init the hero lamp environment 
nginx:
  pkg:
    - installed
  service:
    - running
    - watch:
      - pkg: nginx
      - file: /etc/nginx/nginx.conf
      - user: root
  user.present:
    - uid: 0
    - gid: 0
    - home: /var/www/herouser
    - shell: /bin/nologin
    - require:
      - group: root 
  group.present:
    - gid: 0
    - require:
      - pkg: nginx

/etc/nginx/nginx.conf:
  file.managed:
    - source: salt://nginx/httpd.conf
    - user: root
    - group: root
    - mode: 644