<?php

namespace my\src;

use Mpdf\Mpdf;
use Mpdf\MpdfException;

require_once("ProspettoCommissione.php");
require_once("ProspettoLaureando.php");
require_once("ProspettoLaureandoIngegneriaInformatica.php");
require_once("Destinatario.php");
require_once("Laureando.php");
require_once("CorsoDiLaurea.php");
require_once("../vendor/autoload.php");

class GeneratoreProspetti
{
    private CorsoDiLaurea $corsoDiLaurea;

    /** @var int[] $matricole */
    private array $matricole;

    private string $percorsoBase;
    private string $percorsoBaseCdL;
    private string $dataLaurea;

    public function __Construct(
        string $nomeCortoCdL,
        array $matricole,
        string $dataLaurea
    ) {
        $this->corsoDiLaurea = new CorsoDiLaurea($nomeCortoCdL);
        $this->matricole = $matricole;
        $this->dataLaurea = $dataLaurea;
        $this->percorsoBase = "../out/";
        $this->percorsoBaseCdL = $this->percorsoBase . $this->corsoDiLaurea->nomeCorto;
    }

    /**
     * @throws MpdfException
     */
    public function generaPDF(): void
    {
        $this->preparaCartellaPDF();

        /** @var Destinatario[] $tabellaDestinatari */
        $tabellaDestinatari = [];

        $prospettoCommissione = new ProspettoCommissione(
            $this->corsoDiLaurea->nomeCompleto
        );

        foreach ($this->matricole as $matricola) {
            match ($this->corsoDiLaurea->nomeCorto) {
                "t-inf" => $prospettoLaureando = new ProspettoLaureandoIngegneriaInformatica(
                    $this->corsoDiLaurea,
                    $matricola,
                    $this->dataLaurea
                ),
                default => $prospettoLaureando = new ProspettoLaureando(
                    $this->corsoDiLaurea,
                    $matricola,
                    $this->dataLaurea
                )
            };

            $nomePDFlaureando =
                $prospettoLaureando->laureando->cognome . "-"
                . $prospettoLaureando->laureando->nome . "-"
                . $matricola . ".pdf";

            $this->prospettoToPDF(
                $prospettoLaureando->esporta(),
                $nomePDFlaureando
            );

            $prospettoLaureando->aggiungiSimulazioneVoto();
            $prospettoCommissione->aggiungiLaureando($prospettoLaureando);

            $tabellaDestinatari[] = new Destinatario(
                $nomePDFlaureando,
                $prospettoLaureando->laureando->mail
            );
        }

        $this->prospettoToPDF(
            $prospettoCommissione->esporta(),
            "Commissione.pdf"
        );

        file_put_contents(
            $this->percorsoBaseCdL . "/Destinatari.json",
            json_encode($tabellaDestinatari, JSON_PRETTY_PRINT)
        );
    }

    private function preparaCartellaPDF(): void
    {
        if (!is_dir($this->percorsoBase)) {
            mkdir($this->percorsoBase);
        }

        if (is_dir($this->percorsoBaseCdL)) {
            $files = glob($this->percorsoBaseCdL . "/*");
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        } else {
            mkdir($this->percorsoBaseCdL);
        }
    }

    /**
     * @throws MpdfException
     */
    private function prospettoToPDF(string $prospetto, string $nomePDF): void
    {
        $prospettoPDF = new Mpdf();
        $prospettoPDF->WriteHTML($prospetto);
        $prospettoPDF->Output($this->percorsoBaseCdL . "/" . $nomePDF, "F");
    }

}