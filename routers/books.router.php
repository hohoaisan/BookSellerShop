<?php
include('../controllers/book.controller.php');

$router->get('/', $index);
$router->get('/{bookid}', $bookDetail);
?>