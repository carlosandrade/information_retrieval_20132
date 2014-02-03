from scrapy.spider import BaseSpider
from scrapy.selector import Selector
from crawler.items import UniItem

class UniSpider(BaseSpider):
  name = "uni"
  allowed_domains = ["conteudoweb.capes.gov.br"]
  start_urls = [
     'http://conteudoweb.capes.gov.br/conteudoweb/ProjetoRelacaoCursosServlet?acao=pesquisarIes&codigoArea=10300007&descricaoArea=CI%CANCIAS+EXATAS+E+DA+TERRA+&descricaoAreaConhecimento=CI%CANCIA+DA+COMPUTA%C7%C3O&descricaoAreaAvaliacao=CI%CANCIA+DA+COMPUTA%C7%C3O'
  ]

  def parse(self, response):
    sel = Selector(response)
    sites = sel.xpath('//*[@id="tabela"]/tbody/tr')
    items = []

    for site in sites:
      item = UniItem()
      item['program'] = site.xpath('td[1]/a/text()').extract()
      item['ies'] = site.xpath('td[2]/text()').extract()
      item['uf'] = site.xpath('td[3]/text()').extract()
      item['m'] = site.xpath('td[4]/text()').extract()
      item['d'] = site.xpath('td[5]/text()').extract()
      item['f'] = site.xpath('td[6]/text()').extract()

    # request.meta['it'] = item
    # request = Request('http://www.dcc.ufmg.br/pos/pessoas/professores2.php?tipo=PE',
    #                   callback=self.parse_ufmg)

    return items

  def parse_ufmg(self, response):
    sel = Selector(response)
    teachers = sel.xpath('/html/body/table/tr[2]/td[2]/table/tr[2]/td[2]/table/tr[2]/td/table')

    items = []

    for person in teachers:
      item = IesItem2()
      it = response.meta['it']
      item['nome'] = person.xpath('tr/td/table/tr[1]/td[2]/strong/text()').extract()
      item['uf'] = it['uf']
      item['ies'] = it['ies']
      item['area'] = person.xpath('tr/td/table/tr[1]/td[2]/text()').extract()
      temp = str(item['area'])

      begin = temp.find(') ') + 2
      end = -1

      if temp[begin] == '"':
        begin+= 1
        end-= 1

      item['area'] = temp[begin:end]
      item['cleanNome'] = self.cleanup(''.join(item['nome']))

      items.append(item)

    return items