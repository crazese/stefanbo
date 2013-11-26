base:
  '*':
    - global
  
  'p-authserver*':
    - prod.authserver.ext.nginx_authserver
    - prod.authserver.ext.mysql_authserver
    - prod.authserver.ext.php_authserver
    - prod.authserver.ext.memcached_authserver
  
  'p-herouser*':
    - prod.herouser.ext.nginx_herouser
    - prod.herouser.ext.mysql_herouser
    - prod.herouser.ext.php_herouser
    - prod.herouser.ext.memcached_herouser
