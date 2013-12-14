# -*- coding: utf-8 -*-
import os
for root , dirs, files in os.walk('/Users/apple'):
	open('/tmp/folder_list', 'a').write(root + dirs + files)

