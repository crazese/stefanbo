#from django.conf.urls.defaults import *
from django.conf.urls import patterns, url, include
from django.conf import settings
from mysite import views
from django.contrib import admin
admin.autodiscover()

urlpatterns = patterns('mysite.views',
    (r'^hello/$','hello'),
    #('^/$', my_homepage_view),
    (r'^time/$', 'current_datetime'),
    (r'^time/plus/(\d{1,2})/$','hours_ahead'),
    #(r'^request/$',display_meta),
    # Examples:
    # url(r'^$', 'mysite.views.home', name='home'),
    # url(r'^mysite/', include('mysite.foo.urls')),
    # Uncomment the admin/doc line below to enable admin documentation:
    # url(r'^admin/doc/', include('django.contrib.admindocs.urls')),
    url(r'^books/',include('mysite.books.urls')),
    # Uncomment the next line to enable the admin:
    url(r'^admin/', include(admin.site.urls)),
)

#if settings.DEBUG:
#    urlpatterns += patterns('',
#        (r'^debuginfo/$',views.debug),
#        )


