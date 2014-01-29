"""
Django settings for django_mysite project.

For more information on this file, see
https://docs.djangoproject.com/en/1.6/topics/settings/

For the full list of settings and their values, see
https://docs.djangoproject.com/en/1.6/ref/settings/
"""

# Build paths inside the project like this: os.path.join(BASE_DIR, ...)
import os
import easy_thumbnails
BASE_DIR = os.path.dirname(os.path.dirname(__file__))


# Quick-start development settings - unsuitable for production
# See https://docs.djangoproject.com/en/1.6/howto/deployment/checklist/

# SECURITY WARNING: keep the secret key used in production secret!
SECRET_KEY = '7t6q^ua8s%(kt)f+zeny=j4j1bo(xhj*x3)!2$a+m$_5^_m*#*'

# SECURITY WARNING: don't run with debug turned on in production!
DEBUG = True

TEMPLATE_DEBUG = True

ALLOWED_HOSTS = []


# Application definition

INSTALLED_APPS = (
    'django_admin_bootstrapped',
    'django.contrib.admin',
    'django.contrib.auth',
    'django.contrib.contenttypes',
    'django.contrib.sessions',
    'django.contrib.messages',
    'django.contrib.staticfiles',
    'polls',
    'bootstrap_toolkit',
    'demo_app',
    'blog',
    #'sorl.thumbnail',
    'easy_thumbnails',
    'debug_toolbar',
)

MIDDLEWARE_CLASSES = (
    'django.contrib.sessions.middleware.SessionMiddleware',
    'django.middleware.common.CommonMiddleware',
    'django.middleware.csrf.CsrfViewMiddleware',
    'django.contrib.auth.middleware.AuthenticationMiddleware',
    'django.contrib.messages.middleware.MessageMiddleware',
    'django.middleware.clickjacking.XFrameOptionsMiddleware',
)

ROOT_URLCONF = 'django_mysite.urls'

WSGI_APPLICATION = 'django_mysite.wsgi.application'


# Database
# https://docs.djangoproject.com/en/1.6/ref/settings/#databases

DATABASES = {
    'default': {
        'ENGINE': 'django.db.backends.mysql',
        'NAME': 'django_mysite',
<<<<<<< HEAD
	    'USER': 'root',
	    'PASSWORD': '123456',
	    'HOST': '192.168.1.203',
=======
	'USER': 'root',
	'PASSWORD': '123456',
	#'HOST': '192.168.1.203',
    'HOST': 'localhost'
>>>>>>> 75c0d1b342296b499c31e76cb75f9fca90200c59
    }
}

# Internationalization
# https://docs.djangoproject.com/en/1.6/topics/i18n/

LANGUAGE_CODE = 'en-us'

TIME_ZONE = 'Asia/Shanghai'

USE_I18N = True

USE_L10N = True

USE_TZ = True


# Static files (CSS, JavaScript, Images)
# https://docs.djangoproject.com/en/1.6/howto/static-files/

MEDIA_ROOT = os.path.join(BASE_DIR, "media")

MEDIA_URL = '/media/'

STATIC_URL = '/static/'

STATIC_ROOT = os.path.join(BASE_DIR, 'static')

STATICFILES_FINDERS = (
    'django.contrib.staticfiles.finders.FileSystemFinder',
    'django.contrib.staticfiles.finders.AppDirectoriesFinder',
)

TEMPLATE_LOADERS = (
    'django.template.loaders.filesystem.Loader',
    'django.template.loaders.app_directories.Loader',
#     'django.template.loaders.eggs.Loader',
)

TEMPLATE_DIRS = [
        os.path.join(BASE_DIR, 'templates')
    ]

BOOTSTRAP_BASE_URL      = os.path.join(STATIC_ROOT, 'bootstrap')
BOOTSTRAP_CSS_BASE_URL  = os.path.join(BOOTSTRAP_BASE_URL, 'css')
#BOOTSTRAP_CSS_URL       = BOOTSTRAP_CSS_BASE_URL + 'bootstrap.css'
BOOTSTRAP_JS_BASE_URL   = os.path.join(BOOTSTRAP_BASE_URL, 'js')

# Enable for single bootstrap.js file
#BOOTSTRAP_JS_URL        = BOOTSTRAP_JS_BASE_URL + 'bootstrap.js'

STATICFILES_DIRS = (
    ('b_css', BOOTSTRAP_CSS_BASE_URL),
    ('b_js',  BOOTSTRAP_JS_BASE_URL),
    ('chart_js', os.path.join(STATIC_ROOT, 'js'))
)

THUMBNAIL_ALIASES = {
    '': {
        'avatar': {'size': (125, 125), 'crop': True},
    },
}

THUMBNAIL_DEBUG = True
THUMBNAIL_PRESERVE_EXTENSIONS = ('png','jpeg',)


if DEBUG:
    INTERNAL_IPS = ('192.168.2.195',)
    MIDDLEWARE_CLASSES += (
        'debug_toolbar.middleware.DebugToolbarMiddleware',
    )
    INSTALLED_APPS += (
        'debug_toolbar',
    )
    DEBUG_TOOLBAR_PANELS = (
        'debug_toolbar.panels.versions.VersionsPanel',
        'debug_toolbar.panels.timer.TimerPanel',
        'debug_toolbar.panels.settings.SettingsPanel',
        'debug_toolbar.panels.headers.HeadersPanel',
        'debug_toolbar.panels.request.RequestPanel',
        'debug_toolbar.panels.sql.SQLPanel',
        'debug_toolbar.panels.staticfiles.StaticFilesPanel',
        'debug_toolbar.panels.templates.TemplatesPanel',
        'debug_toolbar.panels.cache.CachePanel',
        'debug_toolbar.panels.signals.SignalsPanel',
        'debug_toolbar.panels.logging.LoggingPanel',
        'debug_toolbar.panels.redirects.RedirectsPanel',
    )
    DEBUG_TOOLBAR_CONFIG = {
        'INTERCEPT_REDIRECTS': False,
    }

#INTERNAL_IPS = ('192.168.2.195',)

