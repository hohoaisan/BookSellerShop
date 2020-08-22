<?php
use Author\AuthorController;
$router->get('/', 'Author\AuthorController@index');
$router->get('/{authorID}', 'Author\AuthorController@authorDetail');

?>