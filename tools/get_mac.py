#!/usr/bin/env python
import urllib2, base64
import re
from BeautifulSoup import BeautifulSoup
#get the html contents from urllib2
#user and pw
login_user = 'admin'
login_pw = '59715112'
auth = 'Basic ' + base64.b64encode(login_user+':'+login_pw)

#sys_log heads and requset
sys_log_html= 'http://@192.168.1.1/sys_log.htm'
log_heads = {'Referer': sys_log_html ,
			 'Authorization' : auth}
log_request = urllib2.Request(sys_log_html, None, log_heads)

log_response = urllib2.urlopen(log_request)


#ipmac_binding heads and requset
ipmac_binding_html 	= 'http://192.168.1.1/Dhcp_table.htm'
ipmac_heads = { 'Referer': ipmac_binding_html,
				'Authorization' : auth }
ipmac_request = urllib2.Request(ipmac_binding_html, None, ipmac_heads)

ipmac_response = urllib2.urlopen(ipmac_request)

#the contents from html
log = log_response.read()
ipmac = ipmac_response.read()


#############################
#log html parser

dt_log = []
kind_log = []
contents_log = []

soup_log=BeautifulSoup(log)
result_log = soup_log.findAll('td')


dt_log 		 = result_log[1::3]
kind_log 	 = result_log[2::3]
contents_log = result_log[3::3]

#ipmac html parser
soup_ipmac	 =BeautifulSoup(ipmac)
result_ipmac = soup_ipmac.findAll('td')

ipadd 		 = result_ipmac[3::5]
macadd		 = result_ipmac[4::5]
username	 = result_ipmac[5::5]


#IP = re.compile('</?\w+[^>]*>').sub('\n',test)

#clean the result



#for i in contents_log:
#	if "not" in i:
#		log_num=contents_log.index(i)
#		for j in macadd:
#			if j in i:
#				ip_num=macadd.index(j)
#				print ''' IP: %s , MAC: %s, Username: %s, contents_log: %s ''' % (re.compile('</?\w+[^>]*>').sub('\n',ipadd[ip_num]),
#					re.compile('</?\w+[^>]*>').sub('\n',macadd[ip_num]),
#					re.compile('</?\w+[^>]*>').sub('\n',username[ip_num]),
#					re.compile('</?\w+[^>]*>').sub('\n',contents_log[log_num])
#					)


#for i in contents_log:
#	if "not in the allowed list" in i:
#		log_num=contents_log.index(i)
#		for j in macadd:
#			if j in i:
#				ip_num=macadd.index(j)
#				print ''' IP: %s , MAC: %s, Username: %s, contents_log: %s ''' % (ipadd[ip_num],macadd[ip_num],username[ip_num],contents_log[log_num])

#rebuild contents_log
contents=[]
for i in contents_log:
	str = '''%s''' % i
	contents.append(re.compile('</?\w+[^>]*>').sub('\n',str))

def f5(seq, idfun=None):
   # order preserving
   if idfun is None:
       def idfun(x): return x
   seen = {}
   result = []
   for item in seq:
       marker = idfun(item)
       # in old Python versions:
       # if seen.has_key(marker)
       # but in new ones:
       if marker in seen: continue
       seen[marker] = 1
       result.append(item)
   return result

temp_list=[]
for i in contents:
	if "but" in i:
		temp_list.append(i)

result = f5(temp_list)

for i in result:
	for j in macadd:
		if '''%s''' j in 


#for i in contents:
#	if "but" in i:
#		log_num=contents.index(i)
#		for j in macadd:
#			if '''%s''' % j in i:
#				ip_num=macadd.index(j)
#				print ''' IP: %s , MAC: %s, Username: %s, contents_log: %s ''' % (ipadd[ip_num],macadd[ip_num],username[ip_num],contents[log_num])
