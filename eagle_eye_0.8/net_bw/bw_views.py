# -*- coding:utf-8 -*-
import datetime
import decimal
import string
import urllib
from django.http import Http404

from django.shortcuts import render_to_response
from django.template import RequestContext
from django.contrib.auth.decorators import login_required
from django.core.paginator import Paginator, InvalidPage, EmptyPage
from base_dict.models import PayType
import commUtil
import models
from net_bw.models import BandWidth, Room, BandWidthDaily
from django.db import connection
from settings import PER_PAGE_COUNT
import utils
from django.utils.http import urlquote

#机房带宽展示页面
@login_required
def index(request, id):
    version = "0.1"
    #list = [61,62,67,72,73,74,75,76,81,84,85,95,106,107,108,109,110,126,128,132,133,134,137,138,139,140,143,146,152,153,154,155,156]
    #list = [62,61,67]
    #commUtil.getCommGroupBW('2011-09-01', 2, list)
    #models.BandWidth().getBWByRoom(begin_time='2011-09-01', days=2, room_list=list)
    ctx = RequestContext(request)
    results_list = []
    pageUrl = ""

    room_code = ''
    room_name = ''
    total_bw = ''
    vod_bw = ''
    live_bw = ''
    web_bw = ''
    time = ''

    try:
        id = int(id)
    except ValueError:
        raise Http404()

    if id == 1:
        results_list = BandWidth.objects.all().order_by('-time')
        pageUrl = "/net_bw/room_bw/1?"

    if id == 2:
        if request.method == 'GET':
            room_code = request.GET['room_code']
            room = request.GET['room_name']
            if room != '':
                #url转成中文
                room_name = urllib.unquote(room)
            total_bw = request.GET['total_bw']
            vod_bw = request.GET['vod_bw']
            live_bw = request.GET['live_bw']
            web_bw = request.GET['web_bw']
            time = request.GET['time']

        if request.method == 'POST':
            room_code = request.POST['room_code']
            room_name = request.POST['room_name']
            total_bw = request.POST['total_bw']
            vod_bw = request.POST['vod_bw']
            live_bw = request.POST['live_bw']
            web_bw = request.POST['web_bw']
            time = request.POST['time']
            
        room_id = 0
        sql = "select id, room_id, bw_value, vod_bw_value, live_bw_value, web_bw_value, time from net_bw_bandwidth where 1 = 1 "
        whereSql = ""

        if room_code != '':
            try:
                room_id = Room.objects.get(room_code=room_code).id
            except Room.DoesNotExist:
                room_id = 99999


        if room_name != '':
            room_id = Room.objects.get(room_cn_name=room_name).id

        if room_code != '' and room_name != '':
            room_id = Room.objects.get(room_code=room_code, room_cn_name=room_name).id

        if room_id != 0:
            whereSql = whereSql + " and room_id = " + str(room_id)

        if total_bw != '':
            whereSql = whereSql + " and bw_value = " + total_bw

        if vod_bw != '':
            whereSql = whereSql + " and vod_bw_value = " + vod_bw

        if live_bw != '':
            whereSql = whereSql + " and live_bw_value = " + live_bw

        if web_bw != '':
            whereSql = whereSql + " and web_bw_value = " + web_bw

        if time != '':
            whereSql = whereSql + " and time = \'" + time + "\'"

        if whereSql == '':
            results_list = BandWidth.objects.all().order_by('-time')
        else:
            cursor = connection.cursor()
            cursor.execute(sql + whereSql)
            for row in cursor.fetchall():
                results_list.append({'room':Room.objects.get(id=row[1]), 'bw_value':row[2], 'vod_bw_value':row[3], 'live_bw_value':row[4], 'web_bw_value':row[5], 'time':row[6],'id':row[0]})

            params = (('room_name',room_name.encode('utf8')), ('room_code',room_code))
            pageUrl = "/net_bw/room_bw/2?" + urllib.urlencode(params) + "&total_bw=" + total_bw + "&vod_bw=" + vod_bw + "&live_bw=" + live_bw + "&web_bw=" + web_bw + "&time=" + time + "&"
            
    # 分页
    paginator = Paginator(results_list, PER_PAGE_COUNT)

    try:
        page = int(request.GET.get('page', '1'))
    except ValueError:
        page = 1

    try:
        results = paginator.page(page)
    except (EmptyPage, InvalidPage):
        results = paginator.page(paginator.num_pages)

    return_value = {'user': request.user, 'version': version, 'results': results, 'pageUrl':pageUrl, 'room_code':room_code,'room_name':room_name,'total_bw':total_bw,'vod_bw':vod_bw,'live_bw':live_bw,'web_bw':web_bw,'time':time}
    return render_to_response('net_bw/room_bw.html', return_value,
                              context_instance=ctx)

#机房日利用率
@login_required
def day_report(request, id):
    now = (datetime.datetime.now() + datetime.timedelta(days=-1)).strftime('%Y-%m-%d')
    try:
        id = int(id)
    except ValueError:
        raise Http404()

    if id == 1:
        time = now

    if id == 2:
        time = request.POST['time']
        if time == '':
            time = now

    ctx = RequestContext(request)

    list = []

    cursor = connection.cursor()
    sql = "select b.room_code as code, b.room_cn_name as name, b.max_BW as maxBW, b.min_BW as payBW, a.bw_value as valueBW, a.rate as rate, a.time as time from net_bw_bandwidth a, net_bw_room b, base_dict_paytype c where a.room_id = b.id and b.room_pay_id = c.id and c.pay_code = 01 and a.time = \'" + time + "\'"
    cursor.execute(sql)
    for row in cursor.fetchall():
        list.append(
                {"code": row[0], "name": row[1], "max_BW": row[2], "min_BW": row[3], "valueBW": row[4], "rate": row[5],
                 "time": row[6]})
    cursor.close()

    bandWidthDaily = BandWidthDaily.objects.all().filter(time=time)

    version = "0.1"

    return_value = {'user': request.user, 'version': version, 'object_list': list, 'time': time,
                    'bandWidthDaily': bandWidthDaily}
    return render_to_response('net_bw/room_bw_day_report.html', return_value, context_instance=ctx)

