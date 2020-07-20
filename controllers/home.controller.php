<?php
include('../models/home.php');

use HomePage\HomePage as HomePage;
use Phug\Lexer\State;
use Status\Status as Status;
use Pug\Facade as PugFacade;

$index = function() {
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