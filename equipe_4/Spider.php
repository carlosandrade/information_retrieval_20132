<?php
include 'vendor/autoload.php';

use Symfony\Component\DomCrawler\Crawler;

class Spider {
    /**
     * PHP Crawler objeto
     *
     * @var Goutte\Client
     */
    protected  $goutte = null;

    /**
     * Lista de stop words
     *
     * @var array
     */
    protected $stop_words = array(
        '/\ba\b/i', '/\bainda\b/i', '/\balem\b/i', '/\bambas\b/i', '/\bambos\b/i', '/\bantes\b/i',
        '/\bao\b/i', '/\baonde\b/i', '/\baos\b/i', '/\bapos\b/i', '/\baquele\b/i', '/\baqueles\b/i',
        '/\bas\b/i', '/\bassim\b/i', '/\bcom\b/i', '/\bcomo\b/i', '/\bcontra\b/i', '/\bcontudo\b/i',
        '/\bcuja\b/i','/\bcujas\b/i', '/\bcujo\b/i', '/\bcujos\b/i', '/\bda\b/i', '/\bdas\b/i', '/\bde\b/i',
        '/\bdela\b/i', '/\bdele\b/i', '/\bdeles\b/i', '/\bdemais\b/i', '/\bdepois\b/i', '/\bdesde\b/i',
        '/\bdesta\b/i', '/\bdeste\b/i', '/\bdispoe\b/i', '/\bdispoem\b/i', '/\bdiversa\b/i',
        '/\bdiversas\b/i', '/\bdiversos\b/i', '/\bdo\b/i', '/\bdos\b/i', '/\bdurante\b/i', '/\be\b/i',
        '/\bela\b/i', '/\belas\b/i', '/\bele\b/i', '/\beles\b/i', '/\bem\b/i', '/\bentao\b/i', '/\bentre\b/i',
        '/\bessa\b/i', '/\bessas\b/i', '/\besse\b/i', '/\besses\b/i', '/\besta\b/i', '/\bestas\b/i',
        '/\beste\b/i', '/\bestes\b/i', '/\bha\b/i', '/\bisso\b/i', '/\bisto\b/i', '/\blogo\b/i', '/\bmais\b/i',
        '/\bmas\b/i', '/\bmediante\b/i', '/\bmenos\b/i', '/\bmesma\b/i', '/\bmesmas\b/i', '/\bmesmo\b/i',
        '/\bmesmos\b/i', '/\bna\b/i', '/\bnas\b/i', '/\bnao\b/i', '/\bnas\b/i', '/\bnem\b/i', '/\bnesse\b/i', '/\bneste\b/i',
        '/\bnos\b/i', '/\bo\b/i', '/\bos\b/i', '/\bou\b/i', '/\boutra\b/i', '/\boutras\b/i', '/\boutro\b/i', '/\boutros\b/i',
        '/\bpelas\b/i', '/\bpelas\b/i', '/\bpelo\b/i', '/\bpelos\b/i', '/\bperante\b/i', '/\bpois\b/i', '/\bpor\b/i',
        '/\bporque\b/i', '/\bportanto\b/i', '/\bproprio\b/i', '/\bpropios\b/i', '/\bquais\b/i', '/\bqual\b/i',
        '/\bqualquer\b/i', '/\bquando\b/i', '/\bquanto\b/i', '/\bque\b/i', '/\bquem\b/i', '/\bquer\b/i', '/\bse\b/i',
        '/\bseja\b/i', '/\bsem\b/i', '/\bsendo\b/i', '/\bseu\b/i', '/\bseus\b/i', '/\bsob\b/i', '/\bsobre\b/i', '/\bsua\b/i',
        '/\bsuas\b/i', '/\btal\b/i', '/\btambem\b/i', '/\bteu\b/i', '/\bteus\b/i', '/\btoda\b/i', '/\btodas\b/i', '/\btodo\b/i',
        '/\btodos\b/i', '/\btua\b/i', '/\btuas\b/i', '/\btudo\b/i', '/\bum\b/i', '/\buma\b/i', '/\bumas\b/i', '/\buns\b/i'
    );

