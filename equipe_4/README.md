# Recuperaçao da Informaçao

A idéia do buscador é pegar notícias de vários sites (e.g.: Uol esportes, globo esporte), e reuni-las
em nosso sistema de busca de forma que o usuário possa obter informações de múltiplas fontes, e não de
uma única como acontece atualmente, e oferecer mecanismos de busca eficientes para se encontrar uma
informação, e ainda uma organização eficiente para facilitar a visualização das notícias.

# Primeira Interaçao
 - Crawler utilizando a biblioteca Goutte
 - Indexaçao e Busca utilizando biblioteca ZendSearch
 - Prototipo html da interface de busca

# Segunda Interaçao
 - Text Mining: Classificador das noticias utilizando Naive Bayes
 - Melhorias no crawler: UserAgent, Timming
 - Melhorias na indexaçao e busca

 Para instalar:

 ```
 curl -s https://getcomposer.org/installer | php
 php composer.phar install
 ```

 Executar o crawler:
 ```
 php Spider.php
 ```

 Executar o indexador:
 ```
 php Indexer.php
 ```

 Executar a busca:
 Dentro do diretorio do projeto...
 ```
 php -S localhost:8080
 ```
 Ir no browser, digitar o endereço http://localhost:8080 e buscar :p