# -*- coding: latin1 -*-
# PoliticoSpyder

import re
import unicodedata

import nltk
from nltk.corpus import stopwords

from scrapy.spider import BaseSpider
from scrapy.selector import Selector
from scrapy.http import FormRequest

from politico.items import PoliticoItem


class PoliticoSpyder(BaseSpider):

    name = 'camara'
    allowed_domains = ['camara.leg.br']
    start_urls = ['http://www2.camara.leg.br/deputados/pesquisa/']

    def parse(self, response):
        """
        Função principal da aranha, delega a atividade que ela exerce
        """

        sel = Selector(response)
        form = sel.xpath('//*[@id="formDepAtual"]')
        deputados = form.xpath('//select[@id="deputado"]/option')

        for deputado in deputados:
            yield FormRequest(
                url=form.xpath('@action').extract()[0],
                formdata={
                    'deputado': deputado.xpath('@value').extract()[0],
                    'rbDeputado': 'IC'
                },
                callback=self.save_deputado
            )

    def save_deputado(self, response):
        """
        Função que grava os dados coletados da aranha no "banco de dados"
        """

        sel = Selector(response)
        pol = PoliticoItem()

        pol['nome'] = sel.xpath(
            '//*[@id="content"]//ul[@class="visualNoMarker"]/li[1]/text()').extract()[0]

        print pol['nome']

        pol['clean_nome'] = self.cleanup(unicode(pol['nome']));

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

        return clean
