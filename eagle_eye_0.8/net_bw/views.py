# -*- coding:utf-8 -*-
import string
from django.core.paginator import Paginator, EmptyPage, InvalidPage
from django.db import connection
from django.template import RequestContext

# Create your views here.
from django.http import Http404, HttpResponseRedirect
from django.shortcuts import render_to_response
import time
from base_dict.models import PayType, BusinessType, ISP
from geo_topology.models import City
from net_bw.models import Contact, Room, IP_range, BWModify
from settings import PER_PAGE_COUNT
import logging

def ini_list(request):
    bizs=BusinessType.objects.all()
    citys=City.objects.all()
    pays=PayType.objects.all()
    isps=ISP.objects.all()
    dic={'bizs':bizs,'citys':citys,'pays':pays,'isps':isps}
    return render_to_response('room/addRoom.html',dic)

def room_list(request):
    allrooms=Room.objects.order_by('room_code')
    paginator=Paginator(allrooms,PER_PAGE_COUNT)
    try:
        page=int(request.GET.get('page','1'))
    except ValueError:
        page=1
    try:
        allroom=paginator.page(page)
    except(EmptyPage,InvalidPage):
        allroom=paginator.page(paginator.num_pages)
    bizs=BusinessType.objects.all()
    citys=City.objects.all()
    pays=PayType.objects.all()
    isps=ISP.objects.all()

    pageUrl = "/net_bw/showRoom?"
    dic={'bizs':bizs,'citys':citys,'pays':pays,'isps':isps,'results':allroom, 'pageUrl':pageUrl}
    return render_to_response('room/showRoom.html',dic)

def room_add(request):
    global rooma
    room_code=request.POST.get('room_code')
    room_en_name=request.POST.get('room_en_name')
    room_cn_name=request.POST.get('room_cn_name')
    city=request.POST.get('room_city')
    room_city=City.objects.get(id=city)
    room_address=request.POST.get('room_address')
    isp=request.POST.get('isp')
    room_isp=ISP.objects.get(id=isp)

    max_BW=request.POST.get('max_BW')
    min_BW=request.POST.get('min_BW')
    max_available_BW=request.POST.get('max_available_BW')
    pay=request.POST.get('pay_type')
    room_pay=PayType.objects.get(id=pay)
    room_biz=request.POST.get('biz_type')
    print 'room biz type=' + room_biz

    list=[]
    list.append(room_code)
    list.append(room_en_name)
    list.append(room_cn_name)
    list.append(city)
    list.append(isp)
    list.append(max_BW)
    list.append(min_BW)
    list.append(max_available_BW)
    list.append(pay)
    list.append(room_biz)

    count=0
    for i in range(len(list)):
        l=list[i]
        if l !='':
            count +=1
    if count==10:
        rooma=Room(room_code=room_code,room_en_name=room_en_name,room_cn_name=room_cn_name,room_isp=room_isp,room_city=room_city,room_address=room_address,
                                        max_BW=float(max_BW),min_BW=float(min_BW),max_available_BW=float(max_available_BW),room_pay=room_pay,style='NULL')
        rooma.save()
        rbiz=BusinessType.objects.get(id=room_biz)
        rooma.room_biz.add(rbiz)

    return render_to_response('room/contactIP.html',{'rooma':rooma})

def contactIP(request,id):
    try:
        id=int(id)
    except ValueError:
        raise Http404()
    rooma=Room.objects.get(id=id)
    return render_to_response('room/contactIP.html',{'rooma':rooma})