#机房周利用率
@login_required
def week_report(request, id):
    begintime = ''
    endtime = ''
    try:
        id = int(id)
    except ValueError:
        raise Http404()

    if id == 1:
        time = datetime.datetime.now()

    if id == 2:
        strtime = request.POST['time']
        if strtime == '':
            time = datetime.datetime.now()
        else:
            time = datetime.datetime.strptime(strtime, '%Y-%m-%d')
    #monday:0 sunday:6
    #get weekday
    weekday = time.weekday()

    if weekday == 0:
        begintime = time.strftime('%Y-%m-%d')
        enddate = time + datetime.timedelta(days=6)
        endtime = enddate.strftime('%Y-%m-%d')
    elif weekday == 6:
        endtime = time.strftime('%Y-%m-%d')
        begindate = time + datetime.timedelta(days=-6)
        begintime = begindate.strftime('%Y-%m-%d')
    else:
        begindate = time + datetime.timedelta(days=(0 - weekday))
        begintime = begindate.strftime('%Y-%m-%d')
        enddate = time + datetime.timedelta(days=(6 - weekday))
        endtime = enddate.strftime('%Y-%m-%d')

    time = begintime + '-' + endtime

    ctx = RequestContext(request)

    version = "0.1"

    list = []
    cursor = connection.cursor()
    sql = "select b.room_code as code, b.room_cn_name as name, b.id as id from net_bw_bandwidth a, net_bw_room b,base_dict_paytype c where a.room_id = b.id and c.id = b.room_pay_id and c.pay_code = \'01\' and a.time >= \'" + begintime + "\' and a.time <= \'" + endtime + "\' group by a.room_id"
    cursor.execute(sql)
    room = []
    for row in cursor.fetchall():
        room_list = []
        room_list.append(row[2])
        room.append(row[2])
        comm_list = commUtil.getCommGroupBW(begintime, 7, room_list)

        #paid_bw = utils.getPaidBW(comm_list[0]['sum_service_bw'],comm_list[0]['sum_paid_bw'],comm_list[0]['sum_peak_value'])
        list.append({"code": row[0], "name": row[1], "max_BW": comm_list[0]['sum_service_bw'], "min_BW": comm_list[0]['sum_paid_bw'], "max_value": comm_list[0]['sum_peak_value'],"max_rate": comm_list[0]['rate'], "id": row[2]})

    cursor.close()
    if len(room) > 0:
        summary = commUtil.getCommGroupBW(begintime, 7, room)

        return_value = {'user': request.user, 'version': version, 'object_list': list, 'time': time,
                    'sum_max_BW': summary[0]['sum_service_bw'], 'sum_min_BW': summary[0]['sum_paid_bw'], 'sum_bw_value': summary[0]['sum_peak_value'],
                    'max_rate': summary[0]['rate']}
    else:
        return_value = {'user': request.user, 'version': version, 'object_list': list, 'time': time}
    return render_to_response('net_bw/room_bw_week_report.html', return_value, context_instance=ctx)

#机房月利用率
@login_required
def month_report(request, id, query_type):
    now = datetime.datetime.now().strftime('%Y-%m')
    print 'query_type' + query_type

    months = utils.getLastSixMonths()

    selectedMonth = now

    timeSql = ""
    chartTime = ""
    days = 0
    begintime = ""
    endtime = ""
    time = ""
    try:
        id = int(id)
    except ValueError:
        raise Http404()

    if id == 1:
        time = now
        selectedMonth = now
        timeSql = " and a.time like \'" + time + "%%\'"
        chartTime = time + "-01"
        days = utils.getDays(time)

    if id == 2:
        begintime = request.POST['beginTime']
        endtime = request.POST['endTime']

        if begintime != '' and endtime != '':
            timeSql = " and a.time >= \'" + begintime + "\' and a.time <= \'" + endtime + "\'"
            chartTime = begintime
            d1 = datetime.date(int(begintime[0:4]),int(begintime[5:7]),int(begintime[8:10]))
            d2 = datetime.date(int(endtime[0:4]),int(endtime[5:7]),int(endtime[8:10]))
            days = (d2-d1).days + 1
            time = begintime + "-" + endtime

    if id == 3:
        time = query_type

        if time == '':
            time = now
        timeSql = " and a.time like \'" + time + "%%\'"
        chartTime = time + "-01"
        days = utils.getDays(time)

        selectedMonth = time
        print months

    ctx = RequestContext(request)

    version = "0.1"

    list = []
    cursor = connection.cursor()
    sql = "select b.room_code as code, b.room_cn_name as name, b.id as id from net_bw_bandwidth a, net_bw_room b,base_dict_paytype c where a.room_id = b.id and c.id = b.room_pay_id and c.pay_code = \'01\'" + timeSql + " group by a.room_id"

    print sql
    cursor.execute(sql)
    room = []
    for row in cursor.fetchall():
        room_list = []
        room_list.append(row[2])
        room.append(row[2])
        comm_list = commUtil.getCommGroupBW(chartTime, days, room_list)
        list.append({"code": row[0], "name": row[1], "max_BW": comm_list[0]['sum_service_bw'], "min_BW": comm_list[0]['sum_paid_bw'], "max_value": comm_list[0]['sum_peak_value'],
                     "max_rate": comm_list[0]['rate'], "id": row[2]})

    if len(room) > 0:
        summary = commUtil.getCommGroupBW(chartTime,days, room)
        print '------summary=' + str(summary)

        rateYAxisName = 'Rate'
        bwYAxisName = 'BandWidth'
        subcaption = time

        listChart = []
        for i in range(days):
            comm_time = (datetime.date(int(chartTime[0:4]),int(chartTime[5:7]),int(chartTime[8:10])) + datetime.timedelta(days=i)).strftime('%Y-%m-%d')
            sql = "select room_id from net_bw_bandwidth where time = \'" + comm_time +"\'"
            cursor.execute(sql)
            room_list = []
            for row in cursor.fetchall():
                room_list.append(row[0])
            comm_list = commUtil.getCommGroupBW(comm_time, 0, room_list)
            if days > 31:
                if i%5 == 0:
                    listChart.append({'time':comm_time,'bw':comm_list[0]['sum_peak_value'],'rate':comm_list[0]['rate']*100, 'show_name':1})
                else:
                    listChart.append({'time':comm_time,'bw':comm_list[0]['sum_peak_value'],'rate':comm_list[0]['rate']*100, 'show_name':0})

                if i == days - 1:
                    listChart.append({'time':comm_time,'bw':comm_list[0]['sum_peak_value'],'rate':comm_list[0]['rate']*100, 'show_name':1})
            else:
                listChart.append({'time':comm_time,'bw':comm_list[0]['sum_peak_value'],'rate':comm_list[0]['rate']*100, 'show_name':1})

        cursor.close()

        lenLst = len(listChart)
        print listChart
        today = listChart[lenLst-1]['time']
        if days != lenLst:
            for i in range(days - lenLst):
                timeLst = today[0:7] + '-' + str(int(today[8:10]) + i + 1)
                listChart.append({'time':timeLst,'bw':0,'rate':0})

        rateCaption = '全网月利用率曲线图'
        bwCaption = '全网月峰值带宽曲线图'

        return_value = {'beginTime':begintime, 'endTime':endtime,'selectedMonth':selectedMonth,'months':months, 'user': request.user, 'version': version, 'object_list': list, 'time': time,
                        'sum_max_BW': summary[0]['sum_service_bw'], 'sum_min_BW': summary[0]['sum_paid_bw'], 'sum_bw_value': summary[0]['sum_peak_value'],
                        'sum_rate': summary[0]['rate'], 'rateYAxisName': rateYAxisName, 'bwYAxisName': bwYAxisName, 'subcaption':subcaption, 'rateCaption':rateCaption, 'bwCaption':bwCaption,'listChart':listChart}
    else:
        return_value = {'beginTime':begintime, 'endTime':endtime,'selectedMonth':selectedMonth,'months':months, 'user': request.user, 'version': version}

    return render_to_response('net_bw/room_bw_month_report.html', return_value, context_instance=ctx)

