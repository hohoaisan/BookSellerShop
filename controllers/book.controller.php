<?php
    include('../models/book.model.php');
    use Pug\Facade as PugFacade;

    use BookModel\BookModel as BookModel;   
    
    $index = function($bookid){
        $book = BookModel::getBook($bookid);
        echo PugFacade::displayFile('../views/home/bookDetail.jade',[
            'book' => $book
        ]);
        exit();
    }
?>