# -*- coding: utf-8 -*-
# 重启路由器脚本
#
import urllib2, base64
 
 
# 192.168.1.1
# admin:admin (BASE64编码)
if __name__ == '__main__':
    # 请求地址
    url = 'http://192.168.1.1/userRpm/SysRebootRpm.htm?Reboot=重启路由器'
    # 验证的用户名和密码
    login_user = 'admin'
    login_pw = 'admin'
     
    auth = 'Basic ' + base64.b64encode('admin:admin')
    print auth
    heads = { 'Referer' : 'http://192.168.1.1/userRpm/SysRebootRpm.htm',
             'Authorization' : auth
    }
     
    # 请求重启路由器
    request = urllib2.Request(url, None, heads)
    response = urllib2.urlopen(request)
    print response.read()


    