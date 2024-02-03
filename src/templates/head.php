<?php

declare(strict_types=1);
require_once(__DIR__ . "/../php/imports.php");

try {
    $session = Session::is_logged();
} catch (Throwable $e) {
    print_exception($e);
    return;
}
?>

<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CornCTF</title>
<link rel="icon" type="image/png" href="images/favicon.png">
<link href="css/style.css" rel="stylesheet">
<script src="js/beer.min.js" type="text/javascript" defer></script>
<script src="js/dark.js" type="module"></script>
<?php if (!$session) { ?>
    <script src="js/register.js" type="module"></script>
    <script src="js/login.js" type="module"></script>
<?php } ?>