    /**
     * Configuraçoes dos sites que serao scaneados
     *
     * @var array
     */
    protected $websites = array(
        array(
            'name' => 'galaticosonline',
            'baseUrl' => 'http://www.galaticosonline.com',
            'paginationLimit' => 2,
            'startPage' => 0,
            'list' => array(
                'typeLink' => 'relative',
                'endPoint' => '/noticias,,{page}.html',
                'filter' => '//div[@class="posts"]/ul/li/div[@class="description clearfix "]/h2/a',
            ),
            'scan' => array(
                'title' => '//h1[@class="box-titulo"]',
                'text' => '//div[@class="single-content"]/p',
            )
        ),
        array(
            'name' => 'globoesporte',
            'baseUrl' => 'http://globoesporte.globo.com',
            'paginationLimit' => 3,
            'startPage' => 1,
            'list' => array(
                'typeLink' => 'absolut',
                'endPoint' => '/futebol/noticia/plantao/{page}.html',
                'filter' => '//li[@class="chamada item-noticia-plantao"]/a',
            ),
            'scan' => array(
                'title' => '//div[@class="materia-titulo"]/h1',
                'text' => '//div[@class="corpo-conteudo"]/p',
            )
        ),
    );

    /**
     * Lista de links para scanear
     *
     * @var array
     */
    protected $list = array();

    /**
     * Resultados do scaneamento
     *
     * @var array
     */
    protected $result = array();

    /**
     * Executa o crawler
     */
    public function run()
    {
        $this->goutte = new Goutte\Client(); // instancia lib do crawler

        //echo "<pre>";
        foreach ($this->websites as $website) {
            $this->getList($website); // Pega lista de links
            $this->scan($website); // Scaneia links da lista
            $this->list = array(); // Limpa lista
        }

        //print_r($this->list);
        //print_r($this->result);
    }

    /**
     * Ler site e cria lista das paginas a serem scaneadas e capturadas
     *
     * @param $website
     */
    public function getList($website)
    {
        // para quantidade de paginas configuradas
        for ($page = $website['startPage']; $page < $website['paginationLimit']; $page++) {
            // Monta url de destino
            $url = $website['baseUrl'] . str_replace('{page}', $page, $website['list']['endPoint']);

            // Pega pagina
            $crawler = $this->goutte->request('GET', $url);

            // Filtras no deseja
            $list = $crawler->filterXPath($website['list']['filter'])
                ->each(
                    function ($node, $i) {
                        return trim($node->attr('href')); // coloca na lista
                    }
                );

            $this->list = array_merge($this->list, $list); // junta links das paginas
        }
    }

    /**
     * Scaneia link e recupera informaçoes configuradas
     *
     * @param $website
     */
    public function scan($website)
    {
        // para cada link na lista
        foreach ($this->list as $list) {
            $url = $list; // Monta url

            // Monta url
            if ($website['list']['typeLink'] == 'relative') {
                $url = $website['baseUrl'] . $list;
            }

            // Pega pagina
            $crawler = $this->goutte->request('GET', $url);

            // Pega informaçoes da pagina
            $data = array();
            foreach ($website['scan'] as $key => $filter) {
                $str = strip_tags($crawler->filterXPath($filter)->text());
                $str = preg_replace($this->stop_words, "", $str);
                $data[$key] = $str;
            }

            // coloca na lista de resultados
            $this->result[$website['name']][] = $data;
        }
    }

    /**
     * Salva resultados em json
     *
     * @param null $dir
     */
    public function save($dir = null) {
        if (is_null($dir)) {
            $dir = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "data" .DIRECTORY_SEPARATOR . "json";
        }

        foreach ($this->result as $entry => $data) {
            file_put_contents($dir . DIRECTORY_SEPARATOR . $entry . ".json", json_encode($data));
        }
    }
}

$sp = new Spider();
$sp->run();
$sp->save();
