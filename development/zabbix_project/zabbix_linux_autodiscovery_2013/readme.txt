G. Husson - Thalos - 201307

Zabbix scripts, templates, regexps for autodiscovering disks, services and process in Linux.
Template Linux disk autodiscovery : discovers physical disks dans creates basic items (speed, IOs, IO spent)
Template Linux processes autodiscovery : discovers processes (based on regexp on the process name)
Template Linux services autodiscovery : discovers TCP and UDP listenning services (based on process name or on port number)

In zabbix frontend :
====================
1) add the regexps : administration -> general -> regular expressions
	==> reproduce the content of file "regexps.txt"
2) import templates from "zbx_export_templates.xml" 

For each monitored linux host :
===============================
1) install custom scripts (I put them there : /opt/zabbix_agent/bin/ - example : /opt/zabbix_agent/bin/discovery_disks.perl)
   (they are in archive "linux_autodiscovery_scripts.tgz")
2) set rights on custom scripts :
	chown root:zabbix /opt/zabbix_agent/bin/discovery_*
	chmod 750 /opt/zabbix_agent/bin/discovery_*
3) add sudo for netstat from zabbix agent :
	visudo
	==> add : 
		zabbix  ALL=NOPASSWD: /opt/zabbix_agent/bin/discovery_disks.perl
		zabbix  ALL=NOPASSWD: /opt/zabbix_agent/bin/discovery_processes.perl
		zabbix  ALL=NOPASSWD: /opt/zabbix_agent/bin/discovery_tcp_services.perl
		zabbix  ALL=NOPASSWD: /opt/zabbix_agent/bin/discovery_udp_services.perl
		zabbix  ALL=NOPASSWD: /opt/zabbix_agent/bin/discovery_thalos_nfs_mountpoint.perl
4) add the content of file "agent_custom_keys.txt" to your /etc/zabbix/zabbix_agentd.conf
	(if path is not the same as in 1), adapt it)
5) restart zabbix agent
6) test customs keys on the host :
	zabbix_get -s 127.0.0.1 -k custom.disks.discovery_perl
	zabbix_get -s 127.0.0.1 -k custom.proc.discovery_perl
	zabbix_get -s 127.0.0.1 -k custom.services.tcp.discovery_perl
	zabbix_get -s 127.0.0.1 -k custom.services.udp.discovery_perl
7) link the templates to the host :
	Template Linux disk autodiscovery
	Template Linux processes autodiscovery
	Template Linux services autodiscovery
8) wait 30 mn for autodiscovery, then look " Latest data " of host in zabbix frontend

Note on Linux services autodiscovery :
======================================
In early releases, templates does autodiscovery based on service process name.
I remarked that some service process names were not found by netstat.
Furthermore, some process spawns random listenners (ex : NFS, syslogd).
Finally, I set discovery on port numbers.
For this :
1) I changed TCP and UDP discovery rules filter. Example with TCP : 
	Macro {#PORT}
	Regexp @Linux TCP services for fordiscovery by port
2) I created regexp on ports, example with TCP :
	^(21|22|25|26|53|80)$
This discovery by port is done in the current release.
But in zabbix 2.0.2, regexp field length is limited, and I already have problems with that.
If this problem is fixed or if you find a trick, please mail me.	





