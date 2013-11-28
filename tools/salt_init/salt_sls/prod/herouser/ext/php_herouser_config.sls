php_herouser_config:
  file.managed:
    - name: /var/www/herouser/config.php
    - source: salt://prod/herouser/template/config.php