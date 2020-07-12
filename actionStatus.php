<?php namespace Status;
//Dùng để tạo ra thông báo, khi lấy được thông báo sẽ làm rỗng session chứa thông báo 
class Status {
  public static function initalizeSession() {
    if (!isset($_SESSION["messages"])) {
      $_SESSION["messages"] = [];
    };
    if (!isset($_SESSION["errors"])) {
      $_SESSION["errors"] = [];
    };
    if (!$_SESSION["shopping_cart"]) {
      $_SESSION["shopping_cart"] = [];
    };
  }
  public static function getErrors() {
    self::initalizeSession(); //Khởi tạo nếu session chưa có
    $errors = $_SESSION["errors"]; //Đặt vào biến tạm
    $_SESSION["errors"] = []; // Làm rỗng
    return $errors;
  }

  public static function getItemsCart() {
    self::initalizeSession(); //Khởi tạo nếu session chưa có
    $errors = $_SESSION["shopping_cart"]; //Đặt vào biến tạm
    $_SESSION["shopping_cart"] = []; // Làm rỗng
    return $errors;
  }

  public static function getMessages() {
    self::initalizeSession();
    $messages = $_SESSION["messages"];
    $_SESSION["messages"] = [];
    return $messages;
  }
  public static function addError($error) {
    self::initalizeSession();
    array_push($_SESSION["errors"], $error);
  }
  public static function addMessage($message) {
    self::initalizeSession();
    array_push($_SESSION["messages"], $message);
  }

  public static function addErrors($errors) {
    self::initalizeSession();
    $_SESSION["errors"] = array_merge($_SESSION["errors"], $errors);
  }
  public static function addMessages($messages) {
    self::initalizeSession();
    $_SESSION["messages"] = array_merge($_SESSION["messages"], $messages);
  }
}
