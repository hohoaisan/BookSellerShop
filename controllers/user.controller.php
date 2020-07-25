<?php
include('../models/user.model.php');
include('api.controller.php');
include('auth.controller.php');
use Pug\Facade as PugFacade;
use UserModel\UserModel as UserModel;  


$index  = function() {
  header('location: /user/profile');
  exit();
};
$user_profile  = function() {
  echo PugFacade::displayFile('../views/home/user/userInfo.jade');
};
$user_address  = function() use($getFullAddressInfo,$getUserInfo ) {
  $userInfo = $getUserInfo();
  $addressInfo = $getFullAddressInfo($userInfo['addressid']);
  echo PugFacade::displayFile('../views/home/user/userAddress.jade', [
    'address' => $addressInfo,
  ]);
};
$user_orders  = function() {
  echo PugFacade::displayFile('../views/home/user/userOrders.jade');
};
$user_rating  = function() {
  echo PugFacade::displayFile('../views/home/user/userRating.jade');
};

