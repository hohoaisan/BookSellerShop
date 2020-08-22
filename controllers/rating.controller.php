<?php
include_once('../modules/pagination.php');
include('user.controller.php');

use RatingModel\RatingModel as RatingModel;
use OrderModel\OrderModel as OrderModel;
use Status\Status as Status;

$getRating = function ($ratingid) {
  $result = RatingModel::getRating($ratingid);
  if ($result) {
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
  } else {
    http_response_code(404);
  }
};

$postSetRating = function() {
  $errors = [];
  if (!isset($_POST["bookid"]) || !$_POST["bookid"]) {
    http_response_code(403);
    exit();
  }

  if (!isset($_POST["rating"]) || $_POST["rating"] < 1 || $_POST["rating"] > 5) {
    http_response_code(403);
    exit();
  }

  if (!isset($_POST["content"]) || !$_POST["content"]) {
    array_push($errors, "Phải có nội dung đánh giá");
  }
  if (count($errors)) {
    if ($_POST["redirecturl"]) {
      Status::addErrors($errors);
      header('location: ' . $_POST["redirecturl"]);
    }
    http_response_code(403);
  }
};

$setRating = function () use ($getUserInfo, $postSetRating) {
  $postSetRating();
  $bookid = $_POST["bookid"];
  $user = $getUserInfo();
  $checkBoughtBook = OrderModel::checkUserHasBoughtBook($user['userid'], $bookid);
  $checkHasComment = RatingModel::checkUserHasRated($user['userid'], $bookid);
  if ($checkBoughtBook && !$checkHasComment) {
    $rating = $_POST["rating"];
    $content = isset($_POST["content"])?$_POST["content"]:"";
    $result = RatingModel::createRating($user['userid'], $bookid, $rating, $content);
    if ($result) {
      if ($_POST["redirecturl"]) {
        Status::addMessage("Đã thêm đánh giá");
        header('location: ' . $_POST["redirecturl"]);
        exit();
      }
    }
    else {
      if ($_POST["redirecturl"]) {
        Status::addError("Có lỗi xảy ra khi thêm đánh giá");
        header('location: ' . $_POST["redirecturl"]);
        exit();
      }
      http_response_code(500);
    }
  }
  http_response_code(403);
  exit();

};


$verifyRatingOwner = function ($ratingid) use ($getUserInfo) {
  $user = $getUserInfo();
  $rating = RatingModel::getRating($ratingid);
  if ($rating['userid'] == $user['userid'] || $user['isadmin'] == '1') {
  } else {
    http_response_code(403);
    exit();
  }
};

$removeRating = function ($ratingid) use ($verifyRatingOwner) {
  $verifyRatingOwner($ratingid);
  $result = RatingModel::removeRating($ratingid);
  if ($result) {
    if ($_POST["redirecturl"]) {
      Status::addMessage("Đã xoá đánh giá");
      header('location: ' . $_POST["redirecturl"]);
      exit();
    }
    http_response_code(201);
  } else {
    if ($_POST["redirecturl"]) {
      Status::addError("Có lỗi xảy ra khi xoá đánh giá");
      header('location: ' . $_POST["redirecturl"]);
      exit();
    }
    http_response_code(500);
  }
};
$editRating = function ($ratingid) use ($verifyRatingOwner) {
  $errors = [];
  if (!isset($_POST["rating"]) || $_POST["rating"] < 1 || $_POST["rating"] > 5) {
    http_response_code(403);
    exit();
  }

  if (!isset($_POST["content"]) || !$_POST["content"]) {
    array_push($errors, "Phải có nội dung đánh giá");
  }
  if (count($errors)) {
    if ($_POST["redirecturl"]) {
      Status::addErrors($errors);
      header('location: ' . $_POST["redirecturl"]);
    }
    http_response_code(403);
  }
  else {
    $verifyRatingOwner($ratingid);
    $rating = $_POST["rating"];
    $content = isset($_POST["content"])?$_POST["content"]:"";
    $result = RatingModel::editRating($ratingid, $rating, $content);
    if ($result) {
      if ($_POST["redirecturl"]) {
        Status::addMessage("Đã sửa đánh giá");
        header('location: ' . $_POST["redirecturl"]);
        exit();
      }
    }
    else {
      if ($_POST["redirecturl"]) {
        Status::addError("Có lỗi xảy ra khi sửa đánh giá");
        // header('location: ' . $_POST["redirecturl"]);
        exit();
      }
      http_response_code(500);
    }
  }
};
