<?php
include('../models/cart.model.php');

use Status\Status as Status;
use Pug\Facade as PugFacade;
use CartModel\CartModel as CartModel;


$index = function () {
  $fetch = CartModel::getItemsDetail($_SESSION["cart"]);
  $result = [];
  if ($fetch) {
    $result = $fetch["books"];
  }
  echo PugFacade::displayFile('../views/home/cart.jade', [
    'cartItems' => $result
  ]);
};
$removeItem = function () {
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
      echo json_encode(array('status' => true, 'bookid' => $_POST["bookid"]), JSON_UNESCAPED_UNICODE);
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
  if (isset($_POST["bookid"]) && isset($_POST["quantity"])) {
    
  }
};

$showCart = function () {
  print_r(CartModel::getItemsDetail($_SESSION["cart"]));
  // print_r($_SESSION["cart"]);
};
