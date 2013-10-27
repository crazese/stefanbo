# You may add here your
# server {
#       ...
# }
# statements for each of your virtual hosts to this file

##
# You should look at the following URL's in order to grasp a solid understanding
# of Nginx configuration files in order to fully unleash the power of Nginx.
# http://wiki.nginx.org/Pitfalls
# http://wiki.nginx.org/QuickStart
# http://wiki.nginx.org/Configuration
#
# Generally, you will want to move this file somewhere, and start with a clean
# file but keep this around for reference. Or just disable in sites-enabled.
#
# Please see /usr/share/doc/nginx-doc/examples/ for more detailed examples.
##

server {
        listen 80 default_server;
        listen [::]:80 default_server ipv6only=on;

        root /var/www/authserver;
        index index.php index.html index.htm;

        server_name localhost;

        location / {
                try_files $uri $uri/ /index.html;
        }

        location /doc/ {
                alias /usr/share/doc/;
                autoindex on;
                allow 127.0.0.1;
                allow ::1;
                deny all;
        }


        location ~ \.php$ {
               fastcgi_split_path_info ^(.+\.php)(/.+)$;
               # NOTE: You should have "cgi.fix_pathinfo = 0;" in php.ini
               fastcgi_param  SCRIPT_FILENAME  /var/www/authserver$fastcgi_script_name;
               # With php5-cgi alone:
               fastcgi_pass 127.0.0.1:9000;
               # With php5-fpm:
               fastcgi_pass unix:/var/run/php5-fpm.sock;
               fastcgi_index index.php;
               include fastcgi_params;
        }

        # deny access to .htaccess files, if Apache's document root
        # concurs with nginx's one
        #
        #location ~ /\.ht {
        #       deny all;
        #}
}


cgi.fix_pathinfo = 0;