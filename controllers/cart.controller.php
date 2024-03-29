<?php

namespace Cart;

use API\APIController;
use User\UserController;
use BookModel\BookModel as BookModel;
use Exception;
use Status\Status as Status;
use Pug\Facade as PugFacade;
use PaymentModel\PaymentModel as PaymentModel;
use ShippingModel\ShippingModel as ShippingModel;
use OrderModel\OrderModel as OrderModel;
use PDOException;

class CartController
{
  public static function getItemsDetail($cart)
  {
    try {
      $totalMoney = 0;
      $books = [];
      foreach ($cart as $bookid => $quantity) {
        $book = BookModel::getBook($bookid);
        $book["sel_quantity"] = $quantity;
        $book["amount"] = $book["sel_quantity"] * $book["price"];
        $totalMoney += $book["amount"];
        // $book["price"] = number_format($book["price"], 0, ",", ".");
        array_push($books, $book);
      }
      // $totalMoney = number_format($totalMoney, 0, ",", ".");
      return [
        'totalmoney' => $totalMoney,
        'books' => $books
      ];
    } catch (PDOException  $e) {
      return false;
    }
  }

  public static function index()
  {
    $errors = Status::getErrors();
    $messages = Status::getMessages();
    $fetch = self::getItemsDetail($_SESSION["cart"]);
    $result = [];
    if ($fetch) {
      $result = $fetch["books"];
      $totalMoney = $fetch["totalmoney"];
    }
    $isItemExceeded = self::isCartItemExceededRemainingQuantity($fetch);
    if ($isItemExceeded) {
      array_push($errors, "Giỏ hàng tồn tại sản phẩm đã hết hàng hoặc vượt quá số lượng có trong hệ thống, vui lòng chỉnh sửa lại giỏ hàng hoặc xoá bỏ sản phẩm khỏi giỏ hàng");
    }
    echo PugFacade::displayFile('../views/home/cart/cart.jade', [
      'cartItems' => $result,
      'totalMoney' => $totalMoney,
      'errors' => $errors,
      'messages' => $messages,
      'isItemExceeded' => $isItemExceeded
    ]);
  }

  public static function removeAllItem()
  {
    //Trả lại số lượng cho mặt hàng
    $_SESSION["cart"] = [];
    http_response_code(201);
    echo json_encode(array('status' => true), JSON_UNESCAPED_UNICODE);
    exit();
  }

