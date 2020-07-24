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

$removeAllItem = function () {
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
  if (isset($_POST["bookid"]) && isset($_POST["quantity"])) {
    $condition = true; //Kiểm tra số lượng còn
    if ($condition) {
      $bookid = $_POST["bookid"];
      $quantity = $_POST["quantity"];
      if (isset($_SESSION["cart"][$bookid])) {
        $_SESSION["cart"][$bookid] += $quantity;
      } else {
        $_SESSION["cart"][$bookid] = $quantity;
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
    $totalQuantity += $quantity;
  }
  echo json_encode([
    'cart' => $_SESSION["cart"],
    'total' => $totalQuantity
  ], JSON_UNESCAPED_UNICODE);
  // print_r(CartModel::getItemsDetail($_SESSION["cart"]));
  // print_r($_SESSION["cart"]);
};

$checkCartIsReady = function () {
  if (count($_SESSION["cart"]) == 0) {
    header('location: /cart/');
    exit();
  }
};


$purchaseCart = function () use ($checkCartIsReady, $getUserInfo, $getFullAddressInfo) {
  $errors = Status::getErrors();
  $checkCartIsReady();
  $userInfo = $getUserInfo();
  $addressInfo = $getFullAddressInfo($userInfo['addressid']);
  // select userid,username, fullname, phone, email, male,addressid, addresstext, dob
  echo PugFacade::displayFile('../views/home/cart.purchase.jade', [
    'user' => $userInfo,
    'address' => $addressInfo,
    'errors' => $errors
  ]);
};

$purchaseProcessMiddleWare = function () {
  $errors = [];
  if ((!isset($_POST["userid"]) || $_POST["userid"] == "") and (!isset($_POST['receivername']) || $_POST['receivername'] == "")) {
    array_push($errors, "Phải có tên người nhận hoặc bạn phải đăng nhập");
  }

  if (!isset($_POST["phone"]) || $_POST["phone"] == "") {
    array_push($errors, "Phải có số điện thoại");
  }
  if (!isset($_POST["addressid"]) || $_POST["addressid"] == "") {
    array_push($errors, "Phải có địa chỉ địa phương");
  }
  if ($errors) {
    Status::addErrors($errors);
    header('location: /cart/purchase');
    exit();
  }
};

$purchaseProcess = function () use ($checkCartIsReady, $purchaseProcessMiddleWare, $getFullAddressInfo) {
  unset($_SESSION['orderInfo']);
  $checkCartIsReady();
  $purchaseProcessMiddleWare();
  $fetch = CartModel::getItemsDetail($_SESSION["cart"]);
  $result = $fetch["books"];
  $totalMoney = $fetch["totalmoney"];
  $payment = CartModel::getPaymentMethod();
  $shipping = CartModel::getShippingMethod();



  $_SESSION['orderInfo'] = [
    'userid' => isset($_POST['userid']) ? $_POST['userid'] : "",
    'receivername' => $_POST['receivername'],
    'phone' => $_POST['phone'],
    'addressid' => $_POST['addressid'],
    'addresstext' => $_POST['addresstext'],
    'totalAmount' => $totalMoney,
    'totalMoney' => $totalMoney,
  ];
  $addressInfo = $getFullAddressInfo($_SESSION['orderInfo']['addressid']);
  $_SESSION['orderInfo']['fullAddress'] = $_SESSION['orderInfo']['addresstext'] . ", " . $addressInfo["wardname"] . ", " . $addressInfo["districtname"] . ", " . $addressInfo["provincename"];
  $defaultShippingMethod = CartModel::getDefaultShippingMethod();
  $defaultPaymentMethod = CartModel::getDefaultPaymentMethod();
  $_SESSION['orderInfo']['paymentid'] = $defaultPaymentMethod['id'];
  $_SESSION['orderInfo']['shippingid'] = $defaultShippingMethod['id'];
  $_SESSION['orderInfo']['shippingprice'] = $defaultShippingMethod['pricing'];
  $_SESSION['orderInfo']['totalMoney'] = $_SESSION['orderInfo']['totalAmount'] + $defaultShippingMethod['pricing'];


  echo PugFacade::displayFile('../views/home/cart.purchaseProcess.jade', [
    'orderInfo' => $_SESSION['orderInfo'],
    'cartItems' => $result,
    'shipping' => $shipping,
    'payment' => $payment

  ]);
};

$purchaseProcessEdit = function () {
  $_POST = json_decode(file_get_contents("php://input"), true);
  if (isset($_POST['shippingid']) && $_POST['shippingid'] != "") {
    //Get price of new shipping method
    $shippingid = $_POST['shippingid'];
    $shippingmethods = CartModel::getShippingMethod();
    $findMethod = current(array_filter($shippingmethods, function ($item) use ($shippingid) {
      return ($item['id'] == $shippingid);
    }));
    $shipprice = $findMethod['pricing'];
    //Recalculate to total
    $_SESSION['orderInfo']['shippingprice'] = $shipprice;
    $_SESSION['orderInfo']['shippingid'] = $shippingid;
    $_SESSION['orderInfo']["totalMoney"] = $shipprice + $_SESSION['orderInfo']["totalAmount"];
    http_response_code(201);
    echo json_encode($_SESSION['orderInfo'], JSON_UNESCAPED_UNICODE);
    exit();
  }
  if (isset($_POST['payingmethodid']) && $_POST['payingmethodid'] != "") {
    $_SESSION['orderInfo']['paymentid'] = $_POST['payingmethodid'];
    http_response_code(201);
    echo json_encode($_SESSION['orderInfo'], JSON_UNESCAPED_UNICODE);
    exit();
  }
};


$purchaseComplete = function () {
  if (isset($_POST['confirm']) && isset($_SESSION['cart']) && count($_SESSION['cart']) && isset($_SESSION['orderInfo']) && count($_SESSION['orderInfo'])) {
    $userid = $_SESSION['orderInfo']['userid'];
    $receivername = $_SESSION['orderInfo']['receivername'];
    $addressid = $_SESSION['orderInfo']['addressid'];
    $totalmoney = $_SESSION['orderInfo']['totalMoney'];
    $phone = $_SESSION['orderInfo']['phone'];
    $shippingid = $_SESSION['orderInfo']['shippingid'];
    $paymentid = $_SESSION['orderInfo']['paymentid'];
    $addresstext = $_SESSION['orderInfo']['addresstext'];


    $orderId = CartModel::savePurchasedOrder($userid,  $receivername,  $addressid,  $totalmoney,  $phone,  $shippingid,  $paymentid,  $addresstext);
    if ($orderId) {
      $cart = CartModel::getItemsDetail($_SESSION["cart"]);
      $result = CartModel::savePurchasedOrderDetail($orderId, $cart);
      unset($_SESSION['cart']);
      unset($_SESSION['orderInfo']);
      echo PugFacade::displayFile('../views/home/cart.purchaseComplete.jade', [
        'orderid' => $orderId
      ]);
      exit();
    }
    Status::addError("Có lỗi xảy ra vui lòng thử lại sau");
  }
  header('location: /cart/purchase');
  exit();
};
