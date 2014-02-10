# -*- coding: latin1 -*-
# PoliticoSpyder

import re
import unicodedata

import nltk
from nltk.corpus import stopwords
from scrapy.utils.response import open_in_browser

from scrapy.spider import BaseSpider
from scrapy.selector import Selector
from scrapy.http import FormRequest
from scrapy.http import Request

from politico.items import PoliticoItem


class PoliticoSpyder(BaseSpider):

    name = 'camara'
    #sallowed_domains = ['camara.leg.br', 'google.com', 'google.com.br']
    start_urls = [
        'http://www2.camara.leg.br/deputados/pesquisa/']

    def parse(self, response):
        """
        Função principal da aranha, delega a atividade que ela exerce
        """

        sel = Selector(response)
        form = sel.xpath('//*[@id="formDepAtual"]')
        deputados = form.xpath('//select[@id="deputado"]/option')[1:-1]

        for deputado in deputados[0:5]:
        #deputado = deputados[0]
            yield FormRequest(
                url=form.xpath('@action').extract()[0],
                formdata={
                    'deputado': deputado.xpath('@value').extract()[0],
                    'rbDeputado': 'IC'
                },
                callback=self.save_deputado,
                meta={'nome': deputado.xpath('text()').extract()[0]}
            )

    def save_deputado(self, response):
        """
        Função que grava os dados coletados da aranha no "banco de dados"
        """

        sel = Selector(response)
        pol = PoliticoItem()

        print "dados de " + response.meta['nome']

        pol['apelido'] = response.meta['nome']

        pol['nome'] = self.format(sel.xpath(
            '//*[@id="content"]//ul[@class="visualNoMarker"]/li[1]/text()').extract()[0],
            r" (.*)", 1)

        pol['clean_nome'] = self.cleanup(unicode(pol['nome'] + " " + pol['apelido']));

        pol['aniversario'] = self.format(sel.xpath(
            '//*[@id="content"]//ul[@class="visualNoMarker"]/li[2]/text()').extract()[0],
            r" (\d*) / (\d*) .*", "\\1/\\2")

        pol['partido'] = self.format(sel.xpath(
            '//*[@id="content"]//ul[@class="visualNoMarker"]/li[3]/text()').extract()[0],
            r" (.*?) / (.*?) / (.*)", 1)

        pol['UF'] = self.format(sel.xpath(
            '//*[@id="content"]//ul[@class="visualNoMarker"]/li[3]/text()').extract()[0],
            r"(.*?) / (.*?) / (.*)", 2)

        pol['diplomacao'] = self.format(sel.xpath(
            '//*[@id="content"]//ul[@class="visualNoMarker"]/li[3]/text()').extract()[0],
            r"(.*?) / (.*?) / (.*)", 3)

        pol['telefone'] = self.format(sel.xpath(
            '//*[@id="content"]//ul[@class="visualNoMarker"]/li[4]/text()').extract()[0],
            r" (\(\d*?\)) ([\d-]*) .*", "\\1 \\2")

        pol['legislaturas'] = self.find(sel.xpath(
            '//*[@id="content"]//ul[@class="visualNoMarker"]/li[5]/text()').extract()[0],
            r"\d{2}/\d{2}")

        #pegando email

        try:
            email = sel.xpath('//*[@id="content"]/div/div[3]/ul/li[4]/a/text()').extract()[0]
            pol['email'] = email if(re.search('@', email)) else ""
        except Exception, e:
            pol['email'] = ""
            pass

        yield Request(
            'https://www.google.com.br/search?q=' + response.meta['nome'].replace(' ', '+'),
            method='GET',
            callback=self.save_biografia,
            meta={'politico': pol}
        )

        #return pol

    def save_biografia(self, response):
        """
        Retorno do site de Pesquisa do Google, verifica se ha uma biografia associada a pesquisa
        """

        sel = Selector(response)
        pol = response.meta['politico']

        try:
            bio = sel.xpath('//*[@id="kno-result"]/div/ol/div[1]/div[1]/div[4]/li/div/div[1]/span[1]/text()').extract()
            pol['biografia'] = "\n".join(bio) if bio else "Sem detalhes sobre esse deputado..."
        except Exception, e:
            pol['biografia'] = "Sem detalhes sobre esse deputado..."
            pass

        return pol

    def format(self, data, format, filter):
        """
        Função para tratar o campo o texto coletado da aranha

        :param format: O formato esperado.
        :param filter: O formato de saida.
        """

        if type(filter) == int:
            filter = "\\" + str(filter)

        _result = re.subn(format, filter, data)
        _data = _result[0] if _result[1] >= 1 else ""

        re.purge()
        return _data

    def find(self, data, format):
        """
        Função para tratar o campo o texto coletado da aranha,
        retorna todas as ocorrencias de formart

        :param format: O formato esperado.
        """

        return re.findall(format, data)

    def cleanup(self, text):
        """
        Função de preprocessamento de texto. Remove os acentos e palavras
        não muito importantes.

        :param text: text a ser manipulado;
        """

        try:
            text = unicodedata.normalize('NFKD', text).encode('ascii', 'ignore')
        except TypeError:
            pass

        stopset = set(stopwords.words('portuguese'))
        tokens = nltk.word_tokenize(text)

        clean = [
            token for token in tokens if not token in stopset and len(token) > 2
        ]

        clean = list(set(clean))

        return clean
