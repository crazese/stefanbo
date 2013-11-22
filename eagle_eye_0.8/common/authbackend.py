from django.contrib.auth.models import User
from settings import LDAP_HOST
from settings import LDAP_DOMAIN
import ldap

class ExchangeBackend:

    def authenticate(self, username=None, password=None):
        username_full = LDAP_DOMAIN + "\\" + username
        try:
            l = ldap.initialize(LDAP_HOST, trace_level=0)
            l.set_option(ldap.OPT_REFERRALS, 0)
            l.bind_s(username_full, password)
            l.unbind_s()
            try:
                user = User.objects.get(username=username)
            except User.DoesNotExist:     
                user = User(username=username, password=password)
                user.is_staff = True
                user.save() 
            return user                          
        except:
            return None
                        
    def get_user(self, user_id):
        try:
            return User.objects.get(pk=user_id)
        except User.DoesNotExist:
            return None            