def contactIP_add(request,id):
    try:
        id=int(id)
    except ValueError:
        raise Http404()
    room=Room.objects.get(id=id)
    name=request.POST.get('contact_name')
    sex=request.POST.get('contact_sex')
    phone=request.POST.get('phone')
    mail=request.POST.get('mail','')
    style=request.POST.get('style')
    inner_start_list=request.POST.getlist('inner_start')
    inner_end_list=request.POST.getlist('inner_end')
    for i in range(len(inner_start_list)):
        inner_start=inner_start_list[i]
        print 'inner_start=' + inner_start
        inner_end=inner_end_list[i]
        print 'inner_end=' + inner_end
        if inner_start != '':
            if inner_end!='':
                    innerip=IP_range(start=inner_start,end=inner_end,kind='inner')
                    innerip.save()
                    rinip=IP_range.objects.get(start=inner_start,end=inner_end,kind='inner')
                    room.inner_IP.add(rinip)
    out_start_list=request.POST.getlist('out_start')
    out_end_list=request.POST.getlist('out_end')
    for j in range(len(out_start_list)):
        out_start=out_start_list[j]
        out_end=out_end_list[j]
        if out_start!='':
            if out_end!='':
                outip=IP_range(start=out_start,end=out_end,kind='out')

                outip.save()
                routip=IP_range.objects.get(start=out_start,end=out_end,kind='out')
                room.out_IP.add(routip)
    internet_start_list=request.POST.getlist('internet_start')
    internet_end_list=request.POST.getlist('internet_end')
    for k in range(len(internet_start_list)):
        internet_start=internet_start_list[k]
        internet_end=internet_end_list[k]
        if internet_start!='':
            if internet_end!='':
                internetip=IP_range(start=internet_start,end=internet_end,kind='internet')
                internetip.save()
                rinternetip=IP_range.objects.get(start=internet_start,end=internet_end,kind='internet')
                room.internet_IP.add(rinternetip)
    if name !='':
        if sex!='':
            if phone!='':
                    contact=Contact(contact_name=name,contact_sex=sex,contact_tel=phone,contact_mail=mail)
                    contact.save()
    room.style=style
    room.save()
    
    rcontact=Contact.objects.get(contact_name=name,contact_sex=sex,contact_tel=phone,contact_mail=mail)
    room.room_contact.add(rcontact)
    return render_to_response('room/addResult.html')

def room_manage(request,id):
    try:
        id=int(id)
    except ValueError:
        raise Http404()
    ctx=RequestContext(request)
    rooma=Room.objects.get(id=id)
    return render_to_response('room/roomManage.html',{'rooma':rooma},context_instance=ctx)

