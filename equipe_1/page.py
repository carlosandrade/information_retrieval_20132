#!/usr/bin/env python
# -*- coding: utf-8 -*-

from flask import Flask
from flask import render_template
from pip import *
import mansearch 
import manindex
import setting
from java.io import File
from org.apache.lucene.analysis.standard import StandardAnalyzer
from org.apache.lucene.index import DirectoryReader
from org.apache.lucene.queryparser.classic import QueryParser
from org.apache.lucene.search import IndexSearcher
from org.apache.lucene.store import SimpleFSDirectory
from org.apache.lucene.util import Version

app = Flask(__name__)
Bootstrap(app)

indexDir = 'indexer'

@app.route('/')
def index():
    manindex.indexContent(indexDir)
    return render_template('index.html')

def content(doc_id = None):
    pass

@app.route('/search/?conceito=<int:conceito>')
@app.route('/search/?universidade=<universidade>')
@app.route('/search/?universidade=<universidade>&conceito=<int:conceito>')
def search(universidade = None,conceito = None):
	args = []
	if universidade != None:
		args.append('ies:'+universidade)
	if conceito != None:
		args.append('m:'+str(conceito))
	
	scoreDocs = mansearch.buscar('indexer/',args)
	
	fsDir = SimpleFSDirectory(File(indexDir))
    #print fsDir
    
    #Criando buscador baseado no diretorio dos indices passados pelo usuario
	searcher = IndexSearcher(DirectoryReader.open(fsDir))
    
	table = []
	for scoreDoc in scoreDocs:
		doc = searcher.doc(scoreDoc.doc)
		table.append(dict((field.name(), field.stringValue()) for field in doc.getFields()))

	return render_template('busca.html',table = table)
	
	pass

if __name__ == "__main__":
	app.run(debug=True)
