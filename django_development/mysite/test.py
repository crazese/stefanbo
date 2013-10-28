from django.template import Template, Context
raw_template="""<p> Dear {{ person_name }}, </p>
<p> Thanks for placing on order from {{ company }}. It's scheduled to
ship on {{ ship_date|date:"F j, Y" }}. </p>

{% if ordered_warranty %}
<p> Your warranty information will be included in the packaging.</p>
{% else %}
<p> You didn't order a warranty, so you're on your own when
the products inevitably stop working.</p>
{% endif %}

<p>Sincerely, <br />{{company }}</p>"""

t = Template(raw_template)

import datetime
c = Context({'person_name': 'John Smith',
			 'company': 'Outdoor equipment',
			 'ship_date': datetime.date(2009, 4, 2),
			 'ordered_warranty': False})

t.render(c)

########################################################################
from django.template import Template, Context
person = {'name': 'Sally', 'age': '43'}
t = Template('{{ person.name }} is {{ person.age }} years old.')
c = Context({'person': person})
t.render(c) 

from django.template import Template, Context
import datetime
d = datetime.date(1993, 5, 2)
d.years
d.month
d.day

t = Template('The month is {{ date.month }} and the year is {{ date.year }}.')
c = Context({'date' : d})
t.render(c)


from django.template import Template, Context
class person(object):
	def __init__(self, first_name, last_name):
		self.first_name = first_name
		self.last_name = last_name

t = Template('Hello, {{ person.first_name }} {{ person.last_name }}.')
c = Context({'person' : Person('john', 'smith')})


#########################################################################

from django.template import Template, Context
person = {'name': 'Sally', 'age': '43'}
t = Template('{{ person.name.upper }} is {{ person.age }} years old')
c = Context({'person': person})
t.render(c)


#########################################################################

