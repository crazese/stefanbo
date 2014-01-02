from django.db import models
from sorl.thumbnail import ImageField
############################################################

class BlogPost(models.Model):
	title = models.CharField(max_length=150)
	body = models.TextField()
	timestamp = models.DateTimeField()


#############################################################
class Item(models.Model):
	name = models.CharField(max_length=250)
	description = models.TextField()

	class Meta:
		ordering = ['name']

	def __unicode__(self):
		return self.name

	@models.permalink
	def get_absolute_url(self):
		return ('item_detail', None, {'object_id': self.id})

class Photo(models.Model):
	item = models.ForeignKey(Item)
	title = models.CharField(max_length=100)
	image = ImageField(upload_to='photos')
	caption = models.CharField(max_length=250, blank=True)

	def __unicode__(self):
		return self.image
	#class Meta:
	#	ordering = ['title']

	#def __unicode__(self):
	#	return self.title

	#@models.permalink
	#def get_absolute_url(self):
	#	return ('photo_detail', None, {'object_id': self.id})

