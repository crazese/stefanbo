sourcelist:
  file.managed:
    - name: /etc/apt/sources.list
    - source: salt://global/sources.list

apt-get_update:
  cmd.wait:
    - name: apt-get update
    - watch:
      - file: sourcelist

init_packages_installed:
  pkg.installed:
    - names:
      - gcc
      - g++
      - make
      - curl
      - vim
      - zip
      - unzip
      - wget
      - openssl
      - libssl0.9.8
      - libssl-dev
      - libpcre3
      - libpcre3-dev
      - libcurl3
      - libevent-dev

ifupdown:
  pkg.installed

apt-get_upgrade:
  cmd.run:
    - name: apt-get upgrade -y
    - require: 
      - cmd: apt-get_update

