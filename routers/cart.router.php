<?php

use Pug\Facade as PugFacade;

use Cart\CartController;


$router->get('/', Closure::fromCallable('Cart\CartController::index'));
$router->post('/remove', Closure::fromCallable('Cart\CartController::removeItem'));
$router->post('/removeAll', Closure::fromCallable('Cart\CartController::removeAllItem'));
$router->post('/add', Closure::fromCallable('Cart\CartController::addItem'));
$router->post('/edit', Closure::fromCallable('Cart\CartController::editItem'));
$router->get('/getJSON', Closure::fromCallable('Cart\CartController::getJSON'));
$router->get('/purchase', Closure::fromCallable('Cart\CartController::purchaseCart'));

$router->post('/purchase/process', Closure::fromCallable('Cart\CartController::purchaseProcess'));
$router->post('/purchase/process/edit', Closure::fromCallable('Cart\CartController::purchaseProcessEdit'));
$router->post('/purchase/process/confirm', Closure::fromCallable('Cart\CartController::purchaseComplete'));
$router->get('/purchase/process', function() {
  header('location: /cart/purchase');
});


?>