#isp日利用率
@login_required
def day_isp(request, id):
    now = (datetime.datetime.now() + + datetime.timedelta(days=-1)).strftime('%Y-%m-%d')
    try:
        id = int(id)
    except ValueError:
        raise Http404()

    if id == 1:
        time = now

    if id == 2:
        time = request.POST['time']
        if time == '':
            time = now

    ctx = RequestContext(request)

    version = "0.1"

    list = []
    cursor = connection.cursor()
    sql = "select c.isp_code as isp_code, c.isp_cn_name as name, sum(b.max_BW) as sum_max, sum(b.min_BW) as sum_min, sum(a.bw_value) as sum_bw, avg(a.rate) as avg_rate from net_bw_bandwidth a, net_bw_room b, base_dict_isp c, base_dict_paytype d where a.time = \'" + time + "\' and a.room_id = b.id and b.room_isp_id = c.id and d.id = b.room_pay_id and d.pay_code = \'01\' group by c.id"

    cursor.execute(sql)
    for row in cursor.fetchall():
        min_BW = []
        bw_value = []
        sql = "select b.min_BW as min_BW, a.bw_value as bw_value from net_bw_bandwidth a, net_bw_room b, base_dict_isp c, base_dict_paytype d where a.time = \'" + time + "\' and a.room_id = b.id and b.room_isp_id = c.id and d.id = b.room_pay_id and d.pay_code = \'01\' and c.isp_code = \'" + row[0] + "\'"
        cursor.execute(sql)
        for bw in cursor.fetchall():
            min_BW.append(bw[0])
            bw_value.append(bw[1])
            
        rate = utils.getDailyRate(min_BW, bw_value)
        
        list.append({"isp_code": row[0], "name": row[1], "sum_max": row[2], "sum_min": row[3], "sum_bw": row[4],
                     "avg_rate": rate})

    cursor.close()

    bandWidthDaily = BandWidthDaily.objects.all().filter(time=time)

    return_value = {'user': request.user, 'version': version, 'object_list': list, 'time': time,
                    'bandWidthDaily': bandWidthDaily}

    return render_to_response('net_bw/room_bw_day_isp.html', return_value, context_instance=ctx)

#isp周利用率
@login_required
def week_isp(request, id):
    begintime = ''
    endtime = ''
    try:
        id = int(id)
    except ValueError:
        raise Http404()

    if id == 1:
        time = datetime.datetime.now()

    if id == 2:
        strtime = request.POST['time']
        if strtime == '':
            time = datetime.datetime.now()
        else:
            time = datetime.datetime.strptime(strtime, '%Y-%m-%d')
            #monday:0 sunday:6
        #get weekday
    weekday = time.weekday()

    if weekday == 0:
        begintime = time.strftime('%Y-%m-%d')
        enddate = time + datetime.timedelta(days=6)
        endtime = enddate.strftime('%Y-%m-%d')
    elif weekday == 6:
        endtime = time.strftime('%Y-%m-%d')
        begindate = time + datetime.timedelta(days=-6)
        begintime = begindate.strftime('%Y-%m-%d')
    else:
        begindate = time + datetime.timedelta(days=(0 - weekday))
        begintime = begindate.strftime('%Y-%m-%d')
        enddate = time + datetime.timedelta(days=(6 - weekday))
        endtime = enddate.strftime('%Y-%m-%d')

    time = begintime + '-' + endtime

    ctx = RequestContext(request)

    version = "0.1"

    list = []
    cursor = connection.cursor()
    sql = "select c.isp_code as isp_code, c.isp_cn_name as name, c.id from net_bw_bandwidth a, net_bw_room b, base_dict_isp c, base_dict_paytype d where a.time >= \'" + begintime + "\' and a.time <= \'" + endtime + "' and a.room_id = b.id and b.room_isp_id = c.id and d.id = b.room_pay_id and d.pay_code = '01' group by c.id"
    cursor.execute(sql)

    all_room = []
    for row in cursor.fetchall():
        sql = "select a.room_id from net_bw_bandwidth a, net_bw_room b, base_dict_isp c, base_dict_paytype d where a.time >= \'" + begintime + "\' and a.time <= \'" + endtime + "\' and a.room_id = b.id and b.room_isp_id = c.id and d.id = b.room_pay_id and d.pay_code = \'01\' and c.id = " + str(row[2])
        cursor.execute(sql)

        room_list = []
        for room in cursor.fetchall():
            room_list.append(room[0])

        comm_list = commUtil.getCommGroupBW(begintime, 7, room_list)
        all_room = all_room + room_list

        list.append({"isp_code": row[0], "name": row[1], "sum_max": comm_list[0]['sum_service_bw'], "sum_min": comm_list[0]['sum_paid_bw'], "sum_bw": comm_list[0]['sum_peak_value'],
                     "avg_rate": comm_list[0]['rate'], "isp_id": row[2]})

    cursor.close()

    if len(all_room) > 0:
        summary = commUtil.getCommGroupBW(begintime, 7, all_room)
        return_value = {'user': request.user, 'version': version, 'object_list': list, 'time': time,
                    'sum_max_BW': summary[0]['sum_service_bw'], 'sum_min_BW': summary[0]['sum_paid_bw'], 'sum_bw_value': summary[0]['sum_peak_value'],
                    'sum_rate': summary[0]['rate']}
    else:
        return_value = ""

    return render_to_response('net_bw/room_bw_week_isp.html', return_value, context_instance=ctx)

