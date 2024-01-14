<?php

namespace my\src;

require_once("GestioneCarrieraStudente.php");
require_once("Esame.php");
require_once("CorsoDiLaurea.php");

class Laureando
{
    public int $matricola;
    /** @var Esame[] $esami */
    public array $esami;
    public float $mediaPesata;
    public int $cfuCurriculari;
    public int $cfuInMedia;
    private CorsoDiLaurea $corsoDiLaurea;
    public string $nome;
    public string $cognome;
    public string $mail;

    public function __construct(int $matricola, CorsoDiLaurea $corsoDiLaurea)
    {
        $this->matricola = $matricola;
        $this->corsoDiLaurea = $corsoDiLaurea;

        $this->costruisciAnagrafica();
        $this->costruisciCarriera();
        $this->calcolaStatistiche();
    }

    protected function costruisciAnagrafica(): void
    {
        $anagrafica = GestioneCarrieraStudente::restituisciAnagraficaStudente(
            $this->matricola
        );
        $anagrafica = json_decode($anagrafica, true)["Entries"]["Entry"];

        $this->nome = $anagrafica["nome"];
        $this->cognome = $anagrafica["cognome"];
        $this->mail = $anagrafica["email_ate"];
    }

    protected function costruisciCarriera(): void
    {
        $carriera = GestioneCarrieraStudente::restituisciCarrieraStudente(
            $this->matricola
        );
        $carriera = json_decode($carriera, true)["Esami"]["Esame"];
        $this->costruisciCarrieraConJSON($carriera);
    }

    protected function costruisciCarrieraConJSON(array $carriera): void
    {
        $this->esami = [];
        $filtroEsami = json_decode(
            file_get_contents("../config/filtroEsami.json")
        );

        foreach ($carriera as $esameJson) {
            // Evita eventuali esami pre-verbalizzati della magistrale
            if (gettype($esameJson["DES"]) != "string") {
                continue;
            }

            $esame = new Esame(
                $esameJson["DES"],
                $esameJson["DATA_ESAME"],
                $esameJson["VOTO"],
                $esameJson["PESO"],
                $this->corsoDiLaurea->lode
            );

            $inCurriculum = $esame->controllaValidita(
                $filtroEsami,
                $esameJson["MATRICOLA"]
            );

            if (!$inCurriculum) {
                continue;
            }

            $this->esami[] = $esame;
        }

        Esame::ordinaPerData($this->esami);
    }

    protected function calcolaStatistiche(): void
    {
        $this->mediaPesata = 0.0;
        $this->cfuCurriculari = 0;
        $this->cfuInMedia = 0;

        foreach ($this->esami as $esame) {
            if ($esame->faMedia) {
                $this->cfuInMedia += $esame->cfu;
                $this->mediaPesata += $esame->voto * $esame->cfu;
            }
            $this->cfuCurriculari += $esame->cfu;
        }
        $this->mediaPesata = $this->mediaPesata / $this->cfuInMedia;
    }

}
