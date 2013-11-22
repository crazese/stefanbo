# -*- coding:utf8 -*-
from django.db import models

# Create your models here.

class BusinessType(models.Model):
    biz_code=models.CharField('业务类型编码',max_length=30,unique=True)
    biz_cn_name=models.CharField('中文名称',max_length=50)
    biz_en_name=models.CharField('英文名称',max_length=50)
    class Meta:
        verbose_name='业务类型'
        verbose_name_plural='业务类型'
    def __unicode__(self):
        return self.biz_cn_name


class PayType(models.Model):
    pay_code=models.CharField('付费类型编码',max_length=30,unique=True)
    pay_cn_name=models.CharField('中文名称',max_length=50)
    pay_en_name=models.CharField('英文名称',max_length=50)
    class Meta:
        verbose_name='付费类型'
        verbose_name_plural='付费类型'
    def __unicode__(self):
        return self.pay_cn_name


class ISP(models.Model):
    isp_code=models.CharField('ISP编码',max_length=30,unique=True)
    isp_cn_name=models.CharField('中文名称',max_length=50)
    isp_en_name=models.CharField('英文名称',max_length=50)
    class Meta:
        verbose_name='ISP'
        verbose_name_plural='ISP'
    def __unicode__(self):
        return self.isp_cn_name