#isp月利用率
@login_required
def month_isp(request, id):
    now = datetime.datetime.now().strftime('%Y-%m')
    months = utils.getLastSixMonths()
    flag = 0
    try:
        id = int(id)
    except ValueError:
        raise Http404()

    if id == 1:
        time = now
        selectedMonth = now
        flag = 1

    if id == 2:
        time = request.POST['time']
        if time == '':
            time = now
            flag = 1
        selectedMonth = time

    ctx = RequestContext(request)

    version = "0.1"

    list = []
    cursor = connection.cursor()
    sql = "select c.isp_code as isp_code, c.isp_cn_name as name, c.id from net_bw_bandwidth a, net_bw_room b, base_dict_isp c , base_dict_paytype d where a.time like \'" + time + "%%\' and a.room_id = b.id and b.room_isp_id = c.id and d.id = b.room_pay_id and d.pay_code = '01'group by c.id"
    cursor.execute(sql)

    begintime = time + "-01"
    all_room = []
    for row in cursor.fetchall():
        sql = "select distinct(a.room_id) from net_bw_bandwidth a, net_bw_room b, base_dict_isp c , base_dict_paytype d where a.time like \'" + time + "%%\' and a.room_id = b.id and b.room_isp_id = c.id and d.id = b.room_pay_id and d.pay_code = \'01\' and c.id = " + str(row[2])
        cursor.execute(sql)

        room_list = []
        for room in cursor.fetchall():
            room_list.append(room[0])

        comm_list = commUtil.getCommGroupBW(begintime, utils.getDays(time), room_list)
        all_room = all_room + room_list
        list.append({"isp_code": row[0], "name": row[1], "sum_max":comm_list[0]['sum_service_bw'], "sum_min": comm_list[0]['sum_paid_bw'], "sum_bw": comm_list[0]['sum_peak_value'],
                     "avg_rate": comm_list[0]['rate'], "isp_id": row[2]})

    cursor.close()
    if len(all_room) > 0:
        summary = commUtil.getCommGroupBW(begintime, utils.getDays(time), all_room)
        return_value = {'selectedMonth':selectedMonth,'months':months,'user': request.user, 'version': version, 'object_list': list, 'time': time,
                    'sum_max_BW': summary[0]['sum_service_bw'], 'sum_min_BW': summary[0]['sum_paid_bw'], 'sum_bw_value': summary[0]['sum_peak_value'],
                    'sum_rate': summary[0]['rate']}
    else:
        return_value = ""

    return render_to_response('net_bw/room_bw_month_isp.html', return_value, context_instance=ctx)

#区域日利用率
@login_required
def day_area(request, id):
    now = (datetime.datetime.now() + + datetime.timedelta(days=-1)).strftime('%Y-%m-%d')
    try:
        id = int(id)
    except ValueError:
        raise Http404()

    if id == 1:
        time = now

    if id == 2:
        time = request.POST['time']
        if time == '':
            time = now

    ctx = RequestContext(request)

    version = "0.1"

    list = []
    cursor = connection.cursor()
    sql = "select sum(a.max_BW) as sum_max, sum(a.min_BW) as sum_min, sum(f.bw_value) as sum_bw, b.isp_cn_name, e.region_cn_name, b.id as isp_id, e.id as region_id from net_bw_bandwidth f, net_bw_room a, base_dict_isp b, geo_topology_city c, geo_topology_province d, geo_topology_region e, base_dict_paytype g where f.room_id = a.id and a.room_isp_id = b.id and a.room_city_id = c.id and c.parent_p_id = d.id and d.parent_rg_id = e.id and g.id = a.room_pay_id and g.pay_code = \'01\' and f.time = \'" + time + "\' group by e.id, b.id"
    cursor.execute(sql)

    for row in cursor.fetchall():
        min_BW = []
        bw_value = []
        sql = "select a.min_BW, f.bw_value from net_bw_bandwidth f, net_bw_room a, base_dict_isp b, geo_topology_city c, geo_topology_province d, geo_topology_region e, base_dict_paytype g where f.room_id = a.id and a.room_isp_id = b.id and a.room_city_id = c.id and c.parent_p_id = d.id and d.parent_rg_id = e.id and g.id = a.room_pay_id and g.pay_code = \'01\' and f.time = \'" + time +"\' and b.id = " + str(row[5]) + " and e.id = " + str(row[6])
        cursor.execute(sql)
        for bw in cursor.fetchall():
            min_BW.append(bw[0])
            bw_value.append(bw[1])

        rate = utils.getDailyRate(min_BW, bw_value)
        list.append({"sum_max": row[0], "sum_min": row[1], "sum_bw": row[2], "isp_cn_name": row[3], "region_cn_name": row[4], 'rate':rate})

    cursor.close()

    bandWidthDaily = BandWidthDaily.objects.all().filter(time=time)

    return_value = {'user': request.user, 'version': version, 'object_list': list, 'time': time,
                    'bandWidthDaily': bandWidthDaily}

    return render_to_response('net_bw/room_bw_day_area.html', return_value, context_instance=ctx)

#区域周利用率
@login_required
def week_area(request, id):
    begintime = ''
    endtime = ''
    try:
        id = int(id)
    except ValueError:
        raise Http404()

    if id == 1:
        time = datetime.datetime.now()

    if id == 2:
        strtime = request.POST['time']
        if strtime == '':
            time = datetime.datetime.now()
        else:
            time = datetime.datetime.strptime(strtime, '%Y-%m-%d')

    #monday:0 sunday:6
    #get weekday
    weekday = time.weekday()

    if weekday == 0:
        begintime = time.strftime('%Y-%m-%d')
        enddate = time + datetime.timedelta(days=6)
        endtime = enddate.strftime('%Y-%m-%d')
    elif weekday == 6:
        endtime = time.strftime('%Y-%m-%d')
        begindate = time + datetime.timedelta(days=-6)
        begintime = begindate.strftime('%Y-%m-%d')
    else:
        begindate = time + datetime.timedelta(days=(0 - weekday))
        begintime = begindate.strftime('%Y-%m-%d')
        enddate = time + datetime.timedelta(days=(6 - weekday))
        endtime = enddate.strftime('%Y-%m-%d')

    time = begintime + '-' + endtime

    ctx = RequestContext(request)

    version = "0.1"

    list = []
    cursor = connection.cursor()
    sql = "select b.isp_cn_name, e.region_cn_name, b.id as isp_id, e.id as region_id from net_bw_bandwidth f, net_bw_room a, base_dict_isp b, geo_topology_city c, geo_topology_province d, geo_topology_region e, base_dict_paytype g where f.room_id = a.id and a.room_isp_id = b.id and a.room_city_id = c.id and c.parent_p_id = d.id and d.parent_rg_id = e.id and g.id = a.room_pay_id and g.pay_code = \'01\' and f.time >= \'" + begintime + "\' and f.time <= \'" + endtime + "\' group by e.id, b.id"
    cursor.execute(sql)

    all_room = []
    for row in cursor.fetchall():
        sql = "select distinct(f.room_id) from net_bw_bandwidth f, net_bw_room a, base_dict_isp b, geo_topology_city c, geo_topology_province d, geo_topology_region e, base_dict_paytype g where f.room_id = a.id and a.room_isp_id = b.id and a.room_city_id = c.id and c.parent_p_id = d.id and d.parent_rg_id = e.id and g.id = a.room_pay_id and g.pay_code = \'01\' and f.time >= \'" + begintime + "\' and f.time <= \'" + endtime + "\' and e.id = " + str(row[3]) + " and b.id = " + str(row[2])
        cursor.execute(sql)

        room_list = []
        for room in cursor.fetchall():
            room_list.append(room[0])

        comm_list = commUtil.getCommGroupBW(begintime, 7, room_list)
        list.append({"max_BW": comm_list[0]['sum_service_bw'], "min_BW":comm_list[0]['sum_paid_bw'], "bw_value":comm_list[0]['sum_peak_value'], "rate": comm_list[0]['rate'], "isp_cn_name": row[0],
                     "region_cn_name": row[1], "isp_id": row[2], "region_id": row[3]})
        all_room = all_room + room_list

    cursor.close()
    if len(all_room) > 0:
        summary = commUtil.getCommGroupBW(begintime, 7, all_room)

        return_value = {'user': request.user, 'version': version, 'object_list': list, 'time': time,
                    'sum_max_BW': summary[0]['sum_service_bw'], 'sum_min_BW': summary[0]['sum_paid_bw'], 'sum_bw_value': summary[0]['sum_peak_value'],
                    'sum_rate': summary[0]['rate']}
    else:
        return_value = ""

    return render_to_response('net_bw/room_bw_week_area.html', return_value, context_instance=ctx)

