<?php

namespace my\src;

require_once("CorsoDiLaurea.php");

class SimulatoreVoto
{

    private CorsoDiLaurea $corsoDiLaurea;
    private float $M;
    private int $CFU;
    public array $simulazione;

    public function __construct(
        CorsoDiLaurea $corsoDiLaurea,
        float $M,
        int $CFU
    ) {
        $this->corsoDiLaurea = $corsoDiLaurea;
        $this->M = $M;
        $this->CFU = $CFU;
        $this->simulazione = [];
    }

    public function simula(): string
    {
        // In una simulazione di voto di laurea o varia il voto di tesi T o quello di commissione C
        if ($this->corsoDiLaurea->maxC == 0) {
            $minV = $this->corsoDiLaurea->minT;
            $maxV = $this->corsoDiLaurea->maxT;
            $stepV = $this->corsoDiLaurea->stepT;
        } else {
            $minV = $this->corsoDiLaurea->minC;
            $maxV = $this->corsoDiLaurea->maxC;
            $stepV = $this->corsoDiLaurea->stepC;
        }

        for ($v = $minV; $v <= $maxV; $v += $stepV) {
            $this->simulazione[] = [$v, round($this->calcolaVoto($v), 3)];
        }

        if (count($this->simulazione) > 10) {
            return $this->organizzaInQuattroColonne();
        } else {
            return $this->organizzaInDueColonne();
        }
    }

    /**
     * @noinspection PhpUnusedParameterInspection
     * @noinspection PhpUnusedLocalVariableInspection
     */
    private function calcolaVoto(float $V): float
    {
        // V è la componente variabile, che prende il posto di C o di T
        $M = $this->M;
        $CFU = $this->CFU;
        return eval("return " . $this->corsoDiLaurea->codiceFormula . ";");
    }

    private function organizzaInQuattroColonne(): string
    {
        $prospetto = '
    <div class="simulazione">
        <table>
            <tr>
                <td colspan="4">SIMULAZIONE DI VOTO DI LAUREA</td>
            </tr>
            <tr>' . str_repeat(
                '
                <td>' . ($this->corsoDiLaurea->maxC == 0 ? "VOTO TESI (T)" : "VOTO COMMISSIONE (C)") . '</td>
                <td>VOTO LAUREA</td>',
                2
            ) . '
            </tr>';

        $numeroRighe = count($this->simulazione);
        $numeroRigheMezzi = floor($numeroRighe / 2);

        for ($i = 0; $i <= $numeroRigheMezzi; $i++) {
            $prospetto .= '
            <tr>
                <td>' . $this->simulazione[$i][0] . '</td>
                <td>' . $this->simulazione[$i][1] . '</td>';

            if ($i == $numeroRigheMezzi) {
                break;
            }

            $prospetto .= '
                <td>' . $this->simulazione[$numeroRigheMezzi + 1 + $i][0] . '</td>
                <td>' . $this->simulazione[$numeroRigheMezzi + 1 + $i][1] . '</td>
            </tr>';
        }

        return $prospetto;
    }

    private function organizzaInDueColonne(): string
    {
        $prospetto = '
    <div class="simulazione">
        <table>
            <tr>
                <td colspan="2">SIMULAZIONE DI VOTO DI LAUREA</td>
            </tr>
            <tr>
                <td>' . ($this->corsoDiLaurea->maxC == 0 ? "VOTO TESI (T)" : "VOTO COMMISSIONE (C)") . '</td>
                <td>VOTO LAUREA</td>                                   
            </tr>';

        $numeroRighe = count($this->simulazione);

        for ($i = 0; $i < $numeroRighe; $i++) {
            $prospetto .= '
            <tr>
                <td>' . $this->simulazione[$i][0] . '</td>
                <td>' . $this->simulazione[$i][1] . '</td>
            </tr>';
        }

        return $prospetto;
    }

}