def manage_room(request,id):
    try:
        id=int(id)
    except ValueError:
        raise Http404()
    ctx=RequestContext(request)
    rooma=Room.objects.get(id=id)

    oname_list=request.POST.getlist('ocontact_name')
    osex_list=request.POST.getlist('ocontact_sex')
    ophone_list=request.POST.getlist('ophone')
    omail_list=request.POST.getlist('omail')

    oinner_start_list=request.POST.getlist('oinner_start')
    oinner_end_list=request.POST.getlist('oinner_end')
    rooma.inner_IP.filter(kind='inner').delete()
    for m in range(len(oinner_start_list)):
        oinner_start=oinner_start_list[m]
        oinner_end=oinner_end_list[m]
        oinnerip=IP_range(start=oinner_start,end=oinner_end,kind='inner')
        oinnerip.save()
        orinip=IP_range.objects.get(start=oinner_start,end=oinner_end,kind='inner')
        rooma.inner_IP.add(orinip)
    oout_start_list=request.POST.getlist('oout_start')
    oout_end_list=request.POST.getlist('oout_end')

    rooma.out_IP.filter(kind='out').delete()
    for n in range(len(oout_start_list)):
        oout_start=oout_start_list[n]
        oout_end=oout_end_list[n]
        ooutip=IP_range(start=oout_start,end=oout_end,kind='out')
        ooutip.save()
        oroutip=IP_range.objects.get(start=oout_start,end=oout_end,kind='out')
        rooma.out_IP.add(oroutip)
    ointernet_start_list=request.POST.getlist('ointernet_start')
    ointernet_end_list=request.POST.getlist('ointernet_end')
    rooma.internet_IP.filter(kind='internet').delete()
    for p in range(len(ointernet_start_list)):
        ointernet_start=ointernet_start_list[p]
        ointernet_end=ointernet_end_list[p]
        ointernetip=IP_range(start=ointernet_start,end=ointernet_end,kind='internet')
        ointernetip.save()
        orinternetip=IP_range.objects.get(start=ointernet_start,end=ointernet_end,kind='internet')
        rooma.internet_IP.add(orinternetip)
    rooma.room_contact.all().delete()
    for q in range(len(oname_list)):
            oname=oname_list[q]
            osex=osex_list[q]
            ophone=ophone_list[q]
            omail=omail_list[q]
            ocontact=Contact(contact_name=oname,contact_sex=osex,contact_tel=ophone,contact_mail=omail)
            ocontact.save()
            orcontact=Contact.objects.get(contact_name=oname,contact_sex=osex,contact_tel=ophone,contact_mail=omail)
            rooma.room_contact.add(orcontact)

    
    style=request.POST.get('style')

    inner_start_list=request.POST.getlist('inner_start')
    inner_end_list=request.POST.getlist('inner_end')
    for i in range(len(inner_start_list)):
        inner_start=inner_start_list[i]
        inner_end=inner_end_list[i]
        if inner_start !='':
            if inner_end !='':
                innerip=IP_range(start=inner_start,end=inner_end,kind='inner')
                innerip.save()
                rinip=IP_range.objects.get(start=inner_start,end=inner_end,kind='inner')
                rooma.inner_IP.add(rinip)
    out_start_list=request.POST.getlist('out_start')
    out_end_list=request.POST.getlist('out_end')
    for j in range(len(out_start_list)):
        out_start=out_start_list[j]
        out_end=out_end_list[j]
        if out_start !='':
            if out_end !='':
                outip=IP_range(start=out_start,end=out_end,kind='out')

                outip.save()
                routip=IP_range.objects.get(start=out_start,end=out_end,kind='out')
                rooma.out_IP.add(routip)
    internet_start_list=request.POST.getlist('internet_start')
    internet_end_list=request.POST.getlist('internet_end')
    for k in range(len(internet_start_list)):
        internet_start=internet_start_list[k]
        internet_end=internet_end_list[k]
        if internet_start !='':
            if internet_end !='':
                internetip=IP_range(start=internet_start,end=internet_end,kind='internet')
                internetip.save()
                rinternetip=IP_range.objects.get(start=internet_start,end=internet_end,kind='internet')
                rooma.internet_IP.add(rinternetip)
    rooma.style=style
    rooma.save()
    return render_to_response('room/addResult.html',{'rooma':rooma},context_instance=ctx)

def room_modify(request, id):
    try:
        id = int(id)
    except ValueError:
        raise Http404()
    ctx = RequestContext(request)
    rooma = Room.objects.get(id=id)
    bizs=BusinessType.objects.all()
    citys=City.objects.all()
    pays=PayType.objects.all()
    isps=ISP.objects.all()
       
    return render_to_response('room/roomModify.html',{'rooma':rooma,'bizs':bizs,'citys':citys,'pays':pays,'isps':isps},context_instance=ctx)

