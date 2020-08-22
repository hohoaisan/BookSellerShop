<?php
use Auth\AuthController;
// Lấy url của router và xử lí để tránh lỗi, url sẽ được đưa vào các thẻ a, form, ... liên quan đến router hiện tại

// Đưa các hàm trong controller vào router

// $router chính là $router từ index.php truyền sang bằng use($router),
//nhằm tiếp nhận những đưỡng dẫn phía sau /admin/... thông qua hàm mount
$router->get('/login', Closure::fromCallable('Auth\AuthController::login'));
$router->get('/register', Closure::fromCallable('Auth\AuthController::register'));
$router->post('/login', Closure::fromCallable('Auth\AuthController::postLogin'));
$router->post('/register', Closure::fromCallable('Auth\AuthController::postRegister'));
$router->all('/logout', Closure::fromCallable('Auth\AuthController::logout'));
// $router->all('/test', $test);


// function encryptCookie($value){
//   if(!$value){return false;}
//   $key = 'The Line Secret Key';
//   $text = $value;
//   $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
//   $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
//   $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_ECB, $iv);
//   return trim(base64_encode($crypttext)); //encode for cookie
// }

// function decryptCookie($value){
//   if(!$value){return false;}
//   $key = 'The Line Secret Key';
//   $crypttext = base64_decode($value); //decode cookie
//   $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
//   $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
//   $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $crypttext, MCRYPT_MODE_ECB, $iv);
//   return trim($decrypttext);
// }
// $test = function() {
  
// };