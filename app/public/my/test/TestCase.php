<?php

namespace my\src;

require_once("../src/CorsoDiLaurea.php");
require_once("../src/ProspettoLaureando.php");
require_once("../src/ProspettoLaureandoIngegneriaInformatica.php");
require_once("../src/Laureando.php");
require_once("../src/LaureandoIngegneriaInformatica.php");
require_once("../src/SimulatoreVoto.php");

abstract class TestCase
{
    protected CorsoDiLaurea $corsoDiLaurea;
    protected ProspettoLaureando $prospettoLaureando;
    protected array $output;

    protected function __construct(string $file)
    {
        $this->output = json_decode(
            file_get_contents($file),
            true
        );

        $this->corsoDiLaurea = new CorsoDiLaurea($this->output["cdl"]);

        match ($this->corsoDiLaurea->nomeCorto) {
            "t-inf" => $this->prospettoLaureando = new ProspettoLaureandoIngegneriaInformatica(
                $this->corsoDiLaurea,
                $this->output["matricola"],
                $this->output["dataLaurea"]
            ),
            default => $this->prospettoLaureando = new ProspettoLaureando(
                $this->corsoDiLaurea,
                $this->output["matricola"],
                $this->output["dataLaurea"]
            )
        };
    }

    abstract public static function inizializza(): void;

    protected static function cercaTestCases(): array
    {
        return glob('cases/*_output.json');
    }

    abstract protected function avvia(): void;

    protected function testaAnagrafica(): void
    {
        $this->scriviTest(
            "Matricola",
            $this->prospettoLaureando->laureando->matricola,
            $this->output["matricola"]
        );

        $this->scriviTest(
            "Nome",
            $this->prospettoLaureando->laureando->nome,
            $this->output["nome"]
        );

        $this->scriviTest(
            "Cognome",
            $this->prospettoLaureando->laureando->cognome,
            $this->output["cognome"]
        );

        $this->scriviTest(
            "Email",
            $this->prospettoLaureando->laureando->mail,
            $this->output["mail"]
        );

        if ($this->output["cdl"] == "t-inf") {
            $this->scriviTest(
                "Bonus",
                $this->prospettoLaureando->laureando->bonus
                    ? "SI" : "NO",
                $this->output["bonus"]
            );
        }
    }

    abstract protected function scriviTest(
        string $nomeProprieta,
        mixed $valoreProprieta,
        mixed $valoreProprietaAttesa
    ): void;

    protected function testaStatistiche(): void
    {
        $mediaPesata = round(
            $this->prospettoLaureando->laureando->mediaPesata,
            3
        );
        $this->scriviTest(
            "Media Pesata (M)",
            $mediaPesata,
            $this->output["mediaPesata"]
        );

        $this->scriviTest(
            "Crediti che fanno media (CFU)",
            $this->prospettoLaureando->laureando->cfuInMedia,
            $this->output["cfuInMedia"]
        );

        $this->scriviTest(
            "Crediti curriculari conseguiti",
            $this->prospettoLaureando->laureando->cfuCurriculari,
            $this->output["cfuCurriculari"]
        );

        if ($this->output["cdl"] == "t-inf") {
            $mediaPesataInf = round(
                $this->prospettoLaureando->laureando->mediaPesataInf,
                3
            );
            $this->scriviTest(
                "Media pesata esami INF",
                $mediaPesataInf,
                $this->output["mediaPesataInf"]
            );
        }
    }

    protected function testaSimulazione(): void
    {
        $simulatoreVoto = new SimulatoreVoto(
            $this->corsoDiLaurea,
            $this->prospettoLaureando->laureando->mediaPesata,
            $this->prospettoLaureando->laureando->cfuInMedia
        );
        $simulatoreVoto->simula();

        $simulazione = $simulatoreVoto->simulazione;
        $dimensione = count($simulazione);

        for ($i = 0; $i < $dimensione; $i++) {
            $this->scriviTest(
                $simulazione[$i][0],
                $simulazione[$i][1],
                $this->output["simulazione"][$i][1]
            );
        }
    }
}
