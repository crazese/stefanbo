from __future__ import unicode_literals
from django.conf.urls import patterns, url
from demo.views import HomePageView, FormView, FormHorizontalView, FormInlineView, PaginationView
from django.views.generic import TemplateView

from demo import views

urlpatterns = patterns('',
    url(r'^$', HomePageView.as_view(), name='home'),
    url(r'^form$', FormView.as_view(), name='form'),
    url(r'^form_horizontal$', FormHorizontalView.as_view(), name='form_horizontal'),
    url(r'^form_inline$', FormInlineView.as_view(), name='form_inline'),
    url(r'^pagination$', PaginationView.as_view(), name='pagination'),
)