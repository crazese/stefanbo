#!/usr/bin/env python
try: set
except NameError: from sets import Set as set
def unique(s):
	try:
		return list(set(s))
	except TypeError:
		pass
	t = list(s)
	try:
		t.sort()
	except TypeError:
		del t
	else:
		return [x for i, x in enumerate(t) if not i or x != t[i -1]]
	u = []
	for x in s:
		if x not in u:
			u.append(x)
	return u

#打乱 了顺序