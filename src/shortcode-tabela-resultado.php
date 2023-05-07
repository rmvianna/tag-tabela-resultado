<?php
class Bicho 
{
    public $grupo;
    public $nome;
    public $img;

    public function __construct($grupo, $nome, $img) 
    {
        $this->grupo = $grupo;
        $this->nome = $nome;
        $this->img = $img;
    }

    public function ehDoMilhar($finalMilhar)
    {
        $intFinalMilhar = intval($finalMilhar === "00" ? "100" : $finalMilhar); //Tratamento especial para 00, que equivale a 100
        $limiteInicial = ($this->grupo - 1) * 4 + 1; //Ex.: Grupo 05 inicia em 17, pois 5 - 1 = 4 * 4 = 16 + 1 => 17
        $limiteFinal = $this->grupo * 4; //O limite final sempre é o grupo x 4, pois cada bicho tem 4 pares de número

        return $intFinalMilhar >= $limiteInicial &&
            $intFinalMilhar <= $limiteFinal;
    }
}

class Premio
{
    public $resultado;
    public $bicho;

    public function __construct($resultado, $bicho = null)
    {
        $this->resultado = $resultado;

        if ($bicho == null && is_numeric($resultado)) {
            $finalMilhar = substr($resultado, -2);

            foreach ($GLOBALS["bichos"] as $bicho) {
                if ($bicho->ehDoMilhar($finalMilhar)) {
                    $this->bicho = $bicho;
                    break;
                }
            }
        } else {
            $this->bicho = $bicho;
        }
    }
}

class Bilhete
{
    public $premios;

    public function __construct($premios, $auto, $totalPremios)
    {
        $this->premios = $premios;

        if ($auto == 1 && count($premios) == 5) {
            if ($totalPremios <= 8) {
                $this->premios[5] = $this->inferirPremioPorSomaMilhares();
                $this->premios[6] = $this->inferirPremioPorMultiplicacaoDoisPrimeirosPremios();

                if ($totalPremios == 8) {
                    $this->premios[7] = $this->inferirPremioPorMultiplicacaoDezenaPrimeiroPremio();
                }
            } else {
                $this->premios[5] = $this->inferirPremioPorColuna(0);
                $this->premios[6] = $this->inferirPremioPorColuna(1);
                $this->premios[7] = $this->inferirPremioPorColuna(2);
                $this->premios[8] = $this->inferirPremioPorColuna(3);
                $this->premios[9] = $this->inferirPremioPorSomaMilhares();
            }
        }
    }

    private function inferirPremioPorColuna($coluna)
    {
        $milhar = "";

        //Busca o "n-ésimo" dígito do milhar dos 4 primeiros prêmios do bilhete
        for ($i = 0; $i < 4; $i++) {
            $milhar .= substr($this->premios[$i]->resultado, $coluna, 1);
        }

        return new Premio($milhar);
    }

    private function inferirPremioPorMultiplicacaoDoisPrimeirosPremios()
    {
        $multiplicacao = str_pad(
            $this->premios[0]->resultado * $this->premios[1]->resultado,
            6,
            "0",
            STR_PAD_LEFT
        );

        return new Premio(substr(strval($multiplicacao), -6, 3));
    }

    private function inferirPremioPorMultiplicacaoDezenaPrimeiroPremio()
    {
        $multiplicacao = str_pad(
            intval(substr($this->premios[0]->resultado, -2)) * 4,
            2,
            "0",
            STR_PAD_LEFT
        );

        $premio = new Premio($multiplicacao);
        $premio->resultado = "Salteado";

        return $premio;
    }

    private function inferirPremioPorSomaMilhares()
    {
        $soma = 0;

        for ($i = 0; $i < count($this->premios); $i++) {
            $soma += intval($this->premios[$i]->resultado);
        }

        //Apenas os últimos 4 dígitos devem ser considerados para o milhar desse Prêmio
        return new Premio(
            str_pad(substr(strval($soma), -4), 4, "0", STR_PAD_LEFT)
        );
    }
}

//Base de "bichos" do jogo
$GLOBALS["bichos"] = [
    new Bicho(1, "Avestruz", "1f427"),
    new Bicho(2, "Águia", "1f985"),
    new Bicho(3, "Burro", "1f434"),
    new Bicho(4, "Borboleta", "1f98b"),
    new Bicho(5, "Cachorro", "1f415"),
    new Bicho(6, "Cabra", "1f410"),
    new Bicho(7, "Carneiro", "1f411"),
    new Bicho(8, "Camelo", "1f42b"),
    new Bicho(9, "Cobra", "1f40d"),
    new Bicho(10, "Coelho", "1f407"),
    new Bicho(11, "Cavalo", "1f40e"),
    new Bicho(12, "Elefante", "1f418"),
    new Bicho(13, "Galo", "1f413"),
    new Bicho(14, "Gato", "1f408"),
    new Bicho(15, "Jacaré", "1f40a"),
    new Bicho(16, "Leão", "1f981"),
    new Bicho(17, "Macaco", "1f412"),
    new Bicho(18, "Porco", "1f437"),
    new Bicho(19, "Pavão", "1f99a"),
    new Bicho(20, "Peru", "1f983"),
    new Bicho(21, "Touro", "1f402"),
    new Bicho(22, "Tigre", "1f405"),
    new Bicho(23, "Urso", "1f43b"),
    new Bicho(24, "Veado", "1f98c"),
    new Bicho(25, "Vaca", "1f404"),
];

