<?php

namespace Auth;



use UserModel\UserModel as UserModel;

use Status\Status as Status;
use Pug\Facade as PugFacade;


class AuthController
{
  public static function parseUser()
  {
    if (isset($_COOKIE['authentication'])) {
      $authen = $_COOKIE['authentication'];
      $userid = $GLOBALS['cryptor']->decrypt($authen);
      $result = UserModel::checkUserId($userid);
      if ($result) {
        return $result;
      }
    } else return false;
  }


  public static function requireLogin()
  {
    $user = self::parseUser();
    if ($user) {
    } else {
      header('location: /auth/login');
    }
  }

  public static function requireAdmin()
  {
    $user = self::parseUser();
    if ($user && $user['isadmin'] == '1') {
    } else {
      header('location: /auth/login');
    }
  }


  public static function login()
  {
    $prompt = false;
    $user = self::parseUser();
    if ($user) {
      if ($user['isadmin'] == '1') {
        header('location: /admin');
        exit();
      }
      $prompt = true;
    }
    $errors = Status::getErrors();
    $messages = Status::getMessages();
    echo PugFacade::displayFile('../views/auth/login.jade', [
      'errors' => $errors,
      'messages' => $messages,
      'prompt' => $prompt
    ]);
  }

  public static function register()
  {
    $prompt = false;
    $user = self::parseUser();
    if ($user) {
      if ($user['isadmin'] == '1') {
        header('location: /admin');
        exit();
      }
      $prompt = true;
    }
    $errors = Status::getErrors();
    $messages = Status::getMessages();
    echo PugFacade::displayFile('../views/auth/register.jade', [
      'errors' => $errors,
      'messages' => $messages,
      'prompt' => $prompt
    ]);
  }

  public static function postRegisterRequiredField()
  {
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
      Status::addErrors($errors);
      header('location: /auth/register');
      exit();
    }
  }

  public static function postRegister()
  {
    self::postRegisterRequiredField();
    $username = $_POST["username"];
    $email = $_POST["email"];
    $checkExists = UserModel::checkUserExists($username, $email);
    if (!$checkExists) {
      $password = $_POST["password"];
      $fullname = $_POST["fullname"];
      $male = $_POST["male"];
      $phone = $_POST["phone"];
      $dob = $_POST["dob"];
      $result = UserModel::regiserNewUser($username, $password, $email, $fullname, $male, $phone, $dob);
      if ($result) {
        Status::addMessage("Tạo tài khoản thành công");
      } else {
        Status::addError("Tạo tài khoản thất bại");
      }
    } else {
      Status::addError("Tên người dùng hoặc email đã được đăng kí");
    }
    header('location: /auth/register');
    exit();
  }

  public static function postLoginRequiredField()
  {
    if (!isset($_POST["username"]) || !isset($_POST["password"])) {
      echo PugFacade::displayFile('../views/auth/login.jade');
      exit();
    }
    $errors = [];
    if ($_POST["username"] == "") {
      Status::addError("Tên đăng nhập không được để trống");
    };
    if ($_POST["password"] == "") {
      Status::addError("Mật khẩu không được để trống");
    };
    if (count($errors)) {
      header('location: /auth/login');
      exit();
    }
  }


  public static function postLogin()
  {
    self::postLoginRequiredField();
    $username = $_POST["username"];
    $password = $_POST["password"];
    $result = UserModel::verifyUser($username, $password);
    if ($result["status"] == 1) {
      $authen = $GLOBALS['cryptor']->encrypt($result['userid']);
      print_r($authen);
      setcookie("authentication", $authen, time() + (1800), "/");
      // setcookie("userid", $result["userid"], time() + (1800), "/");
      // setcookie("admin", $result["isadmin"], time() + (1800), "/");
      header('location: /');
    } else {
      Status::addError($result["message"]);
      header('location: /auth/login');
      exit();
    }
  }


  public static function logout()
  {
    setcookie('authentication', null, -1, '/');
    header('location: /auth/login');
  }
}
