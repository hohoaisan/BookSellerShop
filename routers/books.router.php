<?php
include('../controllers/book.controller.php');

$router->get('/{bookid}', $bookDetail);
?>