<?php
// Lấy url của router và xử lí để tránh lỗi, url sẽ được đưa vào các thẻ a, form, ... liên quan đến router hiện tại

// Đưa các hàm trong controller vào router
include_once('../controllers/admin.controller.php');
// $router chính là $router từ index.php truyền sang bằng use($router),
//nhằm tiếp nhận những đưỡng dẫn phía sau /admin/... thông qua hàm mount

$router->before('GET|POST', '/.*', function() {
  if (isset($_COOKIE['userid']) && isset($_COOKIE['admin'] ) && $_COOKIE['admin']=="1") {
    //Tiếp tục chạy các hàm bên dưới
  }
  else {
    // Nếu chưa đăng nhập và chưa phải là admin thì bắt buộc phải login
    header('location: /auth/login');
  }
});
$router->get('/', $index);

$router->get('/orders', $orders);
$router->get('/orders/{orderid}', $orderJSON);
$router->post('/orders/{orderid}/reject', $orderReject);
$router->post('/orders/{orderid}/accept', $orderAccept);
$router->post('/orders/{orderid}/complete', $orderComplete);
$router->post('/orders/{orderid}/error', $orderReject);

$router->get('/users', $users);
$router->post('/users/{userid}/disable', $usersDisable);
$router->post('/users/{userid}/delete', $usersDelete);
$router->post('/users/{userid}/makeadmin', $usersAdmin);

$router->get('/categories', $categories);
$router->get('/books', $books);
$router->get('/books/add', $bookAdd);
$router->post('/books/add', $postBookAdd);
$router->get('/books/edit/{bookid}', $bookEdit);
$router->post('/books/edit/{bookid}', $postBookEdit);

$router->get('/authors', $authors);
$router->post('/authors/add', $authorAdd);
$router->post('/authors/{authorid}/edit', $authorEdit);
$router->post('/authors/{authorid}/delete', $authorDelete);
