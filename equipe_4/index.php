<?php
require "Search.php";

$qAr = array();

if (!empty($_GET['q'])) {
    $q = $_GET['q'];
    $qAr = explode(' ', $q);
    $sc = new Search();
    $results = $sc->query($q);

    foreach ($results as &$result) {
        $result['title'] = htmlentities($result['title']);
        $result['text'] = htmlentities($result['text']);

        foreach ($qAr as $word) {
            $result['title'] = str_ireplace($word,'<span style="background-color: #FFFF00">'.$word.'</span>', $result['title']);
            $result['text'] = str_ireplace($word,'<span style="background-color: #FFFF00">'.$word.'</span>', $result['text']);
        }
    }
}
?>
<html>
    <head>
        <title>Futebol Noticias</title>
        <style type="text/css">
            #divBusca {
                margin-top: -5%;
                border: solid 1px #000000;
                border-radius: 10px;
                width: 360px;
                height: 32px;
            }

            #txtBusca {
                float: left;
                background-color: transparent;
                padding-left: 5px;
                font-size: 18px;
                border: none;
                height: 32px;
                width: 200px;
            }

            #btnBusca {
                border: none;
                margin-left: 18%;
                float: left;
                height: 32px;
                border-radius: 7px 7px 7px 7px;
                width: 70px;
                font-weight: bold;

            }

            #divBusca img {
                float: left;
            }

            #divg {
                position: absolute;
                left: 5%;
                top: 5%
            }

            #h1 {
                margin-left: 20%;
            }

            #imagens {
                height: 100%;
                background-image: url(bola.png);
                background-repeat: no-repeat;
                margin-left: 30%;
                margin-top: 5%;
                filter: alpha(opacity=10);
            }
        </style>
    </head>
    <body >
        <div id="imagens">
            <div id="divg">
                <h1 id="h1">Futebol Noticias</h1>
                <div id="divBusca">
                    <form action="" method="get">
                        <img src="search.png" width="24" height="24" alt=""/>
                        <input type="text" id="txtBusca" name="q" placeholder="Digite aqui sua pesquisa" value="<?= implode(' ', $qAr)?>"/>
                        <button id="btnBusca">Buscar</button>
                    </form>
                </div>
            </div>
            <div style="position: relative; top: 15%; left: -35%">
                <?php if (!empty($results)): ?>
                    <?php foreach ($results as $result): ?>
                        <div>
                            <h3>
                                <span>[<?= ucfirst($result['group']); ?>]</span>
                                <a href="<?= $result['url']; ?>"> <?= $result['title']; ?></a>
                            </h3>
                            <div style="width: 800px">
                                <span><?= substr($result['text'], 0, 240); ?>...</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </body>
</html>