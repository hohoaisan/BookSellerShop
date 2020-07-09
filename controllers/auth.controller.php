<?php
include('../models/auth.model.php');

use AuthModel\AuthModel as AuthModel;
use Pug\Facade as PugFacade;

$login = function () {
  if (isset($_COOKIE['userid']) && isset($_COOKIE['admin'])) {
    if ($_COOKIE['admin'] == "1") {
      header('location: /admin');
    } else {
      header('location: /');
    }
  }
  echo PugFacade::displayFile('../views/auth/login.jade');
};

$register = function () {
  if (isset($_COOKIE['userid']) && isset($_COOKIE['admin'])) {
    if ($_COOKIE['admin'] == "1") {
      header('location: /admin');
    } else {
      header('location: /');
    }
  }
  echo PugFacade::displayFile('../views/auth/register.jade');
};

$postRegisterRequiredField = function () {
  //array chứa error
  $errors = [];
  if (!$_POST["username"]) {
    array_push($errors, "Tên đăng nhập không được để trống");
  };
  if (strlen($_POST["username"]) >= 30) {
    array_push($errors, "Tên đăng nhập không được vượt quá 30 ký tự");
  };
  if ($_POST["password"] !== $_POST["password-repeat"]) {
    array_push($errors, "Sai mật khẩu xác nhận");
  };
  if (!$_POST["password"]) {
    array_push($errors, "Mật khẩu không được để trống");
  };
  if (!$_POST["email"]) {
    array_push($errors, "Email không được để trống");
  };
  if (count($errors)) {
    echo PugFacade::displayFile('../views/auth/register.jade', ['errors' => $errors]);
    exit();
  }
};

$postRegister = function () use ($postRegisterRequiredField) {
  $postRegisterRequiredField();
  $username = $_POST["username"];
  $email = $_POST["email"];
  $checkExists = AuthModel::checkUserExists($username, $email);
  if (!$checkExists) {
    $password = $_POST["password"];
    $fullname = $_POST["fullname"];
    $male = $_POST["male"];
    $phone = $_POST["phone"];
    $dob = $_POST["dob"];
    $result = AuthModel::regiserNewUser($username, $password, $email, $fullname, $male, $phone, $dob);
    if ($result) {
      $messages = ["Tạo tài khoản thành công"];
      echo PugFacade::displayFile('../views/auth/login.jade', ['messages' => $messages]);
      exit();
    } else {
      $errors = ["Tạo tài khoản thất bại"];
      echo PugFacade::displayFile('../views/auth/register.jade', ['errors' => $errors, 'username' => $_POST["username"], 'fullname' => $_POST["fullname"]]);
      exit();
    }
  } else {
    $errors = ["Tên người dùng hoặc email đã được đăng kí"];
    echo PugFacade::displayFile('../views/auth/register.jade', ['errors' => $errors, 'username' => $_POST["username"], 'fullname' => $_POST["fullname"]]);
    exit();
  }
};

$postLoginRequiredField = function () {
  if (!isset($_POST["username"]) || !isset($_POST["password"])) {
    echo PugFacade::displayFile('../views/auth/login.jade');
    exit();
  }
  $errors = [];
  if ($_POST["username"] == "") {
    array_push($errors, "Tên đăng nhập không được để trống");
  };
  if ($_POST["password"] == "") {
    array_push($errors, "Mật khẩu không được để trống");
  };
  if (count($errors)) {
    echo PugFacade::displayFile('../views/auth/login.jade', ['errors' => $errors]);
    exit();
  }
};


$postLogin = function () use ($postLoginRequiredField) {
  $postLoginRequiredField();
  $username = $_POST["username"];
  $password = $_POST["password"];
  $result = AuthModel::verifyCredential($username, $password);
  if ($result["status"] == 1) {
    setcookie("userid", $result["userid"], time() + (300), "/"); //cookie tồn tại 3 phút (180)
    setcookie("admin", $result["isadmin"], time() + (300), "/");
    header('location: /auth/login');
  } else {
    $errors = [$result["message"]];
    echo PugFacade::displayFile('../views/auth/login.jade', ['errors' => $errors, 'username' => $username]);
    exit();
  }
};


$logout = function () {
  setcookie('userid', null, -1, '/');
  setcookie('admin', null, -1, '/');
  header('location: /auth/login');
};
