<?php 

use Category\CategoryController;
    $router->get('/', Closure::fromCallable('Category\CategoryController::index'));
    $router->get('/{cateid}', Closure::fromCallable('Category\CategoryController::categoryBook'));
?>