<?php
// Lấy url của router và xử lí để tránh lỗi, url sẽ được đưa vào các thẻ a, form, ... liên quan đến router hiện tại

// Đưa các hàm trong controller vào router
include_once('../controllers/example.controller.php');

// $router chính là $router từ index.php truyền sang bằng use($router),
//nhằm tiếp nhận những đưỡng dẫn phía sau /admin/... thông qua hàm mount
$router->get('/', $getpostdatabaseexample);
$router->get('/subadmin', $subadmin);
$router->post('/subadmin', $postSubadmin);
// http://localhost/example/haha/subpath/bb
$router->get('/{param1}/subpath/{param2}', $routeparamexample);
