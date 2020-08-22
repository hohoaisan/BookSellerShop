<?php
// include('../controllers/book.controller.php');

use Book\BookController;
$router->get('/', Closure::fromCallable('Book\BookController::index'));
$router->get('/{bookid}', Closure::fromCallable('Book\BookController::bookDetail'));
?>