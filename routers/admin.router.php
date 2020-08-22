<?php
// Lấy url của router và xử lí để tránh lỗi, url sẽ được đưa vào các thẻ a, form, ... liên quan đến router hiện tại

// Đưa các hàm trong controller vào router
// $router chính là $router từ index.php truyền sang bằng use($router),
//nhằm tiếp nhận những đưỡng dẫn phía sau /admin/... thông qua hàm mount
use Admin\AdminController;

use Auth\AuthController;

$router->before('GET|POST', '*', 'Auth\AuthController@requireAdmin');
$router->before('GET|POST', '.*', 'Auth\AuthController@requireAdmin');

$router->get('/', 'Admin\AdminController@index');

$router->get('/orders', 'Admin\AdminController@orders');
$router->get('/orders/{orderid}', 'Admin\AdminController@orderJSON');
$router->post('/orders/{orderid}/reject', 'Admin\AdminController@orderReject');
$router->post('/orders/{orderid}/accept', 'Admin\AdminController@orderAccept');
$router->post('/orders/{orderid}/complete', 'Admin\AdminController@orderComplete');
$router->post('/orders/{orderid}/error', 'Admin\AdminController@orderError');

$router->get('/users', 'Admin\AdminController@users');
$router->post('/users/{userid}/disable', 'Admin\AdminController@userDisable');
$router->post('/users/{userid}/enable', 'Admin\AdminController@userEnable');
$router->post('/users/{userid}/delete', 'Admin\AdminController@userDelete');
$router->post('/users/{userid}/makeadmin', 'Admin\AdminController@userMakeAdmin');
$router->post('/users/{userid}/removeadmin', 'Admin\AdminController@userRemoveAdmin');
$router->get('/users/{userid}/','Admin\AdminController@getUserJSON');

$router->get('/books', 'Admin\AdminController@books');
// $router->get('/books?q=', $books);
// $router->get('/books?author=id', $books);
// $router->get('/books?category=id', $books);
$router->get('/books/add', 'Admin\AdminController@bookAdd');
$router->post('/books/add', 'Admin\AdminController@postBookAdd');
$router->get('/books/{bookid}/edit', 'Admin\AdminController@bookEdit');
$router->post('/books/{bookid}/edit', 'Admin\AdminController@postBookEdit');
$router->post('/books/{bookid}/delete', 'Admin\AdminController@bookDelete');
$router->post('/books/{bookid}/smpedit', 'Admin\AdminController@postBookEditSimple');

$router->get('/authors', 'Admin\AdminController@authors');
$router->post('/authors/add', 'Admin\AdminController@authorAdd');
$router->post('/authors/{authorid}/edit', 'Admin\AdminController@authorEdit');
$router->post('/authors/{authorid}/delete', 'Admin\AdminController@authorDelete');

$router->get('/categories', 'Admin\AdminController@categories');
$router->post('/categories/add', 'Admin\AdminController@categoryAdd');
$router->post('/categories/{categoryid}/edit', 'Admin\AdminController@categoryEdit');
$router->post('/categories/{categoryid}/delete', 'Admin\AdminController@categoryDelete');

$router->get('/banner', 'Admin\AdminController@banners');
$router->get('/banner/add', 'Admin\AdminController@bannerAdd');
$router->post('/banner/add', 'Admin\AdminController@postBannerAdd');
$router->post('/banner/{bookid}/delete', 'Admin\AdminController@bannerDelete');

