<?php
include("./lib/wordpress-functions.php");
include("./shortcode-tabela-resultado.php");
?>
<!doctype html>
<html>
    <head>
        <title>Testes Tag TabelaResultado</title>
        <link rel="stylesheet" href="style/tabela-resultado.css"/>
        <link rel="stylesheet" href="style/index.css"/>
    </head>
    <body>
        <h1>Teste 01 -> Tag sem atributos</h1>
        <div class="layout-linha">
            <div class="layout-coluna">
                <h2>Fonte</h2>
                <pre id="teste1">[tabelaresultado]
Prêmio.Milhar.Bicho
1º.0136. 09 Cobra
2º.3925. 07 Carneiro
3º.4853. 14 Gato
4º.0566. 17 Macaco
5º.4831. 08 Camelo
6º.3300. 25 Vaca
7º.9662. 16 Leão
8º.9352. 13 Galo
9º.4389. 23 Urso
10º.7365. 17 Macaco
[/tabelaresultado]</pre>
            </div>
            <div class="layout-coluna">
                <h2>Resultado</h2>
                <?php evaluate_tag( "teste1", "index.php" ); ?>
            </div>
        </div>
        <h1>Teste 02 -> Tag com separador</h1>
        <div class="layout-linha">
            <div class="layout-coluna">
                <h2>Fonte</h2>
                <pre id="teste2">[tabelaresultado separador="|"]
Prêmio|Milhar|Bicho
1º|0.136| 09 Cobra
2º|3.925| 07 Carneiro
3º|4.853| 14 Gato
4º|0.566| 17 Macaco
5º|4.831| 08 Camelo
[/tabelaresultado]</pre>
            </div>
            <div class="layout-coluna">
                <h2>Resultado</h2>
                <?php evaluate_tag( "teste2", "index.php" ); ?>
            </div>
        </div>
        <h1>Teste 03 -> Tag com class CSS</h1>
        <div class="layout-linha">
            <div class="layout-coluna">
                <h2>Fonte</h2>
                <pre id="teste3">[tabelaresultado cssclass="tabela-resultado-anterior"]
Prêmio.Milhar.Bicho
1º.2984. 21 Touro
2º.4743. 11 Cavalo
3º.0419. 05 Cachorro
4º.7443. 11 Cavalo
5º.5333. 14 Gato
[/tabelaresultado]</pre>
            </div>
            <div class="layout-coluna">
                <h2>Resultado</h2>
                <?php evaluate_tag( "teste3", "index.php" ); ?>
            </div>
        </div>
        <h1>Teste 04 - Bilhete com 7 prêmios</h1>
        <div class="layout-linha">
            <div class="layout-coluna">
                <h2>Fonte</h2>
                <pre id="teste4">[tabelaresultado auto="1" premios="7"]3599
8767
3545
9638
6022
[/tabelaresultado]</pre>
            </div>
            <div class="layout-coluna">
                <h2>Resultado</h2>
                <?php evaluate_tag( "teste4", "index.php" ); ?>
            </div>
        </div>
        <h1>Teste 05 - Bilhete com 8 prêmios</h1>
        <div class="layout-linha">
            <div class="layout-coluna">
                <h2>Fonte</h2>
                <pre id="teste5">[tabelaresultado auto="1" premios="8"]6144
3479
5416
1768
1963
[/tabelaresultado]</pre>
            </div>
            <div class="layout-coluna">
                <h2>Resultado</h2>
                <?php evaluate_tag( "teste5", "index.php" ); ?>
            </div>
        </div>
        <h1>Teste 06 - Bilhete com 10 prêmios</h1>
        <div class="layout-linha">
            <div class="layout-coluna">
                <h2>Fonte</h2>
                <pre id="teste6">[tabelaresultado auto="1"]6506
6989
9742
8490
6294
[/tabelaresultado]</pre>
            </div>
            <div class="layout-coluna">
                <h2>Resultado</h2>
                <?php evaluate_tag( "teste6", "index.php" ); ?>
            </div>
        </div>
    </body>
</html>