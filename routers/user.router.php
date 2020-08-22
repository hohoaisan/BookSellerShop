<?php

use User\UserController;
use Auth\AuthController;
$router->before('GET|POST', '*', 'Auth\AuthController@requireLogin');
$router->before('GET|POST', '.*', 'Auth\AuthController@requireLogin');

$router->get('/', 'User\UserController@index');
$router->get('/profile', 'User\UserController@user_profile');
$router->post('/profile', 'User\UserController@user_profile_edit');
$router->get('/address', 'User\UserController@user_address');
$router->post('/address/edit', 'User\UserController@user_address_edit');
$router->get('/orders', 'User\UserController@user_orders');
$router->get('/orders/{orderid}', 'User\UserController@user_orderJSON');
$router->get('/rating', 'User\UserController@user_rating');
?>