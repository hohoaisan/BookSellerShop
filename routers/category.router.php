<?php 
    include('../controllers/category.controller.php');
    $router->get('/', $index);
    $router->get('/{cateid}', $categoryBook);
?>