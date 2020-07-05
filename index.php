<?php
include_once('vendor/autoload.php');
$_SERVER['REQUEST_URI'] = "/" . trim($_SERVER['REQUEST_URI'], "/");
use Pug\Facade as PugFacade;

$router = new \Bramus\Router\Router();
$router->get('/', function () {
  echo PugFacade::displayFile(__DIR__ . '/views/index.pug');
});

$router->get('/users', function () {
  echo "Users Route"; 
});

$router->mount('/example', function () use($router) {
  require('routers/example.router.php');
});

$router->mount('/auth', function () use($router) {
  include('routers/auth.router.php');
});

$router->mount('/register', function () use($router) {
  include('routers/register.router.php');
});

$router->mount('/admin', function () use($router) {
  include('routers/admin.router.php');  
});

// Nếu trang web không tìm thấy thì trả về lỗi 404
$router->set404(function () {
  http_response_code(404);
  echo PugFacade::displayFile(__DIR__ . '/views/404.pug');
});
$router->run();
