# -*- coding: utf-8 -*-
import os
export = ""
for root, dirs, files in os.walk('/Users/apple'):
	export += "\n %s;%s;%s" % (root, dirs, files)
open('/tmp/folder_list', 'w').write(export)

