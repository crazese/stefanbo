#!/usr/bin/env python

# otherfile.py 

import manynames

x = 66
print(x)						# 66: the global here
print(manynames.x)				# 11: global become attributes after imports

manynames.f()					# 11: manynames's x, not the one here!
manynames.g()					# 22: local in other file's function

print(manynames.C.x)			# 33: attribute of class in other module
I = manynames.C()				
print(I.x)						# 33: still from class here
I.m()
print(I.x)						# 55: now from instance!