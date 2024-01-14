<?php

use my\src\Destinatario;
use my\src\GeneratoreMail;
use my\src\GeneratoreProspetti;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

require_once('GeneratoreProspetti.php');
require_once('GeneratoreMail.php');
require_once('Destinatario.php');

if (isset($_POST["creaProspetti"])) {
    if (
        empty($_POST["CdL"]) ||
        empty($_POST["matricole"]) ||
        empty($_POST["dataLaurea"])
    ) {
        echo "Riempire tutti i campi";
        http_response_code(400);
        die();
    }
    InterfacciaUtente::creaProspetti(
        $_POST["CdL"],
        $_POST["matricole"],
        $_POST["dataLaurea"]
    );
} elseif (isset($_POST["apriProspetti"])) {
    if (empty($_POST["CdL"])) {
        echo "Specificare il corso di laurea";
        http_response_code(400);
        die();
    }
    InterfacciaUtente::apriProspetti($_POST["CdL"]);
} elseif (isset($_POST["inviaProspetti"])) {
    if (empty($_POST["CdL"])) {
        echo "Specificare il corso di laurea";
        http_response_code(400);
        die();
    }
    InterfacciaUtente::inviaProspetti($_POST["CdL"]);
} else {
    http_response_code(400);
}


class InterfacciaUtente
{

    public static function creaProspetti(
        string $CdL,
        string $matricole,
        string $dataLaurea
    ): void {
        $intMatricole = array_map(
            'intval',
            preg_split('/\s+/', $matricole)
        );

        try {
            $generatoreProspetti = new GeneratoreProspetti(
                $CdL,
                $intMatricole,
                $dataLaurea
            );
            $generatoreProspetti->generaPDF();
        } catch (Throwable) {
            echo "Qualcosa è andato storto, probabilmente una matricola è sbagliata";
            http_response_code(500);
            die();
        }

        $requestURL =
            '?CdL=' . $CdL
            . '&dataLaurea=' . $dataLaurea
            . '&matricole=' . urlencode($matricole);

        header('Location: /app/' . $requestURL . '&creaProspettiOk');
    }

    public static function apriProspetti(string $CdL): void
    {
        $prospettoCommissione = '../out/' . $CdL . '/Commissione.pdf';
        if (!is_file($prospettoCommissione)) {
            echo "Nessun prospetto trovato, provare a crearli nuovamente";
            http_response_code(404);
            die();
        }
        header('Location: ' . $prospettoCommissione);
    }

    public static function inviaProspetti(string $CdL): void
    {
        $percorsoBase = "../out/" . $CdL;

        if (!is_file($percorsoBase . "/Destinatari.json")) {
            echo "Destinatari inesistenti, rigenerare i prospetti";
            http_response_code(400);
            die();
        }

        /** @var Destinatario[] $destinatari */
        $destinatari = json_decode(
            file_get_contents($percorsoBase . "/Destinatari.json"),
        );
        $mailTotali = count($destinatari);
        $mailInviate = 0;
        $generatoreMail = new GeneratoreMail($CdL);

        for (; $mailInviate < $mailTotali; $mailInviate++) {
            if ($destinatari[$mailInviate]->ricevuta) {
                continue;
            }

            try {
                /*
                 *  A scopo di testing, per simulare un invio scorretto ogni
                 *  tanto, si può utilizzare il seguente codice:
                 *
                sleep(1);
                if (random_int(0, 100) < 25) {
                    throw new PHPMailerException();
                }
                 *
                 */
                $generatoreMail->inviaMail(
                    $destinatari[$mailInviate]->mail,
                    $percorsoBase . "/" . $destinatari[$mailInviate]->nomePDF
                );
            } catch (PHPMailerException) {
                echo 'Errore invio prospetto ' . $mailInviate + 1 . ' di ' . $mailTotali;
                http_response_code(503);
                die();
            }

            $destinatari[$mailInviate]->ricevuta = true;
            file_put_contents(
                $percorsoBase . "/Destinatari.json",
                json_encode($destinatari, JSON_PRETTY_PRINT)
            );

            echo 'Prospetti inviati: ' . $mailInviate + 1 . ' su ' . $mailTotali;
            break;
        }

        if ($mailInviate + 1 >= $mailTotali) {
            // L'utente preme di nuovo invia mail dopo che sono state tutte inviate
            if ($mailInviate == $mailTotali) {
                echo "I destinatari di questo CdL hanno già ricevuto le mail";
            }
            http_response_code(201);
        }
    }

}
