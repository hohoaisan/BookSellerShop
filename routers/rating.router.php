<?php 
use Rating\RatingController;

    $router->post('/{ratingid}/delete', Closure::fromCallable('Rating\RatingController::removeRating'));
    $router->post('/{ratingid}/edit', Closure::fromCallable('Rating\RatingController::editRating'));
    $router->post('/create', Closure::fromCallable('Rating\RatingController::setRating'));
    $router->get('/{ratingid}/', Closure::fromCallable('Rating\RatingController::getRating'));
?>