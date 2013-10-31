#!/usr/bin/env python
try : set
except NameError: from sets import Set as set
# f定义了序列seq的元素之间的等价对应关系， 而且对于
# seq的任意元素x， f(x)必须是可哈希的
def uniquer(seq, f=None):
	'''保留由f定义的每个等价类中最早出现的元素'''
	if f is None:
		def f(x): return x
	already_seen = set()
	result = []
	for item in seq:
		marker = f(item)
		if marker not in already_seen:
			already_seen.add(marker)
			result.append(item)
	return result