def modify_handle(request,id):
    try:
        id=int(id)
    except ValueError:
        raise Http404()
    ctx = RequestContext(request)
    rooma=Room.objects.get(id=id)
    room_en_name=request.POST.get('room_en_name')
    room_cn_name=request.POST.get('room_cn_name')
    city=request.POST.get('room_city')
    room_city=City.objects.get(id=city)
    room_address=request.POST.get('room_address')
    isp=request.POST.get('isp')
    room_isp=ISP.objects.get(id=isp)

    max_BW=request.POST.get('max_BW')
    min_BW=request.POST.get('min_BW')
    max_available_BW=request.POST.get('max_available_BW')
    pay=request.POST.get('pay_type')
    room_pay=PayType.objects.get(id=pay)
    room_biz_list=request.POST.getlist('biz_type')

    rooma.room_en_name=room_en_name
    rooma.room_cn_name=room_cn_name
    rooma.room_city=room_city
    rooma.room_address=room_address
    rooma.room_pay=room_pay
    rooma.room_isp=room_isp
    max=float(max_BW)
    min=float(min_BW)
    max_available=float(max_available_BW)
    if max!=rooma.max_BW:
        history1=BWModify(room=rooma,kind='MAX_BW',old_value=rooma.max_BW,new_value=float(max_BW),time=time.strftime('%Y-%m-%d',time.localtime(time.time())))
        history1.save()
    if min!=rooma.min_BW:
        history2=BWModify(room=rooma,kind='MIN_BW',old_value=rooma.min_BW,new_value=float(min_BW),time=time.strftime('%Y-%m-%d',time.localtime(time.time())))
        history2.save()
    if max_available!=rooma.max_available_BW:
        history3=BWModify(room=rooma,kind='MAX_AVAILABLE_BW',old_value=rooma.max_available_BW,new_value=float(max_available),time=time.strftime('%Y-%m-%d',time.localtime(time.time())))
        history3.save()
    rooma.max_BW=float(max_BW)
    rooma.min_BW=float(min_BW)
    rooma.max_available_BW=float(max_available_BW)
    rooma.save()

    for biza in room_biz_list:
        biz=BusinessType.objects.get(id=biza)
        rooma.room_biz.add(biz)
    return render_to_response('room/modifyResult.html',context_instance=ctx)


def room_delete(request,id):
    try:
        id = int(id)
    except ValueError:
        raise Http404()
    ctx = RequestContext(request)
    rooma = Room.objects.get(id=id)
    rooma.delete()
    return render_to_response('room/delResult.html',{ 'rooma':rooma},context_instance=ctx)

def show_bw_modify(request):
    bws=BWModify.objects.all()
    return render_to_response('room/BWModifyHistory.html',{'bws':bws})

def contact(request,id):
    try:
        id=int(id)
    except ValueError:
        raise Http404()
    rooma=Room.objects.get(id=id)
    return render_to_response('room/addContact.html',{'rooma':rooma})

def add_contact(request,id):
    try:
        id=int(id)
    except ValueError:
        raise Http404()
    rooma=Room.objects.get(id=id)
    name=request.POST.get('contact_name')
    sex=request.POST.get('contact_sex')
    phone=request.POST.get('phone')
    mail=request.POST.get('mail')
    if name !='':
        if sex !='':
            if phone !='':
                contact=Contact(contact_name=name,contact_sex=sex,contact_tel=phone,contact_mail=mail)
                contact.save()
                rooma.room_contact.add(contact)
    return HttpResponseRedirect('/net_bw/success/')

def success(request):
    return render_to_response('room/sucess.html')

def room_search(request):
    global rooms
    rooms=[]
    room_code=request.POST.get('r_code','')
    room_cn_name=request.POST.get('r_cn_name','')
    room_isp=request.POST.get('r_isp','')
    room_address=request.POST.get('r_address','')
    maxbw=request.POST.get('r_maxbw','')
    minbw=request.POST.get('r_minbw','')
    room_city=request.POST.get('r_city','')
    room_pay=request.POST.get('pay_type','')
    cursor=connection.cursor()
    s="select * from net_bw_room as room,net_bw_room_room_biz as biz "
    str=''

    if room_code !='':
        print room_code
        #str_code=' and room.room_code="%s"' %room_code
        str_code = " and room.room_code= \'" + room_code + "\'"
        str +=str_code

    if room_isp !='':
        print room_isp
        #str_isp=' and room.room_isp_id="%s"' %room_isp
        str_isp = " and room.room_isp_id = " + room_isp
        str +=str_isp

    if room_cn_name !='':
        room_cn_name = '%%' + room_cn_name + '%%'
        str_cn_name = ' and room.room_cn_name like "%s"' %room_cn_name
        str +=str_cn_name
    
    if room_address !='':
        room_address = '%%' + room_address + '%%'
        str_address=' and room.room_address like "%s"' %room_address
        str +=str_address

    if maxbw !='':
        #str_max=' and room.max_BW="%s"' %room_max
        str_max = " and room.max_BW= " + maxbw
        str +=str_max

    if minbw !='':
        #str_min=' and room.min_BW="%s"' %room_min
        str_min = " and room.min_BW= "+ minbw
        str +=str_min

    if room_city !='':
        #str_city=' and room.room_city_id="%s"' %room_city
        str_city = " and room.room_city_id= "+ room_city
        str +=str_city

    if room_pay !='':
        #str_pay=' and room.room_pay_id="%s"' %room_pay
        str_pay = " and room.room_pay_id= "+ room_pay
        str +=str_pay

    if str!='':
        str=str[4:str.__len__()]
        s +="where "
        s +=str
        print s
        cursor.execute(s)
        for row in cursor.fetchall():
                room=Room.objects.get(id=row[0])
                if room not in rooms:
                    rooms.append(room)
        cursor.close()
    return render_to_response('room/searchResult.html',{'rooms':rooms})

