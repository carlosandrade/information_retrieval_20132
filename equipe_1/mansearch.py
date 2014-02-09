# ====================================================================
#   Licensed under the Apache License, Version 2.0 (the "License");
#   you may not use this file except in compliance with the License.
#   You may obtain a copy of the License at
#
#       http://www.apache.org/licenses/LICENSE-2.0
#
#   Unless required by applicable law or agreed to in writing, software
#   distributed under the License is distributed on an "AS IS" BASIS,
#   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
#   See the License for the specific language governing permissions and
#   limitations under the License.
# ====================================================================
#
# Author: Erik Hatcher
#
# to query the index generated with manindex.py
#  python mansearch.py <query>
# by default, the index is stored in 'pages', which can be overriden with
# the MANDEX environment variable
# ====================================================================


import sys, os, lucene

from string import Template
from datetime import datetime
from getopt import getopt, GetoptError
# from pip import liberate

from java.io import File
from org.apache.lucene.analysis.standard import StandardAnalyzer
from org.apache.lucene.index import DirectoryReader
from org.apache.lucene.queryparser.classic import QueryParser
from org.apache.lucene.search import IndexSearcher
from org.apache.lucene.store import SimpleFSDirectory
from org.apache.lucene.util import Version

def buscar(indexDir, args,options = None):
    lucene.initVM(vmargs=['-Djava.awt.headless=true'])
    
    fsDir = SimpleFSDirectory(File(indexDir))
    #print fsDir
    
    #Criando buscador baseado no diretorio dos indices passados pelo usuario
    searcher = IndexSearcher(DirectoryReader.open(fsDir))
    
    #Analizador para filtro dos tokens 
    analyzer = StandardAnalyzer(Version.LUCENE_CURRENT)
    #print analyzer

    #Criando um QueryParser usando por padrao contents
    #Variavel com as restricoes da busca
    parser = QueryParser(Version.LUCENE_CURRENT, "keywords", analyzer)
    #print parser

    parser.setDefaultOperator(QueryParser.Operator.AND)

    #Juntando parametros passados com o valor do mesmo
    command = ' '.join(args)
    #print command

    query = parser.parse(command)
    #print query

    #Criando um JArray com resultado da consulta
    return searcher.search(query, 200).scoreDocs
    #print scoreDocs

def printDoc(indexDir,scoreDocs,args,stats,duration):
    
    """
    formato: IES , Nota Doutorado , Nota Mestrado , UF , Nota mestrado Profissional , Programa 
    """
    format =" #ies , #d , #m , #uf , #f , #program , #professor "
    #print indexDir

    class CustomTemplate(Template):
        delimiter = '#'

    template = CustomTemplate(format)
    
    fsDir = SimpleFSDirectory(File(indexDir))
    #print fsDir
    
    #Criando buscador baseado no diretorio dos indices passados pelo usuario
    searcher = IndexSearcher(DirectoryReader.open(fsDir))
    
    #imprimindo a quantidade e os documentos que tem a consulta feita
    if stats:

        #Juntando parametros passados com o valor do mesmo
        command = ' '.join(args)
        #print command

        print >>sys.stderr, "Encontrado %d documento(s) (em %s) com consulta igual a '%s':" %(len(scoreDocs), duration,command)

    newTable = []

    for scoreDoc in scoreDocs:
        doc = searcher.doc(scoreDoc.doc)
        table = dict((field.name(), field.stringValue()) for field in doc.getFields())
        newTable.append(template.substitute(table).split(","))

    if newTable :
        headers = ["IES"," Nota Doutorado", " Nota Mestrado "," UF "," Nota mestrado Profissional "," Programa ","Professor"]
        print tabulate(newTable,headers,tablefmt="grid")

def usage():
    print sys.argv[0], "[--format=<format string>] [--index=<index dir>] [--stats] <query...>"
    print "default index is found from MANDEX environment variable"

try:
    options, args = getopt(sys.argv[1:], '', ['format=', 'index=', 'stats'])
except GetoptError:
    usage()
    sys.exit(2)

#Pegando o diretorio onde estao os indices pela variavel de ambiente MANDEX ou por padrao serah pages/
indexDir = os.environ.get('MANDEX') or 'indexer/'

#Verificando as opcoes passado pelo usuario --format > formato da string --index > diretorio do indices --stats status da consulta
stats = False
for o, a in options:
    if o == "--format":
        format = a
    elif o == "--index":
        indexDir = a
    elif o == "--stats":
        stats = True


#Horario atual
start = datetime.now()
scoreDocs = buscar(indexDir, args, options)
duration = datetime.now() - start
printDoc(indexDir,scoreDocs, args, stats,duration)
