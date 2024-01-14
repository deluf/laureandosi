<?php

namespace my\src;

use DateTime;

require_once("GestioneCarrieraStudente.php");
require_once("Laureando.php");
require_once("Esame.php");
require_once("TipoEsame.php");

class LaureandoIngegneriaInformatica extends Laureando
{
    public bool $bonus;
    public float $mediaPesataInf;

    protected function costruisciCarriera(): void
    {
        $carriera = GestioneCarrieraStudente::restituisciCarrieraStudente(
            $this->matricola
        );
        $carriera = json_decode($carriera, true)["Esami"]["Esame"];

        $this->costruisciCarrieraConJSON($carriera);

        $annoImmatricolazione = $carriera[0]["ANNO_IMM"];
        $dataChiusura = $carriera[0]["DATA_CHIUSURA"];

        $this->calcolaBonus($annoImmatricolazione, $dataChiusura);
        if ($this->bonus) {
            $this->applicaBonus();
        }
    }

    private function calcolaBonus(
        int $annoImmatricolazione,
        string $dataChiusura
    ): void {
        $dateFormat = 'd/m/Y';
        $inizioCarriera = DateTime::createFromFormat(
            $dateFormat,
            "01/09/" . $annoImmatricolazione
        );
        $fineCarriera = DateTime::createFromFormat($dateFormat, $dataChiusura);

        /* Il bonus è valido se la laurea avviene entro 3 anni e 6 mesi
           dal 01/09 dell'anno di immatricolazione */
        $this->bonus = $inizioCarriera->diff($fineCarriera)->days <= 1277;
    }

    private function applicaBonus(): void
    {
        $esameConVotoPiuBasso = $this->esami[0];
        foreach ($this->esami as $esame) {
            if (!$esame->faMedia) {
                continue;
            }
            if ($esame->voto < $esameConVotoPiuBasso->voto) {
                $esameConVotoPiuBasso = $esame;
            } elseif (
                $esame->voto == $esameConVotoPiuBasso->voto
                && $esame->cfu > $esameConVotoPiuBasso->cfu) {
                    $esameConVotoPiuBasso = $esame;
            }
        }
        $esameConVotoPiuBasso->faMedia = false;
    }

    protected function calcolaStatistiche(): void
    {
        $esamiInformatici = json_decode(
            file_get_contents("../config/esamiInformatici.json"),
            true
        );

        $this->mediaPesata = 0.0;
        $this->cfuCurriculari = 0;
        $this->cfuInMedia = 0;
        $this->mediaPesataInf = 0.0;
        $cfuInMediaInf = 0;

        foreach ($this->esami as $esame) {
            $this->cfuCurriculari += $esame->cfu;

            if (in_array($esame->nome, $esamiInformatici)) {
                $esame->tipo = TipoEsame::INFORMATICO;
            }
            if (!$esame->faMedia) {
                continue;
            }

            $this->cfuInMedia += $esame->cfu;
            $this->mediaPesata += $esame->voto * $esame->cfu;
            if ($esame->tipo == TipoEsame::INFORMATICO) {
                $cfuInMediaInf += $esame->cfu;
                $this->mediaPesataInf += $esame->voto * $esame->cfu;
            }
        }
        $this->mediaPesata = $this->mediaPesata / $this->cfuInMedia;
        $this->mediaPesataInf = ($cfuInMediaInf == 0) ?
            0 : $this->mediaPesataInf / $cfuInMediaInf;
    }

}