<?php 

use Category\CategoryController;
    $router->get('/', 'Category\CategoryController@index');
    $router->get('/{cateid}', 'Category\CategoryController@categoryBook');
?>