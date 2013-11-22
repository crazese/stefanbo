# -*- coding: utf-8 -*-

from django.db import models
from django.forms import ModelForm
from base_dict.models import PayType, BusinessType

# Create your models here.

class Country(models.Model):
    country_code=models.CharField('编码',max_length=50,unique=True)
    country_cn_name=models.CharField('中文名称',max_length=50)
    country_en_name=models.CharField('英文名称',max_length=50)
    class Meta:
        verbose_name='国家信息'
        verbose_name_plural='国家信息'
    def __unicode__(self):
        return self.country_cn_name

class Region(models.Model):
    region_code=models.CharField('编码',max_length=50,unique=True)
    region_cn_name=models.CharField('中文名称',max_length=50)
    region_en_name=models.CharField('英文名称',max_length=50)
    parent_co=models.ForeignKey(Country,verbose_name='所属国家')
    class Meta:
        verbose_name='大区信息'
        verbose_name_plural='大区信息'
    def __unicode__(self):
        return self.region_cn_name

class Province(models.Model):
    province_code=models.CharField('编码',max_length=50,unique=True)
    province_cn_name=models.CharField('中文名称',max_length=50)
    province_en_name=models.CharField('英文名称',max_length=50)
    parent_rg=models.ForeignKey(Region,verbose_name='所属大区')
    class Meta:
        verbose_name='省信息'
        verbose_name_plural='省信息'
    def __unicode__(self):
        return self.province_cn_name

class City(models.Model):
    city_code=models.CharField('编码',max_length=50,unique=True)
    city_cn_name=models.CharField('中文名称',max_length=50)
    city_en_name=models.CharField('英文名称',max_length=50)
    parent_p=models.ForeignKey(Province,verbose_name='所属省')
    class Meta:
        verbose_name='城市信息'
        verbose_name_plural='城市信息'
    def __unicode__(self):
        return self.city_cn_name
    
