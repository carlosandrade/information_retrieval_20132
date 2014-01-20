<?php
include 'vendor/autoload.php';

use ZendSearch\Lucene;

/**
 * Class Search
 *
 * Efetua busca de informaçoes nos indexes
 */
class Search {

    /**
     * Executa uma busca nos indexes criados
     *
     * @param $query
     */
    public function query($query)
    {
        // Definiçao dos caminhos dos arquivos
        $dir = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "data" .DIRECTORY_SEPARATOR;
        $jsonDir = $dir . "json";
        $indexDir = $dir . "index";

        // Abre o indice
        $index = Lucene\Lucene::open($indexDir . DIRECTORY_SEPARATOR . 'futebol'); // Abre index

        // prepara a query
        $title_query = "title:($query)";
        $text_query = "text:($query)";
        $localization_query = "localization:($query)";
        $group_query = "group:($query)";
        //$taxonomy_query = "taxonomy:($query)";

        // Monta a query
        $fullQuery = Lucene\Search\QueryParser::parse("$title_query $text_query $group_query $localization_query");
        // Faz a busca
        $hits = $index->find($query); // Executa query

        // Lista resultados
        $results = array();
        foreach ($hits as $hit) {
            $document = $hit->getDocument();

            $result = array(
                'url' => $document->getFieldValue('url'),
                'title' => $document->getFieldValue('title'),
                'text' => $document->getFieldValue('text'),
                'group' => $document->getFieldValue('group'),
            );

            $results[] = $result;
        }

        return $results;
    }
}
