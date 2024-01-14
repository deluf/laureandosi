<?php

namespace my\src;

require_once('ProspettoLaureando.php');
require_once('LaureandoIngegneriaInformatica.php');
require_once('Esame.php');
require_once('TipoEsame.php');

class ProspettoLaureandoIngegneriaInformatica extends ProspettoLaureando
{

    protected function aggiungiAnagrafica(bool $chiudiTabella = true): void
    {
        parent::aggiungiAnagrafica(false);
        $this->prospetto .= '
            <tr>
                <td>Bonus:</td>
                <td>' . ($this->laureando->bonus ? "SI" : "NO") . '</td>
            </tr>';
        $this->prospetto .= parent::CHIUDI_TABELLA;
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
                <td>INF</td>
            </tr>';

        foreach ($this->laureando->esami as $esame) {
            $esami .= '
            <tr>
                <td>' . $esame->nome . '</td>
                <td>' . $esame->cfu . '</td>
                <td>' . $esame->voto . '</td>
                <td>' . ($esame->faMedia ? "X" : "") . '</td>
                <td>' . ($esame->tipo == TipoEsame::INFORMATICO ? "X" : "") . '</td>
            </tr>';
        }

        $this->prospetto .= $esami . self::CHIUDI_TABELLA;
    }

    protected function aggiungiStatistiche(bool $chiudiTabella = true): void
    {
        parent::aggiungiStatistiche(false);
        $this->prospetto .= '
            <tr>
                <td>Media pesata esami INF:</td>
                <td>' . round($this->laureando->mediaPesataInf, 3) . '</td>
            </tr>';
        $this->prospetto .= parent::CHIUDI_TABELLA;
    }

}