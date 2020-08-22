<?php

use Pug\Facade as PugFacade;

use Cart\CartController;


$router->get('/', 'Cart\CartController@index');
$router->post('/remove', 'Cart\CartController@removeItem');
$router->post('/removeAll', 'Cart\CartController@removeAllItem');
$router->post('/add', 'Cart\CartController@addItem');
$router->post('/edit', 'Cart\CartController@editItem');
$router->get('/getJSON', 'Cart\CartController@getJSON');
$router->get('/purchase', 'Cart\CartController@purchaseCart');

$router->post('/purchase/process', 'Cart\CartController@purchaseProcess');
$router->post('/purchase/process/edit', 'Cart\CartController@purchaseProcessEdit');
$router->post('/purchase/process/confirm', 'Cart\CartController@purchaseComplete');
$router->get('/purchase/process', function() {
  header('location: /cart/purchase');
});


?>