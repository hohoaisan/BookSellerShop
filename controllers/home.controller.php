<?php
include('../models/home.php');

use HomePage\HomePage as HomePage;
use Phug\Lexer\State;
use Status\Status as Status;
use Pug\Facade as PugFacade;

$index = function() {
  // Status::getItemsCart();
  // if(isset($_POST["action"]))
  // {
  //   if($_POST["action"] == "add")
  //   {
  //       $bookid = $_POST["bookid"];
  //       $bookname = $_POST["bookname"];
  //       $price = $_POST["price"];
  //       $bookimageurl = $_POST["bookimageurl"];
  //       $object = (object) [
  //         'bookid' =>  $bookid,  
  //         'bookname'  =>  $bookname,
  //         'price'=> $price,
  //         'bookimageurl'=>$bookimageurl
  //       ];
  //   }
  //   Status::addItemsCart($object);
  // }    
  $listBooks = HomePage::getBooks();
  $listCategories = HomePage::getCategory();
  $mostSeller = HomePage::mostSeller();
  $mostPopular = HomePage::mostPopular();
  echo PugFacade::displayFile('../views/home/index.jade', [
      'listBooks'=> $listBooks,
      'listCategories'=> $listCategories,
      'mostSeller'=> $mostSeller,
      'mostPopular'=> $mostPopular
  ]);
  exit();
};

?>