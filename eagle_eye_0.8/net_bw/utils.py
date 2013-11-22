# -*- coding:utf-8 -*-
import datetime
import decimal
from django.db import connection
from net_bw.models import BandWidthDaily

# 计算全网日利用率
def bandWidthDaily(time):
    cursor = connection.cursor()
    i = 0
    max_BW = []
    min_BW = []
    bw_value = []
    for i in range(len(time)):
        # 计算全网日利用率
        sql = "select b.max_BW, b.min_BW, a.bw_value from net_bw_bandwidth a, net_bw_room b, base_dict_paytype c where a.room_id = b.id and c.id = b.room_pay_id and a.time = \'" + time[i] + "\' and c.pay_code = \'01\'"
        cursor.execute(sql)
        for row in cursor.fetchall():
           max_BW.append(row[0])
           min_BW.append(row[1])
           bw_value.append(row[2])

        bandWidthDaily = BandWidthDaily(max_BW=getDailyMaxBW(max_BW), min_BW=getDailyMinBW(min_BW), bw_value=getDailyBWValue(bw_value), rate=getDailyRate(min_BW, bw_value), time=time[i])
        bandWidthDaily.save()

    cursor.close()

# 求和日最大服务带宽
def getDailyMaxBW(max_BW):
    i = 0
    sum = 0
    for i in range(len(max_BW)):
        sum = sum + max_BW[i]

    print 'getDailyMaxBW=' + str(sum)
    return sum

# 求和日付费带宽
def getDailyMinBW(min_BW):
    i = 0
    sum = 0
    for i in range(len(min_BW)):
        sum = sum + min_BW[i]

    print 'getDailyMinBW=' + str(sum)
    return sum

# 求和日峰值带宽
def getDailyBWValue(bw_value):
    i = 0
    sum = 0
    for i in range(len(bw_value)):
        sum = sum + bw_value[i]

    print 'getDailyBWValue=' + str(sum)
    return sum

# 求日利用率
def getDailyRate(min_BW, bw_value):
    decimal.getcontext().prec = 4
    i = 0
    # 分子
    zaehler = 0
    # 分母
    nenner = 0

    if len(bw_value) < 1:
        rate = 0
    else:
        for i in range(len(bw_value)):
            if min_BW[i] >= bw_value[i]:
                zaehler = zaehler + bw_value[i]
                nenner = nenner + min_BW[i]
            else:
                zaehler = zaehler + bw_value[i]
                nenner = nenner + bw_value[i]

        rate = decimal.Decimal(zaehler)/decimal.Decimal(nenner)
    
    return rate

#取最近的6个月
def getLastSixMonths():
    months = []
    date = datetime.datetime.now()
    for i in range(6):
        new_year = date.year + (date.month - i)/12
        new_month = (date.month - i)%12
        if new_month < 10:
            months.append({'month': str(new_year) + '-0' + str(new_month)})
        else:
            months.append({'month': str(new_year) + '-' + str(new_month)})

    return months

#根据年月取天数
def getDays(time):
    year = int(time[0:4])
    month = int(time[5:7])
    days = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31]
    if (2 == month and 0 == (year%4)) and (0 != (year%100) or 0 == (year%400)):
        days[1] = 29

    return days[month - 1]

def getMax(rate):
    if len(rate) > 1:
        tmp = rate[0]
        for i in range(len(rate)):
            if rate[i] > tmp:
                tmp = rate[i]
    else:
        tmp = 0
    return tmp


