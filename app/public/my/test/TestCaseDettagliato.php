<?php

namespace my\src;

use Mpdf\Mpdf;
use Mpdf\MpdfException;

require_once("../vendor/autoload.php");
require_once("TestCase.php");

class TestCaseDettagliato extends TestCase
{

    /**
     * @throws MpdfException
     */
    public static function inizializza(): void
    {
        $files = parent::cercaTestCases();
        foreach ($files as $file) {
            $testCase = new TestCaseDettagliato($file);
            $testCase->avvia();
        }
    }

    /**
     * @throws MpdfException
     */
    protected function avvia(): void
    {
        echo '
<div class="output">
    <span class="testcase">'
            . $this->prospettoLaureando->laureando->matricola
            . '</span>
    <span class="intestazione">Test automatizzato</span>
    <span class="intestazione">PDF calcolato</span>
    <span class="intestazione">PDF atteso</span>
    <table>
        <tr>
            <th>Proprietà</th>
            <th>Valore calcolato</th>
            <th>Valore atteso</th>
        </tr>';

        $this->testaAnagrafica();
        $this->testaStatistiche();
        $this->testaSimulazione();
        [$mioPDF, $testPDF] = $this->testaPDF();

        echo '
    </table>
    <iframe src="' . $mioPDF . '#toolbar=0"></iframe>
    <iframe src="' . $testPDF . '#toolbar=0"></iframe>
</div>';
    }

    protected function testaAnagrafica(): void
    {
        echo '
        <tr>
            <td colspan="3" class="sezione">Anagrafica</td>
        </tr>';
        parent::testaAnagrafica();
    }

    protected function testaStatistiche(): void
    {
        echo '
        <tr>
            <td colspan="3" class="sezione">Statistiche</td>
        </tr>';
        parent::testaStatistiche();
    }

    protected function testaSimulazione(): void
    {
        echo '
        <tr>
            <td colspan="3" class="sezione">Simulazione</td>
        </tr>';
        parent::testaSimulazione();
    }

    /**
     * @throws MpdfException
     */
    private function testaPDF(): array
    {
        $this->prospettoLaureando->aggiungiSimulazioneVoto();

        $percorso = "../test/cases/"
            . $this->prospettoLaureando->laureando->matricola;
        $mioPDF = $percorso . "_output_my.pdf";
        $testPDF = $percorso . "_output.pdf";

        $prospettoPDF = new Mpdf();
        $prospettoPDF->WriteHTML($this->prospettoLaureando->esporta());
        $prospettoPDF->Output($mioPDF, "F");

        return [$mioPDF, $testPDF];
    }

    protected function scriviTest(
        string $nomeProprieta,
        mixed $valoreProprieta,
        mixed $valoreProprietaAttesa
    ): void {
        $class = $valoreProprieta != $valoreProprietaAttesa ? "error" : "pass";
        echo '
        <tr>
            <td>' . $nomeProprieta . '</td>
            <td class="' . $class . '">' . $valoreProprieta . '</td>
            <td class="' . $class . '">' . $valoreProprietaAttesa . '</td>
        </tr>';
    }
}
