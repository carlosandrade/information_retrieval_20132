#!/usr/bin/env python
# -*- coding: utf-8 -*-

from flask import Flask
from flask import render_template
from flask_bootstrap import Bootstrap
import manindex
import mansearch

app = Flask(__name__)
Bootstrap(app)

@app.route('/')
def index():
    indexContent()
    return render_template('index.html')

def content(doc_id = None):
    pass#result = 

def search():
    pass

if __name__ == "__main__":
    app.run(debug=True)
