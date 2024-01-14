<?php

namespace my\src;

require_once("ProspettoLaureando.php");
require_once("Laureando.php");

class ProspettoCommissione
{
    private string $nomeCompletoCdL;
    private string $listaLaureandi;
    private string $prospettiLaureandi;

    public function __construct(string $nomeCompletoCdL)
    {
        $this->nomeCompletoCdL = $nomeCompletoCdL;
        $this->prospettiLaureandi = "";
        $this->listaLaureandi = "";

        $this->aggiungiIntestazione();
    }

    private function aggiungiIntestazione(): void
    {
        $this->listaLaureandi .= ' 
    <div class="intestazione">
        ' . $this->nomeCompletoCdL . '
        <br>
        LAUREANDOSI 2 - Progettazione: f.delucchini@studenti.unipi.it
    </div>
    <div class="laureandi">
        <table>
            <tr>
                <td>COGNOME</td>
                <td>NOME</td>
                <td>CDL</td>
                <td>VOTO LAUREA</td>
            </tr>';
    }

    public function aggiungiLaureando(ProspettoLaureando $prospettoLaureando
    ): void {
        $this->listaLaureandi .= '
            <tr>
                <td>' . $prospettoLaureando->laureando->cognome . '</td>
                <td>' . $prospettoLaureando->laureando->nome . '</td>
                <td></td>
                <td>/110</td>
            </tr>';

        $this->prospettiLaureandi .= '<pagebreak>';
        $this->prospettiLaureandi .= $prospettoLaureando->prospetto;
    }

    public function esporta(): string
    {
        return
            ProspettoLaureando::APRI_HTML
            . $this->listaLaureandi
            . ProspettoLaureando::CHIUDI_TABELLA
            . $this->prospettiLaureandi
            . ProspettoLaureando::CHIUDI_HTML;
    }

}
