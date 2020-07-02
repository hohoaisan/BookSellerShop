<?php

include_once('vendor/autoload.php');

$pug = new Pug();

$pug->displayFile('views/404.pug');
http_response_code(404);
?>