# -*- coding: utf-8 -*-

output = file('result', 'w')
text_input = file('cdays-4-test.txt', 'r').readlines()

output.writelines([l for l in text_input if l[:-1].strip() and not l.startswith('#')])