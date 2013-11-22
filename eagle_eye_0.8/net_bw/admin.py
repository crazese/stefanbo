# -*- coding: utf-8 -*-
from django.contrib import admin
from net_bw.models import *

class IPAdmin(admin.ModelAdmin):
    list_display = ('kind','start','end')
    search_fields = ('kind',)
admin.site.register(IP_range,IPAdmin)

class ContactAdmin(admin.ModelAdmin):
    list_display = ('contact_name','contact_sex','contact_tel')
    list_display_links=('contact_name',)
    search_fields = ('contact_name','contact_sex')
admin.site.register(Contact,ContactAdmin)
class RoomAdmin(admin.ModelAdmin):
    fieldsets=(
        (None,{
            'fields':('room_code','room_cn_name','room_city','room_contact','max_BW','min_BW','room_pay','room_biz')
        }),
        ('IP设置',{
            'fields':('inner_IP','out_IP','internet_IP')
        }),
    )
    list_display=('room_code','room_cn_name','room_city','contact_name','max_BW','min_BW','room_pay','biz_name','inner','out','internet')
    list_display_links=('room_cn_name',)
    raw_id_fields = ('room_city','room_contact')
    search_fields = ('pay_cn_name','room_city')

admin.site.register(Room,RoomAdmin)

class BandAdmin(admin.ModelAdmin):
    list_play=('room_name','biz','time','bw_value')
    search_fields = ('time','biz','room_name','bw_value')
admin.site.register(BandWidth,BandAdmin)