from django.conf.urls import patterns, include, url
from django.conf import settings
from django.contrib import admin
admin.autodiscover()

from django.views.generic import TemplateView

urlpatterns = patterns('',
    # Examples:
    # url(r'^$', 'django_mysite.views.home', name='home'),
    # url(r'^blog/', include('blog.urls')),
    url(r'^polls/', include('polls.urls', namespace="polls")),
    url(r'^admin/', include(admin.site.urls)),

    # bootstrap toolkit
    url(r'^bootstrap/', include('demo_app.urls', namespace="demo_app")),

    # blog
    url(r'^blog/', include('blog.urls', namespace="blog")),
)

