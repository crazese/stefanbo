# -*- coding:utf-8 -*-
from __future__ import division
from django import template

register = template.Library()

def percent_decimal(value):
    if value == 0 or value == "":
        return "0"
    else:
        value = float(str(value))
        value = int(value * 10000)
        value2 = value/100.0

        return str(value2) + '%'
register.filter('percent_decimal', percent_decimal)

def format_time(value):
    if len(value) == 10:
        return value.replace('-','/')
    else:
        beginTime = value[0:10].replace('-','/')
        endTime = value[11:21].replace('-','/')
        str = beginTime + '-' + endTime
        return str
register.filter('format_time', format_time)



  