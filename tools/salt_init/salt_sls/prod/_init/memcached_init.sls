memcached:
  pkg.installed

memcached_start:
  cmd:
    - name: /usr/bin/memcached -m 64 -p 11211 -u nobody -l 127.0.0.1
    - wait
    - require:
      - pkg: memcached
