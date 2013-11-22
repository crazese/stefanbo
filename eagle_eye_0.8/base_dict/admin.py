# -*- coding: utf-8 -*-
from django.contrib import admin
from base_dict.models import *

class BizAdmin(admin.ModelAdmin):
    list_display = ('biz_code','biz_cn_name','biz_en_name')
    list_display_links=('biz_cn_name',)
    search_fields = ('biz_cn_name','biz_en_name')

class PayAdmin(admin.ModelAdmin):
    list_display=('pay_code','pay_cn_name','pay_en_name')
    list_display_links=('pay_cn_name',)
    search_fields = ('pay_cn_name','pay_en_name')

class IspAdmin(admin.ModelAdmin):
    list_display = ('isp_code','isp_cn_name','isp_en_name')
    list_display_links = ('isp_cn_name',)
    search_fields = ('isp_cn_name','isp_en_name')

admin.site.register(ISP,IspAdmin)
admin.site.register(BusinessType,BizAdmin)
admin.site.register(PayType,PayAdmin)
