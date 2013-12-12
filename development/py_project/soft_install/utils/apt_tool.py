import os, sys, subprocess
from os_tool import os_cmd


class Apt(object):

	def __init__(self):
		self.conf = open('../conf/sources.list','r').read()
		self.cur_conf = '/etc/apt/sources.list'
		
	def apt_mod(self):
		if self.conf == self.cur_conf:
			print "The %s will not change"
		else:
			print "I will change the %s configuration"%self.cur_conf
			try:
				apt_file = open(self.cur_conf, 'w')
				apt_file.write(self.conf)
				apt_file.close
			finally:
				print "Change the configuration successfuly"


	def apt_update(self):
		''' Executed the 'apt-get update' 
						 'apt-get upgrade -y' on the ubuntu system '''
		os_cmd('apt-get update')
		os_cmd('apt-get -y upgrade')


	def apt_install(self, soft):
		''' It will install soft with apt command on the ubuntu '''
		try:
			print 'I will begin to install %s on the system !' % soft
			os_cmd('apt-get install -y %s' % soft)
			print 'The install operation complete !'
			os_cmd('apt-get -fy install')
			os_cmd('apt-get -y autoremove')
		except OSError as e :
			print >>sys.stderr, "Execution Falied: ", e