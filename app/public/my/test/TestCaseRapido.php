<?php

namespace my\src;

require_once("TestCase.php");

class TestCaseRapido extends TestCase
{
    private int $corretti;
    private int $errati;

    protected function __construct(string $file)
    {
        parent::__construct($file);
        $this->errati = 0;
        $this->corretti = 0;
    }

    public static function inizializza(): void
    {
        echo '
    <table>
        <tr>
            <th>Test case</th>
            <th>Corretti</th>
            <th>Errati</th>
        </tr>';

        $totaleErrati = 0;
        $totaleCorretti = 0;
        $files = parent::cercaTestCases();

        foreach ($files as $file) {
            $testCase = new TestCaseRapido($file);
            $testCase->avvia();
            $totaleErrati += $testCase->errati;
            $totaleCorretti += $testCase->corretti;
        }

        echo '
        <tr>
            <th>Totale</th>
            <th class="pass">' . $totaleCorretti . '</th>
            <th class="error">' . $totaleErrati . '</th>
            <th>' . round(
                100 * $totaleCorretti / ($totaleErrati + $totaleCorretti),
                2
            ) . '%</th>
        </tr>
    </table>';
    }

    protected function avvia(): void
    {
        $this->testaAnagrafica();
        $this->testaStatistiche();
        $this->testaSimulazione();

        echo '    
        <tr>
            <td>' . $this->prospettoLaureando->laureando->matricola . '</td>
            <td class="pass">' . $this->corretti . '</td>
            <td class="error">' . $this->errati . '</td>
            <td>' . round(
                100 * $this->corretti / ($this->errati + $this->corretti),
                2
            ) . '%</td>
        </tr>';
    }

    protected function scriviTest(
        string $nomeProprieta,
        mixed $valoreProprieta,
        mixed $valoreProprietaAttesa
    ): void {
        if ($valoreProprieta != $valoreProprietaAttesa) {
            $this->errati++;
        } else {
            $this->corretti++;
        }
    }
}