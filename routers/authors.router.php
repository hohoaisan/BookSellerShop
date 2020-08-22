<?php
use Author\AuthorController;
$router->get('/', Closure::fromCallable('Author\AuthorController::index'));
$router->get('/{authorID}', Closure::fromCallable('Author\AuthorController::authorDetail'));

?>