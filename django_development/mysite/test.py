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

在方法查找过程中，如果某方法抛出一个异常，
除非该异常有一个 silent_variable_failure 属性并且值为 True ，
否则的话它将被传播。
如果异常被传播，模板里的指定变量会被置为空字符串

In [45]: t = Template("My name is {{ person.first_name }}.")

In [46]: class PersonClass3:
   ....:     def first_name(self):
   ....:         raise AssertionError, "foo"
   ....:     
   ....:     

In [48]: p = PersonClass3()


In [53]: t.render(Context({"person": p}))
---------------------------------------------------------------------------
AssertionError                            Traceback (most recent call last)

/var/www/html/<ipython console> in <module>()

/usr/local/lib/python2.6/dist-packages/Django-1.5.4-py2.6.egg/django/template/base.pyc in render(self, context)
    138         context.render_context.push()
    139         try:
--> 140             return self._render(context)
    141         finally:
    142             context.render_context.pop()

/usr/local/lib/python2.6/dist-packages/Django-1.5.4-py2.6.egg/django/template/base.pyc in _render(self, context)
    132 
    133     def _render(self, context):
--> 134         return self.nodelist.render(context)
    135 
    136     def render(self, context):

/usr/local/lib/python2.6/dist-packages/Django-1.5.4-py2.6.egg/django/template/base.pyc in render(self, context)
    828         for node in self:
    829             if isinstance(node, Node):
--> 830                 bit = self.render_node(node, context)
    831             else:
    832                 bit = node

/usr/local/lib/python2.6/dist-packages/Django-1.5.4-py2.6.egg/django/template/debug.pyc in render_node(self, node, context)
     72     def render_node(self, node, context):
     73         try:
---> 74             return node.render(context)
     75         except Exception as e:
     76             if not hasattr(e, 'django_template_source'):

/usr/local/lib/python2.6/dist-packages/Django-1.5.4-py2.6.egg/django/template/debug.pyc in render(self, context)
     82     def render(self, context):
     83         try:
---> 84             output = self.filter_expression.resolve(context)
     85             output = template_localtime(output, use_tz=context.use_tz)
     86             output = localize(output, use_l10n=context.use_l10n)

/usr/local/lib/python2.6/dist-packages/Django-1.5.4-py2.6.egg/django/template/base.pyc in resolve(self, context, ignore_failures)
    576         if isinstance(self.var, Variable):
    577             try:
--> 578                 obj = self.var.resolve(context)
    579             except VariableDoesNotExist:
    580                 if ignore_failures:

/usr/local/lib/python2.6/dist-packages/Django-1.5.4-py2.6.egg/django/template/base.pyc in resolve(self, context)
    726         if self.lookups is not None:
    727             # We're dealing with a variable that needs to be resolved

--> 728             value = self._resolve_lookup(context)
    729         else:
    730             # We're dealing with a literal, so it's already been "resolved"


/usr/local/lib/python2.6/dist-packages/Django-1.5.4-py2.6.egg/django/template/base.pyc in _resolve_lookup(self, context)
    777                     else:
    778                         try: # method call (assuming no args required)
--> 779                             current = current()
    780                         except TypeError: # arguments *were* required
    781                             # GOTCHA: This will also catch any TypeError


/var/www/html/<ipython console> in first_name(self)

AssertionError: foo

In [54]: class SilentAssertionError(AssertionError):
   ....:     silent_variable_failure = True
   ....:     
   ....:     

In [55]: class PersonClass4:
   ....:     def first_name(self):
   ....:         raise SilentAssertionError
   ....:     
   ....:     

In [56]: p = PersonClass4()

In [57]: t.render(Context({"person": p}))
Out[57]: u'My name is .'

仅在方法无需传入参数时，其调用才有效。 
否则，系统将会转移到下一个查找类型（列表索引查找）。

#########################################################################################

from django.template import Context
c = Context({"foo": "bar"})
c['foo']




