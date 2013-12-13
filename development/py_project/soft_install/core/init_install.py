# -*- coding: utf-8 -*-
#!/usr/bin/env python

import sys
sys.path.append('..')
from utils.apt_tool import Apt
from utils.os_tool import * 

def main():
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

if __name__ == "__main__":  
	main() 