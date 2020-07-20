<?php
include('../models/cart.model.php');
include_once('../controllers/auth.controller.php');
include_once('../controllers/api.controller.php');

use Status\Status as Status;
use Pug\Facade as PugFacade;
use CartModel\CartModel as CartModel;


$index = function () {
  $fetch = CartModel::getItemsDetail($_SESSION["cart"]);
  $result = [];
  if ($fetch) {
    $result = $fetch["books"];
    $totalMoney = $fetch["totalmoney"];
  }
  echo PugFacade::displayFile('../views/home/cart.jade', [
    'cartItems' => $result,
    'totalMoney' => $totalMoney
  ]);
};

$removeAllItem = function() {
  //Trả lại số lượng cho mặt hàng
  $_SESSION["cart"] = [];
  http_response_code(201);
  echo json_encode(array('status' => true), JSON_UNESCAPED_UNICODE);
  exit();
};

$removeItem = function () {
  $_POST = json_decode(file_get_contents("php://input"), true);
  if (isset($_POST["bookid"])) {
    $bookid = $_POST["bookid"];
    $condition = true;
    if ($condition) {
      unset($_SESSION["cart"][$bookid]);
      $fetch = CartModel::getItemsDetail($_SESSION["cart"]);
      $totalMoney = $fetch["totalmoney"];
      http_response_code(201);
      echo json_encode(array('status' => true, 'bookid' => $bookid, 'totalMoney' => $totalMoney), JSON_UNESCAPED_UNICODE);
      exit();
    } else {
      http_response_code(304);
      echo json_encode(array(
        'status' => false,
        'message' => "Không thể xoá khỏi giỏ hàng"
      ), JSON_UNESCAPED_UNICODE);
      exit();
    }
  }
  header('location: /cart/');
};


//Thiếu: Kiểm tra số lượng còn
//Nếu đủ thì trừ lui

$addItem = function () {
  $_POST = json_decode(file_get_contents("php://input"), true);
  if (isset($_POST["bookid"])) {
    $condition = true; //Kiểm tra số lượng còn
    if ($condition) {
      $bookid = $_POST["bookid"];
      if (isset($_SESSION["cart"][$bookid])) {
        $_SESSION["cart"][$bookid]++;
      } else {
        $_SESSION["cart"][$bookid] = 1;
      }
      http_response_code(201);
      echo json_encode(array('status' => true, 'bookid' => $bookid), JSON_UNESCAPED_UNICODE);
      exit();
    } else {
      http_response_code(304);
      echo json_encode(array(
        'status' => false,
        'message' => "Không thể thêm vào giỏ hàng"
      ), JSON_UNESCAPED_UNICODE);
      exit();
    }
  }
  header('location: /cart/');
};


$editItem = function () {
  $_POST = json_decode(file_get_contents("php://input"), true);
  if (isset($_POST["bookid"]) && isset($_POST["quantity"])) {
    $bookid = $_POST["bookid"];
    $quantity = $_POST["quantity"];
    if (isset($_SESSION["cart"][$bookid])) {
      $condition = true;
      if ($condition) {
        $_SESSION["cart"][$bookid] = $quantity;
        $fetch = CartModel::getItemsDetail($_SESSION["cart"]);
        $totalMoney = $fetch["totalmoney"];
        http_response_code(201);
        echo json_encode(array('status' => true, 'bookid' => $bookid, 'totalMoney' => $totalMoney), JSON_UNESCAPED_UNICODE);
        exit();
      } else {
        http_response_code(304);
        echo json_encode(array(
          'status' => false,
          'message' => "Không thể sửa vào giỏ hàng"
        ), JSON_UNESCAPED_UNICODE);
        exit();
      }
    } else {
      http_response_code(405);
      echo json_encode(array(
        'status' => false,
        'message' => "Giỏ hàng không tồn tại sản phẩm này"
      ), JSON_UNESCAPED_UNICODE);
      exit();
    }
  }
  echo "rejected";
  header('location: /cart/');
};

$getJSON = function () {

  $totalQuantity = 0;
  foreach ($_SESSION["cart"] as $id => $quantity) {
    $totalQuantity+=$quantity;
  }
  echo json_encode([
    'cart'=> $_SESSION["cart"],
    'total' => $totalQuantity
  ], JSON_UNESCAPED_UNICODE);
  // print_r(CartModel::getItemsDetail($_SESSION["cart"]));
  // print_r($_SESSION["cart"]);
};

$checkCartIsReady = function() {
  if (count($_SESSION["cart"]) == 0){
    header('location: /cart/');
    exit();
  }
};


$purchaseCart = function() use($checkCartIsReady,$getUserInfo,$getFullAddressInfo) {
  $checkCartIsReady();
  $userInfo = $getUserInfo();
  $addressInfo = $getFullAddressInfo($userInfo['addressid']);
  // select userid,username, fullname, phone, email, male,addressid, addresstext, dob
  echo PugFacade::displayFile('../views/home/cart.purchase.jade', [
    'user' => $userInfo,
    'address' =>$addressInfo
  ]);
};

$purchaseProcess = function() use($checkCartIsReady) {
  $checkCartIsReady();
  print_r($_POST);
};