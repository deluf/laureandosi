<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laureandosi2</title>
    <!--suppress HtmlUnknownTarget -->
    <link rel="stylesheet" href="/my/gui/testing.css">
</head>
<body>
<?php

use my\src\TestCaseDettagliato;
use my\src\TestCaseRapido;

require_once("TestCaseDettagliato.php");
require_once("TestCaseRapido.php");

if (isset($_POST["testDettagliato"])) {
    /** @noinspection PhpUnhandledExceptionInspection */
    TestCaseDettagliato::inizializza();
} else {
    TestCaseRapido::inizializza();
}
?>
</body>
</html>
