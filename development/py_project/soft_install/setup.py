try: 
	from setuptools import setup
except ImportError:
	from distutils.core import setup 

config = {
	'description':'soft_install',
	'author':'stefanmonkey',
	'url':'URL to get it at.',
	'download_url':'Where to download it.',
	'author_mail':'stefanmonkeybo@gmail.com',
	'version':'0.1',
	'install_requires':['nose'],
	'packages':['core'],
	'scripts':[],
	'name':'py_project'	
}

setup(**config)