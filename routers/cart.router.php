<?php

include('../controllers/cart.controller.php');
use Pug\Facade as PugFacade;



$router->get('/', $index);
$router->post('/remove', $removeItem);
$router->post('/removeAll', $removeAllItem);
$router->post('/add', $addItem);
$router->post('/edit', $editItem);
$router->get('/getJSON', $getJSON);
$router->get('/purchase', $purchaseCart);

$router->post('/purchase/process', $purchaseProcess);
$router->post('/purchase/process/edit', $purchaseProcessEdit);
$router->post('/purchase/process/confirm', $purchaseComplete);
$router->get('/purchase/process', function() {
  header('location: /cart/purchase');
});


?>