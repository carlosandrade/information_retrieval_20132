# Define here the models for your scraped items
#
# See documentation in:
# http://doc.scrapy.org/en/latest/topics/items.html

from scrapy.item import Item, Field

class PoliticoItem(Item):
    nome = Field()
    aniversario = Field()
    profissao = Field()
    partido = Field()
    UF = Field()
    diplomacao = Field()
    telefone = Field()
    fax = Field()
    legislaturas = Field()
