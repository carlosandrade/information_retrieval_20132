# Define your item pipelines here
#
# Don't forget to add your pipeline to the ITEM_PIPELINES setting
# See: http://doc.scrapy.org/en/latest/topics/item-pipeline.html

class TutorialPipeline(object):
    def process_item(self, item, spider):
        return item

class CleanPipeline(object):
	def process_item(self, item, spider):
		# Obtem conjunto de stopwords em portugues, e as armazena
		stopset = set(stopwords.words('portuguese'))
		if item['nome']:
			tokens = nltk.word_tokenize(item['nome'])    
		elif item['program']:
			tokens = nltk.word_tokenize(item['program'])    
		# Adiciona o token ao texto se o mesmo nao for uma stopword e possui tamanho maior que 2
		clean = [token for token in tokens if not token in stopset and len(token) > 2]
		return clean