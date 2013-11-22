# -*- coding: utf-8 -*-
from django.db import models
from django.forms.models import ModelForm
from base_dict.models import *
import commUtil
from geo_topology.models import City
import datetime
import decimal
from sets import Set
from django.db import connection

class IP_range(models.Model):
    start=models.IPAddressField("起始段",max_length=100)
    end=models.IPAddressField("终止段",max_length=100)
    kind=models.CharField("IP种类",max_length=20)
    def __unicode__(self):
        return u"%s  %s"% (self.start, self.end)
    class Meta:
        verbose_name='IP信息'
        verbose_name_plural='IP信息'

class Contact(models.Model):
    GENDER_CHOICES = (('MAN',"男"),('FEMALE','女'))
    contact_name=models.CharField('联系人姓名',max_length=50)
    contact_sex=models.CharField('性别',max_length=10, choices=GENDER_CHOICES)
    contact_tel=models.CharField('联系电话',max_length=30)
    contact_mail=models.EmailField('e-mail',max_length=50)
    class Meta:
        verbose_name='机房联系人信息'
        verbose_name_plural='机房联系人信息'
    def __unicode__(self):
        return self.contact_name

class Room(models.Model):
    room_code=models.CharField('机房编码',max_length=50,unique=True)
    room_en_name=models.CharField('机房英文名称',max_length=100)
    room_cn_name=models.CharField('机房中文名称',max_length=100)
    room_isp=models.ForeignKey(ISP,verbose_name='ISP')
    room_city=models.ForeignKey(City,verbose_name='所在城市')
    room_address=models.CharField('机房详细地址',max_length=100)
    room_contact=models.ManyToManyField(Contact,verbose_name='机房联系人信息')
    max_BW=models.FloatField('最大服务带宽(M)')
    min_BW=models.FloatField('最少付费带宽(M)')
    max_available_BW=models.FloatField('最大可用带宽(M)')
    room_pay=models.ForeignKey(PayType,verbose_name='机房付费类型')
    room_biz=models.ManyToManyField(BusinessType,verbose_name='机房服务类型')
    GENDER_CHOICES = (('TWO',"二层"),('THREE','三层'))
    style=models.CharField('接入方式',max_length=10,choices=GENDER_CHOICES)
    inner_IP=models.ManyToManyField(IP_range,related_name='inner_IP',verbose_name='内网IP',editable=True)
    out_IP=models.ManyToManyField(IP_range,related_name='out_IP',verbose_name='外网IP',editable=True)
    internet_IP=models.ManyToManyField(IP_range,related_name='internet_IP',verbose_name='互联IP',editable=True,null=True)

    class Meta:
        verbose_name='机房信息'
        verbose_name_plural='机房信息'
    def __unicode__(self):
        return self.room_cn_name
    def contact_name(self):
        return self.room_contact
    def biz_name(self):
        return self.room_biz
    def inner(self):
        return self.inner_IP
    def out(self):
        return self.out_IP
    def internet(self):
        return self.internet_IP

class BWModify(models.Model):
    room=models.ForeignKey(Room,verbose_name='选择机房')
    GENDER_CHOICES = (('MAX_BW',"最大服务带宽"),('MIN_BW','最少付费带宽')),('MAX_AVAILABLE_BW','最大可用带宽')
    kind=models.CharField('带宽种类',max_length=20, choices=GENDER_CHOICES)
    old_value=models.FloatField('原始值',max_length=50)
    new_value=models.FloatField('新值',max_length=50)
    time=models.DateField('更改时间',max_length=100)
    class Meta:
        verbose_name='机房-带宽更改历史'
        verbose_name_plural='机房-带宽更改历史'
    def __unicode__(self):
        return u"%s %s %s %s %s"% (self.room, self.kind,self.old_value,self.new_value,self.time)

class BandWidth(models.Model):
    room=models.ForeignKey(Room,verbose_name='选择机房')
    #biz=models.ForeignKey(BusinessType,verbose_name='业务类型')
    time=models.CharField('日期',max_length=10)
    vod_bw_value=models.FloatField("vod日峰值带宽(M)")
    live_bw_value=models.FloatField("live日峰值带宽(M)")
    web_bw_value=models.FloatField("web日峰值带宽(M)")
    bw_value=models.FloatField("日峰值带宽(M)")
    max_BW=models.FloatField('最大服务带宽(M)')
    min_BW=models.FloatField('最少付费带宽(M)')
    rate=models.FloatField("日利用率")
    class Meta:
        verbose_name='机房-带宽关系'
        verbose_name_plural='机房-带宽关系'
    #def __unicode__(self):
     #   return self.room_id

    '''
    # 取机房带宽
    def getBWByRoom(self, begin_time, days, room_list):
        returnList = []
        #time sql拼装
        if days == 0:
            timeSQL = " and time = '" + begin_time + "'"
        else:
            end_time = (datetime.date(int(begin_time[0:4]),int(begin_time[5:7]),int(begin_time[8:10])) + datetime.timedelta(days=(days - 1))).strftime('%Y-%m-%d')
            timeSQL = " and time >= '" + begin_time + "' and time <= '" + end_time + "'"

        #room sql拼装
        if len(room_list) > 0:
            roomSQL = " room_id in " + commUtil.list2String(room_list)
        else:
            roomSQL = " room_id = 0"

        cursor = connection.cursor()
        # 取得机房期间内的带宽,现在机房为97个，以后分开查询************************
        sql = "select room_id,bw_value from net_bw_bandwidth where" + roomSQL + timeSQL + "order by room_id"
        print sql
        cursor.execute(sql)
        bw_value_list = []

        #计数
        count = 0
        #中间room_id
        tmp = 0
        print cursor.fetchmany()
        for row in cursor.fetchall():
            #保存第一条记录的room_id
            if count == 0:
                tmp = row[0]
            if tmp == row[0]:
                bw_value_list.append(row[1])
            else:
                returnList.append(bw_value_list)
                tmp = row[0]
                bw_value_list = []
                bw_value_list.append(row[1])

            count = count + 1

            if count == days * len(room_list):
               returnList.append(bw_value_list)

        print returnList
            
        cursor.close()
    '''


class BandWidthDaily(models.Model):
    max_BW=models.FloatField('最大服务带宽(M)')
    min_BW=models.FloatField('最少付费带宽(M)')
    bw_value=models.FloatField("日峰值带宽(M)")
    rate=models.FloatField("日利用率")
    time=models.CharField('日期',max_length=10)

    class Meta:
        verbose_name='机房-带宽日利用率'
        verbose_name_plural='机房-带宽日利用率'

