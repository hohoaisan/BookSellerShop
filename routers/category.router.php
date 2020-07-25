<?php 
    include('../controllers/category.controller.php');
    $router->get('/{cateid}', $categoryBook);
?>