#计算每天ISP利用率
def getDayISP(isp_code, beginTime, endTime):
    d1 = datetime.date(int(beginTime[0:4]),int(beginTime[5:7]),int(beginTime[8:10]))
    d2 = datetime.date(int(endTime[0:4]),int(endTime[5:7]),int(endTime[8:10]))
    list = []
    rate = []
    for i in range(((d2-d1).days) + 1):
        time = (datetime.date(int(beginTime[0:4]),int(beginTime[5:7]),int(beginTime[8:10])) + datetime.timedelta(days=i)).strftime('%Y-%m-%d')
        print time
        min_BW = []
        bw_value = []
        max_BW = []
        cursor = connection.cursor()
        sql = "select max(b.min_BW) as min_BW, max(a.bw_value) as bw_value, max(b.max_BW) from net_bw_bandwidth a, net_bw_room b, base_dict_isp c, base_dict_paytype d where a.time = \'" + time + "\' and a.room_id = b.id and b.room_isp_id = c.id and d.id = b.room_pay_id and d.pay_code = \'01\' and c.isp_code = \'" + isp_code +  "\' group by b.id"
        cursor.execute(sql)
        for bw in cursor.fetchall():
             min_BW.append(bw[0])
             bw_value.append(bw[1])
             max_BW.append(bw[2])

        rate.append(getDailyRate(min_BW, bw_value))

    print min_BW
    print bw_value
    print max_BW
    list.append({"max_BW":getDailyMaxBW(max_BW),"min_BW":getDailyMinBW(min_BW), "bw_value":getDailyBWValue(bw_value),"rate":getDailyRate(min_BW, bw_value)})
    print list
    cursor.close()
    return list

#计算每天ISP利用率
def getDayISPChart(isp_id, beginTime, endTime):
    d1 = datetime.date(int(beginTime[0:4]),int(beginTime[5:7]),int(beginTime[8:10]))
    d2 = datetime.date(int(endTime[0:4]),int(endTime[5:7]),int(endTime[8:10]))
    list = []
    for i in range(((d2-d1).days) + 1):
        time = (datetime.date(int(beginTime[0:4]),int(beginTime[5:7]),int(beginTime[8:10])) + datetime.timedelta(days=i)).strftime('%Y-%m-%d')
        print time
        min_BW = []
        bw_value = []
        cursor = connection.cursor()
        sql = "select b.min_BW as min_BW, a.bw_value as bw_value from net_bw_bandwidth a, net_bw_room b, base_dict_isp c, base_dict_paytype d where a.time = \'" + time + "\' and a.room_id = b.id and b.room_isp_id = c.id and d.id = b.room_pay_id and d.pay_code = \'01\' and c.id = \'" + isp_id +  "\'"
        cursor.execute(sql)
        for bw in cursor.fetchall():
             min_BW.append(bw[0])
             bw_value.append(bw[1])

        list.append({"rate":getDailyRate(min_BW, bw_value), "bw":getMax(bw_value), "time":time})

    cursor.close()
    print list    
    return list

#计算每天区域+ISP利用率
def getDayArea(isp_id, region_id, beginTime, endTime):
    d1 = datetime.date(int(beginTime[0:4]),int(beginTime[5:7]),int(beginTime[8:10]))
    d2 = datetime.date(int(endTime[0:4]),int(endTime[5:7]),int(endTime[8:10]))
    rate = []
    for i in range(((d2-d1).days) + 1):
        time = (datetime.date(int(beginTime[0:4]),int(beginTime[5:7]),int(beginTime[8:10])) + datetime.timedelta(days=i)).strftime('%Y-%m-%d')
        print time
        min_BW = []
        bw_value = []
        cursor = connection.cursor()
        sql = "select a.min_BW, f.bw_value from net_bw_bandwidth f, net_bw_room a, base_dict_isp b, geo_topology_city c, geo_topology_province d, geo_topology_region e, base_dict_paytype g where f.room_id = a.id and a.room_isp_id = b.id and a.room_city_id = c.id and c.parent_p_id = d.id and d.parent_rg_id = e.id and g.id = a.room_pay_id and g.pay_code = \'01\' and f.time = \'" + time + "\' and b.id = " + str(isp_id) + " and e.id = " + str(region_id)
        cursor.execute(sql)
        for bw in cursor.fetchall():
             min_BW.append(bw[0])
             bw_value.append(bw[1])

        rate.append(getDailyRate(min_BW, bw_value))

    cursor.close()
    print rate
    return getMax(rate)

