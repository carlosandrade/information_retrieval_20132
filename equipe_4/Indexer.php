<?php
include 'vendor/autoload.php';
include 'InformationRetrieval/Analyzer/TextMining.php';

use ZendSearch\Lucene;
use InformationRetrieval\Analyzer\TextMining;

/**
 * Class Indexer
 * Ler dados extraidos das paginas dos arquivos json
 */
class Indexer
{
    /**
     * Classificador das noticias
     *
     * @var TextMining
     */
    protected $classifier = null;

    /**
     * Inicializador da classe
     *
     * @param TextMining $classifier
     */
    public function __construct($classifier)
    {
        $this->classifier = $classifier;
    }

    /**
     * Indexa dados nos arquivos de json
     */
    public function index()
    {
        // Definiçao das localizaçoes dos arquivos
        $dir = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR;
        $jsonDir = $dir . "json";
        $indexDir = $dir . "index";

        // Cria indice
        $index = Lucene\Lucene::create($indexDir . DIRECTORY_SEPARATOR . "futebol");

        // ler aquivos json
        $files = scandir($jsonDir);
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }

            // Se arquivo existe
            if (is_file($jsonDir . DIRECTORY_SEPARATOR . $file)) {
                $json = json_decode(file_get_contents($jsonDir . DIRECTORY_SEPARATOR . $file));

                // Cria documento e define campos para indexar
                foreach ($json as $entry) {
                    $doc = new Lucene\Document();
                    $doc->addField(Lucene\Document\Field::Text('url', $entry->url));
                    $doc->addField(Lucene\Document\Field::Text('title', $entry->title));
                    //$doc->addField(Lucene\Document\Field::Text('taxonomy', $entry->taxonomy));
                    $doc->addField(Lucene\Document\Field::Keyword('group', $this->classifier->classify($entry->title)));
                    $doc->addField(Lucene\Document\Field::Text('text', $entry->text));
                    $doc->addField(Lucene\Document\Field::Keyword('datetime', $entry->datetime));

                    // Adiciona documento ao index
                    $index->addDocument($doc);
                }
            }
        }
    }
}

// instancia o classificador
$tm = new TextMining();
// Carrega dados de treino
$tm->loadData("/home/helder/PhpstormProjects/InformationRetrieval20132/equipe_4/data/training.json");
// Executa o treinamento
$tm->training();

// Instancia o indexador
$ix = new Indexer($tm);
// Indexa os dados
$ix->index();
