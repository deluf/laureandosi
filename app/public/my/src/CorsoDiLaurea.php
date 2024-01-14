<?php

namespace my\src;

class CorsoDiLaurea
{
    public string $nomeCorto;
    public string $nomeCompleto;
    public string $formula;
    public string $codiceFormula;
    public int $cfu;
    public float $minT;
    public float $maxT;
    public float $stepT;
    public float $minC;
    public float $maxC;
    public float $stepC;
    public int $lode;
    public string $note;
    public string $oggettoMail;
    public string $corpoMail;

    public function __construct(string $nomeCortoCdL)
    {
        $corsoDiLaureaJSON = json_decode(
            file_get_contents("../config/corsiDiLaurea.json"),
            true
        )[$nomeCortoCdL];

        $this->nomeCorto = $corsoDiLaureaJSON["nomeCorto"];
        $this->nomeCompleto = $corsoDiLaureaJSON["nomeCompleto"];
        $this->formula = $corsoDiLaureaJSON["formula"];
        $this->codiceFormula = $corsoDiLaureaJSON["codiceFormula"];
        $this->cfu = $corsoDiLaureaJSON["cfu"];
        $this->minT = $corsoDiLaureaJSON["minT"];
        $this->maxT = $corsoDiLaureaJSON["maxT"];
        $this->stepT = $corsoDiLaureaJSON["stepT"];
        $this->minC = $corsoDiLaureaJSON["minC"];
        $this->maxC = $corsoDiLaureaJSON["maxC"];
        $this->stepC = $corsoDiLaureaJSON["stepC"];
        $this->lode = $corsoDiLaureaJSON["lode"];
        $this->note = $corsoDiLaureaJSON["note"];
        $this->oggettoMail = $corsoDiLaureaJSON["oggettoMail"];
        $this->corpoMail = $corsoDiLaureaJSON["corpoMail"];
    }
}