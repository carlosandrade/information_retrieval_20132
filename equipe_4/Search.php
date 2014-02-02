<?php
include 'vendor/autoload.php';

use ZendSearch\Lucene;
use ZendSearch\Lucene\Search\Query\MultiTerm;
use ZendSearch\Lucene\Search\Query\Boolean;
use ZendSearch\Lucene\Search\Query\Range;
use ZendSearch\Lucene\Search\Query\Term as QueryTerm;
use ZendSearch\Lucene\Index\Term as IndexTerm;

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
    public function query($query, $category = array(), $month = null, $year = null)
    {
        // Definiçao dos caminhos dos arquivos
        $dir = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "data" .DIRECTORY_SEPARATOR;
        $jsonDir = $dir . "json";
        $indexDir = $dir . "index";

        // Abre o indice
        $index = Lucene\Lucene::open($indexDir . DIRECTORY_SEPARATOR . 'futebol'); // Abre index

        // Queries
        $queryLucene = new Boolean();
        $queryDefault = new MultiTerm();
        foreach (explode(" ", $query) as $word) {
            $sign = null;
            $term = $word;

            if ($term[0] == '-') {
                $term = substr($word, 1);
                $sign = false;
            }

            $queryDefault->addTerm(new IndexTerm(strtolower($term), 'title'), $sign);
            $queryDefault->addTerm(new IndexTerm(strtolower($term), 'text'), $sign);
        }
        $queryLucene->addSubquery($queryDefault, true);


        if (!empty($category)) {
            if (count($category) > 1) {
                $queryGroup = new MultiTerm();

                foreach ($category as $group) {
                    $queryGroup->addTerm(new IndexTerm($group, 'group'));
                }

                $queryLucene->addSubquery($queryGroup, true);
            } else {
                $queryLucene->addSubquery(new QueryTerm(new IndexTerm($category[0], 'group')), true);
            }
        }

        $dateQueryFrom = "00010101";
        $dateQueryTo = date('Y1231');
        if (!is_null($month)) {
            $dateQueryFrom = substr_replace($dateQueryFrom, $month, 4, 2);
            $dateQueryTo = substr_replace($dateQueryTo, $month, 4, 2);
        }

        if (!is_null($year)) {
            $dateQueryFrom = substr_replace($dateQueryFrom, $year, 0, 4);
            $dateQueryTo = substr_replace($dateQueryTo, $year, 0, 4);
        }

        if (!is_null($month) || !is_null($year)) {
            $termFrom = new IndexTerm($dateQueryFrom, 'datetime');
            $termTo = new IndexTerm($dateQueryTo, 'datetime');
            $queryLucene->addSubquery(new Range($termFrom, $termTo, true), true);
        }

        // Faz a busca
        $hits = $index->find($queryLucene); // Executa query

        // Lista resultados
        $results = array();
        foreach ($hits as $hit) {
            $document = $hit->getDocument();

            $result = array(
                'url' => $document->getFieldValue('url'),
                'title' => $document->getFieldValue('title'),
                'text' => $document->getFieldValue('text'),
                'group' => $document->getFieldValue('group'),
                'date' => $document->getFieldValue('datetime'),
            );

            $results[] = $result;
        }

        return $results;
    }
}
