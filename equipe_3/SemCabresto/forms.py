from flask.ext.wtf import Form
from wtforms import TextField, TextAreaField, SubmitField, validators

class SearchForm(Form):
  name = TextField("Name",  [validators.Required("Please enter your name.")])
  email = TextField("Email",  [validators.Required("Please enter your email address."), validators.Email("Please enter your email address.")])
  subject = TextField("Subject",  [validators.Required("Please enter a subject.")])
  message = TextAreaField("Message",  [validators.Required("Please enter a message.")])
  submit = SubmitField("Send")

class AdvancedSearchForm(Form):
  firstname = TextField("First name",  [validators.Required("Please enter your first name.")])
  lastname = TextField("Last name",  [validators.Required("Please enter your last name.")])
  submit = SubmitField("Create account")

def __init__(self, *args, **kwargs):
  Form.__init__(self, *args, **kwargs)

  def validate(self):
    if not Form.validate(self):
      return False