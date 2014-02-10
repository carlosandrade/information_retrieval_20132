<?php

namespace InformationRetrieval\Analyzer;

use HybridLogic\Classifier\Basic;
use HybridLogic\Classifier;
use Exception;

/**
 * Classificador Naive bayes
 *
 * Class TextMining
 * @package InformationRetrieval\Analyzer
 */
class TextMining
{
    protected $classifier;
    protected $data;

    public function __construct($dataPath = null)
    {
        $tokenizer = new Basic;
        $this->classifier = new Classifier($tokenizer);

        if (!is_null($dataPath)) {
            $this->loadData($dataPath);
        }
    }

    public function loadData($dataPath)
    {
        if (!file_exists($dataPath)) {
            throw new Exception('Arquivo de dados nao econtrado.');
        }

        $this->data = json_decode(file_get_contents($dataPath));
        //print_r($this->data);
    }

    public function training()
    {
        foreach($this->data as $category => $row) {
            $this->classifier->train($category, $row);
        }
    }

    public function classify($input) {
        $threshold = 1;
        $groupClassified = 'no-group';
        $results = $this->classifier->classify($input);

        foreach ($results as $group => $result) {
            if ($result < $threshold) {
                $groupClassified = $group;
                $threshold = $result;
            }
        }

        return $groupClassified;
    }
}
