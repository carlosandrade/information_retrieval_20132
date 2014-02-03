# Define here the models for your scraped items
#
# See documentation in:
# http://doc.scrapy.org/en/latest/topics/items.html

from scrapy.item import Item, Field

class PoliticoItem(Item):
    nome = Field()
    clean_nome = Field()
    aniversario = Field()
    partido = Field()
    UF = Field()
    diplomacao = Field()
    telefone = Field()
    legislaturas = Field()
    email = Field()
    biografia = Field()
