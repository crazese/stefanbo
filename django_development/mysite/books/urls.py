from django.conf.urls import patterns, url
from mysite.books import views

urlpatterns=patterns('',
	#url(r'^$', views.index, name='index'),
	#(r'^search-form/$',views.search_form),
	#(r'^search/$',views.search),
	(r'^contacts/$',views.contact),
	)