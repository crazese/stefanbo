from __future__ import unicode_literals
from django.conf.urls import patterns, url
from django.views.generic import TemplateView

from demo_test.views import *
urlpatterns = patterns('',
    url(r'^$', HomePageView.as_view(), name='home'),
    url(r'^form$', FormView.as_view(), name='form'),
    url(r'^form_horizontal$', FormHorizontalView.as_view(), name='form_horizontal'),
    url(r'^form_inline$', FormInlineView.as_view(), name='form_inline'),
    url(r'^pagination$', PaginationView.as_view(), name='pagination'),
    url(r'^bootstrap$', BootstrapView.as_view(), name='bootstrap'),
    url(r'^check$', CheckView.as_view(), name='check'),
)
