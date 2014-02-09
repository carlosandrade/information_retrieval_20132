<?php
require "Search.php";

$qAr = array('verona');

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

                $("#advanced").fadeToggle('slow');
            });
        });
    </script>
    <link type="text/css" href="css/style.css" rel="stylesheet"/>
</head>
<body >
<div id="imagens">
    <div id="divg">
        <h1 id="h1">Futebol Noticias</h1>
        <div id="divBusca">
            <form action="" method="get">
                <input type="text" id="txtBusca" name="q" placeholder="Digite aqui sua pesquisa" value="<?= implode(' ', $qAr)?>"/>
                <button id="btnBusca">Buscar</button>

                <div id="advancedLink">
                    <a href="#">Op&ccedil;&otilde;es avan&ccedil;adas</a>
                </div>
                
                <div id="advanced" style="display: none;">
                    <div class="seta-cima"></div>
                    <div id="checkbox"><h2>Categoria:</h2><br>
                        <input type="checkbox" name="category[]" value="campeonatos"><label><label>Campeonatos</label>
                        <input type="checkbox" name="category[]" value="times"><label>Times</label>
                        <input type="checkbox" name="category[]" value="jogadores"><label>Jogadores</label>
                    </div>
                    <div id="mes" style="float: left">
                        <h2>M&ecirc;s:</h2><br />
                        <select name="month">
                            <option value=""></option>
                            <option value="01">Janeiro</option>
                            <option value="02">Fevereiro</option>
                            <option value="03">Mar&ccedil;o</option>
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
                        <h2>Ano:</h2><br />
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
    <div class="lista">
        <?php if (!empty($results)): ?>
            <?php foreach ($results as $result): ?>
                <?php if (!empty($result['title']) && !empty($result['text'])): ?>
                    <div class="item">
                        <h3>
                            <span class="group">[<?= ucfirst($result['group']); ?>]</span>
                            <a href="<?= $result['url']; ?>"> <?= $result['title']; ?></a>
                        </h3>
                        <div>
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
