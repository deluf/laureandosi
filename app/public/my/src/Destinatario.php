<?php

namespace my\src;

class Destinatario
{
    public string $nomePDF;
    public string $mail;
    public bool $ricevuta;

    public function __Construct(string $nomePDF, string $mail)
    {
        $this->nomePDF = $nomePDF;
        $this->mail = $mail;
        $this->ricevuta = false;
    }
}