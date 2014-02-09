# -*- coding: utf-8 -*-

from scrapy.spider import BaseSpider
from scrapy.selector import Selector
from crawler.items import IesItem

class UfmgSpider(BaseSpider):
  name = "ufmg"
  allowed_domains = ["dcc.ufmg.br"]
  start_urls = [
     'http://www.dcc.ufmg.br/pos/pessoas/professores2.php?tipo=PE'
  ]
  
  def parse(self, response):
    sel = Selector(response)
    teachers = sel.xpath('/html/body/table/tr[2]/td[2]/table/tr[2]/td[2]/table/tr[2]/td/table')

    items = []

    for person in teachers:
      item = IesItem()
      item['nome'] = person.xpath('tr/td/table/tr[1]/td[2]/strong/text()').extract()
      
      a = person.xpath('tr/td/table/tr[1]/td[2]/text()').extract()
      a = a[1].replace(', ',',').replace(' .','').replace('.','').replace('"','')
      begin = a.find(')')

      if begin == -1:
        a = a.replace('-', ')')
        begin = a.find(')') 
      
      begin += 2
      
      item['area'] = a[begin:].split(',')
      yield item