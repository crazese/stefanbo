from django.conf.urls import patterns, url

from blog import views

from blog.models import Item, Photo

urlpatterns = patterns('',
	url(r'^archive$', views.archive, name='archive'),
)

urlpatterns += patterns('',
	url(r'^$', views.IndexView.as_view(), name='blog_index'),
	#url(r'^item/(?P<pk>\d+)$', views.ItemListView.as_view(), name='item_list'),
	#url(r'^item/(?P<pk>\d+)$', views.ItemDetailView.as_view(), name='item_detail'), 
	#url(r'^photos/(?P<pk>\d+)/$', views.PhotoDetailView.as_view(), name='photo_detail'),
	
	# test 
	url(r'^item/$', views.item_test, name='item_test'),
)
