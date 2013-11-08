from django.conf.urls.defaults import *
from mysite.contant import views

urlpatterns = patterns('',
	(r'^foo/$', views.foobar_view, {'template_name': 'template1.html'}),
	(r'^bar/$', views.foobar_view, {'template_name': 'template2.html'}),
	)

urlpatterns =+ patterns('',
	(r'^events/$',views.object_list, {'model': models.Event}),
	(r'^blog/entries/$', views.object_list, {'model': models.BlogEntry}),
	)