from flask import Flask, render_template, request, flash
from forms import SearchForm, AdvancedSearchForm

app = Flask(__name__)

app.secret_key = 'development key'

@app.route('/about')
def about():
  return render_template('about.html')

@app.route('/advancedSearch')
def advancedSearch():
  form = AdvancedSearchForm()
  
  if request.method == 'POST':
    if form.validate() == False:
      return render_template('advancedSearch.html', form=form)
    else:   
      return "[1] Create a new user [2] sign in the user [3] redirect to the user's profile"
  
  elif request.method == 'GET':
    return render_template('advancedSearch.html', form=form)   

@app.route('/', methods=['GET', 'POST'])
def search():
  form = SearchForm()
  
  if request.method == 'POST':
    if form.validate() == False:
      return render_template('search.html', form=form)
    else:   
      return "[1] Create a new user [2] sign in the user [3] redirect to the user's profile"
  
  elif request.method == 'GET':
    return render_template('search.html', form=form)   

@app.route('/result', methods=['GET', 'POST'])
def result():
  return render_template('result.html', results=123)   

  #results = array('nome' => 'Anna', 'Estado'=> 'BA', 'partido' => 'vermelho', 'cargo' => 'rainha')


if __name__ == '__main__':
  app.run(debug=True)
