{% for pak in ['php5-cli', 'php5-common' ,'php5-mysql','php5-suhosin','php5-gd', 'php5-mcrypt', 'php5-fpm', 'php5-cgi', 'php-pear', 'php5-curl', 'php5-openssl', 'php5-dev','libpcre3-dev','libevent-dev','libcloog-ppl-dev'] %}
{{ pak }}:
	pkg.installed
{% endfor %}

