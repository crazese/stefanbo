from django.shortcuts import render, get_object_or_404
from django.template import loader, Context
from blog.models import BlogPost, Item, Photo
from django.http import HttpResponse, HttpResponseRedirect
from django.core.urlresolvers import reverse
from django.views import generic

def archive(request):
	posts = BlogPost.objects.all()
	t = loader.get_template('archive.html')
	c = Context({'posts': posts})
	return HttpResponse(t.render(c))

##########################################################

class IndexView(generic.ListView):
	template_name = 'blog_index.html'
	context_object_name = 'latest_item_list'

	def get_queryset(self):
		"""Return the last five published Items."""
		return Item.objects.all()


def blog_index(request):
	latest_item_list = Item.objects.all()
	context = {'latest_item_list': latest_item_list}
	return render(request, 'blog_index.html', context)


class testView(generic.ListView):
	model = Item
	template_name = 'test.html'

def item_test(request):
	test_list = Item.objects.all()
	context = {'item_lists': test_list }
	return render(request, 'test.html', context)