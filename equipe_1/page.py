#!/usr/bin/env python
# -*- coding: utf-8 -*-
import sys, os, lucene
from flask import Flask
from flask import render_template
from flask import request
from flask_bootstrap import Bootstrap
import manindex
import mansearch
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
    #manindex.indexContent(indexDir)
    return render_template('index.html')

def content(doc_id = None):
    pass

@app.route('/search/',methods = ['GET','POST'])
def search():
	args = []
	if request.method == 'POST':
		vm_env = lucene.getVMEnv()
		if vm_env == None:
			lucene.initVM(vmargs=['-Djava.awt.headless=true'])
		if request.form['ies']:
			args.append('ies:'+request.form['ies'])
		if request.form['area']:
			args.append('area:'+request.form['area'])
		if request.form['professor']:
			args.append('professor:'+request.form['professor'])
		if request.form['uf']:
			args.append('uf:'+request.form['uf'])
		if request.form['conceito']:
			#args.append('m:'+request.form['conceito']+'d:'+request.form['conceito']+'f:'+request.form['conceito'])
			args.append('m:'+request.form['conceito'])
			args.append('d:'+request.form['conceito'])
	table = []
	if(len(args) > 0): 
		scoreDocs = mansearch.buscar('indexer/',args)
		fsDir = SimpleFSDirectory(File(indexDir))
		searcher = IndexSearcher(DirectoryReader.open(fsDir))
		for scoreDoc in scoreDocs:
			doc = searcher.doc(scoreDoc.doc)
			table.append(dict((field.name(), field.stringValue()) for field in doc.getFields()))
	return render_template('busca.html',table = table)
	
	pass

if __name__ == "__main__":
	app.run(debug=True)
