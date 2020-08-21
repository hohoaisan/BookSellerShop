<?php
include_once('../models/rating.model.php');
include_once('../modules/pagination.php');
include('user.controller.php');

use RatingModel\RatingModel as RatingModel;
use Phug\Lexer\State;
use Status\Status as Status;
use Pug\Facade as PugFacade;

$getRating = function ($ratingid) {
  $result = RatingModel::getRating($ratingid);
  if ($result) {
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
  } else {
    http_response_code(404);
  }
};
$setRating = function ($ratingid) {
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
      header('location: ' . $_POST["redirecturl"]);
      exit();
    }
    http_response_code(201);
  } else {
    http_response_code(500);
  }
};
$editRating = function ($ratingid) {
};
