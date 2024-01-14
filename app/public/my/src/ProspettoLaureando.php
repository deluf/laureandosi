<?php

namespace my\src;

require_once("Laureando.php");
require_once("LaureandoIngegneriaInformatica.php");
require_once("Esame.php");
require_once("SimulatoreVoto.php");
require_once("CorsoDiLaurea.php");

class ProspettoLaureando
{
    /** @noinspection HtmlRequiredTitleElement */
    final public const APRI_HTML = '<html lang="it">
<head>
    <style>
        * {            
            box-sizing: border-box;
            padding: 0;
            margin: 0;
        }
        body {
            font-size: .8rem;
            width: 100%;
        }
        div {
            margin-top: 1rem;
        }
        .intestazione {
            text-align: center;
        }
        .laureandi table {            
            border: 1px solid black;
            border-collapse: collapse;
            text-align: center;
            width: 100%;
        }
        .laureandi td {
            border: 1px solid black;
        }
        .anagrafica table, .statistiche table {
            border: 1px solid black;
            width: 100%;
        }
        .anagrafica table td:nth-child(1), .statistiche table td:nth-child(1) {
            width: 50%;
        }
        .esami table, .simulazione table {  
            border-collapse: collapse;
            text-align: center;
            width: 100%;
        }
        .esami td, .simulazione td {
            border: 1px solid black;
        }
        .esami td:nth-child(1) {
            text-align: left;
        }
    </style>
</head>
<body>';
    final public const CHIUDI_HTML = '
</body>
</html>';
    final public const CHIUDI_TABELLA = '
        </table>
    </div>';
    public string $prospetto;
    public Laureando $laureando;
    private CorsoDiLaurea $corsoDiLaurea;
    private string $dataLaurea;

    public function __construct(
        CorsoDiLaurea $corsoDiLaurea,
        int $matricola,
        string $dataLaurea
    ) {
        $this->corsoDiLaurea = $corsoDiLaurea;
        $this->dataLaurea = $dataLaurea;
        $this->prospetto = "";

        match ($this->corsoDiLaurea->nomeCorto) {
            "t-inf" => $this->laureando = new LaureandoIngegneriaInformatica(
                $matricola, $corsoDiLaurea
            ),
            default => $this->laureando = new Laureando(
                $matricola,
                $corsoDiLaurea
            )
        };

        $this->aggiungiIntestazione();
        $this->aggiungiAnagrafica();
        $this->aggiungiEsami();
        $this->aggiungiStatistiche();
    }

    private function aggiungiIntestazione(): void
    {
        $this->prospetto .= '
    <div class="intestazione">
        ' . $this->corsoDiLaurea->nomeCompleto . '
        <br>
        CARRIERA E SIMULAZIONE DEL VOTO DI LAUREA
    </div>';
    }

    protected function aggiungiAnagrafica(bool $chiudiTabella = true): void
    {
        $this->prospetto .= '
    <div class="anagrafica">
        <table>
            <tr>
                <td>Matricola:</td>
                <td>' . $this->laureando->matricola . '</td>
            </tr>
            <tr>
                <td>Nome:</td>
                <td>' . $this->laureando->nome . '</td>
            </tr>
            <tr>
                <td>Cognome:</td>
                <td>' . $this->laureando->cognome . '</td>
            </tr>
            <tr>
                <td>Email:</td>
                <td>' . $this->laureando->mail . '</td>
            </tr>
            <tr>
                <td>Data:</td>
                <td>' . $this->dataLaurea . '</td>
            </tr>';

        if ($chiudiTabella) {
            $this->prospetto .= self::CHIUDI_TABELLA;
        }
    }

    protected function aggiungiEsami(): void
    {
        $esami = '
    <div class="esami">
        <table>
            <tr>
                <td style="text-align: center">ESAME</td>
                <td>CFU</td>
                <td>VOT</td>
                <td>MED</td>
            </tr>';

        foreach ($this->laureando->esami as $esame) {
            $esami .= '
            <tr>
                <td>' . $esame->nome . '</td>
                <td>' . $esame->cfu . '</td>
                <td>' . $esame->voto . '</td>
                <td>' . ($esame->faMedia ? "X" : "") . '</td>
            </tr>';
        }

        $this->prospetto .= $esami . self::CHIUDI_TABELLA;
    }

    protected function aggiungiStatistiche(bool $chiudiTabella = true): void
    {
        $this->prospetto .= '
    <div class="statistiche">
        <table>
            <tr>
                <td>Media pesata (M):</td>
                <td>' . round($this->laureando->mediaPesata, 3) . '</td>
            </tr>
            <tr>
                <td>Crediti che fanno media (CFU):</td>
                <td>' . $this->laureando->cfuInMedia . '</td>
            </tr>
            <tr>
                <td>Crediti curriculari conseguiti:</td>
                <td>' . $this->laureando->cfuCurriculari
                      . '/' . $this->corsoDiLaurea->cfu . '</td>
            </tr>' .
            match ($this->corsoDiLaurea->nomeCorto) {
                "t-inf", "m-ce" => '
            <tr>
                <td>Voto di testi (T):</td>
                <td>0</td>
            </tr>',
                default => ""
            } . '
            <tr>
                <td>Formula calcolo voto di laurea:</td>
                <td>' . $this->corsoDiLaurea->formula . '</td>
            </tr>';

        if ($chiudiTabella) {
            $this->prospetto .= self::CHIUDI_TABELLA;
        }
    }

    public function aggiungiSimulazioneVoto(): void
    {
        $simulatoreVoto = new SimulatoreVoto(
            $this->corsoDiLaurea,
            $this->laureando->mediaPesata,
            $this->laureando->cfuInMedia
        );

        $this->prospetto .= $simulatoreVoto->simula();
        $this->prospetto .= self::CHIUDI_TABELLA;
        $this->prospetto .= '
    <div>
        ' . $this->corsoDiLaurea->note . '
    </div>';
    }

    public function esporta(): string
    {
        return self::APRI_HTML . $this->prospetto . self::CHIUDI_HTML;
    }

}
