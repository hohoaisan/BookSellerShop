<?php

use Database\Database as Database;
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

$postRegisterRequiredField = function(){
  //array chứa error
  $errors = [];
  if (!$_POST["username"]) {
      array_push($errors, "Tên đăng nhập không được để trống");
  };
  if (strlen($_POST["username"]) >= 30) {
      array_push($errors, "Tên đăng nhập không được vượt quá 30 ký tự");
  };
  if (!$_POST["password"]) {
      array_push($errors, "Mật khẩu không được để trống");
  };
  if (!$_POST["email"]) {
      array_push($errors, "Email không được để trống");
  };
      
  //check username và email tồn tại chưa
  $user =[$_POST["username"], $_POST["email"]];
  $result = Database::querySingleResult("select * from users WHERE username=? OR email=? LIMIT 1",$user);
  if ($result) { // if user exists
      if ($result['username'] === $_POST["username"]) {
          array_push($errors, "Username already exists");
      }
  
      if ($result['email'] === $_POST["email"]) {
          array_push($errors, "Email already exists");
      }
  }
  
  if (count($errors)) {
      echo PugFacade::displayFile('../views/auth/register.jade', ['errors' => $errors]);
      exit();
  }
};

$postRegister = function() use($postRegisterRequiredField){
  $postRegisterRequiredField();

  $user = [$_POST["username"], $_POST["password"], $_POST["email"]];
  $result = Database::queryExecute("insert INTO users(username, password, email) VALUES (?, ?, ?)", $user);
  if($result){
    $succ = "Tạo tài khoản thành công";
    echo PugFacade::displayFile('../views/auth/register.jade', ['succ' => $succ]);
    exit();
  }else{
    $errors = ["Tạo tài khoản thất bại"];
    echo PugFacade::displayFile('../views/auth/register.jade', ['errors' => $errors]);
    exit();
  }
};

$postLoginRequiredField = function () {
  if (!isset($_POST["username"]) || !isset($_POST["password"])) {
    echo PugFacade::displayFile('../views/auth/index.jade');
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
    echo PugFacade::displayFile('../views/auth/index.jade', ['errors' => $errors]);
    exit();
  }
};


$postLogin = function () use ($postLoginRequiredField) {
  $postLoginRequiredField();
  $username = $_POST["username"];
  $password = $_POST["password"];
  $result = Database::verifyCredential($username, $password);
  if ($result["status"] == 1) {
    setcookie("userid", $result["userid"], time() + (300), "/"); //cookie tồn tại 3 phút (180)
    setcookie("admin", $result["isadmin"], time() + (300), "/");
    header('location: /auth/login');
  } else {
    $errors = [$result["message"]];
    echo PugFacade::displayFile('../views/auth/index.jade', ['errors' => $errors, 'username' => $username]);
    exit();
  }
};


$logout = function () {
  setcookie('userid', null, -1, '/');
  setcookie('admin', null, -1, '/');
  header('location: /auth/login');
};
