<?php
session_start();
if (!isset($_SESSION["cart"])) {
  $_SESSION["cart"] = [];
}

include_once('modules/actionStatus.php');
include_once('vendor/autoload.php');
include_once('models/connect.php');
include_once('modules/Cypher.php');



$router = new \Bramus\Router\Router();
$_SERVER['REQUEST_URI'] = "/" . trim($_SERVER['REQUEST_URI'], "/");
use Pug\Facade as PugFacade;





// Hiển thị trang chủ
include_once('controllers/home.controller.php');
$router->get('/', $index);


// Điều hướng (router) tới các khu vực khác nhau trong website
$router->mount('/cart', function () use($router) {include('routers/cart.router.php');});
$router->mount('/authors', function () use($router) {include('routers/authors.router.php');});
$router->mount('/categories', function () use($router) {include('routers/category.router.php');});
$router->mount('/books', function () use($router) {include('routers/books.router.php');});
$router->mount('/auth', function () use($router) {include('routers/auth.router.php');});
$router->mount('/admin', function () use($router) {include('routers/admin.router.php');});
$router->mount('/api', function () use($router) {include('routers/api.router.php');});
$router->mount('/user', function () use($router) {include('routers/user.router.php');});
$router->mount('/rating', function () use($router) {include('routers/rating.router.php');});


// Hiển thị trang 404 nếu không tìm thấy
$router->set404(function () {
  http_response_code(404);
  echo PugFacade::displayFile(__DIR__.'/views/home/404.jade');
});
$router->run();
