<?php
// Lấy url của router và xử lí để tránh lỗi, url sẽ được đưa vào các thẻ a, form, ... liên quan đến router hiện tại

// Đưa các hàm trong controller vào router
// $router chính là $router từ index.php truyền sang bằng use($router),
//nhằm tiếp nhận những đưỡng dẫn phía sau /admin/... thông qua hàm mount
use Admin\AdminController;

use Auth\AuthController;

$router->before('GET|POST', '*', Closure::fromCallable('Auth\AuthController::requireAdmin'));
$router->before('GET|POST', '.*', Closure::fromCallable('Auth\AuthController::requireAdmin'));

$router->get('/', Closure::fromCallable('Admin\AdminController::index'));

$router->get('/orders', Closure::fromCallable('Admin\AdminController::orders'));
$router->get('/orders/{orderid}', Closure::fromCallable('Admin\AdminController::orderJSON'));
$router->post('/orders/{orderid}/reject', Closure::fromCallable('Admin\AdminController::orderReject'));
$router->post('/orders/{orderid}/accept', Closure::fromCallable('Admin\AdminController::orderAccept'));
$router->post('/orders/{orderid}/complete', Closure::fromCallable('Admin\AdminController::orderComplete'));
$router->post('/orders/{orderid}/error', Closure::fromCallable('Admin\AdminController::orderError'));

$router->get('/users', Closure::fromCallable('Admin\AdminController::users'));
$router->post('/users/{userid}/disable', Closure::fromCallable('Admin\AdminController::userDisable'));
$router->post('/users/{userid}/enable', Closure::fromCallable('Admin\AdminController::userEnable'));
$router->post('/users/{userid}/delete', Closure::fromCallable('Admin\AdminController::userDelete'));
$router->post('/users/{userid}/makeadmin', Closure::fromCallable('Admin\AdminController::userMakeAdmin'));
$router->post('/users/{userid}/removeadmin', Closure::fromCallable('Admin\AdminController::userRemoveAdmin'));
$router->get('/users/{userid}/',Closure::fromCallable('Admin\AdminController::getUserJSON'));

$router->get('/books', Closure::fromCallable('Admin\AdminController::books'));
// $router->get('/books?q=', $books);
// $router->get('/books?author=id', $books);
// $router->get('/books?category=id', $books);
$router->get('/books/add', Closure::fromCallable('Admin\AdminController::bookAdd'));
$router->post('/books/add', Closure::fromCallable('Admin\AdminController::postBookAdd'));
$router->get('/books/{bookid}/edit', Closure::fromCallable('Admin\AdminController::bookEdit'));
$router->post('/books/{bookid}/edit', Closure::fromCallable('Admin\AdminController::postBookEdit'));
$router->post('/books/{bookid}/delete', Closure::fromCallable('Admin\AdminController::bookDelete'));
$router->post('/books/{bookid}/smpedit', Closure::fromCallable('Admin\AdminController::postBookEditSimple'));

$router->get('/authors', Closure::fromCallable('Admin\AdminController::authors'));
$router->post('/authors/add', Closure::fromCallable('Admin\AdminController::authorAdd'));
$router->post('/authors/{authorid}/edit', Closure::fromCallable('Admin\AdminController::authorEdit'));
$router->post('/authors/{authorid}/delete', Closure::fromCallable('Admin\AdminController::authorDelete'));

$router->get('/categories', Closure::fromCallable('Admin\AdminController::categories'));
$router->post('/categories/add', Closure::fromCallable('Admin\AdminController::categoryAdd'));
$router->post('/categories/{categoryid}/edit', Closure::fromCallable('Admin\AdminController::categoryEdit'));
$router->post('/categories/{categoryid}/delete', Closure::fromCallable('Admin\AdminController::categoryDelete'));

$router->get('/banner', Closure::fromCallable('Admin\AdminController::banners'));
$router->get('/banner/add', Closure::fromCallable('Admin\AdminController::bannerAdd'));
$router->post('/banner/add', Closure::fromCallable('Admin\AdminController::postBannerAdd'));
$router->post('/banner/{bookid}/delete', Closure::fromCallable('Admin\AdminController::bannerDelete'));

