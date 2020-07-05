<?php
include('../models/connect.php');

use Pug\Facade as PugFacade;

$login = function () {
  if (isset($_COOKIE['userid']) && isset($_COOKIE['admin'])) {
    if ($_COOKIE['admin'] == "1") {
      header('location: /admin');
    }
    else {
      header('location: /');
    }
  }
  echo PugFacade::displayFile('../views/auth/login.pug');
};

$postLoginRequiredField = function () {
  if (!isset($_POST["username"]) || !isset($_POST["password"])) {
    echo PugFacade::displayFile('../views/auth/login.pug');
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
    echo PugFacade::displayFile('../views/auth/login.pug', ['errors' => $errors]);
    exit();
  }
};


$postLogin = function () use ($postLoginRequiredField, $conn) {
  $postLoginRequiredField();

  // https://www.php.net/manual/en/pdo.prepared-statements.php và tài liệu của thầy
  $sql = "select userid, username, password, admin from auth where username=?";
  $stmt = $conn->prepare($sql);
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $stmt->execute(array($_POST["username"]));
  $result = $stmt->fetch();
  if ($result && $result["password"] == $_POST["password"]) {
    setcookie("userid", $result["userid"], time() + (300), "/"); //cookie tồn tại 3 phút (180)
    setcookie("admin", $result["admin"], time() + (300), "/");
    header('location: /auth/login');
  }
  $errors = ["Tên tài khoản hoặc mật khẩu không đúng"];
  echo PugFacade::displayFile('../views/auth/login.pug', ['errors' => $errors]);
  exit();
};


$logout = function() {
  setcookie('userid', null, -1, '/'); 
  setcookie('admin', null, -1, '/'); 
  header('location: /');
};