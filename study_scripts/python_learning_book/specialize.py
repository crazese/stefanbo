#!/usr/bin/env python

class Super:
	
	def method(self):
		print('in Super method')

	def delegate(self):
		self.action()


class Inheritor(Super):
	pass


class Replacer(Super):

	def method(self):
		print('in Replacer.method')


class Extender(Super):

	def method(self):
		print('starting Extender.method')
		Super.method(self)
		print('ending Extender.method')


class Provider(Super):

	def action(self):
		print('in Provider.action')


if __name__ == '__main__':
	for kclass in (Inheritor, Replacer, Extender):
		print '\n' + kclass.__name__ + '...'
		kclass().method()
		
	print '\nProvider...'
	x = Provider()
	x.delegate()