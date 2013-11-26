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

sourcelist:
  file.managed:
    - name: /etc/apt/sources.list
    - source: salt://global/sources.list

apt-get_update:
  cmd:
    - name: apt-get update
    - wait
    - watch:
      - file: sourcelist


