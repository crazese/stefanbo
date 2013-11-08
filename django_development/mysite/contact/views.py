from django.shortcuts import render_to_response
from mysite.models import MyModel

def foobar_view(request, template_name):
	m_list = MyModel.objects.filter(is_new=True)
	return render_to_response(template_name, {'m_list': m_list})

def object_list(request, model):
	obj_list = model.objects.all()
	template_name = 'mysite/%s_list.html' % model.__name__.lower()
	return render_to_response(template_name, {'object_list': obj_list})