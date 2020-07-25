<?php
// Lấy url của router và xử lí để tránh lỗi, url sẽ được đưa vào các thẻ a, form, ... liên quan đến router hiện tại

// Đưa các hàm trong controller vào router
include_once('../controllers/admin.controller.php');
include('../controllers/auth.controller.php');
// $router chính là $router từ index.php truyền sang bằng use($router),
//nhằm tiếp nhận những đưỡng dẫn phía sau /admin/... thông qua hàm mount

$router->before('GET|POST', '*', $requireAdmin);
$router->before('GET|POST', '.*', $requireAdmin);

$router->get('/', $index);

$router->get('/orders', $orders);
$router->get('/orders/{orderid}', $orderJSON);
$router->post('/orders/{orderid}/reject', $orderReject);
$router->post('/orders/{orderid}/accept', $orderAccept);
$router->post('/orders/{orderid}/complete', $orderComplete);
$router->post('/orders/{orderid}/error', $orderError);

$router->get('/users', $users);
$router->post('/users/{userid}/disable', $userDisable);
$router->post('/users/{userid}/enable', $userEnable);
$router->post('/users/{userid}/delete', $userDelete);
$router->post('/users/{userid}/makeadmin', $userMakeAdmin);
$router->post('/users/{userid}/removeadmin', $userRemoveAdmin);
$router->get('/users/{userid}/',$getUserJSON);

$router->get('/books', $books);
// $router->get('/books?q=', $books);
// $router->get('/books?author=id', $books);
// $router->get('/books?category=id', $books);
$router->get('/books/add', $bookAdd);
$router->post('/books/add', $postBookAdd);
$router->get('/books/{bookid}/edit', $bookEdit);
$router->post('/books/{bookid}/edit', $postBookEdit);
$router->post('/books/{bookid}/delete', $bookDelete);
$router->post('/books/{bookid}/smpedit', $postBookEditSimple);

$router->get('/authors', $authors);
$router->post('/authors/add', $authorAdd);
$router->post('/authors/{authorid}/edit', $authorEdit);
$router->post('/authors/{authorid}/delete', $authorDelete);

$router->get('/categories', $categories);
$router->post('/categories/add', $categoryAdd);
$router->post('/categories/{categoryid}/edit', $categoryEdit);
$router->post('/categories/{categoryid}/delete', $categoryDelete);
