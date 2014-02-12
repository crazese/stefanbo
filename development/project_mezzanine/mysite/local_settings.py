DEBUG = True

# Make these unique, and don't share it with anybody.
SECRET_KEY = "14ce40e2-05df-47a9-a9e6-0032ffd9d8eb158f20f8-ea82-4dcb-8262-c59541f271337012049a-9878-4e11-80e3-cf4a199f583c"
NEVERCACHE_KEY = "670a44a0-a3d0-494c-a27f-c11f0ac2be1a610ac41b-54f8-493c-8bb6-f58e4625a0bc55a0aeff-2a53-4746-8385-6a9b54f804b4"

DATABASES = {
    "default": {
        # Ends with "postgresql_psycopg2", "mysql", "sqlite3" or "oracle".
        "ENGINE": "django.db.backends.mysql",
        # DB name or path to database file if using sqlite3.
        "NAME": "mysite",
        # Not used with sqlite3.
        "USER": "root",
        # Not used with sqlite3.
        "PASSWORD": "123456",
        # Set to empty string for localhost. Not used with sqlite3.
        "HOST": "192.168.1.204",
        # Set to empty string for default. Not used with sqlite3.
        "PORT": "3306",
    }
}
