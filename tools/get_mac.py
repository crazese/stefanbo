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
sys_log_html= 'http://192.168.1.1/sys_log.htm'
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

def filter(stri):
	re_h=re.compile('</?\w+[^>]*>')
	stri=re_h.sub('',str(stri))
	return stri


#rebuild contents_log
def rebuild(seq, idfun=None):
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
       x = filter(idfun(item))
       result.append(x)
   return result

temp_list = rebuild(contents_log)

result_list=[]
for i in temp_list[:]:
	if "but" in i:
		result_list.append(i)

for i in rebuild(result_list):
	for j in macadd:
		if filter(j) in i:
			num=macadd.index(j)
			ip = filter(ipadd[num])
			mac = filter(macadd[num])
			user = filter(username[num])
			print "IP : %s , MAC : %s , USERNAME : %s " % (ip,mac,user) 
