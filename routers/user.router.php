<?php
include('../controllers/user.controller.php');
include('../controllers/auth.controller.php');


$router->before('GET|POST', '*', $requireLogin);
$router->before('GET|POST', '.*', $requireLogin);


$router->get('/', $index);
$router->get('/profile', $user_profile);
$router->post('/profile', $user_profile_edit);
$router->get('/address', $user_address);
$router->post('/address/edit', $user_address_edit);
$router->get('/orders', $user_orders);
$router->get('/rating', $user_rating);
?>