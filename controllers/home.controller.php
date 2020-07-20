<?php
include('../models/home.model.php');

use HomePage\HomePage as HomePage;
use Phug\Lexer\State;
use Status\Status as Status;
use Pug\Facade as PugFacade;

$removeParam = function ($param) {
  $url = $_SERVER['REQUEST_URI'];
  $url = preg_replace('/(&|\?)' . preg_quote($param) . '=[^&]*$/', '', $url);
  $url = preg_replace('/(&|\?)' . preg_quote($param) . '=[^&]*&/', '$1', $url);
  if (strpos($url, '?')) {
      return $url . '&';
  } else return $url . '?';
};

$index = function() use ($removeParam){
  //Pagination
  try {
      $page = intval(isset($_GET['page']) ? $_GET['page'] : 1);
  } catch (Exception $e) {
      $page = 1;
  }
  $itemperpage = 18;

  $fetch = HomePage::getShowBooks($page, $itemperpage);
  $result = $fetch['result']; //Lấy kết quả trong 1 trang pagination

  $num_records = $fetch['rowcount']; //Lấy số kết quả trong toàn bộ bảng
  $num_page = ceil($num_records / $itemperpage); //Số trang

  if (!$fetch) {
      $result = [];
  }
  $listBooks = HomePage::getBooks();
  $listCategories = HomePage::getCategory();
  $mostSeller = HomePage::mostSeller();
  $mostPopular = HomePage::mostPopular();
  echo PugFacade::displayFile('../views/home/index.jade', [
      'listBooks'=> $listBooks,
      'listCategories'=> $listCategories,
      'mostSeller'=> $mostSeller,
      'mostPopular'=> $mostPopular,
      'showBooks' => $result,// Xác đỊnh mục nào đang được chọn
      'pagination_url' => $removeParam('page'), //Lấy url cũ và render mới
      'pagination_pages' => $num_page,
      'pagination_current_page' => $page
  ]);
  exit();
};

?>