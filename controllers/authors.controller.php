<?php 
    include('../models/author.model.php');
    use Pug\Facade as PugFacade;
    use AuthorModel\AuthorModel as AuthorModel; 

    $index = function(){
        echo PugFacade::displayFile('../views/home/author.jade');
    }
?>