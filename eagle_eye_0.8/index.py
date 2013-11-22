# -*- coding:utf-8 -*-

from django.shortcuts import render_to_response
from django.template import RequestContext
from django.contrib.auth.decorators import login_required
from settings import APP_VERSION

@login_required
def main(request):
    ctx = RequestContext(request)
    return render_to_response('main.html',{'user':request.user,},context_instance=ctx)

@login_required
def top(request):
    ctx = RequestContext(request)
    return render_to_response('top.html',{'user':request.user,'version':APP_VERSION},context_instance=ctx)

@login_required
def left(request):
    ctx = RequestContext(request)
    return render_to_response('left.html',{'user':request.user,},context_instance=ctx)

@login_required
def index(request):
    print  request.user.is_authenticated() 
    ctx = RequestContext(request)
    results = {}
    version = "0.1"
    return render_to_response('index.html',{'user':request.user,'version':version, 'object_list':results},context_instance=ctx)




