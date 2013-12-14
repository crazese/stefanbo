# -*- coding: utf-8 -*-
# import Python Lib
import sys
sys.path.append('..')

# import some function from utils
from utils.apt_tool import *
from utils.ftp_tool import *
from util.os_tool import *
from utils.apt_tool import Apt

class Install(object):

	def __init__(self, start):
		self.quips = [
			]
		self.start = start

	def install_start(self):
		next = self.start

		while True:
			print "\n-----------"
			soft = getattr(self, next)
			next = soft()

	def install_end(self):
		print "######## Thanks for you use the soft! #########"
		exit(0)


	def start_point(self):
		# You need to choose what soft you want to install
		# nginx or mysql or php
		print "What soft do you want to install next or others ?"
		print "-- nginx"
		print "-- php"
		print "-- mysql"
		print "-- init 		# init the system"
		print "-- quit		# exit the soft install procedure"

		action = raw_input(">>>")

		if action == "nginx":
			print "######## I will install nginx next! ########"
			return 'nginx_install'

		elif action == "php":
			print "######## I  will install php next! ########"
			return 'php_install'

		elif action == "mysql":
			print "######## I will install mysql next! #######"
			return 'mysql_install'

		elif action == "init":
			print "######## I will init the system with apt tools! ########"
			return 'init_install'
		
		elif action == "end":
			print "######## You will quit the soft install procedure! ########"
			return 'install_end'

		else:
			print "######## WRONG INPUT!! ########"
			return 'start_point'


	def init_install(self):
		'''
		Init the system , change the /etc/apt/source.list and update it.
		'''	
		apt = Apt()
		apt.apt_mod()
		apt.apt_update()

		soft_list = ['build-essential',
				 'gcc',
				 'g++',
				 'make',
				 'libncurses5-dev',
				 'libxp-dev',
				 'libmotif-dev',
				 'libxt-dev',
				 'libstdc++6']
		for soft in soft_list:
			apt.apt_install(soft)

		print "######## The system init install complete successfully! #########"
		return 'start_point'


	def nginx_install(self):
		'''
		Install the nginx soft 
		'''
		


a_install = Install("init_install")
a_install.install_start()