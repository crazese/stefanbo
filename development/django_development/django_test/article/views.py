from django.http import HttpResponse  
from django.shortcuts import render_to_response  
from article.models import Article  
  
def hello(request):  
    name    ="Mike"  
    html    =" <html> <body> Hi %s,this seems to have worked!  </body> </html> " % name  
    return HttpResponse(html)  
  
def hello_template_simple(request):  
    name    ="Mike"  
    return render_to_response('hello.html',{'name':name})  
  
def articles(request):  
    language    ='en-gb'  
    session_language    ='en-gb'  
    if 'lang' in request.COOKIES:  
        language    = request.COOKIES['lang']  
  
    if 'lang' in request.session:  
        session_language =request.session['lang']  
  
    return render_to_response('articles.html',  
            {'articles':Article.objects.all(),'language':language,'session_language':session_language})  
def article(request,article_id=1):  
    return render_to_response('article.html',  
            {'article':Article.objects.get(id=article_id)})  
def language(request,language='en-gb'):  
    response    =HttpResponse("setting language to %s" % language)  
    response.set_cookie('lang',language)  
    request.session['lang']=language  
    return response  