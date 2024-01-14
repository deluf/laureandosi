<?php

/**
 * Template Name: Amministrazione
 */
if (!is_user_logged_in()) {
    auth_redirect();
}
elseif (get_current_user_id() != 1) {
    http_response_code(403);
    echo '
<html lang="en">
<head><title>403 Forbidden</title></head>
<body>
<h1 style="text-align: center;">403 Forbidden</h1>
</body>
</html>';
    die();
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
</head>
<body>
<h1>Laureandosi 2.0</h1>
<h2>Strumenti di amministazione</h2>
<a class="button" href="/admin">Pannello WordPress</a>
<!--suppress HtmlUnknownTarget -->
<form action="/my/test/Testing.php" method="post">
    <input type="submit" class="button" value="Test dettagliato"
           name="testDettagliato">
</form>
<!--suppress HtmlUnknownTarget -->
<form action="/my/test/Testing.php" method="post">
    <input type="submit" class="button" value="Test rapido" name="testRapido">
</form>
<a class="back" href="/">Torna indietro</a>
</body>
</html>