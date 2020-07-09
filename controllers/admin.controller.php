<?php
session_start();

include('../models/admin.model.php');
include('../actionStatus.php');
use AdminModel\AdminModel as AdminModel;
use Status\Status as Status;
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


$authors = function () {
  $result = AdminModel::getAuthors();
  //Khởi tạo session
  $errors = Status::getErrors();
  $messages = Status::getMessages();
  if ($result) {
    echo PugFacade::displayFile('../views/admin/authors.jade', [
      'authors' => $result,
      'errors' => $errors,
      'messages' => $messages
    ]);
  }
  else {
    array_push($errors, "Có vấn đề xảy ra xin vui lòng thử lại");
    echo PugFacade::displayFile('../views/admin/authors.jade', [
      'authors' => [],
      'errors' => $errors,
      'messages' => $messages
    ]);
  }
  exit();
};

$authorFieldRequired = function () use ($authors) {
  if (!isset($_POST["name"]) || !isset($_POST["description"])) {
    //Nếu có post request gửi tới nhưng không có 2 trường
    //cần thiết thì quay về 
    header('location: /admin/authors');
  }
  $errors = [];
  if ($_POST["name"]=="") {
    array_push($errors, "Tên tác giả không được để trống");
  }
  if ($_POST["description"]=="") {
    array_push($errors, "Giới thiệu tác giả không được để trống");
  }
  if (count($errors)) {
    Status::addErrors($errors);
    header('location: /admin/authors');
    exit();
  }
};

$authorAdd = function () use ($authorFieldRequired) {
  $authorFieldRequired();
  $name = $_POST["name"];
  $description = $_POST["description"];
  $result = AdminModel::addAuthor($name, $description);
  if ($result) {
    Status::addMessage("Đã thêm vào cơ sở dữ liệu");
  }
  else {
    Status::addError("Có lỗi xảy ra, xin thử lại");
  }
  header('location: /admin/authors');
  exit();
};
$authorEdit = function ($authorid) use ($authorFieldRequired) {
  $authorFieldRequired();
  $name = $_POST["name"];
  $description = $_POST["description"];

  $result = AdminModel::editAuthor($authorid, $name, $description);
  if ($result) {
    Status::addMessage("Đã sửa tác giả có id ".$authorid." vào cơ sở dữ liệu");
  }
  else {
    Status::addError("Có lỗi xảy ra, xin thử lại");
  }
  header('location: /admin/authors');
  exit();
};
$authorDelete = function ($authorid) {
  $result = AdminModel::removeAuthor($authorid);
  if ($result) {
    Status::addMessage("Đã xoá tác giả có id ".$authorid." khỏi cơ sở dữ liệu");
  }
  else {
    Status::addError("Có lỗi xảy ra, xin thử lại");
  }
  header('location: /admin/authors');
  exit();
};

//For Categories
$categories = function () {
  $result = AdminModel::getCategories();
  //Khởi tạo session
  $errors = Status::getErrors();
  $messages = Status::getMessages();
  if ($result) {
    echo PugFacade::displayFile('../views/admin/categories.jade', [
      'categories' => $result,
      'errors' => $errors,
      'messages' => $messages
    ]);
  }
  else {
    array_push($errors, "Có vấn đề xảy ra xin vui lòng thử lại");
    echo PugFacade::displayFile('../views/admin/categories.jade', [
      'categories' => [],
      'errors' => $errors,
      'messages' => $messages
    ]);
  }
  exit();
};

$categoryFieldRequired = function () use($categories){
  if (!isset($_POST["name"]) || !isset($_POST["description"])) {
    //Nếu có post request gửi tới nhưng không có 2 trường
    //cần thiết thì quay về 
    header('location: /admin/categories');
  }
  if ($_POST["name"] === ""){
    Status::addErrors(['Tên danh mục không được để trống']);
    header('location: /admin/categories');
    exit();
  }

};

$categoryAdd = function () use ($categoryFieldRequired) {
  $categoryFieldRequired();
  $name = $_POST["name"];
  $result = AdminModel::addCategories($name);
  if ($result) {
    Status::addMessage("Đã thêm vào cơ sở dữ liệu");
  }
  else {
    Status::addError("Có lỗi xảy ra, xin thử lại");
  }
  header('location: /admin/categories');
  exit();
};

$categoryEdit = function ($categoryid) use ($categoryFieldRequired) {
  $categoryFieldRequired();
  $name = $_POST["name"];

  $result = AdminModel::editCategories($categoryid, $name);
  if ($result) {
    Status::addMessage("Đã sửa danh mục ID: ".$categoryid." vào cơ sở dữ liệu");
  }
  else {
    Status::addError("Có lỗi xảy ra, xin thử lại");
  }
  header('location: /admin/categories');
  exit();
};

$categoryDelete = function ($categoryid) {
  $result = AdminModel::removeAuthor($categoryid);
  if ($result) {
    Status::addMessage("Đã xoá danh mục ID: ".$categoryid." khỏi cơ sở dữ liệu");
  }
  else {
    Status::addError("Có lỗi xảy ra, xin thử lại");
  }
  header('location: /admin/categories');
  exit();
};