#区域月利用率
@login_required
def month_area(request, id):
    now = datetime.datetime.now().strftime('%Y-%m')
    months = utils.getLastSixMonths()
    flag = 0
    try:
        id = int(id)
    except ValueError:
        raise Http404()

    if id == 1:
        time = now
        selectedMonth = now
        flag = 1

    if id == 2:
        time = request.POST['time']
        if time == '':
            time = now
            flag = 1
        selectedMonth = time

    ctx = RequestContext(request)

    version = "0.1"

    list = []
    cursor = connection.cursor()
    sql = "select b.isp_cn_name, e.region_cn_name, b.id as isp_id, e.id as region_id from net_bw_bandwidth f, net_bw_room a, base_dict_isp b, geo_topology_city c, geo_topology_province d, geo_topology_region e, base_dict_paytype g where f.room_id = a.id and a.room_isp_id = b.id and a.room_city_id = c.id and c.parent_p_id = d.id and d.parent_rg_id = e.id and g.id = a.room_pay_id and g.pay_code = \'01\' and f.time like \'" + time + "%%\' group by e.id, b.id"

    cursor.execute(sql)
    begintime = time + "-01"

    all_room = []
    for row in cursor.fetchall():
        room_list = []
        sql = "select distinct(f.room_id) from net_bw_bandwidth f, net_bw_room a, base_dict_isp b, geo_topology_city c, geo_topology_province d, geo_topology_region e, base_dict_paytype g where f.room_id = a.id and a.room_isp_id = b.id and a.room_city_id = c.id and c.parent_p_id = d.id and d.parent_rg_id = e.id and g.id = a.room_pay_id and g.pay_code = \'01\' and f.time like \'" + time +"%%\' and e.id = " + str(row[3]) + " and b.id = " + str(row[2])

        cursor.execute(sql)

        for room in cursor.fetchall():
            room_list.append(room[0])


        comm_list = commUtil.getCommGroupBW(begintime, utils.getDays(time), room_list)
        list.append({"max_BW": comm_list[0]['sum_service_bw'], "min_BW": comm_list[0]['sum_paid_bw'], "bw_value": comm_list[0]['sum_peak_value'], "rate": comm_list[0]['rate'], "isp_cn_name": row[0],
                     "region_cn_name": row[1], "isp_id": row[2], "region_id": row[3]})
        all_room = all_room + room_list

    cursor.close()
    if len(all_room) > 0:
        summary = commUtil.getCommGroupBW(begintime, utils.getDays(time), all_room)
        return_value = {'selectedMonth':selectedMonth,'months':months,'user': request.user, 'version': version, 'object_list': list, 'time': time,
                    'sum_max_BW': summary[0]['sum_service_bw'], 'sum_min_BW': summary[0]['sum_paid_bw'], 'sum_bw_value': summary[0]['sum_peak_value'],
                    'sum_rate': summary[0]['rate']}
    else:
        return_value = ""
    return render_to_response('net_bw/room_bw_month_area.html', return_value, context_instance=ctx)

#机房带宽添加
@login_required
def add(request, payId):
    yes = datetime.datetime.now() + datetime.timedelta(days=-1)
    now = yes.strftime('%Y-%m-%d')

    ctx = RequestContext(request)

    room = []
    for e in BandWidth.objects.filter(time=now):
        room.append(e.room_id)

    if int(payId) == 9:
        results = Room.objects.exclude(id__in=(room))
    else:
        p = PayType.objects.get(id=int(payId))
        results = Room.objects.exclude(id__in=(room)).filter(room_pay=p)

    roomTypeLst = PayType.objects.all()
    version = "0.1"
    returnValue = {'selectID': int(payId), 'user': request.user, 'version': version, 'object_list': results,
                   'roomTypeLst': roomTypeLst, 'total_bw': 0, 'vod_bw': 0, 'live_bw': 0, 'web_bw':0, 'today': now, 'flag': 0}
    return render_to_response('net_bw/room_bw_add.html', returnValue, context_instance=ctx)

#机房带宽修改
@login_required
def modify(request, id):
    now = datetime.datetime.now().strftime('%Y-%m-%d')
    try:
        id = int(id)
    except ValueError:
        raise Http404()
    ctx = RequestContext(request)
    bandwidth = BandWidth.objects.get(id=id)
    version = "0.1"
    return render_to_response('net_bw/room_bw_modify.html',
                              {'user': request.user, 'version': version, 'bandwidth': bandwidth, 'today':now},
                              context_instance=ctx)

#机房带宽删除
@login_required
def delete(request, id):
    try:
        id = int(id)
        BandWidth.objects.filter(id=id).delete()
    except ValueError:
        raise Http404()
    ctx = RequestContext(request)
    results_list = BandWidth.objects.all().order_by('-time')
    paginator = Paginator(results_list, PER_PAGE_COUNT)

    try:
        page = int(request.GET.get('page', '1'))
    except ValueError:
        page = 1

    try:
        results = paginator.page(page)
    except (EmptyPage, InvalidPage):
        results = paginator.page(paginator.num_pages)
    version = "0.1"
    pageUrl = "/net_bw/room_bw/1?"
    return render_to_response('net_bw/room_bw.html', {'user': request.user, 'version': version, 'results': results, 'pageUrl':pageUrl},
                              context_instance=ctx)

