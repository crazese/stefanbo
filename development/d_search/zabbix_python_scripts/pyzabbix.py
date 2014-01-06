import logging
import requests
import json


class _NullHandler(logging.Handler):

    def emit(self, record):
        pass

logger = logging.getLogger(__name__)
logger.addHandler(_NullHandler())


class ZabbixAPIException(Exception):

    """ generic zabbix api exception
    code list:
         -32602 - Invalid params (eg already exists)
         -32500 - no permissions
    """
    pass


class ZabbixAPI(object):

    def __init__(self,
                 server='http://localhost/zabbix',
                 session=None,
                 use_authenticate=False):
        """
        Parameters:
            server: Base URI for zabbix web interface (omitting /api_jsonrpc.php)
            session: optional pre-configured requests.Session instance
            use_authenticate: Use old (Zabbix 1.8) style authentication
        """

        if session:
            self.session = session
        else:
            self.session = requests.Session()

        # Default headers for all requests
        self.session.headers.update({
            'Content-Type': 'application/json-rpc',
            'User-Agent': 'python/pyzabbix'
        })

        self.use_authenticate = use_authenticate
        self.auth = ''
        self.id = 0

        self.url = server + '/api_jsonrpc.php'
        logger.info("JSON-RPC Server Endpoint: %s", self.url)

    def login(self, user, password):
        user_info = {'user': user,
                     'password': password}
        obj = self.json_obj('user.login', user_info)
        content = self.postRequest(obj)
        try:
            self.auth = content['result']
        except KeyError, e:
            e = content['error']['data']
            print e

    def json_obj(self, method, params):
        obj = {'jsonrpc': '2.0',
               'method': method,
               'params': params,
               'auth': self.auth,
               'id': self.id}
        return json.dumps(obj)

    def postRequest(self, json_obj):
        # print 'Post: %s' % json_obj
        headers = {'Content-Type': 'application/json-rpc',
                   'User-Agent': 'python/zabbix_api'}
        req = urllib2.Request(self.url, json_obj, headers)
        opener = urllib2.urlopen(req)
        content = json.loads(opener.read())
        self.id += 1
        # print 'Receive: %s' % content
        return content

    def confimport(self, format='', source='', rules=''):
        """Alias for configuration.import because it clashes with
           Python's import reserved keyword"""

        return self.do_request(
            method="configuration.import",
            params={"format": format, "source": source, "rules": rules}
        )['result']

    def api_version(self):
        return self.apiinfo.version()

    def do_request(self, method, params=None):
        request_json = {
            'jsonrpc': '2.0',
            'method': method,
            'params': params or {},
            'auth': self.auth,
            'id': self.id,
        }

        logger.debug("Sending: %s", str(request_json))
        response = self.session.post(
            self.url,
            data=json.dumps(request_json),
        )
        logger.debug("Response Code: %s", str(response.status_code))

        # NOTE: Getting a 412 response code means the headers are not in the
        # list of allowed headers.
        response.raise_for_status()

        if not len(response.text):
            raise ZabbixAPIException("Received empty response")

        try:
            response_json = json.loads(response.text)
        except ValueError:
            raise ZabbixAPIException(
                "Unable to parse json: %s" % response.text
            )
        logger.debug("Response Body: %s", json.dumps(response_json,
                                                     indent=4,
                                                     separators=(',', ': ')))

        self.id += 1

        if 'error' in response_json:  # some exception
            msg = "Error {code}: {message}, {data} while sending {json}".format(
                code=response_json['error']['code'],
                message=response_json['error']['message'],
                data=response_json['error']['data'],
                json=str(request_json)
            )
            raise ZabbixAPIException(msg, response_json['error']['code'])

        return response_json

    def __getattr__(self, attr):
        """Dynamically create an object class (ie: host)"""
        return ZabbixAPIObjectClass(attr, self)


class ZabbixAPIObjectClass(object):

    def __init__(self, name, parent):
        self.name = name
        self.parent = parent

    def __getattr__(self, attr):
        """Dynamically create a method (ie: get)"""

        def fn(*args, **kwargs):
            if args and kwargs:
                raise TypeError("Found both args and kwargs")

            return self.parent.do_request(
                '{0}.{1}'.format(self.name, attr),
                args or kwargs
            )['result']

        return fn
