include:
  - prod._init.php_init

/etc/php5/fpm/php.ini:
  file.managed:
    - source: salt://prod/herouser/template/php.ini
    - require:
      - pkg: php5-fpm

php5-fpm_restart:
  cmd.wait:
    - name: /etc/init.d/php5-fpm restart    
    - watch:
      - file: /etc/php5/fpm/php.ini
