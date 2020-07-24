<?php
include('../controllers/user.controller.php');


$requireLogin = function () {
  if (isset($_COOKIE['userid'])) {
  } else {

    header('location: /auth/login');
  }
};

$router->before('GET|POST', '*', $requireLogin);
$router->before('GET|POST', '.*', $requireLogin);


$router->get('/', $index);
$router->get('/profile', $user_profile);
$router->get('/address', $user_address);
$router->get('/orders', $user_orders);
$router->get('/rating', $user_rating);
?>