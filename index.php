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
  // Gọi tấT cả những path router từ example.router sang
  require('routers/example.router.php');
});


// Nếu trang web không tìm thấy thì trả về lỗi 404
$router->set404(function () {
  http_response_code(404);
  echo PugFacade::displayFile(__DIR__ . '/views/404.pug');
});
$router->run();
