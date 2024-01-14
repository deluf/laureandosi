<?php

/**
 * Template Name: App
 */
if (!is_user_logged_in()) {
    auth_redirect();
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laureandosi2</title>
    <!--suppress HtmlUnknownTarget -->
    <link rel="stylesheet" href="/my/gui/landing.css">
    <!--suppress HtmlUnknownTarget -->
    <link rel="stylesheet" href="/my/gui/app.css">
</head>
<body>
<h1>Laureandosi 2.0</h1>
<h2>Gestione Prospetti di Laurea</h2>
<!--suppress HtmlUnknownTarget -->
<form action="/my/src/InterfacciaUtente.php" method="post">
    <div class="left">
        <label for="CdL">CdL:</label>
        <select id="CdL" name="CdL">
            <option value=""
                <?php
                if (empty($_GET["CdL"])) {
                    echo 'selected';
                } ?>
            >Seleziona un CdL
            </option>
            <?php
            $corsiDiLaurea = json_decode(
                file_get_contents("my/config/corsiDiLaurea.json"),
                true
            );
            foreach ($corsiDiLaurea as $corsoDiLaurea) {
                $selected = isset($_GET["CdL"]) &&
                    ($_GET["CdL"] == $corsoDiLaurea["nomeCorto"]);
                echo '<option value="' . $corsoDiLaurea["nomeCorto"] . '" '
                    . ($selected ? 'selected' : '')
                    . '>' . $corsoDiLaurea["nomeCompleto"] . '</option>';
            } ?>
        </select>
        <label for="dataLaurea">Data Laurea:</label>
        <input type="date" id="dataLaurea" name="dataLaurea"
            <?php
            if (isset($_GET["dataLaurea"])) {
                echo 'value="' . $_GET["dataLaurea"] . '"';
            } ?>
        >
    </div>
    <div class="center">
        <label for="matricole">Matricole:</label>
        <textarea id="matricole" name="matricole"><?php
            if (isset($_GET["matricole"])) {
                echo $_GET["matricole"];
            }
            ?></textarea>
    </div>
    <div class="right">
        <input type="submit" class="button" value="Crea prospetti"
               name="creaProspetti">
        <input type="submit" class="link" value="Apri prospetti"
               name="apriProspetti">
        <input type="button" class="button" value="Invia prospetti"
               name="inviaProspetti">
        <span id="info">
            <?php
            if (isset($_GET["creaProspettiOk"])) {
                echo 'Prospetti creati';
            }
            ?>
            </span>
    </div>
</form>
<a class="back" href="/">Torna indietro</a>
<!--suppress HtmlUnknownTarget -->
<script src="/my/gui/gestoreInvioMail.js"></script>
</body>
</html>