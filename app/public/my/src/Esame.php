<?php

namespace my\src;

use DateTime;

require_once("TipoEsame.php");
require_once("FiltroEsami.php");

class Esame
{
    public TipoEsame $tipo;
    public string $nome;
    private string $data;
    public ?int $voto;
    public int $cfu;
    public ?bool $faMedia;

    public function __construct(
        string $nome,
        string $data,
        ?string $voto,
        int $cfu,
        int $lode
    ) {
        $this->tipo = TipoEsame::STANDARD;
        $this->nome = $nome;
        $this->data = $data;
        $this->cfu = $cfu;

        if ($voto == "30  e lode") {
            $this->voto = $lode;
        } else {
            $this->voto = (int)$voto;
        }
    }

    /*
     * Returns:
     *  true se l'esame è in curriculum (in media o meno)
     *  false se l'esame non è in curriculum
     */

    public static function ordinaPerData(array &$esami): void
    {
        usort(
            $esami,
            function (Esame $a, Esame $b) {
                $ta = DateTime::createFromFormat("d/m/Y", $a->data);
                $tb = DateTime::createFromFormat("d/m/Y", $b->data);
                return ($ta->getTimestamp() - $tb->getTimestamp());
            }
        );
    }

    public function controllaValidita(array $filtroEsami, int $matricola): bool
    {
        /** @var FiltroEsami[] $filtroEsami */

        foreach ($filtroEsami as $filtro) {
            if ($filtro->matricola != $matricola and $filtro->matricola != "*") {
                continue;
            }
            if (in_array($this->nome, $filtro->esamiNonInCurriculum)) {
                return false;
            }
            foreach ($filtro->esamiNonInMedia as $esameNonInMedia) {
                if ($this->nome == $esameNonInMedia) {
                    $this->faMedia = false;
                    return true;
                }
            }
        }

        if ($this->voto == null) {
            $this->faMedia = false;
        } else {
            $this->faMedia = true;
        }
        return true;
    }

}