#机房带宽 修改 增加 提交
@login_required
def submit(request, id):
    decimal.getcontext().prec = 4
    errors = []
    time_add = []
    try:
        id = int(id)
    except ValueError:
        raise Http404()

    #add
    if id == 1:
        room_id_add = request.REQUEST.getlist('room_id')
        vod_bw_add = request.REQUEST.getlist('vod_bw')
        live_bw_add = request.REQUEST.getlist('live_bw')
        web_bw_add = request.REQUEST.getlist('web_bw')
        total_bw_add = request.REQUEST.getlist('total_bw')
        time = request.POST['time']

        if not errors:
            i = 0
            for i in range(len(room_id_add)):
                if len(BandWidth.objects.filter(room=Room.objects.get(id=string.atoi(room_id_add[i])),
                                                time=time)) > 1:
                    room = Room.objects.get(id=string.atoi(room_id_add[i]))
                    errors.append('机房' + room.room_code + '-' + room.room_cn_name + '-' + time + '已经输入过，请修改!')
                else:
                    rate = decimal.Decimal(string.atof(total_bw_add[i])) / decimal.Decimal(
                        Room.objects.get(id=string.atoi(room_id_add[i])).min_BW)

                    if rate >= 1:
                        rate = 1

                    bw = BandWidth(room=Room.objects.get(id=string.atoi(room_id_add[i])), time=time,
                                   vod_bw_value=string.atof(vod_bw_add[i]), live_bw_value=string.atof(live_bw_add[i]), web_bw_value=string.atof(web_bw_add[i]), 
                                   bw_value=string.atof(total_bw_add[i]), max_BW=Room.objects.get(id=string.atoi(room_id_add[i])).max_BW, min_BW=Room.objects.get(id=string.atoi(room_id_add[i])).min_BW, rate=rate)
                    bw.save()

            utils.bandWidthDaily(list(set(time_add)))

            ctx = RequestContext(request)
            results_list = BandWidth.objects.all().order_by('-time')
            paginator = Paginator(results_list, PER_PAGE_COUNT)
            version = "0.1"

            try:
                page = int(request.GET.get('page', '1'))
            except ValueError:
                page = 1

            try:
                results = paginator.page(page)
            except (EmptyPage, InvalidPage):
                results = paginator.page(paginator.num_pages)
            pageUrl = "/net_bw/room_bw/1?"
            return render_to_response('net_bw/room_bw.html',
                                      {'user': request.user, 'version': version, 'results': results, 'pageUrl':pageUrl},
                                      context_instance=ctx)

    #modify    
    if id == 2:
        version = "0.1"
        bw_id = request.POST['bw_id']
        vod_bw = request.POST['vod_bw']
        live_bw = request.POST['live_bw']
        web_bw = request.POST['web_bw']
        bw_value = request.POST['bw_value']
        time = request.POST['time']

        rate = decimal.Decimal(string.atof(bw_value)) / decimal.Decimal(
            Room.objects.get(id=BandWidth.objects.get(id=bw_id).room_id).min_BW)

        if rate >= 1:
            rate = 1

        BandWidth.objects.filter(id=bw_id).update(vod_bw_value=string.atof(vod_bw),
                                                  live_bw_value=string.atof(live_bw), web_bw_value=string.atof(web_bw), bw_value=string.atof(bw_value)
                                                  , time=time, rate=rate)

        ctx = RequestContext(request)

        results_list = BandWidth.objects.all().order_by('-time')
        paginator = Paginator(results_list, PER_PAGE_COUNT)

        try:
            page = int(request.GET.get('page', '1'))
        except ValueError:
            page = 1

        try:
            results = paginator.page(page)
        except (EmptyPage, InvalidPage):
            results = paginator.page(paginator.num_pages)
        pageUrl = "/net_bw/room_bw/1?"
        return render_to_response('net_bw/room_bw.html',
                                  {'user': request.user, 'version': version, 'results': results, 'pageUrl':pageUrl},
                                  context_instance=ctx)

#机房周利用率曲线图
@login_required
def week_chart(request, id, time):
    beginTime = time[0:10]
    endTime = time[11:21]
    list = []
    rateYAxisName = 'Rate'
    bwYAxisName = 'BandWidth'
    subcaption = time

    ctx = RequestContext(request)

    version = "0.1"

    cursor = connection.cursor()

    if id != '0':
        name = ''
        sql = "select a.time as time,a.bw_value as bw, a.rate as rate, b.room_cn_name as name from net_bw_bandwidth a, net_bw_room b where a.room_id = b.id and a.room_id = " + str(
            id) + " and a.time >= \'" + beginTime + "\' and a.time <= \'" + endTime + "\' order by a.time"
        cursor.execute(sql)
        for row in cursor.fetchall():
            list.append({'time':row[0],'bw':row[1],'rate':row[2]*100})
            name = row[3]

        cursor.close()

        rateCaption = '周利用率曲线图'
        bwCaption = '周峰值带宽曲线图'

        title = name + u"带宽周利用率曲线图"
    else:
        for i in range(7):
            time = (datetime.date(int(beginTime[0:4]),int(beginTime[5:7]),int(beginTime[8:10])) + datetime.timedelta(days=i)).strftime('%Y-%m-%d')
            sql = "select room_id from net_bw_bandwidth where time = \'" + time +"\'"
            cursor.execute(sql)
            room_list = []
            for row in cursor.fetchall():
                room_list.append(row[0])
            comm_list = commUtil.getCommGroupBW(time, 0, room_list)
            list.append({'time':time,'bw':comm_list[0]['sum_peak_value'],'rate':comm_list[0]['rate']*100})

        cursor.close()

        rateCaption = '全网周利用率曲线图'
        bwCaption = '全网周峰值带宽曲线图'
        title = "全网带宽利用率曲线图"

    lstTime = list[len(list)-1]['time']
    d1 = datetime.date(int(lstTime[0:4]),int(lstTime[5:7]),int(lstTime[8:10]))
    d2 = datetime.date(int(endTime[0:4]),int(endTime[5:7]),int(endTime[8:10]))
    for i in range((d2-d1).days):
        timeLst = lstTime[0:8] + str(int(lstTime[8:10]) + i + 1)
        list.append({'time':timeLst,'bw':0,'rate':0})

    return_value = {'user': request.user, 'version': version, 'name': title, 'object_list': list, 'rateYAxisName': rateYAxisName,'bwYAxisName':bwYAxisName,'subcaption':subcaption,'rateCaption':rateCaption,'bwCaption':bwCaption}
    return render_to_response('net_bw/room_bw_fcf_chart.html', return_value, context_instance=ctx)

