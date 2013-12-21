<?php
include 'vendor/autoload.php';

use ZendSearch\Lucene;

class Search {

    /**
     * Executa uma busca nos indexes criados
     *
     * @param $query
     */
    public function query($query)
    {
        $dir = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "data" .DIRECTORY_SEPARATOR;
        $jsonDir = $dir . "json";
        $indexDir = $dir . "index";

        // Percorre os indices
        $files = scandir($jsonDir);
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }

            $indexName = substr($file, 0, -5);
            $index = Lucene\Lucene::open($indexDir . DIRECTORY_SEPARATOR . $indexName); // Abre index

            $hits = $index->find($query); // Executa query

            // Lista resultados
            foreach ($hits as $hit) {
                $document = $hit->getDocument();

                // return a Zend\Search\Lucene\Field object
                // from the Zend\Search\Lucene\Document
                echo "<h3>" . $document->getFieldValue('url') . "</h3><br />";
                //echo "<p>" . $hit->text . "</p><br /><br />";
            }
        }
    }
}

$q = !empty($_GET['q']) ? $_GET['q'] : 'CBF';
$sc = new Search();
$sc->query($q);