# PoliticoSpyder
import re

from scrapy.spider import BaseSpider
from scrapy.selector import Selector
from scrapy.http import FormRequest

from politico.items import PoliticoItem

class PoliticoSpyder(BaseSpider):

    name = 'camara'
    allowed_domains = ['camara.leg.br']
    start_urls = ['http://www2.camara.leg.br/deputados/pesquisa/']

    def parse(self, response):
        self.itens = []

        sel = Selector(response)
        form = sel.xpath('//*[@id="formDepAtual"]')
        deputados = form.xpath('//select[@id="deputado"]/option')

        pol = PoliticoItem()

        for deputado in deputados:
            pol = FormRequest(
                url=form.xpath('@action').extract()[0],
                formdata={
                    'deputado': deputado.xpath('@value').extract()[0],
                    'rbDeputado': 'IC'
                },
                meta={'pol': pol},
                callback=self.save_deputado
            )
            
            self.itens.append(pol)

        return self.itens

    def save_deputado(self, response):
        sel = Selector(response)
        pol = response.meta['pol']

        #pol = PoliticoItem()

        pol['nome'] = sel.xpath('//*[@id="content"]//ul[@class="visualNoMarker"]/li[1]/text()').extract()[0]

        pol['aniversario'] = self.format(sel.xpath(
            '//*[@id="content"]//ul[@class="visualNoMarker"]/li[2]/text()').extract()[0],
            r"(\d*) / (\d*) .*", "\\1/\\2")

        pol['partido'] = self.format(sel.xpath(
            '//*[@id="content"]//ul[@class="visualNoMarker"]/li[3]/text()').extract()[0],
            r"(.*?) / (.*?) / (.*)", 1)

        pol['UF'] = self.format(sel.xpath(
            '//*[@id="content"]//ul[@class="visualNoMarker"]/li[3]/text()').extract()[0],
            r"(.*?) / (.*?) / (.*)", 2)

        pol['diplomacao'] = self.format(sel.xpath(
            '//*[@id="content"]//ul[@class="visualNoMarker"]/li[3]/text()').extract()[0],
            r"(.*?) / (.*?) / (.*)", 3)

        pol['telefone'] = self.format(sel.xpath(
            '//*[@id="content"]//ul[@class="visualNoMarker"]/li[4]/text()').extract()[0],
            r"(\(\d*?\)) ([\d-]*) .*", "\\1 \\2")

        pol['legislaturas'] = self.find(sel.xpath(
            '//*[@id="content"]//ul[@class="visualNoMarker"]/li[5]/text()').extract()[0],
            r"\d{2}/\d{2}")

        #itens.append(pol)
        return pol

    def format(self, data, format, filter):
        if type(filter) == int:
            filter = "\\" + str(filter)

        _result = re.subn(format, filter, data)
        _data = _result[0] if _result[1] >= 1 else ""

        re.purge()
        return _data

    def find(self, data, format):
        return re.findall(format, data)
