#!/usr/bin/env python
#coding=utf-8
                                                                                                                                                                                   
import json
import urllib2
import sys
from urllib2 import Request, urlopen, URLError, HTTPError
                                                                                                                                                                                   
#url and url header
zabbix_url="http://192.168.1.203:82/api_jsonrpc.php"
zabbix_header = {"Content-Type":"application/json"}
zabbix_user   = "Admin"
zabbix_pass   = "zabbix"
auth_code     = ""
                                                                                                                                                                                   
#auth user and password
auth_data = json.dumps(
        {
            "jsonrpc":"2.0",
            "method":"user.login",
            "params":
                    {
                        "user":zabbix_user,
                        "password":zabbix_pass
                    },
            "id":0
        })
                                                                                                                                                                                   
# create request object
request = urllib2.Request(zabbix_url,auth_data)
for key in zabbix_header:
    request.add_header(key,zabbix_header[key])
                                                                                                                                                                                   
#auth and get authid
try:
    result = urllib2.urlopen(request)
except HTTPError, e:
    print 'The server couldn\'t fulfill the request, Error code: ', e.code
except URLError, e:
    print 'We failed to reach a server.Reason: ', e.reason
else:
    response=json.loads(result.read())
    result.close()
    '''
    sucess result:
        {"jsonrpc":"2.0",
         "result":"0d225d8d2a058625f814f3a0749cd218",
         "id":0}
    error  result:
        {'code': -32602,
         'data': 'Login name or password is incorrect.',
         'message': 'Invalid params.'}
　　'''
    if  'result'  in  response:
        auth_code=response['result']
    else:
        print  response['error']['data']
                                                                                                                                                                                    
# request json
if len(auth_code) == 0:
    sys.exit(1)
if len(auth_code) != 0:
    get_host_data = json.dumps(
    {
        "jsonrpc":"2.0",
        "method":"host.get",
        "params":{
                "output": "extend",
        },
        "auth":auth_code,
        "id":1,
    })
                                                                                                                                                                                    
    # create request object
    request = urllib2.Request(zabbix_url,get_host_data)
    for key in zabbix_header:
        request.add_header(key,zabbix_header[key])
                                                                                                                                                                                    
    # get host list
    try:
        result = urllib2.urlopen(request)
    except URLError as e:
        if hasattr(e, 'reason'):
            print 'We failed to reach a server.'
            print 'Reason: ', e.reason
        elif hasattr(e, 'code'):
            print 'The server could not fulfill the request.'
            print 'Error code: ', e.code
    else:
        response = json.loads(result.read())
        result.close()
                                                                                                                                                                                          
        print response
        print "Number Of Hosts: ", len(response['result'])