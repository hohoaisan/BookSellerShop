<?php
include_once('vendor/autoload.php');
include_once('models/connect.php');
include_once('controllers/home.controller.php');

$router = new \Bramus\Router\Router();
$_SERVER['REQUEST_URI'] = "/" . trim($_SERVER['REQUEST_URI'], "/");
use Pug\Facade as PugFacade;





$router->get('/', $index);
$router->get('/cart', $cart);
$router->get('/users', function () use($router) {include('routers/user.router.php');});
$router->mount('/auth', function () use($router) {include('routers/auth.router.php');});
$router->mount('/admin', function () use($router) {include('routers/admin.router.php');});
$router->mount('/api', function () use($router) {include('routers/api.router.php');});

$router->set404(function () {
  http_response_code(404);
  echo PugFacade::displayFile(__DIR__.'/views/home/404.jade');
});
$router->run();
