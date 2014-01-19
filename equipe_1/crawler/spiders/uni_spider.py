import nltk
from scrapy.spider import BaseSpider
#from scrapy.contrib.spiders import CrawlSpider, Rule
#from scrapy.contrib.linkextractors.sgml import SgmlLinkExtractor
from scrapy.selector import Selector
from crawler.items import UniItem
from nltk.corpus import stopwords

class UniSpider(BaseSpider):
  name = "uni"
  allowed_domains = ["conteudoweb.capes.gov.br"]
  start_urls = [
     'http://conteudoweb.capes.gov.br/conteudoweb/ProjetoRelacaoCursosServlet?acao=pesquisarIes&codigoArea=10300007&descricaoArea=CI%CANCIAS+EXATAS+E+DA+TERRA+&descricaoAreaConhecimento=CI%CANCIA+DA+COMPUTA%C7%C3O&descricaoAreaAvaliacao=CI%CANCIA+DA+COMPUTA%C7%C3O'
  ]

 #rules = (Rule ( SgmlLinkExtractor( allow=("conteudoweb.capes.gov.br", ), ), callback="parse_items", follow= True),)

  # Funcao para remover stopwords de um texto
  def cleanup(self, text):
    # Obtem conjunto de stopwords em portugues, e as armazena
    stopset = set(stopwords.words('portuguese'))
    tokens = nltk.word_tokenize(text)
    # Adiciona o token ao texto se o mesmo nao for uma stopword e possui tamanho maior que 2
    clean = [token for token in tokens if not token in stopset and len(token) > 2]
    return clean

  def parse_items(self, response):
    sel = Selector(response)
    sites = sel.xpath('//*[@id="tabela"]/tbody/tr')
    items = []

    for site in sites:
      item = UniItem()
      item['cleanProgram'] = self.cleanup(' '.join(site.xpath('td[1]/a/text()').extract()))
      item['program'] = site.xpath('td[1]/a/text()').extract()
      item['ies'] = site.xpath('td[2]/text()').extract()
      item['uf'] = site.xpath('td[3]/text()').extract()
      item['m'] = site.xpath('td[4]/text()').extract()
      item['d'] = site.xpath('td[5]/text()').extract()
      item['f'] = site.xpath('td[6]/text()').extract()

      items.append(item)

    return items