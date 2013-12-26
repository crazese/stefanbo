from django import forms
from django.contrib.auth.models import User
from bootstrap_toolkit.widgets import BootstrapDateInput, BootstrapTextInput, BootstrapUneditableInput

class LoginForm(forms.Form):
	username = forms.CharField(
		required = True,
		label = u"admin",
		error_messages = {'required':'Please input your username'},
		widget = forms.TextInput(attrs = {'placeholder':u"root",}),
	)

	password = forms.CharField(
		required = True, 
		label = u'password',
		error_messages = {'required': u"Please input your password"},
		widget = forms.PasswordInput(attrs = {'placeholder': u"password"}),
	)

	def clean(self):
		if not self.is_valid():
			raise forms.ValidationError(u"username and password must input")
		else:
			cleaned_data = super(LoginForm, self).clean()
