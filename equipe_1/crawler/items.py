# Define here the models for your scraped items
#
# See documentation in:
# http://doc.scrapy.org/en/latest/topics/items.html

from scrapy.item import Item, Field

class UniItem(Item):
	cleanProgram = Field()
	program = Field()
	ies = Field()
	uf = Field()
	m = Field()
	d = Field()
	f = Field()

class IesItem(Item):
	nome = Field()
	area = Field()
	cleanNome = Field()

class IesItem2(Item):
	nome = Field()
	area = Field()
	cleanNome = Field()
	uf = Field()
	ies = Field()