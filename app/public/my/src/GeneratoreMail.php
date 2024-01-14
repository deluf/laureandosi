<?php

namespace my\src;

use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;

require_once("Destinatario.php");
require_once("CorsoDiLaurea.php");
require_once("../vendor/autoload.php");

class GeneratoreMail
{
    private PHPMailer $mail;
    private CorsoDiLaurea $corsoDiLaurea;

    public function __construct(string $nomeCortoCdL)
    {
        $this->mail = new PHPMailer(true);
        $this->mail->CharSet = "UTF-8";
        $this->mail->IsSMTP();
        $this->mail->Host = "mixer.unipi.it";
        $this->mail->SMTPAuth = false;
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = 25;

        $this->corsoDiLaurea = new CorsoDiLaurea($nomeCortoCdL);
    }

    public function __destruct()
    {
        $this->mail->SmtpClose();
    }

    /**
     * @throws PHPMailerException
     */
    public function inviaMail(string $mail, string $allegato): void
    {
        $this->mail->setFrom("no-reply-laureandosi@ing.unipi.it");

        /*
         * FIXME:
         *  A scopo di test tutte le mail arrivano al mio indirizzo personale
         *  di ateneo, prima del deployment sostituire questo con $mail
         */
        $this->mail->AddAddress('f.delucchini@studenti.unipi.it');

        $this->mail->Subject = $this->corsoDiLaurea->oggettoMail;
        $this->mail->Body = $this->corsoDiLaurea->corpoMail;
        $this->mail->addAttachment($allegato);

        if (!$this->mail->Send()) {
            throw new PHPMailerException();
        }
    }

}