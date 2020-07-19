<?php

include('../controllers/cart.controller.php');
use Pug\Facade as PugFacade;



$router->get('/', $index);
$router->post('/remove', $removeItem);
$router->post('/add', $addItem);
$router->post('/edit', $editItem);
$router->get('/show', $showCart);
?>