#计算每天区域+ISP利用率
def getDayAreaChart(isp_id, region_id, beginTime, endTime):
    d1 = datetime.date(int(beginTime[0:4]),int(beginTime[5:7]),int(beginTime[8:10]))
    d2 = datetime.date(int(endTime[0:4]),int(endTime[5:7]),int(endTime[8:10]))
    list = []
    for i in range(((d2-d1).days) + 1):
        time = (datetime.date(int(beginTime[0:4]),int(beginTime[5:7]),int(beginTime[8:10])) + datetime.timedelta(days=i)).strftime('%Y-%m-%d')
        print time
        min_BW = []
        bw_value = []
        cursor = connection.cursor()
        sql = "select a.min_BW, f.bw_value from net_bw_bandwidth f, net_bw_room a, base_dict_isp b, geo_topology_city c, geo_topology_province d, geo_topology_region e, base_dict_paytype g where f.room_id = a.id and a.room_isp_id = b.id and a.room_city_id = c.id and c.parent_p_id = d.id and d.parent_rg_id = e.id and g.id = a.room_pay_id and g.pay_code = \'01\' and f.time = \'" + time + "\' and b.id = " + str(isp_id) + " and e.id = " + str(region_id)
        cursor.execute(sql)
        for bw in cursor.fetchall():
             min_BW.append(bw[0])
             bw_value.append(bw[1])

        list.append({"rate":getDailyRate(min_BW, bw_value), "bw":getMax(bw_value), "time":time})

    cursor.close()
    print list
    return list

#取得全网周利用率
def getWeekSummaryData(beginTime, endTime):

    cursor = connection.cursor()
    i = 0
    max_BW = []
    min_BW = []
    bw_value = []
    list = []
    # 计算全网利用率
    sql = "select max(b.max_BW), max(b.min_BW), max(a.bw_value) from net_bw_bandwidth a, net_bw_room b, base_dict_paytype c where a.room_id = b.id and c.id = b.room_pay_id and a.time >= \'" + beginTime + "\' and a.time <= \'" + endTime + "\' and c.pay_code = \'01\' group by b.id"
    cursor.execute(sql)
    for row in cursor.fetchall():
       max_BW.append(row[0])
       min_BW.append(row[1])
       bw_value.append(row[2])

    list.append({"max_BW":getDailyMaxBW(max_BW),"min_BW":getDailyMinBW(min_BW), "bw_value":getDailyBWValue(bw_value),"rate":getDailyRate(min_BW, bw_value)})
    cursor.close()

    return list

#取得全网月利用率
def getMonthSummaryData(time):
    cursor = connection.cursor()
    i = 0
    max_BW = []
    min_BW = []
    bw_value = []
    list = []
    # 计算全网利用率
    sql = "select max(b.max_BW), max(b.min_BW), max(a.bw_value) from net_bw_bandwidth a, net_bw_room b, base_dict_paytype c where a.room_id = b.id and c.id = b.room_pay_id and a.time like \'" + time + "%%\' and c.pay_code = \'01\' group by b.id"
    cursor.execute(sql)
    for row in cursor.fetchall():
       max_BW.append(row[0])
       min_BW.append(row[1])
       bw_value.append(row[2])

    list.append({"max_BW":getDailyMaxBW(max_BW),"min_BW":getDailyMinBW(min_BW), "bw_value":getDailyBWValue(bw_value),"rate":getDailyRate(min_BW, bw_value)})
    cursor.close()
    return list

#付费带宽展示
#min(付费带宽与峰值带宽的最大值，最大服务带宽)
def getPaidBW(service_bw, paid_bw, peak_bw):
    max = paid_bw
    min = service_bw
    if max < peak_bw:
        max = peak_bw
    if min < max:
        min = max
    return min





    
        

