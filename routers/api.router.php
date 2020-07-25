<?php
include('../controllers/api.controller.php');
// include('../models/auth.model.php');
// include('../modules/Cypher.php');
use AuthModel\AuthModel as AuthModel;
use Chirp\Cryptor as Cryptor;


$test = function() {
  $encryption_key = 'CKXH26AMJ5';
  $token = "The quick brown fox jumps over the lazy dog.";
  $cryptor = new Cryptor($encryption_key);
  $crypted_token = $cryptor->encrypt($token);
  // print_r(md5(md5('admin')));
  // print_r($crypted_token);
  // print_r($cryptor->decrypt($crypted_token));
};

$router->get('/locals',$getProvince);
$router->get('/locals/{provinceid}/{districtid}',$getward);
$router->get('/locals/{provinceid}',$getDistrict);
$router->get('/test', $test);

