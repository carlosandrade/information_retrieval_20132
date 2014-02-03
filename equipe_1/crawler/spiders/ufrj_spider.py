from scrapy.spider import BaseSpider
from scrapy.selector import Selector
from crawler.items import IesItem

class UfmgSpider(BaseSpider):
  name = "ufrj"
  allowed_domains = ["cos.ufrj.br"]
  start_urls = [
     'http://www.cos.ufrj.br/index.php?option=com_pescstaff&Itemid=110&func=viewcategory&catid=100'
  ]

  def parse(self, response):
    sel = Selector(response)
    teachers = sel.xpath('/html/body/div[4]/div[1]/div/table/tr')

    items = []

    for person in teachers:
      item = IesItem()
      item['nome'] = person.xpath('td[1]/a/b/text()').extract()
      item['area'] = person.xpath('td[3]/ul/li/span/text()').extract()
      
      if (item['area']):
        yield item