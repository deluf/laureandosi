<?php

namespace my\src;

/*
 * FIXME:
 *  A scopo di test tutte le chiamate al database vengono emulate localmente
 *  sfruttando i test case, prima del deployment sostituire l'emulatore con
 *  il database effettivo
*/
class GestioneCarrieraStudente
{
    public static function restituisciAnagraficaStudente(int $matricola): string
    {
        return file_get_contents("../test/cases/" . $matricola . "_anagrafica.json");
    }

    public static function restituisciCarrieraStudente(int $matricola): string
    {
        return file_get_contents("../test/cases/" . $matricola . "_esami.json");
    }
}