#ISP周利用率曲线图
@login_required
def isp_week_chart(request, id, time):
    beginTime = time[0:10]
    endTime = time[11:21]

    ctx = RequestContext(request)

    version = "0.1"

    listChart = utils.getDayISPChart(id, beginTime, endTime)

    cursor = connection.cursor()
    list = []
    rateYAxisName = 'Rate'
    bwYAxisName = 'BandWidth'
    subcaption = time
    name = ''
    sql = "select c.isp_cn_name, max(a.bw_value), max(a.rate), a.time from net_bw_bandwidth a, net_bw_room b, base_dict_isp c, base_dict_paytype d where a.time >= \'" + beginTime + "\' and a.time <= \'" + endTime + "\' and a.room_id = b.id and b.room_isp_id = c.id and d.id = b.room_pay_id and d.pay_code = '01' and c.id = " + id + " group by a.time"
    cursor.execute(sql)
    for row in cursor.fetchall():
        list.append({'time':row[3],'bw':row[1],'rate':row[2]*100})
        name = row[0]

    cursor.close()

    lstTime = list[len(list)-1]['time']
    d1 = datetime.date(int(lstTime[0:4]),int(lstTime[5:7]),int(lstTime[8:10]))
    d2 = datetime.date(int(endTime[0:4]),int(endTime[5:7]),int(endTime[8:10]))
    for i in range((d2-d1).days):
        timeLst = lstTime[0:8] + str(int(lstTime[8:10]) + i + 1)
        list.append({'time':timeLst,'bw':0,'rate':0})

    rateCaption = '周利用率曲线图'
    bwCaption = '周峰值带宽曲线图'

    title = name + u"带宽周利用率曲线图"

    return_value = {'user': request.user, 'version': version, 'name': title, 'object_list': listChart, 'rateYAxisName': rateYAxisName,'bwYAxisName':bwYAxisName,'subcaption':subcaption,'rateCaption':rateCaption,'bwCaption':bwCaption}
    return render_to_response('net_bw/room_bw_fcf_chart.html', return_value, context_instance=ctx)

#区域周利用率曲线图
@login_required
def area_week_chart(request, isp_id, region_id, time):
    beginTime = time[0:10]
    endTime = time[11:21]

    ctx = RequestContext(request)

    version = "0.1"

    cursor = connection.cursor()
    list = []
    rateYAxisName = 'Rate'
    bwYAxisName = 'BandWidth'
    subcaption = time
    name = ''
    sql = "select max(f.bw_value) as sum_bw, avg(f.rate) as rate, b.isp_cn_name, e.region_cn_name, f.time from net_bw_bandwidth f, net_bw_room a, base_dict_isp b, geo_topology_city c, geo_topology_province d, geo_topology_region e, base_dict_paytype g where f.room_id = a.id and a.room_isp_id = b.id and a.room_city_id = c.id and c.parent_p_id = d.id and d.parent_rg_id = e.id and g.id = a.room_pay_id and g.pay_code = \'01\' and f.time >= \'" + beginTime + "\' and f.time <= \'" + endTime + "\' and b.id = " + isp_id + " and e.id = " + region_id + " group by e.id, b.id, f.time"
    cursor.execute(sql)
    for row in cursor.fetchall():
        list.append({'time':row[4],'bw':row[0],'rate':row[1]*100})
        name = row[3] + row[2]

    cursor.close()

    lstTime = list[len(list)-1]['time']
    d1 = datetime.date(int(lstTime[0:4]),int(lstTime[5:7]),int(lstTime[8:10]))
    d2 = datetime.date(int(endTime[0:4]),int(endTime[5:7]),int(endTime[8:10]))
    for i in range((d2-d1).days):
        timeLst = lstTime[0:8] + str(int(lstTime[8:10]) + i + 1)
        list.append({'time':timeLst,'bw':0,'rate':0})

    rateCaption = '周利用率曲线图'
    bwCaption = '周峰值带宽曲线图'

    title = name + u"带宽周利用率曲线图"

    return_value = {'user': request.user, 'version': version, 'name': title, 'object_list': list, 'rateYAxisName': rateYAxisName,'bwYAxisName':bwYAxisName,'subcaption':subcaption,'rateCaption':rateCaption,'bwCaption':bwCaption}
    return render_to_response('net_bw/room_bw_fcf_chart.html', return_value, context_instance=ctx)

#区域月利用率曲线图
@login_required
def area_month_chart(request, isp_id, region_id, time):
    beginTime = time[0:10]
    endTime = time[11:21]

    ctx = RequestContext(request)

    version = "0.1"

    cursor = connection.cursor()
    list = []
    rateYAxisName = 'Rate'
    bwYAxisName = 'BandWidth'
    subcaption = time
    name = ''
    sql = "select max(f.bw_value) as sum_bw, avg(f.rate) as rate, b.isp_cn_name, e.region_cn_name, f.time from net_bw_bandwidth f, net_bw_room a, base_dict_isp b, geo_topology_city c, geo_topology_province d, geo_topology_region e, base_dict_paytype g where f.room_id = a.id and a.room_isp_id = b.id and a.room_city_id = c.id and c.parent_p_id = d.id and d.parent_rg_id = e.id and g.id = a.room_pay_id and g.pay_code = \'01\' and f.time like \'" + time + "%%\' and b.id = " + isp_id + " and e.id = " + region_id + " group by e.id, b.id, f.time"
    cursor.execute(sql)
    for row in cursor.fetchall():
        list.append({'time':row[4],'bw':row[0],'rate':row[1]*100})
        name = row[3] + row[2]

    cursor.close()

    days = utils.getDays(time)
    lenLst = len(list)
    today = list[len(list)-1]['time']
    if days != lenLst:
        for i in range(days - lenLst):
            timeLst = today[0:7] + '-' + str(int(today[8:10]) + i + 1)
            list.append({'time':timeLst,'bw':0,'rate':0})

    rateCaption = '月利用率曲线图'
    bwCaption = '月峰值带宽曲线图'
    title = name + u"带宽月利用率曲线图"

    return_value = {'user': request.user, 'version': version, 'name': title, 'object_list': list, 'rateYAxisName': rateYAxisName,'bwYAxisName':bwYAxisName,'subcaption':subcaption,'rateCaption':rateCaption,'bwCaption':bwCaption}
    return render_to_response('net_bw/room_bw_fcf_chart.html', return_value, context_instance=ctx)


#机房月利用率曲线图
@login_required
def month_chart(request, id, time):
    ctx = RequestContext(request)

    version = "0.1"
    cursor = connection.cursor()

    list = []
    rateYAxisName = 'Rate'
    bwYAxisName = 'BandWidth'
    subcaption = time
    name = ''
    timeSql = ""
    days = 0
    if len(time) == 7:
        timeSql = " and a.time like \'" + time + "%%\'"
        days = utils.getDays(time)
    else:
        timeSql = " and a.time >= \'" + time[0:10] + "\' and a.time <= \'" + time[11:22] + "\'"
        d1 = datetime.date(int(time[0:10][0:4]),int(time[0:10][5:7]),int(time[0:10][8:10]))
        d2 = datetime.date(int(time[11:22][0:4]),int(time[11:22][5:7]),int(time[11:22][8:10]))
        days = (d2-d1).days + 1

    sql = "select a.time as time,a.bw_value as bw, a.rate as rate, b.room_cn_name as name, b.max_BW, b.min_BW from net_bw_bandwidth a, net_bw_room b where a.room_id = b.id and a.room_id = " + str(
        id) + timeSql + " order by a.time"
    cursor.execute(sql)
    max_BW = 0
    min_BW = 0
    for row in cursor.fetchall():
        list.append({'time':row[0],'bw':row[1],'rate':row[2]*100})
        name = row[3]
        max_BW = row[4]
        min_BW = row[5]

    cursor.close()

    lenLst = len(list)
    today = list[len(list)-1]['time']
    if days != lenLst:
        for i in range(days - lenLst):
            timeLst = today[0:7] + '-' + str(int(today[8:10]) + i + 1)
            list.append({'time':timeLst,'bw':0,'rate':0})

    rateCaption = '月利用率曲线图'
    bwCaption = '月峰值带宽曲线图'
    title = name + u"带宽月利用率曲线图 最大服务带宽：" + str(max_BW) + u" 付费带宽：" + str(min_BW)

    return_value = {'user': request.user, 'version': version, 'name': title, 'object_list': list, 'rateYAxisName': rateYAxisName,'bwYAxisName':bwYAxisName,'subcaption':subcaption,'rateCaption':rateCaption,'bwCaption':bwCaption}
    return render_to_response('net_bw/room_bw_fcf_chart.html', return_value, context_instance=ctx)