def ip_room(request):
    rooms=Room.objects.all()
    paginator=Paginator(rooms,PER_PAGE_COUNT)
    try:
        page=int(request.GET.get('page','1'))
    except ValueError:
        page=1
    try:
        allroom=paginator.page(page)
    except(EmptyPage,InvalidPage):
        allroom=paginator.page(paginator.num_pages)
    isps=ISP.objects.all()
    citys=City.objects.all()
    pays=PayType.objects.all()
    pageUrl = "/net_bw/ip_room?"
    return render_to_response('room/IProom.html',{'results':allroom,'isps':isps,'citys':citys,'pays':pays,'pageUrl':pageUrl})

def ip_search(request):
    global rooms
    rooms=[]
    room_code=request.POST.get('r_code','')
    room_cn_name=request.POST.get('r_cn_name','')
    room_isp=request.POST.get('r_isp','')
    style=request.POST.get('style','')
    room_city=request.POST.get('r_city','')
    innerip=request.POST.get('inner','')
    outip=request.POST.get('out','')
    internetip=request.POST.get('internet','')

    cursor=connection.cursor()
    s='select * from net_bw_room as room,net_bw_room_room_biz as biz '
    str=''

    if room_code !='':
        str_code=' and room.room_code="%s"' %room_code
        str +=str_code

    if room_isp !='':
        str_isp=' and room.room_isp_id="%s"' %room_isp
        str +=str_isp


    if room_cn_name !='':
        room_cn_name = '%%' + room_cn_name + '%%'
        str_cn_name='and room.room_cn_name like "%s"' %room_cn_name
        str +=str_cn_name
        
    if room_city !='':
        str_city='and room.room_city_id="%s"' %room_city
        str +=str_city

    if style !='':
        str_style=' and room.style="%s"'%style
        str +=str_style

    if str!='':
        str=str[4:str.__len__()]
        s +='where '
        s +=str
        print '---' + s
        cursor.execute(s)
        for row in cursor.fetchall():
                room=Room.objects.get(id=row[0])
                if room not in rooms:
                    rooms.append(room)

    if innerip !='':
           cursor.execute('select  *   from  net_bw_ip_range   where   INET_ATON("%s")   BETWEEN   INET_ATON(start)   AND   INET_ATON(end) AND kind="inner"' % innerip)
           for row in cursor.fetchall():
                room=Room.objects.filter(inner_IP=row[0]).distinct()
                for r in room:
                    rooms.append(r)

    if outip !='':
           cursor.execute('select  *   from  net_bw_ip_range   where   INET_ATON("%s")   BETWEEN   INET_ATON(start)   AND   INET_ATON(end) AND kind="out"' % outip)
           for row in cursor.fetchall():
                room=Room.objects.filter(out_IP=row[0]).distinct()
                for r in room:
                    rooms.append(r)

    if internetip !='':
           cursor.execute('select  *   from  net_bw_ip_range   where   INET_ATON("%s")   BETWEEN   INET_ATON(start)   AND   INET_ATON(end) AND kind="internet"' % internetip)
           for row in cursor.fetchall():
                room=Room.objects.filter(internet_IP=row[0]).distinct()
                for r in room:
                    rooms.append(r)
    cursor.close()

    return render_to_response('room/IPResult.html',{'rooms':rooms})