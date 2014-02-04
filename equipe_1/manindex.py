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
# to index all man pages on $MANPATH or /usr/share/man:
#   python manindex.py pages
# ====================================================================

import os, re, sys, lucene, json

from subprocess import *

from java.io import File
from org.apache.lucene.analysis.miscellaneous import LimitTokenCountAnalyzer
from org.apache.lucene.analysis.standard import StandardAnalyzer
from org.apache.lucene.index import IndexWriter, IndexWriterConfig
from org.apache.lucene.document import Document, Field, StringField, TextField
from org.apache.lucene.store import SimpleFSDirectory
from org.apache.lucene.util import Version

def indexDirectory(dir,writer):
    for name in os.listdir(dir):
        path = os.path.join(dir, name)
        if os.path.isfile(path):
            indexFile(dir, name,writer)


def indexFile(dir, filename, writer):
    path = os.path.join(dir, filename)
    print "\tArquivo: ", filename

    if filename.endswith('.gz'):
        child = Popen('gunzip -c ' + path + ' | groff -t -e -E -mandoc -Tascii | col -bx', shell=True, stdout=PIPE, cwd=os.path.dirname(dir)).stdout
        command, section = re.search('^(.*)\.(.*)\.gz$', filename).groups()
    else:
        child = Popen('groff -t -e -E -mandoc -Tascii ' + path + ' | col -bx',
            shell=True, stdout=PIPE, cwd=os.path.dirname(dir)).stdout
        command, section = re.search('^(.*)\.(.*)$', filename).groups()

    #print child
    #print command 
    #print section

    json_data = open(path)
    dados = json.load(json_data)
    
    data = child.read()
    err = child.close()

    if err:
        raise RuntimeError, '%s failed with exit code %d' %(command, err)

    for ln in dados:
        """ Nota doutorado, 
            mestrado,
            mestrado profissional,
            instituicao de ensino superior,
            uf da ies,
            programa,
            programa sem stopword,
            nome do arquivo,
            respectivamente. 
        """
        d = ln['d'][0].strip()
        m = ln['m'][0].strip()
        f = ln['f'][0].strip()
        ies = ln['ies'][0].strip().lower()
        uf = ln['uf'][0].strip().lower()
        program = ln['program'][0].strip().lower()
        cleanProgram = ""
        for cp in ln['cleanProgram']:
            cleanProgram += cp.strip().lower()

        doc = Document()
        doc.add(Field("d",d, StringField.TYPE_STORED))
        doc.add(Field("m",m, StringField.TYPE_STORED))
        doc.add(Field("f",f, StringField.TYPE_STORED))
        doc.add(Field("ies",ies, StringField.TYPE_STORED))
        doc.add(Field("uf",uf, StringField.TYPE_STORED))
        doc.add(Field("program",program, TextField.TYPE_STORED))
        doc.add(Field("cleanProgram",cleanProgram, TextField.TYPE_STORED))
        doc.add(Field("keywords", ' '.join((d, m, f, ies,uf,program,cleanProgram)),
                  TextField.TYPE_NOT_STORED))
        doc.add(Field("filename", os.path.abspath(path), StringField.TYPE_STORED))
        writer.addDocument(doc)

def indexContent(indexDir):
    lucene.initVM(vmargs=['-Djava.awt.headless=true'])
    
    """ Pegando direitorio passado pelo parametro"""
    directory = SimpleFSDirectory(File(indexDir)) 
    analyzer = StandardAnalyzer(Version.LUCENE_CURRENT)
    analyzer = LimitTokenCountAnalyzer(analyzer, 10000)
    config = IndexWriterConfig(Version.LUCENE_CURRENT, analyzer)
    writer = IndexWriter(directory, config)
    
    manpath = os.environ.get('MANPATH', '/home/massilva/Documentos/Ogri/Codigo/information_retrieval_20132/equipe_1/jsons/').split(os.pathsep)

    for dir in manpath:
        print "Crawling", dir
        for name in os.listdir(dir):
            path = os.path.join(dir, name)
            if os.path.isdir(path):
                indexDirectory(path,writer)
    writer.commit()
    writer.close()
