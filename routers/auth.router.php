<?php
use Auth\AuthController;
$router->get('/login', 'Auth\AuthController@login');
$router->get('/register', 'Auth\AuthController@register');
$router->post('/login', 'Auth\AuthController@postLogin');
$router->post('/register', 'Auth\AuthController@postRegister');
$router->all('/logout', 'Auth\AuthController@logout');
