from django.db.models.fields.files import ImageField, ImageFieldFile
from PIL import Image
import os

def _add_thumb(s):
	"""
	Modifies a string (filename, URL) containing on image filename, to insert
	'.thumb' before the file extension (which is changed to be '.jpg').
	"""
	parts = s.split(".")
	parts.insert(-1, "thumb")
	if parts[-1].lower() not in ['jpeg', 'jpg', 'png']:
		parts[-1] = 'jpg'
	return ".".join(parts)

class ThumbnailImageFieldFile(ImageFieldFile):
	"""
	"""
	def __init__(self, *args, **kwargs):
		super(ThumbnailImageFieldFile, self).__init__(*args, **kwargs)

	def _get_thumb_path(self):
		return _add_thumb(self.path)
	thumb_path = property(_get_thumb_path)

	def _get_thumb_url(self):
		return _add_thumb(self.url)
	thumb_url = property(_get_thumb_url)

	def save(self, name, content, save=True):
		super(ThumbnailImageFieldFile, self).save(name, content, save)
		
		img = Image.open(self.path)
		img.thumbnail(
				(self.field.thumb_width, self.field.thumb_heigth),
				Image.ANTIALIAS
			)
		img.save(self.thumb_path, "png")

	def delete(self, save=True):
		if os.path.exists(self.thumb_path):
			os.remove(self.thumb_path)
		super(ThumbnailImageFieldFile, self).delete(save)


class ThumbnailImageField(ImageField):
	"""
	Behaves like a regular ImageField, but stores an extra (JPEG) thumbnail image,
	providing get_FIELD_thumb_url() and get_FIELD_thumb_filename().
	Accepts two additional, optional arguments; thumb_width and thumb_heigth, 
	both defaulting to 128 (pixels). Resizing will preserve aspect ratio while
	staying inside the requested dimensions; see PIL's Image.thumbnail()
	method documentation for details.
	"""
	attr_class = ThumbnailImageFieldFile

	def __init__(self, thumb_width=128, thumb_heigth=128, *args, **kwargs):
		self.thumb_width = thumb_width
		self.thumb_heigth = thumb_heigth
		super(ThumbnailImageField, self).__init__(*args, **kwargs)

