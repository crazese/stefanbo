#!/usr/bin/env python
#get the html with authorization

import urllib2, base64
import re
from BeautifulSoup import BeautifulSoup

user = 'admin'
pw = '59715112'

html = 'http://192.168.1.1/sys_log.htm'

def get_html(login_user,login_pw,html):
    auth = 'Basic' + base64.b64encode(login_user+':'+login_pw)
    log_heads = {'Referer': html,
                 'Authorization' : auth}
    log_request = urllib2.Request(html,None,log_heads)
    log_response = urllib2.urlopen(log_request)
    result = log_response.read()
    return result

test = get_html(user,pw,html)