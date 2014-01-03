from django.contrib import admin
from blog.models import BlogPost, Photo, Item
from easy_thumbnails.signals import saved_file
from easy_thumbnails.signal_handlers import generate_aliases_global

saved_file.connect(generate_aliases_global)

# Register your models here.
class BlogPostAdmin(admin.ModelAdmin):
	#fields = ['pub_date', 'question']
	fieldsets = [
		('Title', 			{'fields': ['title']}),
		('Blog Text',		{'fields': ['body']}),
		('Date', 			{'fields': ['timestamp']}),
	]

	list_display = ('title', 'body', 'timestamp')
	list_filter = ['timestamp']

admin.site.register(BlogPost, BlogPostAdmin)

class PhotoInline(admin.StackedInline):
	model = Photo

class ItemAdmin(admin.ModelAdmin):
	inlines = [PhotoInline]

admin.site.register(Item, ItemAdmin)
admin.site.register(Photo)