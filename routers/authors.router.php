<?php
include('../controllers/author.controller.php');

$router->get('/', $index);
$router->get('/{authorID}', $authorDetail);

?>