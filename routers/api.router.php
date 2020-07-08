<?php
include_once('../controllers/api.controller.php');

$router->get('/locals',$getProvince);
$router->get('/locals/{provinceid}/{districtid}',$getward);
$router->get('/locals/{provinceid}',$getDistrict);
