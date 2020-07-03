<?php
include_once('vendor/autoload.php');
include_once('helpers.php');

use Pecee\SimpleRouter\SimpleRouter;
use Pug\Facade as PugFacade;

SimpleRouter::get('/', function () {
  
  return PugFacade::displayFile(__DIR__ . '/views/index.pug');
});
SimpleRouter::get('/users/', function () {
  return "Users Route";
});

// Liên kết tới một Controller đơn giản
// https://github.com/skipperbent/simple-php-router#route-prefixes
SimpleRouter::group(['prefix' => '/example'], function () {
  include('controllers/example.php');
});


// Bắt lỗi và trả về khi người dùng truy cập vào địa chỉ không có thực
SimpleRouter::get('/404', function () {
  http_response_code(404);
  return PugFacade::displayFile(__DIR__ . '/views/404.pug');
});

SimpleRouter::error(function (Pecee\Http\Request $request, \Exception $exception) {
  if ($exception instanceof Pecee\SimpleRouter\Exceptions\NotFoundHttpException && $exception->getCode() === 404) {
    response()->redirect('/404');
  }
});

SimpleRouter::start();