  public static function removeItem()
  {
    $_POST = json_decode(file_get_contents("php://input"), true);
    if (isset($_POST["bookid"])) {
      $bookid = $_POST["bookid"];
      $condition = true;
      if ($condition) {
        unset($_SESSION["cart"][$bookid]);
        $fetch = self::getItemsDetail($_SESSION["cart"]);
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
  }


  //Thiếu: Kiểm tra số lượng còn
  //Nếu đủ thì trừ lui

  public static function addItem()
  {
    $message = "";
    $_POST = json_decode(file_get_contents("php://input"), true);
    if (isset($_POST["bookid"]) && isset($_POST["quantity"])) {

      $bookid = $_POST["bookid"];
      $quantity = $_POST["quantity"];
      try {
        if (intval($quantity) <= 0) {
          throw new \Exception("Số lượng phải lớn hơn 0");
        }
        $book = BookModel::getBook($bookid);
        $curr_quantity = isset($_SESSION["cart"][$bookid])?$_SESSION["cart"][$bookid]:0;
        $total_quantity = $quantity;
        if (isset($_SESSION["cart"][$bookid])) {
          $total_quantity += $curr_quantity;
        }
        if ($book['quantity'] - $total_quantity < 0) {

          if ($book['quantity'] && $book['quantity']-$curr_quantity > 0) {
            throw new \Exception("Số lượng vượt quá số lượng còn trong hệ thống, số lượng tối đa có thể thêm: ".($book['quantity']-$curr_quantity));
          }
          if ($book['quantity'] && $book['quantity']-$curr_quantity <= 0) {
            throw new \Exception("Bạn đã thêm vào giỏ tất cả số lượng sản phẩm còn trong hệ thống");
          }
          throw new \Exception("Sản phẩm đã hết hàng");
        }
        if (isset($_SESSION["cart"][$bookid])) {
          $_SESSION["cart"][$bookid] += $quantity;
        } else {
          $_SESSION["cart"][$bookid] = $quantity;
        }
        echo json_encode(array('status' => true, 'bookid' => $bookid), JSON_UNESCAPED_UNICODE);
      } catch (\Exception $e) {
        $message = $e->getMessage();
        echo json_encode(array(
          'status' => false,
          'message' => "Không thể thêm vào giỏ hàng: " . $message
        ), JSON_UNESCAPED_UNICODE);
      }
    } else {
      header('location: /cart/');
    }
    exit();
  }


  public static function editItem()
  {
    $_POST = json_decode(file_get_contents("php://input"), true);
    if (isset($_POST["bookid"]) && isset($_POST["quantity"])) {
      $bookid = $_POST["bookid"];
      $quantity = $_POST["quantity"];
      if (isset($_SESSION["cart"][$bookid])) {
        try {
          if (intval($_POST["quantity"]) <= 0) {
            throw new \Exception("Số lượng phải lớn hơn 0");
          }
          $book = BookModel::getBook($bookid);
          if ($book['quantity'] - $quantity < 0) {
            if ($book['quantity']) {
              throw new \Exception("Số lượng vượt quá số lượng còn trong hệ thống, số lượng tối đa có thể sửa: ".$book['quantity']);
            }
            else {
              throw new \Exception("Sản phẩm đã hết hàng");
            }
          }
          $_SESSION["cart"][$bookid] = $quantity;
          $fetch = self::getItemsDetail($_SESSION["cart"]);
          $totalMoney = $fetch["totalmoney"];
          echo json_encode(array('status' => true, 'bookid' => $bookid, 'totalMoney' => $totalMoney), JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {

          echo json_encode(array(
            'status' => false,
            'message' => "Không thể sửa vào giỏ hàng: " . $e->getMessage()
          ), JSON_UNESCAPED_UNICODE);
        }
      } else {
        echo json_encode(array(
          'status' => false,
          'message' => "Giỏ hàng không tồn tại sản phẩm này"
        ), JSON_UNESCAPED_UNICODE);
      }
    }
    else {
      http_response_code(405);      
    }
    exit();
  }

  public static function getJSON()
  {

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
  }

  public static function checkCartIsReady()
  {
    if (count($_SESSION["cart"]) == 0 || self::isCartItemExceededRemainingQuantity()) {
      header('location: /cart/');
      exit();
    }
  }


  public static function purchaseCart()
  {
    $errors = Status::getErrors();
    self::checkCartIsReady();
    $userInfo = $_SESSION['authuser'];
    $addressInfo = APIController::getFullAddressInfo($userInfo['addressid']);
    // select userid,username, fullname, phone, email, male,addressid, addresstext, dob
    echo PugFacade::displayFile('../views/home/cart/cart.purchase.jade', [
      'user' => $userInfo,
      'address' => $addressInfo,
      'errors' => $errors
    ]);
  }

  public static function purchaseProcessMiddleWare()
  {
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
  }

  public static function purchaseProcess()
  {
    unset($_SESSION['orderInfo']);
    self::checkCartIsReady();
    self::purchaseProcessMiddleWare();
    $fetch = self::getItemsDetail($_SESSION["cart"]);
    $result = $fetch["books"];
    $totalMoney = $fetch["totalmoney"];
    $payment = PaymentModel::getPaymentMethod();
    $shipping = ShippingModel::getShippingMethod();



    $_SESSION['orderInfo'] = [
      'userid' => isset($_POST['userid']) ? $_POST['userid'] : "",
      'receivername' => $_POST['receivername'],
      'phone' => $_POST['phone'],
      'addressid' => $_POST['addressid'],
      'addresstext' => $_POST['addresstext'],
      'totalAmount' => $totalMoney,
      'totalMoney' => $totalMoney,
    ];
    $addressInfo = APIController::getFullAddressInfo($_SESSION['orderInfo']['addressid']);
    $_SESSION['orderInfo']['fullAddress'] = $_SESSION['orderInfo']['addresstext'] . ", " . $addressInfo["wardname"] . ", " . $addressInfo["districtname"] . ", " . $addressInfo["provincename"];
    $defaultShippingMethod = ShippingModel::getDefaultShippingMethod();
    $defaultPaymentMethod = PaymentModel::getDefaultPaymentMethod();
    $_SESSION['orderInfo']['paymentid'] = $defaultPaymentMethod['id'];
    $_SESSION['orderInfo']['shippingid'] = $defaultShippingMethod['id'];
    $_SESSION['orderInfo']['shippingprice'] = $defaultShippingMethod['pricing'];
    $_SESSION['orderInfo']['totalMoney'] = $_SESSION['orderInfo']['totalAmount'] + $defaultShippingMethod['pricing'];


    echo PugFacade::displayFile('../views/home/cart/cart.purchaseProcess.jade', [
      'orderInfo' => $_SESSION['orderInfo'],
      'cartItems' => $result,
      'shipping' => $shipping,
      'payment' => $payment

    ]);
  }

  public static function purchaseProcessEdit()
  {
    $_POST = json_decode(file_get_contents("php://input"), true);
    self::checkCartIsReady();
    if (isset($_POST['shippingid']) && $_POST['shippingid'] != "") {
      //Get price of new shipping method
      $shippingid = $_POST['shippingid'];
      $shippingmethods = ShippingModel::getShippingMethod();
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
  }


  public static function purchaseComplete()
  {
    self::checkCartIsReady();
    if (isset($_POST['confirm']) && isset($_SESSION['cart']) && count($_SESSION['cart']) && isset($_SESSION['orderInfo']) && count($_SESSION['orderInfo'])) {
      $userid = $_SESSION['orderInfo']['userid'];
      $receivername = $_SESSION['orderInfo']['receivername'];
      $addressid = $_SESSION['orderInfo']['addressid'];
      $totalmoney = $_SESSION['orderInfo']['totalMoney'];
      $phone = $_SESSION['orderInfo']['phone'];
      $shippingid = $_SESSION['orderInfo']['shippingid'];
      $paymentid = $_SESSION['orderInfo']['paymentid'];
      $addresstext = $_SESSION['orderInfo']['addresstext'];


      $orderId = OrderModel::savePurchasedOrder($userid,  $receivername,  $addressid,  $totalmoney,  $phone,  $shippingid,  $paymentid,  $addresstext);
      if ($orderId) {
        $cart = self::getItemsDetail($_SESSION["cart"]);
        $result = OrderModel::savePurchasedOrderDetail($orderId, $cart);
        unset($_SESSION['cart']);
        unset($_SESSION['orderInfo']);
        echo PugFacade::displayFile('../views/home/cart/cart.purchaseComplete.jade', [
          'orderid' => $orderId
        ]);
        exit();
      }
      Status::addError("Có lỗi xảy ra vui lòng thử lại sau");
    }
    header('location: /cart/purchase');
    exit();
  }

  public static function isCartItemExceededRemainingQuantity($fetch = null) {
    if (!$fetch) {
      $fetch = self::getItemsDetail($_SESSION['cart']);
    }
    foreach ($fetch['books'] as $book) {
      if ($book['sel_quantity'] > $book['quantity']) {
        return true;
      }
    }
    return false;
  }
}
