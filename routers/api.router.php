<?php

use AuthModel\AuthModel as AuthModel;
use Chirp\Cryptor as Cryptor;


// $test = function() {
//   $encryption_key = 'CKXH26AMJ5';
//   $token = "The quick brown fox jumps over the lazy dog.";
//   $cryptor = new Cryptor($encryption_key);
//   $crypted_token = $cryptor->encrypt($token);
// print_r(md5(md5('admin')));
// print_r($crypted_token);
// print_r($cryptor->decrypt($crypted_token));
// };
use API\APIController;

$router->get('/locals', Closure::fromCallable('API\APIController::getProvince'));
$router->get('/locals/{provinceid}/{districtid}', Closure::fromCallable('API\APIController::getward'));
$router->get('/locals/{provinceid}', Closure::fromCallable('API\APIController::getDistrict'));
//$router->get('/test', $test);
