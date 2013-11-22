# -*- coding: utf-8 -*-
from geo_topology.models import *
from django.contrib import admin


class CountryAdmin(admin.ModelAdmin):
    list_display=('country_code','country_cn_name','country_en_name')
    search_fields = ('country_cn_name','country_en_name')
admin.site.register(Country,CountryAdmin)



class RegionAdmin(admin.ModelAdmin):
    list_display=('region_code','region_cn_name','region_en_name','parent_co')
    list_display_links=('region_cn_name',)
    search_fields = ('region_cn_name','region_en_name')
admin.site.register(Region,RegionAdmin)

class ProvinceAdmin(admin.ModelAdmin):
    list_display=('province_code','province_cn_name','province_en_name','parent_rg')
    list_display_links=('province_cn_name',)
    search_fields = ('province_cn_name','province_en_name')
admin.site.register(Province,ProvinceAdmin)

class CityAdmin(admin.ModelAdmin):
    list_display=('city_code','city_cn_name','city_en_name','parent_p')
    list_display_links=('city_cn_name',)
    search_fields = ('city_cn_name','city_en_name')
admin.site.register(City,CityAdmin)

