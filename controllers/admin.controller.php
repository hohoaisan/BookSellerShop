<?php

use Database\Database as Database;
use Pug\Facade as PugFacade;

$index = function () {
  echo PugFacade::displayFile('../views/admin/index.jade');
};

$orders = function () {
  //orders?name=&user=&sort=pending|accept|history
  echo PugFacade::displayFile('../views/admin/orders.jade');
};

$orderJSON = function () {
  echo PugFacade::displayFile('../views/admin/orders.jade');
};

$orderReject = function () {
  echo PugFacade::displayFile('../views/admin/orders.jade');
};

$orderAccept = function () {
  echo PugFacade::displayFile('../views/admin/orders.jade');
};
$orderComplete = function () {
  echo PugFacade::displayFile('../views/admin/orders.jade');
};
$orderReject = function () {
  echo PugFacade::displayFile('../views/admin/orders.jade');
};

$users = function () {
  echo PugFacade::displayFile('../views/admin/users.jade');
};
$usersDisable = function () {
  echo PugFacade::displayFile('../views/admin/users.jade');
};

$usersDelete = function () {
  echo PugFacade::displayFile('../views/admin/users.jade');
};

$usersAdmin = function () {
  echo PugFacade::displayFile('../views/admin/users.jade');
};

$books = function () {
  echo PugFacade::displayFile('../views/admin/books.jade');
};
$bookAdd = function () {
  echo PugFacade::displayFile('../views/admin/books.add.jade');
};

$postBookAdd = function () {
  echo PugFacade::displayFile('../views/admin/books.add.jade');
};

$bookEdit = function () {
  echo PugFacade::displayFile('../views/admin/books.edit.jade');
};

$postBookEdit = function () {
  echo PugFacade::displayFile('../views/admin/books.edit.jade');
};

$categories = function () {
  echo PugFacade::displayFile('../views/admin/categories.jade');
};


$authors = function ($errors = [], $messages = []) {
  $sql = "select authorid, authorname, authordescription from `authors`";
  $result = Database::queryResults($sql, array());
  echo PugFacade::displayFile('../views/admin/authors.jade', [
    'authors' => $result,
    'errors' => $errors,
    'messages' => $messages
  ]);
};

$authorFieldRequired = function () use ($authors) {
  if (!isset($_POST["name"]) || !isset($_POST["description"])) {
    //Nếu có post request gửi tới nhưng không có 2 trường
    //cần thiết thì quay về 
    header('location: /authors');
  }
  $errors = [];
  if ($_POST["name"]=="") {
    array_push($errors, "Tên tác giả không được để trống");
  }
  if ($_POST["description"]=="") {
    array_push($errors, "Giới thiệu tác giả không được để trống");
  }
  if (count($errors)) {
    $authors($errors);
    exit();
  }
};

$authorAdd = function () use ($authorFieldRequired, $authors) {
  $authorFieldRequired();
  $name = $_POST["name"];
  $description = $_POST["description"];
  $sql = "insert into authors (authorname,authordescription) value (?,?)";
  try {
    Database::queryExecute($sql, array($name, $description));
    $authors([], ["Đã thêm vào cơ sở dữ liệu"]);
  }
  catch (PDOException $e) {
    $authors(["Có lỗi xảy ra ".$e->getMessage()]);
  }

};
$authorEdit = function ($authorid) use ($authorFieldRequired,$authors) {
  $authorFieldRequired();
  $name = $_POST["name"];
  $description = $_POST["description"];
  $sql = "update authors set authorname=?,authordescription=? where authorid=?";
  try {
    Database::queryExecute($sql, array($name, $description, $authorid));
    $authors([], ["Đã sửa tác giả có id ".$authorid." vào cơ sở dữ liệu"]);
  }
  catch (PDOException $e) {
    $authors(["Có lỗi xảy ra ".$e->getMessage()]);
  }
};
$authorDelete = function ($authorid) use ($authors) {
  $sql = "delete from authors where authorid=?";
  try {
    Database::queryExecute($sql, array($authorid));
    $authors([], ["Đã xoá tác giả có id ".$authorid." khỏi cơ sở dữ liệu"]);
  }
  catch (PDOException $e) {
    $authors(["Có lỗi xảy ra ".$e->getMessage()]);
  }
};
