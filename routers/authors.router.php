<?php
include('../controllers/authors.controller.php');

$router->get('/', $index);
$router->get('/{authorID}', $authorDetail);

?>