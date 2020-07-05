<?php

include('../models/connect.php');
use Pug\Facade as PugFacade;

$register = function () {
    echo PugFacade::displayFile('../views/register/register.pug');
};

$postRegisterRequiredField = function() use($conn){
    // if(!isset($_POST['username']) || !isset($_POST['password']) || !isset($_POST['email'])){
    //     echo PugFacade::displayFile('../views/register/register.pug');
    //     exit();
    // }
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
    $username = $_POST["username"];
    $email = $_POST["email"];
    $sql = "select * from users WHERE username='$username' OR email='$email' LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch();
    if ($result) { // if user exists
        if ($result['username'] === $username) {
            array_push($errors, "Username already exists");
        }
    
        if ($result['email'] === $email) {
            array_push($errors, "Email already exists");
        }
    }

    if (count($errors)) {
        echo PugFacade::displayFile('../views/register/register.pug', ['errors' => $errors]);
        exit();
    }
};

$postRegister = function() use($postRegisterRequiredField, $conn){
    $postRegisterRequiredField();

    $sql = "insert INTO users(username, password, email) VALUES (:username, :password, :email)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':email', $email);
    $username = $_POST["username"];
    $password = $_POST["password"];
    $email =  $_POST["email"];
    $stmt->execute();

    $succ = "Tạo tài khoản thành công";
    echo PugFacade::displayFile('../views/register/register.pug', ['succ' => $succ]);
    exit();
};
