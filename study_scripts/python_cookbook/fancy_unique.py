#!/usr/bin/env python
def fancy_unique(seq, f, p):
	'''保留由f定义的等价类的'最好'的元素
	   选择函数p成对地选择(index, item) '''
	representative = []
	for index, item in enumerate(seq):
		marker = f(item)
		if marker in representative:
			#将 index和 item在for循环中重新绑定
			#循环的下一步并不会使用它们的绑定
			index, item = p((index, item), representative[marker])
		representative[marker] = index, item
	#通过对索引排序重新构建序列顺序
	auxlist = representative.values()
	auxlist.sort()
	return [item for index, item in auxlist]