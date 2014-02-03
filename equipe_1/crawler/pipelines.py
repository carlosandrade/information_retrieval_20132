# -*- coding: utf-8 -*-
# Define your item pipelines here
#
# Don't forget to add your pipeline to the ITEM_PIPELINES setting
# See: http://doc.scrapy.org/en/latest/topics/item-pipeline.html

import nltk
import unicodedata
from nltk.corpus import stopwords

class CleanPipeline(object):
	def process_item(self, item, spider):
		# Obtem conjunto de stopwords em portugues, e as armazena
		stopset = set(stopwords.words('portuguese'))
		
		if item['nome']:
			tokens = nltk.word_tokenize(str(item['nome']))    
			# Adiciona o token ao texto se o mesmo nao for uma stopword e possui tamanho maior que 2
			item['cleanNome'] = [token for token in tokens if not token in stopset and len(token) > 2]
		elif item['program']:
			tokens = nltk.word_tokenize(str(item['program']))    
			# Adiciona o token ao texto se o mesmo nao for uma stopword e possui tamanho maior que 2
			item['cleanProgram'] = [token for token in tokens if not token in stopset and len(token) > 2]
		
		return item

# class RemoveAccentsPipeline(object):
	# def process_item(self, item, spider):
	# 	text = unicodedata.normalize("NFKD", unicode(str(item['nome']))
	# 	item['nome'] = list(text.encode("ascii", "ignore"))

	# 	text = unicodedata.normalize("NFKD", unicode(str(item['area']))
	# 	item['area'] = text.encode("ascii", "ignore")

	# 	return item