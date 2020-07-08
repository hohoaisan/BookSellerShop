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
  echo PugFacade::displayFile('../views/auth/index.jade');
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