function gera_tabela_resultado($atts = [], $content = null)
{
    if (empty($content)) {
        return $content;
    }

    // Define os parametros default da tag, que podem ser sobrescritos pelo usuario
    $atributos = shortcode_atts(
        [
            "separador" => ".",
            "auto" => 0,
            "premios" => 10,
            "cssclass" => "tabela-resultado",
        ],
        $atts
    );

    //Apenas formata o conteúdo que já existe, comportamento já existente da tag
    if ($atributos["auto"] == 0 && !tem_apenas_milhares($content)) {
        return formata_resultado($atributos, $content);
    }
    //Extrai os milhares e gera conteúdo novo
    else {
        $premios = extrai_premios($content);

        /* Se auto = 1, gera os premios automaticamente conforme a banca, caso contrário
        apenas gera as colunas de grupo de bicho conforme a quantidade de milhares
        informadas (à partir de 1)
        */
        $bilhete = new Bilhete(
            $premios,
            $atributos["auto"],
            $atributos["premios"]
        );

        return gera_resultado($atributos, $bilhete);
    }
}

function tem_apenas_milhares($content)
{
    $linhas = preg_split("/(\r\n|\n|<br>|<br\/>)/", $content);

    if (empty($linhas)) {
        return false;
    }

    foreach ($linhas as $linha) {
        $linha = preg_replace("~[[:cntrl:]]~", "", strip_tags($linha));
        if (empty($linha)) {
            continue;
        }

        preg_match('/^\d{4}$/', $linha, $extracaoLinha);
        if (!isset($extracaoLinha[0])) {
            return false;
        }
    }

    return true;
}

function formata_resultado($atributos, $content)
{
    $separador = $atributos["separador"];
    $cssclass = $atributos["cssclass"];

    $linhas = preg_split("/(\r\n|\n|<br>|<br\/>)/", $content);
    $tabelaresultado = "<table class='" . $cssclass . "'><thead>";
    $primeira_linha = true;
    $qtd_colunas = 0;

    foreach ($linhas as $linha) {
        $idx_col = 0;
        $linha = strip_tags($linha);
        $colunas = explode($separador, $linha);

        if (empty($colunas) || empty($linha)) {
            continue;
        }

        $tabelaresultado .= "<tr>";

        foreach ($colunas as $coluna) {
            $coluna = trim(str_replace("&nbsp;", "", $coluna));
            $idx_col++;

            if ($primeira_linha) {
                $tabelaresultado .= "<th>" . $coluna . "</th>";
                $qtd_colunas++;
            } else {
                $tabelaresultado .= "<td>" . $coluna . "</td>";
            }
        }

        //Criando coluna vazia ate chegar ao total de colunas do TH
        while ($idx_col < $qtd_colunas) {
            $tabelaresultado .= "<td></td>";
            $idx_col++;
        }

        $tabelaresultado .= "</tr>";

        if ($primeira_linha) {
            $tabelaresultado .= "</thead><tbody>";
            $primeira_linha = false;
        }
    }

    return $tabelaresultado . "</tbody></table>";
}

function extrai_premios($content)
{
    $linhas = preg_split("/(\r\n|\n|<br>|<br\/>)/", $content);
    $premios = [];

    foreach ($linhas as $linha) {
        preg_match("/(\d{4})/", strip_tags($linha), $extracaoLinha);
        $milhar = isset($extracaoLinha[1]) ? $extracaoLinha[1] : null;

        if (empty($milhar)) {
            continue;
        }

        array_push($premios, new Premio($milhar));
    }

    return $premios;
}

function gera_resultado($atributos, $bilhete)
{
    $cssclass = $atributos["cssclass"];
    $html =
        "<table class='" .
        $cssclass .
        "'><thead><tr><th>Prêmio</th><th>Resultado</th><th>Bicho</th></tr></thead><tbody>";

    foreach ($bilhete->premios as $idx => $premio) {
        $html .=
            "<tr><td>" .
            ($idx + 1) .
            "º</td><td>" .
            $premio->resultado .
            "</td><td>" .
            "&nbsp; " .
            sprintf("%02d", intval($premio->bicho->grupo)) .
            " " .
            $premio->bicho->nome .
            " <img draggable='false' width='16px' src='https://s.w.org/images/core/emoji/13.1.0/svg/" .
            $premio->bicho->img .
            ".svg'>";
    }

    return $html . "</tbody></table>";
}

add_shortcode("tabelaresultado", "gera_tabela_resultado");
?>