#ISP月利用率曲线图
@login_required
def isp_month_chart(request, id, time):
    ctx = RequestContext(request)

    version = "0.1"
    cursor = connection.cursor()

    list = []
    rateYAxisName = 'Rate'
    bwYAxisName = 'BandWidth'
    subcaption = time
    name = ''
    sql = "select c.isp_cn_name, max(a.bw_value), max(a.rate),a.time from net_bw_bandwidth a, net_bw_room b, base_dict_isp c, base_dict_paytype d where a.time like \'" + time + "%%\' and a.room_id = b.id and b.room_isp_id = c.id and d.id = b.room_pay_id and d.pay_code = '01' and c.id = " + id + " group by a.time"
    cursor.execute(sql)
    for row in cursor.fetchall():
        list.append({'time':row[3],'bw':row[1],'rate':row[2]*100})
        name = row[0]
    cursor.close()

    days = utils.getDays(time)
    lenLst = len(list)
    today = list[len(list)-1]['time']
    if days != lenLst:
        for i in range(days - lenLst):
            timeLst = today[0:7] + '-' + str(int(today[8:10]) + i + 1)
            list.append({'time':timeLst,'bw':0,'rate':0})

    rateCaption = '月利用率曲线图'
    bwCaption = '月峰值带宽曲线图'
    title = name + u"带宽月利用率曲线图"

    begintime = time + "-01"
    endtime = time + "-" + str(utils.getDays(time))

    listChart = utils.getDayISPChart(id, begintime, endtime)

    return_value = {'user': request.user, 'version': version, 'name': title, 'object_list': listChart, 'rateYAxisName': rateYAxisName,'bwYAxisName':bwYAxisName,'subcaption':subcaption,'rateCaption':rateCaption,'bwCaption':bwCaption}
    return render_to_response('net_bw/room_bw_fcf_chart.html', return_value, context_instance=ctx)

#带宽周利用率
@login_required
def bw_report(request, id):
    begintime = ''
    endtime = ''
    selectID = 0

    time = datetime.datetime.now()

    queryLst = []
    queryLst.append({"id":10,"name":"最近三天"})
    queryLst.append({"id":11,"name":"最近一周"})
    queryLst.append({"id":12,"name":"最近一月"})
    
    try:
        id = int(id)
    except ValueError:
        raise Http404()

    if id == 1 or id == 11:
        #monday:0 sunday:6
        #get weekday
        begindate = time + datetime.timedelta(days=-7)
        begintime = begindate.strftime('%Y-%m-%d')
        enddate = time + datetime.timedelta(days=-1)
        endtime = enddate.strftime('%Y-%m-%d')

        if id == 11:
            selectID = 11

    if id == 2:
         begintime = request.POST['beginTime']
         endtime = request.POST['endTime']

    if id == 10:
        begindate = time + datetime.timedelta(days=-3)
        begintime = begindate.strftime('%Y-%m-%d')
        enddate = time + datetime.timedelta(days=-1)
        endtime = enddate.strftime('%Y-%m-%d')
        selectID = 10

    if id == 12:
        begindate = time + datetime.timedelta(days=-30)
        begintime = begindate.strftime('%Y-%m-%d')
        enddate = time + datetime.timedelta(days=-1)
        endtime = enddate.strftime('%Y-%m-%d')
        selectID = 12

    time = begintime + '-' + endtime

    ctx = RequestContext(request)

    version = "0.1"

    list = []
    cursor = connection.cursor()
    sql = "select b.room_code as code, b.room_cn_name as name, c.pay_cn_name as pay_name, max(a.bw_value) as valueBW, b.id as id from net_bw_bandwidth a, net_bw_room b, base_dict_paytype c where a.room_id = b.id and b.room_pay_id = c.id and a.time >= \'" + begintime + "\' and a.time <= \'" + endtime + "' group by b.room_code"
    cursor.execute(sql)

    for row in cursor.fetchall():
        list.append({"code": row[0], "name": row[1], "pay_name": row[2], "valueBW": row[3], "id":row[4]})

    cursor.close()

    return_value = {'user': request.user, 'version': version, 'object_list': list, 'time': time, 'beginTime':begintime, 'endTime':endtime, 'typeLst':queryLst, 'selectID':selectID}

    return render_to_response('net_bw/room_bw_report.html', return_value, context_instance=ctx)

#带宽曲线图
@login_required
def bw_chart(request, id, time):
    beginTime = time[0:10]
    endTime = time[11:21]

    ctx = RequestContext(request)

    version = "0.1"

    cursor = connection.cursor()

    list = []
    bwYAxisName = 'BandWidth'
    subcaption = time
    name = ''
    sql = "select a.time,a.bw_value,b.room_cn_name, c.pay_cn_name from net_bw_bandwidth a, net_bw_room b, base_dict_paytype c where a.room_id = b.id and b.room_pay_id = c.id and a.time >= \'" + beginTime + "\' and a.time <= \'" + endTime + "\' and b.id  = " + id + " order by a.time"
    cursor.execute(sql)
    for row in cursor.fetchall():
        list.append({'time':row[0], 'bw':row[1]})
        name = row[2] + '|' + row[3] + '|'

    cursor.close()

    lstTime = list[len(list)-1]['time']
    d1 = datetime.date(int(lstTime[0:4]),int(lstTime[5:7]),int(lstTime[8:10]))
    d2 = datetime.date(int(endTime[0:4]),int(endTime[5:7]),int(endTime[8:10]))
    for i in range((d2-d1).days):
        timeLst = lstTime[0:8] + str(int(lstTime[8:10]) + i + 1)
        list.append({'time':timeLst,'bw':0})

    bwCaption = '峰值带宽曲线图'
    title = name + u"峰值带宽曲线图"

    return_value = {'user': request.user, 'version': version, 'name': title, 'object_list': list, 'bwYAxisName':bwYAxisName,'subcaption':subcaption,'bwCaption':bwCaption}
    return render_to_response('net_bw/room_bw_chart.html', return_value, context_instance=ctx)
