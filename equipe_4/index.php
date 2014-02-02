<?php
require "Search.php";

$qAr = array();

if (!empty($_GET['q'])) {
    $q = $_GET['q'];
    $qAr = explode(' ', $q);
    $sc = new Search();
    $category = !empty($_GET['category']) ? $_GET['category'] : array();
    $month = !empty($_GET['month']) ? $_GET['month'] : null;
    $year = !empty($_GET['year']) ? $_GET['year'] : null;
    $results = $sc->query($q, $category, $month, $year);

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
        <script type="application/javascript" src="http://code.jquery.com/jquery-2.1.0.min.js"></script>
        <script type="application/javascript">
            $(document).ready(function() {
                $("#advancedLink a").click(function(e) {
                    e.preventDefault();

                    $("#advanced").toggle('slow');
                });
            });
        </script>
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

            #advancedLink a:link { text-decoration: none }
            #advancedLink a:visited { text-decoration: none }
            #advancedLink a:active { text-decoration: none }
            #advancedLink a:hover { color: #000000;}
            #advanced {
                border: solid;
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

                        <div id="advancedLink">
                            <a href="#">Op&ccedil;&otilde;es avan&ccedil;adas</a>
                        </div>

                        <div id="advanced" style="display: none;">
                            <div id="checkbox">Categoria<br>
                                <input type="checkbox" name="category[]" value="campeonatos">Campeonatos
                                <input type="checkbox" name="category[]" value="times">Times
                                <input type="checkbox" name="category[]" value="jogadores">Jogadores
                            </div>
                            <div id="mes" style="float: left">
                                <span>Mes</span><br />
                                <select name="month">
                                    <option value=""></option>
                                    <option value="01">Janeiro</option>
                                    <option value="02">Fevereiro</option>
                                    <option value="03">Março</option>
                                    <option value="04">Abril</option>
                                    <option value="05">Maio</option>
                                    <option value="06">Junho</option>
                                    <option value="07">Julho</option>
                                    <option value="08">Agosto</option>
                                    <option value="09">Setembro</option>
                                    <option value="10">Outubro</option>
                                    <option value="11">Novembro</option>
                                    <option value="12">Dezembro</option>
                                </select>
                            </div>
                            <div id="ano">
                                <span>Ano</span><br />
                                <select name="year">
                                    <option value=""></option>
                                    <option value="2014">2014</option>
                                    <option value="2013">2013</option>
                                    <option value="2013a">Anterior 2013</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div style="position: relative; top: 30%; left: -35%">
                <?php if (!empty($results)): ?>
                    <?php foreach ($results as $result): ?>
                        <?php if (!empty($result['title']) && !empty($result['text'])): ?>
                        <div>
                            <h3>
                                <span>[<?= ucfirst($result['group']); ?>]</span>
                                <a href="<?= $result['url']; ?>"> <?= $result['title']; ?></a>
                            </h3>
                            <div style="width: 800px">
                                <span><?= substr($result['text'], 0, 240); ?>...</span>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </body>
</html>