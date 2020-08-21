<?php
include_once('../models/user.model.php');
include('api.controller.php');
include('auth.controller.php');
include_once('../models/rating.model.php');
include('../modules/pagination.php');
use Pug\Facade as PugFacade;
use UserModel\UserModel as UserModel;
use Status\Status as Status;
use RatingModel\RatingModel as RatingModel;
use AdminModel\AdminModel as AdminModel;

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
$user_profile  = function () use($getUserInfo) {
  $errors = Status::getErrors();
  $messages = Status::getMessages();
  $userInfo = $getUserInfo();
  echo PugFacade::displayFile('../views/home/user/userInfo.jade', [
    'user' => $userInfo,
    'errors' => $errors,
    'messages' => $messages
  ]);
};

$user_profile_edit_middleware = function () {
  $errors = [];
  if (!isset($_POST["fullname"]) || !$_POST["fullname"]) {
    array_push($errors, "Tên không được để trống");
  }
  if (!isset($_POST["phone"]) || !$_POST["phone"]) {
    array_push($errors, "Điện thoại không được để trống");
  }
  if (!isset($_POST["email"]) || !$_POST["email"]) {
    array_push($errors, "Email không được để trống");
  }
  if (!isset($_POST["male"]) || !in_array($_POST["male"], ["0", "1"])) {
    array_push($errors, "Giới tính phải hợp lệ");
  }
  if (count($errors)) {
    Status::addErrors($errors);
    header('location: /user/profile');
    exit();
  }

  
};


$user_profile_edit = function () use ($user_profile_edit_middleware, $getUserInfo) {
  $userInfo = $getUserInfo();
  $user_profile_edit_middleware();
  $fullname = $_POST["fullname"];
  $phone = $_POST["phone"];
  $email = $_POST["email"];
  $male = $_POST["male"];
  $dob = $_POST["dob"];
  $result  = UserModel::editUserInfo($userInfo["userid"], $fullname, $email, $phone, $male, $dob);
  if ($result) {
    Status::addMessage("Đã sửa thông tin thành công");
  }
  else {
    Status::addError("Có lỗi xảy ra trong quá trình sửa thông tin");
  }

  header('location: /user/profile');
  exit();

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

$user_orders  = function () use ($parseUser, $paginationGenerator){
  $errors = Status::getErrors();
  $messages = Status::getMessages();
  $user = $parseUser();
  $userid = $user['userid'];
  
  //pagination
  try {
    $currentPage = intval(isset($_GET['page']) ? $_GET['page'] : 1);
  } catch (Exception $e) {
    $currentPage = 1;
  }
  $itemperpage = 4;

  $fetch = UserModel::getUserOrders($userid, $currentPage, $itemperpage);
  //print_r($fetch);
  if (!$fetch) {
    array_push($errors, "Có lỗi xảy ra");
    $result = [];
  }

  $result = $fetch['result']; 
  $num_records = $fetch['rowcount']; 
  $num_page = ceil($num_records / $itemperpage); 
  $pagination = $paginationGenerator($currentPage, $num_page);

  echo PugFacade::displayFile('../views/home/user/userOrders.jade',[
    'errors' => $errors,
    'messages' => $messages,
    'orders' => $result,
    'pagination' => $pagination,
    'pagination_current_page' => $currentPage
  ]);
};

$user_orderJSON = function ($orderid) {
  $result = AdminModel::getOrder($orderid);
  $result["books"] =  AdminModel::getOrderDetail($orderid);
  echo json_encode($result, JSON_UNESCAPED_UNICODE);
};

$user_rating  = function () use($getUserInfo) {
  $errors = Status::getErrors();
  $messages = Status::getMessages();
  $user = $getUserInfo();
  $userid = $user["userid"];
  $ratinglist = RatingModel::getPurchasedBooksWithRating($userid);
  echo PugFacade::displayFile('../views/home/user/userRating.jade', [
    'ratinglist' => $ratinglist,
    'messages' => $messages,
    'errors' => $errors,
  ]);
};
