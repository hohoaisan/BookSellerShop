<?php
    include('../models/book.model.php');
    use Pug\Facade as PugFacade;

    use BookModel\BookModel as BookModel;   
    
    $index = function($bookid){
        $book = BookModel::getBook($bookid);
        $category = BookModel::getCategory($bookid);
        $author =  BookModel::getAuthor($bookid);
        echo PugFacade::displayFile('../views/home/bookDetail.jade',[
            'book' => $book,
            'category' => $category,
            'author' => $author
        ]);
        exit();
    }
?>