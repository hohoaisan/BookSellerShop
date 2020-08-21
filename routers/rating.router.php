<?php 
    include('../controllers/rating.controller.php');
    $router->post('/{ratingid}/delete', $removeRating);
    $router->post('/{ratingid}/edit', $editRating);
    $router->post('/create', $setRating);
    $router->get('/{ratingid}/', $getRating);
?>