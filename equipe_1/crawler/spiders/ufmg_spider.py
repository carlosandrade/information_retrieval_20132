import nltk
from scrapy.spider import BaseSpider
from scrapy.selector import Selector
from crawler.items import IesItem
from nltk.corpus import stopwords

class UfmgSpider(BaseSpider):
  name = "ufmg"
  allowed_domains = ["dcc.ufmg.br"]
  start_urls = [
     'http://www.dcc.ufmg.br/pos/pessoas/professores2.php?tipo=PE'
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
    teachers = sel.xpath('/html/body/table/tr[2]/td[2]/table/tr[2]/td[2]/table/tr[2]/td/table')

    items = []

    for person in teachers:
      item = IesItem()
      item['nome'] = person.xpath('tr/td/table/tr[1]/td[2]/strong/text()').extract()
      item['cleanNome'] = self.cleanup(''.join(item['nome']))

      a = str(person.xpath('tr/td/table/tr[1]/td[2]/text()').extract())
      a = a.replace(' .','').replace('.','').replace('"','')
      begin = a.find(')')

      if begin == -1:
        a = a.replace('-', ')')
        begin = a.find(')') 
      
      begin += 2        
      
      item['area'] = a[begin:]     

      yield item