# -*- coding:utf-8 -*-
import datetime
import decimal
from sets import Set
from django.db import connection

#list转换成str
#如 [1,2,3,4]转换后(1,2,3,4)
def list2String(list):
    string = "("
    for i in range(len(list)):
        if i == len(list) - 1:
            string = string + str(list[i])
        else:
            string = string + str(list[i]) + ","

    return string + ")"

# 取最大值
def getMaxValue(value):
    if len(value) > 0:
        tmp = value[0]
        for i in range(len(value)):
            if value[i] > tmp:
                tmp = value[i]
    else:
        tmp = 0
    return tmp

# 取带宽合计
def getSumBW(value, room_set, room_id):
    sum = 0
    if len(room_set) > 0:
        for i in range(len(room_set)):
            group_bw = []
            for j in range(len(room_id)):
                if room_set[i] == room_id[j]:
                   group_bw.append(value[j])
            sum = sum + getMaxValue(group_bw)
            
    return sum

# 取付费带宽合计
def getSumPaidBW(peak_value, paid_bw, room_set, room_id):
    sum = 0
    if len(room_set) > 0:
        for i in range(len(room_set)):
            group_bw = []
            for j in range(len(room_id)):
                if room_set[i] == room_id[j]:
                   group_bw.append(peak_value[j])
                   group_bw.append(paid_bw[j])
            print group_bw
            sum = sum + getMaxValue(group_bw)
    return sum


# 通用分组带宽数据统计
#
# 按照天统计：beginTime为统计日，days=0，roomList=统计的机房ID列表
# 按照周统计：beginTime为周的第一天，days=7，roomList=统计的机房ID列表
# 按照月统计：beginTime为月的第一天，days=统计月的天数，roomList=统计的机房ID列表
#
# 入参:
#   beginTime 开始日期
#   days 天数
#   roomList 机房lst
# 返回参数：
#   list列表:最大服务带宽之和 sum_service_bw,付费带宽之和 sum_paid_bw,峰值之和 sum_peak_value,利用率 rate
#
def getCommGroupBW(beginTime, days, roomList):
    #小数位精度 4位
    decimal.getcontext().prec = 4
    #返回list
    returnList = []
    #拼装sql where条件
    if days == 0:
        timeSQL = " and time = '" + beginTime + "'"
    else:
        endTime = (datetime.date(int(beginTime[0:4]),int(beginTime[5:7]),int(beginTime[8:10])) + datetime.timedelta(days=(days - 1))).strftime('%Y-%m-%d')
        timeSQL = " and time >= '" + beginTime + "' and time <= '" + endTime + "'"

    if len(roomList) > 0:
        roomSQL = "room_id in " + list2String(roomList)
    else:
        roomSQL = "room_id = 0"

    cursor = connection.cursor()
    # 取得机房期间内的带宽,现在机房为97个，以后分开查询************************
    sql = "select bw_value, max_BW, min_BW, room_id from net_bw_bandwidth where " + roomSQL + timeSQL + " order by room_id"
    print sql
    cursor.execute(sql)
    #峰值
    peak_value = []
    #最大服务带宽
    service_bw = []
    #付费带宽
    paid_bw = []
    #机房id
    room_id = []
    for row in cursor.fetchall():
        peak_value.append(row[0])
        service_bw.append(row[1])
        paid_bw.append(row[2])
        room_id.append(row[3])
    cursor.close()

    print peak_value
    #过滤room_id
    room_set = list(Set(room_id))
    print room_set
    #机房最大服务带宽之和
    sum_service_bw = getSumBW(service_bw, room_set, room_id)
    #取机房峰值带宽(分子)
    sum_peak_value = getSumBW(peak_value, room_set, room_id)
    #取机房付费带宽(分母)
    sum_paid_bw = getSumPaidBW(peak_value, paid_bw, room_set, room_id)

    print 'sum_peak_value=' + str(sum_peak_value)
    print 'sum_service_bw=' + str(sum_service_bw)
    print 'sum_paid_bw=' + str(sum_paid_bw)
    #计算利用率
    if sum_paid_bw != 0:
        rate = decimal.Decimal(sum_peak_value)/decimal.Decimal(sum_paid_bw)
    else:
        rate = 0

    print 'rate=' + str(rate)

    returnList.append({"sum_service_bw":sum_service_bw, "sum_peak_value":sum_peak_value, "sum_paid_bw":sum_paid_bw, "rate":rate})
    return returnList