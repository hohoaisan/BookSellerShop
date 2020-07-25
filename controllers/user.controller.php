<?php
include_once('../models/user.model.php');
include('api.controller.php');
include('auth.controller.php');

use Pug\Facade as PugFacade;
use UserModel\UserModel as UserModel;
use Status\Status as Status;

$getUserInfo = function () use ($parseUser) {
  $user = $parseUser();
  if ($user) {
    $userid = $user['userid'];
    return UserModel::getUserInfo($userid);
  }
  return false;
};

$index  = function () {
  header('location: /user/profile');
  exit();
};
$user_profile  = function () {
  echo PugFacade::displayFile('../views/home/user/userInfo.jade');
};
$user_address  = function () use ($getFullAddressInfo, $getUserInfo) {
  $errors = Status::getErrors();
  $messages = Status::getMessages();
  $userInfo = $getUserInfo();
  $addressInfo = $getFullAddressInfo($userInfo['addressid']);

  echo PugFacade::displayFile('../views/home/user/userAddress.jade', [
    'errors' => $errors,
    'messages' => $messages,
    'address' => $addressInfo,
    'addresstext' => $userInfo["addresstext"]
  ]);
};



$user_address_edit_middleware = function ()  {
  if (isset($_POST['addressid']) && isset($_POST['addresstext'])) {
    if ($_POST['addressid'] && $_POST['addresstext']) {
      return;
    }
  }
  Status::addError('Địa chỉ nhập vào phải hợp lệ');
  header('location: /user/address');
  exit();
};

$user_address_edit = function () use ($parseUser, $user_address_edit_middleware) {
    $user_address_edit_middleware();
    $user = $parseUser();
    $userid = $user['userid'];
    $addressid = $_POST['addressid'];
    $addresstext = $_POST['addresstext'];
    $result = UserModel::editUserAddress($userid,$addressid,$addresstext);
    if ($result) {
      if (isset($_POST['json'])) {
        echo json_encode(array(
          'status' => true,
          'message' => "Đã sửa địa chỉ thành công"
        ), JSON_UNESCAPED_UNICODE);
        exit();
      }
      else {
        Status::addMessage('Đã sửa địa chỉ thành công');
      }

    }
    else {
      if (isset($_POST['json'])) {
        echo json_encode(array(
          'status' => false,
          'message' => "Có lỗi xảy ra khi sửa địa chỉ"
        ), JSON_UNESCAPED_UNICODE);
        exit();
      }
      else {
        Status::addError('Có lỗi xảy ra khi thêm địa chỉ');
      }
    }
    header('location: /user/address');
    exit();
  
};

$user_orders  = function () {
  echo PugFacade::displayFile('../views/home/user/userOrders.jade');
};
$user_rating  = function () {
  echo PugFacade::displayFile('../views/home/user/userRating.jade');
};
