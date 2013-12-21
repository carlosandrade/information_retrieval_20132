## Executando Crawler

Na pasta contendo este README, execute:

	scrapy crawl uni -o <nome_do_arquivo_de_saida>.json -t json

O Crawler executará, e escreverá seu output no arquivo especificado

## Items

Dentro de crawler/items.py temos as definiçoes dos itens de interesse do crawler os quais nos nosso caso seriam:

	Program - O curso a que se refere o programa de pos graduação.
	IES - A instituição que oferece o programa.
	UF - O estado onde a IES esta situada.
	M - Nota para Mestrado Academico
	D - Nota para Doutorado
	F - Nota para Mestrado Profissional

Obs: CleanProgram nada mais é que o campo program, porem com a remoção de stopwords portuguesas

## Spider

Dentro de crawler/spiders/uni_spider.py, no metodo parse, temos o codigo do spider responsavel por minerar os itens desejados

Como toda a informação desejada se encontra no formato de tabela, realizou-se um loop para cada linha da tabela e em cada uma delas remove-se os dados necessarios, de acordo com sua coluna. Finalmente, cada linha da tabela é anexada a lista final de itens, que é entao escrita no arquivo json de saida.