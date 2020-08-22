<?php

use User\UserController;
use Auth\AuthController;
$router->before('GET|POST', '*', Closure::fromCallable('Auth\AuthController::requireLogin'));
$router->before('GET|POST', '.*', Closure::fromCallable('Auth\AuthController::requireLogin'));

$router->get('/', Closure::fromCallable('User\UserController::index'));
$router->get('/profile', Closure::fromCallable('User\UserController::user_profile'));
$router->post('/profile', Closure::fromCallable('User\UserController::user_profile_edit'));
$router->get('/address', Closure::fromCallable('User\UserController::user_address'));
$router->post('/address/edit', Closure::fromCallable('User\UserController::user_address_edit'));
$router->get('/orders', Closure::fromCallable('User\UserController::user_orders'));
$router->get('/orders/{orderid}', Closure::fromCallable('User\UserController::user_orderJSON'));
$router->get('/rating', Closure::fromCallable('User\UserController::user_rating'));
?>