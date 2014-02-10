<?php
include 'vendor/autoload.php';
include 'InformationRetrieval/Analyzer/Stemmer.php';

use InformationRetrieval\Analyzer\Stemmer;

/**
 * Class Spider
 *
 * Robo que faz extracao dos dados dos sites definidos
 */
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
            'name' => 'globoesporte',
            'baseUrl' => 'http://globoesporte.globo.com',
            'paginationLimit' => 100,
            'startPage' => 1,
            'list' => array(
                'typeLink' => 'absolut',
                'endPoint' => '/futebol/noticia/plantao/{page}.html',
                'filter' => '//li[@class="chamada item-noticia-plantao"]/a',
            ),
            'scan' => array(
                'title' => '//div[@class="materia-titulo"]/h1',
                'text' => '//div[@class="corpo-conteudo"]/p',
                'datetime' => '//abbr[@class="published"]/time',
            ),
        ),
        array(
            'name' => 'terra',
            'baseUrl' => 'http://esportes.terra.com.br',
            'paginationLimit' => 100,
            'startPage' => 1,
            'list' => array(
                'typeLink' => 'absolut',
                'endPoint' => '/futebol/ultimas/?&vgnpage={page}',
                'filter' => '//div[@class="list articles"]/ol/li/a',
            ),
            'scan' => array(
                'title' => '//h2[@class="ttl-main"]',
                'text' => '//div[@id="news-container"]/p',
                'datetime' => '//time[@itemprop="datePublished"]',
            )
        ),
        array(
            'name' => 'lancenet',
            'baseUrl' => 'http://www.lancenet.com.br',
            'paginationLimit' => 100,
            'startPage' => 1,
            'list' => array(
                'typeLink' => 'relative',
                'endPoint' => '/futebol/?page={page}',
                'filter' => '//div[@class="mt"]/a[1]',
            ),
            'scan' => array(
                'title' => '//h1[@class="article-title"]',
                'text' => '//div[@class="mt news-body"]/p',
                'datetime' => '//div[@class="mt news-body"]/p[@class="date"]/span/span',
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
        // instancia lib do crawler
        // Define user agent como browser chrome
        $this->goutte = new Goutte\Client(
            array('HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1667.0 Safari/537.36')
        );

        //  Percorre difinicoes dos sites
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
            echo "List data from {$website['name']} page {$page}\n";
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
            $wait = rand(5, 10);
            echo "Waiting {$wait} seconds...\n";
            sleep($wait);
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
                if (!strpos($list, "http")) {
                    $url = $website['baseUrl'] . "/" . $list;
                }
            }

            echo "Get data from page {$url}\n";
            // Pega pagina
            $crawler = $this->goutte->request('GET', $url);

            // Pega informaçoes da pagina
            $data = array(
                'url' => $url,
                'taxonomy' => '',
            );
            foreach ($website['scan'] as $key => $filter) {
                try {
                    $elem = $crawler->filterXPath($filter);

                    if (count($elem) > 1) {
                        $str = '';
                        foreach ($elem as $el) {
                            $str .= " " . $el->textContent;
                        }
                    } else {
                        $str = $elem->text();
                    }
                    //$str = strip_tags($crawler->filterXPath($filter)->text());
                    $strProccess = preg_replace($this->stop_words, "", $str);
                    $data['taxonomy'] = $this->stem($strProccess, $data['taxonomy']);
                    $data[$key] = $this->callbacks($website['name'], $key, $str);

                    if ($key == 'datetime') {
                        echo "$str\n";
                        echo "{$data[$key]}\n\n";
                    }
                } catch (Exception $e) {
                    $data[$key] = '';
                }
            }

            // coloca na lista de resultados
            $this->result[$website['name']][] = $data;
            $wait = rand(5, 10);
            echo "Waiting {$wait} seconds...\n";
            sleep($wait);
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

    /**
     * Stemming dos dados extraidos
     *
     * @param $str
     * @param string $taxonomy
     * @return string
     */
    public function stem($str, $taxonomy = '') {
        $stemmer = new Stemmer();
        $tokens = explode(' ', $str);
        $tax = array();

        if (!empty($taxonomy)) {
            $tax = explode(' ', $taxonomy);
        }

        foreach ($tokens as $token) {
            $term = iconv("ASCII", "utf-8", $token);
            $stem = $stemmer->stem($term);

            if (!empty($stem) && !in_array($stem, $tax)) {
                $tax[] = $stem;
            }
        }

        return implode(' ', $tax);
    }

    public function callbacks($website, $key, $value)
    {
        $callbacks = array(
            'globoesporte' => array(
                'datetime' => function($str) {
                    $pieces = explode("/", explode(" ", $str)[0]);

                    return implode("", array_reverse($pieces));
                },
            ),
            'lancenet' => array(
                'datetime' => function($str) {
                    $pieces = explode("/", explode(" ", $str)[0]);

                    return implode("", array_reverse($pieces));
                },
            ),
            'terra' => array(
                'datetime' => function($str) {
                    $pieces = explode(" ", $str);

                    $month = array(
                        'Janeiro' => '01',
                        'Fevereiro' => '02',
                        'Março' => '03',
                        'abril' => '04',
                        'Maio' => '05',
                        'Junho' => '06',
                        'Julho' => '07',
                        'Agosto' => '08',
                        'Setembro' => '09',
                        'Outubro' => '10',
                        'Novembro' => '11',
                        'Dezembro' => '12',
                    );

                    return $pieces[4] . $month[$pieces[2]] . $pieces[0];
                },
            )
        );

        if (!empty($callbacks[$website][$key])) {
            return $callbacks[$website][$key]($value);
        }

        return $value;
    }
}

ini_set('display_errors', 1);
error_reporting(E_ALL);
// Roda o spider
echo "Start\n";
$sp = new Spider();
$sp->run();
$sp->save();
echo "End\n";
