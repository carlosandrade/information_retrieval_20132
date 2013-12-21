<?php
include 'vendor/autoload.php';

use ZendSearch\Lucene;

class Indexer {

    /**
     * Indexa dados nos arquivos de json
     */
    public function index()
    {
        $dir = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "data" .DIRECTORY_SEPARATOR;
        $jsonDir = $dir . "json";
        $indexDir = $dir . "index";

        // ler aquivos json
        $files = scandir($jsonDir);
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }

            // Se arquivo existe
            if (is_file($jsonDir . DIRECTORY_SEPARATOR . $file)) {
                $json = json_decode(file_get_contents($jsonDir . DIRECTORY_SEPARATOR . $file));
                $indexName = substr($file, 0, -5);
                // Cria index
                $index = Lucene\Lucene::create($indexDir . DIRECTORY_SEPARATOR . $indexName);

                // Cria documento e define campos para indexar
                foreach ($json as $entry) {
                    $doc = new Lucene\Document();
                    $doc->addField(Lucene\Document\Field::Text('url', $entry->title));
                    $doc->addField(Lucene\Document\Field::UnStored('contents', $entry->text));
                    $index->addDocument($doc);
                }
            }
        }
    }
}

$ix = new Indexer();
$ix->index();
