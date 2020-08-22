<?php
// include('../controllers/book.controller.php');

use Book\BookController;
$router->get('/', 'Book\BookController@index');
$router->get('/{bookid}', 'Book\BookController@bookDetail');
?>