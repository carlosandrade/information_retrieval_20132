import nltk
from scrapy.spider import BaseSpider
from scrapy.selector import Selector
from crawler.items import IesItem
from nltk.corpus import stopwords

class UfmgSpider(BaseSpider):
  name = "ufrj"
  allowed_domains = ["cos.ufrj.br"]
  start_urls = [
     'http://www.cos.ufrj.br/index.php?option=com_pescstaff&Itemid=110&func=viewcategory&catid=100'
  ]

  # Funcao para remover stopwords de um texto
  def cleanup(self, text):
    # Obtem conjunto de stopwords em portugues, e as armazena
    stopset = set(stopwords.words('portuguese'))
    tokens = nltk.word_tokenize(text)    
    # Adiciona o token ao texto se o mesmo nao for uma stopword e possui tamanho maior que 2
    clean = [token for token in tokens if not token in stopset and len(token) > 2]
    return clean

  def parse(self, response):
    sel = Selector(response)
    teachers = sel.xpath('/html/body/div[4]/div[1]/div/table/tr')

    items = []

    for person in teachers:
      item = IesItem()
      item['nome'] = person.xpath('td[1]/a/b/text()').extract()
      item['area'] = person.xpath('td[3]/ul/li/span/text()').extract()
      item['cleanNome'] = self.cleanup(''.join(item['nome']))
      
      if (item['area']):
        yield item