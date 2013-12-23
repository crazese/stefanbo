from django.conf.urls import patterns, include, url

from django.contrib import admin
admin.autodiscover()

from article.views import HelloTemplate


urlpatterns = patterns('',
    # Examples:
    # url(r'^$', 'django_test.views.home', name='home'),
    # url(r'^blog/', include('blog.urls')),
    url(r'^hello/$','article.views.hello'),
    #url(r'^hello_template/$', 'article.views.hello_template'),
    #url(r'^hello_template_simple/$', 'article.views.hello_template_simple'),
    #url(r'^hello_class_view/$', 'HelloTemplate.as_view()'),

    url(r'^articles/', include('article.urls')),

    url(r'^admin/', include(admin.site.urls)),
)
