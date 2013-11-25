{% for pakge in ['gcc','g++','make','curl','vim','vim','zip','unzip','wget','openssl','libssl0.9.8','libssl-dev','libpcre3','libpcre3-dev'] %}
{{ pakge }}
  pkg.installed
{% endfor %}

/etc/apt/sources.list:
	file.managed
		- source: salt://sources.list

apt-get_update:
	cmd:
	  - name: apt-get update
	  - wait
	  - watch:
	  	- file: /etc/apt/sources.list


