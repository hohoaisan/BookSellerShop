<?php
session_start();
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

if (!isset($_SESSION["cart"])) {
  $_SESSION["cart"] = [];
}

include_once('modules/actionStatus.php');
include_once('vendor/autoload.php');
include_once('modules/Cypher.php');
include_once('modules/pagination.php');


include_once('models/connect.php');
include_once('models/author.model.php');
include_once('models/banner.model.php');
include_once('models/book.model.php');
include_once('models/category.model.php');
include_once('models/order.model.php');
include_once('models/payment.model.php');
include_once('models/rating.model.php');
include_once('models/shipping.model.php');
include_once('models/user.model.php');
include_once('models/config.model.php');


include_once('controllers/admin.controller.php');
include_once('controllers/api.controller.php');
include_once('controllers/auth.controller.php');
include_once('controllers/author.controller.php');
include_once('controllers/book.controller.php');
include_once('controllers/cart.controller.php');
include_once('controllers/category.controller.php');
include_once('controllers/home.controller.php');
include_once('controllers/rating.controller.php');
include_once('controllers/user.controller.php');



$router = new \Bramus\Router\Router();
$_SERVER['REQUEST_URI'] = "/" . trim($_SERVER['REQUEST_URI'], "/");


use Pug\Facade as PugFacade;
use User\UserController;
use Home\HomeController;


use Chirp\Cryptor as Cryptor;
use Config\ConfigModel;

$encryption_key = 'CKXH2U9RPY3EFD70TLS1ZG4N8WQBOVI6AMJ5';
$GLOBALS['cryptor'] = new Cryptor($encryption_key);


$_SESSION['authuser'] = UserController::getUserInfo();
$footer = ConfigModel::getFooterConfig();
PugFacade::share([
  'user' => $_SESSION['authuser'],
  'footer' => $footer
]);

// Hiển thị trang chủ



$router->get('/', 'Home\HomeController@index');
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
