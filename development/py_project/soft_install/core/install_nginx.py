# -*- coding: utf-8 -*-
#!/usr/bin/env python

# append sys.path to import module from other folder!
import sys
sys.path.append('..')

# import utils from utils folder
from utils import con_tool
from utils import ftp_tool
from utils import os_tool

class install_nginx(object):

	def __init__(self):
		self.host = 'hut.jofgame.com'
		self.user = 'zhubo'
		self.pw = 'Jof1Game8yzl'
