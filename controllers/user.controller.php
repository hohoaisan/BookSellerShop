<?php
include('../models/user.model.php');
use Pug\Facade as PugFacade;
use UserModel\UserModel as UserModel;  


$index  = function() {
  header('location: /user/profile');
  exit();
};
$user_profile  = function() {
  echo PugFacade::displayFile('../views/home/user/userInfo.jade');
};
$user_address  = function() {
  echo PugFacade::displayFile('../views/home/user/userAddress.jade');
};
$user_orders  = function() {
  echo PugFacade::displayFile('../views/home/user/userOrders.jade');
};
$user_rating  = function() {
  echo PugFacade::displayFile('../views/home/user/userRating.jade');
};

