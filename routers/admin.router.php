<?php
// Lấy url của router và xử lí để tránh lỗi, url sẽ được đưa vào các thẻ a, form, ... liên quan đến router hiện tại

// Đưa các hàm trong controller vào router
include_once('../controllers/admin.controller.php');
// $router chính là $router từ index.php truyền sang bằng use($router),
//nhằm tiếp nhận những đưỡng dẫn phía sau /admin/... thông qua hàm mount

$router->before('GET|POST', '*', function() {
  if (isset($_COOKIE['userid']) && isset($_COOKIE['admin'] ) && $_COOKIE['admin']=="1") {
    //Tiếp tục chạy các hàm bên dưới
  }
  else {
    // Nếu chưa đăng nhập và chưa phải là admin thì bắt buộc phải login
    header('location: /auth/login');
  }
});
$router->get('/', $index);